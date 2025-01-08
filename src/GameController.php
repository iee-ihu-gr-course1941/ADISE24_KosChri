<?php
require_once 'dbconnect.php';
require_once 'game.php';
require_once 'users.php';

class GameController {
    private $mysqli;
    private $token;

    public function __construct() {
        $this->mysqli = $GLOBALS['mysqli'];
        $this->token = $_SERVER['HTTP_X_PLAYER_TOKEN'] ?? null;
    }

    public function setUser($input) {
        global $mysqli;

        if (!isset($input['name']) || $input['name'] == '') {
            http_response_code(400);
            echo json_encode(['errormesg' => "No name given."]);
            exit;
        }

        $name = $input['name'];

        $sql = "SELECT player_slot FROM players WHERE player_slot IN ('p1', 'p2')";
        $res = $mysqli->query($sql);
        $takenSlots = array_column($res->fetch_all(MYSQLI_ASSOC), 'player_slot');

        if (!in_array('p1', $takenSlots)) {
            $playerSlot = 'p1';
        } elseif (!in_array('p2', $takenSlots)) {
            $playerSlot = 'p2';
        } else {
            echo json_encode(['errormesg' => "All player slots are taken."]);
            return;
        }

        $token = $this->generateToken($name);

        $sql = "INSERT INTO players (name, token, player_slot) VALUES (?, ?, ?)";
        $st = $mysqli->prepare($sql);
        $st->bind_param('sss', $name, $token, $playerSlot);
        $st->execute();

        $sql = "SELECT id, name, token, player_slot FROM players WHERE name=?";
        $st = $mysqli->prepare($sql);
        $st->bind_param('s', $name);
        $st->execute();
        $res = $st->get_result();
        $newPlayer = $res->fetch_all(MYSQLI_ASSOC);
        echo json_encode($newPlayer, JSON_PRETTY_PRINT);
        $st->close();

        if (count($takenSlots) == 0) {
            set_status('initialized');
        } elseif (count($takenSlots) == 1) {
            set_status('started');
        }
    }

 public function read_status($echo = true) {
        global $mysqli;

        $sql = "SELECT status, p_turn, result FROM game_status";
        $st = $mysqli->prepare($sql);
        $st->execute();

        $result = $st->get_result();
        $status = $result->fetch_assoc();

        $st->close();
        if ($echo) {
        echo json_encode($status, JSON_PRETTY_PRINT);
    }
        return $status;
}

public function joinGame() {
    $this->token = $_SERVER['HTTP_X_PLAYER_TOKEN'] ?? null;

    error_log("Received token: " . var_export($this->token, true));  // Log to PHP error log

    if (!$this->token) {
        http_response_code(400);
        echo json_encode(['errormesg' => "Token is missing or invalid."]);
        return;
    }

    $player = $this->getPlayerByToken();

    if (!$player) {
        http_response_code(404);
        echo json_encode(['errormesg' => "Player not found."]);
        return;
    }


    echo json_encode(['message' => "Player joined successfully.", 'player' => $player], JSON_PRETTY_PRINT);
}
private function getPlayerByToken() {
    $sql = "SELECT * FROM players WHERE token = ?";
    $st = $this->mysqli->prepare($sql);
    $st->bind_param('s', $this->token);
    $st->execute();
    $result = $st->get_result();
    $player = $result->fetch_assoc();
    $st->close();

    error_log("Player data: " . var_export($player, true));  // Log player data

    return $player;
}
    private function generateToken($name) {
        return md5($name . time());
    }

 public function exchange_tile($input) {
    global $mysqli;

    $player = $this->getPlayerByToken();
    if (!$player) {
        echo "Invalid token.";
        return;
    }

    if (!isset($input['tile_index'])) {
        echo "Missing tile_index.";
        return;
    }

    $player_id = $player['id'];
    $player_slot = $player['player_slot'];
    $tile_index = $input['tile_index'];

    $game_status = $this->read_status(false); 
    $current_turn = $game_status['p_turn'];

    if ($current_turn !== $player_slot) {
        echo "It is not your turn.";
        return;
    }

    $sql = "SELECT id, tilesPlacedThisTurn FROM players WHERE id = ?";
    $st = $mysqli->prepare($sql);
    $st->bind_param('i', $player_id);
    $st->execute();
    $result = $st->get_result();
    $player = $result->fetch_assoc();
    $tilesPlacedThisTurn = $player['tilesPlacedThisTurn'];
    $st->close();

    if ($tilesPlacedThisTurn != 0) {
        echo "You have already placed tiles this turn.";
        return;
    }

    tileToBag($player_slot, $tile_index);
}

public function read_hand($echo = true) {
    global $mysqli;

    $player = $this->getPlayerByToken();
    if (!$player) {
        echo "Invalid token.";
        return;
    }

    $player_id = $player['id'];
    $player_slot = $player['player_slot'];

    $sql = "SELECT t.id, t.shape, t.color FROM player_hands ph JOIN tiles t ON ph.tile_id = t.id WHERE ph.player_id = ?";
    $st = $mysqli->prepare($sql);
    $st->bind_param('i', $player_id);
    $st->execute();
    $result = $st->get_result();

    $hand = $result->fetch_all(MYSQLI_ASSOC);
    $st->close();
    if ($echo) {
       foreach ($hand as $index => $tile) {
        echo ($index + 1) . ". Shape: " . $tile['shape'] . ", Color: " . $tile['color'] . "\n";
    }

    echo " Your hand ($player_slot) ";
    }
    
    return $hand;
}

    public function start() {
        global $mysqli;

        $sql = "SELECT player_slot FROM players WHERE player_slot IN ('p1', 'p2')";
        $res = $mysqli->query($sql);
        $players = $res->fetch_all(MYSQLI_ASSOC);

        if (count($players) < 2) {
            http_response_code(400);
            echo json_encode(['errormesg' => "Both players must be set. Try adding player"]);
            return;
        }

        $startingPlayer = $players[array_rand($players)]['player_slot'];

        $sql = "UPDATE game_status SET p_turn=?";
        $st = $mysqli->prepare($sql);
        $st->bind_param('s', $startingPlayer);
        $st->execute();

        // Prepare the game
        initBoard();
        cleanBoard();
        fillTileBag(); 

        $sql = "SELECT id FROM players WHERE player_slot IN ('p1', 'p2')";
        $res = $mysqli->query($sql);
        $players = $res->fetch_all(MYSQLI_ASSOC);

        foreach ($players as $player) {
            drawTileStart($player['id']);
        }

        echo "Game started! It's $startingPlayer's turn.";
    }

public function end_turn() {
    global $mysqli;

    $game_status = $this->read_status(false);  
    $current_turn = $game_status['p_turn'];

    $player = $this->getPlayerByToken();
    if (!$player) {
        echo "Invalid token.";
        return;
    }

    if ($player['player_slot'] !== $current_turn) {
        echo "It's not your turn.";
        return;
    }

    $sql = "SELECT id, tilesPlacedThisTurn, tilesDiscardedThisTurn FROM players WHERE player_slot = ?";
    $st = $mysqli->prepare($sql);
    $st->bind_param('s', $current_turn);
    $st->execute();
    $result = $st->get_result();
    $player = $result->fetch_assoc();
    $player_id = $player['id'];
    $tilesPlacedThisTurn = $player['tilesPlacedThisTurn'];
    $tilesDiscardedThisTurn = $player['tilesDiscardedThisTurn'];
    $st->close();

    if ($tilesDiscardedThisTurn != 0) {
        for ($i = 0; $i < $tilesDiscardedThisTurn; $i++) {
            drawTile($player_id);
        }

        $sql = "UPDATE players SET tilesDiscardedThisTurn = 0 WHERE id = ?";
        $st = $mysqli->prepare($sql);
        $st->bind_param('i', $player_id);
        $st->execute();
        $st->close();
    }

    $stmt = $mysqli->prepare("SELECT COUNT(*) as occupied_spaces FROM board WHERE tile_id IS NOT NULL");
    $stmt->execute();
    $result = $stmt->get_result();
    $occupied_spaces = $result->fetch_assoc()['occupied_spaces'];
    $stmt->close();

    $total_spaces = 7 * 7;
    if ($occupied_spaces == $total_spaces) {
        $stmt = $mysqli->prepare("
            SELECT name, score 
            FROM players 
            ORDER BY score DESC 
            LIMIT 1
        ");
        $stmt->execute();
        $result = $stmt->get_result();
        $winner = $result->fetch_assoc();
        $stmt->close();

        $sql = "UPDATE game_status SET status = 'game over'";
        $st = $mysqli->prepare($sql);
        $st->execute();
        $st->close();

        echo "Game over. The board is full. The winner is " . $winner['name'] . " with a score of " . $winner['score'] . ".";
        return;
    }

    $new_turn = ($current_turn == 'p1') ? 'p2' : 'p1';

    $sql = "UPDATE game_status SET p_turn = ?";
    $st = $mysqli->prepare($sql);
    $st->bind_param('s', $new_turn);
    $st->execute();
    $st->close();

    $sql = "UPDATE players SET tilesPlacedThisTurn = 0 WHERE id = ?";
    $st = $mysqli->prepare($sql);
    $st->bind_param('i', $player_id);
    $st->execute();
    $st->close();

    echo "Turn ended for player: $current_turn";
}

    function read_board() {
        global $mysqli;

        $board = [];

        try {
            $stmt = $mysqli->prepare("
                SELECT b.row, b.col, t.color, t.shape, p.player_slot
                FROM board b
                LEFT JOIN players p ON b.player_id = p.id
                LEFT JOIN tiles t ON b.tile_id = t.id
            ");
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $board[] = [
                    'row' => $row['row'],
                    'col' => $row['col'],
                    'color' => $row['color'] ? $row['color'] : 'NULL',
                    'shape' => $row['shape'] ? $row['shape'] : 'NULL',
                    'player_slot' => $row['player_slot'] ? $row['player_slot'] : 'NULL'
                ];
            }

            $stmt->close();

            // Print the board
            foreach ($board as $tile) {
                echo "Row: " . $tile['row'] . ", || Col: " . $tile['col'] . ", || Color: " . $tile['color'] . ", || Shape: " . $tile['shape'] . ", || Placed by: " . $tile['player_slot'] . "\n";
            }

            return $board;

        } catch (Exception $e) {
            throw new Exception("Failed to read board: " . $e->getMessage());
        }
    }

public function do_move($input) {
    global $mysqli;

    $player = $this->getPlayerByToken();
    if (!$player) {
        echo "Invalid token.";
        return;
    }

    if (!isset($input['tileIndex']) || !isset($input['row']) || !isset($input['col'])) {
        echo "Invalid input parameters.";
        return;
    }

    $playerSlot = $player['player_slot']; 
    $tileIndex = $input['tileIndex']; 
    $row = $input['row'];
    $col = $input['col'];

    $mysqli->begin_transaction();

    try {
        $stmt = $mysqli->prepare("SELECT status, p_turn FROM game_status");
        $stmt->execute();
        $result = $stmt->get_result();
        $gameStatusRow = $result->fetch_assoc();

        if (!$gameStatusRow) {
            throw new Exception("Game status could not be retrieved");
        }

        $gameStatus = $gameStatusRow['status'];
        $currentPlayerSlot = $gameStatusRow['p_turn'];

        if ($gameStatus !== 'started') {
            throw new Exception("Game is not started \n");
        }
        if ($playerSlot !== $currentPlayerSlot) {
            throw new Exception("It's not your turn \n");
        }

        $playerId = $player['id'];
        $hand = $this->read_hand(false); 

        if (!isset($hand[$tileIndex - 1])) {
            throw new Exception("Invalid tile index");
        }

        $tileId = $hand[$tileIndex - 1]['id'];

        if (!validateMove($tileId, $row, $col)) {
            throw new Exception("Invalid move");
        }

        placeTile($playerId, $tileId, $row, $col);

        $score = calculateScore($playerId, $row, $col);
        drawTile($playerId);
        $hand = $this->read_hand(true); 
        read_board();
        $mysqli->commit();

        echo "Move completed successfully. Score: $score";

    } catch (Exception $e) {
        $mysqli->rollback();
        echo "Move failed: " . $e->getMessage();
    }
}
}

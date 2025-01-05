<?php
require_once 'dbconnect.php';
require_once 'game.php';
require_once 'users.php';

class GameController {
    private $mysqli;


    public function __construct() {
        $this->mysqli = $GLOBALS['mysqli'];
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
            http_response_code(400);
            echo json_encode(['errormesg' => "All player slots are taken."]);
            return;
        }

        $token = md5($name . time());

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
             http_response_code(200);
             echo json_encode($newPlayer, JSON_PRETTY_PRINT);
        $st->close();

    if (count($takenSlots) == 0) {
        set_status('initialized');
    } elseif (count($takenSlots) == 1) {
        set_status('started');
    }
}
 
public function exchange_tile($input) {
    global $mysqli;

    $player_slot = $input['player_slot'];
    $tile_index = $input['tile_index'];

    $game_status = read_status();
    $current_turn = $game_status['p_turn'];

    if ($current_turn !== $player_slot) {
        echo "It is not your turn.";
        return;
    }

    $sql = "SELECT id, tilesPlacedThisTurn FROM players WHERE player_slot = ?";
    $st = $mysqli->prepare($sql);
    $st->bind_param('s', $player_slot);
    $st->execute();
    $result = $st->get_result();
    $player = $result->fetch_assoc();
    $player_id = $player['id'];
    $tilesPlacedThisTurn = $player['tilesPlacedThisTurn'];
    $st->close();

    if ($tilesPlacedThisTurn != 0) {
        echo "You have already placed tiles this turn.";
        return;
    }

    tileToBag($player_slot, $tile_index);
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

        //prepare the game
        initBoard();
        cleanBoard();
        fillTileBag();

        // draw for p1,p2
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

    $game_status = read_status();
    $current_turn = $game_status['p_turn'];

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

public function do_move($input) {
    global $mysqli;

    // Extract values from input array
    $playerSlot = $input['playerSlot'];
    $tileIndex = $input['tileIndex']; // Index of the tile in the player's hand
    $row = $input['row'];
    $col = $input['col'];

    // Start a transaction
    $mysqli->begin_transaction();

    try {
        // Retrieve playerId from playerSlot
        $stmt = $mysqli->prepare("SELECT id FROM players WHERE player_slot = ?");
        $stmt->bind_param('s', $playerSlot);
        $stmt->execute();
        $result = $stmt->get_result();
        $player = $result->fetch_assoc();

        if (!$player) {
            throw new Exception("Invalid player slot");
        }

        $playerId = $player['id'];

        // Retrieve the player's hand
        $hand = read_hand($playerSlot);

        // Check if the tile index is valid
        if (!isset($hand[$tileIndex - 1])) {
            throw new Exception("Invalid tile index");
        }

        // Get the tile ID from the hand using the index
        $tileId = $hand[$tileIndex - 1]['id'];

        // Step 1: Validate the move
        if (!validateMove($tileId, $row, $col)) {
            throw new Exception("Invalid move");
        }

        // Step 2: Place the tile
        placeTile($playerId, $tileId, $row, $col);

        // Step 3: Calculate the score
        $score = calculateScore($playerId, $row, $col);

        // Commit the transaction
        $mysqli->commit();

        echo "Move completed successfully. Score: $score";

    } catch (Exception $e) {
        // Rollback the transaction in case of error
        $mysqli->rollback();
        echo "Move failed: " . $e->getMessage();
    }
}



}
?>
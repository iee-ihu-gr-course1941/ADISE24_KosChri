<?php
include('dbconnect.php');

function createGame($playerId1,$playerId2) {
    global $mysqli;


    $stmt = $mysqli -> prepare("INSERT INTO games (player1_id, player2_id, status) VALUES (?,?, 'ongoing')");
    $stmt -> bind_param('ii', $playerId1,$playerId2);

    if($stmt->execute()){
        $gameId = $stmt->insert_id;

        return $gameId;
    }else {
        return false;
    }
    $stmt->close();

    }

    //testing function cG ()
$playerId1 = '800';
$playerId2 = '255';
$gameId = createGame($playerId1,$playerId2);

if ($gameId !== false) {
    echo "Game created successfully with ID: " . $gameId;
} else {
    echo "Failed to create game.";
}



function addPlayer($name){
    global $mysqli;
    $token = bin2hex(random_bytes(16));

    $stmt = $mysqli->prepare("INSERT INTO players (name,token, score) VALUES (?,?,0)");
    $stmt -> bind_param('ss', $name, $token);

    if($stmt->execute()){
        $playerId = $stmt ->insert_id;
        return $playerId;

    }else{
        throw new Exception("Failed to add player.");
    }
    $stmt->close();
}
// test function addPlayer()
try {
    $playerName = "Kostas"; 
    $newPlayerId = addPlayer($playerName);

    echo "Player added successfully with ID: " . $newPlayerId;
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}


// function drawTile($gameId, $playerId) {
//     global $mysqli;

//     $stmt = $mysqli->prepare("SELECT * FROM tile_bag WHERE is_used = 0 LIMIT 1");
//     $stmt->execute();
//     $result = $stmt->get_result();
    
//     if ($result->num_rows > 0) {
//         // Fetch the tile data
//         $tile = $result->fetch_assoc();
//         $tileId = $tile['tile_id'];

//         $updateTile = $mysqli->prepare("UPDATE tile_bag SET is_used = 1 WHERE tile_id = ?");
//         $updateTile->bind_param("i", $tileId);
//         $updateTile->execute();

//         $addTileToPlayer = $mysqli->prepare("INSERT INTO player_tiles (game_id, player_id, tile_id) VALUES (?, ?, ?)");
//         $addTileToPlayer->bind_param("iii", $gameId, $playerId, $tileId);
//         $addTileToPlayer->execute();

//         return "Tile drawn successfully!";
//     } else {
//         return "No tiles available to draw.";
//     }
// }
// if (is_numeric($gameId)) {
//     echo "Game created successfully with ID: " . $gameId;

//     echo addPlayer($gameId, $playerId);

//     echo drawTile($gameId, $playerId);
// } else {
//     echo $gameId; 
// }
?>

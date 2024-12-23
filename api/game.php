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

//     //testing function cG ()
// $playerId1 = '800';
// $playerId2 = '255';
// $gameId = createGame($playerId1,$playerId2);



// have to lock the players inside the game do not forget! 


// if ($gameId !== false) {
//     echo "Game created successfully with ID: " . $gameId;
// } else {
//     echo "Failed to create game.";
// }



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
// try {
//     $playerName = "Kostas"; 
//     $newPlayerId = addPlayer($playerName);

//     echo "Player added successfully with ID: " . $newPlayerId;
// } catch (Exception $e) {
//     echo "Error: " . $e->getMessage();
// }





function fillTileBag(){
    global $mysqli;

    $colors = ["red", "blue", "green", "yellow"];
    $shapes = ["circle", "square", "triangle", "star"];
    $points = [1, 2, 3, 4]; 
    $mysqli -> begin_transaction();
//points needed or not ?? 
    try{
        $mysqli->query("DELETE FROM tile_bag");
        // $mysqli->query("DELETE FROM tiles");

        $stmt_tile = $mysqli->prepare("INSERT INTO tiles (color,shape,points) VALUES (?,?, ?)");
        $stmt_bag = $mysqli-> prepare("INSERT INTO tile_bag (tile_id,quantity) VALUES (?,?)");

        foreach ($colors as $color) {
            foreach($shapes as $shape){
                foreach($points as $point){
                $stmt_tile-> bind_param("ssi", $color, $shape, $point);
                $stmt_tile->execute();
                $tileId= $stmt_tile ->insert_id;   // might need to change to tile_id

                $quantity = 3;
                $stmt_bag->bind_param("ii", $tileId, $quantity);
                $stmt_bag->execute();


            }
            
        }
    }

        $mysqli->commit();
        $stmt_bag->close();
        $stmt_tile->close();
        echo "Tile bag filled!";

    }catch( Exception $e){
        $mysqli->rollback();
        throw $e;
    }    
}
        //test     test         test        test        test
try{
    fillTileBag();
}catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

function drawTile(){
    global $mysqli;

    $sql = 'SELECT tile_id FROM tile_bag ORDER BY RAND() LIMIT 1';
    $st = $mysqli->prepare($sql);
    $st->execute();
    $result = $st->get_result();
    $row = $result->fetch_assoc();
    $tileId = $row['tile_id'];

    $sql = "UPDATE tile_bag SET quantity = quantity -1 WHERE tile_id = ?";
    $st =  $mysqli->prepare($sql);
    $st -> bind_param('i' , $tileId);
    $st -> execute();

    $sql =  "DELETE FROM tile_bag WHERE tile_id= ? AND quantity <=0";
    $st = $mysqli-> prepare($sql);
    $st ->bind_param('i', $tileId);
    $st->execute();

    return $tileId;

}

function drawTileStart($playerId){
    global $mysqli;

    $mysqli->begin_transaction();
    
    try{
        $st_hand = $mysqli->prepare("INSERT INTO player_hands (player_id,tile_id) VALUES (?,?)");
        for($i=0 ; $i<6; $i++){
            $tileId = drawTile();

            $st_hand -> bind_param('ii', $playerId,$tileId);
            $st_hand->execute();

        }
    
    $mysqli->commit();
    $st_hand->close();
       echo "Starting tiles drawn successfully for player ID: $playerId.";
    }catch (Exception $e) {
        // Rollback the transaction in case of error
        $mysqli->rollback();
        throw $e;
    }

}

// test     test    test    teest

// try {
//     $playerId = 813; // Replace with the actual player ID you want to draw tiles for
//     drawTileStart($playerId);
// } catch (Exception $e) {
//     echo "Error: " . $e->getMessage();
// }




function initializeGame($playerName1,$playerName2){

    global $mysqli;

    $playerId1 = addPlayer($playerName1);
    $playerId2 = addPlayer($playerName2);

    $st= $mysqli->prepare("INSERT INTO games (player1_id,player2_id,status) VALUES (?,?, 'ongoing')");
    $st->bind_param('ii', $playerId1,$playerId2);

    if($st->execute()){
        $gameId = $st->insert_id;

        $players = [ $playerId1, $playerId2];
        shuffle($players);
        $currentTurn = $players[0];

        $sql = "UPDATE games SET current_turn= ? WHERE id = ?";
        $st = $mysqli->prepare($sql);
        $st->bind_param('ii', $currentTurn ,$gameId);
        $st->execute();

        return $gameId;
    }else{
        throw new Exception("Failed to create game.");
    }
    $stmt->close();
}


//testing
try {
    $playerName1 = "Kos"; 
    $playerName2 = "Chris"; 
    $gameId = initializeGame($playerName1, $playerName2);
    echo "Game initialized successfully with ID: " . $gameId;
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}




function initBoard(){
    global $mysqli;


    $mysqli->begin_transaction();

    try{

        for($row =1 ; $row <=15; $row++){
            for($col=1; $col <=15; $col++){
                $stmt = $mysqli -> prepare("INSERT INTO board (row,col) VALUES (?,?)");
                $stmt -> bind_param('ii', $row, $col);
                $stmt->execute();
            }
        }
        $mysqli->commit();
        echo "Board init successfully.";

    }catch(Exception $e){
        $mysqli->rollback();
        throw $e;
    }
}

function cleanBoard(){
    global $mysqli;

    $mysqli->begin_transaction();

    try{
        $stmt = $mysqli->prepare("UPDATE board SET tile_id= NULL");
        $stmt->execute();

        $mysqli->commit();
        echo "Board cleaned successfully.";
    }catch (Exception $e) {
        $mysqli->rollback();
        throw $e;
    }
}

///test     test test   test    test    test
// try {
//     // Initialize the board
//     initBoard();

//     // Clean the board
//     cleanBoard();
// } catch (Exception $e) {
//     echo "Error: " . $e->getMessage();
// }

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

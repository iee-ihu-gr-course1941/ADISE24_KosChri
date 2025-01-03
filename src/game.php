<?php
include('dbconnect.php');
// 1111
// function createGame($playerId1,$playerId2) {
//     global $mysqli;


//     $stmt = $mysqli -> prepare("INSERT INTO games (player1_id, player2_id, status) VALUES (?,?, 'ongoing')");
//     $stmt -> bind_param('ii', $playerId1,$playerId2);

//     if($stmt->execute()){
//         $gameId = $stmt->insert_id;

//         return $gameId;
//     }else {
//         return false;
//     }
//     $stmt->close();

//     }
//1111
function createGame($playerId1, $playerId2) {
    global $mysqli;

    
    $stmt = $mysqli->prepare("SELECT id FROM players WHERE id IN (?, ?)");
    $stmt->bind_param('ii', $playerId1, $playerId2);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows < 2) {
        // Insert missing players
        if ($stmt->num_rows == 0) {
            $stmt = $mysqli->prepare("INSERT INTO players (id, name) VALUES (?, ?), (?, ?)");
            $name1 = "Player1";
            $name2 = "Player2";
            $stmt->bind_param('isii', $playerId1, $name1, $playerId2, $name2);
            $stmt->execute();
        } else {
            $stmt = $mysqli->prepare("INSERT INTO players (id, name) VALUES (?, ?)");
            if ($stmt->num_rows == 1) {
                $existingPlayer = $stmt->fetch();
                if ($existingPlayer['id'] == $playerId1) {
                    $stmt->bind_param('is', $playerId2, $name2);
                } else {
                    $stmt->bind_param('is', $playerId1, $name1);
                }
                $stmt->execute();
            }
        }
    }
    $stmt->close();

    // Insert game
    $stmt = $mysqli->prepare("INSERT INTO games (player1_id, player2_id, status) VALUES (?, ?, 'ongoing')");
    $stmt->bind_param('ii', $playerId1, $playerId2);

    if ($stmt->execute()) {
        $gameId = $stmt->insert_id;
        return $gameId;
    } else {
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



// function addPlayer($name){
//     global $mysqli;
//     $token = bin2hex(random_bytes(16));

//     $stmt = $mysqli->prepare("INSERT INTO players (name,token, score) VALUES (?,?,0)");
//     $stmt -> bind_param('ss', $name, $token);

//     if($stmt->execute()){
//         $playerId = $stmt ->insert_id;
//         return $playerId;

//     }else{
//         throw new Exception("Failed to add player.");
//     }
//     $stmt->close();
// }

// try {
//     $playerName = "Kostas"; 
//     $newPlayerId = addPlayer($playerName);

//     echo "Player added successfully with ID: " . $newPlayerId;
// } catch (Exception $e) {
//     echo "Error: " . $e->getMessage();
// }





function fillTileBag() {
    global $mysqli;

    $colors = ["red", "blue", "green", "yellow"];
    $shapes = ["circle", "square", "triangle", "star"];

    $mysqli->begin_transaction();

    try {
        $mysqli->query("DELETE FROM tiles");

        $stmt_tile = $mysqli->prepare("INSERT INTO tiles (color, shape) VALUES (?, ?)");

        foreach ($colors as $color) {
            foreach ($shapes as $shape) {
                for ($i = 0; $i < 3; $i++) {
                    $stmt_tile->bind_param("ss", $color, $shape);
                    $stmt_tile->execute();
                }
            }
        }

        $mysqli->commit();
        $stmt_tile->close();
        echo "Tile bag filled!";
    } catch (Exception $e) {
        $mysqli->rollback();
        throw $e;
    }
}
        //test     test         test        test        test
// try{
//     fillTileBag();
// }catch (Exception $e) {
//     echo "Error: " . $e->getMessage();
// }

function drawTile($playerId) {
    global $mysqli;
    $mysqli->begin_transaction();

    try {
        $sql = 'SELECT id FROM tiles WHERE inside_bag = 1 ORDER BY RAND() LIMIT 1';
        $st = $mysqli->prepare($sql);
        $st->execute();
        $result = $st->get_result();
        $row = $result->fetch_assoc();
        $tileId = $row['id']; 

        $sql = "INSERT INTO player_hands (player_id, tile_id) VALUES (?, ?)";
        $st = $mysqli->prepare($sql);
        $st->bind_param('ii', $playerId, $tileId);
        $st->execute();

        $sql = "UPDATE tiles SET inside_bag = 0 WHERE id = ?";
        $st = $mysqli->prepare($sql);
        $st->bind_param('i', $tileId);
        $st->execute();

        $mysqli->commit();
        return $tileId;
    } catch (Exception $e) {
        $mysqli->rollback();
        throw $e;
    }
}

//replace
function tileToBag($playerId, $tileId) {
    global $mysqli;
    $mysqli->begin_transaction();

    try {
        $sql = "DELETE FROM player_hands WHERE player_id = ? AND tile_id = ?";
        $st = $mysqli->prepare($sql);
        $st->bind_param('ii', $playerId, $tileId);
        $st->execute();

        if ($st->affected_rows > 0) {
            $sql = "UPDATE tiles SET inside_bag = 1 WHERE id = ?";
            $st = $mysqli->prepare($sql);
            $st->bind_param('i', $tileId);
            $st->execute();

            $sql = "UPDATE players SET tilesDiscardedThisTurn = tilesDiscardedThisTurn + 1 WHERE id = ?";
            $st = $mysqli->prepare($sql);
            $st->bind_param('i', $playerId);
            $st->execute();
        }

        $mysqli->commit();
        return $tileId;
    } catch (Exception $e) {
        $mysqli->rollback();
        throw $e;
    }
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
// try {
//     $playerName1 = "Kos"; 
//     $playerName2 = "Chris"; 
//     $gameId = initializeGame($playerName1, $playerName2);
//     echo "Game initialized successfully with ID: " . $gameId;
// } catch (Exception $e) {
//     echo "Error: " . $e->getMessage();
// }




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

///test     test test   test    test    test




function placeTile($playerId, $tileId, $row, $col){
    global $mysqli;
// need to check before placing if tile id -- exists -- in table players hand
// need to check before placing if tile id -- exists -- in table players hand
// need to check before placing if tile id -- exists -- in table players hand


    $mysqli->begin_transaction();

    try{
        $stmt = $mysqli->prepare("SELECT tile_id FROM board WHERE row=? AND col=? ");
        $stmt->bind_param('ii', $row,$col);
        $stmt->execute();
        $result = $stmt->get_result();
        $row_result = $result->fetch_assoc();

        if($row_result && $row_result['tile_id'] != NULL){ 
            throw new Exception("Position occupied");
        }

        $stmt = $mysqli->prepare("UPDATE board SET player_id= ?, tile_id= ? WHERE row= ? AND col= ?");
        $stmt->bind_param('iiii' , $playerId, $tileId, $row, $col);
        $stmt->execute();


        $stmt = $mysqli->prepare("DELETE FROM player_hands WHERE player_id=? AND tile_id= ?");
        $stmt ->bind_param('ii', $playerId, $tileId);
        $stmt->execute();
        $st = $mysqli->prepare("UPDATE players SET tilesPlacedThisTurn = tilesPlacedThisTurn+1 WHERE id= ?");
        $st ->bind_param('i', $playerId);
        $st->execute();

        $mysqli->commit();

        echo "Tile placed successfully.";
    } catch (Exception $e) {
        $mysqli->rollback();
        throw $e;
    }

}
///test     test test   test    test    test
///test     test test   test    test    test

// try {
//     $playerId = 813; // Replace with the actual player ID
//     $tileId = 451; // Replace with the actual tile ID
//     $row = 5; // Replace with the actual row
//     $col = 5; // Replace with the actual column
//     placeTile($playerId, $tileId, $row, $col);
// } catch (Exception $e) {
//     echo "Error: " . $e->getMessage();
// }
///test     test test   test    test    test
///test     test test   test    test    test




function validateMove($tileId,$row,$col){
    global $mysqli;

    $mysqli->begin_transaction();

    try{
        if($row < 0 || $row>= 15 || $col <0 || $col>15){
            throw new Exception("Place inside the borders please");
        }

        $st = $mysqli->prepare("SELECT tile_id FROM board WHERE row=? AND col = ?");
        $st ->bind_param('ii', $row,$col);
        $st->execute();
        $result = $st->get_result();
        $row_result = $result -> fetch_assoc();


        if ($row_result && $row_result['tile_id'] != NULL) {
            throw new Exception("Position is already occupied.");
        }

        $st = $mysqli -> prepare("SELECT color,shape FROM tiles WHERE id=?");
        $st ->bind_param('i' , $tileId);
        $st->execute();
        $result = $st->get_result();
        $tile = $result->fetch_assoc();

        if(!$tile){
            throw new Exception("Tile doesnt exist");
        }

        $tileColor = $tile['color'];
        $tileShape = $tile['shape'];

        $adjacentPositions = [
            ['row' => $row - 1, 'col' => $col], // pano
            ['row' => $row + 1, 'col' => $col], // kato
            ['row' => $row, 'col' => $col - 1], // aristera
            ['row' => $row, 'col' => $col + 1], // dexia
        ];
         $adjacentTileFound = false;

        foreach($adjacentPositions as $position){
            $adjRow= $position['row'];
            $adjCol = $position['col'];

            if( $adjRow >= 0 && $adjRow < 15 && $adjCol >= 0 && $adjCol <15 ){
                $st = $mysqli->prepare(" SELECT tile_id FROM board WHERE row=? AND col=? ");
                $st->bind_param('ii', $adjRow, $adjCol);
                $st->execute();
                $result = $st->get_result();
                $adjTileResult = $result->fetch_assoc();

                if($adjTileResult && $adjTileResult['tile_id'] != NULL){
                    $adjacentTileFound = true;
                    $adjTileId = $adjTileResult['tile_id'];

                }
                $st = $mysqli ->prepare("SELECT color,shape FROM tiles WHERE id= ?");
                $st->bind_param('i', $adjTileId);
                $st->execute();
                $result= $st->get_result();
                $adjTile = $result->fetch_assoc();

                if ($adjTile) {
                        $adjTileColor = $adjTile['color'];
                        $adjTileShape = $adjTile['shape'];
                        if ($tileColor != $adjTileColor && $tileShape != $adjTileShape) {
                            throw new Exception("Tile does not match adjacent tiles.");
                        }
            }
        }



    }

    $st= $mysqli->prepare("SELECT COUNT(*) AS count FROM board WHERE tile_id IS NOT NULL");
    $st->execute();
    $result = $st->get_result();
    $totalTiles = $result->fetch_assoc()['count'];

    if ($totalTiles > 0 && !$adjacentTileFound) {
            throw new Exception("Tile must be placed adjacent to an existing tile.");
        }

    $mysqli->commit();

        return true;
}catch (Exception $e) {
       
        $mysqli->rollback();
        return false; 
    }
}



function calculateScore($playerId, $row, $col){

    global $mysqli;

    $score = 0;
    $directions = [
        ['row' => -1, 'col' => 0], // pano
        ['row' => 1, 'col' => 0],  // kato
        ['row' => 0, 'col' => -1], // aristera
        ['row' => 0, 'col' => 1],  // dexia
    ];

    foreach ($directions as $direction) {
        $currentRow = $row;
        $currentCol = $col;
        $lineLength = 0; 

        while (true) {
            $currentRow += $direction['row'];
            $currentCol += $direction['col'];

            $st = $mysqli->prepare("SELECT tile_id FROM board WHERE row = ? AND col = ?");
            $st->bind_param('ii', $currentRow, $currentCol);
            $st->execute();
            $result = $st->get_result();
            $tile = $result->fetch_assoc();
            if ($tile && $tile['tile_id'] != NULL) {
                $lineLength++;
            } else {
                break;
            }
            
        }
        if ($lineLength > 0) {
            $score += $lineLength + 1; 

            
            if ($lineLength + 1 == 6) {
                $score += 6; 
            }
        }
    }
     $st = $mysqli->prepare("UPDATE players SET score = score + ? WHERE id = ?");
    if (!$st) {
        throw new Exception("Prepare statement failed: " . $mysqli->error);
    }
    $st->bind_param('ii', $score, $playerId);
    $st->execute();

    return $score;
}

function endTurn($playerId, $row, $col) {
    global $mysqli;

    $mysqli->begin_transaction();

    try {
        
        $stmt = $mysqli->prepare("SELECT tilesPlacedThisTurn FROM players WHERE id = ?");
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $mysqli->error);
        }
        $stmt->bind_param('i', $playerId);
        $stmt->execute();
        $result = $stmt->get_result();
        if (!$result) {
            throw new Exception("get_result() failed: " . $stmt->error);
        }
        $tilesPlacedThisTurn = $result->fetch_assoc()['tilesPlacedThisTurn'];

        // Reset 
        $stmt = $mysqli->prepare("UPDATE players SET tilesPlacedThisTurn = 0 WHERE id = ?");
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $mysqli->error);
        }
        $stmt->bind_param('i', $playerId);
        $stmt->execute();

        // draw
        for ($i = 0; $i < $tilesPlacedThisTurn; $i++) {
            drawTile($playerId);
        }

        $mysqli->commit();

        return true; 
    } catch (Exception $e) {
      
        $mysqli->rollback();
        return false; 
    }
}
//test test test test test test test//test test test test test test test
// try {
//     $tileId = 573; 
//     $row = 8; 
//     $col = 5; 
    
//     if (validateMove($tileId, $row, $col)) {
//         echo "Move is valid.";
//         placeTile(813,$tileId,$row,$col);

//     } else {
//         echo "Move is invalid.";
//     }
// } catch (Exception $e) {
//     echo "Error: " . $e->getMessage();
// }

//test test test test test test test//test test test test test test test

?>

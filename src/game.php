<?php
include('dbconnect.php');


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
                for ($i = 0; $i < 2; $i++) {
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


function drawTile($playerId) {
    global $mysqli;

    try {
        $mysqli->begin_transaction();

        $sql = 'SELECT id FROM tiles WHERE inside_bag = 1 ORDER BY RAND() LIMIT 1';
        $st = $mysqli->prepare($sql);
        $st->execute();
        $result = $st->get_result();
        $row = $result->fetch_assoc();
        $tileId = $row['id']; 
        $st->close();

        $sql = "UPDATE tiles SET inside_bag = 0 WHERE id = ?";
        $st = $mysqli->prepare($sql);
        $st->bind_param('i', $tileId);
        $st->execute();
        $st->close();

        $sql = "INSERT INTO player_hands (player_id, tile_id) VALUES (?, ?)";
        $st = $mysqli->prepare($sql);
        $st->bind_param('ii', $playerId, $tileId);
        $st->execute();
        $st->close();
        $mysqli->commit();

        return $tileId;
    } catch (Exception $e) {
        $mysqli->rollback();
        throw $e;
    }
}
function read_hand($player_slot) {
    global $mysqli;

    $sql = "SELECT id FROM players WHERE player_slot = ?";
    $st = $mysqli->prepare($sql);
    $st->bind_param('s', $player_slot);
    $st->execute();
    $result = $st->get_result();
    $player = $result->fetch_assoc();
    $player_id = $player['id'];
    $st->close();

    $sql = "SELECT t.id, t.shape, t.color FROM player_hands ph JOIN tiles t ON ph.tile_id = t.id WHERE ph.player_id = ?";
    $st = $mysqli->prepare($sql);
    $st->bind_param('i', $player_id);
    $st->execute();
    $result = $st->get_result();

    $hand = $result->fetch_all(MYSQLI_ASSOC);
    $st->close();

    foreach ($hand as $index => $tile) {
        echo ($index + 1) . ". Shape: " . $tile['shape'] . ", Color: " . $tile['color'] . "\n";
    }

    echo "$player_slot's hand";
    return $hand;
}

//replace
function tileToBag($player_slot, $tile_index) {
    global $mysqli;

    $sql = "SELECT id FROM players WHERE player_slot = ?";
    $st = $mysqli->prepare($sql);
    $st->bind_param('s', $player_slot);
    $st->execute();
    $result = $st->get_result();
    $player = $result->fetch_assoc();
    $player_id = $player['id'];
    $st->close();

    $hand = read_hand($player_slot);

    if ($tile_index < 1 || $tile_index > count($hand)) {
        echo "Invalid tile index.";
        return;
    }

    $tile_id = $hand[$tile_index - 1]['id'];

    $mysqli->begin_transaction();

    try {
        $sql = "DELETE FROM player_hands WHERE player_id = ? AND tile_id = ?";
        $st = $mysqli->prepare($sql);
        $st->bind_param('ii', $player_id, $tile_id);
        $st->execute();
         echo "Deleted tile: $st->affected_rows rows affected<br>";

        if ($st->affected_rows > 0) {
            $sql = "UPDATE tiles SET inside_bag = 1 WHERE id = ?";
            $st = $mysqli->prepare($sql);
            $st->bind_param('i', $tile_id);
            $st->execute();
                        echo "Updated tile inside_bag: $st->affected_rows rows affected<br>";

            $sql = "UPDATE players SET tilesDiscardedThisTurn = tilesDiscardedThisTurn + 1 WHERE id = ?";
            $st = $mysqli->prepare($sql);
            $st->bind_param('i', $player_id);
            $st->execute();
                        echo "Updated tilesDiscardedThisTurn: $st->affected_rows rows affected<br>";


                        $sql = "SELECT tilesDiscardedThisTurn FROM players WHERE id = ?";
            $st = $mysqli->prepare($sql);
            $st->bind_param('i', $player_id);
            $st->execute();
            $result = $st->get_result();
            $player = $result->fetch_assoc();
            echo "tilesDiscardedThisTurn value after update: " . $player['tilesDiscardedThisTurn'] . "<br>";


        }

        $mysqli->commit();
        echo "Transaction complete. Tile $tile_index placed back into the tile bag.";
        return $tile_id;
    } catch (Exception $e) {
        $mysqli->rollback();
        throw $e;
    }
}

function drawTileStart($playerId){
    global $mysqli;

    $mysqli->begin_transaction();
    
    try{        
        for($i=0 ; $i<6; $i++){
            $tileId = drawTile($playerId);
        }
    
    $mysqli->commit();
    echo "Starting tiles drawn successfully for player ID: $playerId.";
    }catch (Exception $e) {
     
        $mysqli->rollback();
        throw $e;
    }
}
 // den leitourgei opos tha ithela
function read_board() {
    global $mysqli;
    $sql = 'select * from board';
    $st = $mysqli->prepare($sql);
    $st->execute();
    $res = $st->get_result();
    return($res->fetch_all(MYSQLI_ASSOC));
}
// na do ligo
function initBoard(){
    global $mysqli;


    $mysqli->begin_transaction();

    try{
        $st = $mysqli->prepare("DELETE FROM board");
        $st->execute();

        for($row =1 ; $row <=3; $row++){
            for($col=1; $col <=3; $col++){
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

function read_status() {
    global $mysqli;

    $sql = "SELECT status, p_turn, result FROM game_status";
    $st = $mysqli->prepare($sql);
    $st->execute();

    $result = $st->get_result();
    $status = $result->fetch_assoc();

    $st->close();

    return $status;
}

function set_status($status){
    global $mysqli;
    $mysqli->begin_transaction();
    try{
        $sql = "UPDATE game_status SET status= ? ";
        $st = $mysqli->prepare($sql);
        $st ->bind_param('s', $status);
        $st ->execute();
        $mysqli->commit();
        $st->close();
        echo "Game is $status.";
    }  catch (Exception $e) {
        $mysqli->rollback();
        throw $e;
    }
}



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

?>

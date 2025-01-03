<?php
include('dbconnect.php');
function show_users() {
    global $mysqli;
    $sql = 'SELECT name FROM players';
    $st = $mysqli->prepare($sql);
    $st->execute();
    $res = $st->get_result();
    header('Content-type: application/json');
    print json_encode($res->fetch_all(MYSQLI_ASSOC), JSON_PRETTY_PRINT);
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

// try {
//     $playerName = "christos"; 
//     $newPlayerId = addPlayer($playerName);

//     echo "Player added successfully with ID: " . $newPlayerId;
// } catch (Exception $e) {
//     echo "Error: " . $e->getMessage();
// }

function current_hand(){

}

?>
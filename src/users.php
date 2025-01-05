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
// public function setUser($input) {
//         global $mysqli;

//         // Check if the name is provided
//         if (!isset($input['name']) || $input['name'] == '') {
//             http_response_code(400);
//             echo json_encode(['errormesg' => "No name given."]);
//             exit;
//         }

//         // Get the name and player slot from the input
//         $name = $input['name'];
//         $player_slot = $input['player_slot']; // Assuming the input contains which player slot to set (e.g., 'p1' or 'p2')

//         // Check if the player slot (player1 or player2) is already taken
//         $sql = "SELECT count(*) as c FROM players WHERE slot=? AND name IS NOT NULL";
//         $st = $mysqli->prepare($sql);
//         $st->bind_param('s', $player_slot);
//         $st->execute();
//         $res = $st->get_result();
//         $r = $res->fetch_all(MYSQLI_ASSOC);
//         if ($r[0]['c'] > 0) {
//             http_response_code(400);
//             echo json_encode(['errormesg' => "Player $player_slot is already set."]);
//             exit;
//         }

//         // Update the player's record with the name and generate a unique token
//         $token = md5($name . time());
//         $sql = "UPDATE players SET name=?, token=? WHERE slot=?";
//         $st2 = $mysqli->prepare($sql);
//         $st2->bind_param('sss', $name, $token, $player_slot);
//         $st2->execute();

//         // Retrieve and return the player information
//         $sql = "SELECT name, token FROM players WHERE slot=?";
//         $st = $mysqli->prepare($sql);
//         $st->bind_param('s', $player_slot);
//         $st->execute();
//         $res = $st->get_result();
//         http_response_code(200);
//         echo json_encode($res->fetch_all(MYSQLI_ASSOC), JSON_PRETTY_PRINT);
//     }


?>
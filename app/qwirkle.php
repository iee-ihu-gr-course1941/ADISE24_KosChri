<?php
require_once '../src/dbconnect.php';
require_once '../src/GameController.php';

$method = $_SERVER['REQUEST_METHOD'];

if (isset($_SERVER['PATH_INFO']) && $_SERVER['PATH_INFO'] !== '') {
    $request = explode('/', trim($_SERVER['PATH_INFO'], '/'));
} else {
    $request = [''];
}


$input = json_decode(file_get_contents('php://input'),true);

if ($input == null) {
    $input = [];
}
// if(isset($_SERVER['HTTP_X_TOKEN'])) {
//     $input['token']=$_SERVER['HTTP_X_TOKEN'];
// } else {
//     $input['token']='';
// }
// $pX=$input['pX']; // player x could be 1 or 2
// $gamename=$input['gamename'];

$gameController = new GameController();

switch ($request[0]) {
    case 'board' : 
        if ($method === 'GET') {
            $gameController->read_board();
        } else {
            http_response_code(405);
            echo json_encode(['message' => 'Method Not Allowed']);
        }
        break;
    case 'setUser':
        if ($method === 'POST') {
            $gameController->setUser($input);
        } else {
            http_response_code(405);
            echo json_encode(['message' => 'Method Not Allowed']);
        }
        break;
    case 'start':
        if ($method === 'POST') {
            $gameController->start();
        } else {
            http_response_code(405);
            echo json_encode(['message' => 'Method Not Allowed']);
        }
        break;
    case 'status':
        if($method === 'POST'){
            $gameController->setStatus();
        }else if($method === 'GET'){
            $gameController->read_status();
        }else {
            http_response_code(405);
            echo json_encode(['message' => 'Method Not Allowed']);
        }break;
    case 'exchange':
        if($method === 'POST'){
            $gameController->exchange_tile($input);
        }else {
            http_response_code(405);
            echo json_encode(['message' => 'Method Not Allowed']);
        }break;
    case 'turnEnd':
        if($method === 'POST'){
            $gameController-> end_turn();
        }else {
            http_response_code(405);
            echo json_encode(['message' => 'Method Not Allowed']);
        }break;
    case 'place':
        if($method === 'POST'){
            $gameController-> do_move($input);
        }else {
            http_response_code(405);
            echo json_encode(['message' => 'Method Not Allowed']);
        }break;
        default:
        http_response_code(404);
        echo json_encode(['message' => 'Not Found']);
        break;

   
}




?>
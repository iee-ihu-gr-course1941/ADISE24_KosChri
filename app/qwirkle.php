<?php
require_once '../src/dbconnect.php';
require_once '../src/GameController.php';

$method = $_SERVER['REQUEST_METHOD'];
$request = isset($_SERVER['PATH_INFO']) && $_SERVER['PATH_INFO'] !== '' 
    ? explode('/', trim($_SERVER['PATH_INFO'], '/')) 
    : [''];
    $token = $_SERVER['HTTP_X_PLAYER_TOKEN'] ?? null;


$input = json_decode(file_get_contents('php://input'), true) ?? [];


$gameController = new GameController();

switch ($request[0]) {
    case 'board': 
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
        if($method === 'GET') {
            $gameController->read_status(true);
        } else {
            echo json_encode(['message' => 'Method Not Allowed']);
        }
        break;
    case 'exchange':
        if ($method === 'POST') {
            $gameController->exchange_tile($input);
        } else {
            http_response_code(405);
            echo json_encode(['message' => 'Method Not Allowed']);
        }
        break;
    case 'turnEnd':
        if ($method === 'POST') {
            $gameController->end_turn($input);
        } else {
            http_response_code(405);
            echo json_encode(['message' => 'Method Not Allowed']);
        }
        break;
    case 'place':
        if ($method === 'POST') {
            $gameController->do_move($input);
        } else {
            http_response_code(405);
            echo json_encode(['message' => 'Method Not Allowed']);
        }
        break;
        //tester method join //
     case 'join':
        if ($method === 'GET') {
        if (!$token) {
            http_response_code(400);
            echo json_encode(['message' => 'Token is required']);
            return;
        }
        $gameController->joinGame();
    }else {
        http_response_code(405);
        echo json_encode(['message' => 'Method Not Allowed']);
    }
    break;
    case 'hand':
        if($method === 'GET'){
            $gameController->read_hand(true);
        }else {
        http_response_code(405);
        echo json_encode(['message' => 'Method Not Allowed']);
    }
    break;
    default:
        http_response_code(404);
        echo json_encode(['message' => 'Not Found']);
        break;
}
?>

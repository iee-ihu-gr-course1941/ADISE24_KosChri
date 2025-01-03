<?php
require_once '../src/dbconnect.php';
require_once '../src/GameController.php';

$method = $_SERVER['REQUEST_METHOD'];

$request = explode('/', trim($_SERVER['PATH_INFO'], '/'));
$input = json_decode(file_get_contents('php://input'),true);

if ($input == null) {
    $input = [];
}


$gameController = new GameController();

switch ($request[0]) {
    case 'createGame':
        if ($method === 'POST') {
            $gameController->createGame($input);
        } else {
            http_response_code(405);
            echo json_encode(['message' => 'Method Not Allowed']);
        }
        break;
    default:
        http_response_code(404);
        echo json_encode(['message' => 'Not Found']);
        break;
    case 'addPlayer' :
        if($method === 'POST'){
            $gameController -> addPlayer($input);
        }else{
            http_response_code(405);
            echo json_encode(['message' => 'Method not allowed']);
        }
        break;
    case 'fillTileBag' :
        if($method === 'POST'){
            $gameController ->fillTileBag();
        } else {
            http_response_code(405);
            echo json_encode(['message' => 'Method not allowed']);
        }
        break;
    case 'drawTile' :
        if ($method === 'POST') {
            $gameController -> drawTile($input);
        }else{
            http_response_code(405);
            echo json_encode(['message' => 'Method not allowed']);
        }
    // case 'initializeGame' :
    //     if($method === )
}
?>
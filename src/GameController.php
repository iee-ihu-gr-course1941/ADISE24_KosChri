<?php
require_once 'dbconnect.php';
require_once 'game.php';
require_once 'users.php';

class GameController {
    private $mysqli;

    public function __construct() {
        $this->mysqli = $GLOBALS['mysqli'];
    }

    public function createGame($input) {
        $playerId1 = $input['playerId1'];
        $playerId2 = $input['playerId2'];

        $gameId = createGame($playerId1, $playerId2);

        if ($gameId) {
            echo json_encode(['gameId' => $gameId]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Failed to create game']);
        }
    }

    public function addPlayer($input){
        try{
            $playerId = addPlayer($input['name']);
            echo json_encode(['playerId' => $playerId]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
    public function fillTileBag(){
        try{
            fillTileBag();
        } catch (Exception $e){
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
    public function drawTile($input){
        try{
            $tileId = drawTile($input['playerId']);
            echo json_encode(['tileId' => $tileId]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }

    }
}
?>
<?php
require_once 'dbconnect.php';
require_once 'game.php';

try{
	$tileId = tileToBag(932,3673);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
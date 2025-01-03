<?php
$user='root';
$pass='2310';
$host='localhost';
$db = 'projectDB';

$socket = '/run/mysqld/mysqld.sock';
$mysqli = new mysqli($host, $user, $pass, $db,null, $socket);
// echo 'success';
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . 
    $mysqli->connect_errno . ") " . $mysqli->connect_error;
}?>

<?php
$user='root';
$pass='2310';
$host='localhost';
$db = 'projectDB';

$socket = '/run/mysqld/mysqld.sock';

if(gethostname()=='users.iee.ihu.gr') {
    $mysqli = new mysqli($host, 'iee2019186', $pass, $db,null,'/home/student/iee/2019/iee2019186/mysql/run/mysql.sock');
}else{
$mysqli = new mysqli($host, $user, $pass, $db,null, $socket);
 // echo 'local connection';
}
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . 
    $mysqli->connect_errno . ") " . $mysqli->connect_error;
}?>


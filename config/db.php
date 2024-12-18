<?php
$host = 'localhost';
$dbport = '3306'; 
$db   = 'projectDB';
$user = 'root';
$pass = '2310';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;port=$dbport;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options); 
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>

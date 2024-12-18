<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once('../config/db.php'); 

try {
    // You can test your connection by doing a simple query, like fetching a list of tables
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll();
    
    echo "Connection successful! Here are the tables in the database:<br>";
    foreach ($tables as $table) {
        echo $table[0] . "<br>"; // Print each table name
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>

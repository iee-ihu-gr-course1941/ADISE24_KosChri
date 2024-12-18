<?php
require_once 'config/db.php';

try {
    // Attempt a simple query to verify the connection
    $stmt = $pdo->query("SELECT VERSION() AS db_version");
    $result = $stmt->fetch();
    
    // Output success and MySQL version
    echo "Database connection successful! 🎉<br>";
    echo "MySQL version: " . $result['db_version'];
} catch (PDOException $e) {
    // Output failure
    echo "Connection failed: " . $e->getMessage();
}
?>

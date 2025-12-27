<?php
// File: config/db.php

$host = 'sdb-67.hosting.stackcp.net'; 
$db_name = 'christmas_gift_db-35303437de8c';

// CHANGE THIS: Your screenshot shows the username is 'cgd'
$username = 'cgd'; 

// This is the password you set today (23/12/2025)
$password = 'Christmas2025!';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8mb4", $username, $password);
    
    // Set PDO to throw exceptions on error (Good for debugging)
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Set default fetch mode to associative array
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}
?>
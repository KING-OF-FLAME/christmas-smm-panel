<?php
// File: config/db.php

$host = 'YOUR_DATABASE_HOST'; 
$db_name = 'YOUR_DATABASE_NAME';
$username = 'YOUR_DATABASE_USERNAME'; 
$password = 'YOUR_DATABASE_PASSWORD';

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

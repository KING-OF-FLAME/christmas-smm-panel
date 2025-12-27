<?php
// File: admin/ban_user.php
require_once '../config/db.php';
require_once '../config/config.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($id > 0 && ($action == 'ban' || $action == 'unban')) {
    
    $status = ($action == 'ban') ? 1 : 0; // 1 = Banned, 0 = Active
    
    $sql = "UPDATE users SET is_banned = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$status, $id])) {
        // Optional: Log who banned whom if you have admin logs
    }
}

// Redirect back to list
header("Location: users.php");
exit;
?>
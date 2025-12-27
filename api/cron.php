<?php // File: cron.php ?><?php
// File: api/cron.php
// This script is meant to be run by a CRON JOB (Server Task Scheduler)
// It resets the daily spins for all users.

require_once '../config/db.php';

// Security: Optional - Add a secret key check to prevent random people from triggering it
// e.g., url: api/cron.php?secret=MySecretKey123
if (!isset($_GET['secret']) || $_GET['secret'] !== 'MySecretKey123') {
    die("Access Denied");
}

try {
    // Reset spins for all users to 5
    $daily_limit = 5;
    $stmt = $pdo->prepare("UPDATE users SET spins_left = ?");
    $stmt->execute([$daily_limit]);

    echo "Success: All users reset to $daily_limit spins.";
    
    // Log execution (Optional)
    error_log("Cron Job Ran: Spins Reset at " . date('Y-m-d H:i:s'));

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
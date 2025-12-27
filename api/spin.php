<?php
// File: api/spin.php
error_reporting(0);
ini_set('display_errors', 0);
header('Content-Type: application/json');
date_default_timezone_set('Asia/Kolkata');

require_once '../config/db.php';
require_once '../config/config.php';

if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Please login first']);
    exit;
}

$user_id = $_SESSION['user_id'];
$REFILL_TIME_SECONDS = 7200; // 2 Hours
$MAX_SPINS = 5;

try {
    // 1. Check User & Spins
    $stmt = $pdo->prepare("SELECT spins_left, coins, last_spin_time FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if (!$user) {
        echo json_encode(['status' => 'error', 'message' => 'User error. Relogin.']);
        exit;
    }

    // 2. Timer / Refill Logic
    $current_time = time();
    $last_spin_ts = $user['last_spin_time'] ? strtotime($user['last_spin_time']) : 0;
    $time_diff = $current_time - $last_spin_ts;

    // Only allow refill if spins are 0
    if ($user['spins_left'] <= 0) {
        if ($time_diff >= $REFILL_TIME_SECONDS) {
            // Time passed: Refill spins
            $update = $pdo->prepare("UPDATE users SET spins_left = ? WHERE id = ?");
            $update->execute([$MAX_SPINS, $user_id]);
            $user['spins_left'] = $MAX_SPINS; 
        } else {
            // Time NOT passed
            $min_left = ceil(($REFILL_TIME_SECONDS - $time_diff) / 60);
            echo json_encode(['status' => 'error', 'message' => "Refill in $min_left mins!", 'timer_active' => true]);
            exit;
        }
    }

    // Double check spins after potential refill
    if ($user['spins_left'] <= 0) {
         echo json_encode(['status' => 'error', 'message' => "No spins left. Check timer.", 'timer_active' => true]);
         exit;
    }

    // 3. EXACT SEGMENTS (Must match JS array order)
    $segments = [
        ['label' => '5 Coins',    'type' => 'coins', 'val' => 5,  'weight' => 30], // Index 0
        ['label' => 'Try Again',  'type' => 'none',  'val' => 0,  'weight' => 10], // Index 1
        ['label' => '10 Coins',   'type' => 'coins', 'val' => 10, 'weight' => 25], // Index 2
        ['label' => '5 Spins',    'type' => 'spins', 'val' => 5,  'weight' => 5],  // Index 3
        ['label' => '15 Coins',   'type' => 'coins', 'val' => 15, 'weight' => 15], // Index 4
        ['label' => 'Oops!',      'type' => 'none',  'val' => 0,  'weight' => 5],  // Index 5
        ['label' => '20 Coins',   'type' => 'coins', 'val' => 20, 'weight' => 9],  // Index 6
        ['label' => 'Jackpot',    'type' => 'coins', 'val' => 50, 'weight' => 1]   // Index 7
    ];

    // 4. Select Winner
    $total_weight = array_sum(array_column($segments, 'weight'));
    $rand = mt_rand(1, $total_weight);
    $current_weight = 0;
    $winner = null;
    $winning_index = 0;

    foreach ($segments as $index => $segment) {
        $current_weight += $segment['weight'];
        if ($rand <= $current_weight) {
            $winner = $segment;
            $winning_index = $index;
            break;
        }
    }
    if (!$winner) { $winner = $segments[0]; $winning_index = 0; }

    // 5. Update DB
    $pdo->beginTransaction();
    
    // Deduct Spin
    $deduct = $pdo->prepare("UPDATE users SET spins_left = spins_left - 1, last_spin_time = NOW() WHERE id = ?");
    $deduct->execute([$user_id]);

    // Give Reward
    if ($winner['type'] == 'coins') {
        $add = $pdo->prepare("UPDATE users SET coins = coins + ? WHERE id = ?");
        $add->execute([$winner['val'], $user_id]);
    } elseif ($winner['type'] == 'spins') {
        $add = $pdo->prepare("UPDATE users SET spins_left = spins_left + ? WHERE id = ?");
        $add->execute([$winner['val'], $user_id]);
    }

    $pdo->commit();

    // 6. Return Data
    $balStmt = $pdo->prepare("SELECT coins, spins_left FROM users WHERE id = ?");
    $balStmt->execute([$user_id]);
    $updatedUser = $balStmt->fetch();

    echo json_encode([
        'status' => 'success',
        'winning_index' => $winning_index,
        'prize' => $winner['label'], // This fixes "undefined" issue
        'message' => "You won " . $winner['label'] . "!",
        'new_coins' => $updatedUser['coins'],
        'spins_left' => $updatedUser['spins_left']
    ]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    echo json_encode(['status' => 'error', 'message' => 'System Error']);
}
?>
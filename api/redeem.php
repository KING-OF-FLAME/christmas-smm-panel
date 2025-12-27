<?php
// File: api/redeem.php
error_reporting(0);
ini_set('display_errors', 0);
header('Content-Type: application/json');

require_once '../config/db.php';
require_once '../config/config.php';

if (session_status() === PHP_SESSION_NONE) session_start();

// 1. Validate Login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Please login first']);
    exit;
}

$user_id = $_SESSION['user_id'];
$code = isset($_POST['code']) ? strtoupper(trim($_POST['code'])) : '';

if (empty($code)) {
    echo json_encode(['status' => 'error', 'message' => 'Enter a code']);
    exit;
}

try {
    // 2. Fetch Coupon
    $stmt = $pdo->prepare("SELECT * FROM coupons WHERE code = ?");
    $stmt->execute([$code]);
    $coupon = $stmt->fetch();

    if (!$coupon) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid Code']);
        exit;
    }

    // 3. Check Expiry
    if ($coupon['used_count'] >= $coupon['max_uses']) {
        echo json_encode(['status' => 'error', 'message' => 'Code Expired']);
        exit;
    }

    // 4. Check Previous Usage
    $check = $pdo->prepare("SELECT id FROM coupon_usage WHERE user_id = ? AND coupon_id = ?");
    $check->execute([$user_id, $coupon['id']]);
    
    if ($check->rowCount() > 0) {
        echo json_encode(['status' => 'error', 'message' => 'You already used this code!']);
        exit;
    }

    // 5. PROCESS TRANSACTION (The most important part)
    $pdo->beginTransaction();

    // A. Add Coins
    $stmtUser = $pdo->prepare("UPDATE users SET coins = coins + ? WHERE id = ?");
    $stmtUser->execute([$coupon['reward_coins'], $user_id]);

    // B. Mark as Used
    $stmtLog = $pdo->prepare("INSERT INTO coupon_usage (user_id, coupon_id) VALUES (?, ?)");
    $stmtLog->execute([$user_id, $coupon['id']]);

    // C. Update Coupon Counter
    $stmtCount = $pdo->prepare("UPDATE coupons SET used_count = used_count + 1 WHERE id = ?");
    $stmtCount->execute([$coupon['id']]);

    $pdo->commit();

    // 6. Get New Balance for Display
    $balStmt = $pdo->prepare("SELECT coins FROM users WHERE id = ?");
    $balStmt->execute([$user_id]);
    $new_bal = $balStmt->fetchColumn();

    echo json_encode([
        'status' => 'success', 
        'message' => 'Success! Added ' . $coupon['reward_coins'] . ' Coins.',
        'new_balance' => $new_bal
    ]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    echo json_encode(['status' => 'error', 'message' => 'DB Error: ' . $e->getMessage()]);
}
?>
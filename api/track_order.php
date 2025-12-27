<?php
// File: api/track_order.php
error_reporting(0);
ini_set('display_errors', 0);
header('Content-Type: application/json');

require_once '../config/db.php';
require_once '../config/config.php';

if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Please login first']);
    exit;
}

$user_id = $_SESSION['user_id'];
$order_input = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;

if ($order_input <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Enter a valid Order ID']);
    exit;
}

try {
    // 1. Find the order in YOUR database first (Security Check)
    // We ensure the order belongs to the logged-in user
    $stmt = $pdo->prepare("SELECT id, api_order_id, status FROM orders WHERE id = ? AND user_id = ?");
    $stmt->execute([$order_input, $user_id]);
    $order = $stmt->fetch();

    if (!$order) {
        echo json_encode(['status' => 'error', 'message' => 'Order not found in your history.']);
        exit;
    }

    // 2. If it's a local manual order (no API ID yet)
    if (empty($order['api_order_id'])) {
        echo json_encode(['status' => 'success', 'data' => ucfirst($order['status'])]);
        exit;
    }

    // 3. Check Live Status from API
    $post = [
        'key' => $API_KEY, // From config.php
        'action' => 'status',
        'order' => $order['api_order_id']
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $API_URL);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $api_res = json_decode($response, true);

    if (isset($api_res['status'])) {
        $live_status = ucfirst($api_res['status']);
        $remains = isset($api_res['remains']) ? $api_res['remains'] : '0';
        
        // Optional: Update DB to keep it fresh
        if (strtolower($live_status) != strtolower($order['status'])) {
            $upd = $pdo->prepare("UPDATE orders SET status = ?, remains = ? WHERE id = ?");
            $upd->execute([strtolower($live_status), $remains, $order_input]);
        }

        $msg = "<strong>Status:</strong> $live_status <br> <strong>Remains:</strong> $remains";
        echo json_encode(['status' => 'success', 'data' => $msg]);
    } else {
        // API Error or Rate Limit
        echo json_encode(['status' => 'error', 'message' => 'Could not fetch live status. Try again later.']);
    }

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'System Error']);
}
?>
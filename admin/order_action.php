<?php
// File: admin/order_action.php
require_once '../config/db.php';
require_once '../config/config.php';

if (!isset($_GET['id']) || !isset($_GET['action'])) die("Invalid");

$order_id = intval($_GET['id']);
$action = $_GET['action'];

// Fetch Order
$stmt = $pdo->prepare("SELECT o.*, s.api_service_id, s.min_qty FROM orders o JOIN services s ON o.service_id = s.id WHERE o.id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) die("Order not found");

// REJECT (Refund)
if ($action == 'reject') {
    $pdo->prepare("UPDATE users SET coins = coins + ? WHERE id = ?")->execute([$order['cost'], $order['user_id']]);
    $pdo->prepare("UPDATE orders SET status = 'canceled' WHERE id = ?")->execute([$order_id]);
    header("Location: orders.php?msg=rejected");
    exit;
}

// APPROVE (Send to API)
if ($action == 'approve') {
    $api_url = defined('SMM_API_URL') ? SMM_API_URL : $API_URL;
    $api_key = defined('SMM_API_KEY') ? SMM_API_KEY : $API_KEY;

    $post = [
        'key' => $api_key,
        'action' => 'add',
        'service' => $order['api_service_id'],
        'link' => $order['link'],
        'quantity' => $order['min_qty']
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $res = curl_exec($ch);
    curl_close($ch);
    $api_res = json_decode($res, true);

    if (isset($api_res['order'])) {
        $pdo->prepare("UPDATE orders SET status = 'processing', api_order_id = ? WHERE id = ?")->execute([$api_res['order'], $order_id]);
        header("Location: orders.php?msg=approved");
    } else {
        die("API Error: " . ($api_res['error'] ?? 'Unknown'));
    }
}
?>
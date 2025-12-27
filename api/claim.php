<?php
// File: api/claim.php
error_reporting(0);
ini_set('display_errors', 0);
header('Content-Type: application/json');

require_once '../config/db.php';
require_once '../config/config.php';

// 1. Auth Check
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Please login first']);
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $service_id = intval($_POST['service_id']);
    $link = trim($_POST['link']);

    // Basic Validation
    if (empty($link) || $service_id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid Link or Service']);
        exit;
    }

    try {
        // 2. Fetch Service
        $stmt = $pdo->prepare("SELECT * FROM services WHERE id = ? AND active = 1");
        $stmt->execute([$service_id]);
        $service = $stmt->fetch();

        if (!$service) {
            echo json_encode(['status' => 'error', 'message' => 'Service not found']);
            exit;
        }

        // 3. Check Balance
        $userStmt = $pdo->prepare("SELECT coins FROM users WHERE id = ?");
        $userStmt->execute([$user_id]);
        $user = $userStmt->fetch();

        if ($user['coins'] < $service['cost_coins']) {
            echo json_encode(['status' => 'error', 'message' => 'Insufficient Coins!']);
            exit;
        }

        // 4. HYBRID LOGIC: Auto-Approve Check
        $countStmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ?");
        $countStmt->execute([$user_id]);
        $total_orders = $countStmt->fetchColumn();

        // Fetch Limit (Default 2)
        $limitStmt = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'auto_order_limit'");
        $auto_limit_row = $limitStmt->fetch();
        $auto_limit = ($auto_limit_row) ? intval($auto_limit_row['setting_value']) : 2; 

        $is_auto = ($total_orders < $auto_limit);

        // 5. START TRANSACTION
        $pdo->beginTransaction();

        // Deduct Coins
        $deduct = $pdo->prepare("UPDATE users SET coins = coins - ? WHERE id = ?");
        $deduct->execute([$service['cost_coins'], $user_id]);

        // 6. PROCESS ORDER
        if ($is_auto) {
            // === AUTOMATIC MODE (Send to SMM) ===
            
            $api_url = defined('SMM_API_URL') ? SMM_API_URL : 'https://indiansmmservices.com/api/v2';
            $api_key = defined('SMM_API_KEY') ? SMM_API_KEY : '';

            // Standard SMM API Params
            $post = [
                'key' => $api_key,
                'action' => 'add',
                'service' => $service['api_service_id'], // Must be SMM ID (e.g., 4321), not DB ID
                'link' => $link,
                'quantity' => $service['min_qty'] // Uses the minimum quantity defined in DB
            ];

            // Enhanced cURL
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $api_url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post)); // Safer encoding
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow redirects if any
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)'); // Prevent blocking
            $response = curl_exec($ch);
            
            if (curl_errno($ch)) {
                throw new Exception("SMM Connection Failed: " . curl_error($ch));
            }
            curl_close($ch);
            
            $api_res = json_decode($response, true);

            // Handle API Response
            if (isset($api_res['order'])) {
                $smm_order_id = $api_res['order'];
                
                // Save Order
                $sql = "INSERT INTO orders (user_id, service_id, link, cost, status, api_order_id, created_at) 
                        VALUES (?, ?, ?, ?, 'processing', ?, NOW())";
                $insert = $pdo->prepare($sql);
                $insert->execute([$user_id, $service_id, $link, $service['cost_coins'], $smm_order_id]);
                
                $local_id = $pdo->lastInsertId();
                $pdo->commit();

                echo json_encode(['status' => 'success', 'message' => "Order #$local_id Sent! (Auto-Approved)"]);
            } else {
                // SMM Error (e.g., Bad Link, Wrong ID)
                $pdo->rollBack();
                $err = isset($api_res['error']) ? $api_res['error'] : 'Unknown Provider Error';
                echo json_encode(['status' => 'error', 'message' => 'API Error: ' . $err]);
            }

        } else {
            // === MANUAL MODE (Hold for Admin) ===
            $sql = "INSERT INTO orders (user_id, service_id, link, cost, status, api_order_id, created_at) 
                    VALUES (?, ?, ?, ?, 'pending', NULL, NOW())";
            $insert = $pdo->prepare($sql);
            $insert->execute([$user_id, $service_id, $link, $service['cost_coins']]);
            
            $local_id = $pdo->lastInsertId();
            $pdo->commit();

            echo json_encode(['status' => 'success', 'message' => "Order #$local_id Placed! Waiting for Admin Approval."]);
        }

    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
    }
}
?>
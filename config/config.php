<?php
// File: config/config.php

// 1. Start Session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- CONFIGURATION SETTINGS ---

// Base URL
define('BASE_URL', 'your_base_url_here'); // e.g., 'http://localhost/gift/'

// SMM API Settings (indiansmmservices.com)
define('SMM_API_URL', 'https://indiansmmservices.com/api/v2');
define('SMM_API_KEY', 'your_api_key_here');

// --- CRITICAL COMPATIBILITY FIX ---
$API_URL = SMM_API_URL;
$API_KEY = SMM_API_KEY; 
// ---------------------------------

// Referral Bonus (Coins)
define('REFERRAL_BONUS', 100);

// --- HELPER FUNCTIONS ---

// We wrap functions in !function_exists to prevent "Cannot redeclare" errors

if (!function_exists('sanitize')) {
    function sanitize($data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = sanitize($value);
            }
            return $data;
        }
        return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('redirect')) {
    function redirect($page) {
        if (filter_var($page, FILTER_VALIDATE_URL)) {
            header("Location: " . $page);
        } else {
            header("Location: " . BASE_URL . $page);
        }
        exit;
    }
}

if (!function_exists('isLoggedIn')) {
    function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
}

if (!function_exists('requireLogin')) {
    function requireLogin() {
        if (!isLoggedIn()) {
            redirect('login.php');
        }
    }
}

if (!function_exists('getUserData')) {
    function getUserData($pdo, $user_id) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return null;
        }
    }
}

if (!function_exists('formatDate')) {
    function formatDate($date) {
        return date('d M Y, h:i A', strtotime($date));
    }
}
?>

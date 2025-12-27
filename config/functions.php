<?php // File: functions.php ?><?php
// File: config/functions.php

/**
 * Sanitize User Input
 * Prevents XSS (Cross-Site Scripting) attacks by removing HTML tags and special chars.
 */
function sanitize($data) {
    if (is_array($data)) {
        foreach ($data as $key => $value) {
            $data[$key] = sanitize($value);
        }
        return $data;
    }
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Redirect Helper
 * Redirects to a specific page within the app and stops execution.
 */
function redirect($page) {
    // Ensure BASE_URL is defined (it should be in config.php)
    if (!defined('BASE_URL')) {
        header("Location: " . $page);
    } else {
        header("Location: " . BASE_URL . $page);
    }
    exit;
}

/**
 * Check Login Status
 * Returns true if user is logged in, false otherwise.
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Require Login
 * Place this at the top of any protected page.
 * If user is not logged in, they are redirected to login.php.
 */
function requireLogin() {
    if (!isLoggedIn()) {
        redirect('login.php');
    }
}

/**
 * Get User Data
 * Fetches the latest user data (coins, spins, etc.) from the database.
 */
function getUserData($pdo, $user_id) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        return null;
    }
}

/**
 * Format Date
 * Makes database timestamps look nice (e.g., "25 Dec 2024, 10:30 AM").
 */
function formatDate($date) {
    return date('d M Y, h:i A', strtotime($date));
}

/**
 * JSON Response Helper
 * Sends a structured JSON response and exits.
 */
function jsonResponse($status, $message, $data = []) {
    header('Content-Type: application/json');
    echo json_encode(array_merge([
        'status' => $status,
        'message' => $message
    ], $data));
    exit;
}
?>
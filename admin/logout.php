<?php
// File: logout.php
// Errors dikhane ke liye (Debugging)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Session start karein (agar pehle se nahi hai)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Config file include karein (BASE_URL ke liye)
// Note: Path check karein. Agar ye file root folder mein hai toh 'config/config.php'
// Agar ye admin folder mein hai toh '../config/config.php'
if (file_exists('config/config.php')) {
    require_once 'config/config.php';
} elseif (file_exists('../config/config.php')) {
    require_once '../config/config.php';
}

// Session data clear karein
$_SESSION = [];
session_unset();
session_destroy();

// Redirect Logic
$login_url = defined('BASE_URL') ? BASE_URL . 'login.php' : 'login.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Logging out...</title>
</head>
<body>
    <p>Logging you out...</p>
    <script>
        // Force redirect via JavaScript
        window.location.href = "<?php echo $login_url; ?>";
    </script>
</body>
</html>
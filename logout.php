<?php
// File: logout.php

// 1. Output Buffering (Prevents "Headers already sent" errors)
ob_start();

// 2. Start session safely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 3. Clear all session variables
$_SESSION = array();

// 4. Destroy the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// 5. Destroy the session
session_destroy();

// 6. Determine Redirect URL
$redirect_url = 'login.php';

// Check if config exists to use the absolute BASE_URL
if (file_exists('config/config.php')) {
    require_once 'config/config.php';
    if (defined('BASE_URL')) {
        $redirect_url = BASE_URL . 'login.php';
    }
}

// 7. Redirect (Method A: PHP Header)
// This is the fastest and correct way to redirect
header("Location: " . $redirect_url);

// 8. Redirect (Method B: JavaScript Fallback)
// If PHP header fails, this HTML/JS ensures the user still gets logged out.
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Logging Out...</title>
    <script type="text/javascript">
        window.location.href = "<?php echo $redirect_url; ?>";
    </script>
</head>
<body>
    <p>Logging out... <a href="<?php echo $redirect_url; ?>">Click here if not redirected.</a></p>
</body>
</html>
<?php
ob_end_flush();
exit;
?>
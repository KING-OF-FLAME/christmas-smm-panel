<?php // File: auth.php ?><?php
// File: includes/auth.php
// Simply ensures the session is started and checks login
// config.php already starts session, so we just check the variable.

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<?php // File: settings.php ?><?php
// File: admin/settings.php
session_start();
require_once '../config/db.php';
require_once '../config/config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

// Handle Save
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    foreach ($_POST as $key => $value) {
        // Update each setting
        $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
        $stmt->execute([$key, $value, $value]);
    }
    $msg = "Settings saved!";
}

// Fetch current settings
$settings_raw = $pdo->query("SELECT * FROM settings")->fetchAll();
$settings = [];
foreach ($settings_raw as $s) {
    $settings[$s['setting_key']] = $s['setting_value'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Site Settings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Site Settings</h2>
        <a href="index.php" class="btn btn-secondary mb-3">&larr; Back to Dashboard</a>

        <?php if(isset($msg)): ?>
            <div class="alert alert-success"><?php echo $msg; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="card p-4">
                
                <div class="mb-3">
                    <label class="form-label">Site Name</label>
                    <input type="text" name="site_name" class="form-control" value="<?php echo $settings['site_name'] ?? ''; ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Banner Image URL</label>
                    <input type="text" name="banner_image" class="form-control" value="<?php echo $settings['banner_image'] ?? ''; ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Monetag / Ad Code (HTML/JS)</label>
                    <textarea name="ad_code_monetag" class="form-control" rows="5"><?php echo $settings['ad_code_monetag'] ?? ''; ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Referral Bonus (Coins)</label>
                    <input type="number" name="referral_bonus" class="form-control" value="<?php echo $settings['referral_bonus'] ?? '300'; ?>">
                </div>

<div class="mb-3">
    <label class="form-label">Auto-Approve Order Limit (0 for manual only)</label>
    <input type="number" name="auto_order_limit" class="form-control" value="<?php echo $settings['auto_order_limit'] ?? '2'; ?>">
    <small class="text-muted">First X orders will be auto-sent. After that, manual approval is required.</small>
</div>
                <button type="submit" class="btn btn-success">Save Settings</button>
            </div>
        </form>
    </div>
</body>
</html>
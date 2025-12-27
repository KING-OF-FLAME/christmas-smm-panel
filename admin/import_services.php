<?php
// File: admin/import_services.php
session_start();
require_once '../config/db.php';
require_once '../config/config.php';

// Force Admin Login
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

$message = "";
$fetched_services = [];
$local_services_map = []; 

// 1. Fetch Local Services from DB (to see what we already have)
$local = $pdo->query("SELECT * FROM services")->fetchAll();
foreach($local as $l) {
    // Map by api_service_id for quick lookup
    $local_services_map[$l['api_service_id']] = $l;
}

// 2. Fetch Live Services from API
if (isset($_POST['fetch_btn']) || isset($_POST['save_selected'])) {
    $api_url = defined('SMM_API_URL') ? SMM_API_URL : $API_URL;
    $api_key = defined('SMM_API_KEY') ? SMM_API_KEY : $API_KEY;

    $post = ['key' => $api_key, 'action' => 'services'];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    
    if (curl_errno($ch)) {
        $message = "<div class='alert alert-danger'>API Connection Error.</div>";
    } else {
        $data = json_decode($response, true);
        if (is_array($data)) {
            $fetched_services = $data;
            if(!isset($_POST['save_selected'])) {
                $message = "<div class='alert alert-success'>Loaded " . count($data) . " services from API.</div>";
            }
        } else {
            $message = "<div class='alert alert-danger'>Invalid API Response. Check Key/URL.</div>";
        }
    }
    curl_close($ch);
}

// 3. Save / Update Selected Services
if (isset($_POST['save_selected'])) {
    $selected_ids = $_POST['service_ids'] ?? [];
    $names = $_POST['names'] ?? [];
    $categories = $_POST['categories'] ?? [];
    $custom_prices = $_POST['custom_coins'] ?? [];
    $min_qtys = $_POST['min_qtys'] ?? [];
    $max_qtys = $_POST['max_qtys'] ?? [];
    
    $imported_count = 0;

    foreach ($selected_ids as $api_id) {
        $name_raw = $names[$api_id] ?? 'Unknown';
        $category = $categories[$api_id] ?? 'General';
        $coin_cost = intval($custom_prices[$api_id]);
        $min_q = intval($min_qtys[$api_id]);
        $max_q = intval($max_qtys[$api_id]);
        
        // Prepare Name (Optional: Add ID to name for clarity)
        $name = $name_raw; 

        // Check if exists
        $check = $pdo->prepare("SELECT id FROM services WHERE api_service_id = ?");
        $check->execute([$api_id]);
        
        if ($check->rowCount() > 0) {
            // UPDATE EXISTING
            $sql = "UPDATE services SET name=?, category=?, cost_coins=?, min_qty=?, max_qty=?, active=1 WHERE api_service_id=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$name, $category, $coin_cost, $min_q, $max_q, $api_id]);
        } else {
            // INSERT NEW
            $sql = "INSERT INTO services (name, api_service_id, category, cost_coins, min_qty, max_qty, active) VALUES (?, ?, ?, ?, ?, ?, 1)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$name, $api_id, $category, $coin_cost, $min_q, $max_q]);
        }
        $imported_count++;
    }
    
    // Refresh local map after import
    $local = $pdo->query("SELECT * FROM services")->fetchAll();
    foreach($local as $l) { $local_services_map[$l['api_service_id']] = $l; }

    if ($imported_count > 0) $message = "<div class='alert alert-success'>Success! Updated/Imported $imported_count services.</div>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Services</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f6f9; padding: 20px; }
        .container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .sticky-header th { position: sticky; top: 0; background: #343a40; color: white; z-index: 100; }
        .imported-row { background-color: #d1e7dd !important; } 
    </style>
    <script>
        function toggleAll(source) {
            checkboxes = document.getElementsByName('service_ids[]');
            for(var i=0, n=checkboxes.length;i<n;i++) checkboxes[i].checked = source.checked;
        }
    </script>
</head>
<body>
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Import & Fix Services</h2>
        <div>
            <a href="index.php" class="btn btn-secondary">Dashboard</a>
        </div>
    </div>
    
    <?php echo $message; ?>

    <?php if (empty($fetched_services)): ?>
    <div class="text-center py-5">
        <p class="text-muted">Click below to load the latest services from IndianSMMServices.</p>
        <form method="POST">
            <button type="submit" name="fetch_btn" class="btn btn-primary btn-lg">Fetch Services from API</button>
        </form>
    </div>
    <?php endif; ?>

    <?php if (!empty($fetched_services)): ?>
    <form method="POST">
        <div class="d-flex justify-content-between align-items-center mb-2 sticky-top bg-white py-2 border-bottom">
            <strong><?php echo count($fetched_services); ?> Services Found</strong>
            <button type="submit" name="save_selected" class="btn btn-success">Save / Update Selected</button>
        </div>
        
        <div style="height: 600px; overflow-y: auto;">
            <table class="table table-bordered table-sm sticky-header">
                <thead>
                    <tr>
                        <th width="40"><input type="checkbox" onClick="toggleAll(this)"></th>
                        <th width="60">ID</th>
                        <th>Category / Name</th>
                        <th>Rate (₹)</th>
                        <th>Cost (Coins)</th>
                        <th>Min/Max</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($fetched_services as $s): ?>
                    <?php 
                        $api_id = $s['service'];
                        $is_in_db = isset($local_services_map[$api_id]);
                        $row_class = $is_in_db ? 'imported-row' : '';
                        
                        // Default coin calculation: Rate * 100 (e.g. 10 Rs = 1000 Coins)
                        // If in DB, use existing coin price, else calculate
                        $default_coins = $is_in_db ? $local_services_map[$api_id]['cost_coins'] : ceil($s['rate'] * 100);
                    ?>
                    <tr class="<?php echo $row_class; ?>">
                        <td class="text-center">
                            <input type="checkbox" name="service_ids[]" value="<?php echo $api_id; ?>" <?php if($is_in_db) echo 'checked'; ?>>
                            <input type="hidden" name="names[<?php echo $api_id; ?>]" value="<?php echo htmlspecialchars($s['name']); ?>">
                            <input type="hidden" name="categories[<?php echo $api_id; ?>]" value="<?php echo htmlspecialchars($s['category']); ?>">
                        </td>
                        <td><strong><?php echo $api_id; ?></strong></td>
                        <td>
                            <small class="text-muted fw-bold"><?php echo htmlspecialchars($s['category']); ?></small><br>
                            <?php echo htmlspecialchars($s['name']); ?>
                        </td>
                        <td>₹<?php echo $s['rate']; ?></td>
                        <td>
                            <input type="number" name="custom_coins[<?php echo $api_id; ?>]" value="<?php echo $default_coins; ?>" class="form-control form-control-sm" style="width: 80px;">
                        </td>
                        <td>
                            <div class="input-group input-group-sm" style="width: 130px;">
                                <input type="number" name="min_qtys[<?php echo $api_id; ?>]" value="<?php echo $s['min']; ?>" class="form-control" placeholder="Min">
                                <input type="number" name="max_qtys[<?php echo $api_id; ?>]" value="<?php echo $s['max']; ?>" class="form-control" placeholder="Max">
                            </div>
                        </td>
                        <td>
                            <?php if($is_in_db): ?>
                                <span class="badge bg-success">Active</span>
                            <?php else: ?>
                                <span class="badge bg-light text-dark">New</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </form>
    <?php endif; ?>
</div>
</body>
</html>
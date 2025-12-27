<?php
// File: admin/coupons.php
require_once '../config/db.php';
require_once '../config/config.php';

// Handle Create Coupon
if (isset($_POST['create_coupon'])) {
    $code = strtoupper(trim($_POST['code']));
    $coins = intval($_POST['coins']);
    $uses = intval($_POST['uses']);

    if (!empty($code) && $coins > 0) {
        // Check duplicate
        $check = $pdo->prepare("SELECT id FROM coupons WHERE code = ?");
        $check->execute([$code]);
        if ($check->rowCount() == 0) {
            $sql = "INSERT INTO coupons (code, reward_coins, max_uses, used_count) VALUES (?, ?, ?, 0)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$code, $coins, $uses]);
            $msg = "<div style='background:#d4edda; color:#155724; padding:10px; margin-bottom:15px; border-radius:4px;'>Coupon '$code' created successfully!</div>";
        } else {
            $msg = "<div style='background:#f8d7da; color:#721c24; padding:10px; margin-bottom:15px; border-radius:4px;'>Error: Code already exists!</div>";
        }
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $pdo->prepare("DELETE FROM coupons WHERE id = ?")->execute([$id]);
    // Also delete usage history so users can potentially reuse if code is recreated (Optional)
    $pdo->prepare("DELETE FROM coupon_usage WHERE coupon_id = ?")->execute([$id]);
    
    header("Location: coupons.php");
    exit;
}

// Fetch All Coupons
$coupons = $pdo->query("SELECT * FROM coupons ORDER BY id DESC")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Coupon Manager</title>
    <style>
        body { font-family: sans-serif; padding: 20px; background: #f4f4f4; }
        .container { max-width: 900px; margin: 0 auto; }
        .box { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin-bottom: 20px; }
        input, button { padding: 10px; margin: 5px 0; width: 100%; box-sizing: border-box; border-radius: 4px; border: 1px solid #ddd; }
        button { background: #28a745; color: white; border: none; cursor: pointer; font-weight: bold; font-size: 16px; }
        button:hover { background: #218838; }
        
        table { width: 100%; border-collapse: collapse; background: white; margin-top: 10px; }
        th, td { padding: 12px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background: #333; color: white; }
        
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .btn-back { background: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 4px; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2>Coupon Manager</h2>
        <div>
            <a href="users.php" class="btn-back">Users</a>
            <a href="../index.php" class="btn-back" style="background:#6c757d;">Home</a>
        </div>
    </div>
    
    <div class="box">
        <h3><i class="fa-solid fa-plus"></i> Create New Promo Code</h3>
        <?php if(isset($msg)) echo $msg; ?>
        <form method="POST">
            <label>Coupon Code</label>
            <input type="text" name="code" placeholder="Ex: SANTA500" required>
            
            <div style="display:flex; gap:15px;">
                <div style="flex:1;">
                    <label>Reward (Coins)</label>
                    <input type="number" name="coins" placeholder="Ex: 500" required>
                </div>
                <div style="flex:1;">
                    <label>Max Uses (Limit)</label>
                    <input type="number" name="uses" placeholder="Ex: 100" value="100" required>
                </div>
            </div>
            
            <button type="submit" name="create_coupon">Create Coupon</button>
        </form>
    </div>

    <div class="box">
        <h3>Active Coupons</h3>
        <table>
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Reward</th>
                    <th>Usage</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($coupons as $c): ?>
                <?php 
                    $is_expired = ($c['used_count'] >= $c['max_uses']);
                    $row_style = $is_expired ? 'background:#f9f9f9; color:#999;' : '';
                ?>
                <tr style="<?php echo $row_style; ?>">
                    <td><strong style="font-size:1.1em; color:#d35400;"><?php echo htmlspecialchars($c['code']); ?></strong></td>
                    <td><?php echo number_format($c['reward_coins']); ?> Coins</td>
                    <td>
                        <?php echo $c['used_count']; ?> / <?php echo $c['max_uses']; ?>
                    </td>
                    <td>
                        <?php if($is_expired): ?>
                            <span style="color:red; font-weight:bold;">Expired</span>
                        <?php else: ?>
                            <span style="color:green; font-weight:bold;">Active</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="?delete=<?php echo $c['id']; ?>" style="color:red; text-decoration:none; font-weight:bold;" onclick="return confirm('Delete this coupon?');">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                
                <?php if(count($coupons) == 0): ?>
                    <tr><td colspan="5" style="text-align:center; padding:20px;">No coupons found. Create one above!</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
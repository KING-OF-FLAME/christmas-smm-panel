<?php
// File: dashboard.php

// 1. Enable Error Reporting (Debugging ke liye)
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/db.php';
require_once 'config/config.php';

// Check Login Logic
if (file_exists('includes/auth.php')) {
    require_once 'includes/auth.php';
} else {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit; }
}

$user_id = $_SESSION['user_id'];

// --- GET USER DATA ---
// Hum config.php wala function use karenge. Agar wo fail hua to manual query.
if (function_exists('getUserData')) {
    $user = getUserData($pdo, $user_id);
} else {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
}

// 1. Fetch Services (Grouped by Category)
$services = $pdo->query("SELECT * FROM services WHERE active = 1 ORDER BY category ASC, name ASC")->fetchAll();

// 2. Fetch Order History
$orders = $pdo->prepare("SELECT o.*, s.name as service_name FROM orders o JOIN services s ON o.service_id = s.id WHERE o.user_id = ? ORDER BY o.created_at DESC LIMIT 10");
$orders->execute([$user_id]);
$order_history = $orders->fetchAll();

// 3. Referral Logic
$ref_code = isset($user['referral_code']) ? $user['referral_code'] : 'USER'.$user_id;
$ref_bonus = defined('REFERRAL_BONUS') ? REFERRAL_BONUS : 10;
?>

<?php include 'includes/header.php'; ?>

<div class="container" style="max-width: 900px; margin-top: 20px;">
    
    <div style="display: flex; gap: 15px; flex-wrap: wrap; margin-bottom: 25px;">
        <div class="auth-box" style="flex: 1; min-width: 250px; text-align: center; border: 1px solid var(--gold); margin: 0;">
            <h3 style="color: var(--gold); margin: 0;">Your Balance</h3>
            <h1 style="font-size: 3rem; margin: 10px 0;"><i class="fa-solid fa-coins"></i> <?php echo number_format($user['coins']); ?></h1>
            <p style="color: #ccc;">Coins Available</p>
        </div>
        
        <div class="auth-box" style="flex: 1; min-width: 250px; margin: 0; display: flex; flex-direction: column; justify-content: center;">
            <h3 style="color: var(--gold); margin-bottom: 10px;"><i class="fa-solid fa-magnifying-glass"></i> Track Order</h3>
            <form id="trackForm" style="display:flex; gap:5px;">
                <input type="number" name="order_id" placeholder="Enter Order ID" required style="width: 100%; padding: 10px; border-radius: 4px; border:none;">
                <button type="submit" class="btn-primary" style="padding: 10px 20px; width:auto;">Check</button>
            </form>
            <div id="track-result" style="margin-top:10px; font-weight:bold; font-size: 0.9rem;"></div>
        </div>
    </div>

    <div class="auth-box" style="margin-bottom: 30px; text-align: center; background: linear-gradient(135deg, rgba(0,0,0,0.4) 0%, rgba(255, 215, 0, 0.1) 100%); border: 2px dashed var(--gold);">
        <h3 style="color: var(--gold); margin-bottom: 5px;"><i class="fa-solid fa-users"></i> Refer & Earn</h3>
        <p style="margin-bottom: 15px; color: #eee;">
            Invite friends! You get <strong><?php echo $ref_bonus; ?> Coins</strong> for every friend.
        </p>
        <div style="background: rgba(255,255,255,0.1); padding: 10px 20px; border-radius: 50px; display: inline-flex; align-items: center; gap: 15px;">
            <code style="font-size: 1.2rem; color: #fff; letter-spacing: 1px;"><?php echo $ref_code; ?></code>
            <button onclick="copyRef()" class="btn-primary" style="margin: 0; padding: 5px 15px; font-size: 0.8rem; border-radius: 20px;">Copy</button>
        </div>
    </div>

    <div class="auth-box" style="margin-bottom: 30px;">
        <h2 style="color: var(--gold); border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px; margin-bottom: 20px;">
            <i class="fa-brands fa-instagram"></i> Claim Gift
        </h2>
        
        <form id="claimForm">
            <div class="form-group" style="margin-bottom: 15px;">
                <label style="display:block; margin-bottom: 5px;">Select Gift Service</label>
                <select name="service_id" id="service_select" style="width: 100%; padding: 12px; border-radius: 5px; background: #222; color: #fff; border: 1px solid #444;" onchange="updateCost()" required>
                    <option value="" data-cost="0">-- Choose Service --</option>
                    <?php
                    $current_cat = "";
                    foreach ($services as $s) {
                        if ($s['category'] != $current_cat) {
                            if ($current_cat != "") echo "</optgroup>";
                            echo "<optgroup label='" . htmlspecialchars($s['category']) . "'>";
                            $current_cat = $s['category'];
                        }
                        echo "<option value='{$s['id']}' data-cost='{$s['cost_coins']}'>{$s['name']} ({$s['cost_coins']} Coins)</option>";
                    }
                    if ($current_cat != "") echo "</optgroup>";
                    ?>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 15px;">
                <label style="display:block; margin-bottom: 5px;">Instagram Link</label>
                <input type="text" name="link" required placeholder="https://instagram.com/p/..." style="width: 100%; padding: 12px; border-radius: 5px; background: #222; color: #fff; border: 1px solid #444;">
                <small style="color: #aaa; display:block; margin-top:5px;">Use <strong>Post Link</strong> for Likes. Use <strong>Username</strong> for Followers.</small>
            </div>

            <div style="background-color: rgba(255, 152, 0, 0.1); border-left: 4px solid #ff9800; padding: 10px; margin-top: 15px; border-radius: 4px;">
                <p style="color: #ffca28; font-size: 0.9rem; margin: 0;">
                    <i class="fa-solid fa-triangle-exclamation"></i> <strong>Note:</strong> Your account must be <strong>PUBLIC</strong>.
                </p>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 25px;">
                <div style="font-size: 1.1rem;">
                    Cost: <strong id="cost_display" style="color: var(--gold);">0</strong> Coins
                </div>
                <button type="submit" class="btn-primary" style="width: auto; padding: 12px 30px;">🚀 Claim Now</button>
            </div>
            <p id="claim-msg" style="margin-top: 15px; font-weight: bold; text-align: center;"></p>
        </form>
    </div>

    <div class="auth-box">
        <h3 style="color: var(--gold); margin-bottom: 15px;">Recent Orders</h3>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; color: #fff; font-size: 0.9rem;">
                <thead>
                    <tr style="background: #333; color: var(--gold);">
                        <th style="padding: 10px; text-align: left;">ID</th>
                        <th style="padding: 10px; text-align: left;">Service</th>
                        <th style="padding: 10px; text-align: left;">SMM ID</th>
                        <th style="padding: 10px; text-align: left;">Status</th>
                        <th style="padding: 10px; text-align: left;">Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($order_history) > 0): ?>
                        <?php foreach($order_history as $order): ?>
                        <tr style="border-bottom: 1px solid #333;">
                            <td style="padding: 10px; color: #aaa;">#<?php echo $order['id']; ?></td>
                            <td style="padding: 10px; max-width: 200px;">
                                <?php echo htmlspecialchars($order['service_name']); ?>
                            </td>
                            <td style="padding: 10px; font-family: monospace; color: #00bcd4;">
                                <?php echo $order['api_order_id'] ? $order['api_order_id'] : '-'; ?>
                            </td>
                            <td style="padding: 10px;">
                                <?php 
                                    // FORCE PENDING IF EMPTY
                                    $s = !empty($order['status']) ? strtolower($order['status']) : 'pending';
                                    
                                    // COLORS
                                    $color = '#ff9800'; // Default Orange (Pending)
                                    if(strpos($s, 'complete')!==false) $color = '#4caf50'; // Green
                                    if(strpos($s, 'process')!==false) $color = '#2196f3'; // Blue
                                    if(strpos($s, 'cancel')!==false) $color = '#f44336'; // Red
                                ?>
                                <span style="color: <?php echo $color; ?>; font-weight: bold;">
                                    <?php echo ucfirst($s); ?>
                                </span>
                            </td>
                            <td style="padding: 10px; color: #ccc;">
                                <?php 
                                    // Use standard PHP date function to avoid conflicts
                                    echo date('d M Y, h:i A', strtotime($order['created_at'])); 
                                ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" style="padding: 20px; text-align: center; color: #777;">No claims yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function updateCost() {
        const select = document.getElementById('service_select');
        if(select.selectedIndex === -1) return;
        const option = select.options[select.selectedIndex];
        const cost = option.getAttribute('data-cost') || 0;
        document.getElementById('cost_display').innerText = cost;
    }
    updateCost();

    function copyRef() {
        navigator.clipboard.writeText("<?php echo $ref_code; ?>");
        alert("Referral Code Copied!");
    }

    document.getElementById('claimForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = this.querySelector('button');
        const msg = document.getElementById('claim-msg');
        btn.disabled = true;
        btn.innerText = "Processing...";
        msg.innerText = "";

        const formData = new FormData(this);
        fetch('api/claim.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerText = "🚀 Claim Now";
            if(data.status === 'success') {
                msg.style.color = '#4caf50';
                msg.innerText = "✅ " + data.message;
                setTimeout(() => window.location.reload(), 2000);
            } else {
                msg.style.color = '#ff6b6b';
                msg.innerText = "❌ " + data.message;
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerText = "Retry";
            msg.innerText = "Network Error";
        });
    });

    document.getElementById('trackForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const resDiv = document.getElementById('track-result');
        resDiv.innerText = "Check order status in the table below."; 
    });
</script>

<?php include 'includes/footer.php'; ?>
<?php
// File: index.php
require_once 'config/db.php';
require_once 'config/config.php';
require_once 'includes/auth.php'; // Force Login

date_default_timezone_set('Asia/Kolkata');

// --- 1. DAILY CHECK-IN LOGIC ---
$user_id = $_SESSION['user_id'];
$daily_reward = 10; // 10 Coins Bonus
$today = date('Y-m-d');

// Check if user already claimed today
$check_stmt = $pdo->prepare("SELECT id FROM checkins WHERE user_id = ? AND checkin_date = ?");
$check_stmt->execute([$user_id, $today]);
$has_checked_in = $check_stmt->rowCount() > 0;

if (isset($_POST['daily_checkin']) && !$has_checked_in) {
    try {
        $pdo->beginTransaction();
        $pdo->prepare("INSERT INTO checkins (user_id, checkin_date, coins_earned) VALUES (?, ?, ?)")->execute([$user_id, $today, $daily_reward]);
        $pdo->prepare("UPDATE users SET coins = coins + ? WHERE id = ?")->execute([$daily_reward, $user_id]);
        $pdo->commit();
        header("Location: index.php?checkin=success");
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
    }
}

// Fetch user data
// Using the safe function from config.php if available, else manual
if (function_exists('getUserData')) {
    $user = getUserData($pdo, $user_id);
} else {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
}

// --- 2. WHEEL TIMER LOGIC ---
$show_timer = false;
$seconds_left = 0;

// Logic: If spins are 0, check time since last spin
if ($user['spins_left'] <= 0) {
    $current_time = time(); 
    $last_spin = $user['last_spin_time'] ? strtotime($user['last_spin_time']) : 0;
    $refill_seconds = 7200; // 2 Hours
    $diff = $current_time - $last_spin;
    
    if ($diff < $refill_seconds) {
        $show_timer = true;
        $seconds_left = $refill_seconds - $diff;
    }
}
?>

<?php include 'includes/header.php'; ?>

<style>
    /* Wheel Styling */
    .wheel-container {
        position: relative;
        width: 320px;
        height: 320px;
        margin: 0 auto;
        border-radius: 50%;
        border: 5px solid #ffca28;
        box-shadow: 0 0 20px rgba(0,0,0,0.5);
        overflow: hidden;
    }
    #wheel-canvas {
        width: 100%;
        height: 100%;
        transition: transform 4s cubic-bezier(0.17, 0.67, 0.12, 0.99);
    }
    .wheel-pointer {
        position: absolute;
        top: -15px; left: 50%; transform: translateX(-50%);
        width: 0; height: 0; 
        border-left: 20px solid transparent; border-right: 20px solid transparent; border-top: 40px solid #d32f2f;
        z-index: 10; filter: drop-shadow(0px 2px 2px rgba(0,0,0,0.5));
    }
    .wheel-center {
        position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
        width: 60px; height: 60px; background: #fff; border-radius: 50%; z-index: 5;
        box-shadow: 0 0 10px rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center;
        font-size: 28px; color: #d32f2f; border: 4px solid #ffca28;
    }
</style>

<div style="text-align: center; margin-top: 20px;">
    <h1>🎄 Spin the Santa Wheel 🎄</h1>
    
    <div style="margin: 20px 0;">
        <?php if (!$has_checked_in): ?>
            <form method="POST">
                <button type="submit" name="daily_checkin" class="btn-primary" style="background: linear-gradient(45deg, #ff9800, #f57c00); box-shadow: 0 4px 15px rgba(245, 124, 0, 0.4); border:none; padding: 12px 25px;">
                    📅 Claim Daily <?php echo $daily_reward; ?> Coins
                </button>
            </form>
        <?php else: ?>
            <button disabled style="background: #333; color: #4caf50; border: 1px solid #4caf50; padding: 10px 20px; border-radius: 5px; cursor: default;">
                ✅ Daily Bonus Claimed
            </button>
        <?php endif; ?>
        
        <?php if(isset($_GET['checkin']) && $_GET['checkin']=='success'): ?>
            <p style="color: #4caf50; margin-top: 5px;">Bonus Added Successfully!</p>
        <?php endif; ?>
    </div>
</div>

<div style="text-align: center; margin-bottom: 25px; display: flex; justify-content: center; gap: 15px; flex-wrap: wrap;">
    <span style="background-color: #d32f2f; color: #ffca28; padding: 10px 25px; border-radius: 50px; font-weight: bold; font-size: 1.2rem; border: 2px solid #ffca28;">
        <i class="fa-solid fa-arrows-rotate"></i> Spins: <span id="spin-count"><?php echo $user['spins_left']; ?></span>
    </span>

    <span id="refill-badge" data-seconds="<?php echo $seconds_left; ?>" style="display: <?php echo $show_timer ? 'flex' : 'none'; ?>; background-color: #1a1a1a; color: #4caf50; padding: 10px 25px; border-radius: 50px; font-weight: bold; font-size: 1.2rem; border: 2px solid #4caf50; align-items: center; gap: 8px;">
        <i class="fa-regular fa-clock"></i> Refill: <span id="timer-display">Loading...</span>
    </span>
</div>

<div class="wheel-container">
    <canvas id="wheel-canvas" width="500" height="500"></canvas>
    <div class="wheel-center"><i class="fa-solid fa-gift"></i></div>
    <div class="wheel-pointer"></div>
</div>

<div style="text-align: center; margin-bottom: 40px; margin-top: 25px;">
    <?php if($show_timer): ?>
        <button id="spin-btn" disabled class="btn-primary" style="padding: 15px 40px; font-size: 1.2rem; background: #555; cursor: not-allowed;">Wait for Refill</button>
    <?php else: ?>
        <button id="spin-btn" class="btn-primary" style="padding: 15px 40px; font-size: 1.2rem; cursor: pointer;">SPIN & WIN</button>
    <?php endif; ?>
</div>

<div class="auth-box" style="margin: 20px auto; max-width: 400px; text-align: center; border: 1px dashed var(--gold);">
    <h3 style="color: var(--gold); margin-bottom: 10px;"><i class="fa-solid fa-ticket"></i> Redeem Promo Code</h3>
    <form id="redeemForm" style="display: flex; gap: 5px;">
        <input type="text" name="code" placeholder="Enter Code (e.g. SANTA)" required style="flex: 1; padding: 10px; border-radius: 4px; border: none;">
        <button type="submit" class="btn-primary" style="width: auto; padding: 10px 15px;">Apply</button>
    </form>
    <div id="redeem-msg" style="margin-top: 10px; font-weight: bold;"></div>
</div>

<script src="https://quge5.com/88/tag.min.js" data-zone="195403" async data-cfasync="false"></script>

<div class="auth-box" style="margin-top: 20px; text-align: left; border: 1px solid var(--gold);">
    <h3 style="color: var(--gold); border-bottom: 1px solid rgba(255,255,255,0.2); padding-bottom: 10px; margin-bottom: 15px;">
        <i class="fa-solid fa-circle-question"></i> How to Play?
    </h3>
    <ul style="list-style: none; padding: 0; font-size: 0.95rem; color: #fff;">
        <li style="margin-bottom: 15px;"><i class="fa-solid fa-check" style="color: var(--gold);"></i> <strong>Daily Spins:</strong> You get <strong>5 Spins</strong>.</li>
        <li style="margin-bottom: 15px;"><i class="fa-solid fa-clock" style="color: var(--gold);"></i> <strong>Auto Refill:</strong> Spins refill every <strong>2 Hours</strong> after they reach 0.</li>
        <li style="margin-bottom: 10px;"><i class="fa-solid fa-coins" style="color: var(--gold);"></i> <strong>Win Coins:</strong> Use coins to get Real Likes!</li>
    </ul>
</div>

<?php include 'includes/footer.php'; ?>
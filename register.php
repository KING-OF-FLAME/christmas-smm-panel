<?php
// File: register.php
require_once 'config/db.php';
require_once 'config/config.php';

// --- UPDATED BONUS SETTINGS ---
$REFERRER_BONUS = 100; // Referrer gets 100 Coins
$NEW_USER_BONUS = 10;  // New User gets 10 Coins
// ------------------------------

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $whatsapp = trim($_POST['whatsapp']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $referral_code_input = isset($_POST['referral_code']) ? trim($_POST['referral_code']) : '';
    
    // 1. GET USER IP ADDRESS
    $user_ip = $_SERVER['REMOTE_ADDR'];

    // 2. SECURITY CHECK: Check if IP already registered
    // Allow max 1 account per IP address
    $ipCheck = $pdo->prepare("SELECT id FROM users WHERE ip_address = ?");
    $ipCheck->execute([$user_ip]);
    
    if ($ipCheck->rowCount() > 0) {
        $error = "Security Alert: You have already created an account from this device/IP.";
    } 
    elseif (empty($name) || empty($email) || empty($password)) {
        $error = "Please fill all required fields.";
    } else {
        // 3. Check Email
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            $error = "Email already registered!";
        } else {
            // Generate Code
            $my_referral_code = strtoupper(substr(str_replace(' ', '', $name), 0, 4) . rand(1000, 9999));
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $initial_coins = 0; 
            $initial_spins = 5; 
            $referrer_id = NULL;

            // 4. Validate Referral Code
            if (!empty($referral_code_input)) {
                $refStmt = $pdo->prepare("SELECT id FROM users WHERE referral_code = ?");
                $refStmt->execute([$referral_code_input]);
                $referrer = $refStmt->fetch();

                if ($referrer) {
                    $referrer_id = $referrer['id'];
                    // Prevent self-referral (if IP matches referrer IP - optional strictness)
                    $initial_coins = $NEW_USER_BONUS; 
                }
            }

            try {
                $pdo->beginTransaction();

                // 5. Insert New User with IP ADDRESS
                $sql = "INSERT INTO users (name, whatsapp, email, password, coins, spins_left, referral_code, referred_by, ip_address) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $insert = $pdo->prepare($sql);
                $insert->execute([$name, $whatsapp, $email, $hashed_password, $initial_coins, $initial_spins, $my_referral_code, $referrer_id, $user_ip]);
                
                $new_user_id = $pdo->lastInsertId();

                // 6. Update Referrer
                if ($referrer_id) {
                    $updateRef = $pdo->prepare("UPDATE users SET coins = coins + ? WHERE id = ?");
                    $updateRef->execute([$REFERRER_BONUS, $referrer_id]);
                }

                $pdo->commit();

                // Auto Login
                session_start();
                $_SESSION['user_id'] = $new_user_id;
                $_SESSION['user_name'] = $name;
                
                header("Location: index.php");
                exit;

            } catch (PDOException $e) {
                $pdo->rollBack();
                $error = "System Error: " . $e->getMessage();
            }
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="auth-box">
    <h2>🎄 Join the Party</h2>
    <p>Sign up & Get Rewards!</p>
    
    <?php if($error): ?>
        <p style="color: #ff6b6b; background: rgba(0,0,0,0.5); padding: 10px; border-radius: 5px; border: 1px solid #ff6b6b;">
            <i class="fa-solid fa-shield-halved"></i> <?php echo $error; ?>
        </p>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="name" required placeholder="Your Name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
        </div>
        <div class="form-group">
            <label>WhatsApp Number</label>
            <input type="text" name="whatsapp" required placeholder="For notifications" value="<?php echo isset($_POST['whatsapp']) ? htmlspecialchars($_POST['whatsapp']) : ''; ?>">
        </div>
        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" required placeholder="name@example.com" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required placeholder="******">
        </div>
        <div class="form-group">
            <label>Referral Code (Optional)</label>
            <input type="text" name="referral_code" 
                   value="<?php echo isset($_GET['ref']) ? htmlspecialchars($_GET['ref']) : ''; ?>" 
                   placeholder="Friend's Code">
            <small style="color: #4caf50;">Use a code to get <?php echo $NEW_USER_BONUS; ?> Coins!</small>
        </div>

        <button type="submit" class="btn-primary">Sign Up</button>
    </form>
    <p style="margin-top: 15px;">Already have an account? <a href="login.php" style="color: var(--gold);">Login here</a></p>
</div>
<?php include 'includes/footer.php'; ?>
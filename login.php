<?php // File: login.php ?><?php
// File: login.php
require_once 'config/db.php';
require_once 'config/config.php';

if (isLoggedIn()) {
    redirect('index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        $stmt = $pdo->prepare("SELECT id, name, password FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Password Correct
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            redirect('index.php');
        } else {
            $error = "Invalid email or password.";
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="auth-box">
    <h2>🎅 Welcome Back</h2>
    <p>Login to continue winning!</p>

    <?php if($error): ?>
        <p style="color: #ff6b6b; background: rgba(0,0,0,0.5); padding: 5px; border-radius: 5px;"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" required>
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>

        <button type="submit" class="btn-primary">Login</button>
    </form>

    <p style="margin-top: 15px;">New here? <a href="register.php" style="color: var(--gold);">Create an account</a></p>
</div>

<?php include 'includes/footer.php'; ?>
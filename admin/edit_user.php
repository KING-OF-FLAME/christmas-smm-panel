<?php
// File: admin/edit_user.php
require_once '../config/db.php';
require_once '../config/config.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$message = "";

// Handle Form Submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $coins = intval($_POST['coins']);
    $spins = intval($_POST['spins']);
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    
    $sql = "UPDATE users SET name = ?, email = ?, coins = ?, spins_left = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute([$name, $email, $coins, $spins, $id])) {
        $message = "<div style='background:#d4edda; color:#155724; padding:10px; margin-bottom:15px;'>User Updated Successfully!</div>";
    } else {
        $message = "<div style='background:#f8d7da; color:#721c24; padding:10px; margin-bottom:15px;'>Error Updating User.</div>";
    }
}

// Fetch Current Data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) { die("User not found."); }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
    <style>
        body { font-family: sans-serif; padding: 20px; background: #f4f4f4; }
        .box { max-width: 500px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        input { width: 100%; padding: 10px; margin: 5px 0 20px 0; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        label { font-weight: bold; color: #333; }
        button { width: 100%; padding: 12px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        button:hover { background: #0056b3; }
        .back-link { display: block; margin-top: 15px; text-align: center; color: #666; text-decoration: none; }
    </style>
</head>
<body>

<div class="box">
    <h2 style="margin-top:0;">Edit User (ID: <?php echo $user['id']; ?>)</h2>
    <?php echo $message; ?>
    
    <form method="POST">
        <label>Full Name</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>

        <label>Email Address</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

        <label>Coins Balance</label>
        <input type="number" name="coins" value="<?php echo $user['coins']; ?>">

        <label>Spins Left</label>
        <input type="number" name="spins" value="<?php echo $user['spins_left']; ?>">

        <button type="submit">Update User</button>
    </form>
    
    <a href="users.php" class="back-link">&larr; Back to User Manager</a>
</div>

</body>
</html>
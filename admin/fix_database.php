<?php
// File: admin/fix_database.php
require_once '../config/db.php';
require_once '../config/config.php';

echo "<h2>Database Fixer Tool</h2>";

try {
    // 1. DROP Tables (Remove old/broken data)
    $pdo->exec("DROP TABLE IF EXISTS coupon_usage");
    $pdo->exec("DROP TABLE IF EXISTS coupons");
    echo "<p style='color:green'>✔ Old Coupon Tables Deleted.</p>";

    // 2. CREATE Tables (Fresh & Clean)
    $sql1 = "CREATE TABLE coupons (
        id INT AUTO_INCREMENT PRIMARY KEY,
        code VARCHAR(50) UNIQUE NOT NULL,
        reward_coins INT DEFAULT 0,
        max_uses INT DEFAULT 100,
        used_count INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql1);
    echo "<p style='color:green'>✔ New 'coupons' table created.</p>";

    $sql2 = "CREATE TABLE coupon_usage (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        coupon_id INT NOT NULL,
        used_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_usage (user_id, coupon_id)
    )";
    $pdo->exec($sql2);
    echo "<p style='color:green'>✔ New 'coupon_usage' table created.</p>";

    // 3. Insert a TEST Code automatically
    $stmt = $pdo->prepare("INSERT INTO coupons (code, reward_coins, max_uses) VALUES ('TEST100', 100, 1000)");
    $stmt->execute();
    echo "<p style='color:blue'>✔ Created promo code: <strong>TEST100</strong> (Reward: 100 Coins)</p>";

    echo "<h3>DONE! Now go to your Dashboard and try code: TEST100</h3>";
    echo "<a href='../index.php'>Go to Home</a>";

} catch (Exception $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
}
?>
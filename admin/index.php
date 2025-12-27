<?php
// File: admin/index.php
session_start();
require_once '../config/db.php';

// Check Admin Login
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

// --- FETCH DASHBOARD STATS ---

// 1. Total Users
$users_count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

// 2. Total Orders
$orders_count = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();

// 3. Pending Orders (Important for manual review)
$pending_orders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn();

// 4. Total Coins (Liability)
$total_coins = $pdo->query("SELECT SUM(coins) FROM users")->fetchColumn();
$total_coins = $total_coins ? $total_coins : 0; // Handle NULL if 0 users

// 5. Active Services
$active_services = $pdo->query("SELECT COUNT(*) FROM services WHERE active = 1")->fetchColumn();

// 6. Active Coupons (Optional check to avoid error if table missing)
try {
    $active_coupons = $pdo->query("SELECT COUNT(*) FROM coupons WHERE used_count < max_uses")->fetchColumn();
} catch (Exception $e) {
    $active_coupons = 0; // Default to 0 if table doesn't exist yet
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; }
        .card { border: none; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: transform 0.2s; }
        .card:hover { transform: translateY(-5px); }
        .nav-link { color: rgba(255,255,255,0.8) !important; font-weight: 500; margin-right: 15px; }
        .nav-link:hover { color: #fff !important; }
        .nav-link.active { color: #fff !important; font-weight: bold; border-bottom: 2px solid #fff; }
    </style>
</head>
<body>
    
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark py-3">
        <div class="container">
            <a class="navbar-brand" href="index.php"><i class="fa-solid fa-gift"></i> Admin Panel</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link active" href="index.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="users.php">Users</a></li>
                    <li class="nav-item"><a class="nav-link" href="orders.php">Orders</a></li>
                    <li class="nav-item"><a class="nav-link" href="services.php">Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="coupons.php">Coupons</a></li>
                    <li class="nav-item"><a class="nav-link" href="settings.php">Settings</a></li>
                    <li class="nav-item"><a class="btn btn-danger btn-sm ms-2" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Dashboard Overview</h2>
            <a href="settings.php" class="btn btn-outline-dark"><i class="fa-solid fa-gear"></i> Site Settings</a>
        </div>
        
        <div class="row">
            <div class="col-md-3">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title">Total Users</h6>
                                <h2 class="display-6 fw-bold"><?php echo number_format($users_count); ?></h2>
                            </div>
                            <i class="fa-solid fa-users fa-3x opacity-50"></i>
                        </div>
                        <a href="users.php" class="text-white text-decoration-none small">Manage Users &rarr;</a>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-white bg-success mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title">Total Orders</h6>
                                <h2 class="display-6 fw-bold"><?php echo number_format($orders_count); ?></h2>
                            </div>
                            <i class="fa-solid fa-cart-shopping fa-3x opacity-50"></i>
                        </div>
                        <a href="orders.php" class="text-white text-decoration-none small">View Logs &rarr;</a>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-white bg-info mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title">Active Services</h6>
                                <h2 class="display-6 fw-bold"><?php echo number_format($active_services); ?></h2>
                            </div>
                            <i class="fa-solid fa-list-check fa-3x opacity-50"></i>
                        </div>
                        <a href="services.php" class="text-white text-decoration-none small">Update Services &rarr;</a>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-white bg-warning mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title">Pending / Review</h6>
                                <h2 class="display-6 fw-bold"><?php echo number_format($pending_orders); ?></h2>
                            </div>
                            <i class="fa-solid fa-clock fa-3x opacity-50"></i>
                        </div>
                        <a href="orders.php?status=pending" class="text-white text-decoration-none small">Check Issues &rarr;</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-2">
            <div class="col-md-4">
                <div class="card bg-white text-dark mb-3 border">
                    <div class="card-body d-flex align-items-center">
                        <div class="bg-warning text-white rounded-circle p-3 me-3">
                            <i class="fa-solid fa-coins fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0">Coins Held</h5>
                            <h3 class="fw-bold mb-0"><?php echo number_format($total_coins); ?></h3>
                            <small class="text-muted">Total User Balance</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card bg-white text-dark mb-3 border">
                    <div class="card-body d-flex align-items-center">
                        <div class="bg-danger text-white rounded-circle p-3 me-3">
                            <i class="fa-solid fa-ticket fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0">Active Coupons</h5>
                            <h3 class="fw-bold mb-0"><?php echo number_format($active_coupons); ?></h3>
                            <small class="text-muted"><a href="coupons.php" class="text-decoration-none">Manage Codes &rarr;</a></small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card bg-white text-dark mb-3 border" style="border-left: 5px solid #333 !important;">
                    <div class="card-body d-flex align-items-center">
                        <div class="bg-dark text-white rounded-circle p-3 me-3">
                            <i class="fa-solid fa-gear fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0">Site Settings</h5>
                            <small class="text-muted d-block">Config & Limits</small>
                            <a href="settings.php" class="btn btn-sm btn-outline-dark mt-2">Open Settings</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
// File: admin/services.php
session_start();
require_once '../config/db.php';
require_once '../config/config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

// Handle Add/Delete
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_service'])) {
        $name = sanitize($_POST['name']);
        // FIX: Use correct variable name
        $api_id = (int)$_POST['api_service_id']; 
        $type = $_POST['type']; // likes/followers
        $qty = (int)$_POST['quantity'];
        $cost = (int)$_POST['cost_coins'];

        // FIX: Insert into 'api_service_id', not 'smm_service_id'
        $stmt = $pdo->prepare("INSERT INTO services (name, api_service_id, category, min_qty, max_qty, cost_coins, active) VALUES (?, ?, ?, ?, ?, ?, 1)");
        // We set min/max qty same as qty for manual packages, and 'Manual' as category
        $stmt->execute([$name, $api_id, 'Manual Add', $qty, $qty, $cost]);
    }

    if (isset($_POST['delete_id'])) {
        $stmt = $pdo->prepare("DELETE FROM services WHERE id = ?");
        $stmt->execute([(int)$_POST['delete_id']]);
    }
}

$services = $pdo->query("SELECT * FROM services ORDER BY id DESC")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Services</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Manage Services</h2>
            <div>
                <a href="import_services.php" class="btn btn-warning">Import from API</a>
                <a href="index.php" class="btn btn-secondary">Dashboard</a>
            </div>
        </div>

        <div class="card p-3 mb-4 bg-light border">
            <h5>Add Custom Service</h5>
            <form method="POST" class="row g-3">
                <div class="col-md-3">
                    <input type="text" name="name" class="form-control" placeholder="Name (e.g. 100 Likes)" required>
                </div>
                <div class="col-md-2">
                    <input type="number" name="api_service_id" class="form-control" placeholder="SMM ID (e.g. 51339)" required>
                </div>
                <div class="col-md-2">
                    <select name="type" class="form-control">
                        <option value="likes">Likes</option>
                        <option value="followers">Followers</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="number" name="quantity" class="form-control" placeholder="Qty (e.g. 100)" required>
                </div>
                <div class="col-md-2">
                    <input type="number" name="cost_coins" class="form-control" placeholder="Cost (Coins)" required>
                </div>
                <div class="col-md-1">
                    <button type="submit" name="add_service" class="btn btn-primary w-100">Add</button>
                </div>
            </form>
        </div>

        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>API ID (SMM)</th>
                    <th>Category</th>
                    <th>Qty</th>
                    <th>Cost</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($services as $svc): ?>
                <tr>
                    <td><?php echo $svc['id']; ?></td>
                    <td><?php echo htmlspecialchars($svc['name']); ?></td>
                    <td class="fw-bold text-primary"><?php echo $svc['api_service_id']; ?></td>
                    <td><?php echo htmlspecialchars($svc['category'] ?? '-'); ?></td>
                    <td><?php echo $svc['min_qty']; ?></td>
                    <td><?php echo $svc['cost_coins']; ?></td>
                    <td>
                        <form method="POST" onsubmit="return confirm('Delete this service?');">
                            <input type="hidden" name="delete_id" value="<?php echo $svc['id']; ?>">
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
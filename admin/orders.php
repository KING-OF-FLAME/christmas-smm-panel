<?php
// File: admin/orders.php
require_once '../config/db.php';
require_once '../config/config.php';

// Increase timeout for bulk actions
set_time_limit(300); 

// --- 1. HANDLE BULK ACTIONS ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulk_action']) && isset($_POST['order_ids'])) {
    
    $api_url = defined('SMM_API_URL') ? SMM_API_URL : $API_URL;
    $api_key = defined('SMM_API_KEY') ? SMM_API_KEY : $API_KEY;
    
    $action = $_POST['bulk_action'];
    $selected_ids = $_POST['order_ids'];
    $count = 0;
    $msg_type = "success";

    // --- A. BULK CHECK STATUS (From SMM API) ---
    if ($action === 'check_status') {
        foreach ($selected_ids as $oid) {
            $stmt = $pdo->prepare("SELECT api_order_id, status FROM orders WHERE id = ?");
            $stmt->execute([$oid]);
            $order = $stmt->fetch();

            if ($order && $order['api_order_id'] > 0) {
                $post = ['key' => $api_key, 'action' => 'status', 'order' => $order['api_order_id']];
                
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $api_url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $res = curl_exec($ch);
                curl_close($ch);
                
                $data = json_decode($res, true);
                if (isset($data['status'])) {
                    $new_status = strtolower($data['status']);
                    $remains = isset($data['remains']) ? $data['remains'] : 0;
                    if ($new_status != strtolower($order['status'])) {
                        $pdo->prepare("UPDATE orders SET status = ?, remains = ? WHERE id = ?")->execute([$new_status, $remains, $oid]);
                        $count++;
                    }
                }
            }
        }
        $msg = "Checked statuses. Updated $count orders.";
    }

    // --- B. BULK APPROVE (Send Pending to API) ---
    elseif ($action === 'approve') {
        foreach ($selected_ids as $oid) {
            // Fetch complete order details needed for API
            $stmt = $pdo->prepare("SELECT o.*, s.api_service_id, s.min_qty FROM orders o JOIN services s ON o.service_id = s.id WHERE o.id = ? AND o.status = 'pending'");
            $stmt->execute([$oid]);
            $order = $stmt->fetch();

            if ($order) {
                $post = [
                    'key' => $api_key,
                    'action' => 'add',
                    'service' => $order['api_service_id'],
                    'link' => $order['link'],
                    'quantity' => $order['min_qty'] // Or calculate based on cost if variable
                ];

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $api_url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $res = curl_exec($ch);
                curl_close($ch);
                $api_res = json_decode($res, true);

                if (isset($api_res['order'])) {
                    $pdo->prepare("UPDATE orders SET status = 'processing', api_order_id = ? WHERE id = ?")->execute([$api_res['order'], $oid]);
                    $count++;
                }
            }
        }
        $msg = "Successfully Approved & Sent $count orders to API.";
    }

    // --- C. BULK REJECT (Refund Coins) ---
    elseif ($action === 'reject') {
        foreach ($selected_ids as $oid) {
            $stmt = $pdo->prepare("SELECT id, user_id, cost, status FROM orders WHERE id = ? AND status = 'pending'");
            $stmt->execute([$oid]);
            $order = $stmt->fetch();

            if ($order) {
                // Refund Coins
                $pdo->prepare("UPDATE users SET coins = coins + ? WHERE id = ?")->execute([$order['cost'], $order['user_id']]);
                // Mark Canceled
                $pdo->prepare("UPDATE orders SET status = 'canceled' WHERE id = ?")->execute([$oid]);
                $count++;
            }
        }
        $msg = "Rejected $count orders and refunded coins.";
    }
}

// --- PAGINATION & FILTERS ---
$limit = 50;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$where_sql = "";
$params = [];
if ($status_filter != 'all') {
    $where_sql = "WHERE o.status = ?";
    $params[] = $status_filter;
}

// FETCH ORDERS
$sql = "SELECT o.*, u.name as user_name, u.email as user_email, s.name as service_name, s.api_service_id 
        FROM orders o 
        LEFT JOIN users u ON o.user_id = u.id 
        LEFT JOIN services s ON o.service_id = s.id 
        $where_sql 
        ORDER BY o.id DESC LIMIT $limit OFFSET $offset";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll();

// Count Total
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM orders o $where_sql");
$countStmt->execute($params);
$total_rows = $countStmt->fetchColumn();
$total_pages = ceil($total_rows / $limit);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function toggleAll(source) {
            checkboxes = document.getElementsByName('order_ids[]');
            for(var i=0, n=checkboxes.length;i<n;i++) {
                if(!checkboxes[i].disabled) {
                    checkboxes[i].checked = source.checked;
                }
            }
        }
    </script>
    <style>
        body { background-color: #f4f6f9; padding: 20px; }
        .table-container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .smm-id { font-family: monospace; font-weight: bold; color: #555; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-0">Order Manager</h2>
            <small class="text-muted">Total Orders: <?php echo $total_rows; ?></small>
        </div>
        <div>
            <a href="index.php" class="btn btn-dark btn-sm">Back to Dashboard</a>
        </div>
    </div>

    <?php if(isset($msg)): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?php echo $msg; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="mb-3 d-flex justify-content-between">
        <div class="btn-group">
            <a href="?status=all" class="btn btn-outline-secondary <?php echo $status_filter=='all'?'active':''; ?>">All</a>
            <a href="?status=pending" class="btn btn-outline-warning <?php echo $status_filter=='pending'?'active':''; ?>">Pending</a>
            <a href="?status=processing" class="btn btn-outline-primary <?php echo $status_filter=='processing'?'active':''; ?>">Processing</a>
        </div>
    </div>

    <form method="POST">
        <div class="mb-3 p-3 bg-white border rounded shadow-sm d-flex align-items-center gap-2">
            <span class="fw-bold">With Selected:</span>
            
            <select name="bulk_action" class="form-select form-select-sm" style="width: auto;" required>
                <option value="" disabled selected>-- Select Action --</option>
                <option value="check_status">🔄 Check Live Status (From API)</option>
                <option value="approve">✅ Bulk Approve (Send to API)</option>
                <option value="reject">❌ Bulk Reject (Refund Coins)</option>
            </select>

            <button type="submit" class="btn btn-primary btn-sm" onclick="return confirm('Are you sure you want to perform this bulk action?');">
                Apply Action
            </button>
        </div>

        <div class="table-container">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th width="40"><input type="checkbox" onclick="toggleAll(this)"></th>
                        <th>ID</th>
                        <th>User</th>
                        <th>Service</th>
                        <th>Link</th>
                        <th>SMM ID</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($orders as $o): ?>
                    <tr>
                        <td>
                            <input type="checkbox" name="order_ids[]" value="<?php echo $o['id']; ?>">
                        </td>

                        <td>#<?php echo $o['id']; ?></td>
                        <td>
                            <strong><?php echo htmlspecialchars($o['user_name']); ?></strong><br>
                            <small class="text-muted"><?php echo htmlspecialchars($o['user_email']); ?></small>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($o['service_name']); ?>
                            <br><span class="badge bg-secondary">API: <?php echo $o['api_service_id']; ?></span>
                        </td>
                        <td>
                            <a href="<?php echo htmlspecialchars($o['link']); ?>" target="_blank" class="text-decoration-none">Link ↗</a>
                        </td>
                        <td class="smm-id">
                            <?php echo $o['api_order_id'] ? $o['api_order_id'] : '-'; ?>
                        </td>
                        <td>
                            <?php 
                                $status = !empty($o['status']) ? strtolower($o['status']) : 'unknown';
                                $badgeClass = 'bg-secondary';
                                if(strpos($status, 'pend') !== false) $badgeClass = 'bg-warning text-dark';
                                if(strpos($status, 'process') !== false) $badgeClass = 'bg-primary';
                                if(strpos($status, 'complete') !== false) $badgeClass = 'bg-success';
                                if(strpos($status, 'cancel') !== false) $badgeClass = 'bg-danger';
                            ?>
                            <span class="badge <?php echo $badgeClass; ?>">
                                <?php echo ucfirst($status); ?>
                            </span>
                        </td>
                        <td>
                            <?php if($status == 'pending' && empty($o['api_order_id'])): ?>
                                <a href="order_action.php?id=<?php echo $o['id']; ?>&action=approve" class="btn btn-success btn-sm">✔</a>
                                <a href="order_action.php?id=<?php echo $o['id']; ?>&action=reject" class="btn btn-danger btn-sm" onclick="return confirm('Refund?');">✖</a>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </form>

    <?php if ($total_pages > 1): ?>
    <nav class="mt-3">
        <ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>&status=<?php echo $status_filter; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
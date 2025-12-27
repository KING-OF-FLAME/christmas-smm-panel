<?php
// File: admin/users.php
require_once '../config/db.php';
require_once '../config/config.php';

// --- 1. HANDLE CSV EXPORT ---
if (isset($_GET['export']) && $_GET['export'] == 'csv') {
    $filename = "users_export_" . date('Y-m-d_H-i') . ".csv";
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Name', 'Email', 'Coins', 'Spins', 'Ref Code', 'Referred By', 'IP Address', 'Device ID', 'Joined Date', 'Status']);
    
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $sql = "SELECT * FROM users";
    $params = [];
    
    if ($search) {
        $sql .= " WHERE name LIKE ? OR email LIKE ? OR ip_address LIKE ?";
        $params = ["%$search%", "%$search%", "%$search%"];
    }
    $sql .= " ORDER BY id DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $status = $row['is_banned'] ? 'BANNED' : 'Active';
        $line = [$row['id'], $row['name'], $row['email'], $row['coins'], $row['spins_left'], $row['referral_code'], $row['referred_by'], $row['ip_address'], $row['device_fingerprint'], $row['created_at'], $status];
        fputcsv($output, $line);
    }
    fclose($output);
    exit;
}

// --- 2. PAGINATION & SEARCH LOGIC ---
$limit = 100; 
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$where_clause = "";
$params = [];

if ($search) {
    $where_clause = " WHERE name LIKE ? OR email LIKE ? OR ip_address LIKE ? OR referral_code LIKE ?";
    $params = ["%$search%", "%$search%", "%$search%", "%$search%"];
}

$sql = "SELECT * FROM users $where_clause ORDER BY id DESC LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll();

$countSql = "SELECT COUNT(*) FROM users $where_clause";
$countStmt = $pdo->prepare($countSql);
$countStmt->execute($params);
$total_rows = $countStmt->fetchColumn();
$total_pages = ceil($total_rows / $limit);
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Manager</title>
    <style>
        body { font-family: sans-serif; background: #f4f4f4; padding: 20px; }
        .container { max-width: 1400px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .search-box input { padding: 8px; width: 300px; border: 1px solid #ccc; border-radius: 4px; }
        .search-box button { padding: 8px 15px; background: #333; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .btn-export { background: #28a745; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px; font-size: 14px; }
        .btn-back { background: #007bff; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px; font-size: 14px; margin-right: 15px; }
        
        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        th, td { padding: 12px 10px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background: #333; color: white; }
        tr:hover { background: #f9f9f9; }
        .banned-row { background-color: #ffe6e6 !important; }
        .badge { padding: 3px 8px; border-radius: 12px; font-size: 11px; font-weight: bold; }
        .badge-green { background: #d4edda; color: #155724; }
        .badge-red { background: #f8d7da; color: #721c24; }
        .actions a { text-decoration: none; margin-right: 10px; font-weight: bold; }
        .edit-btn { color: #007bff; }
        .ban-btn { color: #dc3545; }
        .unban-btn { color: #28a745; }
        .pagination { margin-top: 20px; text-align: center; }
        .pagination a { display: inline-block; padding: 5px 10px; border: 1px solid #ddd; margin: 0 2px; text-decoration: none; color: #333; }
        .pagination a.active { background: #333; color: white; border-color: #333; }
    </style>
</head>
<body>

<div class="container">
    <div style="margin-bottom: 15px;">
        <a href="../index.php" class="btn-back">&larr; Back to Home</a>
        <a href="import_services.php" class="btn-back" style="background:#6c757d;">Manage Services</a>
    </div>

    <div class="top-bar">
        <h2>User Manager <span style="font-size:16px; color:#666;">(<?php echo $total_rows; ?> Total)</span></h2>
        <div class="search-box">
            <form method="GET" style="display:inline-flex; gap:10px;">
                <input type="text" name="search" placeholder="Search Name, Email, IP, Ref Code" value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit">Search</button>
                <?php if($search): ?>
                    <a href="users.php" style="padding: 8px; background: #ddd; color: #333; text-decoration: none; border-radius: 4px;">Clear</a>
                <?php endif; ?>
            </form>
            <a href="?export=csv&search=<?php echo htmlspecialchars($search); ?>" class="btn-export">Export CSV</a>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>User Details</th>
                <th>Wallet</th>
                <th>Stats</th>
                <th>Security Info</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $u): ?>
            <tr class="<?php echo $u['is_banned'] ? 'banned-row' : ''; ?>">
                <td><?php echo $u['id']; ?></td>
                <td>
                    <strong><?php echo htmlspecialchars($u['name']); ?></strong><br>
                    <small style="color:#666;"><?php echo htmlspecialchars($u['email']); ?></small><br>
                    <small style="color:#666;"><?php echo htmlspecialchars($u['whatsapp']); ?></small>
                </td>
                <td>
                    <span style="color: #d35400; font-weight:bold;"><?php echo number_format($u['coins']); ?> Coins</span>
                </td>
                <td>
                    Spins: <?php echo $u['spins_left']; ?><br>
                    Ref Code: <code><?php echo $u['referral_code']; ?></code>
                </td>
                <td>
                    IP: <?php echo $u['ip_address']; ?><br>
                    <small title="<?php echo $u['device_fingerprint']; ?>">
                        Device: <?php echo substr($u['device_fingerprint'] ?? '', 0, 10); ?>...
                    </small>
                </td>
                <td>
                    <?php if($u['is_banned']): ?>
                        <span class="badge badge-red">BANNED</span>
                    <?php else: ?>
                        <span class="badge badge-green">ACTIVE</span>
                    <?php endif; ?>
                </td>
                <td class="actions">
                    <a href="edit_user.php?id=<?php echo $u['id']; ?>" class="edit-btn">Edit</a>
                    <?php if($u['is_banned']): ?>
                        <a href="ban_user.php?id=<?php echo $u['id']; ?>&action=unban" class="unban-btn" onclick="return confirm('Unban this user?');">Unban</a>
                    <?php else: ?>
                        <a href="ban_user.php?id=<?php echo $u['id']; ?>&action=ban" class="ban-btn" onclick="return confirm('Ban this user?');">Ban</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php if ($total_pages > 1): ?>
    <div class="pagination">
        <?php 
        $range = 2;
        if($page > 1) echo '<a href="?page=1&search='.$search.'">&laquo; First</a>';
        if($page > 1) echo '<a href="?page='.($page-1).'&search='.$search.'">Prev</a>';
        for ($i = max(1, $page - $range); $i <= min($total_pages, $page + $range); $i++) {
            $active = ($i == $page) ? 'active' : '';
            echo '<a href="?page='.$i.'&search='.$search.'" class="'.$active.'">'.$i.'</a>';
        }
        if($page < $total_pages) echo '<a href="?page='.($page+1).'&search='.$search.'">Next</a>';
        if($page < $total_pages) echo '<a href="?page='.$total_pages.'&search='.$search.'">Last &raquo;</a>';
        ?>
    </div>
    <?php endif; ?>
</div>
</body>
</html>
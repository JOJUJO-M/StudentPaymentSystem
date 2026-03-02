<?php
// modules/activity_logs/list.php
$page_title = 'Activity Logs';
include __DIR__ . '/../../includes/header.php';
require_login();

$school_id = $_SESSION['user']['school_id'] ?? null;
$where = " WHERE 1=1 ";
$params = [];

if ($school_id) {
    $where .= " AND u.school_id = ? ";
    $params[] = $school_id;
}

$sql = "SELECT al.*, u.username FROM activity_logs al 
        JOIN users u ON al.user_id = u.id 
        $where 
        ORDER BY al.timestamp DESC LIMIT 100";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$logs = $stmt->fetchAll();
?>

<div class="card">
    <div class="card-header">
        <h3>System Activity Logs</h3>
        <p class="text-muted">Showing last 100 records</p>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>User</th>
                    <th>Action</th>
                    <th>Details</th>
                    <th>School</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><strong>
                                <?php echo htmlspecialchars($log['username']); ?>
                            </strong></td>
                        <td><span class="badge bg-info">
                                <?php echo htmlspecialchars($log['action']); ?>
                            </span></td>
                        <td>
                            <?php echo htmlspecialchars($log['details'] ?? '-'); ?>
                        </td>
                        <td>
                            <?php echo $log['school_id'] ?? 'Global'; ?>
                        </td>
                        <td>
                            <?php echo date('M d, H:i:s', strtotime($log['timestamp'])); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
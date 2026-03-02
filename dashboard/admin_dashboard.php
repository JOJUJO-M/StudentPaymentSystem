<?php
// dashboard/admin_dashboard.php
$page_title = 'Admin Dashboard';
include __DIR__ . '/../includes/header.php';

$school_id = $_SESSION['user']['school_id'] ?? null;

if (!$school_id && !in_array('global_admin', $_SESSION['roles'])) {
    echo '<div class="alert alert-danger">Error: No school assigned to this administrator. Please contact Super Admin.</div>';
    include __DIR__ . '/../includes/footer.php';
    exit;
}

// School-specific stats
$stats_queries = [
    'students' => "SELECT COUNT(*) FROM students WHERE school_id = ?",
    'teachers' => "SELECT COUNT(*) FROM teachers t JOIN users u ON t.user_id = u.id WHERE u.school_id = ?",
    'classes' => "SELECT COUNT(*) FROM classes WHERE school_id = ?",
    'revenue' => "SELECT SUM(amount) FROM payments WHERE school_id = ?",
];

$stats = [];
foreach ($stats_queries as $key => $sql) {
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$school_id]);
    $stats[$key] = $stmt->fetchColumn() ?: 0;
}

$stmt_act = $pdo->prepare("SELECT al.*, u.username FROM activity_logs al 
    JOIN users u ON al.user_id = u.id 
    WHERE u.school_id = ? 
    ORDER BY timestamp DESC LIMIT 10");
$stmt_act->execute([$school_id]);
$recent_activities = $stmt_act->fetchAll();
?>

<div class="welcome-banner">
    <h1>Welcome,
        <?php echo htmlspecialchars($_SESSION['username']); ?>
    </h1>
    <p>Management Overview for <?php echo htmlspecialchars($_SESSION['school']['name'] ?? 'Your School'); ?>
        (<?php echo htmlspecialchars($_SESSION['school']['reg_number'] ?? '-'); ?>)</p>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-info">
            <h3>Students</h3>
            <div class="stat-value">
                <?php echo $stats['students']; ?>
            </div>
        </div>
        <div class="stat-icon bg-primary-light">
            <i class="fas fa-user-graduate"></i>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <h3>Teachers</h3>
            <div class="stat-value">
                <?php echo $stats['teachers']; ?>
            </div>
        </div>
        <div class="stat-icon bg-success-light">
            <i class="fas fa-chalkboard-teacher"></i>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <h3>Classes</h3>
            <div class="stat-value">
                <?php echo $stats['classes']; ?>
            </div>
        </div>
        <div class="stat-icon bg-warning-light">
            <i class="fas fa-school"></i>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <h3>Term Revenue</h3>
            <div class="stat-value">$
                <?php echo number_format($stats['revenue'], 2); ?>
            </div>
        </div>
        <div class="stat-icon bg-danger-light">
            <i class="fas fa-wallet"></i>
        </div>
    </div>
</div>

<!-- School Management Grid -->
<div class="grid-3 mt-4">
    <div class="card bg-gradient-success text-white">
        <div class="card-body">
            <h4>Academic Control</h4>
            <p>Manage your school's students and teachers.</p>
            <div class="mt-3">
                <a href="<?php echo $project_root; ?>modules/students/list.php"
                    class="btn btn-light btn-sm btn-block mb-1">Enrolled Students</a>
                <a href="<?php echo $project_root; ?>modules/teachers/list.php"
                    class="btn btn-light btn-sm btn-block">Faculty Members</a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3>Operations</h3>
        </div>
        <div class="card-body p-0">
            <div class="list-group">
                <a href="<?php echo $project_root; ?>modules/classes/list.php" class="list-group-item"><i
                        class="fas fa-door-open"></i> Class Management</a>
                <a href="<?php echo $project_root; ?>modules/academic_years/list.php" class="list-group-item"><i
                        class="fas fa-calendar-check"></i> Term Settings</a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3>Finances</h3>
        </div>
        <div class="card-body p-0">
            <div class="list-group">
                <a href="<?php echo $project_root; ?>modules/payments/list.php" class="list-group-item"><i
                        class="fas fa-receipt"></i> Fee Collection</a>
                <a href="<?php echo $project_root; ?>modules/payments/reports.php" class="list-group-item"><i
                        class="fas fa-chart-line"></i> Revenue Reports</a>
                <a href="<?php echo $project_root; ?>modules/expenses/list.php" class="list-group-item text-danger"><i
                        class="fas fa-file-invoice-dollar"></i> School Expenses</a>
            </div>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        <h3>Recent School Activity</h3>
        <a href="<?php echo $project_root; ?>modules/activity_logs/list.php" class="btn btn-outline btn-sm">Audit
            Trail</a>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>User</th>
                    <th>Action</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recent_activities as $activity): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($activity['username']); ?></strong></td>
                        <td><span class="status-badge info"><?php echo $activity['action']; ?></span></td>
                        <td><?php echo date('M d, H:i', strtotime($activity['timestamp'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
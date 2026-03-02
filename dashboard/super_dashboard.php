<?php
// dashboard/super_dashboard.php
$page_title = 'Super Admin Dashboard';
include __DIR__ . '/../includes/header.php';

// Global Stats
$stats = [
    'total_schools' => $pdo->query("SELECT COUNT(*) FROM schools")->fetchColumn(),
    'total_users' => $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
    'total_students' => $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn(),
    'total_revenue' => $pdo->query("SELECT SUM(amount) FROM payments")->fetchColumn() ?? 0,
];

$recent_activities = $pdo->query("SELECT al.*, u.username FROM activity_logs al LEFT JOIN users u ON al.user_id = u.id ORDER BY timestamp DESC LIMIT 10")->fetchAll();
?>

<div class="welcome-banner">
    <h1>Welcome, Super Admin</h1>
    <p>Global Overview of the School Management System</p>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-info">
            <h3>Total Schools</h3>
            <div class="stat-value">
                <?php echo $stats['total_schools']; ?>
            </div>
        </div>
        <div class="stat-icon bg-primary-light">
            <i class="fas fa-university"></i>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <h3>Total Users</h3>
            <div class="stat-value">
                <?php echo $stats['total_users']; ?>
            </div>
        </div>
        <div class="stat-icon bg-success-light">
            <i class="fas fa-users"></i>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <h3>Total Students</h3>
            <div class="stat-value">
                <?php echo $stats['total_students']; ?>
            </div>
        </div>
        <div class="stat-icon bg-info-light">
            <i class="fas fa-user-graduate"></i>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <h3>Total Revenue</h3>
            <div class="stat-value">$
                <?php echo number_format($stats['total_revenue'], 2); ?>
            </div>
        </div>
        <div class="stat-icon bg-warning-light">
            <i class="fas fa-money-bill-wave"></i>
        </div>
    </div>
</div>

<!-- Quick Actions Grid -->
<div class="grid-3 mt-4">
    <div class="card bg-gradient-primary text-white">
        <div class="card-body">
            <h4>System Control</h4>
            <p>Manage schools and global configurations.</p>
            <div class="mt-3">
                <a href="<?php echo $project_root; ?>modules/schools/list.php"
                    class="btn btn-light btn-sm btn-block mb-1">Manage Schools</a>
                <a href="<?php echo $project_root; ?>modules/users/list.php"
                    class="btn btn-light btn-sm btn-block">Manage Global Admins</a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3>Infrastructure</h3>
        </div>
        <div class="card-body p-0">
            <div class="list-group">
                <a href="<?php echo $project_root; ?>modules/schools/create.php" class="list-group-item"><i
                        class="fas fa-plus"></i> New School Registration</a>
                <a href="<?php echo $project_root; ?>modules/academic_years/list.php" class="list-group-item"><i
                        class="fas fa-calendar-alt"></i> Global Academic Years</a>
                <a href="<?php echo $project_root; ?>modules/settings/index.php" class="list-group-item"><i
                        class="fas fa-cogs"></i> System Settings</a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3>Security & Logs</h3>
        </div>
        <div class="card-body p-0">
            <div class="list-group">
                <a href="<?php echo $project_root; ?>modules/logs/audit.php" class="list-group-item"><i
                        class="fas fa-shield-alt"></i> System Audit Trail</a>
                <a href="<?php echo $project_root; ?>modules/roles/permissions.php" class="list-group-item"><i
                        class="fas fa-user-lock"></i> Role Permissions</a>
                <a href="<?php echo $project_root; ?>modules/db/backup.php" class="list-group-item text-danger"><i
                        class="fas fa-database"></i> Database Backup</a>
            </div>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        <h3>Global Recent Activity</h3>
        <a href="<?php echo $project_root; ?>modules/activity_logs/list.php" class="btn btn-outline btn-sm">View Full
            Log</a>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>User</th>
                    <th>Action</th>
                    <th>Details</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recent_activities as $activity): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($activity['username'] ?? 'System'); ?></strong></td>
                        <td><span class="badge bg-info"><?php echo $activity['action']; ?></span></td>
                        <td><?php echo htmlspecialchars($activity['details'] ?? '-'); ?></td>
                        <td><?php echo date('M d, H:i', strtotime($activity['timestamp'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
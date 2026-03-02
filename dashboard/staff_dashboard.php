<?php
// dashboard/staff_dashboard.php
$page_title = 'Staff Dashboard';
include __DIR__ . '/../includes/header.php';

$school_id = $_SESSION['user']['school_id'] ?? null;

// Staff Stats
$stmt_attendance = $pdo->prepare("SELECT COUNT(*) FROM attendance WHERE date = CURDATE() AND status = 'present' AND school_id = ?");
$stmt_attendance->execute([$school_id]);

$stmt_payments = $pdo->prepare("SELECT COUNT(*) FROM payments WHERE school_id = ? AND payment_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)");
$stmt_payments->execute([$school_id]);

$stats = [
    'attendance_today' => $stmt_attendance->fetchColumn() ?: 0,
    'recent_payments' => $stmt_payments->fetchColumn() ?: 0,
];

$stmt_students = $pdo->prepare("SELECT s.*, u.full_name, c.name as class_name 
    FROM students s 
    JOIN users u ON s.user_id = u.id 
    LEFT JOIN classes c ON s.class_id = c.id 
    WHERE s.school_id = ? 
    ORDER BY u.created_at DESC LIMIT 5");
$stmt_students->execute([$school_id]);
$recent_students = $stmt_students->fetchAll();
?>

<div class="welcome-banner">
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
    <p>Management Overview for <?php echo htmlspecialchars($_SESSION['school']['name'] ?? 'Your School'); ?>
        (<?php echo htmlspecialchars($_SESSION['school']['reg_number'] ?? '-'); ?>)</p>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-info">
            <h3>Present Today</h3>
            <div class="stat-value">
                <?php echo $stats['attendance_today']; ?>
            </div>
        </div>
        <div class="stat-icon bg-success-light">
            <i class="fas fa-check-circle"></i>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <h3>Payments (7d)</h3>
            <div class="stat-value">
                <?php echo $stats['recent_payments']; ?>
            </div>
        </div>
        <div class="stat-icon bg-info-light">
            <i class="fas fa-file-invoice-dollar"></i>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        <h3>Recently Enrolled Students</h3>
        <a href="modules/students/list.php" class="btn btn-outline btn-sm">View All</a>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Class</th>
                    <th>Admission Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recent_students as $student): ?>
                    <tr>
                        <td><strong>
                                <?php echo htmlspecialchars($student['full_name']); ?>
                            </strong></td>
                        <td>
                            <?php echo htmlspecialchars($student['class_name'] ?? 'Not Assigned'); ?>
                        </td>
                        <td>
                            <?php echo date('M d, Y', strtotime($student['admission_date'])); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Operational Tools Grid -->
<div class="grid-2 mt-4">
    <div class="card">
        <div class="card-header">
            <h3>Academic Tools</h3>
        </div>
        <div class="card-body">
            <div class="grid-2">
                <a href="<?php echo $project_root; ?>modules/attendance/create.php"
                    class="btn btn-outline btn-block text-left p-3">
                    <i class="fas fa-calendar-check fa-lg"></i>
                    <div class="mt-2"><strong>Daily Attendance</strong><br><small>Mark student presence</small></div>
                </a>
                <a href="<?php echo $project_root; ?>modules/grades/create.php"
                    class="btn btn-outline btn-block text-left p-3">
                    <i class="fas fa-edit fa-lg"></i>
                    <div class="mt-2"><strong>Enter Marks</strong><br><small>Update exam scores</small></div>
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3>Quick Access</h3>
        </div>
        <div class="card-body p-0">
            <div class="list-group">
                <a href="<?php echo $project_root; ?>modules/students/list.php" class="list-group-item"><i
                        class="fas fa-user-graduate"></i> Search Student</a>
                <a href="<?php echo $project_root; ?>modules/payments/create.php" class="list-group-item"><i
                        class="fas fa-money-bill-wave"></i> Accept Payment</a>
                <a href="<?php echo $project_root; ?>modules/notices/create.php" class="list-group-item"><i
                        class="fas fa-bullhorn"></i> Post Notice</a>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
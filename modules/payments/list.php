<?php
// modules/payments/list.php
$page_title = 'Student Payments Tracking';
include __DIR__ . '/../../includes/header.php';
require_login();

// Restrict to admins and staff (headmaster, staff, global_admin)
if (!has_role(['global_admin', 'headmaster', 'staff'])) {
    header('Location: ../../dashboard/index.php?error=unauthorized');
    exit();
}

$search = $_GET['search'] ?? '';

// Build query based on user role and search
$school_id = $_SESSION['user']['school_id'] ?? null;
$where = " WHERE 1=1 ";
$params = [];

if ($school_id) {
    $where .= " AND p.school_id = ? ";
    $params[] = $school_id;
}

if (!empty($search)) {
    $where .= " AND (s.student_id LIKE ? OR u.full_name LIKE ? OR p.receipt_no LIKE ?) ";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$sql = "SELECT p.*, s.student_id as student_reg_id, u.full_name as student_name, ay.year_name, sch.name as school_name 
        FROM payments p 
        LEFT JOIN students s ON p.student_id = s.user_id 
        LEFT JOIN users u ON p.student_id = u.id 
        LEFT JOIN academic_years ay ON p.academic_year_id = ay.id 
        LEFT JOIN schools sch ON p.school_id = sch.id 
        $where 
        ORDER BY p.payment_date DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$payments = $stmt->fetchAll();
?>

<div class="card">
    <div class="card-header">
        <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
            <h3>Payments History</h3>
            <form action="" method="GET" style="display: flex; gap: 0.5rem; margin-left: auto;">
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>"
                    placeholder="Search by ID, Name, or Receipt..." class="form-control"
                    style="width: 250px; padding: 0.4rem;">
                <button type="submit" class="btn btn-secondary btn-sm" style="width: auto;">Search</button>
            </form>
        </div>
        <a href="create.php" class="btn btn-primary btn-sm" style="width: auto; margin-left: 1rem;"><i
                class="fas fa-plus"></i> Process New Payment</a>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Receipt No</th>
                    <th>Student ID</th>
                    <th>Student Name</th>
                    <th>Amount (TZS)</th>
                    <th>Date</th>
                    <th>Payment Method</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($payments as $payment): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($payment['receipt_no']); ?></strong></td>
                        <td><?php echo htmlspecialchars($payment['student_reg_id']); ?></td>
                        <td><?php echo htmlspecialchars($payment['student_name']); ?></td>
                        <td><?php echo number_format($payment['amount'], 2); ?></td>
                        <td><?php echo date('Y-m-d', strtotime($payment['payment_date'])); ?></td>
                        <td><?php echo ucfirst($payment['payment_method']); ?></td>
                        <td>
                            <span
                                style="background: <?php echo $payment['status'] == 'paid' ? '#e6f4ea' : '#fce8e6'; ?>; color: <?php echo $payment['status'] == 'paid' ? '#1e7e34' : '#d93025'; ?>; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.75rem;">
                                <?php echo strtoupper($payment['status']); ?>
                            </span>
                        </td>
                        <td>
                            <a href="receipt.php?id=<?php echo $payment['id']; ?>" class="btn btn-secondary btn-sm"
                                target="_blank" title="Print Receipt"><i class="fas fa-print"></i></a>
                        </td>
                    </tr>
                    <?php
                endforeach; ?>
                <?php if (empty($payments)): ?>
                    <tr>
                        <td colspan="8" style="text-align: center;">No payments recorded yet.</td>
                    </tr>
                    <?php
                endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
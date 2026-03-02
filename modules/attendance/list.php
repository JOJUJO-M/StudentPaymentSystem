<?php
// modules/attendance/list.php
$page_title = 'Attendance Records';
include __DIR__ . '/../../includes/header.php';
require_login();

$school_id = $_SESSION['user']['school_id'] ?? null;
$where = " WHERE 1=1 ";
$params = [];

if ($school_id) {
    $where .= " AND school_id = ? ";
    $params[] = $school_id;
}

$stmt = $pdo->prepare("SELECT * FROM attendance $where ORDER BY date DESC, id DESC LIMIT 50");
$stmt->execute($params);
$records = $stmt->fetchAll();
?>

<div class="card">
    <div class="card-header">
        <h3>Attendance Records</h3>
        <a href="create.php" class="btn btn-primary btn-sm" style="width: auto;"><i class="fas fa-plus"></i> Take
            Attendance</a>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Student ID</th>
                    <th>Status</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($records as $record): ?>
                    <tr>
                        <td>
                            <?php echo $record['date']; ?>
                        </td>
                        <td>
                            <?php echo $record['student_id']; ?>
                        </td>
                        <td><span class="badge <?php echo $record['status'] == 'present' ? 'bg-success' : 'bg-danger'; ?>">
                                <?php echo strtoupper($record['status']); ?>
                            </span></td>
                        <td>
                            <?php echo htmlspecialchars($record['remarks'] ?? '-'); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
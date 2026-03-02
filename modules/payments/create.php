<?php
// modules/payments/create.php
$page_title = 'Process New Payment';
include __DIR__ . '/../../includes/header.php';
require_login();

// Restrict to staff, headmaster, or global_admin
if (!has_role(['global_admin', 'headmaster', 'staff'])) {
    header('Location: ../../dashboard/index.php?error=unauthorized');
    exit();
}

$school_id = $_SESSION['user']['school_id'] ?? null;

// Fetch students for the current school
$sql = "SELECT s.user_id, s.student_id, u.full_name FROM students s 
        JOIN users u ON s.user_id = u.id 
        WHERE 1=1 ";
$params = [];
if ($school_id) {
    $sql .= " AND s.school_id = ? ";
    $params[] = $school_id;
}
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$students = $stmt->fetchAll();

// Fetch active academic years
$sql = "SELECT * FROM academic_years WHERE 1=1 ";
$params = [];
if ($school_id) {
    $sql .= " AND (school_id = ? OR school_id IS NULL) "; // Support global academic years too
    $params[] = $school_id;
}
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$academic_years = $stmt->fetchAll();
?>

<div class="card">
    <div class="card-header">
        <h3>Record Student Fee</h3>
        <a href="list.php" class="btn btn-secondary btn-sm" style="width: auto;">Back to Payments</a>
    </div>
    <div class="card-body">
        <form id="record-payment-form" class="validate-form">
            <div class="form-grid" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
                <div class="form-group" style="grid-column: span 2;">
                    <label for="student_id">Search & Select Student</label>
                    <select id="student_id" name="student_id" class="form-control" required
                        style="width: 100%; padding: 0.6rem;">
                        <option value="">-- Start Select Student --</option>
                        <?php foreach ($students as $student): ?>
                            <option value="<?php echo $student['user_id']; ?>">
                                <?php echo htmlspecialchars($student['student_id'] . ' - ' . $student['full_name']); ?>
                            </option>
                            <?php
                        endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="academic_year_id">Academic Year</label>
                    <select id="academic_year_id" name="academic_year_id" class="form-control" required>
                        <option value="">-- Choose Year --</option>
                        <?php foreach ($academic_years as $ay): ?>
                            <option value="<?php echo $ay['id']; ?>" <?php echo $ay['is_current'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($ay['year_name']); ?>
                            </option>
                            <?php
                        endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="amount">Amount (TZS)</label>
                    <input type="number" id="amount" name="amount" class="form-control" step="0.01" required
                        placeholder="e.g. 500000">
                </div>
                <div class="form-group">
                    <label for="receipt_no">Receipt No</label>
                    <input type="text" id="receipt_no" name="receipt_no" class="form-control" required
                        placeholder="REC-<?php echo time(); ?>">
                </div>
                <div class="form-group">
                    <label for="payment_method">Payment Method</label>
                    <select id="payment_method" name="payment_method" class="form-control" required>
                        <option value="cash">Cash</option>
                        <option value="bank">Bank Transfer</option>
                        <option value="mobile_money">Mobile Money (M-Pesa/Airtel Money)</option>
                        <option value="cheque">Cheque</option>
                    </select>
                </div>
                <div class="form-group" style="grid-column: span 2;">
                    <label for="remarks">Remarks (Optional)</label>
                    <textarea id="remarks" name="remarks" class="form-control"
                        placeholder="Add any details about this transaction..."></textarea>
                </div>
            </div>
            <div style="margin-top: 2rem;">
                <button type="submit" class="btn btn-primary" style="width: auto;">Submit Payment</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('record-payment-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData.entries());

        // Automatically add school_id if available
        <?php if ($school_id): ?>
            data.school_id = <?php echo $school_id; ?>;
            <?php
        endif; ?>

        data.payment_date = new Date().toISOString().slice(0, 19).replace('T', ' ');
        data.status = 'paid';

        const response = await apiCall(PROJECT_ROOT + 'api/crud.php?entity=payments&action=create', 'POST', data);

        if (response.success) {
            alert('Payment recorded successfully!');
            window.location.href = 'list.php';
        } else {
            alert(response.message);
        }
    });
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
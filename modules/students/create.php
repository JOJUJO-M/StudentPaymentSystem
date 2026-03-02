<?php
// modules/students/create.php
$page_title = 'Enroll Student';
include __DIR__ . '/../../includes/header.php';
require_login();

// Restrict to admins and staff (headmaster, staff, global_admin)
if (!has_role(['global_admin', 'headmaster', 'staff'])) {
    header('Location: ../../dashboard/index.php?error=unauthorized');
    exit();
}

$school_id = $_SESSION['user']['school_id'] ?? null;

// Fetch active academic years for student enrollment
$sql = "SELECT * FROM academic_years WHERE 1=1 ";
$params = [];
if ($school_id) {
    $sql .= " AND (school_id = ? OR school_id IS NULL) ";
    $params[] = $school_id;
}
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$academic_years = $stmt->fetchAll();

// Fetch classes for current school
$sql = "SELECT * FROM classes WHERE 1=1 ";
$params = [];
if ($school_id) {
    $sql .= " AND school_id = ? ";
    $params[] = $school_id;
}
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$classes = $stmt->fetchAll();

// Fetch schools for Global Admin
$schools = [];
if (!$school_id) {
    $schools = $pdo->query("SELECT * FROM schools ORDER BY name ASC")->fetchAll();
}
?>

<div class="card">
    <div class="card-header">
        <h3>Enroll New Student</h3>
        <a href="list.php" class="btn btn-secondary btn-sm" style="width: auto;">Student Records</a>
    </div>
    <div class="card-body">
        <form id="enroll-student-form" class="validate-form">
            <div class="form-grid" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
                <div class="form-group" style="grid-column: span 2;">
                    <label for="full_name">Full Student Name</label>
                    <input type="text" id="full_name" name="full_name" class="form-control" required
                        placeholder="e.g. John Doe">
                </div>
                <div class="form-group">
                    <label for="student_id">Official Registration ID</label>
                    <input type="text" id="student_id" name="student_id" class="form-control" required
                        placeholder="e.g. REG-001-2026">
                </div>
                <div class="form-group">
                    <label for="class_id">Assign to Class</label>
                    <select id="class_id" name="class_id" class="form-control" required>
                        <option value="">-- Choose Class --</option>
                        <?php foreach ($classes as $class): ?>
                            <option value="<?php echo $class['id']; ?>">
                                <?php echo htmlspecialchars($class['name'] . ' (' . $class['section'] . ')'); ?>
                            </option>
                            <?php
                        endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="academic_year_id">Academic Year</label>
                    <select id="academic_year_id" name="academic_year_id" class="form-control" required>
                        <option value="">-- Choose Academic Year --</option>
                        <?php foreach ($academic_years as $ay): ?>
                            <option value="<?php echo $ay['id']; ?>" <?php echo $ay['is_current'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($ay['year_name']); ?>
                            </option>
                            <?php
                        endforeach; ?>
                    </select>
                </div>
                <?php if (!$school_id): ?>
                    <div class="form-group">
                        <label for="school_assigned">Assign to School</label>
                        <select id="school_assigned" name="school_assigned" class="form-control" required>
                            <option value="">-- Select School --</option>
                            <?php foreach ($schools as $sch): ?>
                                <option value="<?php echo $sch['id']; ?>"><?php echo htmlspecialchars($sch['name']); ?>
                                </option>
                                <?php
                            endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>
            </div>
            <div style="margin-top: 2rem;">
                <button type="submit" class="btn btn-primary" style="width: auto;">Enroll Student</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('enroll-student-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData.entries());

        // Set school_id from session if available, else from form
        data.school_id = "<?php echo $school_id; ?>" || data.school_assigned;

        const response = await apiCall(PROJECT_ROOT + 'api/auth.php?action=register_student', 'POST', data);

        if (response.success) {
            alert('Student enrolled successfully!');
            window.location.href = 'list.php';
        } else {
            alert(response.message);
        }
    });
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
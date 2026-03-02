<?php
// modules/subjects/create.php
$page_title = 'Add New Subject';
include __DIR__ . '/../../includes/header.php';
require_login();

// Fetch departments for current school
$school_id = $_SESSION['user']['school_id'] ?? null;
$where = " WHERE 1=1 ";
$params = [];
if ($school_id) {
    $where .= " AND (school_id = ? OR school_id IS NULL) ";
    $params[] = $school_id;
}
$depts = $pdo->prepare("SELECT * FROM departments $where ORDER BY name ASC");
$depts->execute($params);
$departments = $depts->fetchAll();
?>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header">
        <h3>Subject Details</h3>
        <a href="list.php" class="btn btn-secondary btn-sm" style="width: auto;">Back to List</a>
    </div>
    <div class="card-body">
        <div id="alert-container"></div>
        <form id="create-subject-form">
            <div class="form-group mb-3">
                <label for="name">Subject Name</label>
                <input type="text" id="name" name="name" class="form-control" required placeholder="e.g. Mathematics">
            </div>
            <div class="form-group mb-3">
                <label for="code">Subject Code</label>
                <input type="text" id="code" name="code" class="form-control" placeholder="e.g. MATH101">
            </div>
            <div class="form-group mb-3">
                <label for="department_id">Department</label>
                <select id="department_id" name="department_id" class="form-control">
                    <option value="">-- No Department --</option>
                    <?php foreach ($departments as $dept): ?>
                        <option value="<?php echo $dept['id']; ?>">
                            <?php echo htmlspecialchars($dept['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group mb-4">
                <label for="description">Description (Optional)</label>
                <textarea id="description" name="description" class="form-control" rows="3"
                    placeholder="Brief info about this subject..."></textarea>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Create Subject</button>
        </form>
    </div>
</div>

<script>
    document.getElementById('create-subject-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData.entries());

        const response = await apiCall(PROJECT_ROOT + 'api/crud.php?entity=subjects&action=create', 'POST', data);

        const alertContainer = document.getElementById('alert-container');
        if (response.success) {
            alertContainer.innerHTML = `<div class="alert alert-success">${response.message}</div>`;
            setTimeout(() => window.location.href = 'list.php', 1500);
        } else {
            alertContainer.innerHTML = `<div class="alert alert-danger">${response.message}</div>`;
        }
    });
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
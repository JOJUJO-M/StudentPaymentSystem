<?php
// modules/teachers/create.php
$page_title = 'Add New Teacher';
include __DIR__ . '/../../includes/header.php';
require_login();

require_once __DIR__ . '/../../config/database.php';
$depts = $pdo->query("SELECT * FROM departments ORDER BY name ASC")->fetchAll();
?>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header">
        <h3>Add New Teacher</h3>
        <a href="list.php" class="btn btn-secondary btn-sm" style="width: auto;">Back to List</a>
    </div>
    <div class="card-body" style="padding: 1.5rem;">
        <div id="alert-container"></div>
        <form id="teacher-form">
            <div class="form-group" style="margin-bottom: 1rem;">
                <label>Full Name</label>
                <input type="text" name="full_name" class="form-control" required placeholder="Enter full name">
            </div>
            <div class="form-group" style="margin-bottom: 1rem;">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required placeholder="Enter email">
            </div>
            <div class="form-group" style="margin-bottom: 1rem;">
                <label>Username</label>
                <input type="text" name="username" class="form-control" required placeholder="Enter username">
            </div>
            <div class="form-group" style="margin-bottom: 1rem;">
                <label>Employee ID</label>
                <input type="text" name="employee_id" class="form-control" required placeholder="e.g. EMP101">
            </div>
            <div class="form-group" style="margin-bottom: 1rem;">
                <label>Department</label>
                <select name="department_id" class="form-control">
                    <option value="">Select Department</option>
                    <?php foreach ($depts as $dept): ?>
                        <option value="<?php echo $dept['id']; ?>"><?php echo $dept['name']; ?></option>
                        <?php
                    endforeach; ?>
                </select>
            </div>
            <div class="form-group" style="margin-bottom: 2rem;">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required placeholder="Default password">
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Create Teacher Account</button>
        </form>
    </div>
</div>

<script>
    document.getElementById('teacher-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData.entries());

        const response = await apiCall(PROJECT_ROOT + 'api/auth.php?action=register_teacher', 'POST', data);

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
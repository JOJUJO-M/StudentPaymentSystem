<?php
// modules/classes/create.php
$page_title = 'Add New Class';
include __DIR__ . '/../../includes/header.php';
require_login();
?>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header">
        <h3>Add New Class</h3>
        <a href="list.php" class="btn btn-secondary btn-sm" style="width: auto;">Back to List</a>
    </div>
    <div class="card-body" style="padding: 1.5rem;">
        <div id="alert-container"></div>
        <form id="class-form">
            <div class="form-group" style="margin-bottom: 1rem;">
                <label>Class Name</label>
                <input type="text" name="name" class="form-control" required placeholder="e.g. Science A">
            </div>
            <div class="form-group" style="margin-bottom: 1rem;">
                <label>Section</label>
                <input type="text" name="section" class="form-control" placeholder="e.g. A">
            </div>
            <div class="form-group" style="margin-bottom: 2rem;">
                <label>Grade Level</label>
                <input type="text" name="grade_level" class="form-control" placeholder="e.g. Grade 10">
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Create Class</button>
        </form>
    </div>
</div>

<script>
    document.getElementById('class-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData.entries());

        const response = await apiCall(PROJECT_ROOT + 'api/crud.php?entity=classes&action=create', 'POST', data);

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
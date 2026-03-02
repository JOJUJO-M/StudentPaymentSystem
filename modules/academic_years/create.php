<?php
// modules/academic_years/create.php
$page_title = 'Add New Academic Year';
include __DIR__ . '/../../includes/header.php';
require_login();
?>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header">
        <h3>New Academic Year</h3>
        <a href="list.php" class="btn btn-secondary btn-sm" style="width: auto;">Back to List</a>
    </div>
    <div class="card-body">
        <div id="alert-container"></div>
        <form id="create-ay-form">
            <div class="form-group mb-3">
                <label for="year_name">Academic Year Name</label>
                <input type="text" id="year_name" name="year_name" class="form-control" required
                    placeholder="e.g. 2026/2027">
            </div>
            <div class="form-group mb-3">
                <label for="start_date">Start Date</label>
                <input type="date" id="start_date" name="start_date" class="form-control" required>
            </div>
            <div class="form-group mb-3">
                <label for="end_date">End Date</label>
                <input type="date" id="end_date" name="end_date" class="form-control" required>
            </div>
            <div class="form-group mb-4">
                <label>
                    <input type="checkbox" name="is_current" value="1"> Is Current Academic Year
                </label>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Create Academic Year</button>
        </form>
    </div>
</div>

<script>
    document.getElementById('create-ay-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData.entries());

        // Handle checkbox
        data.is_current = e.target.is_current.checked ? 1 : 0;
        data.status = 'active';

        const response = await apiCall(PROJECT_ROOT + 'api/crud.php?entity=academic_years&action=create', 'POST', data);

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
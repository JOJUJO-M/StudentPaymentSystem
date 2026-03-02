<?php
// modules/schools/create.php
$page_title = 'Add New School';
include __DIR__ . '/../../includes/header.php';
require_global_admin();
?>

<div class="card">
    <div class="card-header">
        <h3>School Details</h3>
        <a href="list.php" class="btn btn-secondary btn-sm" style="width: auto;">Back to List</a>
    </div>
    <div class="card-body">
        <form id="create-school-form" class="validate-form">
            <div class="form-grid" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
                <div class="form-group">
                    <label for="name">School Name</label>
                    <input type="text" id="name" name="name" class="form-control" required
                        placeholder="Enter school name">
                </div>
                <div class="form-group">
                    <label for="email">School Email</label>
                    <input type="email" id="email" name="email" class="form-control" required
                        placeholder="Enter school email">
                </div>
                <div class="form-group">
                    <label for="phone">School Phone</label>
                    <input type="text" id="phone" name="phone" class="form-control" required
                        placeholder="Enter school phone number">
                </div>
                <div class="form-group">
                    <label for="website">School Website (Optional)</label>
                    <input type="url" id="website" name="website" class="form-control"
                        placeholder="e.g. https://www.school.com">
                </div>
                <div class="form-group" style="grid-column: span 2;">
                    <label for="address">School Address</label>
                    <textarea id="address" name="address" class="form-control" required
                        placeholder="Enter full school address..."></textarea>
                </div>
            </div>
            <div style="margin-top: 2rem;">
                <button type="submit" class="btn btn-primary" style="width: auto;">Create School</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('create-school-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData.entries());

        const response = await apiCall(PROJECT_ROOT + 'api/crud.php?entity=schools&action=create', 'POST', data);

        if (response.success) {
            alert('School created successfully!');
            window.location.href = 'list.php';
        } else {
            alert(response.message);
        }
    });
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
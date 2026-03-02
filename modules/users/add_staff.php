<?php
// modules/users/add_staff.php
$page_title = 'Add Staff Member';
include __DIR__ . '/../../includes/header.php';
require_login();

// Only headmaster or global admin can add staff
if (!has_role(['global_admin', 'headmaster'])) {
    header('Location: ../../dashboard/index.php?error=unauthorized');
    exit();
}

$school_id = $_SESSION['user']['school_id'] ?? null;
?>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header">
        <h3>Create Staff Account</h3>
        <a href="list.php" class="btn btn-secondary btn-sm" style="width: auto;">Back to Users</a>
    </div>
    <div class="card-body">
        <div id="alert-container"></div>
        <form id="add-staff-form" class="validate-form">
            <div class="form-group mb-3">
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" class="form-control" required
                    placeholder="Staff member's full name">
            </div>
            <div class="form-group mb-3">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" required
                    placeholder="staff@school.com">
            </div>
            <div class="form-group mb-3">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" class="form-control" required
                    placeholder="Choose a username">
            </div>
            <div class="form-group mb-4">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" required
                    placeholder="••••••••">
                <small class="text-muted">Staff will use this for their first login.</small>
            </div>

            <input type="hidden" name="school_id" value="<?php echo $school_id; ?>">

            <button type="submit" class="btn btn-primary" style="width: 100%;">Create Staff Member</button>
        </form>
    </div>
</div>

<script>
    document.getElementById('add-staff-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData.entries());

        const response = await apiCall(PROJECT_ROOT + 'api/auth.php?action=register_staff', 'POST', data);

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
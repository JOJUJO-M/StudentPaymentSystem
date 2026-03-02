<?php
// public/login.php
$page_title = 'Academic Portal Login';
include __DIR__ . '/includes/header.php';

if (is_logged_in()) {
    header('Location: ' . $project_root . 'dashboard/index.php');
    exit();
}
?>

<div class="auth-page">
    <div class="auth-card">
        <div class="brand-logo-container">
            <img src="<?php echo $project_root; ?>assets/images/CBE_Logo2.png" alt="CBE Logo" class="brand-logo">
        </div>
        <h1>Portal Login</h1>
        <div id="alert-container"></div>
        <form id="login-form" class="validate-form">
            <div class="form-group">
                <label for="login">Username or Email</label>
                <input type="text" id="login" name="login" class="form-control" required
                    placeholder="Enter username or email">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" required
                    placeholder="Enter password">
            </div>
            <button type="submit" class="btn btn-primary">Sign In</button>
        </form>
        <div class="auth-footer">
            Don't have an account? <a href="<?php echo $project_root; ?>register.php">Register here</a>
        </div>
    </div>
</div>

<script>
    document.getElementById('login-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData.entries());

        const response = await apiCall(PROJECT_ROOT + 'api/auth.php?action=login', 'POST', data);

        const alertContainer = document.getElementById('alert-container');
        if (response.success) {
            alertContainer.innerHTML = `<div class="alert alert-success">${response.message}</div>`;
            setTimeout(() => window.location.href = PROJECT_ROOT + response.redirect, 1000);
        } else {
            alertContainer.innerHTML = `<div class="alert alert-danger">${response.message}</div>`;
        }
    });
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
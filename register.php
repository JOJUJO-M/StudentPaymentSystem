<?php
// register.php
$page_title = 'Register Your School';
include __DIR__ . '/includes/header.php';

if (is_logged_in()) {
    header('Location: ' . get_dashboard_url());
    exit();
}
?>

<div class="auth-page" style="padding-top: 5rem; padding-bottom: 5rem;">
    <div class="auth-card" style="max-width: 800px; width: 90%;">
        <div class="brand-logo-container">
            <img src="<?php echo $project_root; ?>assets/images/CBE_Logo2.png" alt="Logo" class="brand-logo">
        </div>

        <div class="auth-header" style="text-align: center; margin-bottom: 2rem;">
            <h2>Register Your School</h2>
            <p style="color: #6b7280;">Establish your school's online management portal.</p>
        </div>

        <div id="alert-container"></div>

        <!-- School Registration Form -->
        <form id="school-form" class="validate-form">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label for="school_name">School Name</label>
                    <input type="text" id="school_name" name="school_name" class="form-control" required
                        placeholder="e.g. Hope Academy">
                </div>
                <div class="form-group">
                    <label for="reg_number">School Number (Reg ID)</label>
                    <input type="text" id="reg_number" name="reg_number" class="form-control" required
                        placeholder="e.g. S0001">
                </div>
                <div class="form-group" style="grid-column: span 2;">
                    <label for="address">School Address</label>
                    <input type="text" id="address" name="address" class="form-control" required
                        placeholder="Full physical address">
                </div>
                <div class="form-group">
                    <label for="phone">School Phone</label>
                    <input type="text" id="phone" name="phone" class="form-control" required placeholder="+255...">
                </div>
                <div class="form-group">
                    <label for="email">School/Admin Email</label>
                    <input type="email" id="email" name="email" class="form-control" required
                        placeholder="admin@school.com">
                </div>
                <div class="form-group" style="grid-column: span 2;">
                    <label for="logo">School Logo <span style="color:red">*</span></label>
                    <input type="file" id="logo" name="logo" class="form-control" accept="image/*" required
                        onchange="previewLogo(this)">
                    <div id="logo-preview" style="margin-top: 0.5rem; display: none;">
                        <img src="" alt="Preview" style="height: 50px; border-radius: 4px; border: 1px solid #e5e7eb;">
                    </div>
                </div>
            </div>

            <hr style="margin: 2rem 0; border: 0; border-top: 1px solid #e5e7eb;">
            <h3>Admin (Headmaster) Details</h3>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name" class="form-control" required
                        placeholder="Headmaster's full name">
                </div>
                <div class="form-group">
                    <label for="username">Admin Username</label>
                    <input type="text" id="username" name="username" class="form-control" required
                        placeholder="admin123">
                </div>
                <div class="form-group" style="grid-column: span 2;">
                    <label for="password">Admin Password</label>
                    <input type="password" id="password" name="password" class="form-control" required
                        placeholder="••••••••">
                </div>
            </div>

            <button type="submit" class="btn btn-secondary" style="width: 100%; margin-top: 1.5rem;">Register School &
                Admin</button>
        </form>

        <div class="auth-footer" style="margin-top: 2rem; text-align: center;">
            Already managed? <a href="<?php echo $project_root; ?>login.php">Login here</a>
        </div>
    </div>
</div>

<script>
    function previewLogo(input) {
        const preview = document.getElementById('logo-preview');
        const img = preview.querySelector('img');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                img.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // School Registration
    document.getElementById('school-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);

        // Special case for file upload: don't use apiCall which converts to JSON
        try {
            const response = await fetch(PROJECT_ROOT + 'api/auth.php?action=register_headmaster', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            handleResponse(result);
        } catch (error) {
            console.error('Upload Error:', error);
            handleResponse({ success: false, message: 'Network error during upload' });
        }
    });

    function handleResponse(response) {
        const alertContainer = document.getElementById('alert-container');
        if (response.success) {
            alertContainer.innerHTML = `<div class="alert alert-success">${response.message} Redirecting to login...</div>`;
            setTimeout(() => window.location.href = PROJECT_ROOT + 'login.php', 2000);
        } else {
            alertContainer.innerHTML = `<div class="alert alert-danger">${response.message}</div>`;
        }
    }
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
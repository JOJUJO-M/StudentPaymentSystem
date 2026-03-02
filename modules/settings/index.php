<?php
// modules/settings/index.php
$page_title = 'System Settings';
include __DIR__ . '/../../includes/header.php';
require_login();

// Only global admin or headmaster can manage settings
if (!has_role(['global_admin', 'headmaster'])) {
    header('Location: ../../dashboard/index.php?error=unauthorized');
    exit();
}

$school_id = $_SESSION['user']['school_id'] ?? null;
$school = null;

if ($school_id) {
    $stmt = $pdo->prepare("SELECT * FROM schools WHERE id = ?");
    $stmt->execute([$school_id]);
    $school = $stmt->fetch();
}
?>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header">
        <h3>
            <?php echo $school_id ? 'School Profile & Settings' : 'Global System Settings'; ?>
        </h3>
    </div>
    <div class="card-body">
        <div id="alert-container"></div>

        <?php if ($school_id): ?>
            <!-- School Settings Form -->
            <form id="school-settings-form" enctype="multipart/form-data">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div class="form-group">
                        <label>School Name</label>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($school['name']); ?>"
                            class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Registration Number</label>
                        <input type="text" name="reg_number"
                            value="<?php echo htmlspecialchars($school['reg_number'] ?? ''); ?>" class="form-control"
                            required>
                    </div>
                    <div class="form-group" style="grid-column: span 2;">
                        <label>Address</label>
                        <input type="text" name="address" value="<?php echo htmlspecialchars($school['address']); ?>"
                            class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" name="phone" value="<?php echo htmlspecialchars($school['phone']); ?>"
                            class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>School Email</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($school['email']); ?>"
                            class="form-control" required>
                    </div>
                    <div class="form-group" style="grid-column: span 2;">
                        <label>Update Logo</label>
                        <div style="display: flex; gap: 1rem; align-items: center;">
                            <?php if ($school['logo']): ?>
                                <img src="<?php echo $project_root . $school['logo']; ?>" id="current-logo"
                                    style="height: 60px; border-radius: 4px; border: 1px solid #e5e7eb;">
                            <?php endif; ?>
                            <input type="file" name="logo" class="form-control" accept="image/*">
                        </div>
                    </div>
                </div>

                <input type="hidden" name="id" value="<?php echo $school_id; ?>">
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary" style="width: auto;">Update School Info</button>
                </div>
            </form>
        <?php else: ?>
            <!-- Global Admin Settings (Placeholder) -->
            <p>Global settings management coming soon.</p>
        <?php endif; ?>
    </div>
</div>

<script>
    if (document.getElementById('school-settings-form')) {
        document.getElementById('school-settings-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);

            try {
                // Use a dedicated endpoint or handle in crud.php
                const response = await fetch(PROJECT_ROOT + 'api/auth.php?action=update_school', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                const alertContainer = document.getElementById('alert-container');
                if (result.success) {
                    alertContainer.innerHTML = `<div class="alert alert-success">${result.message}</div>`;
                    setTimeout(() => location.reload(), 1500);
                } else {
                    alertContainer.innerHTML = `<div class="alert alert-danger">${result.message}</div>`;
                }
            } catch (error) {
                console.error('Update Error:', error);
            }
        });
    }
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
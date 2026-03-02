<?php
// modules/schools/edit.php
$page_title = 'Edit School';
include __DIR__ . '/../../includes/header.php';
require_global_admin();

$id = $_GET['id'] ?? 0;
if (!$id) {
    header('Location: list.php');
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM schools WHERE id = ?");
$stmt->execute([$id]);
$school = $stmt->fetch();

if (!$school) {
    echo '<div class="alert alert-danger">School not found.</div>';
    include __DIR__ . '/../../includes/footer.php';
    exit();
}
?>

<div class="card">
    <div class="card-header">
        <h3>Edit School:
            <?php echo htmlspecialchars($school['name']); ?>
        </h3>
        <a href="list.php" class="btn btn-secondary btn-sm" style="width: auto;">Back to List</a>
    </div>
    <div class="card-body">
        <form id="edit-school-form" class="validate-form">
            <input type="hidden" name="id" value="<?php echo $school['id']; ?>">

            <div class="form-grid" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
                <div class="form-group">
                    <label for="name">School Name</label>
                    <input type="text" id="name" name="name" class="form-control" required
                        value="<?php echo htmlspecialchars($school['name']); ?>" placeholder="Enter school name">
                </div>
                <div class="form-group">
                    <label for="reg_number">School Number (Reg ID)</label>
                    <input type="text" id="reg_number" name="reg_number" class="form-control" required
                        value="<?php echo htmlspecialchars($school['reg_number']); ?>" placeholder="e.g. S001">
                </div>
                <div class="form-group">
                    <label for="email">School Email</label>
                    <input type="email" id="email" name="email" class="form-control" required
                        value="<?php echo htmlspecialchars($school['email']); ?>" placeholder="Enter school email">
                </div>
                <div class="form-group">
                    <label for="phone">School Phone</label>
                    <input type="text" id="phone" name="phone" class="form-control" required
                        value="<?php echo htmlspecialchars($school['phone']); ?>"
                        placeholder="Enter school phone number">
                </div>
                <div class="form-group">
                    <label for="status">School Status</label>
                    <select id="status" name="status" class="form-control" required>
                        <option value="active" <?php echo $school['status'] == 'active' ? 'selected' : ''; ?>>Active
                            (Portal Open)</option>
                        <option value="inactive" <?php echo $school['status'] == 'inactive' ? 'selected' : ''; ?>
                            >Suspended (Portal Closed)</option>
                    </select>
                </div>
                <div class="form-group" style="grid-column: span 2;">
                    <label for="address">School Address</label>
                    <textarea id="address" name="address" class="form-control" required
                        placeholder="Enter full school address..."><?php echo htmlspecialchars($school['address']); ?></textarea>
                </div>
            </div>
            <div style="margin-top: 2rem; display: flex; gap: 1rem;">
                <button type="submit" class="btn btn-primary" style="width: auto;">Update School Details</button>
                <a href="list.php" class="btn btn-secondary" style="width: auto;">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('edit-school-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData.entries());

        const response = await apiCall(PROJECT_ROOT + 'api/crud.php?entity=schools&action=update', 'POST', data);

        if (response.success) {
            alert('School updated successfully!');
            window.location.href = 'list.php';
        } else {
            alert(response.message);
        }
    });
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
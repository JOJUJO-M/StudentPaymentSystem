<?php
// modules/schools/list.php
$page_title = 'Manage Schools';
include __DIR__ . '/../../includes/header.php';
require_global_admin();

$search = $_GET['search'] ?? '';

if (!empty($search)) {
    $stmt = $pdo->prepare("SELECT * FROM schools WHERE name LIKE ? ORDER BY name ASC");
    $stmt->execute(["%$search%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM schools ORDER BY name ASC");
}
$schools = $stmt->fetchAll();
?>

<div class="card">
    <div class="card-header">
        <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
            <h3>Schools List</h3>
            <form action="" method="GET" style="display: flex; gap: 0.5rem; margin-left: auto;">
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>"
                    placeholder="Search schools..." class="form-control" style="width: 200px; padding: 0.4rem;">
                <button type="submit" class="btn btn-secondary btn-sm" style="width: auto;">Search</button>
            </form>
        </div>
        <a href="create.php" class="btn btn-primary btn-sm" style="width: auto; margin-left: 1rem;"><i
                class="fas fa-plus"></i> Add New School</a>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>School Name</th>
                    <th>Reg ID</th>
                    <th>Address</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($schools as $school): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($school['name']); ?></strong></td>
                        <td><span
                                class="badge bg-secondary"><?php echo htmlspecialchars($school['reg_number'] ?? '-'); ?></span>
                        </td>
                        <td><?php echo htmlspecialchars($school['address'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($school['phone'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($school['email'] ?? '-'); ?></td>
                        <td>
                            <?php $status = $school['status'] ?? 'active'; ?>
                            <span class="status-badge <?php echo $status == 'active' ? 'success' : 'danger'; ?>">
                                <?php echo ucfirst($status); ?>
                            </span>
                        </td>
                        <td><?php echo isset($school['created_at']) ? date('Y-m-d', strtotime($school['created_at'])) : '-'; ?>
                        </td>
                        <td>
                            <a href="<?php echo $project_root; ?>modules/schools/edit.php?id=<?php echo $school['id']; ?>"
                                class="btn btn-secondary btn-sm" title="Edit School"><i class="fas fa-edit"></i></a>

                            <button onclick="toggleStatus(<?php echo $school['id']; ?>, '<?php echo $status; ?>')"
                                class="btn <?php echo $status == 'active' ? 'btn-warning' : 'btn-success'; ?> btn-sm"
                                title="<?php echo $status == 'active' ? 'Suspend' : 'Activate'; ?>">
                                <i class="fas <?php echo $status == 'active' ? 'fa-ban' : 'fa-check-circle'; ?>"></i>
                            </button>

                            <button
                                onclick="deleteSchool(<?php echo $school['id']; ?>, '<?php echo addslashes($school['name']); ?>')"
                                class="btn btn-danger btn-sm" title="Delete School">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php
                endforeach; ?>
                <?php if (empty($schools)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center;">No schools found. Add a school to get started.</td>
                    </tr>
                    <?php
                endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    async function toggleStatus(id, currentStatus) {
        const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
        if (confirm(`Are you sure you want to ${newStatus === 'active' ? 'Activate' : 'Suspend'} this school?`)) {
            const response = await apiCall(PROJECT_ROOT + `api/crud.php?entity=schools&action=update`, 'POST', {
                id: id,
                status: newStatus
            });
            if (response.success) {
                location.reload();
            } else {
                alert(response.message);
            }
        }
    }

    async function deleteSchool(id, name) {
        const confirmText = prompt(`To delete "${name}" and ALL its data, please type 'DELETE' below:`);
        if (confirmText === 'DELETE') {
            const response = await apiCall(PROJECT_ROOT + `api/crud.php?entity=schools&action=delete`, 'POST', { id: id });
            if (response.success) {
                location.reload();
            } else {
                alert(response.message);
            }
        } else if (confirmText !== null) {
            alert('Verification failed. Deletion cancelled.');
        }
    }
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
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
                    <th>Address</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($schools as $school): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($school['name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($school['address']); ?></td>
                        <td><?php echo htmlspecialchars($school['phone']); ?></td>
                        <td><?php echo htmlspecialchars($school['email']); ?></td>
                        <td><?php echo date('Y-m-d', strtotime($school['created_at'])); ?></td>
                        <td>
                            <a href="edit.php?id=<?php echo $school['id']; ?>" class="btn btn-secondary btn-sm"><i
                                    class="fas fa-edit"></i></a>
                            <button onclick="deleteSchool(<?php echo $school['id']; ?>)" class="btn btn-danger btn-sm"><i
                                    class="fas fa-trash"></i></button>
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
    async function deleteSchool(id) {
        if (confirm('Are you sure you want to delete this school? THIS WILL DELETE ALL ASSOCIATED DATA (Students, Teachers, etc)!')) {
            const response = await apiCall(PROJECT_ROOT + `api/crud.php?entity=schools&action=delete&id=${id}`, 'POST');
            if (response.success) {
                location.reload();
            } else {
                alert(response.message);
            }
        }
    }
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
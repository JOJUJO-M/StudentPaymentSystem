<?php
// modules/sample_items/list.php
$page_title = 'Sample Items';
include __DIR__ . '/../../includes/header.php';
require_login();

// Fetch items with search support
require_once __DIR__ . '/../../config/database.php';
$search = $_GET['search'] ?? '';

if (!empty($search)) {
    $stmt = $pdo->prepare("SELECT * FROM sample_items WHERE name LIKE ? OR description LIKE ? ORDER BY created_at DESC");
    $stmt->execute(["%$search%", "%$search%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM sample_items ORDER BY created_at DESC");
}
$items = $stmt->fetchAll();
?>

<div class="card">
    <div class="card-header">
        <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
            <h3>Items List</h3>
            <form action="" method="GET" style="display: flex; gap: 0.5rem; margin-left: auto;">
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>"
                    placeholder="Search items..." class="form-control" style="width: 200px; padding: 0.4rem;">
                <button type="submit" class="btn btn-secondary btn-sm" style="width: auto;">Search</button>
                <?php if ($search): ?>
                    <a href="list.php" class="btn btn-sm" style="background: #e5e7eb; width: auto;">Clear</a>
                <?php endif; ?>
            </form>
        </div>
        <a href="create.php" class="btn btn-primary btn-sm" style="width: auto; margin-left: 1rem;"><i
                class="fas fa-plus"></i> Add Item</a>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Date Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td>
                            <?php echo $item['id']; ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($item['name']); ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars(substr($item['description'] ?? '', 0, 50)); ?>...
                        </td>
                        <td>
                            <?php echo number_format($item['price'], 2); ?>
                        </td>
                        <td>
                            <?php echo date('Y-m-d', strtotime($item['created_at'])); ?>
                        </td>
                        <td>
                            <a href="edit.php?id=<?php echo $item['id']; ?>" class="btn btn-secondary btn-sm"><i
                                    class="fas fa-edit"></i></a>
                            <button onclick="deleteItem(<?php echo $item['id']; ?>)" class="btn btn-danger btn-sm"><i
                                    class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($items)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center;">No items found. Add your first item!</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    async function deleteItem(id) {
        if (confirm('Are you sure you want to delete this item?')) {
            const response = await apiCall(PROJECT_ROOT + `api/crud.php?action=delete&id=${id}`, 'POST');
            if (response.success) {
                location.reload();
            } else {
                alert(response.message);
            }
        }
    }
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
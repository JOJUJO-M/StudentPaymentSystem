<?php
// modules/sample_items/edit.php
$page_title = 'Edit Item';
include __DIR__ . '/../../includes/header.php';
require_login();

require_once __DIR__ . '/../../config/database.php';
$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM sample_items WHERE id = ?");
$stmt->execute([$id]);
$item = $stmt->fetch();

if (!$item) {
    header('Location: list.php?error=notfound');
    exit();
}
?>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header">
        <h3>Edit Item:
            <?php echo htmlspecialchars($item['name']); ?>
        </h3>
        <a href="list.php" class="btn btn-secondary btn-sm" style="width: auto;">Back to List</a>
    </div>
    <div style="padding: 1.5rem;">
        <div id="alert-container"></div>
        <form id="edit-item-form" class="validate-form">
            <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
            <div class="form-group">
                <label for="name">Item Name</label>
                <input type="text" id="name" name="name" class="form-control" required
                    value="<?php echo htmlspecialchars($item['name']); ?>">
            </div>
            <div class="form-group">
                <label for="price">Price</label>
                <input type="number" step="0.01" id="price" name="price" class="form-control" required
                    value="<?php echo $item['price']; ?>">
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" class="form-control"
                    rows="4"><?php echo htmlspecialchars($item['description']); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Update Item</button>
        </form>
    </div>
</div>

<script>
    document.getElementById('edit-item-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData.entries());

        const response = await apiCall('/api/crud.php?action=update', 'POST', data);

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

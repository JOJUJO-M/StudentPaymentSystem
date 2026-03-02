<?php
// public/modules/users/edit.php
$page_title = 'Manage User Roles';
include __DIR__ . '/../../includes/header.php';
require_admin();

require_once __DIR__ . '/../../config/database.php';

$user_id = $_GET['id'] ?? 0;

// Fetch user
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: list.php?error=notfound');
    exit();
}

// Fetch all roles
$roles = $pdo->query("SELECT * FROM roles")->fetchAll();

// Fetch current user roles
$stmt = $pdo->prepare("SELECT role_id FROM user_roles WHERE user_id = ?");
$stmt->execute([$user_id]);
$current_role_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Handle role update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected_roles = $_POST['roles'] ?? [];
    
    try {
        $pdo->beginTransaction();
        
        // Remove existing roles
        $stmt = $pdo->prepare("DELETE FROM user_roles WHERE user_id = ?");
        $stmt->execute([$user_id]);
        
        // Add new roles
        $stmt = $pdo->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)");
        foreach ($selected_roles as $role_id) {
            $stmt->execute([$user_id, $role_id]);
        }
        
        $pdo->commit();
        $success = "User roles updated successfully!";
        // Refresh current roles
        $current_role_ids = $selected_roles;
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Error: " . $e->getMessage();
    }
}
?>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header">
        <h3>Manage Roles for: <?php echo htmlspecialchars($user['username']); ?></h3>
        <a href="list.php" class="btn btn-secondary btn-sm" style="width: auto;">Back to List</a>
    </div>
    <div style="padding: 1.5rem;">
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>User Roles</label>
                <div style="display: flex; flex-direction: column; gap: 0.5rem; margin-top: 0.5rem;">
                    <?php foreach ($roles as $role): ?>
                        <label style="display: flex; align-items: center; gap: 0.5rem; font-weight: normal; cursor: pointer;">
                            <input type="checkbox" name="roles[]" value="<?php echo $role['id']; ?>" 
                                <?php echo in_array($role['id'], $current_role_ids) ? 'checked' : ''; ?>>
                            <?php echo strtoupper($role['name']); ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div style="margin-top: 2rem;">
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>

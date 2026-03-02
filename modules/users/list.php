<?php
// public/modules/users/list.php
$page_title = 'User Management';
include __DIR__ . '/../../includes/header.php';
require_admin(); // Only admins can access this page

require_once __DIR__ . '/../../config/database.php';

$search = $_GET['search'] ?? '';

$user_school_id = $_SESSION['user']['school_id'] ?? null;
$is_super = is_global_admin();

$where_clauses = [];
$params = [];

if (!$is_super && $user_school_id) {
    $where_clauses[] = "u.school_id = ?";
    $params[] = $user_school_id;
}

if (!empty($search)) {
    $where_clauses[] = "(u.username LIKE ? OR u.email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$where_sql = !empty($where_clauses) ? " WHERE " . implode(" AND ", $where_clauses) : "";

$sql = "SELECT u.*, GROUP_CONCAT(r.name) as roles_list 
        FROM users u 
        LEFT JOIN user_roles ur ON u.id = ur.user_id 
        LEFT JOIN roles r ON ur.role_id = r.id 
        $where_sql
        GROUP BY u.id 
        ORDER BY u.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll();
?>

<div class="card">
    <div class="card-header">
        <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
            <h3>Users List</h3>
            <form action="" method="GET" style="display: flex; gap: 0.5rem; margin-left: auto;">
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>"
                    placeholder="Search users..." class="form-control" style="width: 200px; padding: 0.4rem;">
                <button type="submit" class="btn btn-secondary btn-sm" style="width: auto;">Search</button>
                <?php if ($search): ?>
                    <a href="list.php" class="btn btn-sm" style="background: #e5e7eb; width: auto;">Clear</a>
                <?php endif; ?>
                <?php if (has_role(['global_admin', 'headmaster'])): ?>
                    <a href="add_staff.php" class="btn btn-primary btn-sm" style="width: auto; margin-left: 0.5rem;"><i
                            class="fas fa-user-plus"></i> Add Staff</a>
                <?php endif; ?>
            </form>
        </div>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Roles</th>
                    <th>Joined Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td>
                            <?php echo $user['id']; ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($user['username']); ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($user['email']); ?>
                        </td>
                        <td>
                            <?php
                            $roles = explode(',', $user['roles_list'] ?? 'user');
                            foreach ($roles as $role):
                                $class = ($role == 'admin') ? 'bg-primary-light' : 'bg-success-light';
                                ?>
                                <span style="padding: 2px 8px; border-radius: 4px; font-size: 0.75rem; margin-right: 4px;"
                                    class="<?php echo $class; ?>">
                                    <?php echo strtoupper($role); ?>
                                </span>
                            <?php endforeach; ?>
                        </td>
                        <td>
                            <?php echo date('Y-m-d', strtotime($user['created_at'])); ?>
                        </td>
                        <td>
                            <a href="edit.php?id=<?php echo $user['id']; ?>" class="btn btn-secondary btn-sm"><i
                                    class="fas fa-user-edit"></i> Manage Roles</a>
                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                <button onclick="deleteUser(<?php echo $user['id']; ?>)" class="btn btn-danger btn-sm"><i
                                        class="fas fa-trash"></i></button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    async function deleteUser(id) {
        if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
            const response = await apiCall(PROJECT_ROOT + `api/crud.php?entity=users&action=delete&id=${id}`, 'POST');
            if (response.success) {
                location.reload();
            } else {
                alert(response.message);
            }
        }
    }
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
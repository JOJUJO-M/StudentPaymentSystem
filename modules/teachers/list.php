<?php
// modules/teachers/list.php
$page_title = 'Teacher Management';
include __DIR__ . '/../../includes/header.php';
require_login();

require_once __DIR__ . '/../../config/database.php';
$school_id = $_SESSION['user']['school_id'] ?? null;
$search = $_GET['search'] ?? '';

$where = " WHERE 1=1 ";
$params = [];

if ($school_id) {
    $where .= " AND u.school_id = ? ";
    $params[] = $school_id;
}

if (!empty($search)) {
    $where .= " AND (u.full_name LIKE ? OR t.employee_id LIKE ?) ";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$query = "SELECT t.*, u.username, u.full_name, u.email, d.name as dept_name 
          FROM teachers t 
          JOIN users u ON t.user_id = u.id 
          LEFT JOIN departments d ON t.department_id = d.id" . $where . " ORDER BY u.full_name ASC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$teachers = $stmt->fetchAll();
?>

<div class="card">
    <div class="card-header">
        <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
            <h3>Teachers List</h3>
            <form action="" method="GET" style="display: flex; gap: 0.5rem; margin-left: auto;">
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>"
                    placeholder="Search teachers..." class="form-control" style="width: 200px; padding: 0.4rem;">
                <button type="submit" class="btn btn-secondary btn-sm" style="width: auto;">Search</button>
                <?php if ($search): ?>
                    <a href="list.php" class="btn btn-sm" style="background: #e5e7eb; width: auto;">Clear</a>
                    <?php
                endif; ?>
            </form>
        </div>
        <a href="create.php" class="btn btn-primary btn-sm" style="width: auto; margin-left: 1rem;"><i
                class="fas fa-chalkboard-teacher"></i> Add Teacher</a>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Emp ID</th>
                    <th>Full Name</th>
                    <th>Department</th>
                    <th>Email</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($teachers as $teacher): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($teacher['employee_id']); ?></strong></td>
                        <td><?php echo htmlspecialchars($teacher['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($teacher['dept_name'] ?? 'General'); ?></td>
                        <td><?php echo htmlspecialchars($teacher['email']); ?></td>
                        <td><?php echo date('Y-m-d', strtotime($teacher['hire_date'])); ?></td>
                        <td>
                            <a href="edit.php?id=<?php echo $teacher['user_id']; ?>" class="btn btn-secondary btn-sm"><i
                                    class="fas fa-edit"></i></a>
                            <button onclick="deleteTeacher(<?php echo $teacher['user_id']; ?>)"
                                class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                    <?php
                endforeach; ?>
                <?php if (empty($teachers)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center;">No teachers found. Add your first teacher!</td>
                    </tr>
                    <?php
                endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    async function deleteTeacher(id) {
        if (confirm('Are you sure you want to delete this teacher? This will also delete their user account.')) {
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
<?php
// modules/classes/list.php
$page_title = 'Class Management';
include __DIR__ . '/../../includes/header.php';
require_login();

require_once __DIR__ . '/../../config/database.php';
$school_id = $_SESSION['user']['school_id'] ?? null;
if ($school_id) {
    $stmt = $pdo->prepare("SELECT c.*, (SELECT COUNT(*) FROM students WHERE class_id = c.id) as student_count FROM classes c WHERE c.school_id = ? ORDER BY grade_level ASC, name ASC");
    $stmt->execute([$school_id]);
} else {
    $stmt = $pdo->query("SELECT c.*, (SELECT COUNT(*) FROM students WHERE class_id = c.id) as student_count FROM classes c ORDER BY grade_level ASC, name ASC");
}
$classes = $stmt->fetchAll();
?>

<div class="card">
    <div class="card-header">
        <h3>Classes List</h3>
        <a href="create.php" class="btn btn-primary btn-sm" style="width: auto;"><i class="fas fa-plus"></i> Add
            Class</a>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Class Name</th>
                    <th>Section</th>
                    <th>Grade Level</th>
                    <th>Students Count</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($classes as $class): ?>
                    <tr>
                        <td><?php echo $class['id']; ?></td>
                        <td><?php echo htmlspecialchars($class['name']); ?></td>
                        <td><?php echo htmlspecialchars($class['section']); ?></td>
                        <td><?php echo htmlspecialchars($class['grade_level']); ?></td>
                        <td><?php echo $class['student_count']; ?></td>
                        <td>
                            <a href="edit.php?id=<?php echo $class['id']; ?>" class="btn btn-secondary btn-sm"><i
                                    class="fas fa-edit"></i></a>
                            <button onclick="deleteClass(<?php echo $class['id']; ?>)" class="btn btn-danger btn-sm"><i
                                    class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                    <?php
                endforeach; ?>
                <?php if (empty($classes)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center;">No classes found.</td>
                    </tr>
                    <?php
                endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    async function deleteClass(id) {
        if (confirm('Are you sure you want to delete this class?')) {
            const response = await apiCall(PROJECT_ROOT + `api/crud.php?entity=classes&action=delete&id=${id}`, 'POST');
            if (response.success) {
                location.reload();
            } else {
                alert(response.message);
            }
        }
    }
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
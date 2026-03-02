<?php
// modules/subjects/list.php
$page_title = 'Subjects';
include __DIR__ . '/../../includes/header.php';
require_login();

$school_id = $_SESSION['user']['school_id'] ?? null;
$where = " WHERE 1=1 ";
$params = [];

if ($school_id) {
    $where .= " AND s.school_id = ? ";
    $params[] = $school_id;
}

$stmt = $pdo->prepare("SELECT s.*, d.name as dept_name FROM subjects s LEFT JOIN departments d ON s.department_id = d.id $where ORDER BY s.name ASC");
$stmt->execute($params);
$subjects = $stmt->fetchAll();
?>

<div class="card">
    <div class="card-header">
        <h3>Subjects List</h3>
        <button onclick="showCreateModal()" class="btn btn-primary btn-sm" style="width: auto;"><i
                class="fas fa-plus"></i> Add Subject</button>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Subject Name</th>
                    <th>Code</th>
                    <th>Department</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($subjects as $subject): ?>
                    <tr>
                        <td><strong>
                                <?php echo htmlspecialchars($subject['name']); ?>
                            </strong></td>
                        <td>
                            <?php echo htmlspecialchars($subject['code'] ?? '-'); ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($subject['dept_name'] ?? 'General'); ?>
                        </td>
                        <td>
                            <button onclick="deleteItem(<?php echo $subject['id']; ?>)" class="btn btn-danger btn-sm"><i
                                    class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    async function deleteItem(id) {
        if (confirm('Delete this subject?')) {
            const response = await apiCall(PROJECT_ROOT + `api/crud.php?entity=subjects&action=delete&id=${id}`, 'POST');
            if (response.success) location.reload();
            else alert(response.message);
        }
    }
    function showCreateModal() {
        window.location.href = 'create.php';
    }
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
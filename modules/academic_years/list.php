<?php
// modules/academic_years/list.php
$page_title = 'Academic Years';
include __DIR__ . '/../../includes/header.php';
require_login();

$school_id = $_SESSION['user']['school_id'] ?? null;
$where = " WHERE 1=1 ";
$params = [];

if ($school_id) {
    $where .= " AND (school_id = ? OR school_id IS NULL) ";
    $params[] = $school_id;
}

$stmt = $pdo->prepare("SELECT * FROM academic_years $where ORDER BY status DESC, year_name DESC");
$stmt->execute($params);
$years = $stmt->fetchAll();
?>

<div class="card">
    <div class="card-header">
        <h3>Academic Years</h3>
        <button onclick="showCreateModal()" class="btn btn-primary btn-sm" style="width: auto;"><i
                class="fas fa-plus"></i> Add Year</button>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Year Name</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                    <th>Current</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($years as $year): ?>
                    <tr>
                        <td><strong>
                                <?php echo htmlspecialchars($year['year_name']); ?>
                            </strong></td>
                        <td>
                            <?php echo date('Y-m-d', strtotime($year['start_date'])); ?>
                        </td>
                        <td>
                            <?php echo date('Y-m-d', strtotime($year['end_date'])); ?>
                        </td>
                        <td><span class="badge <?php echo $year['status'] == 'active' ? 'bg-success' : 'bg-secondary'; ?>">
                                <?php echo $year['status']; ?>
                            </span></td>
                        <td>
                            <?php echo $year['is_current'] ? '<i class="fas fa-check-circle text-success"></i>' : '-'; ?>
                        </td>
                        <td>
                            <button onclick="deleteItem(<?php echo $year['id']; ?>)" class="btn btn-danger btn-sm"><i
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
        if (confirm('Delete this academic year?')) {
            const response = await apiCall(PROJECT_ROOT + `api/crud.php?entity=academic_years&action=delete&id=${id}`, 'POST');
            if (response.success) location.reload();
            else alert(response.message);
        }
    }
    function showCreateModal() {
        window.location.href = 'create.php';
    }
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
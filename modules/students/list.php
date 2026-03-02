<?php
// modules/students/list.php
$page_title = 'Student Records';
include __DIR__ . '/../../includes/header.php';
require_login();

// Restrict to admins and staff (headmaster, staff, global_admin)
if (!has_role(['global_admin', 'headmaster', 'staff'])) {
    header('Location: ../../dashboard/index.php?error=unauthorized');
    exit();
}

$search = $_GET['search'] ?? '';

// Build query based on user role and search
$school_id = $_SESSION['user']['school_id'] ?? null;
$where = " WHERE 1=1 ";
$params = [];

if ($school_id) {
    $where .= " AND s.school_id = ? ";
    $params[] = $school_id;
}

if (!empty($search)) {
    $where .= " AND (s.student_id LIKE ? OR u.full_name LIKE ? OR u.email LIKE ?) ";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$sql = "SELECT s.*, u.full_name as student_name, u.email as student_email, sch.name as school_name, cl.name as class_name 
        FROM students s 
        LEFT JOIN users u ON s.user_id = u.id 
        LEFT JOIN schools sch ON s.school_id = sch.id 
        LEFT JOIN classes cl ON s.class_id = cl.id 
        $where 
        ORDER BY s.user_id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$students = $stmt->fetchAll();
?>

<div class="card">
    <div class="card-header">
        <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
            <h3>Active Enrolled Students</h3>
            <form action="" method="GET" style="display: flex; gap: 0.5rem; margin-left: auto;">
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>"
                    placeholder="Search by ID, Name, or Email..." class="form-control"
                    style="width: 250px; padding: 0.4rem;">
                <button type="submit" class="btn btn-secondary btn-sm" style="width: auto;">Search Student</button>
            </form>
        </div>
        <a href="create.php" class="btn btn-primary btn-sm" style="width: auto; margin-left: 1rem;"><i
                class="fas fa-plus"></i> Enroll Student</a>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Reg ID</th>
                    <th>Full Name</th>
                    <th>Email Address</th>
                    <th>Current Class</th>
                    <?php if (!$school_id): ?>
                        <th>School Assigned</th>
                        <?php
                    endif; ?>
                    <th>Status</th>
                    <th>Date Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($student['student_id']); ?></strong></td>
                        <td><?php echo htmlspecialchars($student['student_name']); ?></td>
                        <td><?php echo htmlspecialchars($student['student_email']); ?></td>
                        <td><?php echo htmlspecialchars($student['class_name'] ?? 'Unassigned'); ?></td>
                        <?php if (!$school_id): ?>
                            <td><?php echo htmlspecialchars($student['school_name'] ?? '-'); ?></td>
                            <?php
                        endif; ?>
                        <td>
                            <span
                                style="background: <?php echo $student['status'] == 'active' ? '#e6f4ea' : '#fce8e6'; ?>; color: <?php echo $student['status'] == 'active' ? '#1e7e34' : '#d93025'; ?>; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.75rem;">
                                <?php echo strtoupper($student['status']); ?>
                            </span>
                        </td>
                        <td><?php echo date('Y-m-d', strtotime($student['admission_date'])); ?></td>
                        <td>
                            <a href="edit.php?id=<?php echo $student['user_id']; ?>" class="btn btn-secondary btn-sm"><i
                                    class="fas fa-edit"></i></a>
                            <button onclick="deleteStudent(<?php echo $student['user_id']; ?>)"
                                class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                    <?php
                endforeach; ?>
                <?php if (empty($students)): ?>
                    <tr>
                        <td colspan="<?php echo !$school_id ? 8 : 7; ?>" style="text-align: center;">No students recorded
                            yet.</td>
                    </tr>
                    <?php
                endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    async function deleteStudent(id) {
        if (confirm('Are you sure you want to delete this student record? This will also remove their portal access.')) {
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
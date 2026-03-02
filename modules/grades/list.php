<?php
// modules/grades/list.php
$page_title = 'Exam Grades';
include __DIR__ . '/../../includes/header.php';
require_login();

$school_id = $_SESSION['user']['school_id'] ?? null;
$where = $school_id ? " WHERE g.school_id = ? " : " WHERE 1=1 ";
$params = $school_id ? [$school_id] : [];

$sql = "SELECT g.*, u.full_name as student_name, s.name as subject_name 
        FROM grades g 
        JOIN students st ON g.student_id = st.id 
        JOIN users u ON st.user_id = u.id 
        JOIN subjects s ON g.subject_id = s.id 
        $where 
        ORDER BY g.created_at DESC LIMIT 50";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$grades = $stmt->fetchAll();
?>

<div class="card">
    <div class="card-header">
        <h3>Academic Grades</h3>
        <a href="create.php" class="btn btn-primary btn-sm" style="width: auto;"><i class="fas fa-plus"></i> Enter
            Marks</a>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Subject</th>
                    <th>Marks</th>
                    <th>Grade</th>
                    <th>Term</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($grades as $grade): ?>
                    <tr>
                        <td><strong>
                                <?php echo htmlspecialchars($grade['student_name']); ?>
                            </strong></td>
                        <td>
                            <?php echo htmlspecialchars($grade['subject_name']); ?>
                        </td>
                        <td>
                            <?php echo $grade['marks']; ?>
                        </td>
                        <td><span class="badge bg-primary">
                                <?php echo $grade['grade']; ?>
                            </span></td>
                        <td>
                            <?php echo htmlspecialchars($grade['term'] ?? '-'); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
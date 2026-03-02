<?php
// modules/payments/reports.php
$page_title = 'Financial Reports';
include __DIR__ . '/../../includes/header.php';
require_login();

$school_id = $_SESSION['user']['school_id'] ?? null;
$where = $school_id ? " WHERE school_id = ? " : " WHERE 1=1 ";
$params = $school_id ? [$school_id] : [];

$stmt = $pdo->prepare("SELECT payment_method, SUM(amount) as total FROM payments $where GROUP BY payment_method");
$stmt->execute($params);
$report = $stmt->fetchAll();
?>

<div class="card">
    <div class="card-header">
        <h3>Revenue Summary</h3>
    </div>
    <div class="card-body">
        <div class="grid-2">
            <div class="card bg-light p-4">
                <h4>By Payment Method</h4>
                <ul>
                    <?php foreach ($report as $r): ?>
                        <li><strong>
                                <?php echo ucfirst($r['payment_method']); ?>:
                            </strong>
                            <?php echo number_format($r['total'], 2); ?> TZS
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="card bg-light p-4">
                <h4>Monthly Trend</h4>
                <p>Chart integration coming soon!</p>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
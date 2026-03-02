<?php
// modules/expenses/list.php
$page_title = 'School Expenses';
include __DIR__ . '/../../includes/header.php';
require_login();

$school_id = $_SESSION['user']['school_id'] ?? null;
?>

<div class="card">
    <div class="card-header">
        <h3>Expense Records</h3>
        <button class="btn btn-primary btn-sm" style="width: auto;">Add Expense</button>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="4" style="text-align: center;">No expenses recorded for this term.</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
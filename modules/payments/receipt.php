<?php
// modules/payments/receipt.php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';

require_login();

$id = $_GET['id'] ?? 0;

$sql = "SELECT p.*, s.student_id as student_reg_id, u.full_name as student_name, 
        ay.year_name, sch.name as school_name, sch.address as school_address, 
        sch.phone as school_phone, sch.email as school_email, sch.logo as school_logo
        FROM payments p 
        LEFT JOIN students s ON p.student_id = s.id 
        LEFT JOIN users u ON s.user_id = u.id 
        LEFT JOIN academic_years ay ON p.academic_year_id = ay.id 
        LEFT JOIN schools sch ON p.school_id = sch.id 
        WHERE p.id = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$payment = $stmt->fetch();

if (!$payment) {
    die("Payment record not found.");
}

// Security: Check if user belongs to the same school (unless global admin)
if (!is_global_admin() && $payment['school_id'] != $_SESSION['user']['school_id']) {
    die("Unauthorized access.");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Receipt -
        <?php echo $payment['receipt_no']; ?>
    </title>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background: #f9fafb;
        }

        .receipt-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .header {
            display: flex;
            justify-content: space-between;
            border-bottom: 2px solid #f3f4f6;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .school-info h2 {
            margin: 0;
            color: #111827;
        }

        .receipt-label {
            text-align: right;
        }

        .receipt-label h1 {
            margin: 0;
            color: #2563eb;
            font-size: 2rem;
        }

        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }

        .info-box h3 {
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 8px;
            margin-bottom: 12px;
            font-size: 0.875rem;
            text-transform: uppercase;
            color: #6b7280;
            letter-spacing: 0.05em;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }

        .details-table th {
            text-align: left;
            padding: 12px;
            background: #f9fafb;
            border-bottom: 2px solid #e5e7eb;
        }

        .details-table td {
            padding: 12px;
            border-bottom: 1px solid #f3f4f6;
        }

        .total-row {
            font-weight: bold;
            font-size: 1.25rem;
            background: #f3f4f6;
        }

        .footer {
            text-align: center;
            margin-top: 40px;
            color: #9ca3af;
            font-size: 0.875rem;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .receipt-container {
                box-shadow: none;
                max-width: 100%;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>

    <div class="no-print" style="max-width: 800px; margin: 0 auto 20px; text-align: right;">
        <button onclick="window.print()"
            style="padding: 10px 20px; background: #2563eb; color: white; border: none; border-radius: 6px; cursor: pointer;">Print
            Receipt</button>
        <button onclick="window.close()"
            style="padding: 10px 20px; background: #6b7280; color: white; border: none; border-radius: 6px; cursor: pointer; margin-left: 10px;">Close</button>
    </div>

    <div class="receipt-container">
        <div class="header">
            <div class="school-info">
                <h2>
                    <?php echo htmlspecialchars($payment['school_name']); ?>
                </h2>
                <p>
                    <?php echo htmlspecialchars($payment['school_address']); ?><br>
                    Phone:
                    <?php echo htmlspecialchars($payment['school_phone']); ?><br>
                    Email:
                    <?php echo htmlspecialchars($payment['school_email']); ?>
                </p>
            </div>
            <div class="receipt-label">
                <h1>RECEIPT</h1>
                <p><strong>No:</strong>
                    <?php echo htmlspecialchars($payment['receipt_no']); ?>
                </p>
                <p><strong>Date:</strong>
                    <?php echo date('F d, Y', strtotime($payment['payment_date'])); ?>
                </p>
            </div>
        </div>

        <div class="grid">
            <div class="info-box">
                <h3>Student Details</h3>
                <p>
                    <strong>Name:</strong>
                    <?php echo htmlspecialchars($payment['student_name']); ?><br>
                    <strong>Reg ID:</strong>
                    <?php echo htmlspecialchars($payment['student_reg_id']); ?><br>
                    <strong>Academic Year:</strong>
                    <?php echo htmlspecialchars($payment['year_name']); ?>
                </p>
            </div>
            <div class="info-box">
                <h3>Payment Status</h3>
                <p>
                    <strong>Method:</strong>
                    <?php echo ucfirst($payment['payment_method']); ?><br>
                    <strong>Status:</strong> <span style="color: #059669; font-weight: bold;">
                        <?php echo strtoupper($payment['status']); ?>
                    </span>
                </p>
            </div>
        </div>

        <table class="details-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th style="text-align: right;">Amount (TZS)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <?php echo htmlspecialchars($payment['payment_type']); ?> Fee
                    </td>
                    <td style="text-align: right;">
                        <?php echo number_format($payment['amount'], 2); ?>
                    </td>
                </tr>
                <tr class="total-row">
                    <td>TOTAL PAID</td>
                    <td style="text-align: right;">TZS
                        <?php echo number_format($payment['amount'], 2); ?>
                    </td>
                </tr>
            </tbody>
        </table>

        <div style="margin-top: 40px;">
            <p><strong>Remarks:</strong>
                <?php echo htmlspecialchars($payment['remarks'] ?? 'N/A'); ?>
            </p>
        </div>

        <div class="footer">
            <p>This is a computer generated receipt. No signature required.</p>
            <p>&copy;
                <?php echo date('Y'); ?>
                <?php echo htmlspecialchars($payment['school_name']); ?>
            </p>
        </div>
    </div>

</body>

</html>
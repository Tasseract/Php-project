<?php
session_start();
include 'db.php';

// Check if the user is logged in and is a cashier
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username']) || $_SESSION['role'] !== 'cashier') {
    header("Location: login.php"); // Redirect to login
    exit();
}

$username = $_SESSION['username'];

// Fetch pending loan applications for approval
$stmt = $conn->prepare("SELECT * FROM loan_applications WHERE status = 'Pending' ORDER BY id DESC");
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cashier Dashboard</title>
    <style>
        body { margin: 0; font-family: 'Segoe UI', sans-serif; background-color: #f6f9fc; }
        .content { padding: 50px; }
        table { width: 100%; border-collapse: collapse; margin-top: 25px; }
        th, td { padding: 12px; border: 1px solid #ddd; font-size: 14px; }
        th { background-color: #f1f5f9; }
        .button { margin-top: 20px; padding: 10px 20px; background-color: #0d6efd; color: white; border: none; cursor: pointer; border-radius: 6px; text-decoration: none; }
    </style>
</head>
<body>
    <div class="content">
        <h2>Cashier Dashboard</h2>
        <h3>Pending Loan Applications</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User ID</th>
                    <th>Amount</th>
                    <th>Purpose</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['user_id']) ?></td>
                        <td><?= htmlspecialchars($row['amount']) ?></td>
                        <td><?= htmlspecialchars($row['purpose']) ?></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td>
                            <form action="process_loan.php" method="POST" style="display:inline">
                                <input type="hidden" name="loan_id" value="<?= $row['id'] ?>">
                                <button type="submit" name="action" value="approve">Approve</button>
                            </form>
                            <form action="process_loan.php" method="POST" style="display:inline">
                                <input type="hidden" name="loan_id" value="<?= $row['id'] ?>">
                                <button type="submit" name="action" value="deny">Deny</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="6">No pending loan applications.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
        <a href="logout.php" class="button">Logout</a>
    </div>
</body>
</html>
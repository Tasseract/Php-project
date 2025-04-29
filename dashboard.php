<?php
session_start();
include 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$username = $_SESSION['username'];
$role = $_SESSION['role'];

// Handle loan application submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $role != 'officer') {
    $amount = $_POST['amount'] ?? 0;
    $purpose = $_POST['purpose'] ?? '';

    $stmt = $conn->prepare("INSERT INTO loan_applications (user_id, amount, purpose) VALUES (?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("ids", $userId, $amount, $purpose);
        $stmt->execute();
        $stmt->close();
    } else {
        echo "Error: " . $conn->error;
    }
}

// Active tab for navigation
$activeTab = $_GET['tab'] ?? 'apply';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Loan Dashboard</title>
    <style>
        body { margin: 0; font-family: 'Segoe UI', sans-serif; background-color: #f6f9fc; display: flex; height: 100vh; }
        .sidebar { width: 240px; background-color: #1e293b; color: white; display: flex; flex-direction: column; justify-content: space-between; padding: 20px; }
        .sidebar h2 { margin-bottom: 30px; }
        .sidebar a { color: white; text-decoration: none; padding: 12px; border-radius: 6px; display: block; margin-bottom: 10px; transition: background 0.2s ease; }
        .sidebar a:hover, .sidebar a.active { background-color: #334155; }
        .top-bar { background-color: #ffffff; box-shadow: 0 2px 4px rgba(0,0,0,0.05); width: calc(100% - 240px); position: fixed; left: 240px; top: 0; padding: 15px 25px; display: flex; justify-content: space-between; align-items: center; z-index: 10; }
        .content { margin-left: 240px; padding: 100px 30px 30px; overflow-y: auto; width: calc(100% - 240px); }
        .form-box input, .form-box textarea, .form-box button { width: 100%; padding: 12px; margin: 10px 0; border-radius: 6px; border: 1px solid #ccc; font-size: 14px; }
        .form-box button { background-color: #0d6efd; color: white; border: none; cursor: pointer; }
        .form-box button:hover { background-color: #0b5ed7; }
        table { width: 100%; border-collapse: collapse; margin-top: 25px; }
        th, td { padding: 12px; border: 1px solid #ddd; font-size: 14px; }
        th { background-color: #f1f5f9; }
        .footer { font-size: 13px; color: #cbd5e1; text-align: center; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div>
            <h2>Dashboard</h2>
            <a href="?tab=apply" class="<?= $activeTab === 'apply' ? 'active' : '' ?>">Apply Loan</a>
            <a href="?tab=myloans" class="<?= $activeTab === 'myloans' ? 'active' : '' ?>">My Loans</a>
            <?php if ($role === 'officer'): ?>
                <a href="?tab=approve_loans" class="<?= $activeTab === 'approve_loans' ? 'active' : '' ?>">Approve Loans</a>
            <?php endif; ?>
        </div>
        <div class="footer">&copy; 2025 Loan System</div>
    </div>

    <div class="top-bar">
        <div>Welcome, <?= htmlspecialchars($username) ?>!</div>
        <div><a href="logout.php">Logout</a></div>
    </div>

    <div class="content">
        <?php if ($activeTab === 'apply'): ?>
            <h3>Apply for a Loan</h3>
            <form method="POST" class="form-box">
                <input type="number" name="amount" step="0.01" placeholder="Loan Amount" required>
                <textarea name="purpose" placeholder="Purpose of the loan" required></textarea>
                <button type="submit" name="apply">Submit Application</button>
            </form>

        <?php elseif ($activeTab === 'myloans'): ?>
            <h3>My Loan Applications</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Amount</th>
                        <th>Purpose</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                // Fetch user loan applications
                $stmt = $conn->prepare("SELECT * FROM loan_applications WHERE user_id = ? ORDER BY id DESC");
                if ($stmt) {
                    $stmt->bind_param("i", $userId);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    while ($row = $result->fetch_assoc()):
                ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['amount']) ?></td>
                        <td><?= htmlspecialchars($row['purpose']) ?></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td><?= htmlspecialchars($row['created_at']) ?></td>
                        <td>
                            <form action="delete_loan.php" method="POST" style="display:inline">
                                <input type="hidden" name="loan_id" value="<?= $row['id'] ?>">
                                <button type="submit" name="delete" onclick="return confirm('Are you sure you want to delete this loan application?');">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
                    <?php $stmt->close(); ?>
                <?php } else { echo "Error: " . $conn->error; } ?>
                </tbody>
            </table>

        <?php elseif ($activeTab === 'approve_loans' && $role === 'officer'): ?>
            <h3>Approve Loan Applications</h3>
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
                <?php
                // Fetch pending loan applications for approval
                $stmt = $conn->prepare("SELECT * FROM loan_applications WHERE status = 'Pending' ORDER BY id DESC");
                if ($stmt) {
                    $stmt->execute();
                    $result = $stmt->get_result();
                    while ($row = $result->fetch_assoc()):
                ?>
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
                    <?php $stmt->close(); ?>
                <?php } else { echo "Error: " . $conn->error; } ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
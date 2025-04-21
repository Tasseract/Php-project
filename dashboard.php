<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$username = $_SESSION['username'];
$role = $_SESSION['role'];

// Handle new loan application
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $role === 'Loan Applicant') {
    $amount = $_POST['amount'];
    $purpose = $_POST['purpose'];

    $stmt = $conn->prepare("INSERT INTO loan_applications (user_id, amount, purpose) VALUES (?, ?, ?)");
    $stmt->bind_param("ids", $userId, $amount, $purpose);
    $stmt->execute();
}

// Handle approval/denial
if (isset($_GET['action']) && $role === 'Loan Officer') {
    $appId = $_GET['id'];
    $action = $_GET['action'];
    if (in_array($action, ['Approved', 'Denied'])) {
        $stmt = $conn->prepare("UPDATE loan_applications SET status=? WHERE id=?");
        $stmt->bind_param("si", $action, $appId);
        $stmt->execute();
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>
    <p>Role: <?php echo $role; ?></p>
    <a class="button" href="logout.php">Logout</a>

    <?php if ($role === 'Loan Officer'): ?>
        <h3>Loan Applications</h3>
        <table border="1" cellpadding="10" cellspacing="0">
            <tr>
                <th>ID</th>
                <th>Applicant ID</th>
                <th>Amount</th>
                <th>Purpose</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php
            $result = $conn->query("SELECT * FROM loan_applications");
            while ($row = $result->fetch_assoc()):
            ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['user_id'] ?></td>
                    <td><?= $row['amount'] ?></td>
                    <td><?= $row['purpose'] ?></td>
                    <td><?= $row['status'] ?></td>
                    <td>
                        <?php if ($row['status'] === 'Pending'): ?>
                            <a class="button" href="?action=Approved&id=<?= $row['id'] ?>">Approve</a>
                            <a class="button" href="?action=Denied&id=<?= $row['id'] ?>">Deny</a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>

    <?php elseif ($role === 'Loan Applicant'): ?>
        <h3>Apply for a Loan</h3>
        <form method="post" class="form-box">
            <input type="number" name="amount" step="0.01" placeholder="Loan Amount" required>
            <textarea name="purpose" placeholder="Purpose of the loan" required></textarea>
            <button type="submit" name="apply">Submit Application</button>
        </form>

        <h3>Your Applications</h3>
        <table border="1" cellpadding="10" cellspacing="0">
            <tr>
                <th>ID</th>
                <th>Amount</th>
                <th>Purpose</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
            <?php
            $stmt = $conn->prepare("SELECT * FROM loan_applications WHERE user_id = ?");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()):
            ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['amount'] ?></td>
                    <td><?= $row['purpose'] ?></td>
                    <td><?= $row['status'] ?></td>
                    <td><?= $row['created_at'] ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php endif; ?>
</div>
</body>
</html>

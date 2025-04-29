<?php
include 'db.php';
if ($_SESSION['role'] != 'applicant') {
    exit("Access denied");
}
?>
<form method="POST">
    <input type="number" name="amount" required placeholder="Loan Amount">
    <textarea name="purpose" required placeholder="Purpose of Loan"></textarea>
    <button name="apply">Submit</button>
</form>

<?php
if (isset($_POST['apply'])) {
    $stmt = $conn->prepare("INSERT INTO loan_applications (user_id, amount, purpose) VALUES (?, ?, ?)");
    $stmt->bind_param("ids", $_SESSION['user_id'], $_POST['amount'], $_POST['purpose']);
    $stmt->execute();
    echo "Loan submitted!";
}
?>

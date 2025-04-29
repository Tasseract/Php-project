<?php
session_start();
include 'db.php';

if ($_SESSION['role'] != 'officer') {
    exit("Unauthorized");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $loan_id = $_POST['loan_id'] ?? 0;
    $action = $_POST['action'] ?? '';

    $status = $action === 'approve' ? 'Approved' : 'Denied';

    $stmt = $conn->prepare("UPDATE loan_applications SET status = ? WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("si", $status, $loan_id);
        $stmt->execute();
        $stmt->close();
    } else {
        echo "Error: " . $conn->error;
    }

    header("Location: dashboard.php?tab=approve_loans");
    exit();
}
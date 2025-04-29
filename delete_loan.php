<?php
session_start();
include 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    exit("Unauthorized access.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $loan_id = $_POST['loan_id'] ?? 0;
    $user_id = $_SESSION['user_id'];

    // Check if the loan belongs to the logged-in user
    $stmt = $conn->prepare("SELECT user_id FROM loan_applications WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $loan_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            if ($row['user_id'] == $user_id) {
                // Proceed to delete the loan
                $stmt = $conn->prepare("DELETE FROM loan_applications WHERE id = ?");
                if ($stmt) {
                    $stmt->bind_param("i", $loan_id);
                    $stmt->execute();
                    $stmt->close();
                    header("Location: dashboard.php?tab=myloans");
                    exit();
                } else {
                    echo "Error: " . $conn->error;
                }
            } else {
                echo "Unauthorized to delete this loan.";
            }
        } else {
            echo "Loan not found.";
        }
        $stmt->close();
    } else {
        echo "Error: " . $conn->error;
    }
}
<?php
$host = 'localhost';
$db = 'loan_system'; 
$user = 'root'; 
$pass = '';     

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

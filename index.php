<?php
include 'db.php';
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Loan System</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            margin: 0;
            height: 100vh;
            font-family: 'Segoe UI', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f4f4f4;
        }
        .container {
            text-align: center;
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        h1 {
            margin-bottom: 20px;
        }
        a.button {
            display: inline-block;
            padding: 12px 24px;
            margin: 10px;
            border: none;
            background:rgb(1, 170, 66);
            color: white;
            border-radius: 8px;
            text-decoration: none;
            transition: background 0.3s;
        }
        a.button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome to Loan System</h1>
        <a class="button" href="login.php">Login</a>
        <a class="button" href="register.php">Register</a>
    </div>
</body>
</html>

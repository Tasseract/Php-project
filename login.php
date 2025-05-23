<?php session_start(); ?>
<?php include 'db.php'; ?>

<?php
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            height: 100vh;
            font-family: 'Segoe UI', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #eef1f5;
        }
        .form-box {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            text-align: center;
            width: 300px;
        }
        input, button {
            width: 100%;
            margin: 10px 0;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 14px;
        }
        button {
            background-color: #007bff;
            color: white;
            border: none;
        }
        button:hover {
            background-color: #0056b3;
        }
        .link {
            display: block;
            margin-top: 15px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="form-box">
        <h2>Login</h2>
        <form method="POST">
            <input name="username" placeholder="Username" required>
            <input name="password" type="password" placeholder="Password" required>
            <button name="login">Login</button>
        </form>
        <a class="link" href="register.php">Don't have an account? Register</a>
        

        <?php
        if (isset($_POST['login'])) {
            // Prepare and bind
            $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->bind_param("s", $_POST['username']);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            // Check if user exists and verify password
            if ($user && password_verify($_POST['password'], $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                if ($user['role'] === 'officer') {
                    header("Location: cashier_dashboard.php");
                } else if ($user['role'] === 'applicant') {
                    header("Location: dashboard.php");
                } else {
                    echo "<p style='color:red;'>Unauthorized role.</p>";
                }
                exit();
            } else {
                echo "<p style='color:red;'>Invalid username or password</p>";
            }
            $stmt->close();
        }
        ?>
    </div>
</body>
</html>
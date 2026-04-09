<?php
require __DIR__ . '/../src/db.php';
session_start();
include "db.php";

/* If already logged in, redirect */
if (isset($_SESSION['id'])) {
    header("Location: dashboard.php");
    exit;
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Taxi Booking | Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
        }
        .login-box {
            width: 350px;
            margin: 100px auto;
            padding: 25px;
            background: white;
            border-radius: 6px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
        }
        input {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
        }
        button {
            width: 100%;
            padding: 10px;
            margin-top: 15px;
            background: #f9b000;
            border: none;
            font-weight: bold;
            cursor: pointer;
        }
        .msg {
            color: red;
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>

<body>

<div class="login-box">
    <h2>Taxi Booking Login</h2>

    <form method="post" action="">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="login">Login</button>
    </form>

    <p style="text-align:center;">
        New user? <a href="register.html">Create account</a>
    </p>

    <?php
    if (isset($_POST['login'])) {

        $username = $_POST['username'];
        $password = $_POST['password'];

        $conn = OpenCon();

        $stmt = $conn->prepare(
            "SELECT id, username, password_hash FROM users WHERE username = ?"
        );
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {

            $row = $result->fetch_assoc();

            if (password_verify($password, $row['password_hash'])) {

                $_SESSION['id'] = $row['id'];
                $_SESSION['username'] = $row['username'];

                //echo $_SESSION['username'];

                header("Location: dashboard.php");
                exit;
            }
        }

        echo "<div class='msg'>Invalid username or password</div>";

        CloseCon($conn);
    }
    ?>
</div>

</body>
</html>

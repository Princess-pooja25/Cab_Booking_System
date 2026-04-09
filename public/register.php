<?php
require __DIR__ . '/../src/db.php';
include "db.php";

$conn = OpenCon();

/* Check if form is submitted */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    /* Basic validation */
    if ($name == "" || $email == "" || $password == "") {
        header("Location: register.html?error=empty");
        exit;
    }

    /* Hash password */
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    /* Check if email already exists */
    $checkStmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $checkStmt->bind_param("s", $email);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows > 0) {
        header("Location: register.php?error=exists");
        exit;
    }

    /* Insert new user */
    $insertStmt = $conn->prepare(
        "INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)"
    );
    $insertStmt->bind_param("sss", $name, $email, $passwordHash);

    if ($insertStmt->execute()) {
        header("Location: login.php?success=registered");
        exit;
    } else {
        header("Location: register.php?error=failed");
        exit;
    }
}
?>



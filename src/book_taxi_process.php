<?php
session_start();
include "db.php";

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Invalid request");
}

$user_id = $_SESSION['id'];
$pickup = trim($_POST['pickup']);
$drop = trim($_POST['drop']);
$pickup_time = $_POST['pickup_time'];
$cab_type = $_POST['cab_type'];

/* ❌ Pickup & Drop should not be same */
if (strcasecmp($pickup, $drop) === 0) {
    die("Pickup and Drop locations cannot be the same.");
}

/* ❌ Empty fields check */
if (empty($pickup) || empty($drop) || empty($pickup_time) || empty($cab_type)) {
    die("All fields are required.");
}

/* ❌ Past time check */
if (strtotime($pickup_time) < time()) {
    die("Pickup time must be in the future.");
}

/* ❌ Cab validation */
$allowed_cabs = ["Mini", "Sedan", "SUV"];
if (!in_array($cab_type, $allowed_cabs)) {
    die("Invalid cab type.");
}

/* ✅ Open DB */
$conn = OpenCon();

/* ❌ Duplicate booking check */
$checkStmt = $conn->prepare(
    "SELECT id FROM bookings 
     WHERE user_id = ?
       AND pickup_location = ?
       AND drop_location = ?
       AND pickup_time = ?
       AND cab_type = ?"
);

$checkStmt->bind_param(
    "issss",
    $user_id,
    $pickup,
    $drop,
    $pickup_time,
    $cab_type
);

$checkStmt->execute();

if ($checkStmt->get_result()->num_rows > 0) {
    die("This booking already exists.");
}

/* ✅ Insert booking */
$stmt = $conn->prepare(
    "INSERT INTO bookings 
    (user_id, pickup_location, drop_location, pickup_time, cab_type, status)
    VALUES (?, ?, ?, ?, ?, 'Confirmed')"
);

$stmt->bind_param(
    "issss",
    $user_id,
    $pickup,
    $drop,
    $pickup_time,
    $cab_type
);

if ($stmt->execute()) {
    $booking_id = $stmt->insert_id;
    header("Location: assign_driver_and_complete.php?booking_id=" . $booking_id);
    exit;
} else {
    echo "Booking failed. Try again.";
}

$stmt->close();
CloseCon($conn);

<?php
session_start();
include "db.php";

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['booking_id'])) {
    die("Booking ID missing.");
}

$conn = OpenCon();
$booking_id = (int) $_GET['booking_id'];

/* 1️⃣ Get driver_id */
$stmt = $conn->prepare(
    "SELECT driver_id 
     FROM bookings 
     WHERE id = ? AND status = 'Ongoing'"
);
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Invalid booking.");
}

$row = $result->fetch_assoc();
$driver_id = $row['driver_id'];

/* 2️⃣ Mark booking completed */
$conn->query(
    "UPDATE bookings 
     SET status = 'Completed' 
     WHERE id = $booking_id"
);

/* 3️⃣ Free the driver */
$conn->query(
    "UPDATE drivers 
     SET is_available = 1 
     WHERE id = $driver_id"
);

/* 4️⃣ Redirect back */
header("Location: payment.php?booking_id=" . $booking_id);
exit;

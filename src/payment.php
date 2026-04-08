<?php
session_start();
include "db.php";

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}
?>

<?php
if (!isset($_GET['booking_id'])) {
    die("Booking ID missing.");
}

$booking_id = (int) $_GET['booking_id'];
$user_id = $_SESSION['id'];

$conn = OpenCon();

$stmt = $conn->prepare(
    "SELECT b.id, b.fare, b.status, b.payment_status,
            d.name AS driver_name,
            d.phone AS driver_phone
     FROM bookings b
     LEFT JOIN drivers d ON b.driver_id = d.id
     WHERE b.id = ? AND b.user_id = ?"
);

$stmt->bind_param("ii", $booking_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Invalid booking.");
}

$booking = $result->fetch_assoc();

/* 🚫 Block invalid access */
if ($booking['status'] !== 'Completed') {
    die("Ride not completed yet.");
}

if ($booking['payment_status'] === 'Paid') {
    die("Payment already completed.");
}

/* 💳 Handle payment */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    sleep(2); // simulate processing

    $transaction_id = "TXN" . time() . rand(100,999);

    $updateBooking = $conn->prepare(
        "UPDATE bookings 
         SET payment_status = 'Paid',
             paid_at = NOW(),
             transaction_id=?
         WHERE id = ?"
    );
    $updateBooking->bind_param("si", $transaction_id, $booking_id);
    $updateBooking->execute();

    echo "
    <div style='text-align:center;margin-top:100px;font-family:Arial;'>
        <h2 style='color:green;'>✅ Payment Successful</h2>
        <p><strong>Transaction ID:</strong> $transaction_id</p>
        <p><strong>Amount Paid:</strong> ₹" . number_format($booking['fare'],2) . "</p>
        <br>
        <a href='receipt.php?booking_id=$booking_id'
   style='display:inline-block;
          padding:10px 15px;
          background:#007bff;
          color:white;
          text-decoration:none;
          border-radius:5px;'>
   🧾 View Receipt
</a>

    </div>
    ";

    exit;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Payment</title>
    <style>
        body { font-family: Arial; background: #f4f6f8; }
           .top-bar {
    display: flex;
    justify-content: flex-end;
    padding: 15px 25px;
    background: #f8f9fa;
    border-radius: 8px;
}


.logout-btn {
    background-color:none;
    color: red;
    text-decoration: none;
    font-weight: bold;
}
        .box {
            width: 400px;
            margin: 100px auto;
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            text-align: center;
        }
        body {
    font-family: Arial;
    background: #f4f6f8;
    margin: 0;
    padding-top: 60px;
}

        button {
            padding: 12px;
            width: 100%;
            background: #28a745;
            color: white;
            border: none;
            font-weight: bold;
            margin-top: 20px;
        }
     


    </style>
</head>
<body>
<div class="top-bar">
    <span>
 <?= isset($_SESSION['name']) ? htmlspecialchars($_SESSION['name']) : '' ?> 
</span>

    <a href="logout.php" class="logout-btn">Logout</a>
</div>
<div class="box">
    <h2>💳 Payment</h2>
    <p><strong>Fare:</strong> ₹<?= $booking['fare'] ?></p>
    <p><strong>Driver:</strong> <?= htmlspecialchars($booking['driver_name']) ?></p>
<p><strong>Phone:</strong> <?= htmlspecialchars($booking['driver_phone']) ?></p>

    <form method="post">
        <button type="submit">Pay Now</button>
    </form>
</div>

</body>

</html>

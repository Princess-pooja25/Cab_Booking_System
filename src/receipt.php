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

$booking_id = (int) $_GET['booking_id'];
$user_id = $_SESSION['id'];

$conn = OpenCon();

$stmt = $conn->prepare(
    "SELECT b.*, d.name AS driver_name, d.phone AS driver_phone
     FROM bookings b
     LEFT JOIN drivers d ON b.driver_id = d.id
     WHERE b.id = ? AND b.user_id = ?"
);

$stmt->bind_param("ii", $booking_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Receipt not found.");
}

$booking = $result->fetch_assoc();

/* Block if not paid */
if ($booking['payment_status'] !== 'Paid') {
    die("Payment not completed.");
}

/* Generate transaction ID if not stored */
$transaction_id = $booking['transaction_id'];

?>
<!DOCTYPE html>
<html>
<head>
    <title>Receipt</title>
    <style>
        body { font-family: Arial; background:#f4f6f8; }
        .receipt {
            width:500px;
            margin:60px auto;
            background:#fff;
            padding:30px;
            border-radius:8px;
        }
        .header {
            text-align:center;
            margin-bottom:20px;
        }
        .header h2 {
            margin:0;
            color:green;
        }
        hr {
            margin:15px 0;
        }
        .row {
            margin:8px 0;
        }
        .total {
            font-size:18px;
            font-weight:bold;
            color:#000;
        }
        
        .btn {
    display: inline-block;
    padding: 10px 20px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: bold;
    margin-right: 10px;
    transition: 0.3s ease;
}

/* Print Button */
.print-btn {
    background-color: #007bff;
    color: white;
}

.print-btn:hover {
    background-color: #0056b3;
}

/* Book Another Ride */
.new-ride-btn {
    background-color: #28a745;
    color: white;
}

.new-ride-btn:hover {
    background-color: #1e7e34;
}

/* Rate Driver */
.rate-btn {
    background-color: #ffc107;
    color: black;
}

.rate-btn:hover {
    background-color: #e0a800;
}
.button-group {
    display: flex;
    justify-content: center;   /* Center horizontally */
    gap: 15px;                 /* Space between buttons */
    margin-top: 20px;
    flex-wrap: wrap;           /* Responsive on small screens */
}

    </style>
</head>
<body>

<div class="receipt">

    <div class="header">
        <h2>🚖 Taxi Booking Receipt</h2>
        <p>Payment Successful ✅</p>
    </div>

    <hr>

    <div class="row"><strong>Booking ID:</strong> <?= $booking['id'] ?></div>
    <div class="row"><strong>Transaction ID:</strong> <?= $transaction_id ?></div>
    <div class="row"><strong>Date:</strong> <?= date("d M Y H:i", strtotime($booking['paid_at'])) ?></div>

    <hr>

    <div class="row"><strong>Pickup:</strong> <?= htmlspecialchars($booking['pickup_location']) ?></div>
    <div class="row"><strong>Drop:</strong> <?= htmlspecialchars($booking['drop_location']) ?></div>
    <div class="row"><strong>Cab Type:</strong> <?= $booking['cab_type'] ?></div>

    <hr>

    <div class="row"><strong>Driver:</strong> <?= htmlspecialchars($booking['driver_name']) ?></div>
    <div class="row"><strong>Phone:</strong> <?= htmlspecialchars($booking['driver_phone']) ?></div>

    <hr>

    <div class="row total">
        Total Paid: ₹<?= number_format($booking['fare'],2) ?>
    </div>

    <br>

    <div class="button-group">

    <a href="#" onclick="window.print()" class="btn print-btn">
    🖨 Print Receipt
    </a>

    <a href="book_taxi.php" class="btn new-ride-btn">
    🚕 Book Another Ride
   </a>

<a href="rate_driver.php?booking_id=<?php echo $booking_id; ?>" class="btn rate-btn">
    ⭐ Rate Driver
</a>

</div>

</div>

</body>
</html>

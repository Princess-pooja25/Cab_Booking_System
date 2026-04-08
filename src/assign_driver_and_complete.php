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
    die("Booking not found.");
}

$booking = $result->fetch_assoc();

/* AUTO ASSIGN DRIVER + CALCULATE FARE */
if ($booking['status'] === 'Confirmed') {

    // 1️⃣ Find available driver
    $driverStmt = $conn->prepare(
        "SELECT id, name, phone 
         FROM drivers 
         WHERE cab_type = ? AND is_available = 1 
         LIMIT 1"
    );
    $driverStmt->bind_param("s", $booking['cab_type']);
    $driverStmt->execute();
    $driverResult = $driverStmt->get_result();

    if ($driverResult->num_rows === 0) {
        die("No driver available right now.");
    }

    $driver = $driverResult->fetch_assoc();
    $driver_id = $driver['id'];

    // 2️⃣ Fare calculation
    $distance = rand(5, 15); // km

    switch ($booking['cab_type']) {
        case 'Mini':
            $fare = 50 + ($distance * 10);
            break;
        case 'Sedan':
            $fare = 80 + ($distance * 15);
            break;
        case 'SUV':
            $fare = 100 + ($distance * 20);
            break;
        default:
            $fare = 0;
    }

    // 3️⃣ Update booking
    $updateStmt = $conn->prepare(
        "UPDATE bookings 
         SET driver_id = ?, fare = ?, status ='Ongoing' 
         WHERE id = ?"
    );
    $updateStmt->bind_param("idi", $driver_id, $fare, $booking_id);
    $updateStmt->execute();

    // 4️⃣ Mark driver busy
    $conn->query(
        "UPDATE drivers SET is_available = 0 WHERE id = $driver_id"
    );

   header("Location: assign_driver_and_complete.php?booking_id=" . $booking_id);
exit;



}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Booking Status</title>
    <style>
        body { font-family: Arial; background: #f4f6f8; }
        .box {
            width: 450px;
            margin: 80px auto;
            background: #fff;
            padding: 25px;
            border-radius: 8px;
        }
        .status {
            font-weight: bold;
            color: <?= 
    $booking['status'] === 'Completed' ? 'green' :
    ($booking['status'] === 'Ongoing' ? 'blue' : 'orange')
?>;

        }
    </style>
</head>
<body>

<div class="box">
    <h2>🚖 Booking Status</h2>

    <p><strong>Pickup:</strong> <?= htmlspecialchars($booking['pickup_location']) ?></p>
    <p><strong>Drop:</strong> <?= htmlspecialchars($booking['drop_location']) ?></p>
    <p><strong>Cab Type:</strong> <?= $booking['cab_type'] ?></p>

    <p class="status">
        Status: <?= $booking['status'] ?>
    </p>
    <?php if ($booking['status'] === 'Ongoing'): ?>

    <hr>
    <a href="complete_ride.php?booking_id=<?= $booking_id ?>"
       style="display:inline-block;
              padding:10px 15px;
              background:green;
              color:white;
              text-decoration:none;
              border-radius:5px;">
       ✅ Complete Ride
    </a>

    <p>🚕 Driver assigned. Ride in progress...</p>

<?php elseif ($booking['status'] === 'Completed'): ?>

    <p>✅ Ride completed. Proceed to payment.</p>
    

   

<?php else: ?>

    <p>⏳ Booking confirmed. Assigning driver...</p>

<?php endif; ?>

</div>

</body>
</html>

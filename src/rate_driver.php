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

/* Get booking + driver */
$stmt = $conn->prepare("
    SELECT b.driver_id, d.name 
    FROM bookings b
    JOIN drivers d ON b.driver_id = d.id
    WHERE b.id = ? AND b.user_id = ?
");
$stmt->bind_param("ii", $booking_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Invalid booking.");
}

$data = $result->fetch_assoc();
$driver_id = $data['driver_id'];
$driver_name = $data['name'];

/* Prevent duplicate rating */
$check = $conn->prepare("
    SELECT id FROM ratings WHERE booking_id = ?
");
$check->bind_param("i", $booking_id);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    die("You have already rated this ride.");
}

/* Handle POST */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $rating = (int) $_POST['rating'];
    $review = $_POST['review'];

    $insert = $conn->prepare("
        INSERT INTO ratings (booking_id, driver_id, user_id, rating, review)
        VALUES (?, ?, ?, ?, ?)
    ");
    $insert->bind_param("iiiis", $booking_id, $driver_id, $user_id, $rating, $review);
    $insert->execute();
        // Update driver's average rating
    $avg = $conn->prepare("
        UPDATE drivers 
        SET rating = (
            SELECT AVG(rating) FROM ratings WHERE driver_id = ?
        )
        WHERE id = ?
    ");
    $avg->bind_param("ii", $driver_id, $driver_id);
    $avg->execute();


    echo "<h3>⭐ Thank you for rating $driver_name!</h3>";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Rate Driver</title>
    <style>
        body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: #f4f6f8;
}

/* Top bar */
.top-bar {
    display: flex;
    justify-content: flex-end;
    padding: 15px 30px;
    background: white;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.logout-btn {
    color: #dc3545;
    text-decoration: none;
    font-weight: bold;
}

/* Rating card */
.rating-card {
    width: 420px;
    margin: 80px auto;
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
}

.rating-card h2 {
    text-align: center;
    margin-bottom: 20px;
}

select, textarea {
    width: 100%;
    padding: 10px;
    margin-top: 8px;
    margin-bottom: 20px;
    border-radius: 6px;
    border: 1px solid #ccc;
}

textarea {
    resize: none;
    height: 100px;
}

button {
    width: 100%;
    padding: 12px;
    background: #ffc107;
    border: none;
    font-weight: bold;
    border-radius: 6px;
    cursor: pointer;
}

button:hover {
    background: #e0a800;
}

        </style>
</head>
<body>

<div class="top-bar">
    <a href="logout.php" class="logout-btn">Logout</a>
</div>

<div class="rating-card">
    <h2>⭐ Rate Driver: <?= htmlspecialchars($driver_name) ?></h2>

    <form method="post">
        <label>Rating</label>
        <select name="rating" required>
            <option value="">Select</option>
            <option value="5">⭐⭐⭐⭐⭐ (5)</option>
            <option value="4">⭐⭐⭐⭐ (4)</option>
            <option value="3">⭐⭐⭐ (3)</option>
            <option value="2">⭐⭐ (2)</option>
            <option value="1">⭐ (1)</option>
        </select>

        <label>Review</label>
        <textarea name="review" placeholder="Write your feedback..."></textarea>

        <button type="submit">Submit Rating</button>
    </form>
</div>

</body>

</html>

<?php
session_start();
include "db.php";

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

$conn = OpenCon();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard | Taxi Booking</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
        }
        .container {
            width: 900px;
            margin: 40px auto;
        }
        .card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            box-shadow: 0 0 8px rgba(0,0,0,0.1);
        }
        input, select, button {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
        }
        button {
            background: #FFD700;
            border: none;
            font-weight: bold;
            cursor: pointer;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background: #f9b000;
        }
        .logout {
            float: right;
            text-decoration: none;
            color: red;
            font-weight: bold;
        }
    </style>
</head>

<body>

<div class="container">

    <!-- Welcome -->
    <div class="card">
        <a href="logout.php" class="logout">Logout</a>
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?> 🚖</h2>
        <p>Book your taxi, view rides, or manage your profile.</p>
    </div>

    <!-- Book Taxi -->
    <div class="card">
        <h3>🚕 Book a Taxi</h3>

        <form action="book_taxi_process.php" method="post">
            <input type="text" name="pickup" placeholder="Pickup Location" required>
            <input type="text" name="drop" placeholder="Drop Location" required>
            <input type="datetime-local" name="pickup_time" required>

            <select name="cab_type">
                <option value="Mini">Mini</option>
                <option value="Sedan">Sedan</option>
                <option value="SUV">SUV</option>
            </select>

            <button type="submit">Book Now</button>
        </form>
    </div>
    <?php
$stmt = $conn->prepare("
    SELECT pickup_location, pickup_time, cab_type, status
    FROM bookings
    WHERE user_id = ?
    ORDER BY pickup_time DESC
");

$stmt->bind_param("i", $_SESSION['id']);
$stmt->execute();
$result = $stmt->get_result();
?>


   
           
        </table>
    </div> 
          



</body>
</html>

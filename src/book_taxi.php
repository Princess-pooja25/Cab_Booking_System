<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Book Taxi</title>
    <style>
        body { font-family: Arial; background: #f4f6f8; }
        .box {
            width: 400px;
            margin: 80px auto;
            background: #fff;
            padding: 25px;
            border-radius: 8px;
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
        }
    </style>
</head>
<body>

<div class="box">
    <h2>🚖 Book a Taxi</h2>

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

</body>
</html>

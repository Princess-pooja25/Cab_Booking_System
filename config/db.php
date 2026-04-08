<?php
function OpenCon() {
    $conn = new mysqli("localhost", "root", "", "taxi_booking");

    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }

    return $conn;
}

function CloseCon($conn) {
    $conn->close();
}
?>

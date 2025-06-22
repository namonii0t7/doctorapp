<?php
session_start();
if (!isset($_SESSION['doctor_id'])) {
    header("Location: doclog.html");
    exit();
}

$doctor_id = $_SESSION['doctor_id'];
$day = $_POST['day'];
$start_time = $_POST['start_time'];
$end_time = $_POST['end_time'];
$max_patients = $_POST['max_patients'];

$conn = new mysqli("localhost", "root", "", "docappp");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "INSERT INTO schedules (doctor_id, day, start_time, end_time, max_patients)
        VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isssi", $doctor_id, $day, $start_time, $end_time, $max_patients);

if ($stmt->execute()) {
    header("Location: doctor_schedule.php");
    exit();
} else {
    echo "Error: " . $stmt->error;
}
$stmt->close();
$conn->close();

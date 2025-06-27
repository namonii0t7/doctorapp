<?php
session_start();
if (!isset($_SESSION['doctor_id'])) {
    header("Location: doclog.html");
    exit();
}

$doctor_id = $_SESSION['doctor_id'];
$date = $_POST['date'];
$start_time = $_POST['start_time'];
$end_time = $_POST['end_time'];
$max_patients = $_POST['max_patients'];
$appointment_fees = $_POST['appointment_fees'];


$conn = new mysqli("localhost", "root", "", "docappp");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "INSERT INTO schedules (doctor_id, date, start_time, end_time, max_patients, appointment_fees)
        VALUES (?, ?, ?, ?, ?,?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isssii", $doctor_id, $date, $start_time, $end_time, $max_patients, $appointment_fees);

if ($stmt->execute()) {
    header("Location: doctor_schedule.php");
    exit();
} else {
    echo "Error: " . $stmt->error;
}
$stmt->close();
$conn->close();
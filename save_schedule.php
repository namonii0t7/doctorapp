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

// ✅ Validation 1: Prevent selecting past dates
$today = date('Y-m-d');
if ($date < $today) {
    echo "<script>alert('You cannot select a past date!'); window.history.back();</script>";
    exit();
}

// ✅ Validation 2: Ensure end time is after start time
if (strtotime($end_time) <= strtotime($start_time)) {
    echo "<script>alert('End time must be after start time!'); window.history.back();</script>";
    exit();
}

// ✅ Save valid schedule
$sql = "INSERT INTO schedules (doctor_id, date, start_time, end_time, max_patients, appointment_fees)
        VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isssii", $doctor_id, $date, $start_time, $end_time, $max_patients, $appointment_fees);

if ($stmt->execute()) {
    echo "<script>alert('Schedule added successfully!'); window.location.href='doctor_schedule.php';</script>";
} else {
    echo "<script>alert('Error: " . $stmt->error . "'); window.history.back();</script>";
}

$stmt->close();
$conn->close();
?>

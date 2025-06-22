<?php
session_start();
if (!isset($_SESSION['doctor_id'])) {
    header("Location: doclog.html");
    exit();
}

$conn = new mysqli("localhost", "root", "", "docappp");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = $_POST['id'];
$doctor_id = $_SESSION['doctor_id'];

// Make sure doctor is deleting only their own schedule
$stmt = $conn->prepare("DELETE FROM schedules WHERE id = ? AND doctor_id = ?");
$stmt->bind_param("ii", $id, $doctor_id);
$stmt->execute();

header("Location: doctor_schedule.php");
exit();

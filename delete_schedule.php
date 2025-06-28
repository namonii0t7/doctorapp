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

// Soft delete: mark schedule as expired instead of deleting
$stmt = $conn->prepare("UPDATE schedules SET status = 'expired' WHERE id = ? AND doctor_id = ?");
$stmt->bind_param("ii", $id, $doctor_id);
$stmt->execute();

header("Location: doctor_schedule.php");
exit();

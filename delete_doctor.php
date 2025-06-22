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

$doctor_id = $_SESSION['doctor_id'];
$stmt = $conn->prepare("DELETE FROM doctors WHERE id = ?");
$stmt->bind_param("i", $doctor_id);
$stmt->execute();

session_destroy();
echo "<script>alert('Account deleted successfully.'); window.location.href='doclog.html';</script>";
?>

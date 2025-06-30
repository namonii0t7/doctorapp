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
$post_id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM blog_posts WHERE id = ? AND doctor_id = ?");
$stmt->bind_param("ii", $post_id, $doctor_id);
$stmt->execute();

header("Location: doctor_profile.php");
exit();

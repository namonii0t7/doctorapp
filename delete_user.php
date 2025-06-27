<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: logreg.html");
    exit();
}

$conn = new mysqli("localhost", "root", "", "docappp");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = $_SESSION['user_id'];

$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    session_destroy();
    echo "<script>alert('Account deleted successfully.'); window.location.href='logreg.html';</script>";
} else {
    echo "Error deleting account: " . $stmt->error;
}

$conn->close();
?>

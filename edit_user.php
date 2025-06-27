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

$id = $_POST['id'];
$firstname = $_POST['firstname'];
$lastname = $_POST['lastname'];

$stmt = $conn->prepare("UPDATE users SET firstname = ?, lastname = ? WHERE id = ?");
$stmt->bind_param("ssi", $firstname, $lastname, $id);

if ($stmt->execute()) {
    echo "<script>alert('Profile updated successfully.'); window.location.href='user_profile.php';</script>";
} else {
    echo "Error updating profile: " . $stmt->error;
}

$conn->close();
?>

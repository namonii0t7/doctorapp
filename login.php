<?php
session_start(); // 🔐 Start the session

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "docappp";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$email = $_POST['email'];
$pass = $_POST['password'];

// Use prepared statement to prevent SQL injection
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    if ($row['status'] !== 'active') {
        echo "<script>alert('Please verify your email before logging in.'); window.history.back();</script>";
    } elseif (password_verify($pass, $row['password'])) {
        // ✅ Store login session
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['name'] = $row['name'];
        $_SESSION['email'] = $row['email'];

        echo "<script>alert('Login successful! Redirecting to homepage.'); window.location.href = 'user_homepage.html';</script>";
    } else {
        echo "<script>alert('Wrong password.'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('No user found with this email.'); window.history.back();</script>";
}

$conn->close();
?>

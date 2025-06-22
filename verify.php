<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "docappp";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Securely check token
    $stmt = $conn->prepare("SELECT id FROM users WHERE verification_token = ? AND status = 'inactive'");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        // Update status to active
        $update = $conn->prepare("UPDATE users SET status = 'active', verification_token = NULL WHERE verification_token = ?");
        $update->bind_param("s", $token);
        if ($update->execute()) {
            echo "<h2>Email verified successfully. You can now <a href='index.html'>login</a>.</h2>";
        } else {
            echo "Error updating status.";
        }
    } else {
        echo "Invalid or already verified token.";
    }
} else {
    echo "No token provided.";
}

$conn->close();
?>

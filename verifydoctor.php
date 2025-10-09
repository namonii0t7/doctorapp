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

  $stmt = $conn->prepare("SELECT id FROM doctors WHERE verification_token = ? AND status = 'pending'");
  $stmt->bind_param("s", $token);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 1) {
    $update = $conn->prepare("UPDATE doctors SET verification_token = NULL WHERE verification_token = ?");
    $update->bind_param("s", $token);
    if ($update->execute()) {
      echo "<h2>Email verified successfully. Your license will be reviewed. Please wait for approval.</h2>";
    } else {
      echo "Error updating verification.";
    }
  } else {
    echo "Invalid or already verified token.";
  }
} else {
  echo "No token provided.";
}

$conn->close();
?>
''
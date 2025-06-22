 <?php
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

// Sanitize input
$email = trim($_POST['email']);
$pass = $_POST['password'];

if (empty($email) || empty($pass)) {
  echo "<script>alert('Please enter both email and password.'); window.history.back();</script>";
  exit();
}

// Look up doctor by email
$sql = "SELECT * FROM doctors WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
  $row = $result->fetch_assoc();

  if (!password_verify($pass, $row['password'])) {
    echo "<script>alert('Incorrect password.'); window.history.back();</script>";
  } elseif (!empty($row['verification_token'])) {
    echo "<script>alert('Please verify your email first.'); window.history.back();</script>";
  } elseif (strtolower($row['status']) !== 'verified') {
    echo "<script>alert('Your license is still under review. Please wait for admin approval.'); window.history.back();</script>";
  } else {
    // Login success
    session_start();
    $_SESSION['doctor_id'] = $row['id'];
    $_SESSION['doctor_email'] = $row['email'];
    $_SESSION['doctor_name'] = $row['firstname'];
    echo "<script>alert('Login successful!'); window.location.href='doctor_homepage.php';</script>";
  }
} else {
  echo "<script>alert('No doctor found with this email.'); window.history.back();</script>";
}

$conn->close();
?>

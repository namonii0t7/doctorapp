<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
require 'config.php'; // contains EMAIL_USERNAME and EMAIL_PASSWORD

$host = "localhost";
$username = "root";
$password = "";
$dbname = "docappp";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $first = $_POST["first"];
  $last = $_POST["last"];
  $email = $_POST["email"];
  $pass = password_hash($_POST["password"], PASSWORD_DEFAULT);
  $phone = $_POST["phone"];
  $specialization = $_POST["specialization"];
  $address = $_POST["address"];
  $license = $_POST["license"];
  $token = bin2hex(random_bytes(32));

  // Check for duplicate doctor email
  $check = $conn->prepare("SELECT id FROM doctors WHERE email = ?");
  $check->bind_param("s", $email);
  $check->execute();
  $check->store_result();

  if ($check->num_rows > 0) {
    echo "<script>alert('Email already registered. Please log in.'); window.location.href = 'doclog.html';</script>";
    exit();
  }

  // Insert doctor with status = pending
  $stmt = $conn->prepare("INSERT INTO doctors (firstname, lastname, email, password, phone, specialization, chamber, license, verification_token, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
  $stmt->bind_param("sssssssss", $first, $last, $email, $pass, $phone, $specialization, $address, $license, $token);

  if ($stmt->execute()) {
    // Send email verification
    $mail = new PHPMailer(true);
    try {
      $mail->isSMTP();
      $mail->Host = 'smtp.gmail.com';
      $mail->SMTPAuth = true;
      $mail->Username = EMAIL_USERNAME;
      $mail->Password = EMAIL_PASSWORD;
      $mail->SMTPSecure = 'tls';
      $mail->Port = 587;

      $mail->setFrom(EMAIL_USERNAME, 'Doctor App');
      $mail->addAddress($email, $first);
      $mail->isHTML(true);
      $mail->Subject = 'Verify your email';
      $link = "http://localhost/finalyearP/verifydoctor.php?token=$token";
      $mail->Body = "Hi Dr. $first,<br><br>Please click the link below to verify your email:<br><a href='$link'>$link</a><br><br>After email verification, your license will be reviewed for approval.";

      $mail->send();
      echo "<script>alert('Registration successful! Check your email for verification.'); window.location.href = 'doclog.html';</script>";
    } catch (Exception $e) {
      echo "Email sending failed. Error: {$mail->ErrorInfo}";
    }
  } else {
    echo "Error: " . $stmt->error;
  }

  $conn->close();
}
?>

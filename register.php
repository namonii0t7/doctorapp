<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
require 'config.php';

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "docappp";

// DB connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch form data
$firstname = $_POST['firstname'];
$lastname = $_POST['lastname'];
$email = $_POST['email'];
$pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
$token = bin2hex(random_bytes(32));

// Check for duplicate email
$check = $conn->prepare("SELECT id FROM users WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo "<script>alert('Email already registered. Please log in.'); window.location.href = 'index.html';</script>";
    exit();
}

// Insert user
$stmt = $conn->prepare("INSERT INTO users (firstname, lastname, email, password, verification_token, status) VALUES (?, ?, ?, ?, ?, 'inactive')");
$stmt->bind_param("sssss", $firstname, $lastname, $email, $pass, $token);

if ($stmt->execute()) {
    // Send verification email
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = EMAIL_USERNAME;
        $mail->Password   = EMAIL_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;


        $mail->setFrom(EMAIL_USERNAME, 'Doctor App');
        $mail->addAddress($email, $firstname);

        $mail->isHTML(true);
        $mail->Subject = 'Verify your email';
        $link = "http://localhost/finalyearP/verify.php?token=$token";
        $mail->Body    = "Hi $firstname,<br><br>Please click the link below to verify your email:<br><a href='$link'>$link</a>";

        $mail->send();
        echo "<script>alert('Registration successful! Check your email for verification.'); window.location.href = 'logreg.html';</script>";
    } catch (Exception $e) {
        echo "Email sending failed. Error: {$mail->ErrorInfo}";
    }
} else {
    echo "Error: " . $stmt->error;
}

$conn->close();
?>

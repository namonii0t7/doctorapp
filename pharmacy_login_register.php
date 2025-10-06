<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
require 'config.php'; // contains EMAIL_USERNAME and EMAIL_PASSWORD

$conn = new mysqli("localhost","root","","docappp");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$error = '';
$success = '';

// ================== EMAIL VERIFICATION ==================
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $stmt = $conn->prepare("SELECT * FROM pharmacies WHERE verification_token=? AND status='inactive'");
    $stmt->bind_param("s",$token);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows == 1){
        $update = $conn->prepare("UPDATE pharmacies SET status='active', verification_token=NULL WHERE verification_token=?");
        $update->bind_param("s",$token);
        $update->execute();
        echo "<script>alert('Email verified! You can now login.'); window.location.href='pharmacy_login_register.php';</script>";
        exit;
    } else {
        echo "<script>alert('Invalid or already verified token.'); window.location.href='pharmacy_login_register.php';</script>";
        exit;
    }
}

// ================== LOGIN ==================
if(isset($_POST['login'])){
    $email = $_POST['email'];
    $password = $_POST['password'];

    $res = $conn->prepare("SELECT * FROM pharmacies WHERE email=?");
    $res->bind_param("s",$email);
    $res->execute();
    $result = $res->get_result();

    if($result->num_rows==1){
        $pharmacy = $result->fetch_assoc();
        if($pharmacy['status']!='active'){
            $error="Please verify your email before logging in.";
        } elseif(password_verify($password,$pharmacy['password'])){
            $_SESSION['pharmacy_id']=$pharmacy['id'];
            $_SESSION['pharmacy_name']=$pharmacy['name'];
            header("Location: pharmacy_dashboard.php");
            exit;
        } else $error="Invalid password.";
    } else $error="Pharmacy not found.";
}

// ================== REGISTER ==================
if(isset($_POST['register'])){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $phone = $_POST['phone'];
    $license = $_POST['license_no'];
    $location = $_POST['location'];
    $token = bin2hex(random_bytes(32));

    $check = $conn->prepare("SELECT id FROM pharmacies WHERE email=?");
    $check->bind_param("s",$email);
    $check->execute();
    $check->store_result();

    if($check->num_rows>0){
        $error="Email already registered.";
    } else {
        $stmt = $conn->prepare("INSERT INTO pharmacies (name,email,password,phone,license_no,location,verification_token,status) VALUES (?,?,?,?,?,?,?,'inactive')");
        $stmt->bind_param("sssssss",$name,$email,$password,$phone,$license,$location,$token);

        if($stmt->execute()){
            $mail = new PHPMailer(true);
            try{
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = EMAIL_USERNAME;
                $mail->Password = EMAIL_PASSWORD;
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom(EMAIL_USERNAME,'MediConnect');
                $mail->addAddress($email,$name);
                $mail->isHTML(true);
                $mail->Subject = 'Verify your Pharmacy Account';
                $link = "http://localhost/finalyearP/pharmacy_login_register.php?token=$token";
                $mail->Body = "Hi $name,<br><br>Please click the link below to verify your pharmacy account:<br><a href='$link'>$link</a>";

                $mail->send();
                $success="Registration successful! Please check your email to verify your account.";
            } catch (Exception $e){
                $error="Email sending failed: {$mail->ErrorInfo}";
            }
        } else {
            $error="Something went wrong.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pharmacy Login & Registration</title>
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { 
  min-height:100vh; 
  display:flex; 
  flex-direction:column; 
  background:#f8f9fa; 
  font-family:'Poppins',sans-serif;
}
main { 
  flex:1; 
  display:flex; 
  justify-content:center; 
  align-items:center; 
  padding-top:60px;
}
.navbar-custom { background:#000; }
.navbar-custom .navbar-brand, .navbar-custom .nav-link { color:#fff; }
.form-card { background:#000; padding:40px; border-radius:10px; box-shadow:0 5px 15px rgba(0,0,0,0.3); width:100%; max-width:600px; }
.form-card input.form-control { background:#fff; color:#000; border-radius:5px; border:1px solid #ccc; }
.form-card a { color:#fff; text-decoration:underline; }
.form-card p { color:#fff; }
header { font-size:2rem; font-weight:bold; margin-bottom:20px; text-align:center; color:#fff; }
.message { text-align:center; font-weight:600; margin-bottom:10px; }
.success { color:#4CAF50; }
.error { color:#f44336; }
footer { background:#000; color:#fff; text-align:center; padding:15px; }
.btn-black { background:#fff; color:#000; border:1px solid #000; transition:0.3s; }
.btn-black:hover { background:#fff; border-color:#000; }
</style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark navbar-custom fixed-top">
  <div class="container">
    <a class="navbar-brand fw-bold" href="#">MediConnect</a>
  </div>
</nav>

<main>
<!-- Login Form -->
<div class="form-card" id="login-card">
  <header>Pharmacy Login</header>
  <?php if($error) echo "<p class='message error'>$error</p>"; ?>
  <?php if($success) echo "<p class='message success'>$success</p>"; ?>
  <form method="POST">
    <input type="hidden" name="login" value="1">
    <div class="mb-3">
      <input type="text" name="email" class="form-control" placeholder="Email" required>
    </div>
    <div class="mb-3">
      <input type="password" name="password" class="form-control" placeholder="Password" required>
    </div>
    <div class="d-grid mb-3">
      <button type="submit" class="btn btn-black py-3">Sign In</button>
    </div>
    <p class="text-center mt-2">Don't have an account? <a href="#" onclick="toggleForm()">Sign Up</a></p>
  </form>
</div>

<!-- Registration Form -->
<div class="form-card d-none" id="register-card">
  <header>Pharmacy Sign Up</header>
  <form method="POST">
    <input type="hidden" name="register" value="1">
    <div class="mb-3">
      <input type="text" name="name" class="form-control" placeholder="Pharmacy Name" required>
    </div>
    <div class="mb-3">
      <input type="email" name="email" class="form-control" placeholder="Email" required>
    </div>
    <div class="mb-3">
      <input type="password" name="password" class="form-control" placeholder="Password" required>
    </div>
    <div class="mb-3">
      <input type="text" name="phone" class="form-control" placeholder="Phone Number" required>
    </div>
    <div class="mb-3">
      <input type="text" name="license_no" class="form-control" placeholder="Pharmacy License Number" required>
    </div>
    <div class="mb-3">
      <input type="text" name="location" class="form-control" placeholder="Pharmacy Location" required>
    </div>
    <div class="d-grid mb-3">
      <button type="submit" class="btn btn-black py-3">Register</button>
    </div>
    <p class="text-center mt-2">Already have an account? <a href="#" onclick="toggleForm()">Login</a></p>
  </form>
</div>
</main>

<footer>
  <p class="mb-1">&copy; 2025 MediConnect. All rights reserved.</p>
  <p class="mb-0"><a href="about.html">About</a> | <a href="blog.php">Blog</a> | <a href="#">Services</a></p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function toggleForm(){
  document.getElementById('login-card').classList.toggle('d-none');
  document.getElementById('register-card').classList.toggle('d-none');
}
</script>
</body>
</html>

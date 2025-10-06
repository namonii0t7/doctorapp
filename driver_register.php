<?php
session_start();
$conn = new mysqli("localhost","root","","docappp");
$error='';
$success='';

// DRIVER LOGIN
if(isset($_POST['login'])){
    $email=$_POST['email'];
    $password=$_POST['password'];

    $res=$conn->query("SELECT * FROM drivers WHERE email='$email'");
    if($res->num_rows==1){
        $driver=$res->fetch_assoc();
        if(password_verify($password,$driver['password'])){
            $_SESSION['driver_id']=$driver['id'];
            $_SESSION['driver_name']=$driver['name'];
            header("Location: driver_dashboard.php");
            exit;
        } else $error="Invalid password";
    } else $error="Driver not found";
}

// DRIVER REGISTER
if(isset($_POST['register'])){
    $name=$_POST['name'];
    $email=$_POST['email'];
    $password=password_hash($_POST['password'], PASSWORD_DEFAULT);
    $phone=$_POST['phone'];
    $license=$_POST['license_no'];
    $vehicle=$_POST['vehicle_no'];
    $location=$_POST['location'];

    $check=$conn->query("SELECT * FROM drivers WHERE email='$email'");
    if($check->num_rows>0) $error="Email already registered.";
    else{
        $stmt=$conn->prepare("INSERT INTO drivers(name,email,password,phone,license_no,vehicle_no,location) VALUES(?,?,?,?,?,?,?)");
        $stmt->bind_param("sssssss",$name,$email,$password,$phone,$license,$vehicle,$location);
        if($stmt->execute()) $success="Registration successful! You can login now.";
        else $error="Something went wrong.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Driver Login & Registration</title>
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
.navbar-custom {
   background:#000;
  }
.navbar-custom .navbar-brand, .navbar-custom .nav-link, .navbar-custom .btn { 
  color:#fff;
}
.navbar-custom .nav-link:hover {
   color:#ccc;
  }
.btn-black { 
  background:#fff;
  color:#000;
  border:1px solid #000;
   transition:0.3s;
  }
.btn-black:hover {
   background:#fff; 
   border-color:#000;
  }
.form-card { 
  background:#000;
  padding:40px;
  border-radius:10px;
  box-shadow:0 5px 15px rgba(0,0,0,0.3);
  width:100%; 
  max-width:600px;
}
.form-card input.form-control {
   background:#fff;
   color:#000;
   border-radius:5px;
   border:1px solid #ccc;
  }
.form-card input.form-control:focus {
   border-color:#0d6efd;
   box-shadow:0 0 0 0.2rem rgba(13,110,253,.25);
  }
.form-card input::placeholder {
   color:#666;
  }
.form-card a { 
  color:#fff; 
  text-decoration:underline;
}
.form-card p { 
  color:#fff;
}
footer { 
  background:#000;
  color:#fff;
  text-align:center;
  padding:15px;
}
header {
   font-size:2rem;
   font-weight:bold;
   margin-bottom:20px;
   text-align:center;
   color:#fff;
  }
.message {
   text-align:center; 
   font-weight:600; 
   margin-bottom:10px;
  }
.success { 
  color:#4CAF50;
}
.error {
   color:#f44336;
   }
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
  <header>Driver Login</header>
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
  <header>Driver Sign Up</header>
  <form method="POST">
    <input type="hidden" name="register" value="1">
    <div class="mb-3">
      <input type="text" name="name" class="form-control" placeholder="Full Name" required>
    </div>
    <div class="mb-3">
      <input type="email" name="email" class="form-control" placeholder="Email" required>
    </div>
    <div class="mb-3">
      <input type="password" name="password" class="form-control" placeholder="Password" required>
    </div>
    <div class="mb-3">
      <input type="text" name="phone" class="form-control" placeholder="Phone" required>
    </div>
    <div class="mb-3">
      <input type="text" name="license_no" class="form-control" placeholder="License Number" required>
    </div>
    <div class="mb-3">
      <input type="text" name="vehicle_no" class="form-control" placeholder="Vehicle Number" required>
    </div>
    <div class="mb-3">
      <input type="text" name="location" class="form-control" placeholder="Current Location" required>
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
  <p class="mb-0">
    <a href="about.html">About</a> | 
    <a href="blog.php">Blog</a> | 
    <a href="#">Services</a>
  </p>
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

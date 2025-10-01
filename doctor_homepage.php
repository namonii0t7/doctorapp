<?php
session_start();
if (!isset($_SESSION['doctor_id'])) {
    header("Location: doclog.html");
    exit();
}

$conn = new mysqli("localhost", "root", "", "docappp");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$doctor_id = $_SESSION['doctor_id'];
$sql = "SELECT * FROM doctors WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();
$doctor = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Doctor Homepage | MediConnect</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="profile.css" />
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <style>
    body {
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      background-color: #f8f9fa;
    }

    .navbar-custom {
      background-color: #000;
    }

    .navbar-custom .navbar-brand,
    .navbar-custom .nav-link,
    .navbar-custom .btn {
      color: #fff;
    }

    .navbar-custom .nav-link:hover {
      color: #ccc;
    }

    .btn-black {
      background-color: #fff;
      color: #000;
      border: 1px solid #000;
      transition: 0.3s;
    }

    .btn-black:hover {
      background-color: #fff;
      border-color: #000;
    }

    .main-box {
      flex: 1;
      max-width: 900px;
      margin: 100px auto 40px auto;
      background-color: #000;
      padding: 40px;
      border-radius: 15px;
      color: #fff;
      text-align: center;
      box-shadow: 0 8px 25px rgba(0,0,0,0.4);
    }

    .main-box h2 {
      font-size: 2.5rem;
      margin-bottom: 15px;
    }

    .main-box p {
      font-size: 1.2rem;
      margin-bottom: 30px;
    }

    .feature-buttons {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      justify-content: center;
    }

    .feature-box {
      background-color: #fff;
      color: #000;
      width: 180px;
      height: 140px;
      display: flex;
      justify-content: center;
      align-items: center;
      text-align: center;
      border-radius: 10px;
      text-decoration: none;
      box-shadow: 0 5px 15px rgba(0,0,0,0.2);
      transition: transform 0.2s, box-shadow 0.2s;
    }

    .feature-box:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 20px rgba(0,0,0,0.3);
    }

    .feature-box h3 {
      font-size: 1.4rem;
      line-height: 1.3;
    }

    footer {
      background-color: #000;
      color: #fff;
      text-align: center;
      padding: 15px;
      margin-top: auto;
    }

    footer a {
      color: #fff;
      text-decoration: underline;
      margin: 0 5px;
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark navbar-custom fixed-top">
  <div class="container">
    <a class="navbar-brand fw-bold" href="#">MediConnect</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="mainNavbar">
      <ul class="navbar-nav d-flex align-items-center gap-3 me-3">
        <li class="nav-item"><a class="nav-link active" href="index.html">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="blog.php">Blog</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Services</a></li>
        <li class="nav-item"><a class="nav-link" href="about.html">About</a></li>
      </ul>
      <div class="d-flex align-items-center gap-2">
        <button class="btn btn-black" onclick="window.location.href='doctor_profile.php'">Profile</button>
        <button class="btn btn-black" onclick="window.location.href='logout.php'">Logout</button>
      </div>
    </div>
  </div>
</nav>

<!-- Main Box -->
<div class="main-box">
  <div class="welcome">
    <h2>Welcome, Dr. <?= htmlspecialchars($doctor['firstname'] . ' ' . $doctor['lastname']) ?></h2>
    <p>Specialization: <?= htmlspecialchars($doctor['specialization']) ?></p>
  </div>

  <div class="feature-buttons">
    <a href="doctor_schedule.php" class="feature-box">
      <h3>🗓️<br>Set Schedule</h3>
    </a>
    <a href="view_appointment.php" class="feature-box">
      <h3>📅<br>View Appointments</h3>
    </a>
    <a href="patient_records.php" class="feature-box">
      <h3>🧑‍⚕️<br>Patient Records</h3>
    </a>
    <a href="post_blog.php" class="feature-box">
      <h3>✍️<br>Post Your Blog</h3>
    </a>
  </div>
</div>

<footer>
  <p class="mb-1">&copy; 2025 MediConnect. All rights reserved.</p>
  <p class="mb-0">
    <a href="about.html">About</a> | 
    <a href="blog.php">Blog</a> | 
    <a href="#">Services</a>
  </p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

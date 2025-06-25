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
  <title>Doctor Homepage</title>
  <link rel="stylesheet" href="profile.css" />
</head>
<body>

<div class="wrapper">
  <nav class="nav">
    <div class="nav-logo">
      <p>MediConnect .</p>
    </div>
    <div class="nav-menu" id="navMenu">
      <ul>
        <li><a href="doctor_homepage.php" class="link active">Home</a></li>
        <li><a href="#" class="link">Blog</a></li>
        <li><a href="#" class="link">Services</a></li>
        <li><a href="about.html" class="link">About</a></li>
      </ul>
    </div>
    <div class="nav-button">
      <button class="btn white-btn" onclick="window.location.href='doctor_profile.php?action=login'">Profile</button>
      <button class="btn" onclick="window.location.href='logout.php?action=register'">Logout</button>
    </div>
    <div class="nav-menu-btn">
      <i class="bx bx-menu" onclick="myMenuFunction()"></i>
    </div>
  </nav>
</div>

<div class="dashboard-container">
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
  </div>
</div>

<script src="script.js"></script>
</body>
</html>

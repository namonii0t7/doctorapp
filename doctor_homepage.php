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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="profile.css" />
</head>
<body>


<nav class="navbar navbar-expand-lg navbar-dark bg-transparent fixed-top mt-3">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">MediConnect .</a>

    <!-- Burger Button for Mobile -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Right Side Links & Buttons -->
    <div class="collapse navbar-collapse justify-content-end" id="mainNavbar">
      <ul class="navbar-nav d-flex align-items-center gap-3 me-3">
        <li class="nav-item"><a class="nav-link active" href="index.html">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="blog.php">Blog</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Services</a></li>
        <li class="nav-item"><a class="nav-link" href="about.html">About</a></li>
      </ul>

      <div class="d-flex align-items-center gap-2">
        <button class="btn custom-btn btn-signin" onclick="window.location.href='doctor_profile.php'">Profile</button>
        <button class="btn custom-btn btn-signup" onclick="window.location.href='logout.php'">Logout</button>
      </div>
    </div>
  </div>
</nav>


  <!-- Dashboard content -->
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
      <a href="post_blog.php" class="feature-box">
        <h3>✍️<br>Post Your Blog</h3>
      </a>
    </div>
  </div> 


<script src="script.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

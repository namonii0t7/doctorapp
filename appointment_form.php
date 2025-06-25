<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['schedule_id'])) {
    header("Location: user_homepage.php");
    exit();
}

$schedule_id = $_GET['schedule_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Book Appointment</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <nav class="nav">
      <div class="nav-logo">
        <p>MediConnect .</p>
      </div>
      <div class="nav-menu" id="navMenu">
        <ul>
          <li><a href="user_homepage.html" class="link active">Home</a></li>
          <li><a href="#" class="link">Blog</a></li>
          <li><a href="#" class="link">Services</a></li>
          <li><a href="about.html" class="link">About</a></li>
        </ul>
      </div>
      <div class="nav-button">
        <button class="btn white-btn" id="loginBtn" onclick="window.location.href='logout.php?action=login'">Log out</button>
        <button class="btn" id="registerBtn" onclick="window.location.href='user_profile.php?action=register'">Profile</button>
      </div>
      <div class="nav-menu-btn">
        <i class="bx bx-menu" onclick="myMenuFunction()"></i>
      </div>
    </nav>
  <div class="form-box-doctor">
    <form action="book_appointment.php" method="POST" class="doctor-register-container">
      <input type="hidden" name="schedule_id" value="<?= htmlspecialchars($schedule_id) ?>">

      <header>Confirm Your Appointment</header>

      <p style="color:white;text-align:center;">You're booking as <strong><?= htmlspecialchars($_SESSION['name']) ?></strong></p>

      <button type="submit" class="submit">Confirm Booking</button>
    </form>
  </div>
</body>
</html>

<?php
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
  <div class="form-box-doctor">
    <form action="book_appointment.php" method="POST" class="doctor-register-container">
      <input type="hidden" name="schedule_id" value="<?= htmlspecialchars($schedule_id) ?>">

      <header>Book Your Appointment</header>

      <div class="input-box">
        <i class="bx bx-user"></i>
        <input type="text" class="input-field" name="patient_name" placeholder="Your Name" required>
      </div>

      <div class="input-box">
        <i class="bx bx-envelope"></i>
        <input type="email" class="input-field" name="patient_email" placeholder="Your Email" required>
      </div>

      <button type="submit" class="submit">Confirm Booking</button>
    </form>
  </div>
</body>
</html>

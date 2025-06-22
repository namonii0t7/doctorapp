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

// Auto-delete past schedules (based on current date and time)
$today = date('l');
$current_time = date('H:i:s');
$conn->query("DELETE FROM schedules WHERE day = '$today' AND end_time < '$current_time'");

$doctor_id = $_SESSION['doctor_id'];
$schedules = $conn->query("SELECT * FROM schedules WHERE doctor_id = $doctor_id ORDER BY FIELD(day, 'Saturday','Sunday','Monday','Tuesday','Wednesday','Thursday','Friday')");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Doctor Schedule</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <nav class="nav">
            <div class="nav-logo">
                <p>LOGO .</p>
            </div>
            <div class="nav-menu" id="navMenu">
                <ul>
                    <li><a href="#" class="link active">Home</a></li>
                    <li><a href="#" class="link">Blog</a></li>
                    <li><a href="#" class="link">Services</a></li>
                    <li><a href="#" class="link">About</a></li>
                </ul>
            </div>

            <div class="nav-button">
                <button class="btn white-btn" id="loginBtn" onclick="window.location.href='doctor_profile.php?action=login'">Profile</button>
                <button class="btn" id="registerBtn" onclick="window.location.href='index.html?action=register'">Sign Up</button>
            </div>
            <div class="nav-menu-btn">
                <i class="bx bx-menu" onclick="myMenuFunction()"></i>
            </div>
        </nav>
  <div class="wrapper">
    <div class="schedule-box">
      <h2>Set Your Schedule</h2>
      <form action="save_schedule.php" method="POST">
        <label for="day">Day:</label>
        <select name="day" required>
          <option value="">Select Day</option>
          <option value="Saturday">Saturday</option>
          <option value="Sunday">Sunday</option>
          <option value="Monday">Monday</option>
          <option value="Tuesday">Tuesday</option>
          <option value="Wednesday">Wednesday</option>
          <option value="Thursday">Thursday</option>
          <option value="Friday">Friday</option>
        </select>

        <label for="start_time">Start Time:</label>
        <input type="time" name="start_time" required>

        <label for="end_time">End Time:</label>
        <input type="time" name="end_time" required>

        <label for="max_patients">Max Patients:</label>
        <input type="number" name="max_patients" min="1" required>

        <input type="submit" value="Save Schedule">
      </form>

      <h2>Your Schedules</h2>
      <table>
        <tr>
          <th>Day</th>
          <th>Start Time</th>
          <th>End Time</th>
          <th>Max Patients</th>
          <th>Action</th>
        </tr>
        <?php while($row = $schedules->fetch_assoc()): ?>
          <tr>
            <td><?= $row['day'] ?></td>
            <td><?= date("g:i A", strtotime($row['start_time'])) ?></td>
            <td><?= date("g:i A", strtotime($row['end_time'])) ?></td>
            <td><?= $row['max_patients'] ?></td>
            <td>
              <form action="delete_schedule.php" method="POST" style="margin:0;">
                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                <button type="submit" class="delete-btn">Delete</button>
              </form>
            </td>
          </tr>
        <?php endwhile; ?>
      </table>
    </div>
  </div>
</body>
</html>

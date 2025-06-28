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

// Mark past schedules as expired (soft-delete)
$today = date('Y-m-d');
$current_time = date('H:i:s');
$conn->query("UPDATE schedules SET status = 'expired' WHERE date = '$today' AND end_time < '$current_time' AND status = 'active'");

$doctor_id = $_SESSION['doctor_id'];
// Fetch only active schedules
$schedules = $conn->query("SELECT * FROM schedules WHERE doctor_id = $doctor_id AND status = 'active' ORDER BY date");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Doctor Schedule</title>
  <link rel="stylesheet" href="style.css" />
  <style>
    /* Optional: style your delete button */
    .delete-btn {
      background-color: #e74c3c;
      border: none;
      color: white;
      padding: 6px 12px;
      border-radius: 6px;
      cursor: pointer;
      font-weight: bold;
      transition: background-color 0.3s ease;
    }
    .delete-btn:hover {
      background-color: #c0392b;
    }
  </style>
</head>
<body>
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
      <button class="btn white-btn" id="loginBtn" onclick="window.location.href='doctor_profile.php?action=login'">Profile</button>
      <button class="btn" id="registerBtn" onclick="window.location.href='doclog.html?action=register'">Log out</button>
    </div>
    <div class="nav-menu-btn">
      <i class="bx bx-menu" onclick="myMenuFunction()"></i>
    </div>
  </nav>

  <div class="wrapper">
    <div class="schedule-box">
      <h2>Set Your Schedule</h2>
      <form action="save_schedule.php" method="POST">
        <label for="date">Date:</label>
        <input type="date" name="date" required>

        <label for="start_time">Start Time:</label>
        <input type="time" name="start_time" required>

        <label for="end_time">End Time:</label>
        <input type="time" name="end_time" required>

        <label for="max_patients">Max Patients:</label>
        <input type="number" name="max_patients" min="1" required>

        <label for="appointment_fees">Appointment Fees:</label>
        <input type="number" name="appointment_fees" required>

        <input type="submit" value="Save Schedule">
      </form>

      <h2>Your Schedules</h2>
      <table>
        <tr>
          <th>Date</th>
          <th>Start Time</th>
          <th>End Time</th>
          <th>Max Patients</th>
          <th>Appointment Fees</th>
          <th>Action</th>
        </tr>
        <?php while($row = $schedules->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($row['date']) ?></td>
            <td><?= date("g:i A", strtotime($row['start_time'])) ?></td>
            <td><?= date("g:i A", strtotime($row['end_time'])) ?></td>
            <td><?= htmlspecialchars($row['max_patients']) ?></td>
            <td><?= htmlspecialchars($row['appointment_fees']) ?></td>
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

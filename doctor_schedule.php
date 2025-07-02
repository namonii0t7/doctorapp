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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
      <!-- Navbar -->
   <nav class="navbar navbar-expand-lg navbar-dark bg-transparent fixed-top mt-3">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">MediConnect .</a>

    <!-- Burger Button -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Nav links -->
    <div class="collapse navbar-collapse" id="mainNavbar">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link active" href="doctor_homepage.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="blog.php">Blog</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Services</a></li>
        <li class="nav-item"><a class="nav-link" href="about.html">About</a></li>
      </ul>
      <div class="d-flex ms-3 gap-2">
  <button class="btn custom-btn btn-signin" onclick="window.location.href='doctor_profile.php'">Profile</button>
  <button class="btn custom-btn btn-signup" onclick="window.location.href='logout.php'">Log out</button>
</div>

    </div>
  </div>
</nav>>

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
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

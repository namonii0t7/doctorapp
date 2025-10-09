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
    body {
      background-color: #fff;
      color: #000;
      font-family: 'Poppins', sans-serif;
    }

    .wrapper {
      max-width: 1000px;
      margin: 120px auto;
      padding: 20px;
    }

    .schedule-box {
      background: #000;
      padding: 20px;
      border-radius: 10px;
    }

    .schedule-box h2 {
      color: #fff;
      text-align: center;
      margin-bottom: 20px;
    }

    form label {
      color: #fff;
      font-weight: bold;
    }

    form input[type="date"],
    form input[type="time"],
    form input[type="number"],
    form input[type="submit"] {
      width: 100%;
      margin-bottom: 15px;
      padding: 8px;
      border-radius: 5px;
      border: 1px solid #ccc;
    }

    form input[type="submit"] {
      background: #fff;
      color: #000;
      font-weight: bold;
      border: 2px solid #000;
      cursor: pointer;
      transition: 0.3s;
    }

    form input[type="submit"]:hover {
      background: #000;
      color: #fff;
    }

    table {
      width: 100%;
      margin-top: 20px;
      border-collapse: collapse;
      background: #fff;
      color: #000;
      border-radius: 8px;
      overflow: hidden;
    }

    table th, table td {
      padding: 12px;
      border: 1px solid #ddd;
      text-align: center;
    }

    table th {
      background: #f4f4f4;
      font-weight: bold;
    }

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
 <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand fw-bold" href="#">MediConnect</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="mainNavbar">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link active" href="doctor_homepage.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="blog.php">Blog</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Services</a></li>
        <li class="nav-item"><a class="nav-link" href="about.html">About</a></li>
      </ul>
    </div>
  </div>
</nav>

  <div class="wrapper">
    <div class="schedule-box">
      <h2>Set Your Schedule</h2>
      <form action="save_schedule.php" method="POST">
        <label for="date">Date:</label>
        <!-- Restrict past date selection -->
        <input type="date" name="date" required min="<?= date('Y-m-d'); ?>">

        <label for="start_time">Start Time:</label>
        <input type="time" name="start_time" required>

        <label for="end_time">End Time:</label>
        <input type="time" name="end_time" required>

        <label for="max_patients">Max Patients:</label>
        <input type="number" name="max_patients" min="1" required>

        <label for="appointment_fees">Appointment Fees:</label>
        <input type="number" name="appointment_fees" min="0" required>

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

  <footer class="bg-dark text-white text-center py-3">
  <div class="container">
    <p class="mb-1">&copy; 2025 MediConnect. All rights reserved.</p>
    <p class="mb-0">
      <a href="about.html" class="text-white text-decoration-underline">About</a> | 
      <a href="blog.php" class="text-white text-decoration-underline">Blog</a> | 
      <a href="#" class="text-white text-decoration-underline">Services</a>
    </p>
  </div>
</footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

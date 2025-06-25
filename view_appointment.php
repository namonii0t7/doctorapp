<?php
session_start();
if (!isset($_SESSION['doctor_id'])) {
    header("Location: doclog.html");
    exit();
}

$doctor_id = $_SESSION['doctor_id'];

$conn = new mysqli("localhost", "root", "", "docappp");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT 
            u.firstname AS patient_firstname,
            u.lastname AS patient_lastname,
            s.date,
            s.start_time,
            s.end_time
        FROM appointments a
        JOIN users u ON a.user_id = u.id
        JOIN schedules s ON a.schedule_id = s.id
        WHERE s.doctor_id = ?
        ORDER BY s.date, s.start_time";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>View Appointments</title>
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <div class="wrapper">
    <!-- Navigation Bar -->
    <nav class="nav">
      <div class="nav-logo">
        <p>MediConnect .</p>
      </div>
      <div class="nav-menu" id="navMenu">
        <ul>
          <li><a href="doctor_homepage.php" class="link">Home</a></li>
          <li><a href="#" class="link">Blog</a></li>
          <li><a href="#" class="link">Services</a></li>
          <li><a href="#" class="link">About</a></li>
        </ul>
      </div>
      <div class="nav-button">
        <button class="btn white-btn" onclick="window.location.href='logout.php'">Logout</button>
      </div>
      <div class="nav-menu-btn">
        <i class="bx bx-menu" onclick="myMenuFunction()"></i>
      </div>
    </nav>

    <!-- Appointment Table Section -->
    <div class="schedule-box">
      <h2>Your Appointments</h2>

      <?php if ($result->num_rows > 0): ?>
        <table>
          <tr>
            <th>Patient Name</th>
            <th>Date</th>
            <th>Start Time</th>
            <th>End Time</th>
          </tr>
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?php echo htmlspecialchars($row['patient_firstname'] . ' ' . $row['patient_lastname']); ?></td>
              <td><?php echo htmlspecialchars($row['date']); ?></td>
              <td><?php echo htmlspecialchars($row['start_time']); ?></td>
              <td><?php echo htmlspecialchars($row['end_time']); ?></td>
            </tr>
          <?php endwhile; ?>
        </table>
      <?php else: ?>
        <p style="text-align:center; color:white;">You have no appointments.</p>
      <?php endif; ?>

    </div>
  </div>

  <script src="script.js"></script>
</body>
</html>

<?php
$conn->close();
?>

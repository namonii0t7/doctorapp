<?php
$conn = new mysqli("localhost", "root", "", "docappp");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT s.id AS schedule_id, s.date, s.start_time, s.end_time, s.max_patients, s.appointment_fees,
               d.id AS doctor_id, d.firstname, d.lastname, d.specialization, d.chamber
        FROM schedules s
        JOIN doctors d ON s.doctor_id = d.id
        WHERE d.status = 'verified' AND s.status = 'active'
        ORDER BY s.date, s.start_time";

$schedules = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="style.css" />
  <title>User Homepage</title>
</head>
<body>
  <nav class="nav">
    <div class="nav-logo">
      <p>MediConnect .</p>
    </div>
    <div class="nav-menu" id="navMenu">
      <ul>
        <li><a href="user_homepage.html" class="link active">Home</a></li>
        <li><a href="#" class="link">Services</a></li>
        <li><a href="about.html" class="link">About Us</a></li>
      </ul>
    </div>
    <div class="search-bar">
      <input type="text" placeholder="Search by location..." id="searchInput" />
      <button type="button" onclick="search()">Search</button>
    </div>
    <div class="nav-button">
      <button class="btn white-btn" onclick="window.location.href='logout.php'">Log out</button>
      <button class="btn" onclick="window.location.href='user_profile.php'">Profile</button>
    </div>
    <div class="nav-menu-btn">
      <i class="bx bx-menu" onclick="myMenuFunction()"></i>
    </div>
  </nav>

  <div class="schedule-box">
    <h2>Available Doctor Schedules</h2>
    <table id="scheduleTable">
      <tr>
        <th>Doctor</th>
        <th>Specialization</th>
        <th>Location</th>
        <th>Date</th>
        <th>Time</th>
        <th>Fees</th>
        <th>Book Appointment</th>
        <th>Visit Profile</th>
      </tr>
      <?php while ($row = $schedules->fetch_assoc()): ?>
        <tr>
          <td class="doctor-name"><?= htmlspecialchars($row['firstname'] . ' ' . $row['lastname']) ?></td>
          <td class="specialization"><?= htmlspecialchars($row['specialization']) ?></td>
          <td class="location"><?= htmlspecialchars($row['chamber']) ?></td>
          <td><?= htmlspecialchars($row['date']) ?></td>
          <td><?= date("g:i A", strtotime($row['start_time'])) ?> - <?= date("g:i A", strtotime($row['end_time'])) ?></td>
          <td>৳<?= htmlspecialchars(number_format($row['appointment_fees'], 2)) ?></td>
          <td>
            <form action="appointment_form.php" method="GET">
              <input type="hidden" name="schedule_id" value="<?= $row['schedule_id'] ?>">
              <button type="submit" class="submit">Go to</button>
            </form>
          </td>
          <td>
            <form action="visit_doctor_profile.php" method="GET">
              <input type="hidden" name="id" value="<?= $row['doctor_id'] ?>">
              <button type="submit" class="submit">View</button>
            </form>
          </td>
        </tr>
      <?php endwhile; ?>
    </table>
  </div>

  <script>
  document.getElementById('searchInput').addEventListener('input', search);

  function search() {
    const input = document.getElementById('searchInput').value.toLowerCase().trim();
    const rows = document.querySelectorAll('#scheduleTable tr');

    for (let i = 1; i < rows.length; i++) {
      const doctorName = rows[i].querySelector('.doctor-name')?.textContent.toLowerCase().trim() || '';
      const specialization = rows[i].querySelector('.specialization')?.textContent.toLowerCase().trim() || '';
      const location = rows[i].querySelector('.location')?.textContent.toLowerCase().trim() || '';

      if (
        doctorName.includes(input) ||
        specialization.includes(input) ||
        location.includes(input)
      ) {
        rows[i].style.display = '';
      } else {
        rows[i].style.display = 'none';
      }
    }
  }
  </script>
</body>
</html>

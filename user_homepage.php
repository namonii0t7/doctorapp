<?php
$conn = new mysqli("localhost", "root", "", "docappp");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch schedules only for approved doctors
$sql = "SELECT s.id AS schedule_id, s.day, s.start_time, s.end_time, s.max_patients,
               d.firstname, d.lastname, d.specialization, d.chamber
        FROM schedules s
        JOIN doctors d ON s.doctor_id = d.id
        WHERE d.status = 'verified'
        ORDER BY FIELD(s.day, 'Saturday','Sunday','Monday','Tuesday','Wednesday','Thursday','Friday')";

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

      <!-- Search Bar -->
      <div class="search-bar">
        <input type="text" placeholder="Search by location..." id="searchInput" />
        <button type="button" onclick="search()">Search</button>
      </div>

      <!-- Auth Buttons -->
      <div class="nav-button">
        <button class="btn white-btn" id="loginBtn" onclick="window.location.href='login.html'">Login</button>
        <button class="btn" id="registerBtn" onclick="window.location.href='register.html'">Sign Up</button>
      </div>
      <div class="nav-menu-btn">
        <i class="bx bx-menu" onclick="myMenuFunction()"></i>
      </div>
    </nav>


  <!-- Doctor Schedules -->
  <div class="schedule-box">
    <h2>Available Doctor Schedules</h2>
    <table id="scheduleTable">
      <tr>
        <th>Doctor</th>
        <th>Specialization</th>
        <th>Location</th>
        <th>Day</th>
        <th>Time</th>
        <th>Action</th>
      </tr>
      <?php while ($row = $schedules->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['firstname'] . ' ' . $row['lastname']) ?></td>
          <td><?= htmlspecialchars($row['specialization']) ?></td>
          <td class="location"><?= htmlspecialchars($row['chamber']) ?></td>
          <td><?= $row['day'] ?></td>
          <td><?= date("g:i A", strtotime($row['start_time'])) ?> - <?= date("g:i A", strtotime($row['end_time'])) ?></td>
          <td>
            <form action="book_appointment.php" method="POST">
              <input type="hidden" name="schedule_id" value="<?= $row['schedule_id'] ?>">
              <button type="submit" class="submit">Book</button>
            </form>
          </td>
        </tr>
      <?php endwhile; ?>
    </table>
  </div>

  <!-- Search Script -->
  <script>
    function search() {
      const input = document.getElementById('searchInput').value.toLowerCase();
      const rows = document.querySelectorAll('#scheduleTable tr');

      for (let i = 1; i < rows.length; i++) {
        const location = rows[i].querySelector('.location').textContent.toLowerCase();
        if (location.includes(input)) {
          rows[i].style.display = '';
        } else {
          rows[i].style.display = 'none';
        }
      }
    }
  </script>
</body>
</html>

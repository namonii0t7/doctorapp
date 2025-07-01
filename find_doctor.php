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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css" />
  <title>User Homepage</title>
  <style>
    body {
      background: url("images/doc2.jpg");
      background-size: cover;
      background-repeat: no-repeat;
      background-attachment: fixed;
      font-family: 'Poppins', sans-serif;
    }

    .custom-btn {
      border-radius: 30px;
      padding: 8px 20px;
      font-weight: 500;
      transition: 0.3s ease;
      font-family: 'Poppins', sans-serif;
    }

    .btn-signin {
      background: rgba(255, 255, 255, 0.2);
      color: #fff;
      border: none;
      backdrop-filter: blur(10px);
    }

    .btn-signin:hover {
      background: rgba(255, 255, 255, 0.3);
    }

    .btn-signup {
      background: rgba(255, 255, 255, 0.4);
      color: #000;
      border: none;
      backdrop-filter: blur(10px);
    }

    .btn-signup:hover {
      background: rgba(255, 255, 255, 0.6);
    }

    .schedule-box {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(8px);
      margin: 140px auto 60px auto;
      padding: 30px;
      max-width: 1200px;
      border-radius: 20px;
      box-shadow: 0 0 20px rgba(0,0,0,0.2);
    }

    .schedule-box h2 {
      color: white;
      text-align: center;
      margin-bottom: 30px;
      font-weight: 600;
    }

    table {
      width: 100%;
      background-color: rgba(255, 255, 255, 0.05);
      color: white;
      border-radius: 10px;
      overflow: hidden;
    }

    th, td {
      padding: 12px 15px;
      text-align: center;
      font-size: 15px;
    }

    th {
      background-color: rgba(255, 255, 255, 0.2);
    }

    tr:nth-child(even) {
      background-color: rgba(255, 255, 255, 0.05);
    }

    .submit {
      background-color: #ffd369;
      border: none;
      padding: 6px 12px;
      border-radius: 8px;
      font-weight: 600;
      cursor: pointer;
    }

    .submit:hover {
      background-color: #ffcc55;
    }

    .form-control::placeholder {
      font-size: 14px;
    }
  </style>
</head>
<body>

<!-- Bootstrap Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-transparent fixed-top px-4">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="#">MediConnect<span class="text-primary"> .</span></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="mainNavbar">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link active" href="user_homepage.html">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="blog.php">Blog</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Services</a></li>
        <li class="nav-item"><a class="nav-link" href="about.html">About Us</a></li>
      </ul>

      <!-- Search -->
      <form class="d-flex me-3" role="search">
        <input class="form-control rounded-pill" type="search" placeholder="Search by location..." id="searchInput">
      </form>

      <!-- Buttons -->
      <div class="d-flex gap-2">
        <button class="btn custom-btn btn-signin" onclick="window.location.href='logout.php'">Log out</button>
        <button class="btn custom-btn btn-signup" onclick="window.location.href='user_profile.php'">Profile</button>
      </div>
    </div>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn = new mysqli("localhost", "root", "", "docappp");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT s.id AS schedule_id, s.date, s.start_time, s.end_time, s.max_patients, s.appointment_fees,
               d.id AS doctor_id, d.firstname, d.lastname, d.specialization, d.chamber
        FROM schedules s
        JOIN doctors d ON s.doctor_id = d.id
        WHERE d.status = 'verified' 
          AND s.status = 'active' 
          AND s.date >= CURDATE()
        ORDER BY s.date, s.start_time";


$schedules = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>User Homepage | MediConnect</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="style.css" />
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f8f9fa;
      min-height: 100vh;
    }

    /* Navbar */
    .navbar-custom {
      background-color: #000;
    }
    .navbar-custom .navbar-brand,
    .navbar-custom .nav-link,
    .navbar-custom .btn {
      color: #fff;
    }
    .navbar-custom .nav-link:hover {
      color: #ccc;
    }

    /* Buttons */
    .btn-black {
  background-color: #fff;
  color: #000 !important;
  border: 1px solid #000;
  border-radius: 30px;
  font-weight: 500;
  padding: 6px 18px;
  transition: 0.3s;
}

.btn-black:hover {
  background-color: #000;
  color: #fff !important;
  border-color: #000;
}


    /* Main Box */
    .main-box {
      max-width: 1200px;
      margin: 140px auto 60px auto;
      background-color: #000;
      color: #fff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 8px 30px rgba(0,0,0,0.3);
      overflow-x: auto;
    }

    .main-box h2 {
      text-align: center;
      margin-bottom: 30px;
      font-weight: 600;
    }

    /* Table Styling */
table {
  width: 100%;
  border-collapse: collapse;
  background-color: #fff; /* White table */
  color: #000; /* Black text */
  border-radius: 6px;
  overflow: hidden;
}

th, td {
  padding: 12px 15px;
  text-align: center;
  font-size: 15px;
  border-bottom: 1px solid #ddd;
}

th {
  background-color: #f2f2f2; /* Light gray for header */
  font-weight: 600;
}

tr:nth-child(even) {
  background-color: #f9f9f9; /* Slightly different shade for rows */
}

tr:hover {
  background-color: #f1f1f1; /* Row hover effect */
}

/* Update submit buttons to match white table */
.submit {
  background-color: #000;
  color: #fff;
  border: none;
  padding: 6px 12px;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: 0.3s;
}

.submit:hover {
  background-color: #333;
}


    /* Search input */
    .form-control {
      border-radius: 30px;
      padding: 6px 20px;
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark navbar-custom fixed-top">
  <div class="container-fluid">
     <button class="btn btn-outline-light me-3" onclick="history.back()">← Back</button>
    <a class="navbar-brand fw-bold" href="#">MediConnect</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="mainNavbar">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0 gap-3">
        <li class="nav-item"><a class="nav-link active" href="user_homepage.html">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="blog.php">Blog</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Services</a></li>
        <li class="nav-item"><a class="nav-link" href="about.html">About Us</a></li>
      </ul>

      <!-- Search -->
      <form class="d-flex me-3" role="search">
        <input class="form-control" type="search" placeholder="Search by location..." id="searchInput">
      </form>

      <!-- Buttons -->
      <div class="d-flex gap-2">
        <button class="btn btn-black" onclick="window.location.href='logout.php'">Log out</button>
        <button class="btn btn-black" onclick="window.location.href='user_profile.php'">Profile</button>
      </div>
    </div>
  </div>
</nav>

<!-- Main Box -->
<div class="main-box">
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

<footer class="bg-dark text-white text-center py-1 fixed-bottom">
  <div class="container">
    <p class="mb-1">&copy; 2025 MediConnect. All rights reserved.</p>
    <p class="mb-0">
      <a href="about.html" class="text-white text-decoration-underline">About</a> | 
      <a href="blog.php" class="text-white text-decoration-underline">Blog</a> | 
      <a href="#" class="text-white text-decoration-underline">Services</a>
    </p>
  </div>
</footer>

<script>
  document.getElementById('searchInput').addEventListener('input', search);

  function search() {
    const input = document.getElementById('searchInput').value.toLowerCase().trim();
    const rows = document.querySelectorAll('#scheduleTable tr');

    for (let i = 1; i < rows.length; i++) {
      const doctorName = rows[i].querySelector('.doctor-name')?.textContent.toLowerCase().trim() || '';
      const specialization = rows[i].querySelector('.specialization')?.textContent.toLowerCase().trim() || '';
      const location = rows[i].querySelector('.location')?.textContent.toLowerCase().trim() || '';

      if (doctorName.includes(input) || specialization.includes(input) || location.includes(input)) {
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

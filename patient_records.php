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

$doctor_id = $_SESSION['doctor_id'];

// Fetch latest appointment per patient
$sql_latest = "
  SELECT 
    a.id AS appointment_id, 
    u.id AS user_id, 
    u.firstname, 
    u.lastname, 
    u.email, 
    pr.weight, 
    pr.pulse, 
    pr.blood_pressure, 
    pr.temperature, 
    pr.blood_group, 
    pr.problems, 
    pr.allergies, 
    pr.symptoms_duration, 
    pr.previous_history, 
    pr.prescription, 
    pr.notes, 
    pr.next_appointment,
    s.date AS appointment_date
  FROM appointments a
  JOIN (
      SELECT MAX(a2.id) AS latest_appointment_id
      FROM appointments a2
      JOIN schedules s2 ON a2.schedule_id = s2.id
      WHERE s2.doctor_id = ? AND a2.status = 'approved'
      GROUP BY a2.user_id
  ) latest ON latest.latest_appointment_id = a.id
  JOIN users u ON a.user_id = u.id
  JOIN schedules s ON a.schedule_id = s.id
  LEFT JOIN patient_records pr ON pr.appointment_id = a.id AND pr.doctor_id = ?
  ORDER BY s.date DESC
";

$stmt_latest = $conn->prepare($sql_latest);
$stmt_latest->bind_param("ii", $doctor_id, $doctor_id);
$stmt_latest->execute();
$result_latest = $stmt_latest->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Doctor Patients</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
  <style>
    body {
      background: #fff;
      color: #000;
      font-family: 'Poppins', sans-serif;
      padding-top: 80px;
    }
    .wrapper {
      max-width: 1200px;
      margin: 0 auto 50px;
      padding: 20px;
    }
    .page-title {
      text-align: center;
      margin-bottom: 30px;
      font-size: 28px;
      color: #000;
      font-weight: 600;
    }
    .patients-grid {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      justify-content: center;
    }
    .patient-box {
      background: #000;
      color: #fff;
      padding: 20px;
      border-radius: 12px;
      width: 320px;
      display: flex;
      flex-direction: column;
      gap: 10px;
    }
    .patient-box h3 {
      margin-bottom: 5px;
      font-size: 20px;
    }
    .patient-box p {
      margin: 0;
      font-size: 14px;
    }
    .record-section p {
      font-size: 13px;
      color: #ddd;
    }
    .patient-box .btn {
      margin-top: 10px;
      background: #fff;
      color: #000;
      border-radius: 5px;
      font-weight: bold;
      text-align: center;
      text-decoration: none;
      padding: 6px 10px;
      transition: 0.3s;
    }
    .patient-box .btn:hover {
      background: #27ae60;
      color: #fff;
    }
    .collapse-btn {
      background: none;
      border: none;
      color: #27ae60;
      text-align: left;
      padding: 0;
      margin-top: 8px;
      font-size: 14px;
      cursor: pointer;
    }
    #searchInput {
      border-radius: 50px;
      width: 250px;
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
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
      <form class="d-flex me-3" role="search">
        <input class="form-control rounded-pill" type="search" placeholder="Search patients..." id="searchInput">
      </form>
      <div class="d-flex gap-2">
        <button class="btn btn-light" onclick="window.location.href='doctor_profile.php'">Profile</button>
        <button class="btn btn-light" onclick="window.location.href='logout.php'">Logout</button>
      </div>
    </div>
  </div>
</nav>

<div class="wrapper">
  <h2 class="page-title">Patients Who Booked Appointments</h2>

  <?php if ($result_latest->num_rows > 0): ?>
    <div class="patients-grid" id="patientsGrid">
      <?php while ($row = $result_latest->fetch_assoc()): ?>
        <div class="patient-box">
          <h3 class="patient-name"><?= htmlspecialchars($row['firstname'] . ' ' . $row['lastname']) ?></h3>
          <p class="patient-email"><strong>Email:</strong> <?= htmlspecialchars($row['email']) ?></p>
          <p><strong>Latest Appointment:</strong> <?= htmlspecialchars($row['appointment_date']) ?></p>

          <div class="record-section">
            <p><strong>Weight:</strong> <?= htmlspecialchars($row['weight']) ?></p>
            <p><strong>Pulse:</strong> <?= htmlspecialchars($row['pulse']) ?></p>
            <p><strong>Blood Pressure:</strong> <?= htmlspecialchars($row['blood_pressure']) ?></p>
            <p><strong>Temperature:</strong> <?= htmlspecialchars($row['temperature']) ?></p>
            <p><strong>Blood Group:</strong> <?= htmlspecialchars($row['blood_group']) ?></p>
            <p><strong>Problems:</strong> <?= nl2br(htmlspecialchars($row['problems'])) ?></p>
            <p><strong>Allergies:</strong> <?= nl2br(htmlspecialchars($row['allergies'])) ?></p>
            <p><strong>Symptoms Duration:</strong> <?= nl2br(htmlspecialchars($row['symptoms_duration'])) ?></p>
            <p><strong>Previous History:</strong> <?= nl2br(htmlspecialchars($row['previous_history'])) ?></p>
            <p><strong>Prescription:</strong> <?= nl2br(htmlspecialchars($row['prescription'])) ?></p>
            <p><strong>Notes:</strong> <?= nl2br(htmlspecialchars($row['notes'])) ?></p>
            <p><strong>Next Appointment:</strong> <?= htmlspecialchars($row['next_appointment']) ?></p>
          </div>

          <a href="add_patient_record.php?appointment_id=<?= $row['appointment_id'] ?>&user_id=<?= $row['user_id'] ?>" class="btn">Update Current Record</a>

          <!-- Previous Records Collapse -->
          <?php
            $user_id = $row['user_id'];
            $sql_prev = "
              SELECT pr.*, s.date AS appointment_date
              FROM patient_records pr
              JOIN appointments a ON pr.appointment_id = a.id
              JOIN schedules s ON a.schedule_id = s.id
              WHERE pr.doctor_id = ? AND a.user_id = ? AND a.id != ?
              ORDER BY s.date DESC
            ";
            $stmt_prev = $conn->prepare($sql_prev);
            $stmt_prev->bind_param("iii", $doctor_id, $user_id, $row['appointment_id']);
            $stmt_prev->execute();
            $res_prev = $stmt_prev->get_result();
          ?>
          <?php if ($res_prev->num_rows > 0): ?>
            <button class="collapse-btn" data-bs-toggle="collapse" data-bs-target="#prev<?= $row['user_id'] ?>">Show Previous Records</button>
            <div class="collapse mt-2" id="prev<?= $row['user_id'] ?>">
              <?php while ($p = $res_prev->fetch_assoc()): ?>
                <div style="border-top:1px solid #555; margin-top:8px; padding-top:8px;">
                  <p><strong>Date:</strong> <?= htmlspecialchars($p['appointment_date']) ?></p>
                  <p><strong>Weight:</strong> <?= htmlspecialchars($p['weight']) ?></p>
                  <p><strong>Pulse:</strong> <?= htmlspecialchars($p['pulse']) ?></p>
                  <p><strong>Blood Pressure:</strong> <?= htmlspecialchars($p['blood_pressure']) ?></p>
                  <p><strong>Temperature:</strong> <?= htmlspecialchars($p['temperature']) ?></p>
                  <p><strong>Problems:</strong> <?= nl2br(htmlspecialchars($p['problems'])) ?></p>
                  <p><strong>Allergies:</strong> <?= nl2br(htmlspecialchars($p['allergies'])) ?></p>
                  <p><strong>Symptoms Duration:</strong> <?= nl2br(htmlspecialchars($p['symptoms_duration'])) ?></p>
                  <p><strong>Previous History:</strong> <?= nl2br(htmlspecialchars($p['previous_history'])) ?></p>
                  <p><strong>Prescription:</strong> <?= nl2br(htmlspecialchars($p['prescription'])) ?></p>
                  <p><strong>Notes:</strong> <?= nl2br(htmlspecialchars($p['notes'])) ?></p>
                  <p><strong>Next Appointment:</strong> <?= htmlspecialchars($p['next_appointment']) ?></p>
                </div>
              <?php endwhile; ?>
            </div>
          <?php endif; ?>
        </div>
      <?php endwhile; ?>
    </div>
  <?php else: ?>
    <p style="text-align:center; color:#ccc;">No patients have booked appointments yet.</p>
  <?php endif; ?>
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
<script>
  document.getElementById('searchInput').addEventListener('input', function () {
    const keyword = this.value.toLowerCase().trim();
    const boxes = document.querySelectorAll('.patient-box');
    boxes.forEach(box => {
      const name = box.querySelector('.patient-name')?.textContent.toLowerCase() || '';
      const email = box.querySelector('.patient-email')?.textContent.toLowerCase() || '';
      box.style.display = (name.includes(keyword) || email.includes(keyword)) ? 'flex' : 'none';
    });
  });
</script>
</body>
</html>

<?php $conn->close(); ?>


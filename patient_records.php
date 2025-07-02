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

$sql = "
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
    pr.next_appointment
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

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $doctor_id, $doctor_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Doctor Patients</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="style.css" />
  <style>
    body {
      background: url("images/doc2.jpg");
      background-size: cover;
      background-repeat: no-repeat;
      background-attachment: fixed;
      overflow: auto;
      font-family: 'Poppins', sans-serif;
    }

    h2.page-title {
      text-align: center;
      margin: 120px 0 30px 0;
      color: white;
      font-weight: 600;
      font-size: 2rem;
    }

    .wrapper {
      display: flex;
  justify-content: center;
  flex-direction: column;
  align-items: center;
  min-height: 100vh;
  padding-top: 100px; 
  box-sizing: border-box;
  background: rgba(39, 39, 39, 0.4);  
    }

    .patients-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
      gap: 30px;
    }

    .patient-box {
      background: rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(12px);
      border-radius: 20px;
      padding: 25px 30px;
      box-shadow: 0 0 20px rgba(255, 255, 255, 0.2);
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      width: 100%;
    }

    .patient-box h3 {
      font-size: 22px;
      color: white;
      margin-bottom: 6px;
    }

    .patient-box p {
      margin: 4px 0;
      font-size: 15px;
      color: #eee;
    }

    .patient-box .btn {
      margin-top: 12px;
      background: rgba(255, 255, 255, 0.7);
      color: black;
      border: none;
      padding: 8px 16px;
      border-radius: 8px;
      cursor: pointer;
      text-decoration: none;
      font-weight: 600;
      text-align: center;
      transition: background-color 0.3s ease;
      align-self: flex-start;
    }

    .patient-box .btn:hover {
      background: rgba(255, 255, 255, 0.5);
    }

    .record-section {
      margin-top: 15px;
      background: transparent;
      padding: 15px;
      border-radius: 12px;
      font-size: 14px;
      color: #ddd;
      line-height: 1.4;
    }

    .record-section strong {
      color: #fff;
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
  </style>
</head>
<body>
  <div class="wrapper">
  <!-- Bootstrap Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-transparent fixed-top px-4 mt-3">
    <div class="container-fluid">
      <a class="navbar-brand fw-bold" href="#">MediConnect<span class="text-primary"> .</span></a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="mainNavbar">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link active" href="doctor_homepage.php">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="blog.php">Blog</a></li>
          <li class="nav-item"><a class="nav-link" href="#">Services</a></li>
          <li class="nav-item"><a class="nav-link" href="about.html">About</a></li>
        </ul>
        <form class="d-flex me-3" role="search">
          <input class="form-control rounded-pill" type="search" placeholder="Search patients..." id="searchInput">
        </form>
        <div class="d-flex gap-2">
          <button class="btn custom-btn btn-signup" onclick="window.location.href='doctor_profile.php'">Profile</button>
          <button class="btn custom-btn btn-signin" onclick="window.location.href='logout.php'">Logout</button>
        </div>
      </div>
    </div>
  </nav>

  <h2 class="page-title">Patients Who Booked Appointments</h2>

  
    <?php if ($result->num_rows > 0): ?>
      <div class="patients-grid" id="patientsGrid">
        <?php while ($row = $result->fetch_assoc()): ?>
          <div class="patient-box">
            <h3 class="patient-name"><?= htmlspecialchars($row['firstname'] . ' ' . $row['lastname']) ?></h3>
            <p class="patient-email"><strong>Email:</strong> <?= htmlspecialchars($row['email']) ?></p>

            <?php if ($row['weight'] || $row['pulse'] || $row['blood_pressure'] || $row['temperature'] || $row['blood_group']): ?>
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
            <?php else: ?>
              <p style="margin-top:12px; font-style: italic; color:#aaa;">No patient record added yet.</p>
            <?php endif; ?>

            <a href="add_patient_record.php?appointment_id=<?= $row['appointment_id'] ?>&user_id=<?= $row['user_id'] ?>" class="btn">Update Patient Record</a>
          </div>
        <?php endwhile; ?>
      </div>
    <?php else: ?>
      <p style="text-align:center; color:#ccc;">No patients have booked appointments yet.</p>
    <?php endif; ?>
</div>

  <script>
    document.getElementById('searchInput').addEventListener('input', function () {
      const keyword = this.value.toLowerCase().trim();
      const boxes = document.querySelectorAll('.patient-box');
      boxes.forEach(box => {
        const name = box.querySelector('.patient-name')?.textContent.toLowerCase() || '';
        const email = box.querySelector('.patient-email')?.textContent.toLowerCase() || '';
        if (name.includes(keyword) || email.includes(keyword)) {
          box.style.display = 'flex';
        } else {
          box.style.display = 'none';
        }
      });
    });
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>

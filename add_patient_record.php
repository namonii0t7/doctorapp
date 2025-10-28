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

if (!isset($_GET['appointment_id']) || !isset($_GET['user_id'])) {
    echo "Missing appointment or user ID.";
    exit();
}

$appointment_id = (int)$_GET['appointment_id'];
$user_id = (int)$_GET['user_id'];

if ($appointment_id <= 0 || $user_id <= 0) {
    echo "Invalid appointment or user ID.";
    exit();
}

// Get patient basic info
$stmt = $conn->prepare("SELECT firstname, lastname, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
if ($user_result->num_rows == 0) {
    echo "Patient not found.";
    exit();
}
$user = $user_result->fetch_assoc();
$stmt->close();

// Check if a record exists for this appointment and doctor
$stmt = $conn->prepare("SELECT * FROM patient_records WHERE appointment_id = ? AND doctor_id = ?");
$stmt->bind_param("ii", $appointment_id, $doctor_id);
$stmt->execute();
$record_result = $stmt->get_result();
$record = $record_result->fetch_assoc() ?? [];
$stmt->close();

$success_msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $weight = $_POST['weight'] ?? '';
    $pulse = $_POST['pulse'] ?? '';
    $blood_pressure = $_POST['blood_pressure'] ?? '';
    $temperature = $_POST['temperature'] ?? '';
    $blood_group = $_POST['blood_group'] ?? '';
    $problems = $_POST['problems'] ?? '';
    $allergies = $_POST['allergies'] ?? '';
    $symptoms_duration = $_POST['symptoms_duration'] ?? '';
    $previous_history = $_POST['previous_history'] ?? '';
    $prescription = $_POST['prescription'] ?? '';
    $notes = $_POST['notes'] ?? '';
    $next_appointment = $_POST['next_appointment'] ?? null;

    if ($record) {
        // Update existing record
        $stmt = $conn->prepare("UPDATE patient_records SET weight=?, pulse=?, blood_pressure=?, temperature=?, blood_group=?, problems=?, allergies=?, symptoms_duration=?, previous_history=?, prescription=?, notes=?, next_appointment=? WHERE appointment_id=? AND doctor_id=?");
        $stmt->bind_param("sssssssssssiii", $weight, $pulse, $blood_pressure, $temperature, $blood_group, $problems, $allergies, $symptoms_duration, $previous_history, $prescription, $notes, $next_appointment, $appointment_id, $doctor_id);
        $stmt->execute();
        $stmt->close();
        $success_msg = "Patient record updated successfully.";
    } else {
        // Insert new record
        $stmt = $conn->prepare("INSERT INTO patient_records (appointment_id, user_id, doctor_id, weight, pulse, blood_pressure, temperature, blood_group, problems, allergies, symptoms_duration, previous_history, prescription, notes, next_appointment) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iiissssssssssss", $appointment_id, $user_id, $doctor_id, $weight, $pulse, $blood_pressure, $temperature, $blood_group, $problems, $allergies, $symptoms_duration, $previous_history, $prescription, $notes, $next_appointment);
        $stmt->execute();
        $stmt->close();
        $success_msg = "Patient record added successfully.";
    }

    // Refresh record after saving
    $stmt = $conn->prepare("SELECT * FROM patient_records WHERE appointment_id = ? AND doctor_id = ?");
    $stmt->bind_param("ii", $appointment_id, $doctor_id);
    $stmt->execute();
    $record_result = $stmt->get_result();
    $record = $record_result->fetch_assoc() ?? [];
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Update Patient Record</title>
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="style.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: #fff; /* white background */
      color: #000;
      padding-top: 80px;   /* space for fixed navbar */
      padding-bottom: 70px; /* space for fixed footer */
    }

    .wrapper {
      flex-direction: column;
      background: #000; /* black container */
      max-width: 650px;
      margin: 0 auto;
      border-radius: 15px;
      padding: 40px 40px 50px 40px;
      box-shadow: 0 0 25px rgba(0,0,0,0.2);
    }

    h2 {
      color: #fff; /* white heading */
      font-weight: 600;
      margin-bottom: 30px;
      text-align: center;
      font-size: 22px;
      letter-spacing: 1px;
    }

    form label {
      display: block;
      color: #fff; /* labels white */
      font-weight: 500;
      margin: 15px 0 6px;
      font-size: 14px;
    }

    form input[type="text"],
    form input[type="date"],
    form textarea {
      width: 100%;
      background: #fff; /* white inputs */
      border: 1px solid #ccc;
      border-radius: 8px;
      padding: 12px 16px;
      color: #000; /* black text */
      font-size: 14px;
      outline: none;
      resize: vertical;
      transition: 0.3s;
    }

    form input[type="text"]:focus,
    form input[type="date"]:focus,
    form textarea:focus {
      border-color: #000; /* black border on focus */
    }

    textarea {
      min-height: 80px;
    }

    .btnn {
      margin-top: 30px;
      width: 100%;
      height: 48px;
      font-size: 15px;
      font-weight: 600;
      color: #fff; /* white text */
      background: #000; /* black button */
      border-radius: 8px;
      border: 2px solid #fff;
      cursor: pointer;
      transition: 0.3s ease;
    }

    .btnn:hover {
      background: #222;
    }

    .success-message {
      text-align: center;
      font-weight: 600;
      margin-bottom: 15px;
      color: #4CAF50;
    }
  </style>
</head>
<body>

<!-- Navbar (fixed-top Bootstrap) -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
  <div class="container">
     <button class="btn btn-outline btn-dark me-3" onclick="history.back()">← Back</button>
    <a class="navbar-brand fw-bold" href="#">MediConnect</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="mainNavbar">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link active" href="doctor_homepage.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="blog.php">Blog</a></li>
        <li class="nav-item"><a class="nav-link" href="service.html">Services</a></li>
        <li class="nav-item"><a class="nav-link" href="about.html">About</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="wrapper">
  <h2>Update Patient Record for <?= htmlspecialchars($user['firstname'] . ' ' . $user['lastname']) ?></h2>

  <?php if ($success_msg): ?>
    <p class="success-message"><?= htmlspecialchars($success_msg) ?></p>
  <?php endif; ?>

  <form action="" method="POST" autocomplete="off">
    <label for="weight">Weight (kg):</label>
    <input type="text" id="weight" name="weight" value="<?= htmlspecialchars($record['weight'] ?? '') ?>" />

    <label for="pulse">Pulse:</label>
    <input type="text" id="pulse" name="pulse" value="<?= htmlspecialchars($record['pulse'] ?? '') ?>" />

    <label for="blood_pressure">Blood Pressure:</label>
    <input type="text" id="blood_pressure" name="blood_pressure" value="<?= htmlspecialchars($record['blood_pressure'] ?? '') ?>" />

    <label for="temperature">Temperature (°C):</label>
    <input type="text" id="temperature" name="temperature" value="<?= htmlspecialchars($record['temperature'] ?? '') ?>" />

    <label for="blood_group">Blood Group:</label>
    <input type="text" id="blood_group" name="blood_group" value="<?= htmlspecialchars($record['blood_group'] ?? '') ?>" />

    <label for="problems">Problems:</label>
    <textarea id="problems" name="problems"><?= htmlspecialchars($record['problems'] ?? '') ?></textarea>

    <label for="allergies">Allergies:</label>
    <textarea id="allergies" name="allergies"><?= htmlspecialchars($record['allergies'] ?? '') ?></textarea>

    <label for="symptoms_duration">Symptoms Duration:</label>
    <textarea id="symptoms_duration" name="symptoms_duration"><?= htmlspecialchars($record['symptoms_duration'] ?? '') ?></textarea>

    <label for="previous_history">Previous History:</label>
    <textarea id="previous_history" name="previous_history"><?= htmlspecialchars($record['previous_history'] ?? '') ?></textarea>

    <label for="prescription">Prescription:</label>
    <textarea id="prescription" name="prescription"><?= htmlspecialchars($record['prescription'] ?? '') ?></textarea>

    <label for="notes">Notes:</label>
    <textarea id="notes" name="notes"><?= htmlspecialchars($record['notes'] ?? '') ?></textarea>

    <label for="next_appointment">Next Appointment Date:</label>
    <input type="date" id="next_appointment" name="next_appointment" value="<?= htmlspecialchars($record['next_appointment'] ?? '') ?>" />

    <button type="submit" class="btnn">Save Record</button>
  </form>
</div>

<!-- Footer (fixed-bottom Bootstrap) -->
<footer class="bg-dark text-white text-center py-3 fixed-bottom">
  <div class="container">
    <p class="mb-1">&copy; 2025 MediConnect. All rights reserved.</p>
    <p class="mb-0">
      <a href="about.html" class="text-white text-decoration-underline">About</a> | 
      <a href="blog.php" class="text-white text-decoration-underline">Blog</a> | 
      <a href="#" class="text-white text-decoration-underline">Services</a>
    </p>
  </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

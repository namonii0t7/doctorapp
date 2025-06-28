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
  <style>
    /* Additional style for this form page */
    .wrapper {
      flex-direction: column;
      padding-top: 140px;
      background: rgba(39, 39, 39, 0.6);
      max-width: 520px;
      margin: 120px auto 50px auto;
      border-radius: 15px;
      padding: 30px 30px 40px 30px;
      backdrop-filter: blur(10px);
      box-shadow: 0 0 20px rgba(0,0,0,0.3);
    }

    h2 {
      color: #fff;
      font-weight: 600;
      margin-bottom: 25px;
      text-align: center;
    }

    form label {
      display: block;
      color: #fff;
      font-weight: 500;
      margin: 12px 0 5px;
      font-size: 14px;
    }

    form input[type="text"],
    form input[type="date"],
    form textarea {
      width: 100%;
      background: rgba(255, 255, 255, 0.2);
      border: none;
      border-radius: 30px;
      padding: 12px 20px;
      color: #fff;
      font-size: 15px;
      outline: none;
      resize: vertical;
      transition: background 0.3s ease;
      font-family: 'Poppins', sans-serif;
    }

    form input[type="text"]:hover,
    form input[type="date"]:hover,
    form textarea:hover,
    form input[type="text"]:focus,
    form input[type="date"]:focus,
    form textarea:focus {
      background: rgba(255, 255, 255, 0.35);
    }

    textarea {
      min-height: 80px;
      font-family: 'Poppins', sans-serif;
    }

    .btn {
      margin-top: 25px;
      width: 100%;
      height: 45px;
      font-size: 15px;
      font-weight: 600;
      color: black;
      background: rgba(255, 255, 255, 0.7);
      border-radius: 30px;
      border: none;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    .btn:hover {
      background: rgba(255, 255, 255, 0.5);
      box-shadow: 1px 5px 7px 1px rgba(0, 0, 0, 0.2);
    }

    .success-message {
      text-align: center;
      font-weight: 600;
      margin-bottom: 15px;
      color: #a8ffa8;
      text-shadow: 0 0 5px #3b7b3b;
    }
  </style>
</head>
<body>

<nav class="nav">
  <div class="nav-logo"><p>MediConnect .</p></div>
  <div class="nav-menu" id="navMenu">
    <ul>
      <li><a href="patient_records.php" class="link">Back to Patients</a></li>
    </ul>
  </div>
  <div class="nav-button">
    <button class="btn" onclick="window.location.href='doctor_profile.php'">Profile</button>
    <button class="btn" onclick="window.location.href='logout.php'">Logout</button>
  </div>
  <div class="nav-menu-btn">
    <i class="bx bx-menu" onclick="myMenuFunction()"></i>
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

    <button type="submit" class="btn">Save Record</button>
  </form>
</div>

</body>
</html>


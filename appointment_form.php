<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['schedule_id'])) {
    header("Location: user_homepage.php");
    exit();
}

$schedule_id = $_GET['schedule_id'];
$user_id = $_SESSION['user_id'];

$conn = new mysqli("localhost", "root", "", "docappp");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//  Check if user already booked this schedule
$check_appointment = $conn->prepare("SELECT id FROM appointments WHERE schedule_id = ? AND user_id = ?");
$check_appointment->bind_param("ii", $schedule_id, $user_id);
$check_appointment->execute();
$check_appointment->store_result();
if ($check_appointment->num_rows > 0) {
    echo "<h2 style='color:red; text-align:center;'>You have already booked your appointment.</h2>";
    echo "<p style='text-align:center;'><a href='user_homepage.php'>Return to Home</a></p>";
    exit();
}
$check_appointment->close();

//  Check if slots are full
$slot_query = $conn->prepare("
    SELECT COUNT(a.id) AS booked, s.max_patients, s.appointment_fees, s.doctor_id
    FROM schedules s
    LEFT JOIN appointments a ON a.schedule_id = s.id
    WHERE s.id = ?
");
$slot_query->bind_param("i", $schedule_id);
$slot_query->execute();
$result = $slot_query->get_result();
$data = $result->fetch_assoc();
$booked = $data['booked'];
$max_patients = $data['max_patients'];
$appointment_fees = $data['appointment_fees'];
$doctor_id = $data['doctor_id'];
$slot_query->close();

if ($booked >= $max_patients) {
    echo "<h2 style='color:red; text-align:center;'>Sorry, appointment slots are full. Try again tomorrow.</h2>";
    echo "<p style='text-align:center;'><a href='user_homepage.php'>Return to Home</a></p>";
    exit();
}

// Get doctor phone
$stmt2 = $conn->prepare("SELECT phone FROM doctors WHERE id = ?");
$stmt2->bind_param("i", $doctor_id);
$stmt2->execute();
$doctor_result = $stmt2->get_result();
$doctor_phone = "N/A";
if ($doctor_result->num_rows > 0) {
    $doctor_data = $doctor_result->fetch_assoc();
    $doctor_phone = $doctor_data['phone'];
}
$stmt2->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Book Appointment with Payment</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <nav class="nav">
    <div class="nav-logo">
      <p>MediConnect .</p>
    </div>
    <div class="nav-menu" id="navMenu">
      <ul>
        <li><a href="user_homepage.html" class="link active">Home</a></li>
        <li><a href="blog.php" class="link">Blog</a></li>
        <li><a href="#" class="link">Services</a></li>
        <li><a href="about.html" class="link">About</a></li>
      </ul>
    </div>
    <div class="nav-button">
      <button class="btn white-btn" onclick="window.location.href='logout.php'">Log out</button>
      <button class="btn" onclick="window.location.href='user_profile.php'">Profile</button>
    </div>
  </nav>

  <div class="form-box-doctor">
    <form action="book_appointment.php" method="POST" class="doctor-register-container">
      <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_id) ?>">
      <input type="hidden" name="schedule_id" value="<?= htmlspecialchars($schedule_id) ?>">
      <input type="hidden" name="amount" value="<?= htmlspecialchars($appointment_fees) ?>">

      <header>Confirm Appointment & Pay with bKash</header>

      <p style="color:white;text-align:center;">
        You're booking as 
        <strong>
          <?= isset($_SESSION['firstname']) && isset($_SESSION['lastname']) 
              ? htmlspecialchars($_SESSION['firstname'] . " " . $_SESSION['lastname']) 
              : 'User' ?>
        </strong>
      </p>

      <p style="color:white;text-align:center; font-weight:bold;">
        Please pay <?= htmlspecialchars($appointment_fees) ?> tk to this number: <?= htmlspecialchars($doctor_phone) ?> and enter your payment details
      </p>

      <label style="color:white;">bKash Number</label>
      <input type="text" name="payer_number" placeholder="e.g. 01XXXXXXXXX" required pattern="01[0-9]{9}">

      <label style="color:white;">Transaction ID (TrxID)</label>
      <input type="text" name="trxid" placeholder="e.g. TX1234ABC" required>

      <label style="color:white;">Amount (BDT)</label>
      <input type="number" value="<?= htmlspecialchars($appointment_fees) ?>" readonly>

      <button type="submit" class="submit">Confirm & Pay</button>
    </form>
  </div>
</body>
</html>

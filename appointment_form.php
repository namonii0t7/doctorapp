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

// Check if user already booked this schedule
$check_appointment = $conn->prepare("SELECT id FROM appointments WHERE schedule_id = ? AND user_id = ?");
$check_appointment->bind_param("ii", $schedule_id, $user_id);
$check_appointment->execute();
$check_appointment->store_result();
if ($check_appointment->num_rows > 0) {
    echo "<h2 style='color:red; text-align:center;'>You have already booked your appointment.</h2>";
    echo "<p style='text-align:center;'><a href='user_homepage.html'>Return to Home</a></p>";
    exit();
}
$check_appointment->close();

// Check if slots are full
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
    echo "<p style='text-align:center;'><a href='user_homepage.html'>Return to Home</a></p>";
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
  <title>Book Appointment & Pay | MediConnect</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css" />
  <style>
    body {
      background-color: #fff;
      color: #000;
      font-family: 'Poppins', sans-serif;
    }

    .wrapper {
      max-width: 600px;
      margin: 130px auto;
      background: #000;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.3);
    }

    .wrapper header {
      color: #fff;
      text-align: center;
      font-size: 22px;
      font-weight: bold;
      margin-bottom: 20px;
    }

    label {
      color: #fff;
      font-weight: bold;
      margin-top: 10px;
    }

    .input-field {
      width: 100%;
      padding: 8px;
      margin-top: 5px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }

    .submit {
      width: 100%;
      background: #fff;
      color: #000;
      border: 2px solid #000;
      font-weight: bold;
      padding: 10px;
      border-radius: 6px;
      cursor: pointer;
      transition: 0.3s;
    }

    .submit:hover {
      background: #000;
      color: #fff;
      border-color: #fff;
    }

    p {
      color: #fff;
      text-align: center;
      margin-bottom: 15px;
    }

    footer {
      margin-top: 100px;
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
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link active" href="user_homepage.html">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="blog.php">Blog</a></li>
          <li class="nav-item"><a class="nav-link" href="service.html">Services</a></li>
          <li class="nav-item"><a class="nav-link" href="about.html">About</a></li>
        </ul>
        <div class="d-flex ms-3 gap-2">
          <button class="btn btn-light" onclick="window.location.href='logout.php'">Logout</button>
          <button class="btn btn-outline-light" onclick="window.location.href='user_profile.php'">Profile</button>
        </div>
      </div>
    </div>
  </nav>

  <div class="wrapper">
    <form action="book_appointment.php" method="POST">
      <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_id) ?>">
      <input type="hidden" name="schedule_id" value="<?= htmlspecialchars($schedule_id) ?>">
      <input type="hidden" name="amount" value="<?= htmlspecialchars($appointment_fees) ?>">

      <header>Confirm Appointment & Pay with bKash</header>

      <p>
        You’re booking as 
        <strong>
          <?= isset($_SESSION['firstname']) && isset($_SESSION['lastname']) 
              ? htmlspecialchars($_SESSION['firstname'] . " " . $_SESSION['lastname']) 
              : 'User' ?>
        </strong>
      </p>

      <p>
        Please pay <strong><?= htmlspecialchars($appointment_fees) ?> BDT</strong>  
        to this number: <strong><?= htmlspecialchars($doctor_phone) ?></strong>  
        and enter your payment details below.
      </p>

      <label>bKash Number</label>
      <input type="text" name="payer_number" placeholder="01XXXXXXXXX" class="input-field" required pattern="01[0-9]{9}">

      <label>Transaction ID (TrxID)</label>
      <input type="text" name="trxid" placeholder="TrxID" class="input-field" required>

      <label>Amount (BDT)</label>
      <input type="number" value="<?= htmlspecialchars($appointment_fees) ?>" class="input-field" readonly>

      <button type="submit" class="submit">Confirm & Pay</button>
    </form>
  </div>

  <!-- Footer -->
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
</body>
</html>

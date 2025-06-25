<?php
$conn = new mysqli("localhost", "root", "", "docappp");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$doctor_id = $_GET['id'] ?? null;

if (!$doctor_id) {
    echo "Invalid doctor ID.";
    exit();
}

$sql = "SELECT * FROM doctors WHERE id = ? AND status = 'verified'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();
$doctor = $result->fetch_assoc();

if (!$doctor) {
    echo "Doctor not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Doctor Profile</title>
  <link rel="stylesheet" href="profile.css" />
  
</head>
<body>

  <div class="wrapper">
    <nav class="nav">
      <div class="nav-logo">
        <p>MediConnect .</p>
      </div>
      <div class="nav-menu" id="navMenu">
        <ul>
          <li><a href="user_homepage.html" class="link active">Home</a></li>
          <li><a href="#" class="link">Blog</a></li>
          <li><a href="#" class="link">Services</a></li>
          <li><a href="about.html" class="link">About</a></li>
        </ul>
      </div>
      <div class="nav-button">
        <button class="btn white-btn" onclick="window.location.href='user_profile.php'">Profile</button>
        <button class="btn" onclick="window.location.href='logout.php'">Log out</button>
      </div>
      <div class="nav-menu-btn">
        <i class="bx bx-menu" onclick="myMenuFunction()"></i>
      </div>
    </nav>
  </div>

  <div class="profile-container">
    <div class="left-section">
      <div class="profile-photo">
        <?php if (!empty($doctor['image']) && file_exists('uploads/' . $doctor['image'])): ?>
          <img src="uploads/<?= htmlspecialchars($doctor['image']) ?>" alt="Doctor Profile Picture" />
        <?php else: ?>
          <span>👤</span>
        <?php endif; ?>
      </div>
      <div class="bio">
        <h3>Bio</h3>
        <p><?= nl2br(htmlspecialchars($doctor['bio'])) ?></p>
      </div>
    </div>

    <div class="right-section">
      <label>Profile Info</label>
      <textarea rows="7" readonly><?php 
        echo "Dr. " . htmlspecialchars($doctor['firstname'] . ' ' . $doctor['lastname']) . "\n" .
             htmlspecialchars($doctor['specialization']) . "\n" .
             htmlspecialchars($doctor['email']) . "\n" .
             htmlspecialchars($doctor['phone']) . "\n" .
             htmlspecialchars($doctor['chamber']);
      ?></textarea>

      <a href="user_homepage.php" class="back-btn">← Back to Homepage</a>
    </div>
  </div>

  <script src="script.js"></script>
</body>
</html>

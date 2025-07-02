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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="profile.css" />
  
</head>
<body>

  <div class="wrapper">
     <nav class="navbar navbar-expand-lg navbar-dark bg-transparent fixed-top mt-3">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">MediConnect .</a>

    <!-- Burger Button -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Nav links -->
    <div class="collapse navbar-collapse" id="mainNavbar">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link active" href="user_homepage.html">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="blog.php">Blog</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Services</a></li>
        <li class="nav-item"><a class="nav-link" href="about.html">About</a></li>
      </ul>
      <div class="d-flex ms-3 gap-2">
  <button class="btn custom-btn btn-signin" onclick="window.location.href='user_profile.php?action=login'">Profile</button>
  <button class="btn custom-btn btn-signup" onclick="window.location.href='logout.php?action=register'">Log out</button>
</div>

    </div>
  </div>
</nav>>
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
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

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
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Doctor Profile</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
<style>
  body {
    background: #fff;
    color: #fff;
    font-family: 'Poppins', sans-serif;
    padding-top: 80px; /* space for fixed navbar */
  }

  /* Navbar */
  .navbar-custom {
    background: #000;
  }

  /* Profile Container */
  .profile-container {
    max-width: 900px;
    margin: 0 auto 50px;
    display: flex;
    flex-wrap: wrap;
    gap: 30px;
    background: #000;
    padding: 30px;
    border-radius: 12px;
  }

  .left-section {
    flex: 1 1 250px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 20px;
  }

  .profile-photo {
    width: 180px;
    height: 180px;
    border-radius: 50%;
    overflow: hidden;
    display: flex;
    justify-content: center;
    align-items: center;
    background: #333;
    font-size: 50px;
  }

  .profile-photo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .bio {
    width: 100%;
    text-align: center;
  }

  .bio h3 {
    font-size: 20px;
    margin-bottom: 10px;
  }

  .bio p {
    font-size: 14px;
    white-space: pre-wrap;
  }

  .right-section {
    flex: 2 1 400px;
    display: flex;
    flex-direction: column;
    gap: 15px;
  }

  .right-section label {
    font-weight: 600;
    font-size: 16px;
  }

  .right-section textarea {
    width: 100%;
    resize: none;
    border-radius: 8px;
    border: 1px solid #555;
    padding: 12px;
    font-size: 14px;
    color: #fff;
    background: #111;
  }

  .back-btn {
    margin-top: 15px;
    display: inline-block;
    text-decoration: none;
    color: #000;
    background: #fff;
    padding: 10px 18px;
    border-radius: 8px;
    font-weight: bold;
    transition: 0.3s;
  }

  .back-btn:hover {
    background: #27ae60;
    color: #fff;
  }

  footer {
    background: #000;
    color: #fff;
    text-align: center;
    padding: 15px 0;
  }

  footer a {
    color: #fff;
    text-decoration: underline;
    margin: 0 5px;
  }

  @media (max-width: 768px) {
    .profile-container {
      flex-direction: column;
    }
    .right-section textarea {
      min-height: 180px;
    }
  }
</style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark navbar-custom fixed-top">
  <div class="container">
    <a class="navbar-brand fw-bold" href="#">MediConnect</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="mainNavbar">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link active" href="user_homepage.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="blog.php">Blog</a></li>
        <li class="nav-item"><a class="nav-link" href="service.html">Services</a></li>
        <li class="nav-item"><a class="nav-link" href="about.html">About</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- Profile Content -->
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

    <a href="user_homepage.html" class="back-btn"><i class="bx bx-left-arrow-alt"></i> Back to Homepage</a>
  </div>
</div>

<!-- Footer -->
<footer>
  <p class="mb-1">&copy; 2025 MediConnect. All rights reserved.</p>
  <p class="mb-0">
    <a href="about.html">About</a> | 
    <a href="blog.php">Blog</a> | 
    <a href="#">Services</a>
  </p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

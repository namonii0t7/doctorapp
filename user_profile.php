<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "docappp");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>User Profile | MediConnect</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
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

    /* Profile Container */
    .profile-container {
      max-width: 1000px;
      margin: 140px auto 60px auto;
      display: flex;
      gap: 40px;
      background-color: #000;
      color: #fff;
      padding: 40px;
      border-radius: 20px;
      box-shadow: 0 8px 30px rgba(0,0,0,0.3);
    }

    .left-section {
      flex: 1;
      text-align: center;
    }

    .profile-photo span {
      font-size: 80px;
      display: inline-block;
      width: 150px;
      height: 150px;
      line-height: 150px;
      text-align: center;
      background: #fff;
      border-radius: 50%;
      color: #000;
      border: 2px solid #fff;
    }

    .bio h3 {
      margin-top: 20px;
      font-weight: 600;
    }

    .right-section {
      flex: 2;
    }

    label {
      font-weight: 600;
      margin-bottom: 10px;
      display: block;
    }

    textarea {
      width: 100%;
      border-radius: 12px;
      border: 1px solid #fff;
      background: rgba(255,255,255,0.05);
      color: #fff;
      padding: 15px;
      resize: none;
      margin-bottom: 20px;
    }

    .buttons {
      display: flex;
      gap: 15px;
      flex-wrap: wrap;
    }

    .btn {
      border-radius: 30px;
      padding: 8px 20px;
      font-weight: 500;
      border: 1px solid #fff;
      background: #fff;
      color: #000;
      transition: 0.3s;
    }

    .btn:hover {
      background: #ccc;
      color: #000;
    }

    .btn.danger {
      background: #000;
      color: #fff;
      border: 1px solid #ff4d4d;
    }

    .btn.danger:hover {
      background: #ff4d4d;
      color: #fff;
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
        <li class="nav-item"><a class="nav-link" href="about.html">About</a></li>
      </ul>
      <div class="d-flex gap-2">
        <button class="btn" onclick="window.location.href='logout.php'">Log out</button>
        <button class="btn" onclick="window.location.href='user_profile.php'">Profile</button>
      </div>
    </div>
  </div>
</nav>

<!-- Profile Box -->
<div class="profile-container">
  <div class="left-section">
    <div class="profile-photo">
      <span>👤</span>
    </div>
    <div class="bio">
      <h3>Welcome,</h3>
      <p><?= htmlspecialchars($user['firstname'] . ' ' . $user['lastname']); ?></p>
    </div>
  </div>

  <div class="right-section">
    <form action="edit_user.php" method="POST">
      <input type="hidden" name="id" value="<?= $user['id']; ?>" />
      <label>Profile Info</label>
      <textarea name="profile_info" rows="4" readonly><?=
        htmlspecialchars($user['firstname'] . ' ' . $user['lastname']) . "\n" .
        htmlspecialchars($user['email']);
      ?></textarea>

      <div class="buttons">
        <button type="button" class="btn" onclick="window.location.href='edit_user_profile.php'">Edit Profile</button>
        <a href="logout.php" class="btn">Logout</a>
        <a href="delete_user.php" class="btn danger" onclick="return confirm('Are you sure you want to delete your account?');">Delete Account</a>
      </div>
    </form>
  </div>
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
</body>
</html>


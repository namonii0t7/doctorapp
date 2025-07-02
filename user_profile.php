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
  <title>User Profile</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="profile.css" />
  <style>
    .profile-photo span {
      font-size: 80px;
      display: inline-block;
      width: 150px;
      height: 150px;
      line-height: 150px;
      text-align: center;
      background: #ddd;
      border-radius: 50%;
      color: #555;
      border: 2px solid #333;
    }
  </style>
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
  <button class="btn custom-btn btn-signin" onclick="window.location.href='logout.php?action=login'">Log out</button>
  <button class="btn custom-btn btn-signup" onclick="window.location.href='user_profile.php?action=register'">Profile</button>
</div>

    </div>
  </div>
</nav>
</div>

<div class="profile-container">
  <div class="left-section">
    <div class="profile-photo">
      <span>👤</span>
    </div>
    <div class="bio">
      <h3>Welcome,</h3>
      <p><?php echo htmlspecialchars($user['firstname'] . ' ' . $user['lastname']); ?></p>
    </div>
  </div>

  <div class="right-section">
    <form action="edit_user.php" method="POST">
      <input type="hidden" name="id" value="<?php echo $user['id']; ?>" />
      <label>Profile Info</label>
      <textarea name="profile_info" rows="4" readonly><?php 
        echo htmlspecialchars($user['firstname'] . ' ' . $user['lastname']) . "\n" .
             htmlspecialchars($user['email']);
      ?></textarea>

      <div class="buttons">
        <button type="button" class="btn white-btn" onclick="window.location.href='edit_user_profile.php'">Edit Profile</button>
        <a href="logout.php" class="btn">Logout</a>
        <a href="delete_user.php" class="btn danger" onclick="return confirm('Are you sure you want to delete your account?');">Delete Account</a>
      </div>
    </form>
  </div>
</div>

<script src="script.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

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
</body>
</html>

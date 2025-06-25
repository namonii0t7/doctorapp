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
$sql = "SELECT * FROM doctors WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();
$doctor = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Doctor Profile</title>
  <link rel="stylesheet" href="profile.css" />
  <style>
    .profile-photo img {
      width: 150px;
      height: 150px;
      object-fit: cover;
      border-radius: 50%;
      border: 2px solid #333;
    }
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
                    <li><a href="doctor_homepage.php" class="link active">Home</a></li>
                    <li><a href="#" class="link">Blog</a></li>
                    <li><a href="#" class="link">Services</a></li>
                    <li><a href="about.html" class="link">About</a></li>
                </ul>
            </div>
            <div class="nav-button">
                <!-- Keep classes and design, but change onclick to redirect -->
                <button class="btn white-btn" id="loginBtn" onclick="window.location.href='doctor_profile.php?action=login'">Profile</button>
                <button class="btn" id="registerBtn" onclick="window.location.href='logout.php?action=register'">logout</button>
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
          <img src="<?php echo 'uploads/' . htmlspecialchars($doctor['image']); ?>" alt="Doctor Profile Picture" />
        <?php else: ?>
          <span>👤</span>
        <?php endif; ?>
      </div>
      <div class="bio">
        <h3>Bio</h3>
        <p><?php echo htmlspecialchars($doctor['bio']); ?></p>
      </div>
    </div>

    <div class="right-section">
      <form action="edit_doctor.html" method="POST">
        <input type="hidden" name="id" value="<?php echo $doctor['id']; ?>" />
        <label>Profile Info</label>
        <textarea name="profile_info" rows="5" readonly><?php 
          echo "Dr. " . htmlspecialchars($doctor['firstname'] . ' ' . $doctor['lastname']) . "\n" .
               htmlspecialchars($doctor['specialization']) . "\n" .
               htmlspecialchars($doctor['email']) . "\n" .
               htmlspecialchars($doctor['phone']) . "\n" .
               htmlspecialchars($doctor['chamber']);
        ?></textarea>

        <div class="buttons">
          <button type="button" class="btn white-btn" id="editBtn" onclick="window.location.href='edit_doctorpre.php?action=login'">Edit Profile</button>
          <a href="logout.php" class="btn">Logout</a>
          <a href="delete_doctor.php" class="btn danger" onclick="return confirm('Are you sure you want to delete your account?');">Delete Account</a>
        </div>
      </form>
    </div>
  </div>
  <script src="script.js"></script>
</body>
</html>

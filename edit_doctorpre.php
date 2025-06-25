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
$result = $conn->query("SELECT * FROM doctors WHERE id = $doctor_id");
$doctor = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Doctor Profile</title>
  <link rel="stylesheet" href="profile.css">
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
                <button class="btn white-btn" id="loginBtn" onclick="window.location.href='doctor_profile.php?action=login'">Profile</button>
                <button class="btn" id="registerBtn" onclick="window.location.href='doclog.html?action=register'">Log out</button>
            </div>
            <div class="nav-menu-btn">
                <i class="bx bx-menu" onclick="myMenuFunction()"></i>
            </div>
        </nav>
    </div>
  <div class="profile-container">
    <div class="left-section">
      <div class="profile-photo">
        <?php if (!empty($doctor['image'])): ?>
          <img src="uploads/<?php echo htmlspecialchars($doctor['image']); ?>" alt="Profile Picture" style="width:150px; height:150px; object-fit:cover; border-radius:50%;">
        <?php else: ?>
          <span style="font-size: 100px;">👤</span>
        <?php endif; ?>
      </div>
      <div class="bio">
        <h3>Bio</h3>
        <textarea name="bio" form="editProfileForm" rows="6"><?php echo htmlspecialchars($doctor['bio'] ?? ''); ?></textarea>
      </div>
    </div>

    <div class="right-section">
      <form id="editProfileForm" action="edit_doctor.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $doctor['id']; ?>">

        <label>First Name</label>
        <input type="text" name="firstname" value="<?php echo htmlspecialchars($doctor['firstname']); ?>" required>

        <label>Last Name</label>
        <input type="text" name="lastname" value="<?php echo htmlspecialchars($doctor['lastname']); ?>" required>

        <label>Email</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($doctor['email']); ?>" required>

        <label>Phone</label>
        <input type="text" name="phone" value="<?php echo htmlspecialchars($doctor['phone']); ?>" required>

        <label>Chamber Address</label>
        <input type="text" name="chamber" value="<?php echo htmlspecialchars($doctor['chamber']); ?>" required>

        <label>Profile Picture</label>
        <input type="file" name="profile_image" accept="image/*">

        <div class="buttons" style="margin-top:15px;">
          <button type="submit" id="saveBtn">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</body>
</html>

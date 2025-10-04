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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #fff;
      color: #000;
      font-family: 'Poppins', sans-serif;
      padding-top: 80px;
    }
    .navbar {
      background-color: #000 !important;
    }
    .navbar .nav-link, .navbar-brand {
      color: #fff !important;
    }
    .profile-container {
      display: flex;
      justify-content: space-between;
      gap: 30px;
      margin: 40px auto;
      max-width: 1100px;
      padding: 30px;
      background: #000;
      color: #fff;
      border-radius: 15px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }
    .profile-photo img {
      border: 4px solid #fff;
    }
    .bio textarea {
      width: 100%;
      border-radius: 10px;
      border: none;
      padding: 10px;
      background: #fff;
      color: #000;
      resize: none;
    }
    .right-section {
      flex: 1;
    }
    .right-section label {
      font-weight: bold;
      margin-top: 10px;
      display: block;
    }
    .right-section input {
      width: 100%;
      padding: 8px;
      border-radius: 8px;
      border: 1px solid #ccc;
      margin-bottom: 10px;
    }
    .buttons button {
      background: #000;
      color: #fff;
      padding: 10px 20px;
      border-radius: 8px;
      border: none;
      transition: 0.3s;
    }
    .buttons button:hover {
      background: #333;
    }
    footer {
      background: #000;
      color: #fff;
      text-align: center;
      padding: 15px;
      margin-top: 40px;
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg fixed-top">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">MediConnect .</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="mainNavbar">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link active" href="doctor_homepage.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="blog.php">Blog</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Services</a></li>
        <li class="nav-item"><a class="nav-link" href="about.html">About</a></li>
      </ul>
      <div class="d-flex ms-3 gap-2">
        <button class="btn btn-dark" onclick="window.location.href='doctor_profile.php'">Profile</button>
        <button class="btn btn-dark" onclick="window.location.href='logout.php'">Log out</button>
      </div>
    </div>
  </div>
</nav>

<!-- Profile Edit Section -->
<div class="profile-container">
  <div class="left-section">
    <div class="profile-photo mb-3">
      <?php if (!empty($doctor['image'])): ?>
        <img src="uploads/<?php echo htmlspecialchars($doctor['image']); ?>" 
             alt="Profile Picture" style="width:150px; height:150px; object-fit:cover; border-radius:50%;">
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

      <div class="buttons mt-3">
        <button type="submit">Save Changes</button>
      </div>
    </form>
  </div>
</div>

<!-- Footer -->
<footer>
  <p>&copy; 2025 MediConnect. All rights reserved.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

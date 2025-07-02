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
  <link rel="stylesheet" href="profile.css">
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
        <li class="nav-item"><a class="nav-link active" href="doctor_homepage.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="blog.php">Blog</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Services</a></li>
        <li class="nav-item"><a class="nav-link" href="about.html">About</a></li>
      </ul>
      <div class="d-flex ms-3 gap-2">
  <button class="btn custom-btn btn-signin" onclick="window.location.href='doctor_profile.php?action=login'">Profile</button>
  <button class="btn custom-btn btn-signup" onclick="window.location.href='logout.php?action=register'">Log out</button>
</div>

    </div>
  </div>
</nav>>
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
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

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

// Fetch doctor details
$sql = "SELECT * FROM doctors WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();
$doctor = $result->fetch_assoc();

// Fetch blog posts
$post_sql = "SELECT * FROM blog_posts WHERE doctor_id = ? ORDER BY created_at DESC";
$post_stmt = $conn->prepare($post_sql);
$post_stmt->bind_param("i", $doctor_id);
$post_stmt->execute();
$post_result = $post_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Doctor Profile</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    html, body {
      height: 100%;
    }
    body {
      display: flex;
      flex-direction: column;
      background: #fff;
      font-family: 'Poppins', sans-serif;
      color: #000;
      padding-top: 70px;
    }

    .profile-container {
      max-width: 1100px;
      margin: 20px auto;
      padding: 20px;
      background: #000;
      color: #fff;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    }

    .left-section, .right-section {
      flex: 1;
      min-width: 300px;
      margin: 10px;
    }

    .profile-photo img {
      width: 150px;
      height: 150px;
      border-radius: 50%;
      border: 3px solid #fff;
      object-fit: cover;
    }

    textarea {
      width: 100%;
      background: #fff;
      color: #000;
      padding: 10px;
      border-radius: 8px;
      border: none;
      resize: none;
    }

    .buttons .btn {
      margin: 5px;
      border-radius: 8px;
    }

    .white-btn {
      background: #fff;
      color: #000;
      border: 1px solid #000;
    }
    .white-btn:hover {
      background: #ccc;
      color: #000;
    }

    .btn.danger {
      background: #dc3545;
      color: #fff;
    }

    .blog-section {
      margin-top: 30px;
      padding: 20px;
      background: black;
      border-radius: 10px;
    }

    /* Blog post style */
    .blog-post {
      background: #fff;
      color: #000;
      padding: 15px;
      border-radius: 8px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
    }

    .blog-post h3 {
      color: #000;
    }
    .blog-post p, .blog-post small {
      color: #000;
    }
    .blog-post .btn {
      background: #000;
      color: #fff;
      border: none;
    }
    .blog-post .btn:hover {
      background: #333;
      color: #fff;
    }

    footer {
      background: #000;
      color: #fff;
      text-align: center;
      padding: 15px;
      margin-top: auto; /* sticks footer to bottom */
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
  <div class="container-fluid">
     <button class="btn btn-outline-light me-3" onclick="history.back()">← Back</button>
    <a class="navbar-brand" href="#">MediConnect.</a>
    <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="mainNavbar">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link active" href="doctor_homepage.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="blog.php">Blog</a></li>
        <li class="nav-item"><a class="nav-link" href="service.html">Services</a></li>
        <li class="nav-item"><a class="nav-link" href="about.html">About</a></li>
      </ul>
      <div class="d-flex ms-3 gap-2">
        <button class="btn white-btn" onclick="window.location.href='doctor_profile.php?action=login'">Profile</button>
        <button class="btn btn-light" onclick="window.location.href='logout.php?action=register'">Log out</button>
      </div>
    </div>
  </div>
</nav>

<div class="profile-container">
  <div style="display: flex; flex-wrap: wrap; justify-content: space-between;">
    <div class="left-section text-center">
      <div class="profile-photo mb-3">
        <?php if (!empty($doctor['image']) && file_exists('uploads/' . $doctor['image'])): ?>
          <img src="<?php echo 'uploads/' . htmlspecialchars($doctor['image']); ?>" alt="Doctor Profile Picture" />
        <?php else: ?>
          <span style="font-size: 80px;">👤</span>
        <?php endif; ?>
      </div>
      <div class="bio text-start">
        <h3>Bio</h3>
        <p><?php echo htmlspecialchars($doctor['bio']); ?></p>
      </div>
    </div>

    <div class="right-section">
      <form action="edit_doctor.html" method="POST">
        <input type="hidden" name="id" value="<?php echo $doctor['id']; ?>" />
        <label class="form-label">Profile Info</label>
        <textarea name="profile_info" rows="6" readonly><?php 
          echo "Dr. " . htmlspecialchars($doctor['firstname'] . ' ' . $doctor['lastname']) . "\n" .
               htmlspecialchars($doctor['specialization']) . "\n" .
               htmlspecialchars($doctor['email']) . "\n" .
               htmlspecialchars($doctor['phone']) . "\n" .
               htmlspecialchars($doctor['chamber']);
        ?></textarea>

        <div class="buttons mt-3">
          <button type="button" class="btn white-btn" onclick="window.location.href='edit_doctorpre.php'">Edit Profile</button>
          <a href="logout.php" class="btn btn-light">Logout</a>
          <a href="delete_doctor.php" class="btn danger" onclick="return confirm('Are you sure you want to delete your account?');">Delete Account</a>
        </div>
      </form>
    </div>
  </div>

  <div class="blog-section">
    <h2>📝 Your Blog Posts</h2>
    <?php if ($post_result->num_rows > 0): ?>
      <?php while ($post = $post_result->fetch_assoc()): ?>
        <div class="blog-post">
          <h3><?php echo htmlspecialchars($post['title']); ?></h3>
          <p><?php echo nl2br(htmlspecialchars(substr($post['content'], 0, 300))); ?>...</p>
          <small>Posted on: <?php echo date("F j, Y, g:i a", strtotime($post['created_at'])); ?></small><br>
          <a href="view_blog.php?id=<?= $post['id'] ?>" class="btn btn-sm">Read More</a>
          <a href="edit_blog.php?id=<?= $post['id'] ?>" class="btn btn-sm">Edit</a>
          <a href="delete_blog.php?id=<?= $post['id'] ?>" class="btn btn-sm danger" onclick="return confirm('Are you sure you want to delete this post?')">Delete</a>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p style="text-align: center; color:#333;">You haven't posted any blogs yet.</p>
    <?php endif; ?>
  </div>
</div>

<!-- Sticky Footer -->
<footer>
  <p>&copy; <?php echo date("Y"); ?> MediConnect. All Rights Reserved.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

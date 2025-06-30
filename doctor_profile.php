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

    .blog-section {
      width: 100%;
      padding: 20px;
      margin-top: 40px;
      background: transparent;
      border-radius: 10px;
    }

    .blog-section h2 {
      text-align: center;
      margin-bottom: 20px;
    }

    .blog-post {
      margin-bottom: 30px;
      padding: 15px;
      background: transparent;
      border-left: 5px solid #0b7dda;
      border-radius: 5px;
    }

    .blog-post h3 {
      margin-bottom: 10px;
    }

    .blog-post p {
      white-space: pre-wrap;
    }

    .blog-post small {
      color: #555;
    }

    .blog-post a.btn {
      display: inline-block;
      margin-top: 10px;
      margin-right: 10px;
      padding: 5px 10px;
      background-color: #0b7dda;
      color: #fff;
      border-radius: 5px;
      text-decoration: none;
      font-size: 14px;
    }

    .blog-post a.btn.danger {
      background-color: #dc3545;
    }

    .blog-post a.btn.white-btn {
      background-color: #6c757d;
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
        <li><a href="blog.php" class="link">Blog</a></li>
        <li><a href="#" class="link">Services</a></li>
        <li><a href="about.html" class="link">About</a></li>
      </ul>
    </div>
    <div class="nav-button">
      <button class="btn white-btn" onclick="window.location.href='doctor_profile.php'">Profile</button>
      <button class="btn" onclick="window.location.href='logout.php'">Logout</button>
    </div>
    <div class="nav-menu-btn">
      <i class="bx bx-menu" onclick="myMenuFunction()"></i>
    </div>
  </nav>
</div>

<div class="profile-container" style="flex-direction: column;">
  <!-- Profile Box -->
  <div style="display: flex; flex-wrap: wrap; justify-content: space-between;">
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
          <button type="button" class="btn white-btn" onclick="window.location.href='edit_doctorpre.php'">Edit Profile</button>
          <a href="logout.php" class="btn">Logout</a>
          <a href="delete_doctor.php" class="btn danger" onclick="return confirm('Are you sure you want to delete your account?');">Delete Account</a>
        </div>
      </form>
    </div>
  </div>

  <!-- Blog Section Right Below -->
  <div class="blog-section">
    <h2>📝 Your Blog Posts</h2>
    <?php if ($post_result->num_rows > 0): ?>
      <?php while ($post = $post_result->fetch_assoc()): ?>
        <div class="blog-post">
          <h3><?php echo htmlspecialchars($post['title']); ?></h3>
          <p><?php echo nl2br(htmlspecialchars(substr($post['content'], 0, 300))); ?>...</p>
          <small>Posted on: <?php echo date("F j, Y, g:i a", strtotime($post['created_at'])); ?></small><br>
          <a href="view_blog.php?id=<?= $post['id'] ?>" class="btn white-btn">Read More</a>
          <a href="edit_blog.php?id=<?= $post['id'] ?>" class="btn">Edit</a>
          <a href="delete_blog.php?id=<?= $post['id'] ?>" class="btn danger" onclick="return confirm('Are you sure you want to delete this post?')">Delete</a>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p style="text-align: center;">You haven't posted any blogs yet.</p>
    <?php endif; ?>
  </div>
</div>

<script src="script.js"></script>
</body>
</html>

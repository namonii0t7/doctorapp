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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $doctor_id = $_SESSION['doctor_id'];

    $stmt = $conn->prepare("INSERT INTO blog_posts (doctor_id, title, content, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iss", $doctor_id, $title, $content);
    
    if ($stmt->execute()) {
        echo "<script>alert('Blog posted successfully!'); window.location.href='doctor_homepage.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Post Your Blog</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #fff;
      color: #000;
      font-family: 'Poppins', sans-serif;
      margin: 0;
      padding: 0;
    }
    .blog-page-wrapper {
      max-width: 800px;
      margin: 120px auto 80px auto; /* space for navbar and footer */
      padding: 20px;
    }
    .blog-form-container {
      background: #000;
      color: #fff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.3);
    }
    .blog-form-container h2 {
      text-align: center;
      margin-bottom: 20px;
    }
    .blog-form-container label {
      display: block;
      margin-top: 15px;
      font-weight: 500;
    }
    .blog-form-container input[type="text"],
    .blog-form-container textarea {
      width: 100%;
      padding: 10px;
      margin-top: 5px;
      border-radius: 8px;
      border: none;
      outline: none;
      font-size: 14px;
    }
    .blog-form-container input[type="text"] {
      height: 40px;
    }
    .blog-form-container textarea {
      resize: vertical;
    }
    .blog-form-container input[type="text"],
    .blog-form-container textarea {
      background: #fff;
      color: #000;
    }
    .blog-form-container .btn {
      margin-top: 20px;
      width: 100%;
      background: #2ecc71;
      color: #fff;
      border: none;
      padding: 10px;
      font-weight: bold;
      border-radius: 8px;
      cursor: pointer;
      transition: 0.3s;
    }
    .blog-form-container .btn:hover {
      background: #27ae60;
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
  <div class="container">
    <a class="navbar-brand fw-bold" href="#">MediConnect</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="mainNavbar">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link active" href="doctor_homepage.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="blog.php">Blog</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Services</a></li>
        <li class="nav-item"><a class="nav-link" href="about.html">About</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- Blog Form -->
<div class="blog-page-wrapper">
  <div class="blog-form-container">
    <form method="POST" action="">
      <h2>Write Your Health Article</h2>

      <label>Title:</label>
      <input type="text" name="title" required>

      <label>Content:</label>
      <textarea name="content" rows="10" required></textarea>

      <button type="submit" class="btn">Publish</button>
    </form>
  </div>
</div>

<!-- Footer -->
<footer class="bg-dark text-white text-center py-3 fixed-bottom">
  <div class="container">
    <p class="mb-1">&copy; 2025 MediConnect. All rights reserved.</p>
    <p class="mb-0">
      <a href="about.html" class="text-white text-decoration-underline">About</a> | 
      <a href="blog.php" class="text-white text-decoration-underline">Blog</a> | 
      <a href="#" class="text-white text-decoration-underline">Services</a>
    </p>
  </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

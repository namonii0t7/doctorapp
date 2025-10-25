<?php
$conn = new mysqli("localhost", "root", "", "docappp");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT blog_posts.*, doctors.firstname, doctors.lastname, doctors.image 
        FROM blog_posts 
        JOIN doctors ON blog_posts.doctor_id = doctors.id 
        ORDER BY blog_posts.created_at DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Health Blogs | MediConnect</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f8f9fa;
      color: #fff;
    }

    /* Navbar */
    .navbar-brand {
      font-weight: bold;
    }

    /* Blog Section */
    .blog-section {
      padding: 100px 20px 50px 20px;
      max-width: 1200px;
      margin: 0 auto;
    }

    .blog-section h2 {
      text-align: center;
      margin-bottom: 50px;
      font-weight: bold;
      color: #000;
    }

    .blog-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 30px;
    }

    .blog-card {
      background: #000;
      color: #fff;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 5px 20px rgba(0,0,0,0.3);
      transition: transform 0.3s, box-shadow 0.3s;
    }

    .blog-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 25px rgba(0,0,0,0.4);
    }

    .blog-author {
      display: flex;
      align-items: center;
      margin-bottom: 15px;
    }

    .blog-author img {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      object-fit: cover;
      margin-right: 10px;
      border: 2px solid #fff;
    }

    .blog-author div strong {
      font-size: 16px;
    }

    .blog-author div span {
      font-size: 13px;
      color: #ccc;
    }

    .blog-card h3 {
      font-size: 20px;
      margin-bottom: 10px;
      color: #fff;
    }

    .blog-card p {
      color: #ddd;
      font-size: 15px;
      white-space: pre-wrap;
      margin-bottom: 15px;
    }

    .blog-card a.btn {
      display: inline-block;
      background: #fff;
      color: #000;
      padding: 8px 14px;
      border-radius: 30px;
      text-decoration: none;
      font-size: 14px;
      font-weight: bold;
      transition: 0.3s;
    }

    .blog-card a.btn:hover {
      background: #000;
      color: #fff;
      border: 2px solid #fff;
    }

    /* Footer */
    footer {
      margin-top: 50px;
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.html">MediConnect</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="mainNavbar">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="index.html">Home</a></li>
        <li class="nav-item"><a class="nav-link active" href="blog.php">Blog</a></li>
        <li class="nav-item"><a class="nav-link" href="service.html">Services</a></li>
        <li class="nav-item"><a class="nav-link" href="about.html">About</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- Blog Section -->
<section class="blog-section">
  <h2>📰 Health Blogs by Our Doctors</h2>
  <div class="blog-grid">
    <?php if ($result->num_rows > 0): ?>
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="blog-card">
          <div class="blog-author">
            <?php if (!empty($row['image']) && file_exists('uploads/' . $row['image'])): ?>
              <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="Doctor Image">
            <?php else: ?>
              <img src="images/default-avatar.png" alt="Default Avatar">
            <?php endif; ?>
            <div>
              <strong>Dr. <?php echo htmlspecialchars($row['firstname'] . ' ' . $row['lastname']); ?></strong><br>
              <span><?php echo date("F j, Y, g:i a", strtotime($row['created_at'])); ?></span>
            </div>
          </div>
          <h3><?php echo htmlspecialchars($row['title']); ?></h3>
          <p><?php echo nl2br(htmlspecialchars(substr($row['content'], 0, 300))); ?>...</p>
          <a href="view_blog.php?id=<?php echo $row['id']; ?>" class="btn">Read More</a>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p style="text-align:center; color:#000;">No blog posts available yet.</p>
    <?php endif; ?>
  </div>
</section>

<!-- Footer -->
<footer class="bg-dark text-white text-center py-3">
  <div class="container">
    <p class="mb-1">&copy; 2025 MediConnect. All rights reserved.</p>
    <p class="mb-0">
      <a href="about.html" class="text-white text-decoration-underline">About</a> | 
      <a href="blog.php" class="text-white text-decoration-underline">Blog</a> | 
      <a href="service.html" class="text-white text-decoration-underline">Services</a>
    </p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

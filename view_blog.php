<?php
if (!isset($_GET['id'])) {
    die("Blog post not found.");
}

$conn = new mysqli("localhost", "root", "", "docappp");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = $_GET['id'];
$sql = "SELECT blog_posts.*, doctors.firstname, doctors.lastname, doctors.image 
        FROM blog_posts 
        JOIN doctors ON blog_posts.doctor_id = doctors.id 
        WHERE blog_posts.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();

if (!$post) {
    die("Blog post not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?php echo htmlspecialchars($post['title']); ?></title>
  <link rel="stylesheet" href="style.css"> 
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #f5f5f5; /* light background for contrast */
      font-family: 'Poppins', sans-serif;
    }

    .blog-container {
      max-width: 900px;
      margin: 130px auto 50px auto;
      background: #000; /* Black box */
      padding: 30px;
      border-radius: 15px;
      color: #fff; /* White text */
      box-shadow: 0px 4px 15px rgba(0,0,0,0.6);
    }

    .blog-header {
      display: flex;
      align-items: center;
      margin-bottom: 20px;
    }

    .blog-header img {
      width: 70px;
      height: 70px;
      border-radius: 50%;
      object-fit: cover;
      margin-right: 15px;
      border: 2px solid #fff;
    }

    .blog-header div {
      line-height: 1.2;
      color: #fff;
    }

    .blog-header strong {
      font-size: 18px;
      color: #fff;
    }

    .blog-header span {
      font-size: 14px;
      color: #ccc; /* slightly lighter */
    }

    .blog-title {
      font-size: 26px;
      font-weight: 600;
      margin-bottom: 20px;
      color: #fff;
    }

    .blog-content {
      white-space: pre-wrap;
      font-size: 16px;
      line-height: 1.6;
      color: #fff;
    }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
     <button class="btn btn-outline-light me-3" onclick="history.back()">← Back</button>
    <a class="navbar-brand fw-bold" href="#">MediConnect</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
      <span class="navbar-toggler-icon"></span>
    </button>
  </div>
</nav>

<div class="blog-container">
  <div class="blog-header">
    <?php if (!empty($post['image']) && file_exists('uploads/' . $post['image'])): ?>
      <img src="<?php echo 'uploads/' . htmlspecialchars($post['image']); ?>" alt="Doctor Image">
    <?php else: ?>
      <img src="images/default-avatar.png" alt="Default Avatar">
    <?php endif; ?>

    <div>
      <strong>Dr. <?php echo htmlspecialchars($post['firstname'] . ' ' . $post['lastname']); ?></strong><br>
      <span>Posted on: <?php echo date("F j, Y, g:i a", strtotime($post['created_at'])); ?></span>
    </div>
  </div>

  <div class="blog-title"><?php echo htmlspecialchars($post['title']); ?></div>

  <div class="blog-content"><?php echo nl2br(htmlspecialchars($post['content'])); ?></div>
</div>

</body>
</html>

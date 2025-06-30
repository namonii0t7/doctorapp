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
  <link rel="stylesheet" href="style.css"> <!-- This is your main CSS -->
  <style>
    .blog-container {
      max-width: 900px;
      margin: 130px auto 50px auto;
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      padding: 30px;
      border-radius: 15px;
      color: #fff;
      font-family: 'Poppins', sans-serif;
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
    }

    .blog-header strong {
      font-size: 18px;
    }

    .blog-header span {
      font-size: 14px;
      color: #ddd;
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
      color: #f1f1f1;
    }
  </style>
</head>
<body>

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

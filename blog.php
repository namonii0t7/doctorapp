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
  <title>All Blog Posts</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css"> 
  <style>
    .blog-container {
      max-width: 1000px;
      margin: 130px auto 50px auto;
      padding: 30px;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 15px;
      backdrop-filter: blur(10px);
      color: #fff;
      font-family: 'Poppins', sans-serif;
    }

    .blog-container h2 {
      text-align: center;
      margin-bottom: 30px;
      color: #fff;
    }

    .blog-post {
      margin-bottom: 30px;
      padding: 20px;
      background: rgba(255, 255, 255, 0.15);
      border-left: 5px solid #0b7dda;
      border-radius: 10px;
    }

    .blog-author {
      display: flex;
      align-items: center;
      margin-bottom: 10px;
    }

    .blog-author img {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      object-fit: cover;
      margin-right: 10px;
      border: 2px solid #fff;
    }

    .blog-author div {
      line-height: 1.2;
    }

    .blog-author strong {
      font-size: 16px;
    }

    .blog-author span {
      font-size: 13px;
      color: #ddd;
    }

    .blog-post h3 {
      margin-bottom: 10px;
      font-size: 20px;
      color: #fff;
    }

    .blog-post p {
      color: #f1f1f1;
      white-space: pre-wrap;
      font-size: 15px;
      margin-bottom: 10px;
    }

    .blog-post a.btn {
      display: inline-block;
      background: #0b7dda;
      color: #fff;
      padding: 8px 14px;
      border-radius: 30px;
      text-decoration: none;
      font-size: 14px;
    }

    .blog-post a.btn:hover {
      background: #095caa;
    }
  </style>
</head>
<body>

<div class="blog-container">
  <h2>📰 Health Blogs by Our Doctors</h2>

  <?php if ($result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
      <div class="blog-post">
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
    <p style="text-align:center;">No blog posts available yet.</p>
  <?php endif; ?>
</div>

</body>
</html>

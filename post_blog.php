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
  <link rel="stylesheet" href="profile.css"> <!-- reuse your CSS -->
</head>
<body>
  <div class="wrapper">
    <h2>Write Your Health Article</h2>
    <form method="POST" action="">
      <label>Title:</label><br>
      <input type="text" name="title" required style="width:100%; padding:10px;"><br><br>

      <label>Content:</label><br>
      <textarea name="content" rows="10" required style="width:100%; padding:10px;"></textarea><br><br>

      <button type="submit" class="btn">Publish</button>
    </form>
  </div>
</body>
</html>

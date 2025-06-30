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
$post_id = $_GET['id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];

    $stmt = $conn->prepare("UPDATE blog_posts SET title = ?, content = ? WHERE id = ? AND doctor_id = ?");
    $stmt->bind_param("ssii", $title, $content, $post_id, $doctor_id);
    $stmt->execute();

    header("Location: doctor_profile.php");
    exit();
}

// Load post
$stmt = $conn->prepare("SELECT * FROM blog_posts WHERE id = ? AND doctor_id = ?");
$stmt->bind_param("ii", $post_id, $doctor_id);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();

if (!$post) {
    echo "Post not found.";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Blog</title>
    <link rel="stylesheet" href="profile.css">
</head>
<body>
    <div class="wrapper" style="max-width: 700px; margin: 50px auto;">
        <h2>Edit Blog Post</h2>
        <form method="POST">
            <label>Title</label><br>
            <input type="text" name="title" value="<?= htmlspecialchars($post['title']) ?>" style="width:100%; padding:10px;"><br><br>
            <label>Content</label><br>
            <textarea name="content" rows="10" style="width:100%; padding:10px;"><?= htmlspecialchars($post['content']) ?></textarea><br><br>
            <button type="submit" class="btn">Update Post</button>
            <a href="doctor_profile.php" class="btn white-btn">Cancel</a>
        </form>
    </div>
</body>
</html>

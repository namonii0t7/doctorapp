<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: logreg.html");
    exit();
}

$conn = new mysqli("localhost", "root", "", "docappp");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT * FROM users WHERE id = $user_id");
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="profile.css"> 
</head>
<body>

      <nav class="navbar navbar-expand-lg navbar-dark bg-transparent fixed-top mt-3">
  <div class="container-fluid">
     <button class="btn btn-outline-light me-3" onclick="history.back()">← Back</button>
    <a class="navbar-brand" href="#">MediConnect .</a>

    <!-- Burger Button -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Nav links -->
    <div class="collapse navbar-collapse" id="mainNavbar">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link active" href="user_homepage.html">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="blog.php">Blog</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Services</a></li>
        <li class="nav-item"><a class="nav-link" href="about.html">About</a></li>
      </ul>

    </div>
  </div>
</nav>>

    <!-- Profile Section -->
    <div class="profile-container">
        <div class="left-section">
            <div class="profile-photo">👤</div>
            <div class="bio">
                <p><strong><?= htmlspecialchars($user['firstname']) ?> <?= htmlspecialchars($user['lastname']) ?></strong></p>
            </div>
        </div>

        <div class="right-section">
            <form action="edit_user.php" method="POST">
                <input type="hidden" name="id" value="<?= $user['id'] ?>">

                <label>First Name</label>
                <input type="text" name="firstname" value="<?= htmlspecialchars($user['firstname']) ?>" required>

                <label>Last Name</label>
                <input type="text" name="lastname" value="<?= htmlspecialchars($user['lastname']) ?>" required>

                <label>Email</label>
                <input type="email" value="<?= htmlspecialchars($user['email']) ?>" readonly>

                <div class="buttons">
                    <button type="submit" class="btn">Update</button>
                    <a href="delete_user.php" class="btn danger" onclick="return confirm('Are you sure you want to delete your account?')">Delete Account</a>
                </div>
            </form>
        </div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


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
    <link rel="stylesheet" href="profile.css"> <!-- Use your merged style -->
</head>
<body>

    <!-- Navigation (if needed) -->
    <nav class="nav">
        <div class="nav-logo">
            <p>Doctor App</p>
        </div>
        <div class="nav-menu" id="navMenu">
            <ul>
                <li><a href="home.html" class="link active">Home</a></li>
                <li><a href="blog.php" class="link">Blog</a></li>
                <li><a href="#" class="link">About</a></li>
            </ul>
        </div>
    </nav>

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

</body>
</html>


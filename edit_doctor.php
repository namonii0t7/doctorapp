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

$id         = $_SESSION['doctor_id'];
$firstname  = $conn->real_escape_string($_POST['firstname']);
$lastname   = $conn->real_escape_string($_POST['lastname']);
$email      = $conn->real_escape_string($_POST['email']);
$phone      = $conn->real_escape_string($_POST['phone']);
$chamber    = $conn->real_escape_string($_POST['chamber']);
$bio        = $conn->real_escape_string($_POST['bio']);

$image_name = null;

if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    $file_name = $_FILES['profile_image']['name'];
    $file_tmp = $_FILES['profile_image']['tmp_name'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    if (in_array($file_ext, $allowed)) {
        $image_name = 'doctor_' . $id . '_' . time() . '.' . $file_ext;
        $upload_dir = 'uploads/';
        
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $upload_path = $upload_dir . $image_name;

        if (!move_uploaded_file($file_tmp, $upload_path)) {
            echo "<script>alert('Failed to upload profile picture.'); window.history.back();</script>";
            exit();
        }
    } else {
        echo "<script>alert('Invalid file type. Allowed: jpg, jpeg, png, gif.'); window.history.back();</script>";
        exit();
    }
}

// Build update query
if ($image_name) {
    $sql = "UPDATE doctors SET 
            firstname = '$firstname', 
            lastname = '$lastname', 
            email = '$email', 
            phone = '$phone', 
            chamber = '$chamber', 
            bio = '$bio', 
            image = '$image_name' 
        WHERE id = $id";
} else {
    $sql = "UPDATE doctors SET 
            firstname = '$firstname', 
            lastname = '$lastname', 
            email = '$email', 
            phone = '$phone', 
            chamber = '$chamber', 
            bio = '$bio' 
        WHERE id = $id";
}

if ($conn->query($sql) === TRUE) {
    echo "<script>alert('Profile updated successfully!'); window.location.href='doctor_profile.php';</script>";
} else {
    echo "Error updating profile: " . $conn->error;
}

$conn->close();
?>

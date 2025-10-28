<?php
session_start();
$conn = new mysqli("localhost", "root", "", "docappp");

if (!isset($_SESSION['user_id'])) {
    header("Location: user_login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success = $error = '';
$search = "";

// Handle prescription upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['pharmacy_id'])) {
    $pharmacy_id = $_POST['pharmacy_id'];
    $file = $_FILES['prescription'];

    if ($file['error'] == 0) {
        $filename = 'uploads/' . time() . '_' . $file['name'];
        move_uploaded_file($file['tmp_name'], $filename);

        $stmt = $conn->prepare("INSERT INTO medicine_orders (user_id, pharmacy_id, prescription) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $user_id, $pharmacy_id, $filename);

        if ($stmt->execute()) $success = "Prescription uploaded successfully! Pharmacist will approve your prescription if they have medicine available";
        else $error = "Something went wrong.";
    } else $error = "File upload failed!";
}

// Handle search query
if (isset($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $pharmacies = $conn->query("SELECT * FROM pharmacies WHERE status='active' AND (name LIKE '%$search%' OR location LIKE '%$search%')");
} else {
    $pharmacies = $conn->query("SELECT * FROM pharmacies WHERE status='active'");
}

// Fetch current user's orders
$myOrders = $conn->query("
    SELECT o.*, p.name AS pharmacy_name, p.location AS pharmacy_location
    FROM medicine_orders o
    JOIN pharmacies p ON o.pharmacy_id = p.id
    WHERE o.user_id = $user_id
    ORDER BY o.id DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Medicine Corner</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
.navbar-search {
    width: 300px;
}
</style>
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark">
  <div class="container-fluid">
     <button class="btn btn-outline-light me-3" onclick="history.back()">← Back</button>
    <a class="navbar-brand fw-bold" href="#">MediConnect</a>

    <!-- Search Form -->
    <form class="d-flex" method="GET" action="" style="width: 350px;">
        <input class="form-control me-2 navbar-search" type="search" name="search" placeholder="Search pharmacy by name or location" value="<?= htmlspecialchars($search) ?>">
        <button class="btn btn-outline-light" type="submit">Search</button>
    </form>

    <div>
      <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
    </div>
  </div>
</nav>

<div class="container mt-4">

    <h2 class="text-center mb-4">Upload Prescription</h2>

    <?php if($success) echo "<div class='alert alert-success'>$success</div>"; ?>
    <?php if($error) echo "<div class='alert alert-danger'>$error</div>"; ?>

    <?php if ($search): ?>
        <h5 class="text-center mb-4">Showing results for "<span class="text-primary"><?= htmlspecialchars($search) ?></span>"</h5>
    <?php endif; ?>

    <div class="row">
    <?php if ($pharmacies->num_rows > 0): ?>
        <?php while($row = $pharmacies->fetch_assoc()): ?>
            <div class="col-md-6 mb-4">
                <div class="card shadow">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($row['name']) ?></h5>
                        <p><b>Location:</b> <?= htmlspecialchars($row['location']) ?></p>
                        <p>Status: 
                            <?php if($row['status']=='active'): ?>
                                <span class="badge bg-success">Active</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Inactive</span>
                            <?php endif; ?>
                        </p>

                        <?php if($row['status']=='active'): ?>
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="pharmacy_id" value="<?= $row['id'] ?>">

                            <div class="mb-2">
                                <label class="form-label">Upload Prescription</label>
                                <input type="file" name="prescription" class="form-control" required>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Submit Prescription</button>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p class="text-center text-muted">No pharmacies found matching your search.</p>
    <?php endif; ?>
    </div>

    <h3 class="mt-5 mb-3 text-center">My Prescriptions</h3>
    <?php if ($myOrders->num_rows > 0): ?>
        <div class="table-responsive">
        <table class="table table-bordered bg-white">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Pharmacy</th>
                    <th>Location</th>
                    <th>Prescription</th>
                    <th>Status</th>
                    <th>Uploaded On</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($o = $myOrders->fetch_assoc()): ?>
                <tr>
                    <td><?= $o['id'] ?></td>
                    <td><?= htmlspecialchars($o['pharmacy_name']) ?></td>
                    <td><?= htmlspecialchars($o['pharmacy_location']) ?></td>
                    <td><a href="<?= htmlspecialchars($o['prescription']) ?>" target="_blank">View</a></td>
                    <td>
                        <?php if ($o['status'] == 'Pending'): ?>
                            <span class="badge bg-warning">Pending</span>
                        <?php elseif ($o['status'] == 'Accepted'): ?>
                            <span class="badge bg-success">Accepted</span>
                        <?php elseif ($o['status'] == 'Rejected'): ?>
                            <span class="badge bg-danger">Rejected</span>
                        <?php elseif ($o['status'] == 'Ready'): ?>
                            <span class="badge bg-info">Ready</span>
                        <?php endif; ?>
                    </td>
                    <td><?= $o['created_at'] ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        </div>
    <?php else: ?>
        <p class="text-center">No prescriptions uploaded yet.</p>
    <?php endif; ?>

</div>

<footer class="bg-dark text-white text-center py-3 mt-5 fixed-bottom">
    &copy; <?=date('Y')?> MediConnect. All rights reserved.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

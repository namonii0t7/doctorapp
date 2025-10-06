<?php
session_start();
$conn = new mysqli("localhost", "root", "", "docappp");

if (!isset($_SESSION['user_id'])) {
    header("Location: user_login.php");
    exit();
}

$patient_id = $_SESSION['user_id'];
$success = $error = '';
$search = "";

// Handle booking submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['driver_id'])) {
    $driver_id = $_POST['driver_id'];
    $pickup = $_POST['pickup'];
    $drop = $_POST['drop'];
    $emergency = $_POST['emergency_type'];
    $time = $_POST['booking_time'];

    $stmt = $conn->prepare("INSERT INTO ambulance_bookings(patient_id, driver_id, pickup, drop_location, emergency_type, booking_time, status) VALUES (?, ?, ?, ?, ?, ?, 'Pending')");
    $stmt->bind_param("iissss", $patient_id, $driver_id, $pickup, $drop, $emergency, $time);
    if ($stmt->execute()) {
        $success = "Booking request sent! Please wait for driver confirmation.";
    } else {
        $error = "Something went wrong.";
    }
}

// Handle search query
if (isset($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $drivers = $conn->query("SELECT * FROM drivers WHERE name LIKE '%$search%' OR location LIKE '%$search%'");
} else {
    $drivers = $conn->query("SELECT * FROM drivers");
}

// Fetch current user's bookings with driver info
$myBookings = $conn->query("
    SELECT b.*, d.name AS driver_name, d.phone AS driver_phone, d.location AS driver_location
    FROM ambulance_bookings b
    JOIN drivers d ON b.driver_id = d.id
    WHERE b.patient_id = $patient_id
    ORDER BY b.id DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Book Ambulance</title>
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
    <a class="navbar-brand fw-bold" href="#">MediConnect</a>

    <!-- Search Bar -->
    <form class="d-flex" method="GET" action="" style="width: 350px;">
        <input class="form-control me-2 navbar-search" type="search" name="search" placeholder="Search ambulance by name or location" value="<?= htmlspecialchars($search) ?>">
        <button class="btn btn-outline-light" type="submit">Search</button>
    </form>

    <div>
      <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
    </div>
  </div>
</nav>

<div class="container mt-4">

    <h2 class="text-center mb-4">Book Ambulance</h2>

    <?php if($success) echo "<div class='alert alert-success'>$success</div>"; ?>
    <?php if($error) echo "<div class='alert alert-danger'>$error</div>"; ?>

    <?php if ($search): ?>
        <h5 class="text-center mb-4">Showing results for "<span class="text-primary"><?= htmlspecialchars($search) ?></span>"</h5>
    <?php endif; ?>

    <div class="row">
    <?php if ($drivers->num_rows > 0): ?>
        <?php while($row = $drivers->fetch_assoc()): ?>
            <div class="col-md-6 mb-4">
                <div class="card shadow">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($row['name']) ?></h5>
                        <p><b>Phone:</b> <?= htmlspecialchars($row['phone']) ?></p>
                        <p><b>Location:</b> <?= htmlspecialchars($row['location']) ?></p>
                        <p>Status: 
                            <?php if($row['status']=='available'): ?>
                                <span class="badge bg-success">Available</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Busy</span>
                            <?php endif; ?>
                        </p>

                        <?php if($row['status']=='available'): ?>
                        <form method="POST">
                            <input type="hidden" name="driver_id" value="<?= $row['id'] ?>">

                            <div class="mb-2">
                                <label class="form-label">Pickup Location</label>
                                <input type="text" name="pickup" class="form-control" required>
                            </div>

                            <div class="mb-2">
                                <label class="form-label">Drop Location</label>
                                <input type="text" name="drop" class="form-control" required>
                            </div>

                            <div class="mb-2">
                                <label class="form-label">Emergency Type</label>
                                <select name="emergency_type" class="form-select">
                                    <option value="Normal">Normal</option>
                                    <option value="Critical">Critical</option>
                                    <option value="ICU">ICU</option>
                                </select>
                            </div>

                            <div class="mb-2">
                                <label class="form-label">Booking Date & Time</label>
                                <input type="datetime-local" name="booking_time" class="form-control" required>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Book Ambulance</button>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p class="text-center text-muted">No ambulances found matching your search.</p>
    <?php endif; ?>
    </div>

    <h3 class="mt-5 mb-3 text-center">My Ambulance Bookings</h3>
    <?php if ($myBookings->num_rows > 0): ?>
        <div class="table-responsive">
        <table class="table table-bordered bg-white">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Driver</th>
                    <th>Phone</th>
                    <th>Pickup</th>
                    <th>Drop</th>
                    <th>Emergency</th>
                    <th>Time</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($b = $myBookings->fetch_assoc()): ?>
                <tr>
                    <td><?= $b['id'] ?></td>
                    <td><?= htmlspecialchars($b['driver_name']) ?></td>
                    <td><?= htmlspecialchars($b['driver_phone']) ?></td>
                    <td><?= htmlspecialchars($b['pickup']) ?></td>
                    <td><?= htmlspecialchars($b['drop_location']) ?></td>
                    <td><?= htmlspecialchars($b['emergency_type']) ?></td>
                    <td><?= htmlspecialchars($b['booking_time']) ?></td>
                    <td>
                        <?php if ($b['status'] == 'Pending'): ?>
                            <span class="badge bg-warning">Pending</span>
                        <?php elseif ($b['status'] == 'Accepted'): ?>
                            <span class="badge bg-success">Accepted</span>
                        <?php elseif ($b['status'] == 'On the Way'): ?>
                            <span class="badge bg-info">On the Way</span>
                        <?php elseif ($b['status'] == 'Completed'): ?>
                            <span class="badge bg-primary">Completed</span>
                        <?php else: ?>
                            <span class="badge bg-danger">Rejected</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        </div>
    <?php else: ?>
        <p class="text-center">No bookings yet.</p>
    <?php endif; ?>

</div>

<footer class="bg-dark text-white text-center py-3 mt-5">
    &copy; <?=date('Y')?> MediConnect. All rights reserved.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

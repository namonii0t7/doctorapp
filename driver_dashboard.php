<?php
session_start();
if (!isset($_SESSION['driver_id'])) {
    header("Location: driver_login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "docappp");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$driver_id = $_SESSION['driver_id'];

// Handle booking actions
if (isset($_GET['action'], $_GET['id'])) {
    $action = $_GET['action'];
    $id = intval($_GET['id']); 

    if ($action == 'accept') {
        // Only accept if driver is available
        $stmtCheck = $conn->prepare("SELECT status FROM drivers WHERE id=?");
        $stmtCheck->bind_param("i", $driver_id);
        $stmtCheck->execute();
        $driverStatus = $stmtCheck->get_result()->fetch_assoc();

        if (strtolower($driverStatus['status']) == 'available') {
            // Assign booking
            $stmt = $conn->prepare("UPDATE ambulance_bookings SET status='Accepted', driver_id=? WHERE id=?");
            $stmt->bind_param("ii", $driver_id, $id);
            $stmt->execute();

            // Mark driver busy
            $stmt2 = $conn->prepare("UPDATE drivers SET status='Busy' WHERE id=?");
            $stmt2->bind_param("i", $driver_id);
            $stmt2->execute();
        }
    } elseif ($action == 'reject') {
        $stmt = $conn->prepare("UPDATE ambulance_bookings SET status='Rejected' WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $stmt2 = $conn->prepare("UPDATE drivers SET status='Available' WHERE id=?");
        $stmt2->bind_param("i", $driver_id);
        $stmt2->execute();
    } elseif ($action == 'ontheway') {
        $stmt = $conn->prepare("UPDATE ambulance_bookings SET status='On the Way' WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    } elseif ($action == 'complete') {
        $stmt = $conn->prepare("UPDATE ambulance_bookings SET status='Completed' WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        // Make driver available again
        $stmt2 = $conn->prepare("UPDATE drivers SET status='Available' WHERE id=?");
        $stmt2->bind_param("i", $driver_id);
        $stmt2->execute();
    }

    // Refresh page to reflect changes
    header("Location: driver_dashboard.php");
    exit();
}

// Fetch pending bookings
$pending = $conn->query("
    SELECT b.*, CONCAT(u.firstname, ' ', u.lastname) AS patient_name
    FROM ambulance_bookings b
    JOIN users u ON b.patient_id = u.id
    WHERE b.status='Pending'
");

// Fetch my bookings
$myBookings = $conn->query("
    SELECT b.*, CONCAT(u.firstname, ' ', u.lastname) AS patient_name
    FROM ambulance_bookings b
    JOIN users u ON b.patient_id = u.id
    WHERE b.driver_id=$driver_id AND b.status!='Pending'
    ORDER BY b.id DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Driver Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { 
    font-family:'Poppins',sans-serif;
     background:#f8f9fa; 
     color:#000; 
    }
.wrapper { 
    max-width: 1000px;
     margin: 100px auto;
      padding: 20px; 
    }
.dashboard-box { 
    background: #fff; 
    padding: 20px;
     border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1); 
      margin-bottom: 30px; 
    }
.dashboard-box h2 { 
    text-align:center;
     margin-bottom:20px; 
    }
table {
     width: 100%; 
     border-collapse: collapse; }
table th, table td { 
    padding: 12px;
     border: 1px solid #dee2e6; 
     text-align: center; 
    }
table th { 
    background: #343a40; 
    color: #fff; 
}
button {
     padding: 6px 12px;
      border: none; 
      border-radius: 5px;
       font-weight: bold; 
       cursor: pointer; 
    }
button.accept { 
    background: #2ecc71; 
    color:#fff; 
}
button.reject { 
    background: #e74c3c; 
    color:#fff; 
}
button.ontheway { 
    background: #3498db; 
    color:#fff; 
}
button.complete { 
    background: #f1c40f; 
    color:#000; }
</style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
  <div class="container">
     <button class="btn btn-outline-light me-3" onclick="history.back()">← Back</button>
    <a class="navbar-brand fw-bold" href="#">MediConnect</a>
    <div class="ms-auto">
      <a class="btn btn-light" href="logout.php">Logout</a>
    </div>
  </div>
</nav>

<div class="wrapper">

    <div class="dashboard-box">
        <h2>Pending Bookings</h2>
        <?php if ($pending->num_rows > 0): ?>
        <table class="table table-bordered">
            <tr>
                <th>ID</th><th>Patient</th><th>Pickup</th><th>Drop</th><th>Emergency</th><th>Booking Time</th><th>Actions</th>
            </tr>
            <?php while ($row = $pending->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['patient_name']) ?></td>
                <td><?= htmlspecialchars($row['pickup']) ?></td>
                <td><?= htmlspecialchars($row['drop_location']) ?></td>
                <td><?= htmlspecialchars($row['emergency_type']) ?></td>
                <td><?= htmlspecialchars($row['booking_time']) ?></td>
                <td>
                    <a href="?action=accept&id=<?= $row['id'] ?>" class="btn accept btn-sm">Accept</a>
                    <a href="?action=reject&id=<?= $row['id'] ?>" class="btn reject btn-sm">Reject</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
        <?php else: ?>
            <p class="text-center">No pending bookings.</p>
        <?php endif; ?>
    </div>

    <div class="dashboard-box">
        <h2>My Bookings</h2>
        <?php if ($myBookings->num_rows > 0): ?>
        <table class="table table-bordered">
            <tr>
                <th>ID</th><th>Patient</th><th>Pickup</th><th>Drop</th><th>Status</th><th>Actions</th>
            </tr>
            <?php while ($row = $myBookings->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['patient_name']) ?></td>
                <td><?= htmlspecialchars($row['pickup']) ?></td>
                <td><?= htmlspecialchars($row['drop_location']) ?></td>
                <td><?= htmlspecialchars($row['status']) ?></td>
                <td>
                    <?php if ($row['status']=='Accepted'): ?>
                        <a href="?action=ontheway&id=<?= $row['id'] ?>" class="btn ontheway btn-sm">On the Way</a>
                    <?php elseif ($row['status']=='On the Way'): ?>
                        <a href="?action=complete&id=<?= $row['id'] ?>" class="btn complete btn-sm">Completed</a>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
        <?php else: ?>
            <p class="text-center">No bookings found.</p>
        <?php endif; ?>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>

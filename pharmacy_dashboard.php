<?php
session_start();
if(!isset($_SESSION['pharmacy_id'])){
    header("Location: pharmacy_login_register.php");
    exit;
}

$conn = new mysqli("localhost","root","","docappp");

$pharmacy_id = $_SESSION['pharmacy_id'];

// Handle order actions
if(isset($_GET['action'],$_GET['order'])){
    $action = $_GET['action'];
    $order_id = $_GET['order'];
    if(in_array($action,['Accepted','Rejected','Ready'])){
        $stmt = $conn->prepare("UPDATE medicine_orders SET status=? WHERE id=? AND pharmacy_id=?");
        $stmt->bind_param("sii",$action,$order_id,$pharmacy_id);
        $stmt->execute();
    }
}

// Fetch orders for this pharmacy
$orders = $conn->query("
    SELECT o.*, u.firstname, u.lastname 
    FROM medicine_orders o
    JOIN users u ON o.user_id = u.id
    WHERE o.pharmacy_id = $pharmacy_id
    ORDER BY o.id DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Pharmacy Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark">
  <div class="container-fluid">
     <button class="btn btn-outline-light me-3" onclick="history.back()">← Back</button>
    <a class="navbar-brand" href="#">MediConnect Pharmacy</a>
    <div>
      <span class="text-white me-3"><?= $_SESSION['pharmacy_name'] ?></span>
      <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
    </div>
  </div>
</nav>

<div class="container mt-4">
<h2 class="mb-4">Prescription Orders</h2>
<?php if($orders->num_rows > 0): ?>
<table class="table table-bordered bg-white">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>User</th>
            <th>Prescription</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php while($row = $orders->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['firstname'].' '.$row['lastname']) ?></td>
            <td><a href="<?= htmlspecialchars($row['prescription']) ?>" target="_blank">View</a></td>
            <td><?= $row['status'] ?></td>
            <td>
                <?php if($row['status']=='Pending'): ?>
                    <a href="?action=Accepted&order=<?= $row['id'] ?>" class="btn btn-success btn-sm">Accept</a>
                    <a href="?action=Rejected&order=<?= $row['id'] ?>" class="btn btn-danger btn-sm">Reject</a>
                <?php elseif($row['status']=='Accepted'): ?>
                    <a href="?action=Ready&order=<?= $row['id'] ?>" class="btn btn-primary btn-sm">Ready</a>
                <?php else: ?>
                    <span class="text-muted">No actions</span>
                <?php endif; ?>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>
<?php else: ?>
<p class="text-center">No prescription orders yet.</p>
<?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

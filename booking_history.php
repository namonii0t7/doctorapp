<?php
session_start();
$conn = new mysqli("localhost","root","","docappp");
$patient_id=1; // replace with logged-in patient id

$bookings=$conn->query("SELECT b.*, d.name as driver_name FROM ambulance_bookings b LEFT JOIN drivers d ON b.driver_id=d.id WHERE patient_id=$patient_id ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Booking History</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body{font-family:'Poppins',sans-serif;background:#f9f9f9;margin:0;padding:0;}
nav.navbar{background:#000 !important;}
nav.navbar a{color:#fff !important;}
.container{max-width:900px;margin:120px auto;}
.table th, .table td{vertical-align:middle;}
.status-Pending{color:#f39c12;font-weight:bold;}
.status-Accepted{color:#2ecc71;font-weight:bold;}
.status-On\ the\ Way{color:#3498db;font-weight:bold;}
.status-Completed{color:#27ae60;font-weight:bold;}
.status-Rejected{color:#e74c3c;font-weight:bold;}
</style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
<div class="container">
<a class="navbar-brand fw-bold" href="#">MediConnect</a>
</div>
</nav>

<div class="container">
<h2>My Ambulance Bookings</h2>
<table class="table table-bordered table-striped">
<tr>
<th>ID</th><th>Pickup</th><th>Drop</th><th>Driver</th><th>Status</th><th>Booking Time</th>
</tr>
<?php while($row=$bookings->fetch_assoc()): ?>
<tr>
<td><?=$row['id']?></td>
<td><?=$row['pickup']?></td>
<td><?=$row['drop_location']?></td>
<td><?=$row['driver_name']??'Not Assigned'?></td>
<td class="status-<?=$row['status']?>"><?=$row['status']?></td>
<td><?=$row['booking_time']?></td>
</tr>
<?php endwhile; ?>
</table>
</div>
</body>
</html>

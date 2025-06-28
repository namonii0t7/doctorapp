<?php
session_start();
if (!isset($_SESSION['doctor_id'])) {
    header("Location: doclog.html");
    exit();
}

require 'vendor/autoload.php';
require 'config.php'; // contains EMAIL_USERNAME and EMAIL_PASSWORD
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$conn = new mysqli("localhost", "root", "", "docappp");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$alertMessage = "";

// Handle approve/reject actions
if (
    $_SERVER['REQUEST_METHOD'] === 'POST' && 
    isset($_POST['action'], $_POST['user_email'], $_POST['appointment_id'], $_POST['user_name'], $_POST['patient_start'], $_POST['patient_end'], $_POST['schedule_date'])
) {
    $action = $_POST['action'];
    $appointment_id = $_POST['appointment_id'];
    $user_email = $_POST['user_email'];
    $user_name = $_POST['user_name'];
    $patient_start = $_POST['patient_start'];
    $patient_end = $_POST['patient_end'];
    $schedule_date = $_POST['schedule_date'];

    $status = $action === 'approve' ? 'approved' : 'rejected';
    $stmt = $conn->prepare("UPDATE appointments SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $appointment_id);
    $stmt->execute();

    // Send email
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = EMAIL_USERNAME;
        $mail->Password = EMAIL_PASSWORD;
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom(EMAIL_USERNAME, 'MediConnect');
        $mail->addAddress($user_email, $user_name);
        $mail->isHTML(true);

        if ($status === 'approved') {
            $mail->Subject = 'Appointment Confirmed';
            $mail->Body = "Dear $user_name,<br><br>Your appointment has been <strong>approved</strong>.<br>Your schedule is on <strong>$schedule_date</strong> from <strong>$patient_start to $patient_end</strong>.<br>Please arrive on time.<br><br>Thank you.";
            $alertMessage = "Appointment approved and confirmation email sent.";
        } else {
            $mail->Subject = 'Appointment Rejected';
            $mail->Body = "Dear $user_name,<br><br>We regret to inform you that your appointment has been <strong>rejected</strong>.<br>Please try booking another time.<br><br>Thank you.";
            $alertMessage = "Appointment rejected and notification email sent.";
        }

        $mail->send();
    } catch (Exception $e) {
        $alertMessage = "Appointment status updated, but email failed: {$mail->ErrorInfo}";
    }
}

$sql = "SELECT 
            a.id AS appointment_id,
            u.firstname AS patient_firstname,
            u.lastname AS patient_lastname,
            u.email AS user_email,
            s.id AS schedule_id,
            s.date,
            s.start_time AS schedule_start,
            s.end_time AS schedule_end,
            s.max_patients,
            a.status,
            bp.payer_number,
            bp.trxid
        FROM appointments a
        JOIN users u ON a.user_id = u.id
        JOIN schedules s ON a.schedule_id = s.id
        LEFT JOIN bkash_payments bp ON bp.schedule_id = s.id AND bp.user_id = u.id
        WHERE s.doctor_id = ?
        ORDER BY s.date, s.start_time, a.id";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['doctor_id']);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>View Appointments</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="style.css" />
</head>
<body>

<?php if ($alertMessage): ?>
  <script>
    alert("<?= $alertMessage ?>");
    window.location.href = window.location.href; // refresh to update status
  </script>
<?php endif; ?>

<div class="wrapper">
  <nav class="nav">
    <div class="nav-logo"><p>MediConnect .</p></div>
    <div class="nav-menu" id="navMenu">
      <ul>
        <li><a href="doctor_homepage.php" class="link">Home</a></li>
        <li><a href="#" class="link">Blog</a></li>
        <li><a href="#" class="link">Services</a></li>
        <li><a href="#" class="link">About</a></li>
      </ul>
    </div>
    <div class="nav-button">
      <button class="btn white-btn" onclick="window.location.href='logout.php'">Logout</button>
    </div>
    <div class="nav-menu-btn">
      <i class="bx bx-menu" onclick="myMenuFunction()"></i>
    </div>
  </nav>

  <div class="schedule-box">
    <h2>Your Appointments</h2>

    <?php if ($result->num_rows > 0): ?>
      <table>
        <tr>
          <th>Patient Name</th>
          <th>Date</th>
          <th>Start Time</th>
          <th>End Time</th>
          <th>Payer Number</th>
          <th>Transaction ID</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
        <?php 
        $slotTracker = [];
        while ($row = $result->fetch_assoc()): 
            $userFullName = htmlspecialchars($row['patient_firstname'] . ' ' . $row['patient_lastname']);
            $schedule_id = $row['schedule_id'];

            if (!isset($slotTracker[$schedule_id])) $slotTracker[$schedule_id] = 0;

            $start = new DateTime($row['schedule_start']);
            $end = new DateTime($row['schedule_end']);
            $interval = $start->diff($end);
            $total_minutes = ($interval->h * 60) + $interval->i;
            $slot_minutes = floor($total_minutes / $row['max_patients']);

            $current_index = $slotTracker[$schedule_id]++;
            $slot_start = clone $start;
            $slot_start->modify("+".($current_index * $slot_minutes)." minutes");
            $slot_end = clone $slot_start;
            $slot_end->modify("+{$slot_minutes} minutes");

            $patient_start = $slot_start->format("g:i A");
            $patient_end = $slot_end->format("g:i A");
            $schedule_date = htmlspecialchars($row['date']);
        ?>
          <tr>
            <td><?= $userFullName ?></td>
            <td><?= $schedule_date ?></td>
            <td><?= $patient_start ?></td>
            <td><?= $patient_end ?></td>
            <td><?= htmlspecialchars($row['payer_number'] ?? 'N/A') ?></td>
            <td><?= htmlspecialchars($row['trxid'] ?? 'N/A') ?></td>
            <td><?= htmlspecialchars($row['status'] ?? 'pending') ?></td>
            <td>
              <?php if ($row['status'] === 'pending'): ?>
                <form method="post" style="display:inline;">
                  <input type="hidden" name="appointment_id" value="<?= $row['appointment_id'] ?>">
                  <input type="hidden" name="user_email" value="<?= htmlspecialchars($row['user_email']) ?>">
                  <input type="hidden" name="user_name" value="<?= $userFullName ?>">
                  <input type="hidden" name="patient_start" value="<?= $patient_start ?>">
                  <input type="hidden" name="patient_end" value="<?= $patient_end ?>">
                  <input type="hidden" name="schedule_date" value="<?= $schedule_date ?>">
                  <button type="submit" name="action" value="approve">Approve</button>
                  <button type="submit" name="action" value="reject" style="background:red;color:white;">Reject</button>
                </form>
              <?php else: ?>
                <?= ucfirst($row['status']) ?>
              <?php endif; ?>
            </td>
          </tr>
        <?php endwhile; ?>
      </table>
    <?php else: ?>
      <p style="text-align:center; color:white;">You have no appointments.</p>
    <?php endif; ?>
  </div>
</div>
</body>
</html>

<?php $conn->close(); ?>
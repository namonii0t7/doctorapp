<?php
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "docappp");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in and form was submitted
if (!isset($_SESSION['user_id']) || !isset($_POST['schedule_id'])) {
    header("Location: login.php"); // make sure this matches your actual login file
    exit();
}

$user_id = $_SESSION['user_id'];
$name = $_SESSION['name'];
$email = $_SESSION['email'];
$schedule_id = $_POST['schedule_id'];

// Check if user has already booked an appointment with the same doctor (even if different time)
$checkDup = $conn->prepare("SELECT a.id 
    FROM appointments a 
    JOIN schedules s ON a.schedule_id = s.id 
    WHERE a.user_id = ? AND s.doctor_id = (
        SELECT doctor_id FROM schedules WHERE id = ?
    )");
$checkDup->bind_param("ii", $user_id, $schedule_id);
$checkDup->execute();
$dupResult = $checkDup->get_result();

if ($dupResult->num_rows > 0) {
    echo "<script>alert('You have already booked an appointment with this doctor.'); window.location.href='find_doctor.php';</script>";
    exit();
}

// Count how many appointments already booked for this schedule
$countStmt = $conn->prepare("SELECT COUNT(*) AS total FROM appointments WHERE schedule_id = ?");
$countStmt->bind_param("i", $schedule_id);
$countStmt->execute();
$booked = $countStmt->get_result()->fetch_assoc()['total'];

// Get max patients for this schedule
$maxStmt = $conn->prepare("SELECT max_patients, day, start_time, end_time FROM schedules WHERE id = ?");
$maxStmt->bind_param("i", $schedule_id);
$maxStmt->execute();
$row = $maxStmt->get_result()->fetch_assoc();
$max_patients = $row['max_patients'];

if ($booked >= $max_patients) {
    echo "<script>alert('All slots are full for this schedule.'); window.location.href='user_homepage.php';</script>";
    exit();
}

// Insert appointment
$insert = $conn->prepare("INSERT INTO appointments (schedule_id, user_id) VALUES (?, ?)");
$insert->bind_param("ii", $schedule_id, $user_id);

if ($insert->execute()) {
    // (Optional) Send email confirmation
    // include 'send_email.php';
    // $scheduleInfo = "Date: {$row['day']}, Time: " . date("g:i A", strtotime($row['start_time'])) . " - " . date("g:i A", strtotime($row['end_time']));
    // sendAppointmentEmail($email, $name, $scheduleInfo);

    echo "<script>alert('Appointment booked successfully!'); window.location.href='user_homepage.php';</script>";
} else {
    echo "<script>alert('Something went wrong. Please try again.'); window.history.back();</script>";
}

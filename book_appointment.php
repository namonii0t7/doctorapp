<?php
$conn = new mysqli("localhost", "root", "", "docappp");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if schedule ID is sent
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['schedule_id'])) {
    $schedule_id = $_POST['schedule_id'];
    $name = $_POST['patient_name'];
    $email = $_POST['patient_email'];

    // Check how many patients already booked
    $countQuery = "SELECT COUNT(*) as total FROM appointments WHERE schedule_id = ?";
    $stmt = $conn->prepare($countQuery);
    $stmt->bind_param("i", $schedule_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $booked = $result['total'];

    // Get max patients allowed for this schedule
    $limitQuery = "SELECT max_patients FROM schedules WHERE id = ?";
    $stmt2 = $conn->prepare($limitQuery);
    $stmt2->bind_param("i", $schedule_id);
    $stmt2->execute();
    $max_patients = $stmt2->get_result()->fetch_assoc()['max_patients'];

    if ($booked >= $max_patients) {
        echo "<script>alert('Appointment slots are full!'); window.history.back();</script>";
    } else {
        // Insert appointment
        $insert = "INSERT INTO appointments (schedule_id, patient_name, patient_email) VALUES (?, ?, ?)";
        $stmt3 = $conn->prepare($insert);
        $stmt3->bind_param("iss", $schedule_id, $name, $email);
        if ($stmt3->execute()) {
            echo "<script>alert('Appointment booked successfully!'); window.location.href='user_homepage.php';</script>";
        } else {
            echo "<script>alert('Error booking appointment.'); window.history.back();</script>";
        }
    }
} else {
    echo "<script>alert('Invalid access.'); window.location.href='user_homepage.php';</script>";
}
?>

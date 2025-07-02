<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_POST['user_id'];
    $schedule_id = $_POST['schedule_id'];
    $payer_number = $_POST['payer_number'];
    $trxid = $_POST['trxid'];
    $amount = $_POST['amount'];

    $conn = new mysqli("localhost", "root", "", "docappp");
    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }

    // Check if schedule has slots available (handles 0-booking case)
    $count_query = $conn->prepare("SELECT 
                                      (SELECT COUNT(*) FROM appointments WHERE schedule_id = ?) AS booked,
                                      max_patients 
                                   FROM schedules 
                                   WHERE id = ?");
    $count_query->bind_param("ii", $schedule_id, $schedule_id);
    $count_query->execute();
    $result = $count_query->get_result();
    $row = $result->fetch_assoc();

    $booked = (int)$row['booked'];
    $max_patients = (int)$row['max_patients'];
    $count_query->close();

    if ($booked >= $max_patients) {
        echo "<h2 style='color:red; text-align:center;'>Sorry, appointment slots are full. Try again tomorrow.</h2>";
        echo "<p style='text-align:center;'><a href='user_homepage.php'>Return to Home</a></p>";
        $conn->close();
        exit();
    }

    // Check for duplicate transaction
    $check = $conn->prepare("SELECT id FROM bkash_payments WHERE trxid = ?");
    $check->bind_param("s", $trxid);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        echo "<h2 style='color:red; text-align:center;'>This Transaction ID has already been used!</h2>";
        echo "<p style='text-align:center;'><a href='user_homepage.php'>Return to Home</a></p>";
        $check->close();
        $conn->close();
        exit();
    }
    $check->close();

    // Insert appointment
    $insert_appointment = $conn->prepare("INSERT INTO appointments (schedule_id, user_id, status) VALUES (?, ?, 'pending')");
    $insert_appointment->bind_param("ii", $schedule_id, $user_id);

    if ($insert_appointment->execute()) {
        //  Insert payment
        $insert_payment = $conn->prepare("INSERT INTO bkash_payments (user_id, schedule_id, payer_number, trxid, amount) 
                                          VALUES (?, ?, ?, ?, ?)");
        $insert_payment->bind_param("iissd", $user_id, $schedule_id, $payer_number, $trxid, $amount);

        if ($insert_payment->execute()) {
            echo "<h2 style='color:green; text-align:center;'>Your payment details have been submitted.</h2>";
            echo "<p style='text-align:center;'>We will reach out via email to confirm your appointment.</p>";
            echo "<p style='text-align:center;'><a href='user_homepage.html'>Return to Home</a></p>";
        } else {
            echo "<h2 style='color:red; text-align:center;'>Appointment booked but payment failed!</h2>";
            echo "<p style='text-align:center;'><a href='user_homepage.html'>Return to Home</a></p>";
        }

        $insert_payment->close();
    } else {
        echo "<h2 style='color:red; text-align:center;'>Failed to book appointment.</h2>";
        echo "<p style='text-align:center;'><a href='user_homepage.html'>Return to Home</a></p>";
    }

    $insert_appointment->close();
    $conn->close();
} else {
    echo "<h2 style='text-align:center;'>Invalid request.</h2>";
}
?>

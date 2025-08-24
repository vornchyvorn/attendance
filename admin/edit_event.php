<?php
session_start();
include '../db/conn.php';
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../admin/login_admin.php');
    exit();
}
// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = intval($_POST['id']);
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $event_date = $_POST['event_date'];
    $start_time = $_POST['event_start'];
    $end_time   = $_POST['event_end'];
    $location = trim($_POST['location']);

    $stmt = $conn->prepare("UPDATE events SET title=?, description=?, event_date=?, event_start=?, event_end=?, location=? WHERE id=?");
    $stmt->bind_param("ssssssi", $title, $description, $event_date, $start_time, $end_time, $location, $id);

    if ($stmt->execute()) {
        // Redirect back to event list
        header("Location: events_list.php?success=1");
        exit();
    } else {
        echo "បរាជ័យក្នុងការកែប្រែព្រឹត្តិការណ៍: " . $stmt->error;
    }

    $stmt->close();
} else {
    // Redirect if accessed without POST
    header("Location: events_list.php");
    exit();
}
?>

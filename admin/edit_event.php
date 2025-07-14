<?php
session_start();
include '../db/conn.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login_admin.php"); 
    exit();
}

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get and sanitize inputs
    $id = intval($_POST['id']);
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $event_date = $_POST['event_date'];
    $event_start = $_POST['event_start'];
    $event_end = $_POST['event_end'];
    $location = trim($_POST['location']);

    // Prepare and execute update
    $stmt = $conn->prepare("UPDATE events SET title=?, description=?, event_date=?, event_start=?, event_end=?, location=? WHERE id=?");
    $stmt->bind_param("ssssssi", $title, $description, $event_date, $event_start, $event_end, $location, $id);

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

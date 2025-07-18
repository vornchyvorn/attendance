<?php
session_start();
include '../db/conn.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../user/login.php");
    exit();
}

// Check if event ID is provided
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);

    // Prepare delete query
    $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Redirect back to list with success
        header("Location: events_list.php?deleted=1");
        exit();
    } else {
        echo "បរាជ័យក្នុងការលុបព្រឹត្តិការណ៍: " . $stmt->error;
    }

    $stmt->close();
} else {
    // Redirect if ID is not valid
    header("Location: events_list.php");
    exit();
}
?>

<?php
session_start();
include '../db/conn.php';
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../admin/login_admin.php');
    exit();
}

// Check if event ID is provided
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);

    // Prepare delete query
    $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
     
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

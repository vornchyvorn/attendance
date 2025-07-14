<?php
session_start();
include '../db/conn.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../user/login.php");
    exit();
}

// Validate POST data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $status = trim($_POST['status']);

    if (empty($title) || empty($content) || empty($status)) {
        die("សូមបំពេញព័ត៌មានអោយครบ.");
    }

    // Update announcement
    $stmt = $conn->prepare("UPDATE announcements SET title = ?, content = ?, status = ? WHERE id = ?");
    $stmt->bind_param("sssi", $title, $content, $status, $id);

    if ($stmt->execute()) {
        header("Location: announcements_list.php?success=updated");
        exit();
    } else {
        echo "មានបញ្ហាក្នុងការកែប្រែ: " . $conn->error;
    }

    $stmt->close();
} else {
    echo "វិធីសាស្រ្តមិនត្រឹមត្រូវ។";
}
?>

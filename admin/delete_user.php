<?php
session_start();
include '../db/conn.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login_admin.php"); 
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: manage_users.php");
    exit();
}

$user_id = $_GET['id'];

// Get old image before delete
$sql = "SELECT image FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $image_path = "../uploads/" . $row['image'];
    if (file_exists($image_path) && $row['image']) {
        unlink($image_path);
    }
}

// Delete user
$delete = "DELETE FROM users WHERE id = ?";
$stmt = $conn->prepare($delete);
$stmt->bind_param("i", $user_id);
$stmt->execute();

header("Location: manage_users.php?deleted=1");
exit();

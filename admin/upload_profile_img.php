<?php
session_start();
include '../db/conn.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login_admin.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $user_id = $_SESSION['user_id'];
    $image = $_FILES['image'];

    $upload_dir = '../uploads/';
    if (!is_dir($upload_dir)) mkdir($upload_dir);

    $ext = pathinfo($image['name'], PATHINFO_EXTENSION);
    $new_filename = 'user_' . $user_id . '.' . $ext;
    $upload_path = $upload_dir . $new_filename;

    if (move_uploaded_file($image['tmp_name'], $upload_path)) {
        $stmt = $conn->prepare("UPDATE users SET image = ? WHERE id = ?");
        $stmt->bind_param("si", $new_filename, $user_id);
        $stmt->execute();
        header("Location: profile_admin.php?upload=success");
    } else {
        header("Location: profile_admin.php?upload=fail");
    }
    exit();
}
?>

<?php
session_start();
include '../db/conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    if (!isset($_SESSION['admin_id']) || $_SESSION['role'] !== 'admin') {
        header("Location: login_admin.php");
        exit();
    }

    $admin_id = $_SESSION['admin_id'];
    $image = $_FILES['image'];

    $upload_dir = '../uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $ext = pathinfo($image['name'], PATHINFO_EXTENSION);
    $new_filename = 'admin_' . $admin_id . '.' . $ext;
    $upload_path = $upload_dir . $new_filename;

    if (move_uploaded_file($image['tmp_name'], $upload_path)) {
        // ✅ use admin_id instead of id
        $stmt = $conn->prepare("UPDATE admin SET image = ? WHERE admin_id = ?");
        $stmt->bind_param("si", $new_filename, $admin_id);
        $stmt->execute();

        header("Location: profile_admin.php?upload=success");
        exit();
    } else {
        header("Location: profile_admin.php?upload=fail");
        exit();
    }
}

header("Location: profile_admin.php");
exit();

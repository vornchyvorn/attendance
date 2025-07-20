<?php
session_start();
include '../db/conn.php';

// Check if user is admin (adjust your session check)
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login_admin.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("DELETE FROM majors WHERE id = ?");
    $stmt->bind_param('i', $id);

    if ($stmt->execute()) {
        header('Location: create_skills.php?status=deleted');
        exit;
    } else {
        header('Location: create_skills.php?status=error');
        exit;
    }
} else {
    header('Location: create_skills.php');
    exit;
}

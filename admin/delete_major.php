<?php
session_start();
include '../db/conn.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("DELETE FROM majors WHERE id = ?");
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        // អ្នកអាចប្រើសារ session ប្រសិនបើចង់បាន
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

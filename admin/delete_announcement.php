<?php
session_start();
include '../db/conn.php';

if (isset($_GET['id']) && $_SESSION['role'] === 'admin') {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("DELETE FROM announcements WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header("Location: announcements_list.php");
    exit();
}
?>

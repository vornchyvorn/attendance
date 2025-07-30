<?php
session_start();
include '../db/conn.php';

if (!isset($_SESSION['admin_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login_admin.php');
    exit();
}

$id = $_GET['id'] ?? 0;

$stmt = $conn->prepare("DELETE FROM admin WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: manage_staff.php");
    exit();
} else {
    echo "កំហុសក្នុងការលុប!";
}
?>

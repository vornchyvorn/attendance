<?php
session_start();
include '../db/conn.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../admin/login_admin.php');
    exit();
}

// ✅ Accept POST not GET
if (!isset($_POST['id'])) {
    header("Location: manage_users.php");
    exit();
}

$user_id = intval($_POST['id']);


$sql = "SELECT image FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $image_path = "../uploads/" . $row['image'];
    if (!empty($row['image']) && file_exists($image_path)) {
        unlink($image_path);
    }
}

$delete = "DELETE FROM users WHERE id = ?";
$stmt = $conn->prepare($delete);
$stmt->bind_param("i", $user_id);
$stmt->execute();


header("Location: manage_users.php?deleted=1");
exit();
?>

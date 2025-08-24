<?php
session_start();
include '../db/conn.php';

// ប្រាកដថា admin បាន login (ដោយប្រើ admin_id)
if (isset($_GET['id']) && isset($_SESSION['admin_id'])) {
    $id = intval($_GET['id']);

    $stmt = $conn->prepare("DELETE FROM announcements WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header("Location: announcements_list.php");
    exit();
} else {
    // បើគ្មានសិទ្ធិចូល នាំទៅទំព័រ login
    header("Location: ../admin/login_admin.php");
    exit();
}
?>


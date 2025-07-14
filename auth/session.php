<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    // ប្រើសម្រាប់អ្នកប្រើប្រាស់ដែល login ជាសិស្ស
    header("Location: ../index.php");
    exit();
}
?>

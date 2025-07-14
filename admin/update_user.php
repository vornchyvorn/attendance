<?php
session_start();
include '../db/conn.php';

// ពិនិត្យថាវិធីសាស្រ្តទិន្នន័យគឺ POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ទទួលទិន្នន័យពី form
    $id = $_POST['id'];
    $student_id = $_POST['student_id'];
    $username = $_POST['username'];
    $gmail = $_POST['gmail'];
    $major = $_POST['major'];
    $gender = $_POST['gender'];
    $user_type = $_POST['user_type'];
    $date = $_POST['date'];
    $address = $_POST['address'];

    // ធ្វើការអាប់ដេតប្រើ prepared statement
    $stmt = $conn->prepare("UPDATE users SET 
        student_id = ?, 
        username = ?, 
        gmail = ?, 
        major = ?, 
        gender = ?, 
        user_type = ?, 
        date = ?, 
        address = ? 
        WHERE id = ?");

    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ssssssssi", 
        $student_id, 
        $username, 
        $gmail, 
        $major, 
        $gender, 
        $user_type, 
        $date, 
        $address, 
        $id);

    if ($stmt->execute()) {
        // ជោគជ័យ
        $_SESSION['success'] = "ទិន្នន័យត្រូវបានអាប់ដេតដោយជោគជ័យ។";
    } else {
        // បរាជ័យ
        $_SESSION['error'] = "បរាជ័យក្នុងការអាប់ដេតទិន្នន័យ: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();

    // ត្រឡប់ទៅទំព័របញ្ជីអ្នកប្រើ
    header("Location: manage_users.php");
    exit();
} else {
    // ប្រសិនបើទិន្នន័យមិនមកពី POST
    $_SESSION['error'] = "វិធីសាស្រ្តមិនត្រឹមត្រូវ។";
    header("Location: manage_users.php");
    exit();
}
?>

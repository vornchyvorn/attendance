<?php
session_start();
include '../db/conn.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $student_id = $_POST['student_id'];
    $username = $_POST['username'];
    $gmail = $_POST['gmail'];
    $major = $_POST['major'];
    $gender = $_POST['gender'];
    $Education_level = $_POST['Education_level'];
    $school_year = $_POST['school_year'];
    $user_type = $_POST['user_type'];
    $date = $_POST['date'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $stmt = $conn->prepare("UPDATE users SET 
        student_id = ?, 
        username = ?, 
        gmail = ?, 
        major = ?, 
        gender = ?,
        Education_level = ?, 
        school_year = ?,
        user_type = ?, 
        date = ?,
        phone = ?, 
        address = ? 
        WHERE id = ?");

    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("sssssssssssi", 
        $student_id, 
        $username, 
        $gmail, 
        $major, 
        $gender,
        $Education_level,
        $school_year, 
        $user_type, 
        $date,
        $phone, 
        $address, 
        $id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "ទិន្នន័យត្រូវបានអាប់ដេតដោយជោគជ័យ។";
    } else {
        $_SESSION['error'] = "បរាជ័យក្នុងការអាប់ដេតទិន្នន័យ: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();

    header("Location: manage_users.php");
    exit();
} else {
    // ប្រសិនបើទិន្នន័យមិនមកពី POST
    $_SESSION['error'] = "វិធីសាស្រ្តមិនត្រឹមត្រូវ។";
    header("Location: manage_users.php");
    exit();
}
?>

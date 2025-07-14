<?php
session_start();
date_default_timezone_set('Asia/Phnom_Penh'); // កំណត់ម៉ោងភ្នំពេញ

include '../db/conn.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];
$now = date("Y-m-d H:i:s");
$date_today = date("Y-m-d");

// រក attendance record ដែល check_out = NULL និង check_in នៅថ្ងៃនេះ
$stmt = $conn->prepare("SELECT id FROM attendance WHERE student_id = ? AND DATE(check_in) = ? AND check_out IS NULL ORDER BY check_in DESC LIMIT 1");
$stmt->bind_param("ss", $student_id, $date_today);

$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $attendance_id = $row['id'];

    // ✅ ទាញយក username និង major ពី users table
    $user_stmt = $conn->prepare("SELECT username, major FROM users WHERE student_id = ?");
    $user_stmt->bind_param("s", $student_id);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result();

    if ($user_result->num_rows === 1) {
        $user_data = $user_result->fetch_assoc();
        $username = $user_data['username'];
        $major = $user_data['major'];

        // ✅ Update check_out + បំពេញ username និង major បើវាលទទេ
        $stmt = $conn->prepare("
            UPDATE attendance 
            SET 
                check_out = ?, 
                username = IFNULL(username, ?), 
                major = IFNULL(major, ?) 
            WHERE id = ?
        ");
        $stmt->bind_param("sssi", $now, $username, $major, $attendance_id);
    } else {
        // ✅ fallback បើរកមិនឃើញ user
        $stmt = $conn->prepare("UPDATE attendance SET check_out = ? WHERE id = ?");
        $stmt->bind_param("si", $now, $attendance_id);
    }

    // ✅ ចាប់ផ្ដើម Execute
    if ($stmt->execute()) {
        $_SESSION['message'] = "✅ ចេញបានជោគជ័យ!";
    } else {
        $_SESSION['message'] = "⚠️ មានបញ្ហាក្នុងការចេញ!";
    }
} else {
    $_SESSION['message'] = "⚠️ សូមចូលជាមុនសិន មុនចេញ!";
}

header("Location: dashboard.php");
exit();
?>

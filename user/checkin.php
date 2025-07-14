<?php
session_start();
include '../db/conn.php';

date_default_timezone_set('Asia/Phnom_Penh');

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];
$now = date("Y-m-d H:i:s");
$date_today = date("Y-m-d");
$time_now = date("H:i:s");
$time_period = $_POST['time_period'] ?? '';

// ពិនិត្យប្រភេទម៉ោង
if (!in_array($time_period, ['morning', 'evening'])) {
    $_SESSION['message'] = "❌ ប្រភេទម៉ោងមិនត្រឹមត្រូវ!";
    header("Location: dashboard.php");
    exit();
}

// ពិនិត្យថាអ្នកបានចូលរួចនៅថ្ងៃនេះនៅពេលនោះរួចឬអត់
$stmt = $conn->prepare("SELECT * FROM attendance WHERE student_id = ? AND DATE(check_in) = ? AND time_period = ?");
$stmt->bind_param("iss", $student_id, $date_today, $time_period);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $_SESSION['message'] = "⚠️ អ្នកបានចូលរួចហើយក្នុងវេលា $time_period!";
} else {
    // ✅ ទាញ username និង major
    $user_stmt = $conn->prepare("SELECT username, major FROM users WHERE student_id = ?");
    $user_stmt->bind_param("s", $student_id);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result();

    if ($user_result->num_rows === 1) {
        $user_data = $user_result->fetch_assoc();
        $username = $user_data['username'];
        $major = $user_data['major'];

      
      
        $event_stmt = $conn->prepare("SELECT id FROM events WHERE event_date = ? AND event_start <= ? AND event_end >= ? LIMIT 1");
        $event_stmt->bind_param("sss", $date_today, $time_now, $time_now);
        $event_stmt->execute();
        $event_result = $event_stmt->get_result();

        $event_id = null;
        if ($event_result->num_rows > 0) {
            $event_row = $event_result->fetch_assoc();
            $event_id = $event_row['id'];
        } else {
            // ❌ មិនមាន event ត្រឹមពេលនេះទេ
            $_SESSION['message'] = "⚠️ មិនមានព្រឹត្តិការណ៍កំពុងប្រព្រឹត្តនាពេលនេះទេ។ សូមពិនិត្យថ្ងៃនិងម៉ោង!";
            header("Location: dashboard.php");
            exit();
        }

        // ✅ បញ្ចូលវត្តមាន
        $insert = $conn->prepare("INSERT INTO attendance (student_id, username, major, check_in, time_period, event_id) VALUES (?, ?, ?, ?, ?, ?)");
        $insert->bind_param("issssi", $student_id, $username, $major, $now, $time_period, $event_id);


        if ($insert->execute()) {
            $_SESSION['message'] = "✅ ចូលបានជោគជ័យ!";
        } else {
            $_SESSION['message'] = "❌ បរាជ័យក្នុងការចូល!";
        }
    } else {
        $_SESSION['message'] = "❌ មិនអាចទាញយកព័ត៌មានអ្នកប្រើបានទេ!";
    }
}

header("Location: dashboard.php");
exit();
?>
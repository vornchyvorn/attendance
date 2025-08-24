<?php
session_start();
include '../db/conn.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // ពីព្រោះ role ត្រូវបានលុបចេញហើយ មិនចាំបាច់ពិនិត្យ role ទៀតទេ

    $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($admin = $result->fetch_assoc()) {
        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['admin_id'];
            $_SESSION['username'] = $admin['username'];

            // ទាំងអស់ចូលទៅ dashboard តែមួយ បើ role ត្រូវបានលុបចោល
            header("Location: dashbord.php");
            exit;
        } else {
            $error = "ពាក្យសម្ងាត់មិនត្រឹមត្រូវ!";
        }
    } else {
        $error = "ឈ្មោះអ្នកប្រើមិនត្រឹមត្រូវ!";
    }
}
?>


<!DOCTYPE html>
<html lang="km">
<head>

    <title>Admin Login</title>
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../dist/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>
        *{
            font-family: 'Khmer OS Siemreap';
        }

    </style>
</head>

<body class="bg-gradient-to-r from-emerald-500 to-emerald-900 flex items-center justify-center h-screen"> 
    <div class="container flex items-center justify-center">
        <form method="POST" class="bg-white p-6 rounded shadow w-100">
            <img src="../pic/logo.jpg" alt="logo" class="w-20 block m-auto mb-4">
            <p class="text-center text-xl">Event Management System</p><br>
            
            <?php if ($error): ?>
                <p class="text-red-500 mb-2"><?= $error ?></p>
            <?php endif; ?>
            
            <input type="text" name="username" placeholder=" Username..." required class="border border-teal-700 p-3 w-full mb-6 rounded-full">
            
            <input type="password" name="password" placeholder=" Password..." required class="border border-teal-700 p-3 w-full mb-6 rounded-full">
            

            <button class="bg-teal-600 text-white w-[170px] p-3 rounded-full m-auto block">
                <i class="fa-solid fa-right-from-bracket"></i> Login
            </button>
        </form>
    </div>
</body>
</html>

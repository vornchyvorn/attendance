<?php
session_start();
include '../db/conn.php';
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ? AND role='admin' LIMIT 1");
    if ($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                header('Location: ../admin/dashbord.php');
                exit;
            } else {
                $error = "ពាក្យសម្ងាត់មិនត្រឹមត្រូវទេ។";
            }
        } else {
            $error = "មិនមានគណនី Admin នេះទេ។";
        }
    } else {
        $error = "បញ្ហាប្រព័ន្ធក្នុងពេលប្រតិបត្តិការ។";
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
        <p class="text-center text-xl">Institute Management System</p><br>
        <?php if ($error): ?><p class="text-red-500 mb-2"><?= $error ?></p><?php endif; ?>
        <input type="text" name="username" placeholder=" Username..." required class="border border-teal-700 p-3 w-full mb-9 rounded-full">
        <input type="password" name="password" placeholder=" Password..." required class="border border-teal-700 p-3 w-full mb-9 rounded-full">
        <button class="bg-teal-600 text-white w-[170px] p-3 rounded-full m-auto block"><i class="fa-solid fa-right-from-bracket"></i> Login</button>
    </form>
    </div>
    
</body>
</html>

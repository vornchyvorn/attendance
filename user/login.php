<?php
session_start();
include '../db/conn.php';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $_POST['student_id'];
    $password = $_POST['password'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE student_id = ?");
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            if ($row['role'] === 'admin') {
                $error = "សូមចូលតាមគេហទំព័ររបស់អ្នកគ្រប់គ្រង!";
            } else {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['student_id'] = $row['student_id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['role'] = $row['role'];
                header("Location: dashboard.php");
                exit;
            }
        }
    }
    if (!$error) {
        $error = "អត្តលេខ ឬ ពាក្យសម្ងាត់មិនត្រឹមត្រូវ!";
    }
}
?>
<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="/public/style.css">
    <link href="../dist/style.css" rel="stylesheet">
    <style>
    body {
      font-family: "Khmer OS Siemreap", sans-serif;
    }
  </style>
</head>
<body class="focus:outline-none bg-gradient-to-r from-emerald-500 to-emerald-900">
<div class="max-w-xs mx-auto mt-24 bg-white p-6 rounded shadow">
    <h2 class="text-xl font-bold text-center text-teal-700 mb-4">ចូលគណនី</h2>
    <?php if ($error): ?><div class="bg-red-500 text-white p-2 text-center mb-2"><?= $error ?></div><?php endif; ?>
    <form method="post">
        <input type="text" name="student_id" placeholder="អត្តលេខ" class="w-full mb-2 p-2 border border-teal-600 rounded" required>
        <input type="password" name="password" id="password" placeholder="ពាក្យសម្ងាត់" class="w-full mb-2 p-2 border border-teal-600 rounded" required>
        <img src="../pic/close.png" id="eyeicon" class="w-6 ml-[85%] inline mt-[-30%]">
        <button type="submit" class="w-full bg-teal-700 text-white py-2 rounded">ចូល</button>
    </form>
    <p class="mt-3 text-center">មិនទាន់មានគណនី? <a href="register.php" class="text-blue-600">ចុះឈ្មោះ</a></p>
</div>

<script>
    let eyeicon = document.getElementById("eyeicon");
    let password = document.getElementById("password");

    eyeicon.onclick = function () {
        if(password.type == "password"){
            password.type = "text";
            eyeicon.src = "../pic/open.png";
        }
        else {
            password.type = "password";
            eyeicon.src = "../pic/close.png";
        }
        
    }
</script>
</body>
</html>

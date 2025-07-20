<?php
session_start();
include '../db/conn.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = trim($_POST['student_id']);
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $gender = $_POST['gender'];
    $gmail = $_POST['gmail'];
    $major = $_POST['major'];
    $date = $_POST['date'];
    $user_type = $_POST['user_type']; // 'teacher' or 'student'
    $address = $_POST['address'];

    // Image handling
    $image = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];
    $image_size = $_FILES['image']['size'];
    $ext = strtolower(pathinfo($image, PATHINFO_EXTENSION));
    $image_new = uniqid('user_', true) . '.' . $ext;
    $image_path = '../upload_img/' . $image_new;

    // Validate student_id uniqueness
    $check = $conn->prepare("SELECT id FROM users WHERE student_id = ?");
    $check->bind_param("s", $student_id);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        $error = "អត្តលេខនេះបានចុះឈ្មោះរួចហើយ!";
    }
    $check->close();

    // Validate image
    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
    if (!$error) {
        if (!in_array($ext, $allowed_ext)) {
            $error = "ប្រភេទរូបភាពមិនត្រឹមត្រូវ។";
        } elseif ($image_size > 2 * 1024 * 1024) {
            $error = "ទំហំរូបភាពធំជាងកំណត់ (2MB)";
        } elseif (!move_uploaded_file($image_tmp, $image_path)) {
            $error = "ការផ្ទុករូបភាពបរាជ័យ។";
        }
    }

    // Insert into DB
    if (!$error) {
        $stmt = $conn->prepare(
            "INSERT INTO users (student_id, username, password, gender, gmail, major, date, user_type, address, image)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("ssssssssss", $student_id, $username, $password, $gender, $gmail, $major, $date, $user_type, $address, $image_new);

        if ($stmt->execute()) {
            $success = "ចុះឈ្មោះជោគជ័យ! សូមចូលប្រើប្រព័ន្ធ។";
        } else {
            $error = "បរាជ័យក្នុងការចុះឈ្មោះ។";
            if (file_exists($image_path)) unlink($image_path);
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link href="../dist/style.css" rel="stylesheet">
    <style>
        body {
            font-family: "Khmer OS Siemreap", sans-serif;
        }
    </style>
</head>
<body class="bg-gradient-to-r from-emerald-500 to-emerald-900 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full bg-white p-6 rounded shadow">
        <h2 class="text-xl font-bold text-center text-teal-700 mb-4">បង្កើតគណនី</h2>
        <?php if ($error): ?>
            <div class="bg-red-500 text-white p-2 mb-2 text-center"><?= $error ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="bg-green-500 text-white p-2 mb-2 text-center"><?= $success ?></div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data">
            <input type="text" name="username" placeholder="ឈ្មោះ" required class="w-full mb-2 p-2 border border-teal-600 rounded">
            <input type="text" name="student_id" placeholder="អត្តលេខ" required class="w-full mb-2 p-2 border border-teal-600 rounded">

            <div class="relative">
                <input type="password" name="password" id="password" placeholder="ពាក្យសម្ងាត់" required class="w-full mb-2 p-2 border border-teal-600 rounded">
                <img src="../pic/close.png" id="eyeicon" class="w-6 absolute right-2 top-3 cursor-pointer">
            </div>

            <select name="gender" required class="w-full mb-2 p-2 border border-teal-600 rounded">
                <option value="">ជ្រើសរើសភេទ</option>
                <option value="ប្រុស">ប្រុស</option>
                <option value="ស្រី">ស្រី</option>
            </select>

            <input type="email" name="gmail" placeholder="អ៊ីមែល" required class="w-full mb-2 p-2 border border-teal-600 rounded">

            <select name="major" required class="w-full mb-2 p-2 border border-teal-600 rounded">
                <option value="">ជ្រើសរើសជំនាញ</option>
                <?php
                $majors = $conn->query("SELECT major_name FROM majors");
                while ($row = $majors->fetch_assoc()) {
                    echo "<option value='{$row['major_name']}'>{$row['major_name']}</option>";
                }
                ?>
            </select>

            <input type="date" name="date" required class="w-full mb-2 p-2 border border-teal-600 rounded">

            <select name="user_type" required class="w-full mb-2 p-2 border border-teal-600 rounded">
                <option value="">ប្រភេទអ្នកប្រើ</option>
                <option value="គ្រូ">គ្រូ</option>
                <option value="សិស្ស">សិស្ស</option>
            </select>

            <textarea name="address" placeholder="អាសយដ្ឋាន" required class="w-full mb-2 p-2 border border-teal-600 rounded"></textarea>

            <input type="file" name="image" accept="image/*" required class="w-full mb-2 p-2 border border-teal-600 rounded">

            <div class="flex justify-between items-center">
                <a href="login.php" class="text-blue-600">ចូលគណនី</a>
                <button type="submit" class="bg-teal-700 text-white px-4 py-2 rounded">ចុះឈ្មោះ</button>
            </div>
        </form>
    </div>

    <script>
        const eyeicon = document.getElementById("eyeicon");
        const password = document.getElementById("password");

        eyeicon.onclick = function () {
            if (password.type === "password") {
                password.type = "text";
                eyeicon.src = "../pic/open.png";
            } else {
                password.type = "password";
                eyeicon.src = "../pic/close.png";
            }
        }
    </script>
</body>
</html>

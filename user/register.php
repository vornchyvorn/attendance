<?php
session_start();
include '../db/conn.php';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $gender = $_POST['gender'];
    $gmail = $_POST['gmail'];
    $major = $_POST['major'];
    $date = $_POST['date'];
    $user_type = $_POST['user_type']; // គ្រូ / សិស្ស
    $address = $_POST['address'];
    $role = 'user'; // <<✅ កំណត់ role ថេរ
    $image = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];
    $image_size = $_FILES['image']['size'];
    $image_path = '../upload_img/' . $image;

    if ($image_size > 2000000) {
        $error = "ទំហំរូបភាពធំជាងកំណត់។";
    } elseif (move_uploaded_file($image_tmp, $image_path)) {
        $stmt = $conn->prepare(
            "INSERT INTO users (student_id, username, password, gender, gmail, major, date, role, user_type, address, image)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("sssssssssss", $student_id, $username, $password, $gender, $gmail, $major, $date, $role, $user_type, $address, $image);

        if ($stmt->execute()) {
            $success = "ចុះឈ្មោះជោគជ័យ! សូមចូលប្រព័ន្ធ";
        } else {
            $error = "បរាជ័យក្នុងការចុះឈ្មោះ។";
        }
    } else {
        $error = "ការផ្ទុករូបភាពបរាជ័យ។";
    }
}
?>


<!-- HTML UI -->
<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="../dist/style.css" rel="stylesheet">
    <style>
    body {
      font-family: "Khmer OS Siemreap", sans-serif;
    }
  </style>
</head>
<body class="focus:outline-none bg-gradient-to-r from-emerald-500 to-emerald-900">
<div class="max-w-sm mx-auto mt-10 bg-white p-6 rounded shadow">
    <h2 class="text-xl font-bold text-center text-teal-700 mb-4">បង្កើតគណនី</h2>
    <?php if ($error): ?><div class="bg-red-500 text-white p-2 mb-2 text-center"><?= $error ?></div><?php endif; ?>
    <?php if ($success): ?><div class="bg-green-500 text-white p-2 mb-2 text-center"><?= $success ?></div><?php endif; ?>

    <form method="post" enctype="multipart/form-data">
     
        ឈ្មោះ <input type="text" name="username" placeholder="ឈ្មោះ" required class="w-full mb-2 p-2 border border-teal-600 rounded">
        
        អត្តលេខ <input type="text" name="student_id" placeholder="អត្តលេខ" required class="w-full mb-2 p-2 border border-teal-600 rounded">
        <div class="">
            ពាក្យសម្ងាត់​ <input type="password" name="password" id="password" placeholder="ពាក្យសម្ងាត់" required class="w-full mb-2 p-2 m border border-teal-600 rounded">
            <img src="../pic/close.png" id="eyeicon" class="w-6 ml-[85%]">
        </div>
        ភេទ <select name="gender" required class="w-full mb-2 p-2 border border-teal-600 rounded">
                <option value="">ជ្រើសរើស</option>
                <option value="ប្រុស">ប្រុស</option>
                <option value="ស្រី">ស្រី</option>
        </select>
        អ៊ីមែល <input type="email" name="gmail" placeholder="អ៊ីមែល" required class="w-full mb-2 p-2 border border-teal-600 rounded">
        ជំនាញ <select name="major" required class="w-full mb-2 p-2 border border-teal-600 rounded">
            <option value="">ជ្រើសរើស</option>
            <?php
            $majors = $conn->query("SELECT major_name FROM majors");
            while ($row = $majors->fetch_assoc()) {
                echo "<option value='{$row['major_name']}'>{$row['major_name']}</option>";
            }
            ?>
        </select>
        ថ្ងៃ ខែ ឆ្នាំ<input type="date" name="date" required class="w-full mb-2 p-2 border border-teal-600 rounded">
        ប្រភេទអ្នកប្រើ <select name="user_type" required class="w-full mb-2 p-2 border border-teal-600 rounded">
            <option value="" class="">ជ្រើសរើស</option>
            <option value="គ្រូ">គ្រូ</option>
            <option value="សិស្ស">សិស្ស</option>
        </select>
        អាសយដ្ឋាន <textarea name="address" placeholder="បំពេញអាសយដ្ឋាន" required class="w-full mb-2 p-2 border border-teal-600 rounded"></textarea>
        រូបភាព<input type="file" name="image" accept="image/jpg, image/jpeg, image/png, image/gif" required class="w-full mb-2 p-2 border border-teal-600 rounded">
        <div class="col-span-2 flex justify-between">
            <a href="login.php" class="text-blue-600 mt-2">ចូលគណនី</a>
            <button type="submit" class="bg-teal-700 text-white px-4 py-2 rounded">ចុះឈ្មោះ</button>
        </div>
    </form>
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


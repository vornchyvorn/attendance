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
    $Education_level = $_POST['Education_level'];
    $school_year = $_POST['school_year'];
    $gmail = $_POST['gmail'];
    $major = $_POST['major'];
    $date = $_POST['date'];
    $user_type = $_POST['user_type'];
    $phone = $_POST['phone'];
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
        } elseif ($image_size > 5 * 1024 * 1024) {
            $error = "ទំហំរូបភាពធំជាងកំណត់ (2MB)";
        } elseif (!move_uploaded_file($image_tmp, $image_path)) {
            $error = "ការផ្ទុករូបភាពបរាជ័យ។";
        }
    }

    // Insert into DB
    if (!$error) {
        $stmt = $conn->prepare(
            "INSERT INTO users (student_id, username, password, gender, Education_level, school_year, gmail, major, date, user_type, address, phone, image)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("sssssssssssss", $student_id, $username, $password, $gender, $Education_level, $school_year, $gmail, $major, $date, $user_type, $address, $phone, $image_new);

        if ($stmt->execute()) {
            // ✅ Redirect to login.php after success
            header("Location: login.php?register=success");
            exit();
        } else {
            echo "<p class='text-red-600'>មានបញ្ហាក្នុងការចុះឈ្មោះ!</p>";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="km">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="../dist/style.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:ital,wght@0,100..700;1,100..700&family=Koulen&display=swap" rel="stylesheet">
    <style>
        body {
             font-family: "Kantumruy Pro", sans-serif;
        }
    </style>
</head>

<body class="bg-gradient-to-r from-emerald-500 to-emerald-900 min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-4xl bg-white p-6 md:p-8 rounded-xl shadow-lg">
        <h2 class="text-2xl md:text-3xl font-bold text-center text-teal-700 mb-6">បង្កើតគណនី</h2>

        <?php if ($error): ?>
            <div class="bg-red-500 text-white p-2 mb-3 text-center rounded"><?= $error ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="bg-green-500 text-white p-2 mb-3 text-center rounded"><?= $success ?></div>
        <?php endif; ?>
        <form method="post" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Username -->
            <div>
                <label for="username" class="block mb-1 text-teal-700 font-semibold">ឈ្មោះ</label>
                <input type="text" id="username" name="username" placeholder="បញ្ជូលឈ្មោះ" required
                    class="w-full p-3 border border-teal-600 rounded text-base md:text-lg">
            </div>

            <!-- Student ID -->
            <div>
                <label for="student_id" class="block mb-1 text-teal-700 font-semibold">អត្តលេខអ្នកប្រើប្រាស់</label>
                <input type="text" id="student_id" name="student_id" placeholder="បញ្ជូលអត្តលេខ" required
                    class="w-full p-3 border border-teal-600 rounded text-base md:text-lg">
            </div>

            <!-- Password -->
            <div class="relative col-span-1 md:col-span-2">
                <label for="password" class="block mb-1 text-teal-700 font-semibold">ពាក្យសម្ងាត់</label>
                <input type="password" id="password" name="password" placeholder="បញ្ជូលពាក្យសម្ងាត់" required
                    class="w-full p-3 border border-teal-600 rounded text-base md:text-lg pr-12 h-12">
                <img src="../pic/close.png" id="eyeicon"
                    class="w-6 absolute right-4 transform -translate-y-1/2 cursor-pointer z-10" style="margin-top: -2rem; ">
            </div>



            <!-- Gender -->
            <div>
                <label for="gender" class="block mb-1 text-teal-700 font-semibold">ភេទ</label>
                <select id="gender" name="gender" required
                    class="w-full p-3 border border-teal-600 rounded text-base md:text-lg">
                    <option value="">ជ្រើសរើសភេទ</option>
                    <option value="ប្រុស">ប្រុស</option>
                    <option value="ស្រី">ស្រី</option>
                </select>
            </div>

            <!-- Education Level -->
            <div>
                <label for="Education_level" class="block mb-1 text-teal-700 font-semibold">កម្រិតវប្បធម៍</label>
                <select id="Education_level" name="Education_level" required
                    class="w-full p-3 border border-teal-600 rounded text-base md:text-lg">
                    <option value="">ជ្រើសរើសកម្រិតវប្បធម៍</option>
                    <option value="បរិញ្ញាបត្រ">បរិញ្ញាបត្រ</option>
                    <option value="បរិញ្ញាបត្ររង">បរិញ្ញាបត្ររង</option>
                    <option value="9+3">9+3</option>
                </select>
            </div>

            <!-- School Year -->
            <div>
                <label for="school_year" class="block mb-1 text-teal-700 font-semibold">ឆ្នាំសិក្សា</label>
                <select id="school_year" name="school_year" required
                    class="w-full p-3 border border-teal-600 rounded text-base md:text-lg">
                    <option value="">ជ្រើសរើសឆ្នាំ</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                </select>
            </div>

            <!-- Email -->
            <div>
                <label for="gmail" class="block mb-1 text-teal-700 font-semibold">អ៊ីមែល</label>
                <input type="email" id="gmail" name="gmail" placeholder="បញ្ជូលអ៊ីមែល" required
                    class="w-full p-3 border border-teal-600 rounded text-base md:text-lg">
            </div>

            <!-- Major -->
            <div>
                <label for="major" class="block mb-1 text-teal-700 font-semibold">ជំនាញ</label>
                <select id="major" name="major" required
                    class="w-full p-3 border border-teal-600 rounded text-base md:text-lg">
                    <option value="">ជ្រើសរើសជំនាញ</option>
                    <?php
                    $majors = $conn->query("SELECT major_name FROM majors");
                    while ($row = $majors->fetch_assoc()) {
                        echo "<option value='{$row['major_name']}'>{$row['major_name']}</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- Date -->
            <div>
                <label for="date" class="block mb-1 text-teal-700 font-semibold">ថ្ងៃខែឆ្នាំកំណើត</label>
                <input type="date" id="date" name="date" required
                    class="w-full p-3 border border-teal-600 rounded text-base text-black md:text-lg">
            </div>

            <!-- User Type -->
            <div>
                <label for="user_type" class="block mb-1 text-teal-700 font-semibold">ប្រភេទអ្នកប្រើ</label>
                <select id="user_type" name="user_type" required
                    class="w-full p-3 border border-teal-600 rounded text-base md:text-lg">
                    <option value="">ជ្រើសរើសប្រភេទ</option>
                    <option value="គ្រូ">គ្រូ</option>
                    <option value="សិស្ស">សិស្ស</option>
                </select>
            </div>

            <!-- Phone -->
            <div class="col-span-1 md:col-span-2">
                <label for="phone" class="block mb-1 text-teal-700 font-semibold">លេខទូរស័ព្ទ</label>
                <input type="text" id="phone" name="phone" placeholder="បញ្ជូលលេខទូរស័ព្ទ" required
                    class="w-full p-3 border border-teal-600 rounded text-base md:text-lg">
            </div>

            <!-- Address -->


            <!-- Image -->
            <div class="col-span-1 md:col-span-2">
                <label for="image" class="block text-teal-700 font-semibold">រូបភាព</label>
                <input type="file" id="image" name="image" accept="image/*" required
                    class="w-full p-3 border border-teal-600 rounded">
            </div>
            <div class="col-span-1 md:col-span-2">
                <label for="address" class="block mb-1 text-teal-700 font-semibold">អាសយដ្ឋាន</label>
                <textarea id="address" name="address" placeholder="បញ្ជូលអាសយដ្ឋាន" required
                    class="w-full p-3 border border-teal-600 rounded text-base md:text-lg"></textarea>
            </div>

            <!-- Buttons -->
            <div class="flex flex-col md:flex-row justify-between items-center gap-4 col-span-1 md:col-span-2 mt-4">
                <a href=""></a>
                <button type="submit"
                    class="bg-teal-700 text-white px-6 py-3 rounded text-base md:text-lg hover:bg-teal-800 transition">ចុះឈ្មោះ</button>
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
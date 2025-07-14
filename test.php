<?php
session_start();
include 'db/conn.php';
$error = '';
$success = '';
// ==================== Register ===================
if (isset($_POST['action']) && $_POST['action'] === 'register') {
    $student_id = $_POST['student_id'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $gender = $_POST['gender'];
    $gmail = $_POST['gmail'];
    $major = $_POST['major'];
    $date = $_POST['date'];
    $user_type = $_POST['user_type'];
    $address = $_POST['address'];
    $image = $_FILES['image']['name'];
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = 'upload_img/'.$image;

    if ($image_size > 2000000) {
        $error = "ទំហំរូបភាពធំជាងកំណត់។";
    } elseif (move_uploaded_file($image_tmp_name, $image_folder)) {
        $stmt = $conn->prepare(
            "INSERT INTO users 
             (student_id, username, password, gender, gmail, major, date, role, address, image) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            "ssssssssss",
            $student_id,
            $username,
            $password,
            $gender,
            $gmail,
            $major,
            $date,
            $user_type, // saved as role
            $address,
            $image
        );

        if ($stmt->execute()) {
            $success = "ចុះឈ្មោះបានជោគជ័យ! សូមចូលប្រព័ន្ធ។";
        } else {
            $error = "បរាជ័យក្នុងការចុះឈ្មោះ។";
        }
    } else {
        $error = "មិនអាចអាប់ឡូតរូបភាពបាន។";
    }
}

// ==================== Login ===================
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $_POST['student_id'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE student_id = ?");
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
           
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['student_id'] = $row['student_id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['user'];

            // Redirect by role
            if ($row['role'] === 'admin') {
                header("Location: admin/dashboard_admin.php");
            } else {
                header("Location: user/dashboard.php");
            }
            exit();
        }
    }

    $error = "Student ID ឬ លេខសម្ងាត់មិនត្រឹមត្រូវ!";
}
?>
<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ដេប៉ាតឺម៉ង់វិទ្យាសាស្រ្តកុំព្យូទ័រ</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link href="../dist/style.css" rel="stylesheet">
    <style>
        body { font-family: "Khmer OS Siemreap", sans-serif; }
    </style>
</head>
<body class="bg-gray-100">

<!-- Display errors/success -->
<?php if ($error): ?>
    <div class="bg-red-500 text-white p-3 text-center"><?= $error ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="bg-green-500 text-white p-3 text-center"><?= $success ?></div>
<?php endif; ?>

<!-- Header -->
<header class="bg-teal-700 text-white p-2 flex justify-between items-center">
    <div class="flex items-center gap-2">
        <img src="pic/major.jpg" alt="Logo" class="h-20 w-20 ml-12 rounded-sm ">
        <span class="font-bold">ដេប៉ាតឺម៉ង់វិទ្យាសាស្រ្តកុំព្យូទ័រ</span>
    </div>
</header>

<!-- Navbar -->
<nav class="bg-white shadow p-3 flex justify-between items-center">
    <div class="flex gap-4 ml-12">
        <a href="#" class="hover:text-teal-700">ទំព័រដើម</a>
        <a href="#" class="hover:text-teal-700">ព័ត៌មាន</a>
    </div>
    <div class="flex gap-2 mr-12">
        <button onclick="openLogin()" class="hover:text-teal-700">ចូលគណនី</button>
        <button onclick="openRegister()" class="hover:text-teal-700">បង្កើតគណនី</button>
    </div>
</nav>

<!-- Main Content -->
<main class="p-4 flex flex-col md:flex-row gap-4 items-center">
    <div class="md:w-1/2 ml-12">
        <h1 class="text-2xl font-bold text-teal-700">ស្វាគមន៍មកកាន់ដេប៉ាតឺម៉ង់វិទ្យាសាស្រ្តកុំព្យូទ័រ</h1>
        <p class="mt-2 text-gray-700">ចំណេះវិទ្យាសាស្រ្តកុំព្យូទ័រយើងបណ្តុះបណ្តាលនិស្សិតឲ្យមានជំនាញវិជ្ជាជីវៈ 
            <br>និងចំណេះដឹងសារសំខាន់ ដើម្បីរួមចំណែកក្នុងការអភិវឌ្ឍជាតិ។</p>
        <button class="mt-4 bg-teal-700 text-white px-4 py-2 rounded">អានបន្ថែម</button>
    </div>
    <div class="md:w-1/2 md:mr-12"><img src="pic/logo school.jpg" alt="Institute" class="rounded shadow"></div>
</main>

<!-- Login Modal -->
<div id="loginModal" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center z-50">
    <div class="bg-white rounded-lg p-6 w-80">
        <h2 class="text-xl font-bold text-teal-700 mb-4">ចូលគណនី</h2>
        <form method="post" action="">
            <input type="hidden" name="action" value="login">
            <input type="text" name="student_id" placeholder="អត្តលេខ" class="border rounded p-2 w-full mb-2" required>
            <input type="password" name="password" placeholder="ពាក្យសម្ងាត់" class="border rounded p-2 w-full mb-2" required>
    <div class="flex justify-end gap-2 mt-3">
        <button type="button" onclick="closeLogin()" class="mt-3 w-14 text-sm bg-red-700 text-white p-2 md:ml-[155px] rounded">លុប</button>
        <button type="submit" class="bg-teal-700 text-white w-14 mt-3 p-2 rounded text-sm">ចូល</button>
    </div>
    </form>
    </div>
    </div>



<!-- Register Modal -->
<div id="registerModal" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center z-50">
    <div class="bg-white rounded-lg p-6 md:w-[50%] md:overflow-hidden overflow-y-auto max-h-[90%]">
        <h2 class="text-xl font-bold text-teal-700 mb-4">បង្កើតគណនី</h2>
        <form method="post" enctype="multipart/form-data" class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <input type="hidden" name="action" value="register">

            <div>
                <label class="block mb-1 font-semibold">ឈ្មោះអ្នកប្រើ:</label>
                <input type="text" name="username" required class="w-full px-4 py-2 border border-gray-300 rounded">
            </div> 
            <div>
                <label class="block mb-1 font-semibold">លេខសម្គាល់សិស្ស:</label>
                <input type="text" name="student_id" required class="w-full px-4 py-2 border border-gray-300 rounded">
            </div>
            <div>
                <label class="block mb-1 font-semibold">ពាក្យសម្ងាត់:</label>
                <input type="password" name="password" required class="w-full px-4 py-2 border border-gray-300 rounded">
            </div>
            <div>
                <label class="block mb-1 font-semibold">ភេទ:</label>
                <select name="gender" required class="w-full px-4 py-2 border border-gray-300 rounded">
                    <option value="">ជ្រើសរើសភេទ</option>
                    <option value="ប្រុស">ប្រុស</option>
                    <option value="ស្រី">ស្រី</option>
                </select>
            </div>
            <div>
                <label class="block mb-1 font-semibold">អ៊ីមែល:</label>
                <input type="email" name="gmail" required class="w-full px-4 py-2 border border-gray-300 rounded">
            </div>
            <div>
                <label class="block mb-1 font-semibold">ជំនាញសិក្សា:</label>
                <select name="major" required class="w-full px-4 py-2 border border-gray-300 rounded">
                    <option value="">ជ្រើសរើសជំនាញសិក្សា</option>
                    <?php
                    $majors = $conn->query("SELECT major_name FROM majors");
                    while ($row = $majors->fetch_assoc()) {
                        $m = $row['major_name'];
                        echo "<option value='$m'>$m</option>";
                    }
                    ?>
                </select>
            </div>
            <div>
                <label class="block mb-1 font-semibold">ថ្ងៃ/ខែ/ឆ្នាំ:</label>
                <input type="date" name="date" required class="w-full px-4 py-2 border border-gray-300 rounded">
            </div>
            <div>
                <label class="block mb-1 font-semibold">ប្រភេទអ្នកប្រើ:</label>
                <select name="user_type" required class="w-full px-4 py-2 border border-gray-300 rounded">
                    <option value="">ជ្រើសរើសប្រភេទ</option>
                    <option value="គ្រូ">គ្រូ</option>
                    <option value="សិស្ស">សិស្ស</option>
                </select>
            </div>
            <div>
                <label class="block mb-1 font-semibold">អាសយដ្ឋាន:</label>
                <textarea name="address" required class="w-full px-4 py-2 border border-gray-300 rounded"></textarea>
            </div>
            <div>
                <label class="block mb-1 font-semibold">រូបភាព:</label>
                <input type="file" name="image" accept="image/jpg,image/jpeg,image/png" required class="w-full border border-gray-300 rounded p-2">
            </div>
            
        <div class="flex justify-start gap-2 mt-3">
            <button type="button" onclick="closeRegister()" class="py-2 w-12 rounded sm:col-span-2 text-white bg-red-600 ">បិទ</button>
            <button type="submit" class="sm:col-span-2 bg-teal-700 text-white py-2 w-24 rounded ">បង្កើតគណនី</button>
        </div>
    </form>
    </div>
</div>

<!-- Footer -->
<footer class="bg-teal-700 p-4 text-center">
    <div class="text-lg text-white">វិទ្យាស្ថានបច្ចេកវិទ្យាកំពង់ស្ពឺ</div>
    <div class="flex justify-center gap-2 mt-2">
        <a href="https://www.youtube.com/@kampongspeuinstituteoftech" target="_blank"><i class="fa-brands fa-youtube text-red-700"></i></a>
        <a href="https://web.facebook.com/KSITCambodia" target="_blank"><i class="fa-brands fa-square-facebook text-blue-800"></i></a>
        <a href="https://t.me/+mcfO7yltDeY3YzJl" target="_blank"><i class="fa-brands fa-telegram text-sky-800"></i></a>
    </div>
    <div class="text-sm mt-2 text-white">ទំនាក់ទំនង: info@ksit.edu.kh</div>
</footer>

<script>
    function openLogin() {
        document.getElementById('loginModal').classList.remove('hidden');
        document.getElementById('loginModal').classList.add('flex');
    }
    function closeLogin() {
        document.getElementById('loginModal').classList.add('hidden');
        document.getElementById('loginModal').classList.remove('flex');
    }
    function openRegister() {
        document.getElementById('registerModal').classList.remove('hidden');
        document.getElementById('registerModal').classList.add('flex');
    }
    function closeRegister() {
        document.getElementById('registerModal').classList.add('hidden');
        document.getElementById('registerModal').classList.remove('flex');
    }
</script>
</body>
</html>

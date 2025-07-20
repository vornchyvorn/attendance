<?php
session_start();
include '../db/conn.php';

$profile_img = '../pic/user.png'; 
$admin_id = $_SESSION['admin_id'] ?? 0;

if ($admin_id) {
    $stmt = $conn->prepare("SELECT image FROM admin WHERE admin_id = ?");
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        if (!empty($row['image']) && file_exists("../uploads/" . $row['image'])) {
            $profile_img = "../uploads/" . $row['image'];
        }
    }
    $stmt->close();
}

$error = '';
$success = '';
$image_new = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = trim($_POST['student_id']);
    $username = trim($_POST['username']);
    $password_raw = trim($_POST['password']);
    $gender = $_POST['gender'];
    $gmail = trim($_POST['gmail']);
    $major = $_POST['major'];
    $date = $_POST['date'];
    $user_type = $_POST['user_type']; // សិស្ស / គ្រូ
    $address = trim($_POST['address']);

    // Image upload settings
    $image = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];
    $image_size = $_FILES['image']['size'];
    $ext = strtolower(pathinfo($image, PATHINFO_EXTENSION));
    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
    $upload_dir = 'upload_img/';
    $image_new = uniqid('user_', true) . '.' . $ext;
    $image_path = $upload_dir . $image_new;

    // Check duplicate student_id
    $stmt_check = $conn->prepare("SELECT id FROM users WHERE student_id = ?");
    $stmt_check->bind_param("s", $student_id);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        $error = "អត្តលេខសិស្សនេះមានរួចហើយ។";
    }
    $stmt_check->close();

    if (!$error) {
        if (!in_array($ext, $allowed_ext)) {
            $error = "ប្រភេទរូបភាពមិនត្រឹមត្រូវ។";
        } elseif ($image_size > 2 * 1024 * 1024) {
            $error = "រូបភាពត្រូវតែតិចជាង 2MB។";
        } elseif (!move_uploaded_file($image_tmp, $image_path)) {
            $error = "ផ្ទុករូបភាពបរាជ័យ។";
        } else {
            $password = password_hash($password_raw, PASSWORD_DEFAULT);
            $stmt = $conn->prepare(
                "INSERT INTO users (student_id, username, password, gender, gmail, major, date, user_type, address, image)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt->bind_param("ssssssssss", $student_id, $username, $password, $gender, $gmail, $major, $date, $user_type, $address, $image_new);

            if ($stmt->execute()) {
                $success = "✅ ចុះឈ្មោះបានជោគជ័យ។";
            } else {
                $error = "❌ បញ្ចូលទិន្នន័យបរាជ័យ: " . $stmt->error;
                if (file_exists($image_path)) unlink($image_path);
            }
            
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard</title>
<script src="//unpkg.com/alpinejs" defer></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
<link href="../dist/style.css" rel="stylesheet">
<style>
*{
    font-family: 'Khmer OS Siemreap', sans-serif;
}
.dropdown-menu { transition: all 0.3s ease; }
</style>
</head>

<body class="bg-gray-100 text-gray-800  flex" x-data="{ sidebarOpen: true }">

<!-- Sidebar -->
<aside :class="sidebarOpen ? 'w-64' : 'w-0', sidebarOpen ? 'p-4' : 'p-0'" class="bg-[#111811] text-white overflow-hidden h-screen fixed left-0 top-0 transition-all duration-300">
    <div class="flex items-center mb-4" x-show="sidebarOpen" x-transition>
        <img src="../pic/logo.jpg" class="w-[60px] h-[60px] mt-2 rounded-full ml-20" alt="logo">
    </div>
    <hr>
    <!-- Sidebar items -->
    <ul class="space-y-1" x-show="sidebarOpen" x-transition>
        <li><a href="../admin/dashbord.php" class="flex items-center gap-3 px-3 py-4 hover:bg-gray-700"><i class="fa-solid fa-gauge"></i> Dashboard</a></li>

        <!-- User Dropdown -->
        <li x-data="{ open: false }">
            <button @click="open=!open" class="w-full flex justify-between items-center px-3 py-4 hover:bg-gray-700">
                <span class="flex items-center gap-3"><i class="fa-solid fa-user"></i> សិស្សចុះឈ្មោះ</span>
                <i :class="open?'fa-chevron-up':'fa-chevron-down'" class="fa-solid"></i>
            </button>
            <ul x-show="open" x-transition class="pl-6 px-3 mt-1 space-y-3 text-sm dropdown-menu">
                <li><a href="../admin/add_user.php" class="flex items-center gap-3 py-4 w-full hover:bg-gray-700"><i class="fa-regular fa-circle"></i> ចុះឈ្មោះថ្មី</a></li>
                <li><a href="../admin/manage_users.php" class="flex items-center gap-3 py-4 w-full hover:bg-gray-700"><i class="fa-regular fa-circle"></i> គ្រប់គ្រងសិស្ស</a></li>
            </ul>
        </li>

        <!-- Event Dropdown -->
        <li x-data="{ open: false }">
            <button @click="open=!open" class="w-full flex justify-between items-center px-3 py-4 hover:bg-gray-700">
                <span class="flex items-center gap-3"><i class="fa-solid fa-calendar-plus"></i> ព្រឹត្តិការណ៍</span>
                <i :class="open?'fa-chevron-up':'fa-chevron-down'" class="fa-solid"></i>
            </button>
            <ul x-show="open" x-transition class="pl-6 px-3 mt-1 space-y-3 text-sm dropdown-menu">
                <li><a href="../admin/create_event.php" class="flex items-center gap-3 py-4 w-full hover:bg-gray-700"><i class="fa-regular fa-circle"></i> បង្កើតព្រឹត្តិការណ៍ថ្មី</a></li>
                <li><a href="../admin/events_list.php" class="flex items-center gap-3 py-4 w-full hover:bg-gray-700"><i class="fa-regular fa-circle"></i> បញ្ជីព្រឹត្តិការណ៍</a></li>
            </ul>
        </li>

        <!-- Skill Dropdown -->
        <li x-data="{ open: false }">
            <button @click="open=!open" class="w-full flex justify-between items-center px-3 py-4 hover:bg-gray-700">
                <span class="flex items-center gap-3"><i class="fa-solid fa-plus"></i> ជំនាញ</span>
                <i :class="open?'fa-chevron-up':'fa-chevron-down'" class="fa-solid"></i>
            </button>
            <ul x-show="open" x-transition class="pl-6 px-3 mt-1 space-y-4 text-sm dropdown-menu">
                <li><a href="../admin/create_skills.php" class="flex items-center gap-3 py-4 w-full hover:bg-gray-700"><i class="fa-regular fa-circle"></i> បង្កើតជំនាញថ្មី</a></li>
                
            </ul>
        </li>
           <!-- attendance  -->
        <li x-data="{ open: false }">
            <button @click="open=!open" class="w-full flex justify-between items-center px-3 py-4 hover:bg-gray-700">
                <span class="flex items-center gap-3"><i class="fa-solid fa-clipboard-user"></i>គ្រប់គ្រងវត្តមាន</span>
                <i :class="open?'fa-chevron-up':'fa-chevron-down'" class="fa-solid"></i>
            </button>
            <ul x-show="open" x-transition class="pl-6 px-3 mt-1 space-y-4 text-sm dropdown-menu">
                <li><a href="../admin/attendance.php" class="flex items-center gap-3 py-4 w-full hover:bg-gray-700"><i class="fa-regular fa-circle"></i>បញ្ជីវត្តមានអ្នកចូលរួម</a></li>
            </ul>
        </li>

        <!-- Announcements Dropdown -->
        <li x-data="{ open: false }">
            <button @click="open=!open" class="w-full flex justify-between items-center px-3 py-4 hover:bg-gray-700">
                <span class="flex items-center gap-3"><i class="fa-solid fa-bullhorn"></i> សេចក្ដីជូនដំណឹង</span>
                <i :class="open?'fa-chevron-up':'fa-chevron-down'" class="fa-solid"></i>
            </button>
            <ul x-show="open" x-transition class="pl-6 px-3 mt-1 space-y-3 text-sm dropdown-menu">
                <li><a href="../admin/create_announcements.php" class="flex items-center gap-3 py-4 w-full hover:bg-gray-700"><i class="fa-regular fa-circle"></i> បង្កើតសេចក្ដីជូនដំណឹង</a></li>
                <li><a href="../admin/announcements_list.php" class="flex items-center gap-3 py-4 w-full hover:bg-gray-700"><i class="fa-regular fa-circle"></i> បញ្ជីសេចក្ដីជូនដំណឹង</a></li>
            </ul>
        </li>
    </ul>
</aside>


<!-- Main content -->
<main class="flex-1 ml-0 duration-0" :class="sidebarOpen ? 'ml-64' : 'ml-0'">
    <!-- Top navbar -->
    <div class="bg-white border-b p-4 flex justify-between items-center sticky top-0 z-10">
        <button @click="sidebarOpen = !sidebarOpen" class="text-xl text-gray-700 hover:text-blue-500">
            <i :class="sidebarOpen ? 'fa-bars' : 'fa-bars'" class="fa-solid"></i>
        </button>
        
       <div class="relative" x-data="{ open: false }">
    <div class="flex items-center gap-4 cursor-pointer" @click="open = !open">
        <i class="fa-regular fa-bell"></i>
        <img src="<?= $profile_img ?>" class="w-12 h-12 rounded-full border-2 border-green-500 object-cover" alt="Admin">

    </div>

    <!-- Dropdown menu -->
    <div x-show="open" @click.outside="open = false" x-transition 
         class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-2 z-20">
        <a href="../admin/profile_admin.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
            <i class="fa-solid fa-id-badge mr-2"></i> ព័ត៌មានគណនី
        </a>
        <a href="../admin/logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-100">
            <i class="fa-solid fa-sign-out-alt mr-2"></i> ចាកចេញ
        </a>
    </div>
</div>
</div>
<section>
  <div class="flex justify-center items-center min-h-screen bg-white p-4">
    <div class="bg-white rounded-lg p-6 w-full sm:w-[80%] shadow-lg">
      <?php if ($error): ?>
        <p class="text-red-500 text-center mb-4"><?= htmlspecialchars($error) ?></p>
      <?php elseif ($success): ?>
        <p class="text-green-500 text-center mb-4"><?= htmlspecialchars($success) ?></p>
      <?php endif; ?>

      <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
          <label class="block mb-1 font-semibold">ឈ្មោះអ្នកប្រើ</label>
          <input type="text" name="username" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400" value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>" />
        </div>

        <div>
          <label class="block mb-1 font-semibold">លេខសម្គាល់សិស្ស</label>
          <input type="text" name="student_id" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400" value="<?= isset($_POST['student_id']) ? htmlspecialchars($_POST['student_id']) : '' ?>" />
        </div>

        <div>
          <label class="block mb-1 font-semibold">ពាក្យសម្ងាត់</label>
          <input type="password" name="password" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400" />
        </div>

        <div>
          <label class="block mb-1 font-semibold">ភេទ</label>
          <select name="gender" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
            <option value="">ជ្រើសរើសភេទ</option>
            <option value="ប្រុស" <?= (isset($_POST['gender']) && $_POST['gender'] === 'ប្រុស') ? 'selected' : '' ?>>ប្រុស</option>
            <option value="ស្រី" <?= (isset($_POST['gender']) && $_POST['gender'] === 'ស្រី') ? 'selected' : '' ?>>ស្រី</option>
          </select>
        </div>

        <div>
          <label class="block mb-1 font-semibold">អុីម៉ែល</label>
          <input type="email" name="gmail" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400" value="<?= isset($_POST['gmail']) ? htmlspecialchars($_POST['gmail']) : '' ?>" />
        </div>

        <div>
          <label class="block mb-1 font-semibold">ជំនាញសិក្សា</label>
          <select name="major" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
            <option value="">ជ្រើសរើសជំនាញសិក្សា</option>
            <?php
            $result = $conn->query("SELECT major_name FROM majors ORDER BY major_name");
            while ($row = $result->fetch_assoc()) {
                $m = $row['major_name'];
                $selected = (isset($_POST['major']) && $_POST['major'] === $m) ? 'selected' : '';
                echo "<option value=\"$m\" $selected>$m</option>";
            }
            ?>
          </select>
        </div>

        <div>
          <label class="block mb-1 font-semibold">ថ្ងៃខែឆ្នាំកំណើត</label>
          <input type="date" name="date" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400" value="<?= isset($_POST['date']) ? htmlspecialchars($_POST['date']) : '' ?>" />
        </div>

        <div>
          <label class="block mb-1 font-semibold">ប្រភេទអ្នកប្រើ</label>
          <select name="user_type" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
            <option value="">ជ្រើសរើសប្រភេទ</option>
            <option value="គ្រូ" <?= (isset($_POST['user_type']) && $_POST['user_type'] === 'គ្រូ') ? 'selected' : '' ?>>គ្រូ</option>
            <option value="សិស្ស" <?= (isset($_POST['user_type']) && $_POST['user_type'] === 'សិស្ស') ? 'selected' : '' ?>>សិស្ស</option>
          </select>
        </div>

        <div class="sm:col-span-2">
          <label class="block mb-1 font-semibold">អាសយដ្ឋាន</label>
          <textarea name="address" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400"><?= isset($_POST['address']) ? htmlspecialchars($_POST['address']) : '' ?></textarea>
        </div>

        <div class="sm:col-span-2">
          <label class="block mb-1 font-semibold">រូបភាព</label>
          <input type="file" name="image" accept="image/jpg, image/jpeg, image/png, image/gif" required class="w-full border rounded py-2 px-4" />
        </div>

        <div class="sm:col-span-2 flex justify-end gap-4 mt-4">
          <button type="submit" class="bg-teal-600 text-white px-6 py-2 rounded shadow">Add</button>
          <a href="manage_users.php" class="bg-red-600 text-white px-6 py-2 rounded shadow">Cancel</a>
        </div>
      </form>
    </div>
</section>
</main>


<script>
  // Dropdown toggle menu
  document.querySelectorAll('#menu .dropdown-toggle').forEach(toggle => {
    toggle.addEventListener('click', e => {
      e.preventDefault();
      const parentLi = toggle.closest('li.dropdown');
      document.querySelectorAll('#menu li.dropdown.active').forEach(item => {
        if (item !== parentLi) item.classList.remove('active');
      });
      parentLi.classList.toggle('active');
    });
  });

  document.addEventListener('click', e => {
    if (!e.target.closest('#menu')) {
      document.querySelectorAll('#menu li.dropdown.active').forEach(item => {
        item.classList.remove('active');
      });
    }
  });

  $('#menu-btn').click(function () {
    $('#menu').toggleClass('active');
  });
</script>
</body>
</html>

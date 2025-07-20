<?php
session_start();
include '../db/conn.php';

$profile_img = '../pic/user.png';
$user_id = $_SESSION['user_id'] ?? 0;

if ($user_id) {
  $stmt = $conn->prepare("SELECT image FROM admin WHERE id = ?");
  $stmt->bind_param("i", $user_id);
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


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $username = trim($_POST['username']);
  $password_raw = $_POST['password'];
  $role = $_POST['role']; // admin or staff
  $email = $_POST['email'];
  $phone = $_POST['phone'];

  // Image upload variables
  $image = $_FILES['image']['name'];
  $image_tmp = $_FILES['image']['tmp_name'];
  $image_size = $_FILES['image']['size'];
  $upload_dir = '../upload_img/';
  

  // Validation
  if ($image_size > 2 * 1024 * 1024) {
    $error = "Image size too large. Maximum 2MB allowed.";
  } elseif (!in_array(strtolower(pathinfo($image, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif'])) {
    $error = "Invalid image format.";
  } else {
    // Check duplicate username
    $stmt_check = $conn->prepare("SELECT id FROM admin WHERE username = ?");
    $stmt_check->bind_param("s", $username);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
      $error = "Username already exists.";
    }
    $stmt_check->close();
  }

  if (!$error) {
    $image_new_name = uniqid('admin_', true) . '.' . pathinfo($image, PATHINFO_EXTENSION);
    $image_path = $upload_dir . $image_new_name;

    if (move_uploaded_file($image_tmp, $image_path)) {
      $password = password_hash($password_raw, PASSWORD_DEFAULT);
      $stmt = $conn->prepare("INSERT INTO admin (username, password, role, email, phone, image) VALUES (?, ?, ?, ?, ?, ?)");
      $stmt->bind_param("ssssss", $username, $password, $role, $email, $phone, $image_new_name);

      if ($stmt->execute()) {
        $success = ucfirst($role) . " registered successfully!";
      } else {
        $error = "Failed to register $role: " . $stmt->error;
        if (file_exists($image_path))
          unlink($image_path);
      }
      $stmt->close();
    } else {
      $error = "Failed to upload image.";
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
    * {
      font-family: 'Khmer OS Siemreap', sans-serif;
    }

    .dropdown-menu {
      transition: all 0.3s ease;
    }
  </style>
</head>

<body class="bg-gray-100 text-gray-800  flex" x-data="{ sidebarOpen: true }">

  <!-- Sidebar -->
  <aside :class="sidebarOpen ? 'w-64' : 'w-0', sidebarOpen ? 'p-4' : 'p-0'"
    class="bg-[#111811] text-white overflow-hidden h-screen fixed left-0 top-0 transition-all duration-300">
    <div class="flex items-center mb-4" x-show="sidebarOpen" x-transition>
      <img src="../pic/logo.jpg" class="w-[60px] h-[60px] mt-2 rounded-full ml-20" alt="logo">
    </div>
    <hr>
    <!-- Sidebar items -->
    <ul class="space-y-1" x-show="sidebarOpen" x-transition>
      <li><a href="../admin/dashbord.php" class="flex items-center gap-3 px-3 py-4 hover:bg-gray-700"><i
            class="fa-solid fa-gauge"></i> Dashboard</a></li>

      <!-- User Dropdown -->
      <li x-data="{ open: false }">
        <button @click="open=!open" class="w-full flex justify-between items-center px-3 py-4 hover:bg-gray-700">
          <span class="flex items-center gap-3"><i class="fa-solid fa-user"></i> សិស្សចុះឈ្មោះ</span>
          <i :class="open?'fa-chevron-up':'fa-chevron-down'" class="fa-solid"></i>
        </button>
        <ul x-show="open" x-transition class="pl-6 px-3 mt-1 space-y-3 text-sm dropdown-menu">
          <li><a href="../admin/add_user.php" class="flex items-center gap-3 py-4 w-full hover:bg-gray-700"><i
                class="fa-regular fa-circle"></i> ចុះឈ្មោះថ្មី</a></li>
          <li><a href="../admin/manage_users.php" class="flex items-center gap-3 py-4 w-full hover:bg-gray-700"><i
                class="fa-regular fa-circle"></i> គ្រប់គ្រងសិស្ស</a></li>
        </ul>
      </li>

      <!-- Event Dropdown -->
      <li x-data="{ open: false }">
        <button @click="open=!open" class="w-full flex justify-between items-center px-3 py-4 hover:bg-gray-700">
          <span class="flex items-center gap-3"><i class="fa-solid fa-calendar-plus"></i> ព្រឹត្តិការណ៍</span>
          <i :class="open?'fa-chevron-up':'fa-chevron-down'" class="fa-solid"></i>
        </button>
        <ul x-show="open" x-transition class="pl-6 px-3 mt-1 space-y-3 text-sm dropdown-menu">
          <li><a href="../admin/create_event.php" class="flex items-center gap-3 py-4 w-full hover:bg-gray-700"><i
                class="fa-regular fa-circle"></i> បង្កើតព្រឹត្តិការណ៍ថ្មី</a></li>
          <li><a href="../admin/events_list.php" class="flex items-center gap-3 py-4 w-full hover:bg-gray-700"><i
                class="fa-regular fa-circle"></i> បញ្ជីព្រឹត្តិការណ៍</a></li>
        </ul>
      </li>

      <!-- Skill Dropdown -->
      <li x-data="{ open: false }">
        <button @click="open=!open" class="w-full flex justify-between items-center px-3 py-4 hover:bg-gray-700">
          <span class="flex items-center gap-3"><i class="fa-solid fa-plus"></i> ជំនាញ</span>
          <i :class="open?'fa-chevron-up':'fa-chevron-down'" class="fa-solid"></i>
        </button>
        <ul x-show="open" x-transition class="pl-6 px-3 mt-1 space-y-4 text-sm dropdown-menu">
          <li><a href="../admin/create_skills.php" class="flex items-center gap-3 py-4 w-full hover:bg-gray-700"><i
                class="fa-regular fa-circle"></i> បង្កើតជំនាញថ្មី</a></li>

        </ul>
      </li>
      <!-- attendance  -->
      <li x-data="{ open: false }">
        <button @click="open=!open" class="w-full flex justify-between items-center px-3 py-4 hover:bg-gray-700">
          <span class="flex items-center gap-3"><i class="fa-solid fa-clipboard-user"></i>គ្រប់គ្រងវត្តមាន</span>
          <i :class="open?'fa-chevron-up':'fa-chevron-down'" class="fa-solid"></i>
        </button>
        <ul x-show="open" x-transition class="pl-6 px-3 mt-1 space-y-4 text-sm dropdown-menu">
          <li><a href="../admin/attendance.php" class="flex items-center gap-3 py-4 w-full hover:bg-gray-700"><i
                class="fa-regular fa-circle"></i>បញ្ជីវត្តមានអ្នកចូលរួម</a></li>
        </ul>
      </li>

      <!-- Announcements Dropdown -->
      <li x-data="{ open: false }">
        <button @click="open=!open" class="w-full flex justify-between items-center px-3 py-4 hover:bg-gray-700">
          <span class="flex items-center gap-3"><i class="fa-solid fa-bullhorn"></i> សេចក្ដីជូនដំណឹង</span>
          <i :class="open?'fa-chevron-up':'fa-chevron-down'" class="fa-solid"></i>
        </button>
        <ul x-show="open" x-transition class="pl-6 px-3 mt-1 space-y-3 text-sm dropdown-menu">
          <li><a href="../admin/create_announcements.php"
              class="flex items-center gap-3 py-4 w-full hover:bg-gray-700"><i class="fa-regular fa-circle"></i>
              បង្កើតសេចក្ដីជូនដំណឹង</a></li>
          <li><a href="../admin/announcements_list.php" class="flex items-center gap-3 py-4 w-full hover:bg-gray-700"><i
                class="fa-regular fa-circle"></i> បញ្ជីសេចក្ដីជូនដំណឹង</a></li>
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
          <img src="<?= $profile_img ?>" class="w-12 h-12 rounded-full border-2 border-green-500 object-cover"
            alt="Admin">

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

          <form method="POST" enctype="multipart/form-data"
            class="max-w-lg mx-auto p-6 bg-white rounded shadow-md space-y-6">
            <div>
              <label for="username" class="block mb-2 font-semibold text-gray-700">ឈ្មោះអ្នកប្រើ</label>
              <input id="username" name="username" type="text" required
                value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>"
                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500" />
            </div>

            <div>
              <label for="password" class="block mb-2 font-semibold text-gray-700">ពាក្យសម្ងាត់</label>
              <input id="password" name="password" type="password" required
                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500" />
            </div>

            <!-- Role -->
<div>
  <label for="role" class="block mb-2 font-semibold text-gray-700">តួនាទី</label>
  <select id="role" name="role" required
    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500">
    <option value="">ជ្រើសរើសតួនាទី</option>
    <option value="admin" <?= (isset($_POST['role']) && $_POST['role'] == 'admin') ? 'selected' : '' ?>>Admin</option>
    <option value="staff" <?= (isset($_POST['role']) && $_POST['role'] == 'staff') ? 'selected' : '' ?>>Staff</option>
  </select>
</div>

<!-- Phone -->
<div>
  <label for="phone" class="block mb-2 font-semibold text-gray-700">លេខទូរស័ព្ទ</label>
  <input id="phone" name="phone" type="text" required
    value="<?= isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : '' ?>"
    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500" />
</div>

<!-- Gmail -->
<div>
  <label for="email" class="block mb-2 font-semibold text-gray-700">អ៊ីមែល</label>
  <input id="email" name="email" type="email" required
    value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>"
    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500" />
</div>


            <div>
              <label for="image" class="block mb-2 font-semibold text-gray-700">រូបភាព</label>
              <input id="image" name="image" type="file" accept="image/jpg, image/jpeg, image/png, image/gif" required
                class="w-full border border-gray-300 rounded-md py-2 px-4 cursor-pointer" />
            </div>

            <div class="flex justify-end gap-4 mt-4">
              <button type="submit"
                class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-2 rounded shadow transition">Add</button>
              <a href="manage_users.php"
                class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded shadow transition">Cancel</a>
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
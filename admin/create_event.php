<?php
session_start();
include '../db/conn.php';
$profile_img = '../pic/user.png'; 
$user_id = $_SESSION['user_id'] ?? 0;

if ($user_id) {
    $stmt = $conn->prepare("SELECT image FROM users WHERE id = ?");
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
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login_admin.php"); 
    exit();
}

// Insert event when submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title    = $_POST['title'];
    $desc     = $_POST['description'];
    $date     = $_POST['event_date'];
    $time     = $_POST['event_start'];
    $time     = $_POST['event_end'];
    $location = $_POST['location'];

    $sql = "INSERT INTO events (title, description, event_date, event_start, event_end, location) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $title, $desc, $date, $time, $time, $location);
    
    if ($stmt->execute()) {
        header("Location: events_list.php?success=1");
        exit();
    } else {
        $error = "បញ្ចូលព្រឹត្តិការណ៍មិនជោគជ័យ!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard</title>
<link href="../dist/style.css" rel="stylesheet">
<script src="//unpkg.com/alpinejs" defer></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
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
<main class="flex-1 ml-0 transition-all duration-300" :class="sidebarOpen ? 'ml-64' : 'ml-0'">
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
<section class="px-4 py-6">
  <div class="max-w-3xl mx-auto bg-white p-6 sm:p-10 rounded-lg shadow-lg">
    <h2 class="text-2xl font-bold text-center text-teal-600 mb-6">បង្កើតព្រឹត្តិការណ៍ថ្មី</h2>

    <?php if (!empty($error)): ?>
      <p class="text-red-500 mb-4 text-center"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST" class="space-y-5 text-teal-700">
      <div>
        <label class="block mb-1 font-semibold">ចំណងជើង</label>
        <input type="text" name="title" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
      </div>

      <div>
        <label class="block mb-1 font-semibold">ពិពណ៌នា</label>
        <input type="text" name="description" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
      </div>

      <div>
        <label class="block mb-1 font-semibold">ថ្ងៃខែ</label>
        <input type="date" name="event_date" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block mb-1 font-semibold">ចាប់ផ្តើមពីម៉ោង</label>
          <input type="time" name="event_start" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>

        <div>
          <label class="block mb-1 font-semibold">ដល់ម៉ោង</label>
          <input type="time" name="event_end" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
      </div>

      <div>
        <label class="block mb-1 font-semibold">ទីតាំង</label>
        <textarea name="location" rows="3" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400"></textarea>
      </div>

     <div class="flex justify-end items-center pt-4 gap-2">
      <a href="" class="bg-red-600 text-white px-6 py-2 rounded hover:bg-red-700">
        បោះបង់
      </a>
      <button type="submit" class="bg-teal-600 text-white px-6 py-2 rounded hover:bg-teal-700">
        បង្កើត
      </button>
</div>

    </form>
  </div>
</section>


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
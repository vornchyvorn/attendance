<?php
session_start();
include '../db/conn.php';

// Redirect to login if not admin
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../admin/login_admin.php');
    exit();
}

$profile_img = '../pic/user.png'; 
$admin_id = $_SESSION['admin_id'];

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

$sql = "SELECT * FROM announcements ORDER BY created_at DESC";
$result = $conn->query($sql);
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

<body class="bg-gray-100 text-gray-800 flex" 
      x-data="{ 
        sidebarOpen: true, 
        editModal: false, 
        deleteModal: false, 
        selected: {} 
      }">

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
                <span class="flex items-center gap-3"><i class="fa-solid fa-user"></i>ទិន្នន័យអ្នកប្រើប្រាស់</span>
                <i :class="open?'fa-chevron-up':'fa-chevron-down'" class="fa-solid"></i>
            </button>
            <ul x-show="open" x-transition class="pl-6 px-3 mt-1 space-y-3 text-sm dropdown-menu">
                <li><a href="../admin/add_user.php" class="flex items-center gap-3 py-4 w-full hover:bg-gray-700"><i class="fa-regular fa-circle"></i> ចុះឈ្មោះថ្មី</a></li>
                <li><a href="../admin/manage_users.php" class="flex items-center gap-3 py-4 w-full hover:bg-gray-700"><i class="fa-regular fa-circle"></i> គ្រប់គ្រងអ្នកប្រើប្រាស់</a></li>
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
<div class="max-w-6xl mx-auto mt-10 p-6 bg-white rounded shadow">
  <h1 class="text-2xl font-bold text-green-700 mb-6 text-center">គ្រប់គ្រងសេចក្ដីជូនដំណឹង</h1>

  <a href="create_announcements.php" class="inline-block mb-4 bg-sky-500 hover:bg-sky-600 text-white px-4 py-2 rounded">
    <i class="fa-solid fa-plus"></i> បន្ថែមសេចក្ដីជូនដំណឹង
  </a>

  <?php if ($result->num_rows > 0): ?>
    <div class="space-y-4">
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="border-l-4 border-green-500 bg-blue-50 p-4 rounded shadow-sm relative">
          <h2 class="text-lg font-semibold text-green-600"><?= htmlspecialchars($row['title']) ?></h2>
          <p class="text-gray-700 mb-2"><?= nl2br(htmlspecialchars($row['content'])) ?></p>
          <p class="text-sm text-gray-500">ថ្ងៃបញ្ចូល៖ <?= date('d-m-Y H:i', strtotime($row['created_at'])) ?></p>
          <p class="text-sm"><strong>ស្ថានភាព៖</strong>
            <?= $row['status'] === 'active' ? '<span class="text-green-600">🟢 Active</span>' : '<span class="text-red-600">🔴 Inactive</span>' ?>
          </p>

          <div class="absolute top-4 right-4 flex gap-2">
            <button @click="editModal = true; selected = <?= htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8') ?>"><i class="fa-solid fa-pen text-blue-600"></i></button>
            <button @click="deleteModal = true; selected = { id: <?= $row['id'] ?> }"><i class="fa-solid fa-trash text-red-600"></i></button>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  <?php else: ?>
    <p class="text-center text-gray-600 mt-10">មិនមានសេចក្ដីជូនដំណឹង។</p>
  <?php endif; ?>
</div>
</main>

<!-- Edit Modal -->
<div x-show="editModal" class="fixed inset-0 z-50 flex items-center justify-center bg-opacity-40" x-transition>
  <div class="bg-white w-full max-w-xl p-6 rounded shadow" @click.away="editModal = false">
    <h2 class="text-xl font-bold text-green-600 mb-4 text-center">កែប្រែសេចក្ដីជូនដំណឹង</h2>
    <form action="edit_announcement.php" method="POST" class="space-y-4">
      <input type="hidden" name="id" :value="selected.id">
      <div>
        <label class="block mb-1">ចំណងជើង</label>
        <input type="text" name="title" class="w-full border px-3 py-2 rounded" :value="selected.title">
      </div>
      <div>
        <label class="block mb-1">មាតិកា</label>
        <textarea name="content" rows="5" class="w-full border px-3 py-2 rounded" x-text="selected.content"></textarea>
      </div>
      <div>
        <label class="block mb-1">ស្ថានភាព</label>
        <select name="status" class="w-full border px-3 py-2 rounded">
          <option value="active" :selected="selected.status === 'active'">🟢 Active</option>
          <option value="inactive" :selected="selected.status === 'inactive'">🔴 Inactive</option>
        </select>
      </div>
      <div class="flex justify-end gap-2">
        <button type="button" @click="editModal = false" class="px-4 py-2 bg-gray-300 rounded">បោះបង់</button>
        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">រក្សាទុក</button>
      </div>
    </form>
  </div>
</div>

<!-- Delete Modal -->
<div x-show="deleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-opacity-40" x-transition>
  <div class="bg-white w-full max-w-sm p-6 rounded shadow" @click.away="deleteModal = false">
    <h3 class="text-lg font-md text-red-600 mb-4 text-center">តើអ្នកពិតជាចង់លុបសេចក្ដីជូនដំណឹងនេះមែនទេ?</h3>
    <div class="flex justify-end gap-2">
      <button @click="deleteModal = false" class="px-4 py-2 bg-gray-300 rounded">ទេ</button>
      <a :href="`delete_announcement.php?id=${selected.id}`" class="px-4 py-2 bg-red-500 text-white rounded">បាទ/ចាស</a>
    </div>
  </div>
</div>

</body>
</html>

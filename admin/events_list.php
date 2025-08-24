<?php
session_start();
include '../db/conn.php';
if (!isset($_SESSION['admin_id'])) {
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


$sql = "SELECT * FROM events ORDER BY event_date DESC";
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
  <link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:ital,wght@0,100..700;1,100..700&family=Koulen&display=swap" rel="stylesheet">
  <style>
    * {
       font-family: "Kantumruy Pro", sans-serif;
    }
    .dropdown-menu {
      transition: all 0.3s ease;
    }
  </style>
</head>

<body class="bg-gray-100 text-gray-800 flex"
      x-data="{
        sidebarOpen: true,
        editModal: false,
        editEvent: {},
        deleteModal: false,
        deleteId: null
      }">

<!-- Sidebar -->
<aside :class="sidebarOpen ? 'w-64' : 'w-0', sidebarOpen ? 'p-4' : 'p-0'" class="bg-[#111811] text-white text-lg overflow-hidden h-screen fixed left-0 top-0 transition-all duration-300">
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
                <span class="flex items-center gap-3"><i class="fa-solid fa-user"></i> ទិន្នន័យអ្នកប្រើប្រាស់</span>
                <i :class="open?'fa-chevron-up':'fa-chevron-down'" class="fa-solid"></i>
            </button>
            <ul x-show="open" x-transition class="pl-6 px-3 mt-1 space-y-3 text-base dropdown-menu">
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
            <ul x-show="open" x-transition class="pl-6 px-3 mt-1 space-y-3 text-base  dropdown-menu">
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
            <ul x-show="open" x-transition class="pl-6 px-3 mt-1 space-y-4 text-base dropdown-menu">
                <li><a href="../admin/create_skills.php" class="flex items-center gap-3 py-4 w-full hover:bg-gray-700"><i class="fa-regular fa-circle"></i> បង្កើតជំនាញថ្មី</a></li>
                
            </ul>
        </li>
           <!-- attendance  -->
        <li x-data="{ open: false }">
            <button @click="open=!open" class="w-full flex justify-between items-center px-3 py-4 hover:bg-gray-700">
                <span class="flex items-center gap-3"><i class="fa-solid fa-clipboard-user"></i>គ្រប់គ្រងវត្តមាន</span>
                <i :class="open?'fa-chevron-up':'fa-chevron-down'" class="fa-solid"></i>
            </button>
            <ul x-show="open" x-transition class="pl-6 px-3 mt-1 space-y-4 text-base dropdown-menu">
                <li><a href="../admin/attendance.php" class="flex items-center gap-3 py-4 w-full hover:bg-gray-700"><i class="fa-regular fa-circle"></i>បញ្ជីវត្តមានអ្នកចូលរួម</a></li>
            </ul>
        </li>

        <!-- Announcements Dropdown -->
        <li x-data="{ open: false }">
            <button @click="open=!open" class="w-full flex justify-between items-center px-3 py-4 hover:bg-gray-700">
                <span class="flex items-center gap-3"><i class="fa-solid fa-bullhorn"></i> សេចក្ដីជូនដំណឹង</span>
                <i :class="open?'fa-chevron-up':'fa-chevron-down'" class="fa-solid"></i>
            </button>
            <ul x-show="open" x-transition class="pl-6 px-3 mt-1 space-y-3 text-base dropdown-menu">
                <li><a href="../admin/create_announcements.php" class="flex items-center gap-3 py-4 w-full hover:bg-gray-700"><i class="fa-regular fa-circle"></i> បង្កើតសេចក្ដីជូនដំណឹង</a></li>
                <li><a href="../admin/announcements_list.php" class="flex items-center gap-3 py-4 w-full hover:bg-gray-700"><i class="fa-regular fa-circle"></i> បញ្ជីសេចក្ដីជូនដំណឹង</a></li>
            </ul>
        </li>
    </ul>
</aside>


<!-- Main Content -->
<main class="flex-1 ml-0 duration-0" :class="sidebarOpen ? 'ml-64' : 'ml-0'">
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
        <a href="../admin/profile_admin.php" class="block px-4 py-2 text-base  text-gray-700 hover:bg-gray-100">
            <i class="fa-solid fa-id-badge mr-2"></i> ព័ត៌មានគណនី
        </a>
        <a href="../admin/logout.php" class="block px-4 py-2 text-base text-red-600 hover:bg-red-100">
            <i class="fa-solid fa-sign-out-alt mr-2"></i> ចាកចេញ
        </a>
    </div>
</div>
</div>

  <div class="container mx-auto mt-10 p-6 bg-white rounded shadow-md">
    <h1 class="text-2xl font-bold text-teal-700 mb-4">បញ្ជីព្រឹត្តិការណ៍</h1>

    <a href="create_event.php" class="mb-4 inline-block bg-sky-500 hover:bg-sky-600 text-white px-4 py-2 rounded">
      បង្កើតព្រឹត្តិការណ៍ថ្មី
    </a>

    <div class="overflow-x-auto ">
      <table class="min-w-full border border-gray-300 text-sm sm:text-base shadow-sm">
        <thead class="bg-green-700 text-white">
          <tr>
            <th class="border px-4 py-2">#</th>
            <th class="border px-4 py-2">ចំណងជើង</th>
            <th class="border px-4 py-2">ថ្ងៃ-ខែ-ឆ្នាំ</th>
            <th class="border px-4 py-2">To time</th>
            <th class="border px-4 py-2">End time</th>
            <th class="border px-4 py-2">ទីតាំង</th>
            <th class="border px-4 py-2">ពិពណ៌នា</th>
            <th class="border px-4 py-2">សកម្មភាព</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result->num_rows > 0): ?>
            <?php $i = 1; while ($row = $result->fetch_assoc()): ?>
              <tr class="hover:bg-gray-50">
                <td class="border px-4 py-2"><?= $i++ ?></td>
                <td class="border px-4 py-2"><?= htmlspecialchars($row['title']) ?></td>
                <td class="border px-4 py-2"><?= date('d-m-Y', strtotime($row['event_date'])) ?></td>
                <td class="border px-4 py-2"><?= date('g:i A', strtotime($row['event_start'])) ?></td>
                <td class="border px-4 py-2"><?= date('g:i A', strtotime($row['event_end'])) ?></td>

                <td class="border px-4 py-2"><?= htmlspecialchars($row['location']) ?></td>
                <td class="border px-4 py-2"><?= htmlspecialchars($row['description']) ?></td>
                <td class="border px-4 py-2 whitespace-nowrap">
                  <button
                    @click="editModal = true; editEvent = <?= htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8') ?>"
                    class="text-blue-600"><i class="fa-solid fa-pen-to-square"></i>
                  </button>
                  <button
                    @click="deleteModal = true; deleteId = <?= $row['id'] ?>"
                    class="text-red-600 hover:underline ml-2"><i class="fa-solid fa-trash"></i>
                  </button>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="7" class="text-center text-gray-500 py-4">មិនមានព្រឹត្តិការណ៍</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</main>

<!-- Edit Event Modal -->
<div x-show="editModal" class="fixed inset-0 z-50 flex items-center justify-center bg-opacity-40" x-transition>
  <div class="bg-white w-full max-w-xl p-6 rounded shadow" @click.away="editModal = false">
    <h2 class="text-xl font-bold text-center text-blue-600 mb-4">កែប្រែព្រឹត្តិការណ៍</h2>
    <form action="edit_event.php" method="POST" class="space-y-4">
      <input type="hidden" name="id" :value="editEvent.id">
      <div>
        <label class="block mb-1">ចំណងជើង</label>
        <textarea type="text" name="title" class="w-full border rounded px-3 py-2" :value="editEvent.title"></textarea>
      </div>
      <div>
        <label class="block mb-1">ពិពណ៌នា</label>
        <textarea type="text" name="description" class="w-full border rounded px-3 py-2" :value="editEvent.description"></textarea>
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block mb-1">ថ្ងៃខែ</label>
          <input type="date" name="event_date" class="w-full border rounded px-3 py-2" :value="editEvent.event_date">
        </div>
        <div>
          <label class="block mb-1">to time</label>
          <input type="time" name="event_start" class="w-full border rounded px-3 py-2" :value="editEvent.event_start">
        </div>
        <div>
          <label class="block mb-1">end time</label>
          <input type="time" name="event_end" class="w-full border rounded px-3 py-2" :value="editEvent.event_end">
        </div>
      </div>
      <div>
        <label class="block mb-1">ទីតាំង</label>
        <input type="text" name="location" class="w-full border rounded px-3 py-2" :value="editEvent.location">
      </div>
      <div class="flex justify-end gap-2">
        <button type="button" @click="editModal = false" class="px-4 py-2 bg-red-600 rounded text-white">បោះបង់</button>
        <button type="submit" class="px-4 py-2 bg-teal-600 text-white rounded">រក្សាទុក</button>
      </div>
    </form>
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div x-show="deleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-opacity-40" x-transition>
  <div class="bg-white w-full max-w-sm p-6 rounded shadow" @click.away="deleteModal = false">
    <h3 class="text-lg font-md text-red-500 mb-4 text-center">តើអ្នកពិតជាចង់លុបព្រឹត្តិការណ៍នេះមែនទេ?</h3>
    <div class="flex justify-end gap-2">
      <button @click="deleteModal = false" class="px-4 py-2 bg-gray-300 rounded">ទេ</button>
      <a :href="`delete_event.php?id=${deleteId}`" class="px-4 py-2 bg-red-500 text-white rounded">បាទ/ចាស</a>
    </div>
  </div>
</div>

</body>
</html>

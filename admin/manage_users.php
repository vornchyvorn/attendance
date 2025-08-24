<?php
session_start();
include '../db/conn.php';

// Redirect if not admin

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../admin/login_admin.php');
    exit();
}
$profile_img = '../pic/user.png';
$admin_id = $_SESSION['admin_id'];

// Fetch profile image
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

// ✅ Pagination logic
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 10; // Rows per page (default 10)
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1; // Current page
if ($page < 1)
  $page = 1;

$offset = ($page - 1) * $limit;

// Count total rows
$totalResult = $conn->query("SELECT COUNT(*) as total FROM users");
$totalRows = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

// Fetch paginated data
$sql = "SELECT * FROM users ORDER BY id DESC LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);
?>

<?php
include '../db/conn.php';

// Limit & Page setup
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 10; 
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;     
$offset = ($page - 1) * $limit;

// Get total rows
$totalQuery = $conn->query("SELECT COUNT(*) AS total FROM users");
$totalRows = $totalQuery->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

// Fetch paginated data
$sql = "SELECT * FROM users ORDER BY id DESC LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <script src="https://unpkg.com/alpinejs" defer></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:ital,wght@0,100..700;1,100..700&family=Koulen&display=swap" rel="stylesheet">
  <link href="../dist/style.css" rel="stylesheet">
  <style>
    * {
      font-family: "Kantumruy Pro", sans-serif;
    }

    .dropdown-menu {
      transition: all 0.3s ease;
    }
  </style>
</head>

<body class="bg-gray-100 text-gray-800 flex" x-data="{
        sidebarOpen: true,
        editModal: false,
        editEvent: {},
        deleteModal: false,
        deleteId: null
      }">

  <!-- Sidebar -->
  <aside :class="sidebarOpen ? 'w-64' : 'w-0', sidebarOpen ? 'p-4' : 'p-0'"
    class="bg-[#111811] text-white overflow-hidden h-screen fixed text-lg left-0 top-0 transition-all duration-300">
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
          <span class="flex items-center gap-3"><i class="fa-solid fa-user"></i> ទិន្នន័យអ្នកប្រើប្រាស់</span>
          <i :class="open?'fa-chevron-up':'fa-chevron-down'" class="fa-solid"></i>
        </button>
        <ul x-show="open" x-transition class="pl-6 px-3 mt-1 space-y-3 text-base dropdown-menu">
          <li><a href="../admin/add_user.php" class="flex items-center gap-3 py-4 w-full hover:bg-gray-700"><i
                class="fa-regular fa-circle"></i> ចុះឈ្មោះថ្មី</a></li>
          <li><a href="../admin/manage_users.php" class="flex items-center gap-3 py-4 w-full hover:bg-gray-700"><i
                class="fa-regular fa-circle"></i> គ្រប់គ្រងអ្នកប្រើប្រាស់</a></li>
        </ul>
      </li>

      <!-- Event Dropdown -->
      <li x-data="{ open: false }">
        <button @click="open=!open" class="w-full flex justify-between items-center px-3 py-4 hover:bg-gray-700">
          <span class="flex items-center gap-3"><i class="fa-solid fa-calendar-plus"></i> ព្រឹត្តិការណ៍</span>
          <i :class="open?'fa-chevron-up':'fa-chevron-down'" class="fa-solid"></i>
        </button>
        <ul x-show="open" x-transition class="pl-6 px-3 mt-1 space-y-3 text-base dropdown-menu">
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
        <ul x-show="open" x-transition class="pl-6 px-3 mt-1 space-y-4 text-base dropdown-menu">
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
        <ul x-show="open" x-transition class="pl-6 px-3 mt-1 space-y-4 text-base dropdown-menu">
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
        <ul x-show="open" x-transition class="pl-6 px-3 mt-1 space-y-3 text-base dropdown-menu">
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
          <a href="../admin/profile_admin.php" class="block px-4 py-2 text-base text-gray-700 hover:bg-gray-100">
            <i class="fa-solid fa-id-badge mr-2"></i> ព័ត៌មានគណនី
          </a>
          <a href="../admin/logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-100">
            <i class="fa-solid fa-sign-out-alt mr-2"></i> ចាកចេញ
          </a>
        </div>
      </div>
    </div>
    <section>

      <div class="container mx-auto mt-8 p-6 bg-white shadow-md rounded-md"
        x-data="{ search: '', showEditModal: false, editUser: {}, showDetailModal: false }">
        <h2 class="text-teal-700 text-2xl mb-4 font-semibold"> បញ្ជីអ្នកប្រើប្រាស់</h2>
        <!-- Search & Add User -->
        <div class="flex justify-between mb-4">
          <input type="text" x-model="search" placeholder="ស្វែងរកឈ្មោះ / ជំនាញ"
            class="w-1/3 px-4 py-2 border rounded shadow focus:outline-none focus:ring-2 focus:ring-blue-500" />

          <a href="../admin/add_user.php"
            class="bg-green-800 text-white px-4 py-2 rounded hover:bg-green-900 flex items-center gap-2 shadow">
            <i class="fa-solid fa-user-plus"></i> បន្ថែមអ្នកប្រើ
          </a>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto w-full rounded-lg border border-gray-200 shadow-sm">
          <table class="min-w-max w-full border-collapse text-sm text-center text-gray-700">
            <thead class="bg-green-800 text-white">
              <tr>
                <th class="border border-green-800 px-3 py-2 whitespace-nowrap">#</th>
                <th class="border border-green-800 px-3 py-2 whitespace-nowrap">អត្តលេខ</th>
                <th class="border border-green-800 px-3 py-2 whitespace-nowrap">ឈ្មោះ</th>
                <th class="border border-green-800 px-3 py-2 whitespace-nowrap">អុីមែល</th>
                <th class="border border-green-800 px-3 py-2 whitespace-nowrap">ជំនាញ</th>
                <th class="border border-green-800 px-3 py-2 whitespace-nowrap">ភេទ</th>
                <th class="border border-green-800 px-3 py-2 whitespace-nowrap">កម្រិតវប្បធម៍</th>
                <th class="border border-green-800 px-3 py-2 whitespace-nowrap">ឆ្នាំ</th>
                <th class="border border-green-800 px-3 py-2 whitespace-nowrap">ផ្សេងៗ</th>
              </tr>
            </thead>

            <tbody class="bg-white divide-y divide-gray-200">
              <?php if ($result->num_rows > 0):
                $i = 1;
                while ($row = $result->fetch_assoc()): ?>
                  <tr class="hover:bg-green-50 transition-colors" x-show="search === '' || 
                                  '<?= strtolower($row['username']) ?>'.includes(search.toLowerCase()) || 
                                  '<?= strtolower($row['major']) ?>'.includes(search.toLowerCase())">
                    <td class="border border-gray-200 px-3 py-2"><?= $i++ ?></td>
                    <td class="border border-gray-200 px-3 py-2"><?= htmlspecialchars($row['student_id']) ?></td>
                    <!-- <td class="border border-gray-200 px-3 py-2"><?= htmlspecialchars($row['image']) ?></td> -->
                    <td class="border border-gray-200 px-3 py-2"><?= htmlspecialchars($row['username']) ?></td>
                    <td class="border border-gray-200 px-3 py-2"><?= htmlspecialchars($row['gmail']) ?></td>
                    <td class="border border-gray-200 px-3 py-2"><?= htmlspecialchars($row['major']) ?></td>
                    <td class="border border-gray-200 px-3 py-2"><?= htmlspecialchars($row['gender']) ?></td>
                    <td class="border border-gray-200 px-3 py-2"><?= htmlspecialchars($row['Education_level']) ?></td>
                    <td class="border border-gray-200 px-3 py-2"><?= htmlspecialchars($row['school_year']) ?></td>
                    <td class="border border-gray-200 px-3 py-2 flex gap-2 justify-center">
                      <!-- Detail -->
                      <button @click="editUser = { 
                        id: '<?= $row['id'] ?>',
                        student_id: '<?= $row['student_id'] ?>',
                        username: '<?= $row['username'] ?>',
                        gmail: '<?= $row['gmail'] ?>',
                        major: '<?= $row['major'] ?>',
                        gender: '<?= $row['gender'] ?>',
                        Education_level: '<?= $row['Education_level'] ?>',
                        school_year: '<?= $row['school_year'] ?>',
                        user_type: '<?= $row['user_type'] ?>',
                        date: '<?= $row['date'] ?>',
                        phone: '<?= $row['phone'] ?>',
                        address: '<?= $row['address'] ?>'
                      };
                      showDetailModal = true;" class="text-green-600 hover:text-green-800 flex items-center gap-1">
                        <i class="fa-solid fa-eye"></i>
                      </button>

                      <!-- Edit -->
                      <button @click="showEditModal = true; /* Existing code */"
                        class="text-blue-600 hover:text-blue-800 flex items-center gap-1">
                        <i class="fa-solid fa-pen"></i>
                      </button>

                      <!-- Delete -->
                      <button @click="deleteId = <?= $row['id'] ?>; deleteModal = true"
                        class="text-red-600 hover:underline ml-2">
                        <i class="fa-solid fa-trash"></i>
                      </button>
                    </td>

                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr>
                  <td colspan="7" class="text-end text-gray-500 py-4">មិនមានទិន្នន័យទេ។</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
          <!-- Pagination UI -->
          <div class="flex items-center justify-between mt-4">
            <!-- Rows per page -->
            <form method="GET" class="flex items-center gap-2">
              <span>បង្ហាញ:</span>
              <select name="limit" onchange="this.form.submit()" class="border rounded px-2 py-1">
                <option value="10" <?= $limit == 10 ? 'selected' : '' ?>>10</option>
                <option value="25" <?= $limit == 25 ? 'selected' : '' ?>>25</option>
                <option value="50" <?= $limit == 50 ? 'selected' : '' ?>>50</option>
                <option value="100" <?= $limit == 100 ? 'selected' : '' ?>>100</option>
              </select>
              <input type="hidden" name="page" value="<?= $page ?>">
            </form>

            <!-- Pagination links -->
            <div class="flex gap-2">
              <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>&limit=<?= $limit ?>"
                  class="px-3 py-1 border rounded hover:bg-gray-100">Prev</a>
              <?php endif; ?>

              <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?= $i ?>&limit=<?= $limit ?>"
                  class="px-3 py-1 border rounded hover:bg-green-100 <?= $i == $page ? 'bg-green-700 text-white' : '' ?>">
                  <?= $i ?>
                </a>
              <?php endfor; ?>

              <?php if ($page < $totalPages): ?>
                <a href="?page=<?= $page + 1 ?>&limit=<?= $limit ?>"
                  class="px-3 py-1 border rounded hover:bg-gray-100">Next</a>
              <?php endif; ?>
            </div>
          </div>

        </div>
        <!-- Edit Modal -->
        <div x-show="showEditModal" style="display: none;"
          class="fixed inset-0 bg-opacity-50 flex items-center justify-center z-50" @click.away="showEditModal = false">
          <div class="bg-white p-6 rounded-lg w-full max-w-2xl relative shadow-lg">
            <h2 class="text-xl font-semibold mb-4">កែសម្រួលអ្នកប្រើ</h2>

            <form method="POST" action="update_user.php">
              <input type="hidden" name="id" x-model="editUser.id" />

              <!-- ចាប់ផ្តើមជួរពីរជួរ -->
              <div class="grid grid-cols-2 gap-4">
                <!-- ជួរទី១ -->
                <div class="mb-3">
                  <label class="block mb-1">អត្តលេខ</label>
                  <input type="text" name="student_id" x-model="editUser.student_id"
                    class="w-full border px-3 py-2 rounded" required>
                </div>

                <div class="mb-3">
                  <label class="block mb-1">ឈ្មោះ</label>
                  <input type="text" name="username" x-model="editUser.username" class="w-full border px-3 py-2 rounded"
                    required>
                </div>

                <div class="mb-3">
                  <label class="block mb-1">អ៊ីមែល</label>
                  <input type="email" name="gmail" x-model="editUser.gmail" class="w-full border px-3 py-2 rounded"
                    required>
                </div>

                <div class="mb-3">
                  <label class="block mb-1">ជំនាញ</label>
                  <input type="text" name="major" x-model="editUser.major" class="w-full border px-3 py-2 rounded"
                    required>
                </div>

                <!-- ជួរទី២ -->
                <div class="mb-3">
                  <label class="block mb-1">ភេទ</label>
                  <input type="text" name="gender" x-model="editUser.gender" class="w-full border px-3 py-2 rounded"
                    required>
                </div>

                <div class="mb-3">
                  <label class="block mb-1">កម្រិតវប្បធម៍</label>
                  <input type="text" name="Education_level" x-model="editUser.Education_level"
                    class="w-full border px-3 py-2 rounded" required>
                </div>


                <div class="mb-3">
                  <label class="block mb-1">ឆ្នាំទី</label>
                  <input type="text" name="school_year" x-model="editUser.school_year"
                    class="w-full border px-3 py-2 rounded" required>
                </div>

                <div class="mb-3">
                  <label class="block mb-1">ប្រភេទអ្នកប្រើ</label>
                  <input type="text" name="user_type" x-model="editUser.user_type"
                    class="w-full border px-3 py-2 rounded" required>
                </div>

                <div class="mb-3">
                  <label class="block mb-1">ថ្ងៃ/ខែ/ឆ្នាំ</label>
                  <input type="date" name="date" x-model="editUser.date" class="w-full border px-3 py-2 rounded"
                    required>
                </div>

                <div class="mb-3">
                  <label class="block mb-1">លេខទូរសព្ទ</label>
                  <input type="phone" name="phone" x-model="editUser.phone" class="w-full border px-3 py-2 rounded"
                    required>
                </div>

                <div class="mb-3">
                  <label class="block mb-1">អាសយដ្ឋាន</label>
                  <input type="text" name="address" x-model="editUser.address" class="w-full border px-3 py-2 rounded"
                    required>
                </div>
              </div>

              <!-- ប៊ូតុងរក្សាទុក និងបិទ -->
              <div class="flex justify-end gap-2 mt-4">
                <button type="button" @click="showEditModal = false"
                  class="bg-red-600 px-4 py-2 rounded text-white">បិទ</button>
                <button type="submit" class="bg-teal-600 px-4 py-2 rounded text-white">រក្សាទុក</button>
              </div>
            </form>
          </div>
        </div>

        <!-- Detail Modal -->
        <div x-show="showDetailModal" style="display: none;"
          class="fixed inset-0 bg-opacity-50 flex items-center justify-center z-50"
          @click.away="showDetailModal = false">
          <div class="bg-white p-6 rounded-lg w-full max-w-xl shadow-md">
            <h2 class="text-xl font-semibold mb-4 text-teal-600">ព័ត៌មានលម្អិតអ្នកប្រើ</h2>
            <div class="grid grid-cols-2 gap-4 text-gray-500">
              <p><strong>អត្តលេខ:</strong> <span x-text="editUser.student_id"></span></p>
              <p><strong>ឈ្មោះ:</strong> <span x-text="editUser.username"></span></p>
              <p><strong>អុីមែល:</strong> <span x-text="editUser.gmail"></span></p>
              <p><strong>ជំនាញ:</strong> <span x-text="editUser.major"></span></p>
              <p><strong>ភេទ:</strong> <span x-text="editUser.gender"></span></p>
              <p><strong>កម្រិតវប្បធម៌:</strong> <span x-text="editUser.Education_level"></span></p>
              <p><strong>ឆ្នាំទី:</strong> <span x-text="editUser.school_year"></span></p>
              <p><strong>ប្រភេទ:</strong> <span x-text="editUser.user_type"></span></p>
              <p><strong>ថ្ងៃខែឆ្នាំកំណើត:</strong> <span x-text="editUser.date"></span></p>
              <p><strong>លេខទូរសព្ទ:</strong> <span x-text="editUser.phone"></span></p>
              <p><strong>អាសយដ្ឋាន:</strong> <span x-text="editUser.address"></span></p>
            </div>
            <div class="flex justify-end mt-4">
              <button @click="showDetailModal = false" class="bg-teal-600 text-white px-4 py-2 rounded">បិទ</button>
            </div>
          </div>
        </div>

        <!-- delete -->
        <div x-show="deleteModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-opacity-50"
          style="display: none;" @click.away="deleteModal = false">

          <div class="bg-white w-full max-w-sm p-6 rounded shadow relative">
            <h3 class="text-lg font-semibold text-red-600 text-center mb-4">
              តើអ្នកពិតជាចង់លុបអ្នកប្រើនេះមែនទេ?
            </h3>
            <form method="POST" action="delete_user.php">
              <input type="hidden" name="id" :value="deleteId">

              <div class="flex justify-end gap-2 mt-4">
                <button type="button" @click="deleteModal = false" class="bg-gray-300 px-4 py-2 rounded">ទេ</button>

                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded">
                  បាទ/ចាស
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>
  </main>
  <script>
    document.querySelectorAll('#menu .dropdown-toggle').forEach(toggle => {
      toggle.addEventListener('click', e => {
        e.preventDefault();
        const parentLi = toggle.closest('li.dropdown');

        // Close other open dropdowns
        document.querySelectorAll('#menu li.dropdown.active').forEach(item => {
          if (item !== parentLi) item.classList.remove('active');
        });

        // Toggle current dropdown
        parentLi.classList.toggle('active');
      });
    });

    // Close dropdowns when clicking outside menu
    document.addEventListener('click', e => {
      if (!e.target.closest('#menu')) {
        document.querySelectorAll('#menu li.dropdown.active').forEach(item => {
          item.classList.remove('active');
        });
      }
    });

  </script>

  <script>
    $('#menu-btn').click(function () {
      $('#menu').toggleClass("active");
    })
  </script>

  <script>
    $('#menu-btn').click(function () {
      $('#menu').toggleClass("active");
    })
  </script>
</body>

</html>
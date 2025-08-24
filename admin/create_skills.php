<?php
session_start();
include '../db/conn.php';

// Redirect to login if not admin
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


// ===== Add Skill =====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['major_name'])) {
    $major_name = trim($_POST['major_name']);
    if ($major_name !== '') {
        $stmt = $conn->prepare("INSERT INTO majors (major_name) VALUES (?)");
        $stmt->bind_param('s', $major_name);
        if ($stmt->execute()) {
            header("Location: create_skills.php?status=added");
            exit();
        } else {
            header("Location: create_skills.php?status=error");
            exit();
        }
    } else {
        header("Location: create_skills.php?status=empty");
        exit();
    }
}

// ===== Update Skill =====
if (isset($_POST['update_major'])) {
    $id = $_POST['major_id'];
    $new_name = trim($_POST['new_name']);
    $update_stmt = $conn->prepare("UPDATE majors SET major_name=? WHERE id=?");
    $update_stmt->bind_param('si', $new_name, $id);
    if ($update_stmt->execute()) {
        header("Location: create_skills.php?status=updated");
        exit();
    } else {
        header("Location: create_skills.php?status=error");
        exit();
    }
}
$majors = $conn->query("SELECT * FROM majors ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="../dist/style.css" rel="stylesheet">
    <script src="https://unpkg.com/alpinejs" defer></script>
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

<body class="bg-gray-100 text-gray-800 flex" x-data="{
        sidebarOpen: true,
        editModal: false,
        editEvent: {},
        deleteModal: false,
        deleteId: null
      }">
    <aside :class="sidebarOpen ? 'w-64' : 'w-0', sidebarOpen ? 'p-4' : 'p-0'"
        class="bg-[#111811] text-white text-lg overflow-hidden h-screen fixed left-0 top-0 transition-all duration-300">
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
                <button @click="open=!open"
                    class="w-full flex justify-between items-center px-3 py-4 hover:bg-gray-700">
                    <span class="flex items-center gap-3"><i class="fa-solid fa-user"></i> ទិន្នន័យអ្នកប្រើប្រាស់</span>
                    <i :class="open?'fa-chevron-up':'fa-chevron-down'" class="fa-solid"></i>
                </button>
                <ul x-show="open" x-transition class="pl-6 px-3 mt-1 space-y-3 text-base dropdown-menu">
                    <li><a href="../admin/add_user.php" class="flex items-center gap-3 py-4 w-full hover:bg-gray-700"><i
                                class="fa-regular fa-circle"></i> ចុះឈ្មោះថ្មី</a></li>
                    <li><a href="../admin/manage_users.php"
                            class="flex items-center gap-3 py-4 w-full hover:bg-gray-700"><i
                                class="fa-regular fa-circle"></i> គ្រប់គ្រងអ្នកប្រើប្រាស់</a></li>
                </ul>
            </li>

            <!-- Event Dropdown -->
            <li x-data="{ open: false }">
                <button @click="open=!open"
                    class="w-full flex justify-between items-center px-3 py-4 hover:bg-gray-700">
                    <span class="flex items-center gap-3"><i class="fa-solid fa-calendar-plus"></i> ព្រឹត្តិការណ៍</span>
                    <i :class="open?'fa-chevron-up':'fa-chevron-down'" class="fa-solid"></i>
                </button>
                <ul x-show="open" x-transition class="pl-6 px-3 mt-1 space-y-3 text-base dropdown-menu">
                    <li><a href="../admin/create_event.php"
                            class="flex items-center gap-3 py-4 w-full hover:bg-gray-700"><i
                                class="fa-regular fa-circle"></i> បង្កើតព្រឹត្តិការណ៍ថ្មី</a></li>
                    <li><a href="../admin/events_list.php"
                            class="flex items-center gap-3 py-4 w-full hover:bg-gray-700"><i
                                class="fa-regular fa-circle"></i> បញ្ជីព្រឹត្តិការណ៍</a></li>
                </ul>
            </li>

            <!-- Skill Dropdown -->
            <li x-data="{ open: false }">
                <button @click="open=!open"
                    class="w-full flex justify-between items-center px-3 py-4 hover:bg-gray-700">
                    <span class="flex items-center gap-3"><i class="fa-solid fa-plus"></i> ជំនាញ</span>
                    <i :class="open?'fa-chevron-up':'fa-chevron-down'" class="fa-solid"></i>
                </button>
                <ul x-show="open" x-transition class="pl-6 px-3 mt-1 space-y-4 text-base dropdown-menu">
                    <li><a href="../admin/create_skills.php"
                            class="flex items-center gap-3 py-4 w-full hover:bg-gray-700"><i
                                class="fa-regular fa-circle"></i> បង្កើតជំនាញថ្មី</a></li>

                </ul>
            </li>
            <!-- attendance  -->
            <li x-data="{ open: false }">
                <button @click="open=!open"
                    class="w-full flex justify-between items-center px-3 py-4 hover:bg-gray-700">
                    <span class="flex items-center gap-3"><i
                            class="fa-solid fa-clipboard-user"></i>គ្រប់គ្រងវត្តមាន</span>
                    <i :class="open?'fa-chevron-up':'fa-chevron-down'" class="fa-solid"></i>
                </button>
                <ul x-show="open" x-transition class="pl-6 px-3 mt-1 space-y-4 text-base dropdown-menu">
                    <li><a href="../admin/attendance.php"
                            class="flex items-center gap-3 py-4 w-full hover:bg-gray-700"><i
                                class="fa-regular fa-circle"></i>បញ្ជីវត្តមានអ្នកចូលរួម</a></li>
                </ul>
            </li>

            <!-- Announcements Dropdown -->
            <li x-data="{ open: false }">
                <button @click="open=!open"
                    class="w-full flex justify-between items-center px-3 py-4 hover:bg-gray-700">
                    <span class="flex items-center gap-3"><i class="fa-solid fa-bullhorn"></i> សេចក្ដីជូនដំណឹង</span>
                    <i :class="open?'fa-chevron-up':'fa-chevron-down'" class="fa-solid"></i>
                </button>
                <ul x-show="open" x-transition class="pl-6 px-3 mt-1 space-y-3 text-base dropdown-menu">
                    <li><a href="../admin/create_announcements.php"
                            class="flex items-center gap-3 py-4 w-full hover:bg-gray-700"><i
                                class="fa-regular fa-circle"></i> បង្កើតសេចក្ដីជូនដំណឹង</a></li>
                    <li><a href="../admin/announcements_list.php"
                            class="flex items-center gap-3 py-4 w-full hover:bg-gray-700"><i
                                class="fa-regular fa-circle"></i> បញ្ជីសេចក្ដីជូនដំណឹង</a></li>
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
                    <img src="<?= $profile_img ?>" class="w-12 h-12 rounded-full border-2 border-green-500 object-cover"
                        alt="Admin">
                </div>

                <!-- Dropdown menu -->
                <div x-show="open" @click.outside="open = false" x-transition
                    class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-2 z-20">
                    <a href="../admin/profile_admin.php"
                        class="block px-4 py-2 text-base text-gray-700 hover:bg-gray-100">
                        <i class="fa-solid fa-id-badge mr-2"></i> ព័ត៌មានគណនី
                    </a>
                    <a href="../admin/logout.php" class="block px-4 py-2 text-base text-red-600 hover:bg-red-100">
                        <i class="fa-solid fa-sign-out-alt mr-2"></i> ចាកចេញ
                    </a>
                </div>
            </div>
        </div>

        <section class="px-4 py-6">
            <div class="max-w-2xl mx-auto bg-white p-6 rounded shadow mb-6">
                <h2 class="text-xl font-bold text-green-600 mb-4">បង្កើតជំនាញថ្មី</h2>
                <form method="POST" class="space-y-4">
                    <input type="text" name="major_name" placeholder="បញ្ចូលឈ្មោះជំនាញថ្មី"
                        class="w-full px-4 py-2 border rounded" required>
                    <button type="submit" class="bg-sky-500 text-white px-6 py-2 rounded hover:bg-sky-600">
                        បង្កើត
                    </button>
                </form>

                <?php if (isset($_GET['status'])): ?>
                    <?php if ($_GET['status'] === 'added'): ?>
                        <div class="mt-4 p-3 bg-green-100 border border-green-400 text-green-800 rounded">
                            បានបន្ថែមជំនាញថ្មីរួចរាល់!
                        </div>
                    <?php elseif ($_GET['status'] === 'updated'): ?>
                        <div class="mt-4 p-3 bg-blue-100 border border-blue-400 text-blue-800 rounded">
                            បានកែប្រែជំនាញរួចរាល់!
                        </div>
                    <?php elseif ($_GET['status'] === 'error'): ?>
                        <div class="mt-4 p-3 bg-red-100 border border-red-400 text-red-800 rounded">
                            មានបញ្ហាក្នុងការបញ្ចូល ឬ កែប្រែ!
                        </div>
                    <?php elseif ($_GET['status'] === 'empty'): ?>
                        <div class="mt-4 p-3 bg-yellow-100 border border-yellow-400 text-yellow-800 rounded">
                            សូមបញ្ចូលឈ្មោះជំនាញ។
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <div class="max-w-4xl mx-auto bg-white p-6 rounded shadow overflow-x-auto">
                <h2 class="text-xl font-bold mb-4 text-green-600">បញ្ជីជំនាញសិក្សា</h2>
                <table class="min-w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-green-700 text-white text-center">
                            <th class="py-2 px-4">#</th>
                            <th class="py-2 px-4">ឈ្មោះជំនាញ</th>
                            <th class="py-2 px-4">សកម្មភាព</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1;
                        foreach ($majors as $m): ?>
                            <tr class="border-b text-center hover:bg-gray-50">
                                <td class="py-2 px-4"><?= $i++ ?></td>
                                <td class="py-2 px-4"><?= htmlspecialchars($m['major_name']) ?></td>
                                <td class="py-2 px-4 space-x-2">
                                    <a href="#" class="text-blue-600 hover:underline edit-btn" data-id="<?= $m['id'] ?>"
                                        data-name="<?= htmlspecialchars($m['major_name']) ?>">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <a href="#" class="text-red-600 hover:underline"
                                        @click.prevent="deleteId = <?= $m['id'] ?>; deleteModal = true">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if ($majors->num_rows === 0): ?>
                            <tr>
                                <td colspan="3" class="text-center py-4 text-gray-500">មិនមានជំនាញសិក្សានៅឡើយទេ។</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Modal -->
            <div id="editModal" class="fixed inset-0 z-50 hidden bg-opacity-30 flex items-center justify-center">
                <div class="bg-white w-full max-w-md p-6 rounded shadow relative">
                    <h2 class="text-xl font-bold mb-4 text-teal-600">កែប្រែជំនាញ</h2>
                    <form method="POST">
                        <input type="hidden" name="major_id" id="modal-major-id">
                        <div class="mb-4">
                            <label class="block mb-1">ឈ្មោះជំនាញថ្មី</label>
                            <input type="text" name="new_name" id="modal-major-name"
                                class="w-full border px-3 py-2 rounded focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div class="flex justify-end space-x-2">
                            <button type="button" id="closeModal"
                                class="bg-gray-400 px-4 py-2 rounded text-white">បិទ</button>
                            <button type="submit" name="update_major"
                                class="bg-sky-500 px-4 py-2 rounded text-white">រក្សាទុក</button>
                        </div>
                    </form>
                </div>
            </div>
            <!-- delete -->
            <div x-show="deleteModal" x-transition
          class="fixed inset-0 z-50 flex items-center justify-center bg-opacity-50" style="display: none;"
          @click.away="deleteModal = false">
              
                <div class="bg-white p-6 rounded shadow-lg max-w-sm w-full text-center">
                    <p class="mb-4 text-lg font-semibold">តើអ្នកប្រាកដជាចង់លុបមែនទេ?</p>

                    <div class="flex justify-center gap-4">
                        <button @click="deleteModal = false" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                            បោះបង់
                        </button>

                        <a :href="`delete_major.php?id=${deleteId}`"
                            class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                            លុប
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <script>
            const editModal = document.getElementById('editModal');
            const closeModal = document.getElementById('closeModal');

            document.querySelectorAll('.edit-btn').forEach(btn => {
                btn.addEventListener('click', e => {
                    e.preventDefault();
                    const id = btn.dataset.id;
                    const name = btn.dataset.name;
                    document.getElementById('modal-major-id').value = id;
                    document.getElementById('modal-major-name').value = name;
                    editModal.classList.remove('hidden');
                });
            });

            closeModal.addEventListener('click', () => editModal.classList.add('hidden'));
            window.addEventListener('click', e => {
                if (e.target === editModal) editModal.classList.add('hidden');
            });

            // Auto-hide alerts
            setTimeout(() => {
                document.querySelectorAll('.mt-4.p-3').forEach(el => {
                    el.style.display = 'none';
                });
            }, 3000);
        </script>

</body>

</html>
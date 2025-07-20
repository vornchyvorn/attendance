<?php
session_start();

include '../db/conn.php';

if (!isset($_SESSION['admin_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login_admin.php');
    exit();
}

$admin_id = $_SESSION['admin_id'];


$sql = "SELECT * FROM admin WHERE admin_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
$stmt->close();

// ✅ រូបភាព
$default_img = '../pic/user.png';
$profile_img = (!empty($admin['image']) && file_exists("../uploads/" . $admin['image']))
    ? "../uploads/" . htmlspecialchars($admin['image'])
    : $default_img;
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
                <button @click="open=!open"
                    class="w-full flex justify-between items-center px-3 py-4 hover:bg-gray-700">
                    <span class="flex items-center gap-3"><i class="fa-solid fa-user"></i> សិស្សចុះឈ្មោះ</span>
                    <i :class="open?'fa-chevron-up':'fa-chevron-down'" class="fa-solid"></i>
                </button>
                <ul x-show="open" x-transition class="pl-6 px-3 mt-1 space-y-3 text-sm dropdown-menu">
                    <li><a href="../admin/add_user.php" class="flex items-center gap-3 py-4 w-full hover:bg-gray-700"><i
                                class="fa-regular fa-circle"></i> ចុះឈ្មោះថ្មី</a></li>
                    <li><a href="../admin/manage_users.php"
                            class="flex items-center gap-3 py-4 w-full hover:bg-gray-700"><i
                                class="fa-regular fa-circle"></i> គ្រប់គ្រងសិស្ស</a></li>
                </ul>
            </li>

            <!-- Event Dropdown -->
            <li x-data="{ open: false }">
                <button @click="open=!open"
                    class="w-full flex justify-between items-center px-3 py-4 hover:bg-gray-700">
                    <span class="flex items-center gap-3"><i class="fa-solid fa-calendar-plus"></i> ព្រឹត្តិការណ៍</span>
                    <i :class="open?'fa-chevron-up':'fa-chevron-down'" class="fa-solid"></i>
                </button>
                <ul x-show="open" x-transition class="pl-6 px-3 mt-1 space-y-3 text-sm dropdown-menu">
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
                    <span class="flex items-center gap-3"><i class="fa-solid fa-graduation-cap"></i> ជំនាញ</span>
                    <i :class="open?'fa-chevron-up':'fa-chevron-down'" class="fa-solid"></i>
                </button>
                <ul x-show="open" x-transition class="pl-6 px-3 mt-1 space-y-4 text-sm dropdown-menu">
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
                <ul x-show="open" x-transition class="pl-6 px-3 mt-1 space-y-4 text-sm dropdown-menu">
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
                <ul x-show="open" x-transition class="pl-6 px-3 mt-1 space-y-3 text-sm dropdown-menu">
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
                    <a href="../admin/profile_admin.php"
                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <i class="fa-solid fa-id-badge mr-2"></i> ព័ត៌មានគណនី
                    </a>
                    <a href="../admin/logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-100">
                        <i class="fa-solid fa-sign-out-alt mr-2"></i> ចាកចេញ
                    </a>
                </div>
            </div>
        </div>

        <div class="max-w-4xl mx-auto mt-10 bg-white shadow-lg rounded-xl p-8">
            <h2 class="text-xl font-bold text-center text-green-700 mb-8">
                ព័ត៌មានគណនីរបស់អ្នកគ្រប់គ្រង
            </h2>
            <!-- Flex Layout: Image + Info -->
            <div class="flex flex-col md:flex-row items-start gap-8">
                <!-- Profile Image -->
                <div class="flex-shrink-0 text-center w-full md:w-1/3">
                    <img src="<?= $profile_img ?>"
                        class="w-40 h-40 rounded-full object-cover mx-auto border-4 border-gray-300" alt="Admin Image">

                    <!-- Upload Form -->
                    <form action="upload_profile_img.php" method="POST" enctype="multipart/form-data" class="mt-6">

                        <input type="file" name="image" accept="image/*" required
                            class="block w-full border rounded px-3 py-2 mb-4">
                        <button type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded w-full">
                            Upload
                        </button>
                    </form>
                </div>
               
                <!-- Profile Info -->
                <div class="flex-grow grid text-center items-center justify-center gap-6">
                    <div class="flex">
                        <label class="block text-gray-600">ឈ្មោះអ្នកប្រើប្រាស់: </label>
                        <p class="font-semibold text-gray-800"><?= htmlspecialchars($admin['username'] ?? '') ?></p>

                    </div>
                    
                    <div class="flex">
                        <label class="block text-gray-600">តួនាទី: </label>
                        <p class="font-semibold text-gray-800"><?= htmlspecialchars($admin['role'] ?? '') ?>
                    </div>
                    <div class="flex">
                        <label class="block text-gray-600">លេខទូរសព្ទ: </label>
                        <p class="font-semibold text-gray-800">	<?= htmlspecialchars($admin['phone'] ?? '') ?>
                    </div>
                    <div class="flex">
                        <label class="block text-gray-600">អ៊ីម៉ែល: </label>
                        <p class="font-semibold text-gray-800"><?= htmlspecialchars($admin['email'] ?? '') ?>
                    </div>
                    
                </div>
            </div>
        </div>
    </main>
</body>

</html>

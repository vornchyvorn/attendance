<?php
session_start();
include '../db/conn.php';
$profile_img = '../pic/user.png';
$user_id = $_SESSION['user_id'] ?? 0;

// Only admin access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login_admin.php");
    exit();
}

// Fetch profile image
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

// Filters (adapt variable names for form inputs)
$major = $_GET['major_name'] ?? '';
$time_period = $_GET['time_period'] ?? '';
$event_start = $_GET['event_start'] ?? '';
$event_end = $_GET['event_end'] ?? '';

// Pagination variables
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit_options = [10, 25, 50, 100];
$limit = isset($_GET['limit']) && in_array((int) $_GET['limit'], $limit_options) ? (int) $_GET['limit'] : 10;
$offset = ($page - 1) * $limit;

// WHERE conditions
$where = "1";
$params = [];
$types = "";

if (!empty($major)) {
    $where .= " AND u.major = ?";
    $params[] = $major;
    $types .= 's';
}

if (!empty($time_period)) {
    $where .= " AND a.time_period = ?";
    $params[] = $time_period;
    $types .= 's';
}

if (!empty($event_start)) {
    $where .= " AND DATE(a.check_in) >= ?";
    $params[] = $event_start;
    $types .= 's';
}

if (!empty($event_end)) {
    $where .= " AND DATE(a.check_in) <= ?";
    $params[] = $event_end;
    $types .= 's';
}
$event_id = $_GET['event_id'] ?? '';
if (!empty($event_id)) {
    $where .= " AND a.event_id = ?";
    $params[] = $event_id;
    $types .= 'i';
}
// Total rows (for pagination)
$count_sql = "SELECT COUNT(*) AS total FROM attendance a LEFT JOIN users u ON a.student_id = u.student_id WHERE $where";
$count_stmt = $conn->prepare($count_sql);
if (!empty($params)) {
    $count_stmt->bind_param($types, ...$params);
}
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_rows = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit);
$count_stmt->close();

// Fetch paginated data
$sql = "SELECT a.*, u.username, u.major FROM attendance a 
        LEFT JOIN users u ON a.student_id = u.student_id 
        WHERE $where ORDER BY a.check_in DESC LIMIT ? OFFSET ?";
$params_with_limit = $params;
$types_with_limit = $types . 'ii';
$params_with_limit[] = $limit;
$params_with_limit[] = $offset;

$stmt = $conn->prepare($sql);
$stmt->bind_param($types_with_limit, ...$params_with_limit);
$stmt->execute();
$result = $stmt->get_result();

// Fetch majors for filter dropdown
$majors_result = $conn->query("SELECT DISTINCT major FROM users ORDER BY major ASC");
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
                    <span class="flex items-center gap-3"><i class="fa-solid fa-plus"></i> ជំនាញ</span>
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
    <main class="flex-1 ml-0 transition-all duration-300" :class="sidebarOpen ? 'ml-64' : 'ml-0'">
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

        <div class="container mx-auto px-4 py-6">
            <h1 class="text-2xl font-bold text-teal-600 mb-4"> ការត្រួតពិនិត្យការចូលចេញ</h1>

            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6 bg-white shadow p-4 rounded-lg">
                <div>
                    <label class="block text-sm text-gray-600 mb-1">ជំនាញ</label>
                    <select name="major_name" class="w-full border rounded px-3 py-2">
                        <option value="">-- ជ្រើសរើស --</option>
                        <?php
                        $query = "SELECT * FROM majors";
                        $query_run = mysqli_query($conn, $query);
                        if (mysqli_num_rows($query_run) > 0) {
                            while ($data = mysqli_fetch_assoc($query_run)) {
                                echo "<option value='{$data['major_name']}'>{$data['major_name']}</option>";
                            }
                        }
                        ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm text-gray-600 mb-1">ព្រឹត្តិការណ៍</label>
                    <select name="event_id" class="w-full border rounded px-3 py-2">
                        <option value="">-- ជ្រើសរើស --</option>
                        <?php
                        $query = "SELECT * FROM events ORDER BY event_date DESC";
                        $query_run = mysqli_query($conn, $query);
                        if (mysqli_num_rows($query_run) > 0) {
                            while ($data = mysqli_fetch_assoc($query_run)) {
                                $id = $data['id'];
                                $title = htmlspecialchars($data['title']);
                                $date = htmlspecialchars($data['event_date']);
                                $location = htmlspecialchars($data['location']);
                                echo "<option value='$id'>$title ($date - $location)</option>";
                            }
                        }
                        ?>
                    </select>
                </div>

                <?php
                $event_start = $_GET['event_start'] ?? '';
                $event_end = $_GET['event_end'] ?? '';
                ?>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">ចាប់ពីថ្ងៃទី</label>
                    <input type="date" name="event_start" value="<?= htmlspecialchars($event_start) ?>"
                        class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">ដល់ថ្ងៃទី</label>
                    <input type="date" name="event_end" value="<?= htmlspecialchars($event_end) ?>"
                        class="w-full border rounded px-3 py-2">
                </div>

                <div>
                    <label class="block text-sm text-gray-600 mb-1">វេលា</label>
                    <select name="time_period" class="w-full border rounded px-3 py-2">
                        <option value="">-- ជ្រើសរើស --</option>
                        <option value="morning" <?= $time_period === 'morning' ? 'selected' : '' ?>>ព្រឹក</option>
                        <option value="evening" <?= $time_period === 'evening' ? 'selected' : '' ?>>ល្ងាច</option>
                    </select>
                </div>
        </div>

        <div class="flex items-end gap-2 p-4">
            <button type="submit" class="bg-teal-600 text-white px-4 py-2 rounded hover:bg-teal-700"><i
                    class="fa-solid fa-filter mr-1"></i>Filter</button>
            <a href="attendance.php" class="bg-red-600 text-white px-4 py-2 rounded">Clear</a>
            <a href="export_excel.php?<?= http_build_query($_GET) ?>"
                class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"><i
                    class="fa-solid fa-file-excel mr-1"></i>Excel</a>
            <a href="export_pdf.php?major=<?= urlencode($_GET['major'] ?? '') ?>&date=<?= urlencode($_GET['date'] ?? '') ?>&time_period=<?= urlencode($_GET['time_period'] ?? '') ?>&event_id=<?= urlencode($_GET['event_id'] ?? '') ?>"
                class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600" target="_blank">
                <i
                    class="fa-solid fa-file-pdf mr-1"></i> PDF
            </a>
        </div>
        </form>

        <div class="bg-white shadow rounded-lg overflow-x-auto p-4">
            <table class="min-w-full text-sm text-left border">
                <thead class="bg-teal-600 text-white">
                    <tr>
                        <th class="px-4 py-2 border">#</th>
                        <th class="px-4 py-2 border">ឈ្មោះ</th>
                        <th class="px-4 py-2 border">អត្តលេខ</th>
                        <th class="px-4 py-2 border">ជំនាញ</th>
                        <th class="px-4 py-2 border">វេលា</th>
                        <th class="px-4 py-2 border">ម៉ោងចូល</th>
                        <th class="px-4 py-2 border">ម៉ោងចេញ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0):
                        $i = 1;
                        while ($row = $result->fetch_assoc()): ?>
                            <tr class="border-t hover:bg-gray-50">
                                <td class="px-4 py-2 text-center font-medium"><?= $i++ ?></td>
                                <td class="px-4 py-2"><?= htmlspecialchars($row['username']) ?></td>
                                <td class="px-4 py-2"><?= htmlspecialchars($row['student_id']) ?></td>
                                <td class="px-4 py-2"><?= htmlspecialchars($row['major']) ?></td>
                                <td class="px-4 py-2  capitalize"><?= $row['time_period'] ?></td>
                                <td class="px-4 py-2  text-green-700 font-semibold"><?= $row['check_in'] ?></td>
                                <td class="px-4 py-2 ">
                                    <?= $row['check_out'] ? '<span class="text-blue-700 font-semibold">' . $row['check_out'] . '</span>' : '<span class="text-red-500">មិនទាន់ចេញ</span>' ?>
                                </td>
                            </tr>
                        <?php endwhile; else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-gray-500 py-4">មិនមានទិន្នន័យទេ។</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="mt-4 flex items-center justify-between">
            <form method="GET" id="limitForm" class="inline-block">
                <?php
                // Preserve all GET parameters except limit and page
                foreach ($_GET as $key => $value) {
                    if ($key !== 'limit' && $key !== 'page') {
                        echo '<input type="hidden" name="' . htmlspecialchars($key) . '" value="' . htmlspecialchars($value) . '">';
                    }
                }
                ?>
                <label for="limit" class="mr-2 font-medium"></label>
                <select name="limit" id="limit" class="border rounded px-3 py-1"
                    onchange="document.getElementById('limitForm').submit()">
                    <?php foreach ($limit_options as $opt): ?>
                        <option value="<?= $opt ?>" <?= $limit == $opt ? 'selected' : '' ?>><?= $opt ?></option>
                    <?php endforeach; ?>
                </select>
            </form>

            <div>
            </div>
    </main>
    <script>

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
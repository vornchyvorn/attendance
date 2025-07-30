<?php
include '../db/conn.php';

$total_result = $conn->query("SELECT COUNT(*) AS total FROM users");
$total = $total_result->fetch_assoc()['total'];


$female_result = $conn->query("SELECT COUNT(*) AS female FROM users WHERE gender = 'ស្រី'");
$female = $female_result->fetch_assoc()['female'];


$teacher_result = $conn->query("SELECT COUNT(*) AS teacher FROM users WHERE user_type = 'គ្រូ'");
$teacher = $teacher_result->fetch_assoc()['teacher'];


$student_result = $conn->query("SELECT COUNT(*) AS student FROM users WHERE user_type = 'សិស្ស'");
$student = $student_result->fetch_assoc()['student'];


$sql = "SELECT * FROM users";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="km" class="h-full">
<head>
    <meta charset="UTF-8" />
    <title>បញ្ជីអ្នកចូលរួម</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="../dist/style.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css" />
    <style>
        body {
            font-family: "Khmer OS Siemreap", sans-serif;
            background-color: #f0f8ff;
        }
        table.dataTable thead th {
            background-color: #0ea5e9; /* blue-500 */
            color: white;
            text-align: center;
        }
        /* បង្រួមទំហំ search label និង input */
            .dataTables_wrapper .dataTables_filter {
                font-size: 0.875rem; /* 14px */
                margin-bottom: 0.5rem; /* បន្ថែម spacing តិច */
            }

            .dataTables_wrapper .dataTables_filter label {
                margin-bottom: 0;
            }

            .dataTables_wrapper .dataTables_filter input {
                border-radius: 0.5rem;
                border: 1px solid #cbd5e1;
                padding: 0.35rem 0.5rem; /* កាត់បន្ថយ padding */
                width: 220px;
                font-size: 0.875rem; /* 14px */
                margin-left: 0.25rem;
            }

        td, th {
            text-align: center;
            vertical-align: middle;
        }
        tbody tr:hover {
            background-color: #bfdbfe; /* blue-200 */
        }
    </style>
</head>
<body class="bg-blue-50 min-h-screen flex flex-col">

<!-- Navbar -->
<header class="bg-sky-600 sticky top-0 z-50 shadow">
  <nav class="flex items-center justify-between px-4 py-3 md:py-4 md:px-8 max-w-screen-xl mx-auto">
    <!-- Logo -->
    <div class="flex items-center space-x-2">
      <img src="../pic/logo.jpg" alt="Logo" class="w-[58px] h-[58px] rounded-full"/>
    </div>

    <!-- Desktop Menu -->
    <ul class="hidden md:flex items-center space-x-16 text-white text-lg font-medium">
      <li><a href="dashboard.php" class="hover:text-gray-300">ទំព័រដើម</a></li>
      <li><a href="announcements.php" class="hover:text-gray-300">ប្រកាស</a></li>
      <li><a href="participants.php" class="hover:text-gray-300">អ្នកចូលរួម</a></li>
      <li><a href="about.php" class="hover:text-gray-300">អំពីយើង</a></li>
    </ul>

    <!-- Right Controls -->
    <div class="flex items-center space-x-3 md:space-x-4">
      <!-- Profile Button -->
      <div class="relative">
        <button onclick="toggleDropdown()" class="bg-blue-400 text-white px-6 py-1 text-sm h-[40px] rounded-full hover:bg-blue-500">
          My Profile
        </button>
        <ul id="dropdownMenu" class="absolute right-0 mt-2 bg-white text-black shadow-lg rounded hidden z-10 w-40 text-sm">
          <li><a href="user_profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"><i class="fa-solid fa-id-badge mr-2"></i> ព័ត៌មានគណនី</a></li>
            <li>
              <hr class="border-gray-200">
            </li>
            <li><a href="login.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-100""><i class="fa-solid fa-sign-out-alt mr-2"></i> ចាកចេញ</a></li>
        </ul>
      </div>

      <!-- Mobile Toggle Button -->
      <button onclick="toggleMobileMenu()" class="md:hidden text-white text-2xl">
        <i class="fa-solid fa-bars" id="menuIcon"></i>
      </button>
    </div>
  </nav>

  <!-- Mobile Menu -->
  <div id="mobileMenu" class="md:hidden hidden bg-sky-600 px-6 pb-3 text-white text-base font-medium">
    <a href="dashboard.php" class="block py-2 border-b border-white/20">ទំព័រដើម</a>
    <a href="announcements.php" class="block py-2 border-b border-white/20">ប្រកាស</a>
    <a href="participants.php" class="block py-2 border-b border-white/20">អ្នកចូលរួម</a>
    <a href="about.php" class="block py-2">អំពីយើង</a>
  </div>
</header>

<!-- Title -->
<main class="flex-grow">
  <div class="container mx-auto mt-6 mb-4 text-sm:text-left px-10">
      <h1 class="inline-block bg-sky-600 text-white text-md px-10 py-3 rounded-tr-[10px] rounded-bl-[10px]">
          បញ្ជីអ្នកចូលរួម
      </h1>
  </div>

  <!-- Table Section -->
  <div class="container mx-auto px-4 sm:px-6 lg:px-12">
    <div class="bg-white shadow-md rounded-lg p-4">
      <div class="overflow-x-auto">
        <table id="exampleid" class="stripe hover cell-border display w-full text-sm text-center min-w-[600px]">
          <thead class="bg-sky-700 text-white">
          <tr>
            <th class="px-4 py-2">ល.រ</th>
            <th class="px-4 py-2">ថ្ងៃ/ខែ/ឆ្នាំ</th>
            <th class="px-4 py-2">ឈ្មោះ</th>
            <th class="px-4 py-2">ភេទ</th>
            <th class="px-4 py-2">តួនាទី</th>
          </tr>
        </thead>
        <tbody>
          <?php $i = 1; if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr class="hover:bg-gray-50">
                <td class="px-4 py-2"><?= $i++ ?></td>
                <td class="px-4 py-2"><?= date("d/m/Y", strtotime($row['date'])) ?></td>
                <td class="px-4 py-2"><?= htmlspecialchars($row['username']) ?></td>
                <td class="px-4 py-2"><?= htmlspecialchars($row['gender']) ?></td>
                <td class="px-4 py-2"><?= htmlspecialchars($row['user_type']) ?></td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="5" class="text-gray-500 py-4">មិនមានទិន្នន័យ</td>
            </tr>
          <?php endif; ?>
        </tbody>
        </table>
      </div>
      <div id="summary" class="mt-4 text-center text-gray-700 text-sm"></div>
    </div>
  </div>
</main>


<!-- Footer -->
<div class="bg-sky-600 text-white text-center py-8">
    Power by Department of Computer Science @2025
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

<script>
    var total = <?= $total ?>;
    var female = <?= $female ?>;
    var teacher = <?= $teacher ?>;
    var student = <?= $student ?>;
</script>

<script>
    $(document).ready(function () {
    var table = $('#exampleid').DataTable({
        responsive: true,
        language: {
            search: "<span class='text-sm'>ស្វែងរក:</span>",
            searchPlaceholder: "ស្វែងរកអ្នកចូលរួម...",
            lengthMenu: " _MENU_",
            info: "បង្ហាញ _START_ ដល់ _END_ នៃ _TOTAL_",
            paginate: {
                first: "ដំបូង",
                last: "ចុងក្រោយ",
                next: "បន្ទាប់",
                previous: "ថយក្រោយ"
            }
        },
        dom: '<"top flex items-center space-x-2 text-sm"f>rt<"bottom flex justify-between items-center text-xs"lip><"clear">'
    });

    let summaryHtml = `
        សរុប: <strong class="text-blue-600">${total}</strong> |
        ស្រី: <strong class="text-pink-600">${female}</strong> |
        គ្រូ: <strong class="text-green-600">${teacher}</strong> |
        សិស្ស: <strong class="text-orange-600">${student}</strong>
    `;
    $('#summary').html(summaryHtml);
});

</script>
  <script>
  function toggleDropdown() {
    document.getElementById("dropdownMenu").classList.toggle("hidden");
  }

  function toggleMobileMenu() {
    const menu = document.getElementById("mobileMenu");
    const icon = document.getElementById("menuIcon");

    menu.classList.toggle("hidden");
    icon.classList.toggle("fa-bars");
    icon.classList.toggle("fa-xmark");
  }
</script>
</body>
</html>

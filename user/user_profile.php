<?php
session_start();
include '../db/conn.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $imgName = $_FILES['image']['name'];
    $tmpName = $_FILES['image']['tmp_name'];
    $imgExt = strtolower(pathinfo($imgName, PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];

    if (in_array($imgExt, $allowed)) {
        $newName = uniqid('profile_', true) . '.' . $imgExt;
        $uploadPath = '../upload_img/' . $newName;

        if (move_uploaded_file($tmpName, $uploadPath)) {
            // update database
            $stmt = $conn->prepare("UPDATE users SET image = ? WHERE id = ?");
            $stmt->bind_param("si", $newName, $user_id);
            $stmt->execute();
        }
    }
}
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$row = $result->fetch_assoc()) {
    echo "មិនមានអ្នកប្រើ!";
    exit;
}
$profile_img = '../pic/user.png';
if (!empty($row['image']) && file_exists("../upload_img/" . $row['image'])) {
    $profile_img = "../upload_img/" . $row['image'];
}

$username = htmlspecialchars($row['username']);
$student_id = htmlspecialchars($row['student_id'] ?? 'មិនមាន');
$gender = htmlspecialchars($row['gender']);
$Education_level = htmlspecialchars($row['Education_level']);
$school_year = htmlspecialchars($row['school_year']);
$gmail = htmlspecialchars($row['gmail']);
$major = htmlspecialchars($row['major'] ?? 'មិនមាន');
$date = htmlspecialchars($row['date']);
$user_type = htmlspecialchars($row['user_type'] ?? 'មិនមាន');
$phone = htmlspecialchars($row['phone'] ?? 'មិនមាន');
$address = htmlspecialchars($row['address'] ?? 'មិនមាន');
?>
<!DOCTYPE html>
<html lang="km">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>សេចក្ដីប្រកាស</title>
  <link href="../dist/style.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:ital,wght@0,100..700;1,100..700&family=Koulen&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
  <style>
    body{

      font-family: "Kantumruy Pro", sans-serif;
    }
  </style>
</head>
<body class="bg-blue-50 min-h-screen flex flex-col">

<!-- Header -->
<header class="bg-sky-600 sticky top-0 z-50 shadow">
  <nav class="flex items-center justify-between px-4 py-3 md:py-4 md:px-8 max-w-screen-xl mx-auto">
    <!-- Logo -->
    <div class="flex items-center space-x-2">
      <img src="../pic/logo.jpg" alt="Logo" class="w-[58px] h-[58px] rounded"/>
    </div>

    <!-- Desktop Menu -->
    <ul class="hidden md:flex items-center space-x-16 text-white text-xl font-medium">
      <li><a href="dashboard.php" class="hover:text-gray-300">ទំព័រដើម</a></li>
      <li><a href="announcements.php" class="hover:text-gray-300">ប្រកាស</a></li>
      <li><a href="participants.php" class="hover:text-gray-300">អ្នកចូលរួម</a></li>
      <li><a href="about.php" class="hover:text-gray-300">អំពីយើង</a></li>
    </ul>

    <!-- Right Controls -->
    <div class="flex items-center space-x-3 md:space-x-4">
        <!-- Profile Button -->
        <div class="relative">
          <button onclick="toggleDropdown()"
            class="bg-blue-400 text-white px-6 py-1 text-sm h-[40px] rounded-full hover:bg-blue-500 shadow-md">
            My Profile
          </button>
          <ul id="dropdownMenu"
            class="absolute right-0 mt-2 bg-white text-black shadow-lg rounded hidden z-10 w-40 text-sm">
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
<div class="bg-white shadow-lg rounded-xl p-6 w-full max-w-xl mx-auto mt-4">
    <h2 class="text-2xl font-bold text-teal-700 text-center mb-6">ព័ត៌មានគណនី</h2>

    <div class="flex flex-col items-center gap-4">
        <div class="w-32 h-32 relative">
            <img src="<?= $profile_img ?>" alt="Profile" class="rounded-full border-4 border-blue-300 w-full h-full object-cover">
            <form method="POST" enctype="multipart/form-data" class="mt-2 text-center">
                <input type="file" name="image" accept="image/*" class="text-xs" required>
                <button type="submit" class="mt-1 px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 text-xs">ផ្ទុករូបភាពថ្មី</button>
            </form>
        </div>

        <div class="w-full mt-4">
            <div class="grid grid-cols-1 gap-3 text-sm">
                <p><span class="font-semibold text-teal-700">ឈ្មោះ:</span> <?= $username ?></p>
                <p><span class="font-semibold text-teal-700">អត្តលេខ:</span> <?= $student_id ?></p>
                <p><span class="font-semibold text-teal-700">ភេទ:</span> <?= $gender ?></p>
                <p><span class="font-semibold text-teal-700">កម្រិតវប្បធម៍:</span> <?= $Education_level ?></p>
                <p><span class="font-semibold text-teal-700">ឆ្នាំទី:</span> <?= $school_year?></p>
                <p><span class="font-semibold text-teal-700">អ៊ីមែល:</span> <?= $gmail ?></p>
                <p><span class="font-semibold text-teal-700">ជំនាញ:</span> <?= $major ?></p>
                <p><span class="font-semibold text-teal-700">ថ្ងៃខែឆ្នាំកំណើត:</span> <?= $date ?></p>
                <p><span class="font-semibold text-teal-700">ប្រភេទអ្នកប្រើ:</span> <?= $user_type ?></p>
                <p><span class="font-semibold text-teal-700">លេខទូរស័ព្ទ:</span> <?= $phone ?></p>
                <p><span class="font-semibold text-teal-700">អាសយដ្ឋាន:</span> <?= $address ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<div class="bg-sky-600 text-white text-center py-8 mt-4">
    Power by Department of Computer Science @2025
</div>
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


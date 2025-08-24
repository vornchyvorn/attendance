<?php
session_start();
include '../db/conn.php';

// Get only active announcements
$sql = "SELECT * FROM announcements WHERE status = 'active' ORDER BY created_at DESC";
$result = $conn->query($sql);

if (!$result) {
    die("Query error: " . $conn->error);
}
// Query សម្រាប់ events
$event_sql = "SELECT * FROM events ORDER BY event_date DESC LIMIT 5";
$event_result = $conn->query($event_sql);
if (!$event_result) {
    die("Query error (events): " . $conn->error);
}

?>
<!DOCTYPE html>
<html lang="km">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>សេចក្ដីប្រកាស</title>
  <link href="../dist/style.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:ital,wght@0,100..700;1,100..700&family=Koulen&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
  <style>
    body {
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
       <button onclick="toggleDropdown()" class="bg-blue-400 text-white px-6 py-1 text-sm h-[40px] rounded-full hover:bg-blue-500 shadow-md">
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
 <div class="text-center mt-6 mb-4">
  <button class="relative text-xl font-bold text-sky-700 px-6 py-2 border border-sky-700 rounded hover:text-white z-10 transition duration-300 after:content-[''] after:block after:absolute after:bottom-0 after:left-0 after:w-full after:h-[2px] after:bg-sky-700">
    សេចក្ដីប្រកាស
</button>
</div>
<!-- Event List -->
<div class="px-4 md:px-20">
  <?php while ($row = $event_result->fetch_assoc()): ?>
    <div class="bg-white rounded-lg shadow-md p-6 mb-6 max-w-3xl mx-auto border-l-4 border-green-500">
      <h2 class="text-md font-semibold text-green-700 mb-2"><?= htmlspecialchars($row['title']) ?></h2>
      <p class="text-gray-700 mb-2"><?= nl2br(htmlspecialchars($row['description'])) ?></p>
      <div class="text-sm text-gray-500">
        🗓 ថ្ងៃទី <?= date('d/m/Y', strtotime($row['event_date'])) ?> |
        🕒 ម៉ោង <?= date('g:i A', strtotime($row['event_start'])) ?> ដល់
        <?= date('g:i A', strtotime($row['event_end'])) ?>
        <?php if (!empty($row['location'])): ?>
          | 📍ទីតាំង: <?= htmlspecialchars($row['location']) ?>
        <?php endif; ?>
      </div>
    </div>
  <?php endwhile; ?>
</div>

<!-- Announcement List -->
<div class="px-4 md:px-20">
  <?php while ($row = $result->fetch_assoc()): ?>
    <div class="bg-white rounded-lg shadow-md p-6 mb-6 max-w-3xl mx-auto border-l-4 border-green-500">
      <h2 class="text-md font-semibold text-green-700 mb-2"><?= htmlspecialchars($row['title']) ?></h2>
      <p class="text-gray-700 leading-relaxed"><?= nl2br(htmlspecialchars($row['content'])) ?></p>
      <div class="text-sm text-gray-500 mt-3">
        បានបង្កើតនៅថ្ងៃទី <?= date('d/m/Y g:i A', strtotime($row['created_at'])) ?>
      </div>
    </div>
  <?php endwhile; ?>
</div>
</main>
<!-- Footer -->
<div class="bg-sky-600 text-white text-center py-8">
    Power by Department of Computer Science @2025
</div>

<!-- Scripts -->
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

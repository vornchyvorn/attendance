<!DOCTYPE html>
<html lang="km">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>សេចក្ដីប្រកាស</title>
  <link href="../dist/style.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
  <style>
    body {
      font-family: "Khmer OS Siemreap", sans-serif;
    }
  </style>
</head>
<body class="bg-blue-50 min-h-screen flex flex-col">

<!-- Header -->
<header class="bg-teal-600 sticky top-0 z-50 shadow">
  <nav class="flex items-center justify-between px-4 py-3 md:py-4 md:px-8 max-w-screen-xl mx-auto">
    <!-- Logo -->
    <div class="flex items-center space-x-2">
      <img src="../pic/logo.jpg" alt="Logo" class="w-[58px] h-[58px] rounded-full"/>
    </div>

    <!-- Desktop Menu -->
    <ul class="hidden md:flex items-center space-x-16 text-white text-md font-medium">
      <li><a href="dashboard.php" class="hover:text-gray-300">ទំព័រដើម</a></li>
      <li><a href="announcements.php" class="hover:text-gray-300">ប្រកាស</a></li>
      <li><a href="participants.php" class="hover:text-gray-300">អ្នកចូលរួម</a></li>
      <li><a href="about.php" class="hover:text-gray-300">អំពីយើង</a></li>
    </ul>

    <!-- Right Controls -->
    <div class="flex items-center space-x-3 md:space-x-4">
      <!-- Profile Button -->
      <div class="relative">
        <button onclick="toggleDropdown()" class="bg-amber-400 text-white px-3 py-1 text-sm h-[40px] rounded-full hover:bg-amber-500">
          My Profile
        </button>
        <ul id="dropdownMenu" class="absolute right-0 mt-2 bg-white text-black shadow-lg rounded hidden z-10 w-40 text-sm">
          <li><a href="user_profile.php" class="block px-4 py-2 hover:bg-gray-100">User Profile</a></li>
          <li><hr class="border-gray-200"></li>
          <li><a href="login.php" class="block px-4 py-2 hover:bg-gray-100">Log out</a></li>
        </ul>
      </div>

      <!-- Mobile Toggle Button -->
      <button onclick="toggleMobileMenu()" class="md:hidden text-white text-2xl">
        <i class="fa-solid fa-bars" id="menuIcon"></i>
      </button>
    </div>
  </nav>

  <!-- Mobile Menu -->
  <div id="mobileMenu" class="md:hidden hidden bg-teal-600 px-6 pb-3 text-white text-base font-medium">
    <a href="dashboard.php" class="block py-2 border-b border-white/20">ទំព័រដើម</a>
    <a href="announcements.php" class="block py-2 border-b border-white/20">ប្រកាស</a>
    <a href="participants.php" class="block py-2 border-b border-white/20">អ្នកចូលរួម</a>
    <a href="about.php" class="block py-2">អំពីយើង</a>
  </div>
</header>

<!-- Main Content -->
<main class="max-w-5xl mx-auto p-6 mt-6 grid gap-6 flex-grow">

  <!-- Goal Box -->
  <section class="bg-white p-6 rounded-lg shadow">
    <h2 class="text-xl font-bold text-sky-700 mb-2"><i class="fas fa-bullseye mr-2"></i>គោលបំណង</h2>
    <p class="text-gray-800">
      ប្រព័ន្ធនេះត្រូវបានបង្កើតឡើងដើម្បីជួយក្នុងការកំណត់វត្តមាន
      នៃសិស្ស និងគ្រូ។ ប្រព័ន្ធអាចចុះឈ្មោះ ប្រើប្រាស់ ប្រកាសព័ត៌មាន និងចេញរបាយការណ៍។
    </p>
  </section>

  <!-- Features Box -->
  <section class="bg-white p-6 rounded-lg shadow">
    <h2 class="text-xl font-bold text-sky-700 mb-2"><i class="fas fa-list-check mr-2"></i>មុខងារ</h2>
    <ul class="list-disc list-inside text-gray-800 space-y-1">
      <li>ចុះឈ្មោះសិស្ស/គ្រូ</li>
      <li>ចូលប្រើប្រព័ន្ធ</li>
      <li>កំណត់វត្តមាន (Check-in/Check-out)</li>
      <li>មើលប្រកាសព័ត៌មានពីគ្រូ</li>
      <li>ចេញរបាយការណ៍វត្តមាន</li>
    </ul>
  </section>

  <!-- Department Info -->
  <section class="bg-white p-6 rounded-lg shadow">
    <h2 class="text-xl font-bold text-sky-700 mb-2"><i class="fas fa-building mr-2"></i>ព័ត៌មានដេប៉ាតឺម៉ង់</h2>
    <p class="text-gray-800">
      ដេប៉ាតឺម៉ង់វិទ្យាសាស្រ្តកុំព្យូទ័រ<br>
      វិទ្យាស្ថានបច្ចេកវិទ្យាកំពង់ស្ពឺ<br>
      អ៊ីមែល: info@ksit.edu.kh<br>
      ទូរស័ព្ទ: ០៨៧ ៨៨៨ ៨៨៨
    </p>
  </section>

 
</main>

<!-- Footer -->
<div class="bg-teal-600 text-white text-center py-8">
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

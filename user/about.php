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
    body{

      font-family: "Koulen", sans-serif;
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
<main class="w-full max-w-2xl mx-auto px-4 sm:px-6 md:px-8 mt-6 flex flex-col gap-6">
  <section class="bg-white p-4 sm:p-6 rounded-lg shadow hover:bg-teal-50 transition duration-300 w-full border-l-4 border-green-500 border-r-4">
    <h2 class="text-lg sm:text-xl font-bold text-teal-600 mb-2 text-center">
      សូមស្វាគមន៍មកកាន់
    </h2>
    <div class="relative w-full overflow-hidden">
      <p class="text-gray-600 whitespace-nowrap animate-marquee text-sm sm:text-base md:text-lg">
        គេហទំព័រស្រង់វត្តមានសម្រាប់ការចូលរួមព្រឹត្តិការណ៍ផ្សេងៗ របស់យើងខ្ញុំ។
      </p>
    </div>
  </section>
</main>
<style>
@keyframes marquee {
  0% { transform: translateX(100%); }
  100% { transform: translateX(-100%); }
}
.animate-marquee {
  display: inline-block;
  white-space: nowrap;
  animation: marquee 12s linear infinite; /* speed adjustable */
}
</style>
<main class="max-w-5xl mx-auto p-6 mt-6 grid gap-6 flex-grow">
  <section class="bg-sky-600 p-6 rounded-lg shadow border-l-4 border-white">
    <h2 class="text-xl font-bold text-white mb-2"><i class="fas fa-bullseye mr-2 "></i>PURPOSE</h2>
    <p class="text-white">
      ប្រព័ន្ធនេះត្រូវបានបង្កើតឡើងដើម្បីជួយក្នុងការកំណត់វត្តមាន
      នៃសិស្ស និងគ្រូ។ ប្រព័ន្ធអាចចុះឈ្មោះ ប្រើប្រាស់ ប្រកាសព័ត៌មាន និងចេញរបាយការណ៍។
    </p>
  </section>
 
  <section class="bg-sky-600 p-6 rounded-lg shadow border-l-4 border-white">
    <h2 class="text-xl font-bold text-white mb-2"><i class="fas fa-list-check mr-2"></i>FUNCTION</h2>
    <ul class="list-disc list-inside text-white space-y-1">
      <li>ស្កេនQR CODE ដើម្បីចុះឈ្មោះប្រើប្រាស់</li>
      <li>ចុះឈ្មោះសិស្ស/គ្រូ</li>
      <li>ចូលប្រើប្រាស់ប្រព័ន្ធ</li>
      <li>កំណត់វត្តមាន (Check-in/Check-out)</li>
      <li>មើលប្រកាសព័ត៌មានពីគ្រូឬអ្នកគ្រប់គ្រង</li>
      <li>ចេញរបាយការណ៍វត្តមាន</li>
    </ul>
  </section>
 
  <section class="bg-sky-600 p-6 rounded-lg shadow border-l-4 border-white">
    <h2 class="text-xl font-bold text-white mb-2"><i class="fas fa-building mr-2"></i>Department Information</h2>
    <p class="text-white">
      ដេប៉ាតឺម៉ង់វិទ្យាសាស្រ្តកុំព្យូទ័រ<br>
      វិទ្យាស្ថានបច្ចេកវិទ្យាកំពង់ស្ពឺ<br>
      អ៊ីមែល: info@ksit.edu.kh<br>
      ទូរស័ព្ទ: ០៨៧ ៨៨៨ ៨៨៨
    </p>
  </section>
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

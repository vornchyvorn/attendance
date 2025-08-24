<?php
session_start();
include '../db/conn.php';
if (!isset($_SESSION['student_id'])) {
  header('Location: login.php');
  exit();
}
$student_id = $_SESSION['student_id'];
$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
unset($_SESSION['message']);
?>
<!DOCTYPE html>
<html lang="km">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>dashboard</title>
  <link rel="stylesheet" href="/public/style.css">
  <link href="../dist/style.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:ital,wght@0,100..700;1,100..700&family=Koulen&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
  <style>
    body {
      font-family: "Kantumruy Pro", sans-serif;
    }
  </style>
</head>

<body class="bg-white min-h-screen flex flex-col">

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
  <main class="flex-grow">
    <div class="px-4 py-8 text-center">
      <?php if (!empty($message)): ?>
        <div class="bg-green-100 text-green-800 p-2 mb-2 border border-green-200 rounded">
          <?= htmlspecialchars($message) ?>
        </div>
      <?php endif; ?>
      <?php
      date_default_timezone_set('Asia/Phnom_Penh');
      $currentHour = (int) date("H");
      $showMorning = ($currentHour >= 7 && $currentHour < 13);
      $showEvening = ($currentHour >= 13 && $currentHour <= 24);
      ?>
      <?php if ($showMorning): ?>
        <div class="border-2 border-blue-400 bg-sky-600 p-6 rounded-lg w-full max-w-xs mx-auto mb-4">
          <h3 class="text-xl font-bold text-white mb-4">វេលាព្រឹក</h3>
          <form method="post" action="checkin.php" class="inline-block">
            <input type="hidden" name="time_period" value="morning">
            <button type="submit"
              class="bg-sky-500 text-white px-6 py-2 rounded-lg shadow hover:bg-sky-600">ចូល</button>
          </form>
          <form method="post" action="checkout.php" class="inline-block mt-2">
            <input type="hidden" name="time_period" value="morning">
            <button type="submit"
              class="bg-sky-500 text-white px-6 py-2 rounded-lg shadow hover:bg-sky-600">ចេញ</button>
          </form>
        </div>
      <?php endif; ?>
      <?php if ($showEvening): ?>
        <div class="border-2 border-blue-400 bg-sky-600 p-6 rounded-lg w-full max-w-xs mx-auto mb-4">
          <h3 class="text-xl font-bold text-white mb-4">វេលាល្ងាច</h3>
          <form method="post" action="checkin.php" class="inline-block">
            <input type="hidden" name="time_period" value="evening">
            <button type="submit" class="bg-sky-500 text-white px-6 py-2 rounded-lg shadow hover:bg-sky-600">ចូល</button>
          </form>
          <form method="post" action="checkout.php" class="inline-block mt-2">
            <input type="hidden" name="time_period" value="evening">
            <button type="submit" class="bg-sky-500 text-white px-6 py-2 rounded-lg shadow hover:bg-sky-600">ចេញ</button>
          </form>
        </div>
      <?php endif; ?>
      <h3 class="text-xl font-bold mb-4 text-center text-sky-700 underline hover:text-teal-700 duration-100 ">
        ប្រវត្តិនៃការចូលរួម</h3>
      <form method="get" class="flex flex-col gap-2 md:gap-4 px-4 mb-6">
        <div class="flex flex-row gap-2 items-center">
          <input type="date" name="filter_date"
            value="<?= isset($_GET['filter_date']) ? htmlspecialchars($_GET['filter_date']) : '' ?>"
            class="border border-gray-300 px-4 py-2 rounded shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400 w-full md:w-64">

          <button type="submit"
            class="bg-sky-500 text-white px-4 py-2 rounded hover:bg-sky-700 transition shadow-md whitespace-nowrap">
            <i class="fa-solid fa-magnifying-glass mr-1"></i>
          </button>
        </div>
        <div class="text-start">
          <select name="limit" onchange="this.form.submit()"
            class="border border-gray-300 px-4 py-2 rounded text-start shadow-sm w-18">

            <option value="10" <?= ($_GET['limit'] ?? '') == 10 ? 'selected' : '' ?>>10</option>
            <option value="25" <?= ($_GET['limit'] ?? '') == 25 ? 'selected' : '' ?>>25</option>
            <option value="50" <?= ($_GET['limit'] ?? '') == 50 ? 'selected' : '' ?>>50</option>
            <option value="100" <?= ($_GET['limit'] ?? '') == 100 ? 'selected' : '' ?>>100</option>
          </select>
        </div>
      </form>
      <?php
      $limit = isset($_GET['limit']) && is_numeric($_GET['limit']) ? (int) $_GET['limit'] : 10;
      $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
      $offset = ($page - 1) * $limit;
      $params = [$student_id];
      $types = "i";
      $filter_clause = "";
      if (!empty($_GET['filter_date'])) {
        $filter_clause = " AND DATE(check_in) = ?";
        $params[] = $_GET['filter_date'];
        $types .= "s";
      }

      // Count total rows
      $count_stmt = $conn->prepare("SELECT COUNT(*) AS total FROM attendance WHERE student_id = ?" . $filter_clause);
      $count_stmt->bind_param($types, ...$params);
      $count_stmt->execute();
      $total = $count_stmt->get_result()->fetch_assoc()['total'];
      $total_pages = ceil($total / $limit);
      $count_stmt->close();

      // Get paginated results
      $query = "SELECT * FROM attendance WHERE student_id = ?" . $filter_clause . " ORDER BY check_in DESC LIMIT ? OFFSET ?";
      $params[] = $limit;
      $params[] = $offset;
      $types .= "ii";

      $stmt = $conn->prepare($query);
      $stmt->bind_param($types, ...$params);
      $stmt->execute();
      $result = $stmt->get_result();
      ?>
      <!-- Table -->
      <div class="overflow-x-auto px-4">
        <table class="min-w-full text- border border-gray-300 rounded-md text-center shadow-sm">
          <thead class="bg-sky-600 text-white">
            <tr>
              <th class="p-3 border">ថ្ងៃ</th>
              <th class="p-3 border">ម៉ោងចូល</th>
              <th class="p-3 border">ម៉ោងចេញ</th>
            </tr>
          </thead>
          <tbody class="bg-white">
            <?php if ($result->num_rows > 0): ?>
              <?php while ($row = $result->fetch_assoc()): ?>
                <tr class="even:bg-blue-50">
                  <td class="p-3 border"><?= date("d-M-Y", strtotime($row['check_in'])) ?></td>
                  <td class="p-3 border"><?= date("h:i:s A", strtotime($row['check_in'])) ?></td>
                  <td class="p-3 border">
                    <?php
                    if (!empty($row['check_out']) && $row['check_out'] != '0000-00-00 00:00:00') {
                      echo date("h:i:s A", strtotime($row['check_out']));
                    } else {
                      echo '-';
                    }
                    ?>
                  </td>

                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="3" class="p-3 text-red-500"> មិនមានទិន្នន័យ</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>


      <?php if ($total_pages > 1): ?>
        <div class="flex flex-wrap justify-center mt-6 gap-2 px-4">
          <?php
          $url = "?";
          if (!empty($_GET['filter_date']))
            $url .= "filter_date=" . $_GET['filter_date'] . "&";
          if (!empty($_GET['limit']))
            $url .= "limit=" . $_GET['limit'] . "&";

          if ($page > 1) {
            echo "<a href='{$url}page=" . ($page - 1) . "' class='px-3 py-1 bg-gray-200 hover:bg-gray-300 rounded'>« មុន</a>";
          }
          for ($i = 1; $i <= $total_pages; $i++) {
            $active = $i == $page ? "bg-blue-400 text-white" : "bg-gray-200 hover:bg-gray-300";
            echo "<a href='{$url}page={$i}' class='px-3 py-1 rounded {$active}'>{$i}</a>";
          }
          if ($page < $total_pages) {
            echo "<a href='{$url}page=" . ($page + 1) . "' class='px-3 py-1 bg-gray-200 hover:bg-gray-300 rounded'>បន្ទាប់ »</a>";
          }
          ?>
        </div>
      <?php endif; ?>
    </div>
  </main>
  <div class="bg-sky-600 text-white text-center py-8">
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

<?php
session_start();
include '../db/conn.php';

// Redirect to login if not admin
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] !== 'admin') {
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

$result = $conn->query("SELECT * FROM admin WHERE role IN ('admin', 'staff') ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Manage Staff</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen p-6">
  <div class="max-w-7xl mx-auto bg-white p-6 rounded-lg shadow-lg">
    <h1 class="text-2xl font-bold text-center text-gray-800 mb-6">បញ្ជីអ្នកគ្រប់គ្រង និងគ្រូ (Admin & Staff)</h1>
    <div class="overflow-x-auto">
      <table class="min-w-full bg-white border border-gray-300">
        <thead>
          <tr class="bg-gray-200 text-gray-700">
            <th class="py-3 px-4 border">#</th>
            <th class="py-3 px-4 border">ឈ្មោះ</th>
           
            <th class="py-3 px-4 border">អ៊ីមែល</th>
            <th class="py-3 px-4 border">ប្រភេទ</th>
            <th class="py-3 px-4 border">លេខទូរសព្ទ</th>
            <th class="py-3 px-4 border">រូបភាព</th>
            <th class="py-3 px-4 border">សកម្មភាព</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result && $result->num_rows > 0): ?>
            <?php $i = 1; while ($row = $result->fetch_assoc()): ?>
              <tr class="text-center border-b hover:bg-gray-50">
                <td class="py-2 px-4 border"><?= $i++ ?></td>
                <td class="py-2 px-4 border"><?= htmlspecialchars($row['username']) ?></td>
                
                <td class="py-2 px-4 border"><?= htmlspecialchars($row['email']) ?></td>
                
                <td class="py-2 px-4 border capitalize"><?= htmlspecialchars($row['role']) ?></td>
                <td class="py-2 px-4 border"><?= htmlspecialchars($row['phone']) ?></td>
                <td class="py-2 px-4 border"><?= htmlspecialchars($row['image']) ?></td>
                <td class="py-2 px-4 border">
                  <a href="edit_staff.php?id=<?= $row['id'] ?>" class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600">កែ</a>
                  <a href="delete_staff.php?id=<?= $row['id'] ?>" onclick="return confirm('តើអ្នកពិតជាចង់លុបមែនទេ?')" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">លុប</a>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="8" class="text-center py-4">គ្មានទិន្នន័យ staff/admin ទេ។</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>

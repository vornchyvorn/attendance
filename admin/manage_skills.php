<?php
session_start();
include '../db/conn.php';
$error = '';
$success = '';

// === Delete skill ===
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM majors WHERE id = ?");
    $stmt->bind_param('i', $delete_id);
    if ($stmt->execute()) {
        $success = "លុបជំនាញបានជោគជ័យ។";
    } else {
        $error = "លុបមិនបានសម្រេច។";
    }
}

// === Update skill ===
if (isset($_POST['update_skill'])) {
    $id = intval($_POST['id']);
    $major_name = trim($_POST['major_name']);
    $stmt = $conn->prepare("UPDATE majors SET major_name = ? WHERE id = ?");
    $stmt->bind_param('si', $major_name, $id);
    if ($stmt->execute()) {
        $success = "កែប្រែជំនាញបានជោគជ័យ។";
    } else {
        $error = "មិនអាចកែប្រែបាន។";
    }
}

// === Get all majors ===
$result = $conn->query("SELECT * FROM majors ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Skills</title>
<link href="../dist/style.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <!-- Success / Error Message -->
    <div class="max-w-2xl mx-auto mt-6">
        <?php if ($error): ?>
            <p class="bg-red-100 text-red-700 p-3 rounded mb-2"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <?php if ($success): ?>
            <p class="bg-green-100 text-green-700 p-3 rounded mb-2"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>
    </div>

    <h1 class="text-2xl font-bold text-center mt-6 mb-4">គ្រប់គ្រងជំនាញសិក្សា</h1>

    <!-- Table of majors -->
    <div class="overflow-x-auto max-w-2xl mx-auto bg-white rounded shadow p-4">
        <table class="min-w-full border-collapse border border-gray-200">
            <thead class="bg-blue-50">
                <tr>
                    <th class="p-3 border border-gray-200 text-left">ID</th>
                    <th class="p-3 border border-gray-200 text-left">ជំនាញសិក្សា</th>
                    <th class="p-3 border border-gray-200 text-center">សកម្មភាព</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td class="p-3 border border-gray-200"><?= $row['id'] ?></td>
                    <td class="p-3 border border-gray-200"><?= htmlspecialchars($row['major_name']) ?></td>
                    <td class="p-3 border border-gray-200 text-center">
                        <!-- Edit -->
                        <button onclick="openModal(<?= $row['id'] ?>, '<?= addslashes($row['major_name']) ?>')" class="bg-blue-500 text-white px-3 py-1 rounded">កែប្រែ</button>
                        <!-- Delete -->
                        <a href="?delete_id=<?= $row['id'] ?>" class="bg-red-500 text-white px-3 py-1 rounded" onclick="return confirm('ចង់លុបទេ?')">លុប</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
        <div class="bg-white p-6 rounded shadow w-96 relative">
            <h2 class="text-xl font-semibold mb-4">កែប្រែជំនាញ</h2>
            <form method="post">
                <input type="hidden" name="id" id="edit_id">
                <label class="block mb-2">ជំនាញសិក្សា</label>
                <input type="text" name="major_name" id="edit_name" class="w-full border p-2 rounded mb-4" required>
                <div class="text-right">
                    <button type="submit" name="update_skill" class="bg-green-500 text-white px-4 py-2 rounded">រក្សាទុក</button>
                    <button type="button" onclick="closeModal()" class="bg-gray-500 text-white px-4 py-2 rounded">បិទ</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(id, name) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_name').value = name;
            document.getElementById('editModal').classList.remove('hidden');
            document.getElementById('editModal').classList.add('flex');
        }
        function closeModal() {
            document.getElementById('editModal').classList.add('hidden');
        }
    </script>
</body>
</html>

<?php
session_start();
include '../db/conn.php';

// 🔐 ពិនិត្យសុវត្ថិភាព
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// ✅ Upload image ប្រសិនបើមាន
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

// ✅ ទាញយកព័ត៌មានអ្នកប្រើ
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$row = $result->fetch_assoc()) {
    echo "មិនមានអ្នកប្រើ!";
    exit;
}

// ✅ Prepare display data
$profile_img = '../pic/user.png';
if (!empty($row['image']) && file_exists("../upload_img/" . $row['image'])) {
    $profile_img = "../upload_img/" . $row['image'];
}

$username = htmlspecialchars($row['username']);
$student_id = htmlspecialchars($row['student_id'] ?? 'មិនមាន');
$gender = htmlspecialchars($row['gender']);
$gmail = htmlspecialchars($row['gmail']);
$major = htmlspecialchars($row['major'] ?? 'មិនមាន');
$date = htmlspecialchars($row['date']);
$user_type = htmlspecialchars($row['user_type'] ?? 'មិនមាន');
$address = htmlspecialchars($row['address'] ?? 'មិនមាន');
?>
<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <title>ព័ត៌មានគណនី</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../dist/style.css" rel="stylesheet">
    <style>
        body {
            font-family: "Khmer OS Siemreap", sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">

<div class="bg-white shadow-lg rounded-xl p-6 w-full max-w-md">
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
                <p><span class="font-semibold text-teal-700">អ៊ីមែល:</span> <?= $gmail ?></p>
                <p><span class="font-semibold text-teal-700">ជំនាញ:</span> <?= $major ?></p>
                <p><span class="font-semibold text-teal-700">ថ្ងៃខែឆ្នាំ:</span> <?= $date ?></p>
                <p><span class="font-semibold text-teal-700">ប្រភេទអ្នកប្រើ:</span> <?= $user_type ?></p>
                <p><span class="font-semibold text-teal-700">អាសយដ្ឋាន:</span> <?= $address ?></p>
            </div>
        </div>
    </div>
</div>

</body>
</html>

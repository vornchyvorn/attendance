<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="dist/style.css" rel="stylesheet">
    <title>QR Login</title>
    <style>
    body{

      font-family: "Koulen", sans-serif;
    }
  </style>
</head>
<body class="bg-white flex items-center justify-center min-h-screen">
    <div class="bg-sky-500 shadow-lg rounded-xl p-8 max-w-md w-full text-center">
        <h2 class="text-2xl font-bold text-white 0 mb-4 font-Kulen">ស្កេនដើម្បីចូល</h2>
        <div class="flex justify-center">
            <?php
            include 'lib/phpqrcode/phpqrcode-master/qrlib.php'; // Update path if needed

            // Get local IP address of XAMPP server
            // $ip = gethostbyname(gethostname()); // safer across platforms
            $projectPath = '/project/attendance/user/login.php'; // adjust if needed
            // $projectPath = 'https://smashing-loyal-owl.ngrok-free.app/project/attendance/user/dashboard.php'; // adjust if needed

            // Create the full local URL
            // $url = "http://$ip$projectPath";
            $url = "https://smashing-loyal-owl.ngrok-free.app/$projectPath";

            // Folder for QR image
            $folder = 'qrcodes/';
            if (!file_exists($folder)) mkdir($folder);

            $file = $folder . 'login_qr.png';

            // Generate QR code
            QRcode::png($url, $file, QR_ECLEVEL_H, 6);

            echo "<img src='$file' alt='QR Code' class='w-48 h-48 rounded-md border border-gray-300'>";
            ?>
        </div>
        
    </div>
</body>
</html>

<?php
include 'lib/phpqrcode/phpqrcode-master/qrlib.php'; // Update path if needed

// Get local IP address of XAMPP server
$ip = gethostbyname(gethostname()); // safer across platforms
$projectPath = '/systemattendance/user/login.php'; // adjust if needed

// Create the full local URL
$url = "http://$ip$projectPath";

// Folder for QR image
$folder = 'qrcodes/';
if (!file_exists($folder)) mkdir($folder);

$file = $folder . 'login_qr.png';

// Generate QR code
QRcode::png($url, $file, QR_ECLEVEL_H, 6);

// Output to browser
echo "<h2>ស្កេនដើម្បីចូល</h2>";
echo "<img src='$file'>";

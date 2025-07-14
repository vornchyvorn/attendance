<?php 
require_once __DIR__ . '/../vendor/autoload.php';
include '../db/conn.php';

use Mpdf\Mpdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;

// Define fonts folder path
$fontDir = realpath(__DIR__ . '/../fonts');

// Check font file exists
if (!file_exists($fontDir . '/NotoSansKhmer-Regular.ttf')) {
    die('Font file NotoSansKhmer-Regular.ttf not found in ' . $fontDir);
}

// Initialize mPDF with Khmer font
$mpdf = new \Mpdf\Mpdf([
    'fontDir' => array_merge(
        (new \Mpdf\Config\ConfigVariables())->getDefaults()['fontDir'],
        [realpath(__DIR__ . '/../fonts')]
    ),
    'fontdata' => (new \Mpdf\Config\FontVariables())->getDefaults()['fontdata'] + [
        'khmeros' => [
            'R' => 'KhmerOScontent.ttf',
        ],
    ],
    'default_font' => 'khmeros',
]);


// Get filter parameters
$major = $_GET['major'] ?? '';
$date = $_GET['date'] ?? '';
$time_period = $_GET['time_period'] ?? '';
$event_id = $_GET['event_id'] ?? '';

// Get event title if provided
$event_title = '';
if (!empty($event_id)) {
    $event_stmt = $conn->prepare("SELECT title FROM events WHERE id = ?");
    $event_stmt->bind_param("i", $event_id);
    $event_stmt->execute();
    $event_result = $event_stmt->get_result();
    if ($event_row = $event_result->fetch_assoc()) {
        $event_title = $event_row['title'];
    }
    $event_stmt->close();
}

// Build SQL WHERE clause
$where = "1=1";
$params = [];
$types = '';

if (!empty($major)) {
    $where .= " AND a.major = ?";
    $params[] = $major;
    $types .= 's';
}
if (!empty($date)) {
    $where .= " AND DATE(a.check_in) = ?";
    $params[] = $date;
    $types .= 's';
}
if (!empty($time_period)) {
    $where .= " AND a.time_period = ?";
    $params[] = $time_period;
    $types .= 's';
}
if (!empty($event_id)) {
    $where .= " AND a.event_id = ?";
    $params[] = $event_id;
    $types .= 'i';
}
$sql = "SELECT a.*, u.username 
        FROM attendance a 
        LEFT JOIN users u ON a.student_id = u.student_id 
        WHERE $where 
        ORDER BY a.check_in DESC";

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die('SQL Prepare Failed: ' . $conn->error);
}
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Output buffering
ob_start();
?>
<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8" />
    <style>
        <style>
   <style>
    body {
        font-family: "khmeros", sans-serif;
        font-size: 12pt;
    }


        table {
            border-collapse: collapse;
            width: 100%;
            font-size: 12pt;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
        }
        th {
            background-color: #eef3ff;
        }
        .logo {
            width: 80px;
            height: auto;
        }
        .signature-section {
            margin-top: 50px;
            text-align: right;
            font-size: 12pt;
        }
    </style>
</head>
<body>
    <!-- Logo and header -->
    <div style="text-align:center;">
        <img src="pic/logo.jpg" class="logo" alt="Logo"><br>
        <strong style="font-size:14pt;">វិទ្យាស្ថានបច្ចេកវិទ្យាកំពង់ស្ពឺ</strong><br />
        <div style="font-size:14pt;">បញ្ជីវត្តមានសិស្ស</div>
        <?php if ($event_title): ?>
            <div style="font-size:13pt;">ព្រឹត្តិការណ៍៖ <?= htmlspecialchars($event_title) ?></div>
        <?php endif; ?>
        <div style="font-size:12pt;">ថ្ងៃចេញរបាយការណ៍៖ <?= date('d/m/Y') ?></div>
    </div>
    <br />

    <!-- Table content -->
    <table>
        <thead>
            <tr>
                <th>ល.រ</th>
                <th>ឈ្មោះ</th>
                <th>អត្តលេខ</th>
                <th>ជំនាញ</th>
                <th>វេលា</th>
                <th>ម៉ោងចូល</th>
                <th>ម៉ោងចេញ</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 1;
            while ($row = $result->fetch_assoc()):
                $check_in = !empty($row['check_in']) ? date('d/m/Y H:i', strtotime($row['check_in'])) : '';
                $check_out = !empty($row['check_out']) ? date('d/m/Y H:i', strtotime($row['check_out'])) : 'មិនទាន់ចេញ';
            ?>
            <tr>
                <td><?= $i++ ?></td>
                <td style="text-align:left;"><?= htmlspecialchars($row['username']) ?></td>
                <td><?= htmlspecialchars($row['student_id']) ?></td>
                <td><?= htmlspecialchars($row['major']) ?></td>
                <td><?= htmlspecialchars($row['time_period']) ?></td>
                <td><?= $check_in ?></td>
                <td><?= $check_out ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Signature section -->
    <div class="signature-section">
        <p>ថ្ងៃទី <?= date('d') ?> ខែ <?= date('m') ?> ឆ្នាំ <?= date('Y') ?></p>
        <p><strong>ហត្ថលេខា និងឈ្មោះអ្នកធ្វើរបាយការណ៍</strong></p>
        <br><br>
        <p>.................................................</p>
    </div>
</body>
</html>
<?php
$html = ob_get_clean();

$mpdf->WriteHTML($html);
$mpdf->Output('attendance_report.pdf', 'I');
exit();
?>

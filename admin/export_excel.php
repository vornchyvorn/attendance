<?php
session_start();
include '../db/conn.php';

if (!isset($_SESSION['admin_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../admin/login_admin.php');
    exit();
}


require '../vendor/autoload.php'; 

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

// Prepare filters
$major = $_GET['major'] ?? '';
$date = $_GET['date'] ?? '';
$time_period = $_GET['time_period'] ?? '';
$event_id = $_GET['event_id'] ?? '';

$where = "1";
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

// Fetch event title if event_id given
$event_title = '';
if (!empty($event_id)) {
    $event_stmt = $conn->prepare("SELECT title FROM events WHERE id = ?");
    $event_stmt->bind_param('i', $event_id);
    $event_stmt->execute();
    $event_result = $event_stmt->get_result();
    if ($event_row = $event_result->fetch_assoc()) {
        $event_title = $event_row['title'];
    }
    $event_stmt->close();
}

$sql = "SELECT a.*, u.username FROM attendance a 
        LEFT JOIN users u ON a.student_id = u.student_id 
        WHERE $where ORDER BY a.check_in DESC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Create Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set document properties
$spreadsheet->getProperties()
    ->setCreator("System Attendance")
    ->setTitle("Attendance Export")
    ->setDescription("Exported attendance data");

// Header info at top
$sheet->mergeCells('A1:G1');
$sheet->setCellValue('A1', 'ការបញ្ជីវត្តមាន');

$sheet->mergeCells('A2:G2');
$sheet->setCellValue('A2', 'ព្រឹត្តិការណ៍៖ ' . ($event_title ?: 'គ្រប់ព្រឹត្តិការណ៍'));

$sheet->mergeCells('A3:G3');
$sheet->setCellValue('A3', 'ថ្ងៃ Export៖ ' . date('Y-m-d H:i:s'));

// Style header info
$sheet->getStyle('A1:A3')->getFont()->setBold(true)->setSize(14);
$sheet->getStyle('A1:A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getRowDimension(1)->setRowHeight(30);
$sheet->getRowDimension(2)->setRowHeight(22);
$sheet->getRowDimension(3)->setRowHeight(22);

// Table headers start at row 5
$headerRow = 5;
$sheet->setCellValue('A'.$headerRow, 'ល.រ');
$sheet->setCellValue('B'.$headerRow, 'ឈ្មោះ');
$sheet->setCellValue('C'.$headerRow, 'អត្តលេខ');
$sheet->setCellValue('D'.$headerRow, 'ជំនាញ');
$sheet->setCellValue('E'.$headerRow, 'វេលា');
$sheet->setCellValue('F'.$headerRow, 'ម៉ោងចូល');
$sheet->setCellValue('G'.$headerRow, 'ម៉ោងចេញ');

// Style headers
$headerStyle = $sheet->getStyle("A{$headerRow}:G{$headerRow}");
$headerStyle->getFont()->setBold(true);
$headerStyle->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('4CAF50');
$headerStyle->getFont()->getColor()->setRGB('FFFFFF');
$headerStyle->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
// Add border for header cells
$headerStyle->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('000000'));

// Fill data starting row 6
$rowNum = $headerRow + 1;
$index = 1;
while ($row = $result->fetch_assoc()) {
    $sheet->setCellValue('A' . $rowNum, $index++);
    $sheet->setCellValue('B' . $rowNum, $row['username']);
    $sheet->setCellValue('C' . $rowNum, $row['student_id']);
    $sheet->setCellValue('D' . $rowNum, $row['major']);
    $sheet->setCellValue('E' . $rowNum, ucfirst($row['time_period']));
    $sheet->setCellValue('F' . $rowNum, $row['check_in']);
    $sheet->setCellValue('G' . $rowNum, $row['check_out'] ?? 'មិនទាន់ចេញ');

    // Center align all columns except name (B)
    $sheet->getStyle("A{$rowNum}:A{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle("C{$rowNum}:G{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    // Add border for this data row
    $sheet->getStyle("A{$rowNum}:G{$rowNum}")
        ->getBorders()->getAllBorders()
        ->setBorderStyle(Border::BORDER_THIN)
        ->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('000000'));

    $rowNum++;
}

// Auto size columns
foreach(range('A','G') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Prepare download
$filename = 'attendance_export_' . date('Ymd_His') . '.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$filename\"");
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit();
?>

<?php
require_once '../functions.php';
redirect_if_not_logged_in();
redirect_if_not_admin();

require '../vendor/autoload.php'; // For PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$message = '';

// Handle export to Excel
if (isset($_GET['export']) && $_GET['export'] === 'excel') {
    $stmt = $pdo->query('
        SELECT b.id, u.nama AS user_nama, r.nama AS room_nama, b.tanggal_booking, b.status
        FROM bookings b
        JOIN users u ON b.user_id = u.id
        JOIN rooms r ON b.room_id = r.id
        ORDER BY b.tanggal_booking DESC
    ');
    $bookings = $stmt->fetchAll();

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Header
    $sheet->setCellValue('A1', 'ID Booking');
    $sheet->setCellValue('B1', 'Nama User');
    $sheet->setCellValue('C1', 'Nama Kamar');
    $sheet->setCellValue('D1', 'Tanggal Booking');
    $sheet->setCellValue('E1', 'Status');

    $row = 2;
    foreach ($bookings as $booking) {
        $sheet->setCellValue('A' . $row, $booking['id']);
        $sheet->setCellValue('B' . $row, $booking['user_nama']);
        $sheet->setCellValue('C' . $row, $booking['room_nama']);
        $sheet->setCellValue('D' . $row, $booking['tanggal_booking']);
        $sheet->setCellValue('E' . $row, $booking['status']);
        $row++;
    }

    $writer = new Xlsx($spreadsheet);
    $filename = 'laporan_booking_' . date('Ymd_His') . '.xlsx';

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    $writer->save('php://output');
    exit();
}

// Get booking statistics
$stmt = $pdo->query('SELECT COUNT(*) AS total FROM bookings');
$total_bookings = $stmt->fetchColumn();

$stmt = $pdo->query('SELECT COUNT(*) AS confirmed FROM bookings WHERE status = "confirmed"');
$confirmed_bookings = $stmt->fetchColumn();

$stmt = $pdo->query('SELECT COUNT(*) AS pending FROM bookings WHERE status = "pending"');
$pending_bookings = $stmt->fetchColumn();

$stmt = $pdo->query('SELECT COUNT(*) AS cancelled FROM bookings WHERE status = "cancelled"');
$cancelled_bookings = $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Dashboard Admin - Bale's Room</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <nav class="bg-blue-600 p-4 text-white flex justify-between">
        <div>Dashboard Admin - <?= htmlspecialchars($_SESSION['nama']) ?></div>
        <div>
            <a href="manage_rooms.php" class="mr-4 hover:underline">Kelola Kamar</a>
            <a href="confirm_booking.php" class="mr-4 hover:underline">Konfirmasi Booking</a>
            <a href="../logout.php" class="hover:underline">Logout</a>
        </div>
    </nav>
    <main class="p-6 max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-6">Statistik Pemesanan</h1>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white p-4 rounded shadow text-center">
                <div class="text-2xl font-bold"><?= $total_bookings ?></div>
                <div>Total Pemesanan</div>
            </div>
            <div class="bg-white p-4 rounded shadow text-center">
                <div class="text-2xl font-bold text-green-600"><?= $confirmed_bookings ?></div>
                <div>Pemesanan Terkonfirmasi</div>
            </div>
            <div class="bg-white p-4 rounded shadow text-center">
                <div class="text-2xl font-bold text-yellow-500"><?= $pending_bookings ?></div>
                <div>Pemesanan Pending</div>
            </div>
            <div class="bg-white p-4 rounded shadow text-center">
                <div class="text-2xl font-bold text-red-600"><?= $cancelled_bookings ?></div>
                <div>Pemesanan Dibatalkan</div>
            </div>
        </div>
        <a href="?export=excel" class="inline-block bg-green-600 text-white py-2 px-4 rounded hover:bg-green-700 transition">Ekspor ke Excel</a>
    </main>
</body>
</html>

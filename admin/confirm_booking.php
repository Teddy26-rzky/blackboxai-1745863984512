<?php
require_once '../functions.php';
redirect_if_not_logged_in();
redirect_if_not_admin();

$message = '';

// Handle confirmation or cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = $_POST['booking_id'] ?? null;
    $action = $_POST['action'] ?? null;

    if ($booking_id && in_array($action, ['confirm', 'cancel'])) {
        if ($action === 'confirm') {
            $stmt = $pdo->prepare('UPDATE bookings SET status = "confirmed" WHERE id = ?');
            $stmt->execute([$booking_id]);
            $message = 'Booking berhasil dikonfirmasi.';
        } elseif ($action === 'cancel') {
            $stmt = $pdo->prepare('UPDATE bookings SET status = "cancelled" WHERE id = ?');
            $stmt->execute([$booking_id]);
            $message = 'Booking berhasil dibatalkan.';
        }
    } else {
        $message = 'Data tidak valid.';
    }
}

// Get pending bookings
$stmt = $pdo->query('
    SELECT b.id, u.nama AS user_nama, r.nama AS room_nama, b.tanggal_booking
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    JOIN rooms r ON b.room_id = r.id
    WHERE b.status = "pending"
    ORDER BY b.tanggal_booking ASC
');
$bookings = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Konfirmasi Booking - Bale's Room</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <nav class="bg-blue-600 p-4 text-white flex justify-between">
        <div>Konfirmasi Booking - <?= htmlspecialchars($_SESSION['nama']) ?></div>
        <div>
            <a href="dashboard.php" class="mr-4 hover:underline">Dashboard</a>
            <a href="manage_rooms.php" class="mr-4 hover:underline">Kelola Kamar</a>
            <a href="../logout.php" class="hover:underline">Logout</a>
        </div>
    </nav>
    <main class="p-6 max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-6">Konfirmasi Pemesanan</h1>
        <?php if ($message): ?>
            <div class="mb-4 text-green-600"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <?php if (!$bookings): ?>
            <p>Tidak ada pemesanan yang perlu dikonfirmasi.</p>
        <?php else: ?>
            <table class="w-full border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="border border-gray-300 p-2">Nama User</th>
                        <th class="border border-gray-300 p-2">Nama Kamar</th>
                        <th class="border border-gray-300 p-2">Tanggal Booking</th>
                        <th class="border border-gray-300 p-2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td class="border border-gray-300 p-2"><?= htmlspecialchars($booking['user_nama']) ?></td>
                            <td class="border border-gray-300 p-2"><?= htmlspecialchars($booking['room_nama']) ?></td>
                            <td class="border border-gray-300 p-2"><?= htmlspecialchars($booking['tanggal_booking']) ?></td>
                            <td class="border border-gray-300 p-2">
                                <form method="POST" class="inline-block">
                                    <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>" />
                                    <button type="submit" name="action" value="confirm" class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700 transition mr-2">Konfirmasi</button>
                                    <button type="submit" name="action" value="cancel" class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 transition">Batalkan</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </main>
</body>
</html>

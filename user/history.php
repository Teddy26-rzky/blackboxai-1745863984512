<?php
require_once '../functions.php';
redirect_if_not_logged_in();

$stmt = $pdo->prepare('
    SELECT b.*, r.nama AS room_nama, r.harga
    FROM bookings b
    JOIN rooms r ON b.room_id = r.id
    WHERE b.user_id = ?
    ORDER BY b.tanggal_booking DESC
');
$stmt->execute([$_SESSION['user_id']]);
$bookings = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Histori Pemesanan - Bale's Room</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <nav class="bg-blue-600 p-4 text-white flex justify-between">
        <div>Selamat datang, <?= htmlspecialchars($_SESSION['nama']) ?></div>
        <div>
            <a href="rooms.php" class="mr-4 hover:underline">Daftar Kamar</a>
            <a href="../logout.php" class="hover:underline">Logout</a>
        </div>
    </nav>
    <main class="p-6 max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-6">Histori Pemesanan</h1>
        <?php if (!$bookings): ?>
            <p>Belum ada histori pemesanan.</p>
        <?php else: ?>
            <table class="w-full border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="border border-gray-300 p-2">Kamar</th>
                        <th class="border border-gray-300 p-2">Tanggal Booking</th>
                        <th class="border border-gray-300 p-2">Harga</th>
                        <th class="border border-gray-300 p-2">Status</th>
                        <th class="border border-gray-300 p-2">Invoice</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td class="border border-gray-300 p-2"><?= htmlspecialchars($booking['room_nama']) ?></td>
                            <td class="border border-gray-300 p-2"><?= htmlspecialchars($booking['tanggal_booking']) ?></td>
                            <td class="border border-gray-300 p-2">Rp <?= number_format($booking['harga'], 0, ',', '.') ?></td>
                            <td class="border border-gray-300 p-2">
                                <?php
                                $status = $booking['status'];
                                $color = 'gray';
                                if ($status === 'pending') $color = 'yellow-500';
                                elseif ($status === 'confirmed') $color = 'green-600';
                                elseif ($status === 'cancelled') $color = 'red-600';
                                ?>
                                <span class="font-semibold text-<?= $color ?>"><?= ucfirst($status) ?></span>
                            </td>
                            <td class="border border-gray-300 p-2">
                                <?php if ($booking['invoice_url']): ?>
                                    <a href="../<?= htmlspecialchars($booking['invoice_url']) ?>" target="_blank" class="text-blue-600 hover:underline">Lihat Invoice</a>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </main>
</body>
</html>

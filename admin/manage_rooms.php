<?php
require_once '../functions.php';
redirect_if_not_logged_in();
redirect_if_not_admin();

$message = '';

// Handle status update and photo upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_id = $_POST['room_id'] ?? null;
    $status = $_POST['status'] ?? null;

    if ($room_id && $status) {
        // Handle photo upload if exists
        $foto_filename = null;
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            $tmp_name = $_FILES['foto']['tmp_name'];
            $name = basename($_FILES['foto']['name']);
            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array($ext, $allowed)) {
                $foto_filename = uniqid() . '.' . $ext;
                move_uploaded_file($tmp_name, $upload_dir . $foto_filename);
            } else {
                $message = 'Format foto tidak didukung. Gunakan jpg, jpeg, png, atau gif.';
            }
        }

        if (!$message) {
            if ($foto_filename) {
                $stmt = $pdo->prepare('UPDATE rooms SET status = ?, foto = ? WHERE id = ?');
                $stmt->execute([$status, $foto_filename, $room_id]);
            } else {
                $stmt = $pdo->prepare('UPDATE rooms SET status = ? WHERE id = ?');
                $stmt->execute([$status, $room_id]);
            }
            $message = 'Data kamar berhasil diperbarui.';
        }
    } else {
        $message = 'Data tidak lengkap.';
    }
}

// Get all rooms
$stmt = $pdo->query('SELECT * FROM rooms');
$rooms = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Kelola Kamar - Bale's Room</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <nav class="bg-blue-600 p-4 text-white flex justify-between">
        <div>Kelola Kamar - <?= htmlspecialchars($_SESSION['nama']) ?></div>
        <div>
            <a href="dashboard.php" class="mr-4 hover:underline">Dashboard</a>
            <a href="confirm_booking.php" class="mr-4 hover:underline">Konfirmasi Booking</a>
            <a href="../logout.php" class="hover:underline">Logout</a>
        </div>
    </nav>
    <main class="p-6 max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-6">Kelola Kamar</h1>
        <?php if ($message): ?>
            <div class="mb-4 text-green-600"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <table class="w-full border-collapse border border-gray-300 mb-6">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border border-gray-300 p-2">Foto</th>
                    <th class="border border-gray-300 p-2">Nama</th>
                    <th class="border border-gray-300 p-2">Deskripsi</th>
                    <th class="border border-gray-300 p-2">Harga</th>
                    <th class="border border-gray-300 p-2">Status</th>
                    <th class="border border-gray-300 p-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rooms as $room): ?>
                    <tr>
                        <td class="border border-gray-300 p-2">
                            <?php if ($room['foto'] && file_exists('../uploads/' . $room['foto'])): ?>
                                <img src="../uploads/<?= htmlspecialchars($room['foto']) ?>" alt="<?= htmlspecialchars($room['nama']) ?>" class="h-20 w-20 object-cover rounded" />
                            <?php else: ?>
                                Tidak ada foto
                            <?php endif; ?>
                        </td>
                        <td class="border border-gray-300 p-2"><?= htmlspecialchars($room['nama']) ?></td>
                        <td class="border border-gray-300 p-2"><?= htmlspecialchars($room['deskripsi']) ?></td>
                        <td class="border border-gray-300 p-2">Rp <?= number_format($room['harga'], 0, ',', '.') ?></td>
                        <td class="border border-gray-300 p-2"><?= ucfirst($room['status']) ?></td>
                        <td class="border border-gray-300 p-2">
                            <form method="POST" enctype="multipart/form-data" class="space-y-2">
                                <input type="hidden" name="room_id" value="<?= $room['id'] ?>" />
                                <select name="status" class="border border-gray-300 rounded px-2 py-1 w-full">
                                    <option value="available" <?= $room['status'] === 'available' ? 'selected' : '' ?>>Tersedia</option>
                                    <option value="maintenance" <?= $room['status'] === 'maintenance' ? 'selected' : '' ?>>Maintenance</option>
                                </select>
                                <input type="file" name="foto" accept="image/*" class="w-full" />
                                <button type="submit" class="w-full bg-blue-600 text-white py-1 rounded hover:bg-blue-700 transition">Simpan</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</body>
</html>

<?php
session_start();

if (isset($_SESSION['user_id'])) {
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        header('Location: admin/dashboard.php');
    } else {
        header('Location: user/rooms.php');
    }
    exit();
} else {
    header('Location: login.php');
    exit();
}
?>

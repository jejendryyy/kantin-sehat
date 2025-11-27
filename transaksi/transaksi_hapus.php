<?php
// transaksi_hapus.php
session_start();
if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) $_SESSION['cart'] = [];

// accept POST primarily, fallback to GET (some places may link via GET)
$id = 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['menu_id'] ?? 0);
} else {
    // allow GET fallback when necessary
    $id = intval($_GET['menu_id'] ?? 0);
}

if ($id > 0 && isset($_SESSION['cart'][$id])) {
    unset($_SESSION['cart'][$id]);
    $_SESSION['pesan'] = 'Item dihapus dari keranjang.';
} else {
    $_SESSION['pesan'] = 'Item tidak ditemukan di keranjang.';
}

// normalize cart
if (empty($_SESSION['cart'])) $_SESSION['cart'] = [];

header('Location: transaksi_index.php');
exit;
?>
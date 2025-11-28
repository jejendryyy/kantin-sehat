<?php
// transaksi_tambah.php
session_start();
require_once __DIR__ . '/../koneksi.php';

if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) $_SESSION['cart'] = [];

// hanya terima POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: transaksi_index.php');
    exit;
}

$menu_id = intval($_POST['menu_id'] ?? 0);
if ($menu_id <= 0) {
    $_SESSION['pesan'] = 'Menu tidak valid.';
    header('Location: transaksi_index.php');
    exit;
}

// ambil data menu dari DB
$stmt = mysqli_prepare($connection, "SELECT id, nama_makanan, harga, stok FROM tb_menu WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, 'i', $menu_id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$m = mysqli_fetch_assoc($res);
mysqli_stmt_close($stmt);

if (!$m) {
    $_SESSION['pesan'] = 'Menu tidak ditemukan.';
    header('Location: transaksi_index.php');
    exit;
}

// tambahkan ke session cart (jika sudah ada +1)
if (isset($_SESSION['cart'][$menu_id])) {
    // jangan melebihi stok
    $newQty = $_SESSION['cart'][$menu_id]['qty'] + 1;
    $_SESSION['cart'][$menu_id]['qty'] = min($newQty, intval($m['stok']));
} else {
    $_SESSION['cart'][$menu_id] = [
        'id' => intval($m['id']),
        'nama' => $m['nama_makanan'],
        'harga' => floatval($m['harga']),
        'qty' => 1,
        'stok' => intval($m['stok'])
    ];
}

header('Location: transaksi_index.php');
exit;

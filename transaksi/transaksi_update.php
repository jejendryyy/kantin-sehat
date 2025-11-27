<?php
// transaksi_update.php
session_start();
if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) $_SESSION['cart'] = [];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: transaksi_index.php');
    exit;
}

// dukung dua mode: update array qty, atau aksi khusus (mis. "tambah" bila mau)
if (isset($_POST['qty']) && is_array($_POST['qty'])) {
    $changed = 0;
    foreach ($_POST['qty'] as $rawId => $rawJumlah) {
        $id = intval($rawId);
        $jumlah = intval($rawJumlah);
        if ($id <= 0) continue;

        // make sure array key exists before editing
        if (!isset($_SESSION['cart'][$id])) continue;

        // remove if quantity is <= 0 or empty
        if ($jumlah <= 0) {
            unset($_SESSION['cart'][$id]);
            $changed++;
            continue;
        }

        // limit to available stok if present
        $stok = intval($_SESSION['cart'][$id]['stok'] ?? 999999);
        $newQty = min($jumlah, max(1, $stok));
        if ($_SESSION['cart'][$id]['qty'] !== $newQty) {
            $_SESSION['cart'][$id]['qty'] = $newQty;
            $changed++;
        }
    }

    // Set a helpful message to show on index
    if ($changed > 0) {
        $_SESSION['pesan'] = 'Keranjang diperbarui.';
    } else {
        $_SESSION['pesan'] = 'Tidak ada perubahan pada keranjang.';
    }
}

// If cart is now empty, keep it as an empty array
if (empty($_SESSION['cart'])) $_SESSION['cart'] = [];

header('Location: transaksi_index.php');
exit;

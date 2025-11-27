<?php

//include koneksi database
require_once __DIR__ . '/../koneksi.php';
session_start();

//get data dari form
// sanitize input
$tipe_kategori = isset($_POST['tipe_kategori']) ? trim($_POST['tipe_kategori']) : '';
$deskripsi = isset($_POST['deskripsi']) ? trim($_POST['deskripsi']) : '';

if ($tipe_kategori === '') {
    $_SESSION['pesan'] = 'Tipe kategori wajib diisi.';
    header('Location: kategori_tambah.php'); exit;
}

// insert using prepared stmt
$stmt = mysqli_prepare($connection, "INSERT INTO tb_kategori (tipe_kategori, deskripsi, created_at) VALUES (?, ?, NOW())");
mysqli_stmt_bind_param($stmt, 'ss', $tipe_kategori, $deskripsi);
if (mysqli_stmt_execute($stmt)) {
    $_SESSION['pesan'] = 'Kategori berhasil ditambahkan.';
    mysqli_stmt_close($stmt);
    header('Location: kategori_index.php'); exit;
} else {
    $err = mysqli_error($connection);
    mysqli_stmt_close($stmt);
    echo 'Data Gagal Disimpan: ' . htmlspecialchars($err);
}
?>
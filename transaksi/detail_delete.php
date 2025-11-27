<?php
// transaksi/detail_delete.php
session_start();
require_once __DIR__ . '/../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: transaksi_index.php');
    exit;
}
$id = intval($_POST['id'] ?? 0);
if ($id <= 0) {
    $_SESSION['pesan'] = 'ID transaksi tidak valid.';
    header('Location: transaksi_index.php');
    exit;
}

mysqli_begin_transaction($connection);
try {
    // Hapus detail (jika FK cascade sudah ada, perintah ini tetap aman)
    $stmt = mysqli_prepare($connection, "DELETE FROM tb_detail_transaksi WHERE id_transaksi = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Hapus header
    $stmt2 = mysqli_prepare($connection, "DELETE FROM tb_transaksi WHERE id = ?");
    mysqli_stmt_bind_param($stmt2, 'i', $id);
    mysqli_stmt_execute($stmt2);
    $affected = mysqli_stmt_affected_rows($stmt2);
    mysqli_stmt_close($stmt2);

    if ($affected <= 0) throw new Exception('Transaksi tidak ditemukan / gagal dihapus.');

    mysqli_commit($connection);
    $_SESSION['pesan'] = 'Transaksi dan detail berhasil dihapus.';
} catch (Exception $ex) {
    mysqli_rollback($connection);
    $_SESSION['pesan'] = 'Gagal menghapus transaksi: ' . $ex->getMessage();
}
header('Location: detail_list.php');
exit;

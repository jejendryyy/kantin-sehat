<?php
session_start();
require_once __DIR__ . '/../koneksi.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    $_SESSION['pesan'] = 'ID tidak valid.';
    header('Location: kategori_index.php'); exit;
}

$stmt = mysqli_prepare($connection, "DELETE FROM tb_kategori WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, 'i', $id);
if (mysqli_stmt_execute($stmt)) {
    $_SESSION['pesan'] = 'Kategori berhasil dihapus.';
    mysqli_stmt_close($stmt);
    header('Location: kategori_index.php'); exit;
} else {
    $err = mysqli_error($connection);
    mysqli_stmt_close($stmt);
    echo 'Data Gagal dihapus: ' . htmlspecialchars($err);
}
?>
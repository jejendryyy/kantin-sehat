<?php
session_start();
require_once __DIR__ . '/../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: kategori_index.php'); exit;
}

$id = intval($_POST['id'] ?? 0);
$tipe_kategori = isset($_POST['tipe_kategori']) ? trim($_POST['tipe_kategori']) : '';
$deskripsi = isset($_POST['deskripsi']) ? trim($_POST['deskripsi']) : '';

// make deskripsi optional and give default placeholder when empty
if ($deskripsi === '') $deskripsi = '-';

if ($id <= 0 || $tipe_kategori === '') {
    // minimal validation failure
    $_SESSION['pesan'] = 'Nama kategori wajib diisi.';
    header('Location: kategori_edit.php?id=' . $id); exit;
}

// check for duplicate tipe_kategori (exclude current id)
$chk = mysqli_prepare($connection, "SELECT id FROM tb_kategori WHERE tipe_kategori = ? AND id != ? LIMIT 1");
if ($chk) {
    mysqli_stmt_bind_param($chk, 'si', $tipe_kategori, $id);
    mysqli_stmt_execute($chk);
    mysqli_stmt_store_result($chk);
    if (mysqli_stmt_num_rows($chk) > 0) {
        mysqli_stmt_close($chk);
        $_SESSION['pesan'] = 'Nama kategori sudah terdaftar.';
        header('Location: kategori_edit.php?id=' . $id);
        exit;
    }
    mysqli_stmt_close($chk);
}

$stmt = mysqli_prepare($connection, "UPDATE tb_kategori SET tipe_kategori = ?, deskripsi = ? WHERE id = ? LIMIT 1");
if (!$stmt) {
    $_SESSION['pesan'] = 'Gagal menyiapkan query: ' . mysqli_error($connection);
    header('Location: kategori_index.php'); exit;
}
mysqli_stmt_bind_param($stmt, 'ssi', $tipe_kategori, $deskripsi, $id);
if (mysqli_stmt_execute($stmt)) {
    $_SESSION['pesan'] = 'Kategori berhasil diperbarui.';
} else {
    $_SESSION['pesan'] = 'Gagal memperbarui kategori: ' . mysqli_error($connection);
}
mysqli_stmt_close($stmt);

header('Location: kategori_index.php'); exit;

?>

<?php
// menu/menu_update.php
require_once __DIR__ . '/../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: menu_index.php');
    exit;
}

// ambil dan sanitasi input
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$nama_makanan = isset($_POST['nama_makanan']) ? trim($_POST['nama_makanan']) : '';
$id_kategori = isset($_POST['id_kategori']) ? intval($_POST['id_kategori']) : 0;
$harga = isset($_POST['harga']) ? floatval($_POST['harga']) : 0;
$stok = isset($_POST['stok']) ? intval($_POST['stok']) : 0;

// validasi sederhana
$errors = [];
if ($id <= 0) $errors[] = 'ID menu tidak valid.';
if ($nama_makanan === '') $errors[] = 'Nama menu wajib diisi.';
if ($id_kategori <= 0) $errors[] = 'Pilih kategori.';
if ($harga < 0) $errors[] = 'Harga tidak valid.';
if ($stok < 0) $errors[] = 'Stok tidak valid.';

if (!empty($errors)) {
    
    echo '<h3>Terjadi kesalahan:</h3><ul>';
    foreach ($errors as $e) echo '<li>' . htmlspecialchars($e) . '</li>';
    echo '</ul><p><a href="menu_edit.php?id=' . intval($id) . '">Kembali ke edit</a></p>';
    exit;
}

// pastikan kategori yang dipilih ada (prevent foreign key mismatch)
$stmtChk = mysqli_prepare($connection, "SELECT id FROM tb_kategori WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($stmtChk, 'i', $id_kategori);
mysqli_stmt_execute($stmtChk);
mysqli_stmt_store_result($stmtChk);
if (mysqli_stmt_num_rows($stmtChk) === 0) {
    mysqli_stmt_close($stmtChk);
    echo 'Kategori yang dipilih tidak ditemukan. <a href="menu_edit.php?id=' . intval($id) . '">Kembali</a>';
    exit;
}
mysqli_stmt_close($stmtChk);

// update pake prepared statement
$stmt = mysqli_prepare($connection, "UPDATE tb_menu SET nama_makanan = ?, id_kategori = ?, harga = ?, stok = ? WHERE id = ?");
if (!$stmt) {
    die('Prepare gagal: ' . mysqli_error($connection));
}
mysqli_stmt_bind_param($stmt, 'sidii', $nama_makanan, $id_kategori, $harga, $stok, $id);
$ok = mysqli_stmt_execute($stmt);
if ($ok) {
    mysqli_stmt_close($stmt);
    header('Location: menu_index.php');
    exit;
} else {
    $err = mysqli_stmt_error($stmt);
    mysqli_stmt_close($stmt);
    echo 'Gagal mengupdate data: ' . htmlspecialchars($err) . '. <a href="menu_edit.php?id=' . intval($id) . '">Kembali</a>';
    exit;
}

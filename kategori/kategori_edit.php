<?php
// menu/menu_edit.php
session_start();
require_once __DIR__ . '/../koneksi.php';

// ambil id dari query string
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
  header('Location: kategori_index.php');
  exit;
}

// ambil data kategori dari tb_kategori
$stmt = mysqli_prepare($connection, "SELECT id, tipe_kategori, deskripsi FROM tb_kategori WHERE id = ? LIMIT 1");
if (!$stmt) {
  die('Query gagal: ' . mysqli_error($connection));
}
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$kategori = mysqli_fetch_assoc($res);
mysqli_stmt_close($stmt);

if (!$kategori) {
  // jika tidak ditemukan, kembali ke index
  header('Location: kategori_index.php');
  exit;
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Edit Kategori — Kantin Sehat</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  </head>
<body>
  <div class="navbar"><p>Kantin Sehat</p></div>
  <div class="container">
    <div class="card">
      <div class="card-body">
    <h2 style="margin:0 0 8px 0">Edit Kategori</h2>

    <?php if (!empty($_SESSION['pesan'])): ?>
      <div class="error"><?= htmlspecialchars($_SESSION['pesan']) ?></div>
      <?php unset($_SESSION['pesan']); endif; ?>

    <form method="post" action="kategori_update.php" aria-label="Form edit kategori">
      <input type="hidden" name="id" value="<?= htmlspecialchars($kategori['id']) ?>">

      <div class="full">
        <label for="tipe_kategori">Nama Kategori</label>
        <input id="tipe_kategori" name="tipe_kategori" type="text" required value="<?= htmlspecialchars($kategori['tipe_kategori']) ?>" placeholder="Contoh: Makanan Ringan">
      </div>

      <div class="full">
        <label for="deskripsi">Deskripsi (opsional)</label>
        <textarea id="deskripsi" name="deskripsi" rows="3" placeholder="Keterangan singkat tentang kategori"><?= htmlspecialchars($kategori['deskripsi'] === '-' ? '' : $kategori['deskripsi']) ?></textarea>
      </div>

        <div class="form-actions">
          <a class="btn btn-back" href="kategori_index.php">Batal</a>
          <button type="reset" class="btn btn-reset">Reset</button>
          <button type="submit" class="btn btn-save">Simpan Perubahan</button>
        </div>
      </form>
        </div>
      </div>
    </div>
  </body>
  </html>

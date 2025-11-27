<?php
// menu/menu_edit.php
require_once __DIR__ . '/../koneksi.php';

// ambil id dari query string
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    header('Location: menu_index.php');
    exit;
}

// ambil data menu
$stmt = mysqli_prepare($connection, "SELECT id, nama_makanan, id_kategori, harga, stok FROM tb_menu WHERE id = ? LIMIT 1");
if (!$stmt) {
    die('Query gagal: ' . mysqli_error($connection));
}
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$menu = mysqli_fetch_assoc($res);
mysqli_stmt_close($stmt);

if (!$menu) {
    // jika tidak ditemukan, kembali ke index
    header('Location: menu_index.php');
    exit;
}

// ambil daftar kategori untuk dropdown
$kategori_q = mysqli_query($connection, "SELECT id, tipe_kategori FROM tb_kategori ORDER BY tipe_kategori ASC");
$kategori_list = [];
while ($r = mysqli_fetch_assoc($kategori_q)) $kategori_list[] = $r;
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Edit Menu — Kantin Sehat</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
  <div class="navbar"><p>Kantin Sehat</p></div>
  <div class="container">
    <div class="card">
      <div class="card-body">
        <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:8px">
          <h2 style="margin:0">Edit Menu</h2>
          <a class="btn btn-back" href="menu_index.php" style="text-decoration:none">Kembali</a>
        </div>

        <form method="post" action="menu_update.php">
      <input type="hidden" name="id" value="<?= htmlspecialchars($menu['id']) ?>">

      <label for="nama_makanan">Nama Menu</label>
      <input id="nama_makanan" name="nama_makanan" type="text" required
             value="<?= htmlspecialchars($menu['nama_makanan']) ?>">

      <label for="id_kategori">Kategori</label>
      <select id="id_kategori" name="id_kategori" required>
        <option value="">-- Pilih Kategori --</option>
        <?php foreach ($kategori_list as $k): ?>
          <option value="<?= intval($k['id']) ?>"
            <?= (intval($k['id']) === intval($menu['id_kategori'])) ? 'selected' : '' ?>>
            <?= htmlspecialchars($k['tipe_kategori']) ?>
          </option>
        <?php endforeach; ?>
      </select>

      <label for="harga">Harga</label>
      <input id="harga" name="harga" type="number" step="0.01" min="0" required
             value="<?= htmlspecialchars($menu['harga']) ?>">

      <label for="stok">Stok</label>
      <input id="stok" name="stok" type="number" min="0" required
             value="<?= htmlspecialchars($menu['stok']) ?>">

      <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:8px">
        <a class="btn btn-back" href="menu_index.php" style="text-decoration:none">Batal</a>
        <button type="reset" class="btn btn-reset">Reset</button>
        <button type="submit" class="btn btn-save">Simpan Perubahan</button>
      </div>

    </form>
      </div>
    </div>
  </div>
</body>
</html>

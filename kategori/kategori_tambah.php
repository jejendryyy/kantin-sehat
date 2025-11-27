<?php
// kategori/kategori_tambah.php
require_once __DIR__ . '/../koneksi.php';

$errors = [];
$old = ['tipe_kategori' => '', 'deskripsi' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old['tipe_kategori'] = trim($_POST['tipe_kategori'] ?? '');
    $old['deskripsi'] = trim($_POST['deskripsi'] ?? '');

    if ($old['deskripsi'] === '') {
        $old['deskripsi'] = '-';
    }

    // Validasi
    if ($old['tipe_kategori'] === '') {
        $errors[] = 'Nama kategori wajib diisi.';
    }

    // Cek duplikat
    if (empty($errors)) {
        $stmt = mysqli_prepare($connection, "SELECT id FROM tb_kategori WHERE tipe_kategori = ? LIMIT 1");
        if ($stmt === false) {
            $errors[] = 'Query gagal: ' . mysqli_error($connection);
        } else {
            mysqli_stmt_bind_param($stmt, 's', $old['tipe_kategori']);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            if (mysqli_stmt_num_rows($stmt) > 0) {
                $errors[] = 'Kategori sudah terdaftar.';
            }
            mysqli_stmt_close($stmt);
        }
    }

    // Insert
    if (empty($errors)) {
        $ins = mysqli_prepare($connection, "INSERT INTO tb_kategori (tipe_kategori, deskripsi, created_at) VALUES (?, ?, NOW())");
        if ($ins === false) {
            $errors[] = 'Prepare insert gagal: ' . mysqli_error($connection);
        } else {
            mysqli_stmt_bind_param($ins, 'ss', $old['tipe_kategori'], $old['deskripsi']);
            $ok = mysqli_stmt_execute($ins);
            if ($ok) {
                mysqli_stmt_close($ins);
                header('Location: kategori_index.php?success=' . urlencode('Kategori berhasil ditambahkan'));
                exit;
            } else {
                $errors[] = 'Gagal menyimpan: ' . mysqli_stmt_error($ins);
                mysqli_stmt_close($ins);
            }
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Tambah Kategori — Kantin Sehat</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    :root{ --bg:#f5f7fb; --card:#fff; --accent:#2f8f72; --muted:#6b7280 }
    *{box-sizing:border-box}
    body{font-family:Inter, system-ui, -apple-system, 'Segoe UI', Roboto, Arial; margin:0; background:var(--bg); color:#111}

    .navbar{background:linear-gradient(90deg,var(--accent),#4da98a); color:#fff; padding:18px 22px; text-align:center; box-shadow:0 2px 8px rgba(0,0,0,0.08)}
    .navbar p{margin:0;font-weight:700;font-size:20px}

    .container{max-width:760px;margin:32px auto;padding:0 16px}
    .card{background:var(--card); border-radius:12px; box-shadow:0 12px 40px rgba(20,30,40,0.06); overflow:hidden}
    .card-body{padding:22px}

    label{display:block;margin-bottom:6px;font-weight:600;color:#0f1720}
    input, textarea, select{width:100%;padding:10px 12px;border-radius:10px;border:1px solid rgba(0,0,0,0.07);background:linear-gradient(180deg,#fff,#fbfbfd);box-shadow:0 8px 24px rgba(14,20,25,0.03);font-size:14px;color:#111;margin-bottom: 15px;}

    .row{display:flex;gap:10px}
    .col{flex:1}
    .full{display:block;width:100%}
    .form-actions{display:flex;gap:8px;justify-content:flex-end;margin-top:12px}
    .btn{display:inline-flex;align-items:center;gap:8px;padding:8px 12px;border-radius:10px;border:0;cursor:pointer;font-weight:600}
    .btn-save{background:var(--accent); color:#fff}
    .btn-reset{background:linear-gradient(90deg,#ffe7b5,#ffd3a6); color:#8a5a25;border:1px solid rgba(0,0,0,0.03)}
    .btn-cancel{background:transparent;border:1px solid rgba(0,0,0,0.06); color:#333; text-decoration: none;}

    .error{background:#fff4f4;border-left:4px solid #f44336;padding:10px;color:#900;border-radius:6px}
    .hint{font-size:13px;color:#666;margin-top:6px}
  </style>
</head>
<body>
  <div class="navbar"><p>Kantin Sehat</p></div>

  <div class="container">
    <div class="card">
      <div class="card-body">
        <h2 style="margin:0 0 6px 0">Tambah Kategori</h2>

    <?php if (!empty($errors)): ?>
      <div class="error">
        <?php foreach ($errors as $e) echo htmlspecialchars($e) . '<br>'; ?>
      </div>
    <?php endif; ?>

    <form method="post" action="" aria-label="Form tambah kategori">
      <div class="full">
        <label for="tipe_kategori">Nama Kategori</label>
        <input id="tipe_kategori" name="tipe_kategori" type="text" required value="<?= htmlspecialchars($old['tipe_kategori']) ?>" placeholder="Contoh: Makanan Ringan">
      </div>

      <div class="full">
        <label for="deskripsi">Deskripsi (opsional)</label>
        <textarea id="deskripsi" name="deskripsi" rows="3" placeholder="Keterangan singkat tentang kategori"><?= htmlspecialchars($old['deskripsi']) ?></textarea>
      </div>

      <div class="form-actions">
        <a class="btn btn-cancel" href="kategori_index.php">Batal</a>
        <button type="reset" class="btn btn-reset">Reset</button>
        <button type="submit" class="btn btn-save">Simpan</button>
      </div>
    </form>
      </div>
    </div>
  </div>
</body>
</html>

<?php
require_once __DIR__ . '/../koneksi.php';

// Ambil kategori dari database
$kategori_q = mysqli_query($connection, "SELECT id, tipe_kategori FROM tb_kategori ORDER BY tipe_kategori ASC");
$kategori_list = [];
while ($row = mysqli_fetch_assoc($kategori_q)) {
    $kategori_list[] = $row;
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Tambah Menu — Kantin Sehat</title>
        <link rel="stylesheet" href="../assets/css/style.css">
        <style>
            :root{ --bg:#f5f7fb; --card:#fff; --accent:#2f8f72; --muted:#6b7280 }
            *{box-sizing:border-box}
            body{font-family:Inter, system-ui, -apple-system, 'Segoe UI', Roboto, Arial; margin:0; background:var(--bg); color:#111}

            .navbar{background:linear-gradient(90deg,var(--accent),#4da98a); color:#fff; padding:18px 22px; text-align:center; box-shadow:0 2px 8px rgba(0,0,0,0.08)}
            .navbar p{margin:0;font-weight:700;font-size:20px}

            .container{max-width:920px;margin:32px auto;padding:0 16px}
            .card{background:var(--card); border-radius:12px; box-shadow:0 12px 40px rgba(20,30,40,0.06); overflow:hidden}
            .card-body{padding:22px}

            form{display:grid;grid-template-columns:1fr 1fr;gap:12px;align-items:start}
            form .full{grid-column:1 / -1}

            label{display:block;margin-bottom:6px;font-weight:600;color:#0f1720}
            input[type=text], input[type=number], textarea, select{width:100%;padding:10px 12px;border-radius:10px;border:1px solid rgba(0,0,0,0.07);background:linear-gradient(180deg,#fff,#fbfbfd);box-shadow:0 6px 18px rgba(14,20,25,0.03);font-size:14px;color:#111}
            input::placeholder, textarea::placeholder{color:#9aa0a6}

            .form-actions{grid-column:1 / -1;display:flex;gap:8px;align-items:center;justify-content:flex-end;margin-top:6px}
            .btn{display:inline-flex;align-items:center;gap:8px;padding:8px 12px;border-radius:10px;border:0;cursor:pointer;font-weight:600}
            .btn-save{background:var(--accent); color:#fff}
            .btn-reset{background:linear-gradient(90deg,#ffe7b5,#ffd3a6); color:#8a5a25;border:1px solid rgba(0,0,0,0.03)}
            .btn-back{background:transparent;border:1px solid rgba(0,0,0,0.06); color:#333;padding:8px 10px;border-radius:10px;text-decoration:none}

            @media (max-width:760px){ form{grid-template-columns:1fr} .form-actions{justify-content:center} .container{margin:18px 12px} }
        </style>
</head>


<body>

    <div class="navbar"><p>Kantin Sehat</p></div>

    <div class="container">
        <div class="card">
            <div class="card-body">
                <h3 style="margin:0 0 8px 0">Tambah Menu</h3>

                <form action="menu_simpan.php" method="POST" aria-label="Form tambah menu">

                    <div class="full">
                        <label for="nama">Nama Menu</label>
                        <input id="nama" type="text" name="nama_makanan" placeholder="Masukkan nama makanan" required>
                    </div>

                    <div>
                        <label for="kategori">Kategori</label>
                        <select id="kategori" name="id_kategori" required>
                            <option value="">-- Pilih Kategori --</option>
                            <?php foreach ($kategori_list as $k): ?>
                                <option value="<?= $k['id'] ?>"><?= htmlspecialchars($k['tipe_kategori']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label for="harga">Harga</label>
                        <input id="harga" type="number" name="harga" placeholder="Masukkan harga" required>
                    </div>

                    <div>
                        <label for="stok">Stok</label>
                        <input id="stok" type="number" name="stok" placeholder="Masukkan stok menu" required>
                    </div>

                    <div class="form-actions full">
                        <a class="btn-back" href="menu_index.php">Batal</a>
                        <button type="reset" class="btn btn-reset">Reset</button>
                        <button type="submit" class="btn btn-save">Simpan</button>
                    </div>

                </form>
            </div>
        </div>
    </div>

</body>
</html>

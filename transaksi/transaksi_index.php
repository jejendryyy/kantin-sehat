<?php
session_start();
require_once __DIR__ . '/../koneksi.php';

function rupiah($n){ return 'Rp ' . number_format($n,0,',','.'); }

if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Ambil daftar menu
$menu_list = [];
$q = mysqli_query($connection, "SELECT id, nama_makanan, harga, stok FROM tb_menu ORDER BY nama_makanan ASC");
while ($r = mysqli_fetch_assoc($q)) $menu_list[] = $r;

// Hitung subtotal
$subtotal = 0;
foreach ($_SESSION['cart'] as $item){
    $subtotal += $item['harga'] * $item['qty'];
}


// Notifikasi session
$pesan = $_SESSION['pesan'] ?? '';
unset($_SESSION['pesan']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Transaksi Baru</title>
    <link rel="stylesheet" href="../assets/css/style.css">
<style>
    :root{ --bg:#f5f7fb; --card:#fff; --accent:#2f8f72; --muted:#6b7280; --danger:#dc4f4f }
    *{box-sizing:border-box}
    body{font-family:Inter, system-ui, -apple-system, 'Segoe UI', Roboto, Arial; margin:0; background:var(--bg); color:#111}

    .navbar{background:linear-gradient(90deg,var(--accent),#4da98a); color:#fff; padding:18px 22px; text-align:center; box-shadow:0 2px 8px rgba(0,0,0,0.08)}
    .navbar p{margin:0;font-weight:700;font-size:18px}

    .container{max-width:1100px;margin:28px auto;padding:0 16px}
    .card{background:var(--card); border-radius:10px; box-shadow:0 10px 30px rgba(26,32,36,0.06); overflow:hidden}
    .card-body{padding:18px}

    h2{margin-top:0;color:#0f1720}
    .table{width:100%;border-collapse:collapse;margin-top:10px}
    .table th,.table td{padding:10px;border-bottom:1px solid rgba(0,0,0,0.06);font-size:14px}
    .table th{background:#fbfdfe;text-align:left}

    /* buttons */
    .btn{padding:8px 12px;border-radius:8px;border:0;cursor:pointer;font-size:14px;display:inline-flex;align-items:center;gap:8px}
    .btn-add{background:var(--accent);color:white}
    .btn-update{background:#10843a;color:white}
    .btn-delete{background:transparent;border:1px solid rgba(0,0,0,0.06);color:var(--danger)}
    .btn-save{background:var(--accent);color:white;width:100%;margin-top:12px;padding:12px;font-size:16px;border-radius:10px}
    .input-small{width:70px;padding:6px;border-radius:6px;border:1px solid rgba(0,0,0,0.06)}
    /* larger payment input for uang tunai */
    .input-money{width:220px;padding:10px;border-radius:8px;border:1px solid rgba(0,0,0,0.08);font-size:16px;background:linear-gradient(180deg,#fff,#fbfbfd);box-shadow:0 6px 18px rgba(14,20,25,0.03)}

    .payment-box{background:linear-gradient(180deg,#fcfff8,#f7fffb);border:1px solid rgba(47,143,114,0.06);padding:15px;border-radius:8px;margin-top:15px}
    .notice{padding:12px;margin-bottom:15px;border-radius:8px;background:#fffbea;border:1px solid rgba(255,196,0,0.25);color:#715800}
    .notice.success{background:linear-gradient(180deg,#f0fbf6,#f9fff8);border:1px solid rgba(47,143,114,0.12);color:#0f5132}

    .summary{display:flex;gap:12px;align-items:center;justify-content:space-between;margin-top:10px}
    .summary b{font-size:18px}

    @media (max-width:800px){.container{margin:18px auto;padding:0 12px}}
</style>
</head>
<body>
<div class="navbar"><p>Kantin Sehat</p></div>
<div class="container">

<div style="display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:8px">
    <h2 style="margin:0">Transaksi Kasir</h2>
    <div>
        <a href="../index.php" class="btn btn-outline" style="text-decoration:none">Kembali ke Menu</a>
    </div>
</div>

<?php if ($pesan):
    $isSuccess = stripos($pesan, 'Transaksi berhasil') !== false || stripos($pesan, 'Kembalian') !== false;
?>
    <div class="notice<?= $isSuccess ? ' success' : '' ?>"><?= htmlspecialchars($pesan) ?></div>
<?php endif; ?>

<h3>Daftar Menu</h3>
<table class="table">
<tr>
    <th>Menu</th>
    <th>Harga</th>
    <th>Stok</th>
    <th>Aksi</th>
</tr>
<?php foreach($menu_list as $m): ?>
<tr>
    <td><?= htmlspecialchars($m['nama_makanan']) ?></td>
    <td><?= rupiah($m['harga']) ?></td>
    <td><?= $m['stok'] ?></td>
    <td>
        <form action="transaksi_tambah.php" method="POST">
            <input type="hidden" name="aksi" value="tambah">
            <input type="hidden" name="menu_id" value="<?= $m['id'] ?>">
            <button class="btn btn-add" type="submit">Tambah</button>
        </form>
    </td>
</tr>
<?php endforeach; ?>
</table>

<hr>

<h3>Keranjang Belanja</h3>

<?php if (empty($_SESSION['cart'])): ?>
    <p><i>Keranjang masih kosong.</i></p>
<?php else: ?>

<form action="transaksi_update.php" method="POST">
<input type="hidden" name="aksi" value="update_qty">

<table class="table">
<tr>
    <th>Menu</th>
    <th>Qty</th>
    <th>Harga</th>
    <th>Subtotal</th>
    <th>Aksi</th>
</tr>

<?php foreach($_SESSION['cart'] as $item): ?>
<tr>
    <td><?= $item['nama'] ?></td>
    <td><input type="number" class="input-small" name="qty[<?= $item['id'] ?>]" value="<?= $item['qty'] ?>" min="1"></td>
    <td><?= rupiah($item['harga']) ?></td>
    <td><?= rupiah($item['harga'] * $item['qty']) ?></td>
    <td>
  <a class="btn btn-delete" 
     href="transaksi_hapus.php?menu_id=<?= intval($item['id']) ?>"
     onclick="return confirm('Yakin ingin menghapus <?= htmlspecialchars($item['nama']) ?> dari keranjang?')"
     style="text-decoration:none;display:inline-block;padding:6px 10px;border-radius:8px;border:1px solid rgba(0,0,0,0.06);color:var(--danger);background:transparent;">
    Hapus
  </a>
</td>

</tr>
<?php endforeach; ?>

</table>

<button type="submit" class="btn btn-update">Update Jumlah</button>
</form>

<?php endif; ?>

<h3>Total Pembayaran</h3>
<div class="payment-box">
    <p>Total: <b><?= rupiah($subtotal) ?></b></p>

    <form action="transaksi_simpan.php" method="POST">
         <label for="pembayaran">Uang Tunai:</label><br>
         <input id="pembayaran" class="input-money" type="number" name="pembayaran" min="0" value="0" step="100" 
             style="margin-top:8px" aria-label="Uang Tunai, masukan jumlah pembayaran"><br>

        <button type="submit" class="btn-save">Simpan Transaksi</button>
    </form>
</div>

</div>
</body>
</html>

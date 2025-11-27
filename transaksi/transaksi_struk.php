<?php
// transaksi_struk.php
require_once __DIR__ . '/../koneksi.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    echo "ID transaksi tidak valid.";
    exit;
}

// ambil header transaksi
$stmt = mysqli_prepare($connection, "SELECT id, total, pembayaran, kembalian, created_at FROM tb_transaksi WHERE id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$trx = mysqli_fetch_assoc($res);
mysqli_stmt_close($stmt);

if (!$trx) {
    echo "Transaksi tidak ditemukan.";
    exit;
}

// ambil detail
$stmt = mysqli_prepare($connection, "SELECT id_menu, nama_menu, jumlah, harga_satuan, subtotal FROM tb_detail_transaksi WHERE id_transaksi = ? ORDER BY id ASC");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$det_res = mysqli_stmt_get_result($stmt);
$items = [];
while ($r = mysqli_fetch_assoc($det_res)) $items[] = $r;
mysqli_stmt_close($stmt);

// helper rupiah
function rupiah($n){ return "Rp " . number_format($n,0,',','.'); }
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Struk Transaksi #<?= htmlspecialchars($trx['id']) ?></title>
<meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="../assets/css/style.css">
<style>
body{font-family:Arial,Helvetica,sans-serif;max-width:480px;margin:20px auto;color:#222}
.header{ text-align:center; margin-bottom:8px }
.store-name{ font-size:18px; font-weight:700; color:#1f2d44 }
.small{ color:#666; font-size:13px }
.table{ width:100%; border-collapse:collapse; margin-top:10px }
.table th, .table td { text-align:left; padding:6px; border-bottom:1px dashed #ddd; font-size:14px }
.total-row{ font-weight:700; font-size:16px }
.print-btn{ display:inline-block; padding:8px 12px; background:#377dff;color:#fff;border-radius:8px;text-decoration:none;margin-top:12px }
@media print {
  .no-print{ display:none }
}
</style>
</head>
<body>

<div class="header">
  <div class="store-name">Kantin Sehat</div>
  <div class="small">Struk Transaksi</div>
  <div class="small">No: <?= htmlspecialchars($trx['id']) ?> — <?= htmlspecialchars($trx['created_at']) ?></div>
</div>

<table class="table">
  <thead>
    <tr><th>Menu</th><th style="text-align:right">Jumlah x Harga</th></tr>
  </thead>
  <tbody>
    <?php foreach ($items as $it): ?>
      <tr>
        <td><?= htmlspecialchars($it['nama_menu']) ?></td>
        <td style="text-align:right">
          <?= intval($it['jumlah']) ?> x <?= rupiah($it['harga_satuan']) ?> = <?= rupiah($it['subtotal']) ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
  <tfoot>
    <tr><td class="total-row">Total</td><td style="text-align:right" class="total-row"><?= rupiah($trx['total']) ?></td></tr>
    <tr><td>Pembayaran</td><td style="text-align:right"><?= rupiah($trx['pembayaran']) ?></td></tr>
    <tr><td>Kembalian</td><td style="text-align:right"><?= rupiah($trx['kembalian']) ?></td></tr>
  </tfoot>
</table>

<div style="text-align:center; margin-top:12px" class="no-print">
  <a href="#" onclick="window.print(); return false;" class="print-btn">Cetak / Print</a>
  <a href="transaksi_tambah.php" style="margin-left:8px; text-decoration:none">Kembali</a>
</div>

</body>
</html>

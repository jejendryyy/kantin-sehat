<?php
session_start();
require_once __DIR__ . '/../koneksi.php';

$q = mysqli_query($connection, "SELECT id, total, pembayaran, kembalian, created_at FROM tb_transaksi ORDER BY created_at DESC LIMIT 200");
function rupiah($n){ return "Rp " . number_format($n,0,',','.'); }
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Daftar Transaksi - Kantin Sehat</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="../assets/css/style.css">
<style>
  :root{ --bg:#f5f7fb; --card:#fff; --accent:#2f8f72; --muted:#6b7280; --danger:#dc4f4f }
  *{box-sizing:border-box}
  body{font-family:Inter,system-ui,-apple-system,'Segoe UI',Roboto,Arial;margin:0;background:var(--bg);color:#111}

  .navbar{background:linear-gradient(90deg,var(--accent),#4da98a); color:#fff; padding:18px 22px; text-align:center; box-shadow:0 2px 8px rgba(0,0,0,0.08)}
  .navbar p{margin:0;font-weight:700;font-size:18px}

  .container{max-width:1100px;margin:28px auto;padding:0 16px}
  .card{background:var(--card); border-radius:10px; box-shadow:0 10px 30px rgba(26,32,36,0.06); overflow:hidden}
  .card-body{padding:18px}

  h2{margin:0 0 14px;color:#0f1720}
  .table{width:100%;border-collapse:separate;border-spacing:0;margin-top:10px}
  .table th{background:#fbfdfe;color:#111;font-weight:700;padding:12px 14px;border-bottom:1px solid rgba(0,0,0,0.06);text-align:left}
  .table td{padding:12px 14px;border-bottom:1px solid rgba(0,0,0,0.06);color:var(--muted)}

  .toolbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:10px}
  .btn{display:inline-flex;align-items:center;gap:8px;padding:8px 12px;border-radius:8px;border:0;cursor:pointer}
  .btn-primary{background:var(--accent);color:#fff}
  .btn-outline{background:transparent;border:1px solid rgba(0,0,0,0.06);color:#333;text-decoration: none;}
  .btn-danger{background:transparent;border:1px solid rgba(220,70,70,0.14);color:var(--danger);padding:6px 10px;border-radius:8px}

  .link{color:var(--accent);text-decoration:none}

  @media (max-width:800px){.container{margin:18px 12px}}
</style>
</head>
<body>
  <div class="navbar"><p>Kantin Sehat</p></div>
  <div class="container">
    <div class="card"><div class="card-body">
      <div class="toolbar">
        <h2>Daftar Transaksi</h2>
        <div>
          <a class="btn btn-outline" href="../index.php">Kembali</a>
        </div>
      </div>

      <?php if (!empty(
        
        $_SESSION['pesan']
      )): ?>
        <div style="padding:12px;margin-bottom:12px;border-radius:8px;background:linear-gradient(180deg,#f0fbf6,#f9fff8);border:1px solid rgba(47,143,114,0.12);color:#0f5132">
          <?= htmlspecialchars($_SESSION['pesan']) ?>
        </div>
      <?php unset($_SESSION['pesan']); endif; ?>

      <table class="table">
        <thead>
          <tr><th>No</th><th>Tanggal</th><th>Total</th><th>Pembayaran</th><th>Kembalian</th><th>Aksi</th></tr>
        </thead>
        <tbody>
          <?php $no = 1; while ($r = mysqli_fetch_assoc($q)): ?>
            <tr>
              <td><?= $no++ ?></td>
              <td><?= htmlspecialchars($r['created_at']) ?></td>
              <td><?= rupiah($r['total']) ?></td>
              <td><?= rupiah($r['pembayaran']) ?></td>
              <td><?= rupiah($r['kembalian']) ?></td>
              <td>
                <div style="display:flex;gap:8px;align-items:center">
                  <a class="link" href="transaksi_struk.php?id=<?= intval($r['id']) ?>" target="_blank">Lihat / Cetak</a>
                  <form method="POST" action="detail_delete.php" onsubmit="return confirm('Yakin ingin menghapus transaksi ini?');" style="display:inline;margin:0">
                    <input type="hidden" name="id" value="<?= intval($r['id']) ?>">
                    <button class="btn-danger" type="submit">Hapus</button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>

    </div></div>
  </div>
</body>
</html>

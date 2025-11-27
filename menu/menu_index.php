<?php
require_once __DIR__ . '/../koneksi.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Menu - Kantin Sehat</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    :root{ --bg:#f5f7fb; --card:#fff; --accent:#2f8f72; --muted:#6b7280; --danger:#dc4f4f }
    *{box-sizing:border-box}
    body{font-family:Inter, system-ui, -apple-system, 'Segoe UI', Roboto, Arial; margin:0; background:var(--bg); color:#111}

    .navbar{background:linear-gradient(90deg,var(--accent),#4da98a); color:#fff; padding:18px 22px; text-align:center; box-shadow:0 2px 8px rgba(0,0,0,0.08)}
    .navbar p{margin:0;font-weight:700;font-size:20px}

    .container{max-width:1100px;margin:28px auto;padding:0 16px}
    .card{background:var(--card); border-radius:10px; box-shadow:0 10px 30px rgba(26,32,36,0.06); overflow:hidden}
    .card-body{padding:16px}

    /* Minimal table style */
    table{width:100%; border-collapse:separate; border-spacing:0; font-size:14px}
    thead th{background:#fbfdfe; color:#111; font-weight:700; padding:12px 14px; border-bottom:1px solid rgba(0,0,0,0.06); text-align:left}
    tbody td{padding:12px 14px; border-bottom:1px solid rgba(0,0,0,0.06); color:var(--muted)}
    tbody tr:hover{background:linear-gradient(90deg, rgba(47,143,114,0.03), rgba(77,169,138,0.02))}

    .actions a{ color:var(--accent); text-decoration:none; margin-right:8px}

     /* small action buttons */
    .small-btn{display:inline-flex;align-items:center;gap:8px;padding:6px 8px;border-radius:8px;font-size:13px;text-decoration:none;border:1px solid transparent;cursor:pointer}
    .small-btn svg{height:14px;width:14px;opacity:.9}
    .btn-edit{background:linear-gradient(90deg, rgba(47,143,114,0.06), rgba(47,143,114,0.02)); color:var(--accent); border:1px solid rgba(47,143,114,0.12)}
    .btn-delete{background:transparent; color:var(--danger); border:1px solid rgba(220,70,70,0.14); padding:6px 10px}
    .btn-back{padding:6px 10px; border-radius:8px; background:transparent; border:1px solid rgba(0,0,0,0.06); color:#333}
    .btn{background:var(--accent); color:#fff; border:0; padding:8px 12px; border-radius:8px; margin-right:8px; cursor:pointer}
    /* datatables small tweaks */
    .dataTables_wrapper .dataTables_paginate .paginate_button{background:transparent;border:1px solid rgba(0,0,0,0.06);border-radius:6px;padding:6px 8px}
    .dataTables_filter input{padding:6px 8px;border-radius:6px;border:1px solid rgba(0,0,0,0.08)}

    @media (max-width:800px){.container{margin:18px auto;padding:0 12px}}
  </style>
  <link rel="stylesheet" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
</head>
<body>
  <div class="navbar"><p>Kantin Sehat</p></div>
  <div class="container">
    <div class="card">
      <div class="card-body">
        <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:12px">
          <a href="menu_tambah.php" style="text-decoration:none"><button class="btn">Tambah Menu</button></a>
          <a class="small-btn btn-back" href="../index.php" style="text-decoration:none">Kembali</a>
        </div>

        <table id="myTable" style="margin-top:10px;">
        <thead>
          <tr>
            <th scope="col">NO</th>
            <th scope="col">NAMA MENU</th>
            <th scope="col">KATEGORI</th>
            <th scope="col">HARGA</th>
            <th scope="col">STOK</th>
            <th scope="col">AKSI</th>
          </tr>
        </thead>
        <tbody>
          <?php
          // Ambil menu beserta nama kategori (jika ada)
          $no = 1;
          $sql = "SELECT m.*, k.tipe_kategori
                  FROM tb_menu AS m
                  LEFT JOIN tb_kategori AS k ON m.id_kategori = k.id
                  ORDER BY m.id ASC";
          $query = mysqli_query($connection, $sql);
          if ($query === false) {
              echo '<tr><td colspan="6">Query gagal: ' . htmlspecialchars(mysqli_error($connection)) . '</td></tr>';
          } else {
              while ($row = mysqli_fetch_assoc($query)) {
                  // fallback: jika tipe_kategori NULL tampilkan ID
                  $kategori_display = $row['tipe_kategori'] !== null && $row['tipe_kategori'] !== '' 
                                      ? $row['tipe_kategori'] 
                                      : ('ID: ' . $row['id_kategori']);
          ?>
          <tr>
            <td><?= $no++; ?></td>
            <td><?= htmlspecialchars($row['nama_makanan']); ?></td>
            <td><?= htmlspecialchars($kategori_display); ?></td>
            <td>Rp <?= number_format($row['harga'], 0, ',', '.'); ?></td>
            <td><?= intval($row['stok']); ?></td>
            <td class="actions">
              <a class="small-btn btn-edit" href="menu_edit.php?id=<?= intval($row['id']); ?>" title="Edit menu">
                <svg viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M4 13.333v2.667h2.667L15.78 6.887l-2.667-2.667L4 13.333z" fill="currentColor"/></svg>
                Edit
              </a>
              <a class="small-btn btn-delete" href="menu_hapus.php?id=<?= intval($row['id']); ?>" onclick="return confirm('Yakin ingin menghapus menu ini?')" title="Hapus menu">
                <svg viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M6 7h8M7 7l.667 8a1 1 0 001 0l.667-8M13 7l-.667 8a1 1 0 01-1 0L10.667 7" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Hapus
              </a>
            </td>
          </tr>
          <?php
              } // end while
          }
          ?>
        </tbody>
        </table>

      </div>
    </div>
  </div>
  <!-- load full jQuery first, then DataTables, then bootstrap -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
  <script>
    $(function(){
      $('#myTable').DataTable({
        paging: true,
        pageLength: 10,
        lengthChange: false,
        ordering: true,
        autoWidth: false,
        language: { search: "Cari:", paginate: { previous: "Sebelumnya", next: "Berikutnya" } }
      });
    });
  </script>
</body>
</html>

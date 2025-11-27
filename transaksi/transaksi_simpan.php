<?php
// transaksi_simpan.php
session_start();
require_once __DIR__ . '/../koneksi.php';

function rupiah($n){ return 'Rp ' . number_format($n,0,',','.'); }

if (empty($_SESSION['cart'])) {
    $_SESSION['pesan'] = 'Keranjang kosong.';
    header('Location: transaksi_tambah.php');
    exit;
}

$pembayaran = floatval($_POST['pembayaran'] ?? 0);

// hitung total server-side
$total = 0;
foreach ($_SESSION['cart'] as $it) {
    $total += floatval($it['harga']) * intval($it['qty']);
}

if ($pembayaran < $total) {
    $_SESSION['pesan'] = 'Pembayaran kurang: ' . rupiah($total - $pembayaran);
    header('Location: transaksi_tambah.php');
    exit;
}

// Mulai transaksi DB
if (!mysqli_begin_transaction($connection)) {
    $_SESSION['pesan'] = 'Gagal memulai transaksi DB: ' . mysqli_error($connection);
    header('Location: transaksi_tambah.php'); exit;
}

try {
    $kembalian = $pembayaran - $total;

    // 1) Insert header transaksi
    $stmtHeader = mysqli_prepare($connection,
        "INSERT INTO tb_transaksi (total, pembayaran, kembalian, created_at)
         VALUES (?, ?, ?, NOW())"
    );
    if (!$stmtHeader) throw new Exception('Prepare header error: ' . mysqli_error($connection));

    mysqli_stmt_bind_param($stmtHeader, 'ddd', $total, $pembayaran, $kembalian);
    if (!mysqli_stmt_execute($stmtHeader)) {
        $err = mysqli_stmt_error($stmtHeader);
        mysqli_stmt_close($stmtHeader);
        throw new Exception('Execute header error: ' . $err);
    }
    $id_trx = mysqli_insert_id($connection);
    mysqli_stmt_close($stmtHeader);

    if (empty($id_trx) || $id_trx == 0) {
        throw new Exception('Insert header sukses tetapi insert_id kosong — periksa struktur tb_transaksi (AUTO_INCREMENT).');
    }

    // 2) Prepare detail & stok statements
    $stmtDetail = mysqli_prepare($connection,
        "INSERT INTO tb_detail_transaksi (id_transaksi, id_menu, nama_menu, jumlah, harga_satuan, subtotal, created_at)
         VALUES (?, ?, ?, ?, ?, ?, NOW())"
    );
    if (!$stmtDetail) throw new Exception('Prepare detail error: ' . mysqli_error($connection));

    $stmtUpdStok = mysqli_prepare($connection,
        "UPDATE tb_menu SET stok = stok - ? WHERE id = ?"
    );
    if (!$stmtUpdStok) throw new Exception('Prepare update stok error: ' . mysqli_error($connection));

    // 3) Loop items -> simpan detail & update stok
    foreach ($_SESSION['cart'] as $it) {
        $id_menu = intval($it['id']);
        $nama_menu = $it['nama'];
        $qty = intval($it['qty']);
        $harga = floatval($it['harga']);
        $subtotal_item = $qty * $harga;

        // cek stok terakhir di DB utk keamanan (opsional tapi disarankan)
        $qstok = mysqli_prepare($connection, "SELECT stok FROM tb_menu WHERE id = ? LIMIT 1");
        mysqli_stmt_bind_param($qstok, 'i', $id_menu);
        mysqli_stmt_execute($qstok);
        $resstok = mysqli_stmt_get_result($qstok);
        $r = mysqli_fetch_assoc($resstok);
        mysqli_stmt_close($qstok);
        if (!$r) throw new Exception("Menu id {$id_menu} tidak ditemukan (saat validasi stok).");
        if (intval($r['stok']) < $qty) throw new Exception("Stok tidak cukup untuk {$nama_menu}. Tersedia {$r['stok']}.");

        // insert detail
        mysqli_stmt_bind_param($stmtDetail, 'iisiid',
            $id_trx,
            $id_menu,
            $nama_menu,
            $qty,
            $harga,
            $subtotal_item
        );
        if (!mysqli_stmt_execute($stmtDetail)) {
            throw new Exception('Execute detail error: ' . mysqli_stmt_error($stmtDetail));
        }

        // update stok
        mysqli_stmt_bind_param($stmtUpdStok, 'ii', $qty, $id_menu);
        if (!mysqli_stmt_execute($stmtUpdStok)) {
            throw new Exception('Execute update stok error: ' . mysqli_stmt_error($stmtUpdStok));
        }
    }

    // tutup statement
    mysqli_stmt_close($stmtDetail);
    mysqli_stmt_close($stmtUpdStok);

    // commit
    mysqli_commit($connection);

    // kosongkan cart & redirect ke struk
    $_SESSION['cart'] = [];
    $_SESSION['pesan'] = 'Transaksi berhasil. Kembalian: ' . rupiah($kembalian);
    header('Location: transaksi_struk.php?id=' . intval($id_trx));
    exit;

} catch (Exception $ex) {
    // rollback + info error
    mysqli_rollback($connection);
    $_SESSION['pesan'] = 'Gagal menyimpan transaksi: ' . $ex->getMessage();
    header('Location: transaksi_tambah.php');
    exit;
}

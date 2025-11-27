<?php
 require_once __DIR__ . '/../koneksi.php';
$id = $_GET['id'];
$query = "DELETE FROM tb_menu WHERE id='$id'";
if ($connection->query($query)) {
    header("location: menu_index.php");
    exit;
} else {
    echo "Data Gagal dihapus: " . $connection->error;
}
?>
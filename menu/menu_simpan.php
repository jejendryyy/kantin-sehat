<?php

//include koneksi database
require_once __DIR__ . '/../koneksi.php';

//get data dari form
$nama_makanan           = $_POST['nama_makanan'];
$id_kategori = $_POST['id_kategori'];
$harga       = $_POST['harga'];
$stok       = $_POST['stok'];

//query insert data ke dalam database
$query = "INSERT INTO tb_menu (nama_makanan, id_kategori, harga, stok) VALUES ('$nama_makanan', '$id_kategori', '$harga', '$stok')";

//kondisi pengecekan apakah data berhasil dimasukkan atau tidak
if ($connection->query($query)) {
    //redirect ke halaman index.php 
    header("location: menu_index.php");
} else {

    //pesan error gagal insert data
    echo "Data Gagal Disimpan!";
}
?>
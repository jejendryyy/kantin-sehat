<?php
// transaksi_clear.php
session_start();
$_SESSION['cart'] = [];
header('Location: transaksi_index.php');
exit;

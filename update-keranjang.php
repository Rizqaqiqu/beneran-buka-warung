<?php
session_start();

$id = $_POST['product_id'];
$jumlah = $_POST['jumlah'];

if (isset($_SESSION['keranjang'][$id])) {
    $_SESSION['keranjang'][$id] = $jumlah;
}
?>

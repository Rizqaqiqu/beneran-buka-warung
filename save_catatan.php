<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_transaksi']) && isset($_POST['catatan'])) {
    $id_transaksi = mysqli_real_escape_string($conn, $_POST['id_transaksi']);
    $catatan = mysqli_real_escape_string($conn, $_POST['catatan']);

    $query = mysqli_query($conn, "UPDATE tb_pembelian SET catatan = '$catatan' WHERE id_transaksi = '$id_transaksi'");
    if ($query) {
        echo 'success';
    } else {
        echo 'error';
    }
} else {
    echo 'error';
}
?>

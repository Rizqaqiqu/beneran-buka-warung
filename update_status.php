<?php
session_start();
include 'db.php';

if ($_SESSION['status_login'] != true) {
    echo 'unauthorized';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_transaksi = mysqli_real_escape_string($conn, $_POST['id_transaksi']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    $query = "UPDATE tb_pembelian SET status = '$status' WHERE id_transaksi = '$id_transaksi'";
    if (mysqli_query($conn, $query)) {
        echo 'success';
    } else {
        echo 'error';
    }
} else {
    echo 'invalid';
}
?>

<?php
include 'db.php';

if (isset($_GET['id_transaksi'])) {
    $id_transaksi = mysqli_real_escape_string($conn, $_GET['id_transaksi']);
    $query = mysqli_query($conn, "SELECT catatan FROM tb_pembelian WHERE id_transaksi = '$id_transaksi' LIMIT 1");
    if ($query && $row = mysqli_fetch_assoc($query)) {
        echo htmlspecialchars($row['catatan'] ?? '');
    } else {
        echo '';
    }
} else {
    echo '';
}
?>

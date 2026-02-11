<?php
session_start();

// Pastikan ada product_id yang dikirim
if (isset($_POST['product_id'])) {
    $id = $_POST['product_id'];

    // Jika session keranjang ada
    if (isset($_SESSION['keranjang'][$id])) {
        unset($_SESSION['keranjang'][$id]); // Hapus produk dari keranjang

        // Jika keranjang kosong setelah dihapus, hapus session keranjang
        if (empty($_SESSION['keranjang'])) {
            unset($_SESSION['keranjang']);
        }
    }
}

// Setelah dihapus, arahkan kembali ke halaman keranjang
header("Location: keranjang.php");
exit;
?>

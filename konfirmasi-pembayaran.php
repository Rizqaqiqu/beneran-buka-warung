<?php
session_start();
include 'db.php';

if (isset($_POST['submit'])) {
    $user_id = $_SESSION['user_id'] ?? 0;
    $product_id = $_POST['product_id'];
    $jumlah = $_POST['jumlah'];
    $total = $_POST['total'];
    $metode = $_POST['metode'];

    // Simpan ke tb_checkout
    $insert = mysqli_query($conn, "INSERT INTO tb_checkout (user_id, product_id, jumlah, total, metode, created_at)
                                   VALUES ('$user_id', '$product_id', '$jumlah', '$total', '$metode', NOW())");

    if ($insert) {
        echo '<script>alert("Pembayaran berhasil!"); window.location="struk.php";</script>';
    } else {
        echo 'Error: ' . mysqli_error($conn);
    }
} else {
    header("Location: index.php");
    exit;
}
?>

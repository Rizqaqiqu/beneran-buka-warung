<?php
session_start();
include 'db.php';

$product_id = (int)($_POST['product_id'] ?? 0);
$qty = max(1, (int)($_POST['qty'] ?? 1));
if ($product_id <= 0) { header('Location: index.php'); exit; }

if (!isset($_SESSION['checkout_produk'])) $_SESSION['checkout_produk'] = [];

// gunakan key product_id agar mudah akumulasi
if (isset($_SESSION['checkout_produk'][$product_id])) {
    // tambahkan jumlah
    $_SESSION['checkout_produk'][$product_id]['jumlah'] += $qty;
    // update subtotal jika ada harga tersimpan
    $_SESSION['checkout_produk'][$product_id]['subtotal'] = $_SESSION['checkout_produk'][$product_id]['jumlah'] * ($_SESSION['checkout_produk'][$product_id]['product_price'] ?? 0);
} else {
    $r = mysqli_query($conn, "SELECT product_name, product_price, product_image FROM tb_product WHERE product_id = {$product_id} LIMIT 1");
    $pd = $r ? mysqli_fetch_assoc($r) : null;
    $_SESSION['checkout_produk'][$product_id] = [
        'product_id' => $product_id,
        'product_name' => $pd['product_name'] ?? '',
        'product_image' => $pd['product_image'] ?? '',
        'product_price' => (float)($pd['product_price'] ?? 0),
        'jumlah' => $qty,
        'subtotal' => ((float)($pd['product_price'] ?? 0)) * $qty
    ];
}
header('Location: keranjang.php');
<?php
session_start();
include 'db.php';

// Cek apakah user sudah login
if (empty($_SESSION['login_user']) || empty($_SESSION['user_id'])) {
    echo '<script>window.location="login-user.php"</script>';
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil data pembelian untuk user ini
$pembelian_q = mysqli_query($conn, "
    SELECT pb.*, p.product_name, p.product_price, p.product_image
    FROM tb_pembelian pb
    LEFT JOIN tb_product p ON pb.product_id = p.product_id
    WHERE pb.user_id = $user_id
    ORDER BY pb.pembelian_id DESC
");

// Group by id_transaksi
$pembelian_grouped = [];
if ($pembelian_q) {
    while ($r = mysqli_fetch_assoc($pembelian_q)) {
        $id_transaksi = $r['id_transaksi'];
        if (!isset($pembelian_grouped[$id_transaksi])) {
            $pembelian_grouped[$id_transaksi] = [
                'id_transaksi' => $id_transaksi,
                'tanggal' => $r['tanggal'],
                'metode' => $r['metode'],
                'metode_detail' => $r['metode_detail'] ?? '',
                'catatan' => $r['catatan'] ?? '',
                'status' => $r['status'] ?? 'pending',
                'total_harga' => $r['total_harga'] ?? 0,
                'ongkir' => $r['ongkir'] ?? 0,
                'produk' => []
            ];
        }
        $pembelian_grouped[$id_transaksi]['produk'][] = [
            'product_name' => $r['product_name'],
            'product_price' => $r['product_price'],
            'jumlah' => $r['jumlah'],
            'subtotal' => ($r['product_price'] * $r['jumlah'])
        ];
    }
}
$pembelian = array_values($pembelian_grouped);

function rp($n){ return 'Rp. ' . number_format((float)$n,0,',','.'); }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pesanan Saya - WarungRizqi</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Quicksand', sans-serif;
            background: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .header {
            background: linear-gradient(135deg, #43a047, #66bb6a);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
        }
        .order-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            overflow: hidden;
        }
        .order-header {
            background: #f8f9fa;
            padding: 15px;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .order-id {
            font-weight: bold;
            color: #495057;
        }
        .order-date {
            color: #6c757d;
            font-size: 14px;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-success { background: #d4edda; color: #155724; }
        .status-cancel { background: #f8d7da; color: #721c24; }
        .order-body {
            padding: 15px;
        }
        .product-item {
            display: flex;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f8f9fa;
        }
        .product-item:last-child {
            border-bottom: none;
        }
        .product-image {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            object-fit: cover;
            margin-right: 15px;
        }
        .product-info {
            flex: 1;
        }
        .product-name {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .product-details {
            color: #6c757d;
            font-size: 14px;
        }
        .product-price {
            font-weight: bold;
            color: #28a745;
        }
        .order-footer {
            background: #f8f9fa;
            padding: 15px;
            border-top: 1px solid #dee2e6;
        }
        .order-total {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: bold;
            font-size: 16px;
        }
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
        }
        .btn-primary { background: #007bff; color: white; }
        .btn-secondary { background: #6c757d; color: white; }
        .no-orders {
            text-align: center;
            padding: 50px;
            color: #6c757d;
        }
        /* Tema Malam yang Lebih Menarik */
        .dark-mode {
            background: linear-gradient(135deg, #0d0d0d 0%, #1a1a2e 50%, #16213e 100%);
            color: #e0e0e0;
            position: relative;
        }
        .dark-mode::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 20% 80%, rgba(120, 119, 198, 0.1) 0%, transparent 50%),
                        radial-gradient(circle at 80% 20%, rgba(255, 119, 198, 0.1) 0%, transparent 50%),
                        radial-gradient(circle at 40% 40%, rgba(120, 219, 226, 0.1) 0%, transparent 50%);
            pointer-events: none;
            z-index: -1;
        }
        .dark-mode .header {
            background: linear-gradient(135deg, #1e1e2e 0%, #2a2a3e 100%);
            color: #e0e0e0;
        }
        .dark-mode .order-card {
            background: linear-gradient(135deg, #2a2a3e 0%, #3a3a5e 100%);
            border-color: #555;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            color: #e0e0e0;
        }
        .dark-mode .order-card:hover {
            background: linear-gradient(135deg, #3a3a5e 0%, #4a4a7e 100%);
            box-shadow: 0 6px 20px rgba(0,0,0,0.4);
        }
        .dark-mode .order-header {
            background: linear-gradient(135deg, #3a3a5e 0%, #4a4a7e 100%);
            border-color: #555;
        }
        .dark-mode .order-footer {
            background: linear-gradient(135deg, #3a3a5e 0%, #4a4a7e 100%);
            border-color: #555;
        }
        .dark-mode .product-item {
            border-bottom-color: #444;
        }
        .dark-mode .status-badge {
            background: rgba(255, 167, 38, 0.1);
            color: #ffa726;
            border: 1px solid rgba(255, 167, 38, 0.3);
        }
        .dark-mode .btn {
            background: linear-gradient(90deg, #ff6b6b, #ff3b30);
            color: #fff;
        }
        .dark-mode .btn:hover {
            background: linear-gradient(90deg, #ff3b30, #ff6b6b);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-shopping-bag"></i> Pesanan Saya</h1>
            <p>Lihat status dan detail pesanan Anda</p>
        </div>

        <?php if (empty($pembelian)): ?>
            <div class="no-orders">
                <i class="fas fa-shopping-cart fa-3x" style="color: #dee2e6; margin-bottom: 20px;"></i>
                <h3>Belum ada pesanan</h3>
                <p>Anda belum melakukan pemesanan apapun.</p>
                <a href="index.php" class="btn btn-primary">Mulai Belanja</a>
            </div>
        <?php else: ?>
            <?php foreach ($pembelian as $order): ?>
                <div class="order-card">
                    <div class="order-header">
                        <div>
                            <div class="order-id">ID Pesanan: <?php echo htmlspecialchars($order['id_transaksi']); ?></div>
                            <div class="order-date"><?php echo date('d M Y, H:i', strtotime($order['tanggal'])); ?></div>
                        </div>
                        <span class="status-badge status-<?php echo strtolower($order['status']); ?>">
                            <?php echo ucfirst($order['status']); ?>
                        </span>
                    </div>

                    <div class="order-body">
                        <?php foreach ($order['produk'] as $product): ?>
                            <div class="product-item">
                                <img src="produk/<?php echo htmlspecialchars($product['product_image'] ?? 'default.png'); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>" class="product-image">
                                <div class="product-info">
                                    <div class="product-name"><?php echo htmlspecialchars($product['product_name']); ?></div>
                                    <div class="product-details">
                                        <?php echo $product['jumlah']; ?> x <?php echo rp($product['product_price']); ?>
                                    </div>
                                </div>
                                <div class="product-price"><?php echo rp($product['subtotal']); ?></div>
                            </div>
                        <?php endforeach; ?>

                        <div style="margin-top: 15px;">
                            <strong>Metode Pembayaran:</strong> <?php echo htmlspecialchars($order['metode']); ?>
                            <?php if (!empty($order['metode_detail'])): ?>
                                (<?php echo htmlspecialchars($order['metode_detail']); ?>)
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($order['catatan'])): ?>
                            <div style="margin-top: 10px;">
                                <strong>Catatan:</strong> <?php echo htmlspecialchars($order['catatan']); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="order-footer">
                        <div class="order-total">
                            <span>Total Pembayaran:</span>
                            <span><?php echo rp($order['total_harga']); ?></span>
                        </div>
                        <div style="margin-top: 10px;">
                            <a href="struk.php?id=<?php echo urlencode($order['id_transaksi']); ?>" target="_blank" class="btn btn-secondary">
                                <i class="fas fa-print"></i> Cetak Struk
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <div style="text-align: center; margin-top: 30px;">
            <a href="index.php" class="btn btn-primary">Kembali ke Beranda</a>
        </div>
    </div>

    <script>
        // Theme Toggle
        const themeToggle = document.getElementById('theme-toggle');
        const body = document.body;
        const icon = themeToggle.querySelector('i');

        // Check for saved theme preference or default to light mode
        const currentTheme = localStorage.getItem('theme') || 'light';
        if (currentTheme === 'dark') {
            body.classList.add('dark-mode');
            icon.classList.remove('fa-moon');
            icon.classList.add('fa-sun');
        }

        themeToggle.addEventListener('click', () => {
            body.classList.toggle('dark-mode');
            const isDark = body.classList.contains('dark-mode');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            if (isDark) {
                icon.classList.remove('fa-moon');
                icon.classList.add('fa-sun');
            } else {
                icon.classList.remove('fa-sun');
                icon.classList.add('fa-moon');
            }
        });
    </script>
</body>
</html>

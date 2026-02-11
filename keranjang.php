<?php
session_start();
include 'db.php';

// Ambil data dari session
$keranjang = isset($_SESSION['keranjang']) ? $_SESSION['keranjang'] : [];

// Ambil detail produk dari database
$produk_list = [];
if (!empty($keranjang)) {
    $ids = implode(',', array_keys($keranjang));
    $result = mysqli_query($conn, "SELECT * FROM tb_product WHERE product_id IN ($ids)");
    while ($row = mysqli_fetch_assoc($result)) {
        $produk_list[$row['product_id']] = $row;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>🛒 Keranjang Belanja</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
            margin: 0;
            padding: 0;
        }
        .section {
            padding: 40px 0;
        }
        .container {
            max-width: 900px;
            margin: auto;
        }
        h3 {
            text-align: center;
            color: #1565c0;
            font-size: 1.8em;
            margin-bottom: 30px;
        }
        .box {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            border-radius: 18px;
            padding: 25px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th {
            background: linear-gradient(90deg, #1976d2, #64b5f6);
            color: #fff;
            padding: 12px;
            text-align: left;
        }
        td {
            padding: 12px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }
        tr:hover {
            background: #f1f9ff;
            transition: 0.3s;
        }
        img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 12px;
            margin-right: 12px;
            box-shadow: 0 2px 8px #1976d233;
        }
        .product-info {
            display: flex;
            align-items: center;
        }
        .btn {
            display: inline-block;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn-hapus {
            background: #ef5350;
            color: white;
            padding: 6px 14px;
        }
        .btn-hapus:hover {
            background: #d32f2f;
        }
        .btn-tambah {
            position: relative;
            background: linear-gradient(45deg, #43a047, #66bb6a);
            color: white;
            padding: 12px 32px;
            font-size: 1.1em;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(67, 160, 71, 0.3), inset 0 1px 0 rgba(255,255,255,0.2);
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            overflow: hidden;
            border: 2px solid rgba(255,255,255,0.1);
        }
        .btn-tambah::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            transition: left 0.5s;
        }
        .btn-tambah:hover::before {
            left: 100%;
        }
        .btn-tambah:hover {
            background: linear-gradient(45deg, #2e7d32, #4caf50);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(67, 160, 71, 0.4), inset 0 1px 0 rgba(255,255,255,0.3);
            border-color: rgba(255,255,255,0.3);
        }
        .total-box {
            text-align: right;
            margin-top: 20px;
        }
        .grand-total {
            font-weight: 700;
            font-size: 1.3em;
            color: #1976d2;
        }
        .empty {
            text-align: center;
            padding: 50px 20px;
        }
        .empty p {
            font-size: 1.3em;
            color: #777;
            margin-bottom: 20px;
        }
        .tips {
            background: #e3f2fd;
            padding: 14px 18px;
            border-radius: 8px;
            margin-top: 20px;
            color: #1976d2;
            font-size: 0.95em;
        }
        input.jumlah-input {
            width: 60px;
            padding: 6px;
            border-radius: 6px;
            border: 1px solid #bbb;
            text-align: center;
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
        .dark-mode .section {
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.1);
        }
        .dark-mode .box {
            background: rgba(255,255,255,0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.1);
        }
        .dark-mode th {
            background: linear-gradient(90deg, #1e1e2e, #2a2a3e);
            color: #e0e0e0;
        }
        .dark-mode td {
            border-bottom-color: #444;
        }
        .dark-mode tr:hover {
            background: rgba(255,255,255,0.05);
        }
        .dark-mode .btn {
            background: linear-gradient(90deg, #ff6b6b, #ff3b30);
            color: #fff;
        }
        .dark-mode .btn:hover {
            background: linear-gradient(90deg, #ff3b30, #ff6b6b);
        }
        .dark-mode .btn-tambah {
            background: linear-gradient(45deg, #43a047, #66bb6a);
            color: white;
        }
        .dark-mode .btn-tambah:hover {
            background: linear-gradient(45deg, #2e7d32, #4caf50);
        }
        .dark-mode .grand-total {
            color: #ffa726;
        }
        .dark-mode .tips {
            background: rgba(255, 167, 38, 0.1);
            color: #ffa726;
            border: 1px solid rgba(255, 167, 38, 0.3);
        }
    </style>
</head>
<body>
<div class="section">
    <div class="container">
        <h3>🛍️ Keranjang Belanja Anda</h3>
        <div class="box">
        <?php if (!empty($keranjang) && !empty($produk_list)) { ?>
            <table id="cart-table">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Harga</th>
                        <th>Jumlah</th>
                        <th>Total</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                $grand_total = 0;
                foreach ($keranjang as $id => $jumlah): 
                    if (!isset($produk_list[$id])) continue;
                    $p = $produk_list[$id];
                    $total = $p['product_price'] * $jumlah;
                    $grand_total += $total;
                ?>
                    <tr data-id="<?= $id ?>" data-price="<?= $p['product_price'] ?>">
                        <td>
                            <div class="product-info">
                                <img src="produk/<?= $p['product_image'] ?>" alt="<?= $p['product_name'] ?>">
                                <div>
                                    <b><?= $p['product_name'] ?></b><br>
                                    <small style="color:#666;"><?= substr($p['product_description'],0,40) ?>...</small>
                                </div>
                            </div>
                        </td>
                        <td style="color:#1976d2;font-weight:600;">Rp. <?= number_format($p['product_price']) ?></td>
                        <td>
                            <input type="number" class="jumlah-input" value="<?= $jumlah ?>" min="1" max="<?= $p['product_stock'] ?? 99; ?>">
                        </td>
                        <td class="total-item" style="color:#43a047;font-weight:600;">Rp. <?= number_format($total) ?></td>
                        <td>
                            <form method="POST" action="hapus-keranjang.php" style="display:inline;">
                                <input type="hidden" name="product_id" value="<?= $id ?>">
                                <button type="submit" class="btn btn-hapus">🗑️ Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

            <div class="total-box">
                <p class="grand-total">Grand Total: <span id="grand-total">Rp. <?= number_format($grand_total) ?></span></p>
                <a href="checkout.php" class="btn btn-tambah">🧾 Checkout Sekarang</a>
            </div>

            <div class="tips">
                💡 <b>Tips:</b> Ubah jumlah produk dan total akan otomatis diperbarui.  
                Jumlah juga tersimpan ke server tanpa perlu reload!
            </div>
        <?php } else { ?>
            <div class="empty">
                <p>Keranjang Anda kosong.<br>Yuk, belanja produk sekarang!</p>
                <a href="index.php" class="btn btn-tambah">🛒 Belanja Sekarang</a>
            </div>
        <?php } ?>
		</div>
	</div>
</div>

<script>
document.querySelectorAll('.jumlah-input').forEach(input => {
    input.addEventListener('change', function() {
        const tr = this.closest('tr');
        const id = tr.dataset.id;
        const price = parseInt(tr.dataset.price);
        const jumlah = parseInt(this.value);

        // Hitung ulang total item
        const totalItem = price * jumlah;
        tr.querySelector('.total-item').innerText = 'Rp. ' + totalItem.toLocaleString();

        // Hitung ulang grand total
        let grandTotal = 0;
        document.querySelectorAll('.total-item').forEach(td => {
            const val = td.innerText.replace(/[^\d]/g, '');
            grandTotal += parseInt(val) || 0;
        });
        document.getElementById('grand-total').innerText = 'Rp. ' + grandTotal.toLocaleString();

        // Kirim pembaruan ke server (update session tanpa reload)
        fetch('update-keranjang.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'product_id=' + id + '&jumlah=' + jumlah
        });
    });
});

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

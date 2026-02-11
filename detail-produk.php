<?php 
session_start(); // 🔹 Tambahan penting untuk mengaktifkan session
error_reporting(0);
include 'db.php';

// Ambil data kontak admin
$kontak = mysqli_query($conn, "SELECT admin_telp, admin_email, admin_address FROM tb_admin WHERE admin_id = 1");
$a = mysqli_fetch_object($kontak);

// Ambil produk berdasarkan ID
$produk = mysqli_query($conn, "SELECT * FROM tb_product WHERE product_id = '".$_GET['id']."' ");
$p = mysqli_fetch_object($produk);

// ----- hitung diskon (jika kolom ada) -----
$price = isset($p->product_price) ? (float)$p->product_price : 0.0;
$discountPrice = null;
$badgeText = '';
if (isset($p->product_discount) && (float)$p->product_discount > 0) {
    $disc = (float)$p->product_discount;
    $discountPrice = round($price * (1 - $disc / 100));
    $badgeText = '-' . (int)$disc . '%';
} elseif (isset($p->product_discount_price) && (float)$p->product_discount_price > 0) {
    $discountPrice = (float)$p->product_discount_price;
    $badgeText = 'Diskon';
}

// 🔹 Tambahan: proses tambah ke keranjang
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_produk = $_POST['product_id'];
    $jumlah = $_POST['jumlah'];

    if (!isset($_SESSION['keranjang'])) {
        $_SESSION['keranjang'] = [];
    }

    if (isset($_SESSION['keranjang'][$id_produk])) {
        $_SESSION['keranjang'][$id_produk] += $jumlah;
    } else {
        $_SESSION['keranjang'][$id_produk] = $jumlah;
    }

    // Arahkan ke halaman keranjang setelah ditambahkan
    header("Location: keranjang.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Warungrizqi</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Theme Toggle Button */
        .theme-toggle {
            background: none;
            border: none;
            color: #43a047;
            font-size: 18px;
            cursor: pointer;
            padding: 10px;
            border-radius: 50%;
            transition: 0.3s;
        }
        .theme-toggle:hover {
            background: rgba(67, 160, 71, 0.1);
        }

        /* Dark Mode Styles - Tema Malam yang Lebih Menarik */
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
        .dark-mode header {
            background: linear-gradient(135deg, #1e1e2e 0%, #2a2a3e 100%);
            box-shadow: 0 4px 20px rgba(0,0,0,0.5);
        }
        .dark-mode .search {
            background: linear-gradient(135deg, #2a2a3e 0%, #3a3a5e 100%);
            border: 1px solid #444;
        }
        .dark-mode .section {
            background: transparent;
        }
        .dark-mode .footer {
            background: linear-gradient(135deg, #1e1e2e 0%, #2a2a3e 100%);
            border-top: 1px solid #444;
        }
        .dark-mode .detail-produk-flex {
            background: linear-gradient(135deg, #2a2a3e 0%, #3a3a5e 100%);
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.3);
            color: #e0e0e0;
        }
        .dark-mode .info-box h3 {
            color: #ffffff;
        }
        .dark-mode .info-box h4 {
            color: #ffa726;
        }
        .dark-mode .deskripsi-box h5 {
            color: #ffffff;
        }
        .dark-mode .deskripsi-box p {
            color: #e0e0e0;
        }
        .dark-mode .btn {
            background: linear-gradient(90deg, #43a047, #66bb6a);
            color: #fff;
        }
        .dark-mode .btn-tambah {
            background: linear-gradient(90deg, #ff6b6b, #ff3b30);
            color: #fff;
        }
        .dark-mode .theme-toggle {
            color: #ffa726;
        }
        .dark-mode .theme-toggle:hover {
            background: rgba(255, 167, 38, 0.1);
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
        .dark-mode header {
            background: linear-gradient(135deg, #1e1e2e 0%, #2a2a3e 100%);
            box-shadow: 0 4px 20px rgba(0,0,0,0.5);
        }
        .dark-mode .search {
            background: linear-gradient(135deg, #2a2a3e 0%, #3a3a5e 100%);
            border: 1px solid #444;
        }
        .dark-mode .section {
            background: transparent;
        }
        .dark-mode .footer {
            background: linear-gradient(135deg, #1e1e2e 0%, #2a2a3e 100%);
            border-top: 1px solid #444;
        }
        .dark-mode .detail-produk-flex {
            background: linear-gradient(135deg, #2a2a3e 0%, #3a3a5e 100%);
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.3);
            color: #e0e0e0;
        }
        .dark-mode .info-box h3 {
            color: #ffffff;
        }
        .dark-mode .info-box h4 {
            color: #ffa726;
        }
        .dark-mode .deskripsi-box h5 {
            color: #ffffff;
        }
        .dark-mode .deskripsi-box p {
            color: #e0e0e0;
        }
        .dark-mode .btn {
            background: linear-gradient(90deg, #43a047, #66bb6a);
            color: #fff;
        }
        .dark-mode .btn-tambah {
            background: linear-gradient(90deg, #ff6b6b, #ff3b30);
            color: #fff;
        }
    </style>
</head>
<body>
    <!-- header -->
    <header>
        <div class="container">
            <h1><a href="index.php">Warungrizqi</a></h1>
            <ul>
                <li><a href="produk.php">Produk</a></li>
                <li><a href="keranjang.php">🛒 Keranjang</a></li> <!-- 🔹 Tambahkan menu keranjang -->
            </ul>
        </div>
    </header>

    <!-- search -->
    <div class="search">
        <div class="container">
            <form action="produk.php">
                <input type="text" name="search" placeholder="Cari Produk" value="<?php echo $_GET['search'] ?>">
                <input type="hidden" name="kat" value="<?php echo $_GET['kat'] ?>">
                <input type="submit" name="cari" value="Cari Produk">
            </form>
        </div>
    </div>

    <!-- product detail -->
    <div class="section">
        <div class="container">
            <h3>Detail Produk</h3>
            <div class="box detail-produk-flex">
                <div class="detail-img" style="position:relative;">
                    <?php if ($badgeText): ?>
                        <span style="position:absolute; top:12px; left:12px; z-index:5; background:linear-gradient(90deg,#ff6b6b,#ff3b30); color:#fff; padding:6px 8px; border-radius:8px; font-weight:700;">
                            <?= htmlspecialchars($badgeText); ?>
                        </span>
                    <?php endif; ?>
                    <img src="produk/<?php echo htmlspecialchars($p->product_image) ?>" 
                         alt="<?php echo htmlspecialchars($p->product_name) ?>" 
                         style="width:260px; max-width:230px; border-radius:14px; 
                                box-shadow:0 6px 24px rgba(0,0,0,0.06), 0 0 0 4px #e3f2fd; background:#fff; padding:14px; 
                                transition:transform 0.3s cubic-bezier(.4,0,.2,1), box-shadow 0.3s cubic-bezier(.4,0,.2,1);">
                </div>
                <div class="detail-info">
                    <div class="info-box">
                        <h3><?php echo htmlspecialchars($p->product_name) ?></h3>
                        <?php if ($discountPrice !== null && $discountPrice < $price): ?>
                            <h4>
                                <span style="text-decoration:line-through;color:#888;font-weight:600;">Rp. <?php echo number_format($price) ?></span>
                                &nbsp;
                                <span style="color:#1976d2;font-weight:800;">Rp. <?php echo number_format($discountPrice) ?></span>
                            </h4>
                        <?php else: ?>
                            <h4>Rp. <?php echo number_format($price) ?></h4>
                        <?php endif; ?>
                        <p style="margin:8px 0 14px 0;font-weight:600;color:#1976d2;">
                            Stok Tersedia: 
                            <span style="background:#e3f2fd;padding:4px 12px;border-radius:8px;">
                                <?php echo isset($p->product_stock) ? $p->product_stock : 'Tidak tersedia'; ?>
                            </span>
                        </p>
                        <div class="deskripsi-box">
                            <h5>Deskripsi Produk</h5>
                            <p><?php echo $p->product_description ?></p>
                        </div>
                        <div class="wa-contact-box">
                            <a href="https://api.whatsapp.com/send?phone=<?php echo $a->admin_telp ?>&text=Hai, saya tertarik dengan produk Anda." 
                               target="_blank" class="btn">
                                Hubungi via Whatsapp
                            </a>
                            <span class="wa-icon">
                                <img src="img/wa.png" width="32" alt="Whatsapp">
                            </span>
                        </div>
                        
                        <!-- Bagian Tambahkan ke Keranjang -->
                        <form action="" method="POST" style="display:inline;">
                            <input type="hidden" name="product_id" value="<?php echo $p->product_id ?>">
                            <!-- kirim product_price yang digunakan (diskon jika ada) -->
                            <input type="hidden" name="product_price" value="<?php echo ($discountPrice !== null && $discountPrice < $price) ? $discountPrice : $price; ?>">
                            <input type="hidden" name="jumlah" value="1">
                            <button type="submit" class="btn-tambah" id="btnKeranjang">+ Tambahkan ke Keranjang</button>
                        </form>
                        <!-- End Bagian Tambahkan ke Keranjang -->

                        <!-- Tombol langsung checkout -->
                        <form action="checkout.php" method="POST" style="display:inline;">
                            <input type="hidden" name="product_id" value="<?php echo $p->product_id ?>">
                            <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($p->product_name) ?>">
                            <input type="hidden" name="product_price" value="<?php echo ($discountPrice !== null && $discountPrice < $price) ? $discountPrice : $price; ?>">
                            <input type="hidden" name="jumlah" value="1">
                            <button type="submit" class="btn-tambah" style="margin-top:10px;">
                                🚀 Checkout Langsung
                            </button>
                        </form>
                        <!-- End Tombol langsung checkout -->
                    </div>
                </div>
            </div>
		</div>
	</div>

	<!-- footer -->
	<div class="footer">
		<div class="container">
			<h4>Alamat</h4>
			<p><?php echo $a->admin_address ?></p>

			<h4>Email</h4>
			<p><?php echo $a->admin_email ?></p>

			<h4>No. Hp</h4>
			<p><?php echo $a->admin_telp ?></p>
	<small>Copyright &copy; 2025 - WarungRizqi.</small>
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

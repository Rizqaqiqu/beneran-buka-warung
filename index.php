<?php
	include 'db.php';
	session_start();

	// Cek apakah user sudah login, jika tidak redirect ke login
	if (empty($_SESSION['login_user']) || empty($_SESSION['user_id'])) {
		echo '<script>window.location="login-user.php"</script>';
		exit;
	}

	$kontak = mysqli_query($conn, "SELECT admin_telp, admin_email, admin_address FROM tb_admin WHERE admin_id = 1");
	$a = mysqli_fetch_object($kontak);

	// Nama user untuk ikon akun (jika login)
	$nama_pengguna = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : "Akun";

	// deteksi kolom diskon (aman jika kolom belum ada)
	$hasDiscountCol = false;
	$hasDiscountPriceCol = false;
	$col = mysqli_query($conn, "SHOW COLUMNS FROM tb_product LIKE 'product_discount'");
	if ($col && mysqli_num_rows($col) > 0) $hasDiscountCol = true;
	$col2 = mysqli_query($conn, "SHOW COLUMNS FROM tb_product LIKE 'product_discount_price'");
	if ($col2 && mysqli_num_rows($col2) > 0) $hasDiscountPriceCol = true;
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
		/* Tambahan kecil untuk ikon akun */
		.icon-akun {
			width: 35px;
			height: 35px;
			border-radius: 50%;
			background: #fff;
			color: #43a047;
			display: inline-flex;
			align-items: center;
			justify-content: center;
			font-weight: bold;
			font-size: 16px;
			cursor: pointer;
			transition: 0.3s;
			border: 1px solid #ccc;
		}
		.icon-akun:hover {
			background: #f1f8e9;
		}

		/* Emoji kategori */
		.kategori-emoji {
			font-size: 40px;
			display: block;
			margin-bottom: 5px;
		}

		/* tanda diskon */
		.product-badge {
			position: absolute;
			top: 10px;
			left: 10px;
			background: linear-gradient(90deg,#ff6b6b,#ff3b30);
			color: #fff;
			font-size: 12px;
			padding: 6px 8px;
			border-radius: 8px;
			font-weight: 700;
			box-shadow: 0 8px 20px rgba(0,0,0,0.08);
			z-index: 3;
		}
		.harga-old { text-decoration: line-through; color: #888; font-size: 13px; display:block; margin-top:6px; }
		.harga-new { color: #1976d2; font-weight:700; font-size:16px; margin-top:4px; display:block; }

		/* Tombol diskon */
		.btn-diskon {
			display: inline-block;
			padding: 15px 30px;
			background: linear-gradient(90deg,#ff6b6b,#ff3b30);
			color: #fff;
			text-decoration: none;
			border-radius: 25px;
			font-weight: 700;
			font-size: 16px;
			box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3);
			transition: 0.3s;
		}
		.btn-diskon:hover {
			background: linear-gradient(90deg,#ff3b30,#ff6b6b);
			transform: translateY(-2px);
			box-shadow: 0 6px 20px rgba(255, 59, 48, 0.4);
		}

		/* Box kategori */
		.kategori-box {
			text-align: center;
			background: #fff;
			border: 1px solid #ddd;
			border-radius: 10px;
			padding: 20px;
			box-shadow: 0 2px 10px rgba(0,0,0,0.1);
			transition: 0.3s;
			cursor: pointer;
		}
		.kategori-box:hover {
			transform: translateY(-5px);
			box-shadow: 0 4px 20px rgba(0,0,0,0.15);
		}

		/* Modern Category Carousel */
		.kategori-carousel {
			position: relative;
			display: flex;
			justify-content: center;
			align-items: center;
			margin: 0 auto;
			max-width: 1200px;
		}
		.carousel-container {
			width: 100%;
			overflow: hidden;
			border-radius: 20px;
			background: linear-gradient(135deg, #43a047 0%, #66bb6a 100%);
			box-shadow: 0 8px 32px rgba(0,0,0,0.1);
		}
		.carousel-track {
			display: flex;
			gap: 0;
			transition: transform 0.4s ease;
		}
		.carousel-item {
			display: block;
			text-decoration: none;
			flex-shrink: 0;
			min-width: 200px;
			background: rgba(255,255,255,0.1);
			border-right: 1px solid rgba(255,255,255,0.2);
			transition: all 0.3s ease;
		}
		.carousel-item:last-child {
			border-right: none;
		}
		.carousel-item:hover {
			background: rgba(255,255,255,0.2);
			transform: translateY(-3px);
		}
		.item-content {
			display: flex;
			flex-direction: column;
			align-items: center;
			justify-content: center;
			padding: 25px 15px;
			text-align: center;
		}
		.item-icon {
			font-size: 32px;
			margin-bottom: 8px;
			display: block;
		}
		.item-text {
			font-size: 14px;
			font-weight: 600;
			color: #333;
			font-family: 'Quicksand', sans-serif;
		}
		.carousel-nav {
			position: absolute;
			top: 50%;
			transform: translateY(-50%);
			background: rgba(255,255,255,0.9);
			border: none;
			width: 40px;
			height: 40px;
			border-radius: 50%;
			cursor: pointer;
			font-size: 18px;
			color: #666;
			transition: all 0.3s ease;
			box-shadow: 0 4px 15px rgba(0,0,0,0.1);
			z-index: 10;
		}
		.carousel-nav:hover {
			background: white;
			color: #1976d2;
			transform: translateY(-50%) scale(1.1);
			box-shadow: 0 6px 20px rgba(0,0,0,0.15);
		}
		.carousel-nav.prev {
			left: -20px;
		}
		.carousel-nav.next {
			right: -20px;
		}

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

		/* Transitions for theme changes */
		body {
			transition: background 0.5s ease, color 0.5s ease;
		}
		.dark-mode header, .dark-mode .search, .dark-mode .section, .dark-mode .footer, .dark-mode .kategori-box, .dark-mode .col-4 {
			transition: background 0.5s ease, color 0.5s ease, border-color 0.5s ease;
		}

		/* Twinkling Stars for Dark Mode */
		.dark-mode .star {
			position: fixed;
			width: 2px;
			height: 2px;
			background: #fff;
			border-radius: 50%;
			animation: twinkle 2s infinite alternate;
			pointer-events: none;
			z-index: 0;
		}
		@keyframes twinkle {
			0% { opacity: 0.3; transform: scale(1); }
			100% { opacity: 1; transform: scale(1.2); }
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
		.dark-mode .kategori-box {
			background: linear-gradient(135deg, #2a2a3e 0%, #3a3a5e 100%);
			border-color: #555;
			box-shadow: 0 4px 15px rgba(0,0,0,0.3);
			color: #e0e0e0;
		}
		.dark-mode .kategori-box:hover {
			background: linear-gradient(135deg, #3a3a5e 0%, #4a4a7e 100%);
			box-shadow: 0 6px 20px rgba(0,0,0,0.4);
		}
		.dark-mode .product-card-meta {
			color: #e0e0e0;
		}
		.dark-mode .product-card-meta .nama {
			color: #ffffff;
		}
		.dark-mode .product-card-meta .harga {
			color: #ffa726;
		}
		.dark-mode .harga-old {
			color: #888;
		}
		.dark-mode .harga-new {
			color: #ffab40;
		}
		.dark-mode .btn-diskon {
			background: linear-gradient(90deg, #ff6b6b, #ff3b30);
			box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3);
		}
		.dark-mode .btn-diskon:hover {
			background: linear-gradient(90deg, #ff3b30, #ff6b6b);
			box-shadow: 0 6px 20px rgba(255, 59, 48, 0.4);
		}
		.dark-mode .theme-toggle {
			color: #ffa726;
		}
		.dark-mode .theme-toggle:hover {
			background: rgba(255, 167, 38, 0.1);
		}
		.dark-mode .carousel-container {
			background: linear-gradient(135deg, #2a2a3e 0%, #3a3a5e 100%);
		}
		.dark-mode .carousel-item {
			background: rgba(255,255,255,0.05);
			border-right: 1px solid rgba(255,255,255,0.1);
		}
		.dark-mode .carousel-item:hover {
			background: rgba(255,255,255,0.1);
		}
		.dark-mode .item-text {
			color: #e0e0e0;
		}
		.dark-mode .product-badge {
			background: linear-gradient(90deg, #ff6b6b, #ff3b30);
		}
	</style>
</head>
<body>
  <!-- animated layers -->
  <div class="stripes" aria-hidden="true"></div>
  <div class="bg-anim" aria-hidden="true">
    <span class="blob b1"></span>
    <span class="blob b2"></span>
    <span class="blob b3"></span>
  </div>

	<!-- header -->
	<header>
		<div class="container">
			<h1><a href="index.php">Warungrizqi</a></h1>
	<ul>
				<li><a href="produk.php">Produk</a></li>
				<li><a href="keranjang.php" class="btn-keranjang" style="margin-left:12px;">🛒 Keranjang</a></li>
				<li><button id="theme-toggle" class="theme-toggle" title="Toggle Theme"><i class="fas fa-moon"></i></button></li>
				<li>
					<a href="profil-user.php" title="Akun">
						<div class="icon-akun"><?php echo strtoupper(substr($nama_pengguna, 0, 1)); ?></div>
					</a>
				</li>
			</ul>
		</div>
	</header>

	<!-- search -->
	<div class="search">
		<div class="container">
			<form action="produk.php">
				<input type="text" name="search" placeholder="Cari Produk">
				<input type="submit" name="cari" value="Cari Produk">
			</form>
		</div>
	</div>

	<!-- category -->
	<div class="section" style="padding: 20px 0;">
		<h3 style="text-align: center; margin-bottom: 20px;">Pilih Kategori</h3>
		<div class="kategori-carousel">
			<div class="carousel-container">
				<div class="carousel-track">
					<?php
						$kategori = mysqli_query($conn, "SELECT * FROM tb_category ORDER BY category_name ASC");
						$categories = [];
						if(mysqli_num_rows($kategori) > 0){
							while($k = mysqli_fetch_array($kategori)){
								$categories[] = $k;
							}
							for($i = 0; $i < 3; $i++){
								foreach($categories as $k){
									$nama = strtolower($k['category_name']);
									switch ($nama) {
										case 'handphone':
											$emoji = '📱';
											break;
										case 'monitor':
											$emoji = '🖥️';
											break;
										case 'ipad':
											$emoji = '📲';
											break;
										case 'laptop':
											$emoji = '💻';
											break;
										default:
											$emoji = '📦';
											break;
									}
					?>
						<a href="produk.php?kat=<?php echo $k['category_id'] ?>" class="carousel-item">
							<div class="item-content">
								<span class="item-icon"><?php echo $emoji; ?></span>
								<span class="item-text"><?php echo $k['category_name'] ?></span>
							</div>
						</a>
					<?php
								}
							}
						}else{ ?>
						<p>Kategori tidak ada</p>
					<?php } ?>
				</div>
			</div>
			<button class="carousel-nav next" onclick="scrollCarousel(1)">&#10095;</button>
		</div>
	</div>



	<!-- tombol diskon -->
	<div class="section" style="text-align: center; padding: 20px 0;">
		<a href="produk.php?diskon_only=1" class="btn-diskon">🛍️ Lihat Produk Diskon</a>
	</div>

	<!-- semua produk -->
	<div class="section">
		<div class="container">
			<h3>Semua Produk</h3>
			<?php
				$produk = mysqli_query($conn, "SELECT * FROM tb_product WHERE product_status = 1 ORDER BY product_id DESC");
				if(mysqli_num_rows($produk) > 0){
			?>
			<div class="box">
				<?php
					while($p = mysqli_fetch_array($produk)){
						$price = (float)$p['product_price'];
						$discountPrice = null;
						$badgeText = '';
						if (!empty($p['product_discount']) && $p['product_discount'] > 0) {
							$disc = (float)$p['product_discount'];
							$discountPrice = round($price * (1 - $disc / 100));
							$badgeText = '-' . (int)$disc . '%';
						} elseif (!empty($p['product_discount_price']) && $p['product_discount_price'] > 0) {
							$discountPrice = (float)$p['product_discount_price'];
							$badgeText = 'Diskon';
						}
				?>
				<a href="detail-produk.php?id=<?php echo $p['product_id']; ?>">
					<div class="col-4" style="position:relative;">
						<?php if ($badgeText): ?>
							<span class="product-badge"><?php echo htmlspecialchars($badgeText); ?></span>
						<?php endif; ?>
						<img src="produk/<?php echo htmlspecialchars($p['product_image']); ?>" alt="<?php echo htmlspecialchars($p['product_name']); ?>" style="width: 200px; height: 150px; object-fit: contain;">
						<div class="product-card-meta">
							<p class="nama"><?php echo htmlspecialchars(substr($p['product_name'], 0, 40)); ?></p>
							<?php if ($discountPrice !== null && $discountPrice < $price): ?>
								<span class="harga-old">Rp. <?php echo number_format($price); ?></span>
								<span class="harga-new">Rp. <?php echo number_format($discountPrice); ?></span>
							<?php else: ?>
								<p class="harga">Rp. <?php echo number_format($price); ?></p>
							<?php endif; ?>
						</div>
					</div>
				</a>
				<?php } ?>
			</div>
			<?php }else{ ?>
				<p>Tidak ada produk</p>
			<?php } ?>
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
		let currentIndex = 0;
		let autoScroll;
		const track = document.querySelector('.carousel-track');
		const items = document.querySelectorAll('.carousel-item');
		const totalItems = items.length;
		const originalItems = totalItems / 3; // Since we duplicated 3 times
		const itemWidth = 200; // min-width from CSS

		function scrollCarousel(direction) {
			currentIndex += direction;

			// Handle infinite loop
			if (currentIndex < 0) {
				currentIndex = originalItems - 1;
			} else if (currentIndex >= originalItems) {
				currentIndex = 0;
			}

			// To make it seamless, always show from the middle set
			let displayIndex = currentIndex + originalItems;

			const translateX = -displayIndex * itemWidth;
			track.style.transform = `translateX(${translateX}px)`;
		}

		// Start auto-scroll
		function startAutoScroll() {
			autoScroll = setInterval(() => {
				scrollCarousel(1);
			}, 3000);
		}

		// Stop auto-scroll
		function stopAutoScroll() {
			clearInterval(autoScroll);
		}

		// Initialize auto-scroll
		startAutoScroll();

		// Pause auto-scroll on hover
		document.querySelector('.kategori-carousel').addEventListener('mouseenter', stopAutoScroll);
		document.querySelector('.kategori-carousel').addEventListener('mouseleave', startAutoScroll);

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
				createStars();
			} else {
				icon.classList.remove('fa-sun');
				icon.classList.add('fa-moon');
				removeStars();
			}
		});

		// Function to create twinkling stars
		function createStars() {
			for (let i = 0; i < 50; i++) {
				const star = document.createElement('div');
				star.className = 'star';
				star.style.left = Math.random() * 100 + '%';
				star.style.top = Math.random() * 100 + '%';
				star.style.animationDelay = Math.random() * 2 + 's';
				document.body.appendChild(star);
			}
		}

		// Function to remove stars
		function removeStars() {
			const stars = document.querySelectorAll('.star');
			stars.forEach(star => star.remove());
		}

		// Initialize stars if dark mode is active
		if (currentTheme === 'dark') {
			createStars();
		}
	</script>
</body>
</html>

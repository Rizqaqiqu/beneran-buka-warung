<?php 
	error_reporting(0);
	include 'db.php';
	$kontak = mysqli_query($conn, "SELECT admin_telp, admin_email, admin_address FROM tb_admin WHERE admin_id = 1");
	$a = mysqli_fetch_object($kontak);
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
		.dark-mode .col-4 {
			background: linear-gradient(135deg, #2a2a3e 0%, #3a3a5e 100%);
			border-color: #555;
			box-shadow: 0 4px 15px rgba(0,0,0,0.3);
			color: #e0e0e0;
		}
		.dark-mode .col-4:hover {
			background: linear-gradient(135deg, #3a3a5e 0%, #4a4a7e 100%);
			box-shadow: 0 6px 20px rgba(0,0,0,0.4);
		}
		.dark-mode .nama {
			color: #ffffff;
		}
		.dark-mode .harga {
			color: #ffa726;
		}
		.dark-mode .harga-old {
			color: #888;
		}
		.dark-mode .harga-new {
			color: #ffab40;
		}
		.dark-mode .theme-toggle {
			color: #ffa726;
		}
		.dark-mode .theme-toggle:hover {
			background: rgba(255, 167, 38, 0.1);
		}
		.dark-mode .product-badge {
			background: linear-gradient(90deg, #ff6b6b, #ff3b30);
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
			</ul>
			<button id="theme-toggle" class="theme-toggle"><i class="fas fa-moon"></i></button>
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



	<!-- new product -->
	<div class="section">
		<div class="container">
			<h3>Produk</h3>
			<div class="box">
				<?php
					$where = "";
					if($_GET['search'] != '' || $_GET['kat'] != ''){
						$where = "AND product_name LIKE '%".$_GET['search']."%' AND category_id LIKE '%".$_GET['kat']."%' ";
					}

					$produk = mysqli_query($conn, "SELECT * FROM tb_product WHERE product_status = 1 $where ORDER BY product_id DESC");
					if(mysqli_num_rows($produk) > 0){
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
					<a href="detail-produk.php?id=<?php echo $p['product_id'] ?>">
						<div class="col-4" style="position:relative;">
							<?php if ($badgeText): ?>
								<span class="product-badge"><?php echo htmlspecialchars($badgeText); ?></span>
							<?php endif; ?>
							<img src="produk/<?php echo $p['product_image'] ?>" style="width: 200px; height: 150px; object-fit: contain;">
							<p class="nama"><?php echo substr($p['product_name'], 0, 30) ?></p>
							<?php if ($discountPrice !== null && $discountPrice < $price): ?>
								<span class="harga-old">Rp. <?php echo number_format($price); ?></span>
								<span class="harga-new">Rp. <?php echo number_format($discountPrice); ?></span>
							<?php else: ?>
								<p class="harga">Rp. <?php echo number_format($price) ?></p>
							<?php endif; ?>
						</div>
					</a>
				<?php }}else{ ?>
					<p>COMING SOON...</p>
				<?php } ?>
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
			createStars();
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
	</script>
</body>
</html>

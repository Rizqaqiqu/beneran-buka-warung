<?php 
	session_start();
	include 'db.php';
	if($_SESSION['status_login'] != true){
		echo '<script>window.location="login.php"</script>';
		exit;
	}

	// Ambil data ringkasan
	$jumlah_kategori = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM tb_category"));
	$jumlah_produk   = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM tb_product"));
	$jumlah_beli     = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM tb_pembelian"));
	$jumlah_user     = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM tb_admin"));

	// Ambil tahun yang dipilih, default tahun sekarang
	$selected_year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');

	// --- GRAFIK PENJUALAN PER BULAN ---
	$data_penjualan = [];
	for ($i = 1; $i <= 12; $i++) {
		$query = mysqli_query($conn, "SELECT COUNT(*) AS total
									  FROM tb_pembelian
									  WHERE MONTH(tanggal) = $i
									  AND YEAR(tanggal) = $selected_year");
		$row = mysqli_fetch_assoc($query);
		$data_penjualan[] = $row['total'] ?? 0;
	}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Dashboard - WarungRizqi</title>

	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&family=Poppins:wght@600&display=swap" rel="stylesheet">
	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

	<style>
		:root{
			--bg:#0f172a;
			--card-bg:#0b1220;
			--muted:#9aa6b2;
			--accent: linear-gradient(90deg,#4facfe,#00f2fe);
			--glass: rgba(255,255,255,0.04);
			--card-elev: 0 10px 30px rgba(2,6,23,0.5);
		}
		*{box-sizing:border-box}
		html,body{height:100%;margin:0;font-family:Inter,system-ui,Arial;background:
			radial-gradient(800px 400px at 10% 10%, rgba(79,70,229,0.08), transparent 6%),
			radial-gradient(600px 300px at 90% 90%, rgba(16,185,129,0.03), transparent 6%),
			#071022;color:#e6eef8;-webkit-font-smoothing:antialiased}
		a{color:inherit}
		.container{max-width:1180px;margin:28px auto;padding:22px;position:relative;z-index:1}

		/* BACKGROUND ANIMATION: soft moving gradient blobs */
		.bg-anim{
  position:fixed;
  inset:0;
  z-index:0;
  pointer-events:none;
  overflow:hidden;
  mix-blend-mode:normal;
}

/* blob base */
.bg-anim .blob{
  position:absolute;
  border-radius:50%;
  filter:blur(90px);
  opacity:0.18;
  transform:translate3d(0,0,0);
  will-change:transform;
  mix-blend-mode:screen;
}

/* individual blobs: size, color, start position, animation */
.bg-anim .b1{
  width:600px;height:600px;
  left:-12%;top:-18%;
  background:radial-gradient(circle at 20% 30%, rgba(124,58,237,0.95), rgba(6,182,212,0.85));
  animation:blobFloat1 14s ease-in-out infinite;
}
.bg-anim .b2{
  width:420px;height:420px;
  right:-8%;top:6%;
  background:radial-gradient(circle at 60% 40%, rgba(16,185,129,0.95), rgba(79,70,229,0.85));
  animation:blobFloat2 18s ease-in-out infinite;
  opacity:0.14;
}
.bg-anim .b3{
  width:480px;height:480px;
  left:10%;bottom:-16%;
  background:radial-gradient(circle at 30% 60%, rgba(255,186,116,0.95), rgba(255,99,132,0.85));
  animation:blobFloat3 20s ease-in-out infinite;
  opacity:0.12;
}

/* subtle movement keyframes */
@keyframes blobFloat1{
  0%{ transform: translate3d(0,0,0) scale(1); }
  25%{ transform: translate3d(40px,-30px,0) scale(1.05); }
  50%{ transform: translate3d(0,-60px,0) scale(1); }
  75%{ transform: translate3d(-30px,-20px,0) scale(0.98); }
  100%{ transform: translate3d(0,0,0) scale(1); }
}
@keyframes blobFloat2{
  0%{ transform: translate3d(0,0,0) scale(1); }
  20%{ transform: translate3d(-40px,20px,0) scale(1.03); }
  50%{ transform: translate3d(30px,40px,0) scale(1); }
  80%{ transform: translate3d(-10px,10px,0) scale(0.99); }
  100%{ transform: translate3d(0,0,0) scale(1); }
}
@keyframes blobFloat3{
  0%{ transform: translate3d(0,0,0) scale(1); }
  30%{ transform: translate3d(20px,40px,0) scale(1.04); }
  60%{ transform: translate3d(-30px,20px,0) scale(1); }
  90%{ transform: translate3d(10px,-10px,0) scale(0.97); }
  100%{ transform: translate3d(0,0,0) scale(1); }
}

/* reduce motion if user prefers */
@media (prefers-reduced-motion: reduce){
  .bg-anim .blob{ animation: none; transform:none; filter:blur(72px); }
}

		.top-actions{display:flex;gap:10px;align-items:center}
		.btn{padding:10px 14px;border-radius:10px;border:1px solid rgba(255,255,255,0.04);background:linear-gradient(90deg,rgba(255,255,255,0.02),transparent);cursor:pointer;color:#e6eef8;font-weight:700}
		.btn.primary{background:linear-gradient(90deg,#4facfe,#00f2fe);color:#071022;box-shadow:0 12px 30px rgba(79,70,229,0.12)}

		.nav{margin-top:16px;background:transparent;padding:12px;border-radius:12px;display:flex;gap:12px;flex-wrap:wrap}
		.nav a{padding:8px 12px;border-radius:10px;color:var(--muted);text-decoration:none;font-weight:600}
		.nav a.active{background:rgba(255,255,255,0.03);color:#fff;box-shadow:0 6px 18px rgba(2,6,23,0.25)}

		.dashboard-grid{
			display:grid;
			grid-template-columns:repeat(4,1fr);
			gap:18px;
			margin-top:20px;
		}
		@media(max-width:980px){ .dashboard-grid{grid-template-columns:repeat(2,1fr)} }
		@media(max-width:520px){ .dashboard-grid{grid-template-columns:1fr} .nav{justify-content:center} }

		.card {
			border-radius:14px;padding:18px;background:linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01));
			box-shadow:var(--card-elev);border:1px solid rgba(255,255,255,0.03);backdrop-filter: blur(6px);transition:transform .28s,box-shadow .28s;
			overflow:hidden;
		}
		.card:hover{transform:translateY(-8px);box-shadow:0 20px 60px rgba(2,6,23,0.55)}
		.kpi{display:flex;align-items:center;gap:14px}
		.kpi .icon{width:56px;height:56px;border-radius:10px;background:linear-gradient(135deg,#2dd4bf,#60a5fa);display:flex;align-items:center;justify-content:center;font-size:22px;font-weight:800;color:#071022;box-shadow:0 10px 28px rgba(15,23,42,0.12)}
		.kpi .value{font-size:28px;font-weight:800}
		.kpi .label{color:var(--muted);font-size:13px;margin-top:6px}

		.chart-card{grid-column:1 / -1;padding:18px;display:flex;gap:18px;align-items:center;justify-content:space-between}
		.chart-wrap{flex:1;margin-left:10px}
		.canvas{height:320px}
		.year-selector{margin-top:8px;display:flex;gap:8px;flex-wrap:wrap}
		.year-btn{padding:6px 12px;border-radius:8px;border:1px solid rgba(255,255,255,0.04);background:linear-gradient(90deg,rgba(255,255,255,0.02),transparent);cursor:pointer;color:#e6eef8;font-size:14px;outline:none}
		.year-btn.active{background:linear-gradient(90deg,#4facfe,#00f2fe);color:#071022}
		.year-btn:hover{background:linear-gradient(90deg,rgba(255,255,255,0.05),transparent)}

		.small-card{display:flex;flex-direction:column;gap:8px}
		.small-card .muted{color:var(--muted);font-size:13px}

		.box{margin-top:22px;padding:18px;border-radius:12px;background:linear-gradient(180deg, rgba(255,255,255,0.01), rgba(255,255,255,0.0));border:1px solid rgba(255,255,255,0.02)}

		.footer{margin-top:28px;text-align:center;color:var(--muted);font-size:13px}

		/* animated counter */
		.counter { font-variant-numeric: tabular-nums; }

	</style>
</head>
<body>
  <!-- animated background layer (inserted) -->
  <div class="bg-anim" aria-hidden="true">
    <span class="blob b1"></span>
    <span class="blob b2"></span>
    <span class="blob b3"></span>
  </div>

	<div class="container">
		<header>
			<div class="brand">
				<div class="logo">WR</div>
				<div>
					<div class="title">Warungrizqi Admin</div>
					<div class="sub">Dashboard kontrol toko — ringkasan performa</div>
				</div>
			</div>
		</header>

		<nav class="nav" aria-label="Main navigation">
			<a href="dashboard.php" class="active">Dashboard</a>
			<a href="profil.php">Profil</a>
			<a href="data-kategori.php">Kategori</a>
			<a href="data-produk.php">Produk</a>
			<a href="data-pembelian.php">Pembelian</a>
			<a href="data-user.php">Admin</a>
			<a href="technical-report.php">Laporan</a>
			<a href="keluar.php">Keluar</a>
		</nav>

		<section class="dashboard-grid" aria-hidden="false">
			<div class="card">
				<div class="kpi">
					<div class="icon">📦</div>
					<div>
						<div class="value counter" data-target="<?php echo $jumlah_produk; ?>">0</div>
						<div class="label muted">Total Produk</div>
					</div>
				</div>
			</div>

			<div class="card">
				<div class="kpi">
					<div class="icon">🗂️</div>
					<div>
						<div class="value counter" data-target="<?php echo $jumlah_kategori; ?>">0</div>
						<div class="label muted">Total Kategori</div>
					</div>
				</div>
			</div>

			<div class="card">
				<div class="kpi">
					<div class="icon">🛒</div>
					<div>
						<div class="value counter" data-target="<?php echo $jumlah_beli; ?>">0</div>
						<div class="label muted">Total Pembelian</div>
					</div>
				</div>
			</div>

			<div class="card">
				<div class="kpi">
					<div class="icon">👤</div>
					<div>
						<div class="value counter" data-target="<?php echo $jumlah_user; ?>">0</div>
						<div class="label muted">Jumlah Admin</div>
					</div>
				</div>
			</div>

			<div class="card chart-card">
				<div style="min-width:220px">
					<div style="color:var(--muted)">Grafik Penjualan</div>
					<div style="font-weight:800;font-size:18px;margin-top:6px">Per Bulan — <?php echo $selected_year; ?></div>
					<div class="year-selector">
						<button class='year-btn' onclick="window.location.href='dashboard.php?year=<?php echo $selected_year - 1; ?>'">&larr;</button>
						<button class='year-btn active' disabled><?php echo $selected_year; ?></button>
						<button class='year-btn' onclick="window.location.href='dashboard.php?year=<?php echo $selected_year + 1; ?>'">&rarr;</button>
					</div>
				</div>
				<div class="chart-wrap">
					<canvas id="penjualanChart" class="canvas"></canvas>
				</div>
			</div>
		</section>

		<div class="box">
			<h3 style="margin:0 0 8px 0">Ringkasan Cepat</h3>
			<p style="margin:0;color:var(--muted)">Selamat datang, <strong style="color:#fff"><?php echo htmlspecialchars($_SESSION['a_global']->admin_name) ?></strong>. Gunakan menu di atas untuk mengelola toko — tambahkan produk, cek pesanan, dan pantau penjualan.</p>
		</div>

		<div class="footer">
			<small>Copyright &copy; 2025 - <b>WarungRizqi</b>. All Rights Reserved.</small>
		</div>
	</div>

	<script>
		// Counter animation
		document.querySelectorAll('.counter').forEach(el=>{
			const end = parseInt(el.getAttribute('data-target')) || 0;
			let start = 0;
			const dur = 900;
			const step = Math.ceil(end / (dur / 16));
			const id = setInterval(()=>{
				start += step;
				if (start >= end) { el.textContent = end; clearInterval(id); }
				else el.textContent = start;
			},16);
		});

		// Chart.js with gradient
		(function(){
			const ctx = document.getElementById('penjualanChart').getContext('2d');
			const gradient = ctx.createLinearGradient(0,0,0,300);
			gradient.addColorStop(0, 'rgba(79,70,229,0.9)');
			gradient.addColorStop(1, 'rgba(0,242,254,0.08)');

			const labels = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
			const data = <?php echo json_encode($data_penjualan); ?>;
			new Chart(ctx, {
				type: 'bar',
				data: {
					labels: labels,
					datasets: [{
						label: 'Transaksi',
						data: data,
						backgroundColor: gradient,
						borderRadius: 8,
						maxBarThickness: 32,
					}]
				},
				options: {
					responsive: true,
					maintainAspectRatio: false,
					plugins: {
						legend: { display: false },
						tooltip: { mode: 'index', intersect: false }
					},
					scales: {
						x: { grid: { display: false }, ticks: { color: '#bfcbd8' } },
						y: { beginAtZero: true, ticks: { color: '#bfcbd8' }, grid: { color: 'rgba(255,255,255,0.03)' } }
					}
				}
			});
		})();

		/* optional: tiny idle nudge for accessibility-safe devices */
if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
  // slight phase-shift to make motion feel organic (no heavy JS)
  const blobs = document.querySelectorAll('.bg-anim .blob');
  blobs.forEach((b, i) => {
    b.style.animationDelay = (i * 0.9) + 's';
  });
}
	</script>
</body>
</html>

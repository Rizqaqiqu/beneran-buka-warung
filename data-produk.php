<?php 
session_start();
include 'db.php';
if($_SESSION['status_login'] != true){
    echo '<script>window.location="login.php"</script>';
    exit;
}

// ambil data produk
$produk_q = mysqli_query($conn, "SELECT p.*, c.category_name FROM tb_product p LEFT JOIN tb_category c USING (category_id) ORDER BY p.product_id DESC");
$produk = [];
if ($produk_q) {
    while ($r = mysqli_fetch_assoc($produk_q)) $produk[] = $r;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Data Produk - WarungRizqi</title>

	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&family=Poppins:wght@600&display=swap" rel="stylesheet">

	<style>
		:root{
			--bg:#0f172a;
			--card-bg:#0b1220;
			--muted:#9aa6b2;
			--accent: linear-gradient(90deg,#4facfe,#00f2fe);
			--glass: rgba(255,255,255,0.04);
			--card-elev: 0 10px 30px rgba(2,6,23,0.5);
			--success:#16a34a;
		}
		*{box-sizing:border-box}
		html,body{height:100%;margin:0;font-family:Inter,system-ui,Arial;background:
			radial-gradient(800px 400px at 10% 10%, rgba(79,70,229,0.08), transparent 6%),
			radial-gradient(600px 300px at 90% 90%, rgba(16,185,129,0.03), transparent 6%),
			#071022;color:#e6eef8;-webkit-font-smoothing:antialiased}
		a{color:inherit;text-decoration:none}
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

		.brand{display:flex;align-items:center;gap:12px}
		.logo{width:56px;height:56px;border-radius:12px;background:linear-gradient(135deg,#4facfe,#00f2fe);display:flex;align-items:center;justify-content:center;color:#071022;font-weight:800;font-family:Poppins;font-size:18px;box-shadow:0 10px 30px rgba(0,0,0,0.35)}
		.title{font-size:18px;font-weight:700}
		.sub{color:var(--muted);font-size:13px}

		.nav{margin-top:16px;background:transparent;padding:12px;border-radius:12px;display:flex;gap:12px;flex-wrap:wrap}
		.nav a{padding:8px 12px;border-radius:10px;color:var(--muted);text-decoration:none;font-weight:600}
		.nav a.active{background:rgba(255,255,255,0.03);color:#fff;box-shadow:0 6px 18px rgba(2,6,23,0.25)}

		/* controls */
		.controls{display:flex;gap:10px;align-items:center}
		.btn{
		  position:relative;overflow:hidden;display:inline-flex;align-items:center;gap:8px;padding:10px 14px;border-radius:10px;border:1px solid rgba(255,255,255,0.04);
		  background:linear-gradient(90deg,rgba(255,255,255,0.02),transparent);color:#e6eef8;cursor:pointer;font-weight:700;transition:transform .18s ease, box-shadow .18s ease;will-change:transform;text-decoration:none;
		}
		.btn.primary{background:linear-gradient(90deg,#4facfe,#00f2fe);color:#071022;box-shadow:0 12px 30px rgba(79,70,229,0.12)}
		.btn:hover{transform:translateY(-4px);box-shadow:0 12px 30px rgba(2,6,23,0.14)}
		.btn:active{transform:translateY(-1px)}

		/* table card */
		.card{background:linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01));padding:18px;border-radius:12px;border:1px solid rgba(255,255,255,0.03);box-shadow:var(--card-elev);backdrop-filter: blur(6px)}
.table-wrap{overflow:auto;border-radius:10px;margin-top:12px}
.table{width:100%;border-collapse:collapse;min-width:820px}
.table thead th{background:transparent;color:var(--muted);text-align:left;padding:12px;font-weight:700;border-bottom:1px dashed rgba(255,255,255,0.04)}
.table tbody td{padding:12px;border-bottom:1px dashed rgba(255,255,255,0.04);vertical-align:middle;color:#e8f0ff}

/* thumbnails */
.tn{width:64px;height:48px;border-radius:8px;object-fit:cover;border:1px solid var(--glass)}

/* status pill */
.pill{display:inline-block;padding:6px 8px;border-radius:999px;font-weight:700;font-size:13px}
.pill.active{background:rgba(34,197,94,0.12);color:var(--success);border:1px solid rgba(34,197,94,0.06)}
.pill.inactive{background:rgba(255,80,80,0.08);color:#ffb4b4;border:1px solid rgba(255,80,80,0.06)}

/* action buttons */
.action-btn{padding:8px 10px;border-radius:8px;font-weight:700;position:relative;overflow:hidden;cursor:pointer;border:none;transition:transform .14s,box-shadow .14s}
.action-btn:active{transform:translateY(-1px)}

/* edit / delete specifics */
.btn-edit{background:linear-gradient(90deg,#ffd166,#ff6b6b);color:#071022}
.btn-hapus{background:linear-gradient(90deg,#ff6b6b,#ff8fab);color:#071022}

/* hover/focus */
.action-btn:hover{transform:translateY(-6px) scale(1.03);box-shadow:0 18px 36px rgba(2,6,23,0.14)}

/* chevron */
.action-btn::after{content:'›';position:absolute;right:8px;top:50%;transform:translateY(-50%) translateX(8px);opacity:0;transition:transform .18s,opacity .18s;color:rgba(0,0,0,0.12)}
.action-btn:hover::after{transform:translateY(-50%) translateX(0);opacity:0.95;color:rgba(0,0,0,0.18)}

/* ripple */
.ripple{position:absolute;border-radius:50%;transform:scale(0);background:rgba(255,255,255,0.12);animation:ripple 600ms linear;pointer-events:none}
@keyframes ripple{to{transform:scale(2.8);opacity:0}}

/* responsive */
@media(max-width:980px){ .table{min-width:720px} .logo{width:48px;height:48px} }
@media(max-width:660px){ .table{min-width:600px} .controls{flex-direction:column;align-items:flex-start} }
		.empty{padding:28px;text-align:center;color:var(--muted);border-radius:10px;background:rgba(255,255,255,0.02)}
		.footer{margin-top:28px;text-align:center;color:var(--muted);font-size:13px}
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
        <div class="sub">Kelola data produk</div>
      </div>
    </div>
  </header>

  <nav class="nav" aria-label="Main navigation">
    <a href="dashboard.php">Dashboard</a>
    <a href="profil.php">Profil</a>
    <a href="data-kategori.php">Kategori</a>
    <a href="data-produk.php" class="active">Produk</a>
    <a href="data-pembelian.php">Pembelian</a>
    <a href="data-user.php">Admin</a>
    <a href="keluar.php">Keluar</a>
  </nav>

  <div class="controls" style="margin-top:14px;margin-bottom:14px">
    <button class="btn primary" onclick="window.location.href='tambah-produk.php'">+ Tambah Produk</button>
  </div>

    <section class="card">
        <h2 style="margin:0">Data Produk</h2>
        <div class="sub" style="margin-top:6px">Tambah, edit, atau hapus produk</div>

        <div class="table-wrap" aria-live="polite">
            <?php if (!empty($produk)): ?>
            <table class="table" role="table" aria-label="Daftar Produk">
                <thead>
                    <tr>
                        <th style="width:60px">No</th>
                        <th>Gambar</th>
                        <th>Nama Produk</th>
                        <th>Harga</th>
                        <th>Kategori</th>
                        <th>Status</th>
                        <th style="width:190px;text-align:right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($produk as $row): ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td>
                            <?php if (!empty($row['product_image']) && file_exists('produk/'.$row['product_image'])): ?>
                                <img src="produk/<?= htmlspecialchars($row['product_image']); ?>" alt="<?= htmlspecialchars($row['product_name']); ?>" class="tn">
                            <?php else: ?>
                                <div style="width:64px;height:48px;border-radius:8px;background:rgba(255,255,255,0.02);display:flex;align-items:center;justify-content:center;color:var(--muted)">No Img</div>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($row['product_name']); ?></td>
                        <td>Rp. <?= number_format($row['product_price'],0,',','.'); ?></td>
                        <td><?= htmlspecialchars($row['category_name'] ?? '-'); ?></td>
                        <td>
                            <span class="pill <?= ($row['product_status']==1)?'active':'inactive' ?>">
                                <?= ($row['product_status']==1)?'Aktif':'Tidak Aktif' ?>
                            </span>
                        </td>
                        <td style="text-align:right">
                            <button class="action-btn btn-edit" onclick="window.location='edit-produk.php?id=<?= $row['product_id']; ?>'">✏️ Edit</button>
                            <button class="action-btn btn-delete" onclick="if(confirm('Yakin hapus produk ini?')) window.location='proses-hapus.php?type=produk&id=<?= $row['product_id']; ?>'">🗑️ Hapus</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
                <div class="empty">Belum ada produk. Tambah produk baru untuk mulai berjualan.</div>
            <?php endif; ?>
        </div>
    </section>

    <div class="footer">
    	<small>Copyright &copy; 2025 - <b>WarungRizqi</b>. All Rights Reserved.</small>
    </div>
</div>

<script>
// ripple for .btn and .action-btn
function createRipple(e, el){
    const rect = el.getBoundingClientRect();
    const circle = document.createElement('span');
    circle.className = 'ripple';
    const size = Math.max(rect.width, rect.height);
    circle.style.width = circle.style.height = size + 'px';
    circle.style.left = ((e.clientX || rect.left + rect.width/2) - rect.left - size/2) + 'px';
    circle.style.top = ((e.clientY || rect.top + rect.height/2) - rect.top - size/2) + 'px';
    el.appendChild(circle);
    setTimeout(()=> circle.remove(), 700);
}

document.querySelectorAll('.btn, .action-btn').forEach(btn=>{
    btn.addEventListener('click', function(e){
        // keyboard activation sometimes has clientX=0; still show center ripple
        createRipple(e, this);
    });
    // keyboard activation
    btn.addEventListener('keydown', function(e){
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            this.click();
        }
    });
});

// hapus konfirmasi
function confirmDelete(id){
    if (!confirm('Yakin ingin menghapus produk ini?')) return;
    window.location.href = 'proses-hapus.php?idp=' + encodeURIComponent(id);
}
</script>
</body>
</html>
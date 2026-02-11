<?php 
session_start();
include 'db.php';
if($_SESSION['status_login'] != true){
    echo '<script>window.location="login.php"</script>';
    exit;
}

// ambil data kategori
$kategori_q = mysqli_query($conn, "SELECT * FROM tb_category ORDER BY category_id DESC");
$kategori = [];
if ($kategori_q) {
    while ($r = mysqli_fetch_assoc($kategori_q)) $kategori[] = $r;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Data Kategori — WarungRizqi</title>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&family=Poppins:wght@600&display=swap" rel="stylesheet">
<style>
:root{
  --bg:#071022; --card-elev: 0 10px 30px rgba(2,6,23,0.45);
  --accent:#4facfe; --muted:#9aa6b2; --glass: rgba(255,255,255,0.03);
}
*{box-sizing:border-box}
body{
  margin:0;font-family:Inter,system-ui,Arial;background:
  radial-gradient(800px 400px at 10% 10%, rgba(79,70,229,0.08), transparent 6%),
  #071022;color:#e6eef8;-webkit-font-smoothing:antialiased;padding:28px;
}
.container{max-width:1180px;margin:0 auto}
.header{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:18px}
.brand{display:flex;align-items:center;gap:12px}
.logo{width:56px;height:56px;border-radius:12px;background:linear-gradient(135deg,#4facfe,#00f2fe);display:flex;align-items:center;justify-content:center;color:#071022;font-weight:800;font-family:Poppins;font-size:18px;box-shadow:0 10px 30px rgba(0,0,0,0.35)}
.title{font-size:18px;font-weight:700}
.sub{color:var(--muted);font-size:13px}

/* nav */
.nav{margin-top:12px;background:transparent;padding:8px;border-radius:12px;display:flex;gap:10px;flex-wrap:wrap}
.nav a{padding:8px 12px;border-radius:10px;color:var(--muted);text-decoration:none;font-weight:600}
.nav a.active{background:rgba(255,255,255,0.03);color:#fff;box-shadow:0 6px 18px rgba(2,6,23,0.25)}

/* page layout */
.section{margin-top:18px}
.card{
  background:linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01));
  padding:18px;border-radius:12px;border:1px solid rgba(255,255,255,0.03);box-shadow:var(--card-elev);
}

/* table */
.table-wrap{overflow:auto;border-radius:10px}
.table{
  width:100%;border-collapse:collapse;margin-top:12px;background:transparent;
  min-width:720px;
}
.table thead th{background:transparent;color:var(--muted);text-align:left;padding:12px;font-weight:700;border-bottom:1px dashed rgba(255,255,255,0.04)}
.table tbody td{padding:12px;border-bottom:1px dashed rgba(255,255,255,0.04);vertical-align:middle;color:#e8f0ff}

/* buttons */
.btn{
  position:relative;
  overflow:hidden;
  display:inline-flex;
  align-items:center;
  gap:8px;
  padding:9px 12px;
  border-radius:10px;
  border:none;
  cursor:pointer;
  font-weight:700;
  transition:transform .18s ease, box-shadow .18s ease;
  background:transparent;color:#e6eef8;border:1px solid rgba(255,255,255,0.04)
}
.btn.primary{background:linear-gradient(90deg,#4facfe,#00f2fe);color:#071022;border:none;box-shadow:0 10px 28px rgba(79,70,229,0.08)}
.btn.ghost{background:transparent;border:1px solid rgba(255,255,255,0.04)}
.btn:hover{transform:translateY(-4px);box-shadow:0 12px 30px rgba(2,6,23,0.12)}
.btn:active{transform:translateY(-1px)}

/* chevron & ripple */
.btn::after{content:'›';position:absolute;right:10px;top:50%;transform:translateY(-50%) translateX(6px);opacity:0;transition:transform .18s,opacity .18s}
.btn:hover::after{transform:translateY(-50%) translateX(0);opacity:0.9}
.btn .ripple{position:absolute;border-radius:50%;transform:scale(0);background:rgba(255,255,255,0.12);animation:ripple 600ms linear;pointer-events:none}
@keyframes ripple{to{transform:scale(2.8);opacity:0}}

/* action buttons in table */
.action-btn{
  padding:8px 10px;border-radius:8px;font-weight:700;
  position:relative;
  overflow:hidden;
  transition:transform .14s cubic-bezier(.2,.9,.2,1), box-shadow .14s, filter .14s;
  will-change:transform,box-shadow;
  cursor:pointer;
  box-shadow:0 6px 18px rgba(2,6,23,0.06);
}
.action-btn:active{ transform:translateY(-1px) scale(.995); }

/* specific styles */
.btn-edit{
  background:linear-gradient(90deg,#ffd166,#ff6b6b);
  color:#071022;border:none;
}
.btn-hapus{
  background:linear-gradient(90deg,#ff6b6b,#ff8fab);
  color:#071022;border:none;
}

/* hover / focus states */
.action-btn:hover, .action-btn:focus {
  transform:translateY(-6px) scale(1.03);
  box-shadow:0 18px 36px rgba(2,6,23,0.14);
  filter:brightness(1.03);
  outline:none;
}

/* subtle icon chevron on hover */
.action-btn::after{
  content:'›';
  position:absolute;
  right:10px;
  top:50%;
  transform:translateY(-50%) translateX(8px);
  opacity:0; color:rgba(0,0,0,0.12);
  font-weight:800; transition:transform .18s,opacity .18s;
}
.action-btn:hover::after{ transform:translateY(-50%) translateX(0); opacity:0.95; color:rgba(0,0,0,0.18); }

/* ripple for action buttons */
.action-btn .ripple{
  position:absolute;border-radius:50%;transform:scale(0);background:rgba(255,255,255,0.12);
  animation:ripple 600ms linear;pointer-events:none;mix-blend-mode:screen;
}
@keyframes ripple{ to{ transform:scale(2.8); opacity:0 }}

/* empty state */
.empty{padding:28px;text-align:center;color:var(--muted)}

/* footer */
footer{margin-top:18px;text-align:center;color:var(--muted);font-size:13px}
@media(max-width:720px){
  .logo{width:48px;height:48px}
  .table{min-width:600px}
}
</style>
</head>
<body>
    <div class="container">
        <header class="header">
            <div class="brand">
                <div class="logo">WR</div>
                <div>
                    <div class="title">Warungrizqi</div>
                    <div class="sub">Kelola kategori produk</div>
                </div>
            </div>

            <div>
                <button class="btn" onclick="window.location='dashboard.php'">🏠 Dashboard</button>
            </div>
        </header>

        <nav class="nav" aria-label="Main navigation">
            <a href="dashboard.php">Dashboard</a>
            <a href="profil.php">Profil</a>
            <a href="data-kategori.php" class="active">Kategori</a>
            <a href="data-produk.php">Produk</a>
            <a href="data-pembelian.php">Pembelian</a>
            <a href="data-user.php">Admin</a>
            <a href="keluar.php">Keluar</a>
        </nav>

        <section class="section">
            <div class="card">
                <div style="display:flex;justify-content:space-between;align-items:center">
                    <div>
                        <h2 style="margin:0">Data Kategori</h2>
                        <div class="sub" style="margin-top:6px">Tambahkan, edit, atau hapus kategori</div>
                    </div>
                    <div style="display:flex;gap:8px;align-items:center">
                        <button class="btn ghost" onclick="window.location.href='tambah-kategori.php'">+ Tambah Data</button>
                        <!-- Tombol "Lihat Produk" dihapus -->
                    </div>
                </div>

                <div class="table-wrap" style="margin-top:14px">
                    <table class="table" role="table" aria-label="Daftar Kategori">
                        <thead>
                            <tr>
                                <th style="width:60px">No</th>
                                <th>Nama Kategori</th>
                                <th style="width:180px;text-align:right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($kategori)): ?>
                                <?php $no = 1; foreach ($kategori as $row): ?>
                                    <tr>
                                        <td><?= $no++; ?></td>
                                        <td><?= htmlspecialchars($row['category_name']); ?></td>
                                        <td style="text-align:right">
                                            <button class="action-btn btn-edit" onclick="window.location='edit-kategori.php?id=<?= $row['category_id']; ?>'">✏️ Edit</button>
                                            <button class="action-btn btn-delete" onclick="if(confirm('Yakin hapus kategori ini?')) window.location='proses-hapus.php?type=kategori&id=<?= $row['category_id']; ?>'">🗑️ Hapus</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3">
                                        <div class="empty">Belum ada kategori. Tambahkan kategori baru untuk mulai mengelompokkan produk.</div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </section>

        <footer>
            <small>Copyright &copy; 2025 - WarungRizqi.</small>
        </footer>
    </div>

<script>
// ripple for all .btn
document.querySelectorAll('.btn').forEach(btn=>{
    btn.addEventListener('click', function(e){
        const rect = this.getBoundingClientRect();
        const circle = document.createElement('span');
        circle.className = 'ripple';
        const size = Math.max(rect.width, rect.height);
        circle.style.width = circle.style.height = size + 'px';
        circle.style.left = ((e.clientX || rect.left + rect.width/2) - rect.left - size/2) + 'px';
        circle.style.top = ((e.clientY || rect.top + rect.height/2) - rect.top - size/2) + 'px';
        this.appendChild(circle);
        setTimeout(()=> circle.remove(), 700);
    });
});

// Add ripple and keyboard support for action buttons (Edit / Hapus)
document.querySelectorAll('.action-btn').forEach(btn=>{
    // make focusable
    if (!btn.hasAttribute('tabindex')) btn.setAttribute('tabindex','0');

    btn.addEventListener('click', function(e){
        const rect = this.getBoundingClientRect();
        const circle = document.createElement('span');
        circle.className = 'ripple';
        const size = Math.max(rect.width, rect.height);
        circle.style.width = circle.style.height = size + 'px';
        circle.style.left = ((e.clientX || rect.left + rect.width/2) - rect.left - size/2) + 'px';
        circle.style.top = ((e.clientY || rect.top + rect.height/2) - rect.top - size/2) + 'px';
        this.appendChild(circle);
        setTimeout(()=> circle.remove(), 700);
    });

    // keyboard activation
    btn.addEventListener('keydown', function(e){
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            this.click();
        }
    });
});

// hapus konfirmasi (menggunakan redirect ke proses-hapus.php?idk=)
function confirmDelete(id){
    if (!confirm('Yakin ingin menghapus kategori ini?')) return;
    window.location.href = 'proses-hapus.php?idk=' + encodeURIComponent(id);
}
</script>
</body>
</html>
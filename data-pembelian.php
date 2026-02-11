<?php
session_start();
include 'db.php';
if ($_SESSION['status_login'] != true) {
    echo '<script>window.location="login.php"</script>';
    exit;
}

// Ambil data pembelian (gabungkan produk)
$pembelian_q = mysqli_query($conn, "
    SELECT pb.*, p.product_name, p.product_price, p.product_image
    FROM tb_pembelian pb
    LEFT JOIN tb_product p ON pb.product_id = p.product_id
    ORDER BY pb.pembelian_id DESC
");

// Group by id_transaksi to show per order
$pembelian_grouped = [];
if ($pembelian_q) {
    while ($r = mysqli_fetch_assoc($pembelian_q)) {
        $id_transaksi = $r['id_transaksi'];
        if (!isset($pembelian_grouped[$id_transaksi])) {
            $pembelian_grouped[$id_transaksi] = [
                'id_transaksi' => $id_transaksi,
                'user_nama' => $r['user_nama'],
                'user_email' => $r['user_email'],
                'user_telp' => $r['user_telp'],
                'user_alamat' => $r['user_alamat'],
                'metode' => $r['metode'],
                'metode_detail' => $r['metode_detail'] ?? '',
                'tanggal' => $r['tanggal'],
                'catatan' => $r['catatan'] ?? '',
                'total_harga' => $r['total_harga'] ?? 0,
                'ongkir' => $r['ongkir'] ?? 0,
                'produk' => []
            ];
        }
        // Set product name, image, price, and jumlah from the first product
        if (!isset($pembelian_grouped[$id_transaksi]['product_name'])) {
            $pembelian_grouped[$id_transaksi]['product_name'] = $r['product_name'];
            $pembelian_grouped[$id_transaksi]['product_image'] = $r['product_image'];
            $pembelian_grouped[$id_transaksi]['product_price'] = $r['product_price'];
            $pembelian_grouped[$id_transaksi]['jumlah'] = $r['jumlah'];
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
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Data Pembelian - WarungRizqi</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&family=Poppins:wght@600&display=swap" rel="stylesheet">
<style>
:root{
  --bg:#071022; --muted:#9aa6b2; --accent-start:#4facfe; --accent-end:#00f2fe;
  --glass: rgba(255,255,255,0.03);
}
*{box-sizing:border-box}
html,body{height:100%;margin:0;font-family:Inter,system-ui,Arial;background:
  radial-gradient(800px 400px at 10% 10%, rgba(79,70,229,0.08), transparent 6%),
  radial-gradient(600px 300px at 90% 90%, rgba(16,185,129,0.03), transparent 6%),
  #071022;color:#e6eef8;-webkit-font-smoothing:antialiased}

/* ANIMATED BACKGROUND LAYER */
.bg-anim{
  position:fixed;
  inset:0;
  z-index:0;
  pointer-events:none;
  overflow:hidden;
  mix-blend-mode:normal;
}

.bg-anim .blob{
  position:absolute;
  border-radius:50%;
  filter:blur(90px);
  opacity:0.18;
  transform:translate3d(0,0,0);
  will-change:transform;
  mix-blend-mode:screen;
}

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

@media (prefers-reduced-motion: reduce){
  .bg-anim .blob{ animation: none; transform:none; filter:blur(72px); }
}

/* MAIN CONTENT */
a{color:inherit;text-decoration:none}
.container{max-width:1180px;margin:28px auto;padding:22px;position:relative;z-index:1}
.header{display:flex;align-items:center;gap:12px}
.brand{display:flex;align-items:center;gap:12px}
.logo{width:56px;height:56px;border-radius:12px;background:linear-gradient(135deg,var(--accent-start),var(--accent-end));display:flex;align-items:center;justify-content:center;color:#071022;font-weight:800;font-family:Poppins;font-size:18px;box-shadow:0 10px 30px rgba(0,0,0,0.35)}
.title{font-size:18px;font-weight:700}
.sub{color:var(--muted);font-size:13px}

.nav{margin-top:16px;background:transparent;padding:12px;border-radius:12px;display:flex;gap:12px;flex-wrap:wrap}
.nav a{padding:8px 12px;border-radius:10px;color:var(--muted);text-decoration:none;font-weight:600}
.nav a.active{background:rgba(255,255,255,0.03);color:#fff;box-shadow:0 6px 18px rgba(2,6,23,0.25)}

/* controls */
.controls{display:flex;gap:10px;align-items:center}
.btn{
  position:relative;overflow:hidden;display:inline-flex;align-items:center;gap:8px;padding:9px 12px;border-radius:10px;border:none;cursor:pointer;font-weight:700;
  background:transparent;color:#e6eef8;border:1px solid rgba(255,255,255,0.04);transition:transform .16s,box-shadow .16s;
}
.btn.primary{background:linear-gradient(90deg,var(--accent-start),var(--accent-end));color:#071022;border:none;box-shadow:0 10px 28px rgba(79,70,229,0.08)}
.btn.ghost{background:transparent;border:1px solid rgba(255,255,255,0.04)}
.btn:hover{transform:translateY(-4px);box-shadow:0 12px 30px rgba(2,6,23,0.12)}

/* card and table */
.card{background:linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01));padding:18px;border-radius:12px;border:1px solid rgba(255,255,255,0.03);box-shadow:0 10px 30px rgba(2,6,23,0.5);backdrop-filter:blur(6px)}
.table-wrap{overflow:auto;border-radius:10px;margin-top:12px}
.table{width:100%;border-collapse:collapse;min-width:900px}
.table thead th{background:transparent;color:var(--muted);text-align:left;padding:12px;font-weight:700;border-bottom:1px dashed rgba(255,255,255,0.04)}
.table tbody td{padding:12px;border-bottom:1px dashed rgba(255,255,255,0.04);vertical-align:middle;color:#e8f0ff}

/* thumbnails */
.tn{width:64px;height:48px;border-radius:8px;object-fit:cover;border:1px solid var(--glass)}

/* status pill */
.pill{display:inline-block;padding:6px 8px;border-radius:999px;font-weight:700;font-size:13px}
.pill.pending{background:rgba(255,193,7,0.12);color:#ffb020;border:1px solid rgba(255,193,7,0.06)}
.pill.success{background:rgba(34,197,94,0.12);color:#8ef08a;border:1px solid rgba(34,197,94,0.06)}
.pill.cancel{background:rgba(255,80,80,0.08);color:#ffb4b4;border:1px solid rgba(255,80,80,0.06)}

/* action buttons */
.action-btn{padding:8px 10px;border-radius:8px;font-weight:700;position:relative;overflow:hidden;cursor:pointer;border:none;transition:transform .14s,box-shadow .14s}
.action-btn:active{transform:translateY(-1px)}
.btn-struk{background:linear-gradient(90deg,#60a5fa,#4facfe);color:#071022}
.btn-note{background:linear-gradient(90deg,#ffd166,#ff6b6b);color:#071022}

/* hover */
.action-btn:hover{transform:translateY(-6px) scale(1.03);box-shadow:0 18px 36px rgba(2,6,23,0.14)}
.action-btn::after{content:'›';position:absolute;right:8px;top:50%;transform:translateY(-50%) translateX(8px);opacity:0;transition:transform .18s,opacity .18s;color:rgba(0,0,0,0.12)}
.action-btn:hover::after{transform:translateY(-50%) translateX(0);opacity:0.95;color:rgba(0,0,0,0.18)}

/* ripple */
.ripple{position:absolute;border-radius:50%;transform:scale(0);background:rgba(255,255,255,0.12);animation:ripple 600ms linear;pointer-events:none}
@keyframes ripple{to{transform:scale(2.8);opacity:0}}

/* MODAL STYLES */
.modal{
  display:none;
  position:fixed;
  inset:0;
  z-index:1000;
  background:rgba(0,0,0,0.6);
  backdrop-filter:blur(4px);
  align-items:center;
  justify-content:center;
  animation:fadeIn 0.28s ease-out;
}
.modal.active{display:flex}

@keyframes fadeIn{
  from{opacity:0;background:rgba(0,0,0,0.4)}
  to{opacity:1;background:rgba(0,0,0,0.6)}
}

.modal-content{
  background:linear-gradient(180deg, rgba(255,255,255,0.04), rgba(255,255,255,0.01));
  border:1px solid rgba(255,255,255,0.08);
  border-radius:16px;
  padding:28px;
  width:90%;
  max-width:500px;
  box-shadow:0 20px 60px rgba(0,0,0,0.5);
  backdrop-filter:blur(10px);
  animation:slideUp 0.28s cubic-bezier(0.34,1.56,0.64,1);
}

@keyframes slideUp{
  from{transform:translateY(40px);opacity:0}
  to{transform:translateY(0);opacity:1}
}

.modal-header{
  display:flex;
  align-items:center;
  justify-content:space-between;
  margin-bottom:18px;
  padding-bottom:12px;
  border-bottom:1px solid rgba(255,255,255,0.04);
}
.modal-title{font-size:18px;font-weight:700;margin:0}
.modal-close{
  background:transparent;
  border:none;
  color:#e6eef8;
  cursor:pointer;
  font-size:24px;
  padding:0;
  width:32px;
  height:32px;
  display:flex;
  align-items:center;
  justify-content:center;
  border-radius:8px;
  transition:background 0.2s;
}
.modal-close:hover{background:rgba(255,255,255,0.08)}

.modal-body{
  margin-bottom:20px;
}

.form-group{
  margin-bottom:16px;
  display:flex;
  flex-direction:column;
  gap:8px;
}
.form-group label{
  font-weight:700;
  font-size:14px;
  color:#e6eef8;
}
.form-group textarea{
  padding:12px;
  border-radius:10px;
  border:1px solid rgba(255,255,255,0.08);
  background:rgba(255,255,255,0.02);
  color:#e6eef8;
  font-family:Inter,system-ui,Arial;
  font-size:14px;
  resize:vertical;
  min-height:120px;
  transition:border-color 0.2s,background 0.2s;
}
.form-group textarea:focus{
  outline:none;
  border-color:rgba(79,70,229,0.5);
  background:rgba(255,255,255,0.04);
}

.modal-actions{
  display:flex;
  gap:10px;
  justify-content:flex-end;
}
.modal-actions button{
  padding:10px 16px;
  border-radius:10px;
  border:none;
  cursor:pointer;
  font-weight:700;
  transition:transform 0.2s,box-shadow 0.2s;
}
.modal-actions button:hover{
  transform:translateY(-2px);
  box-shadow:0 8px 20px rgba(0,0,0,0.3);
}
.btn-cancel{
  background:transparent;
  border:1px solid rgba(255,255,255,0.1);
  color:#e6eef8;
}
.btn-save{
  background:linear-gradient(90deg,#4facfe,#00f2fe);
  color:#071022;
}

/* responsive */
@media(max-width:980px){ .table{min-width:720px} .logo{width:48px;height:48px} }
@media(max-width:660px){ .table{min-width:600px} .controls{flex-direction:column;align-items:flex-start} .modal-content{width:95%;max-width:none} }
.empty{padding:28px;text-align:center;color:var(--muted);border-radius:10px;background:rgba(255,255,255,0.02)}
</style>
</head>
<body>
<!-- ANIMATED BACKGROUND BLOBS -->
<div class="bg-anim" aria-hidden="true">
  <span class="blob b1"></span>
  <span class="blob b2"></span>
  <span class="blob b3"></span>
</div>

<!-- MODAL CATATAN -->
<div class="modal" id="noteModal">
  <div class="modal-content">
    <div class="modal-header">
      <h2 class="modal-title">✉️ Catatan Pesanan</h2>
      <button class="modal-close" onclick="closeNoteModal()">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label>ID Pesanan</label>
        <input type="text" id="noteOrderId" readonly style="padding:10px;border-radius:10px;border:1px solid rgba(255,255,255,0.08);background:rgba(255,255,255,0.02);color:#e6eef8;font-weight:700;cursor:not-allowed">
      </div>
      <div class="form-group">
        <label>Catatan</label>
        <textarea id="noteText" placeholder="Ketik catatan untuk pesanan ini..."></textarea>
      </div>
    </div>
    <div class="modal-actions">
      <button class="btn-cancel" onclick="closeNoteModal()">Batal</button>
      <button class="btn-save" onclick="saveNote()">💾 Simpan</button>
    </div>
  </div>
</div>

<div class="container">
  <header>
    <div class="brand">
      <div class="logo">WR</div>
      <div>
        <div class="title">Warungrizqi Admin</div>
        <div class="sub">Pantau pesanan dan transaksi</div>
      </div>
    </div>
  </header>

  <nav class="nav" aria-label="Main navigation">
    <a href="dashboard.php">Dashboard</a>
    <a href="profil.php">Profil</a>
    <a href="data-kategori.php">Kategori</a>
    <a href="data-produk.php">Produk</a>
    <a href="data-pembelian.php" class="active">Pembelian</a>
    <a href="data-user.php">Admin</a>
    <a href="keluar.php">Keluar</a>
  </nav>

  <section class="card" aria-labelledby="title" style="margin-top:20px">
    <h2 id="title" style="margin:0">Data Pembelian</h2>
    <div class="sub" style="margin-top:6px">Lihat detail pesanan, metode pembayaran, dan cetak struk.</div>

    <div class="table-wrap" style="margin-top:14px">
      <?php if (!empty($pembelian)): ?>
      <table class="table" role="table" aria-label="Daftar Pembelian">
        <thead>
          <tr>
            <th style="width:56px">No</th>
            <th>Produk</th>
            <th style="width:90px">Jumlah</th>
            <th style="width:140px">Harga Satuan</th>
            <th style="width:140px">Total</th>
            <th style="width:160px">Metode</th>
            <th style="width:160px">Tanggal</th>
            <th style="width:160px;text-align:right">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php $no = 1; foreach ($pembelian as $row): 
            $harga = isset($row['product_price']) ? (float)$row['product_price'] : 0;
            $jumlah = (int)($row['jumlah'] ?? 1);
            $total = isset($row['total_harga']) && $row['total_harga'] !== null ? (float)$row['total_harga'] : ($harga * $jumlah);
            $metode = htmlspecialchars($row['metode'] ?? '-');
            $img = (!empty($row['product_image']) && file_exists('produk/'.$row['product_image'])) ? 'produk/'.htmlspecialchars($row['product_image']) : 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI2NCIgaGVpZ2h0PSI0OCIgdmlld0JveD0iMCAwIDY0IDQ4Ij4KICA8cmVjdCB3aWR0aD0iNjQiIGhlaWdodD0iNDgiIGZpbGw9IiNlMmU4ZjAiLz4KICA8dGV4dCB4PSIzMiIgeT0iMjQiIGZvbnQtZmFtaWx5PSJBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIxMiIgZmlsbD0iIzk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSI+Tm8gSW1hZ2U8L3RleHQ+Cjwvc3ZnPg==';
            $status = strtolower($row['status'] ?? $row['order_status'] ?? 'pending');
            $pillClass = ($status === 'success' || $status === 'paid') ? 'success' : (($status === 'cancel') ? 'cancel' : 'pending');
          ?>
          <tr>
            <td><?= $no++; ?></td>
            <td>
              <div style="display:flex;align-items:center;gap:10px">
                <img src="<?= $img ?>" alt="<?= htmlspecialchars($row['product_name'] ?? 'Produk') ?>" class="tn">
                <div>
                  <div style="font-weight:700"><?= htmlspecialchars($row['product_name'] ?? 'Produk'); ?></div>
                  <div class="small" style="color:var(--muted);margin-top:6px">ID: <?= htmlspecialchars($row['id_transaksi'] ?? ($row['pembelian_id'] ?? '-')); ?></div>
                </div>
              </div>
            </td>
            <td><?= $jumlah; ?></td>
            <td><?= rp($harga); ?></td>
            <td><?= rp($total); ?></td>
            <td>
              <div style="display:flex;flex-direction:column">
                <div style="font-weight:700"><?= $metode; ?></div>
                <?php if (!empty($row['metode_detail'])): ?>
                  <div class="small" style="color:var(--muted);margin-top:6px"><?= htmlspecialchars($row['metode_detail']); ?></div>
                <?php endif; ?>
              </div>
            </td>
            <td><?= htmlspecialchars(date('d-m-Y H:i', strtotime($row['tanggal'] ?? 'now'))); ?></td>
            <td style="text-align:right">
              <button class="action-btn btn-struk" onclick="window.open('struk.php?id=<?= urlencode($row['id_transaksi'] ?? $row['pembelian_id']); ?>','_blank')">📄 Struk</button>
              <button class="action-btn btn-note" onclick="openNoteModal('<?= htmlspecialchars($row['id_transaksi'] ?? $row['pembelian_id']); ?>')">✉️ Catatan</button>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <?php else: ?>
        <div class="empty">Belum ada data pembelian.</div>
      <?php endif; ?>
    </div>
  </section>

  <div style="margin-top:28px;text-align:center;color:var(--muted);font-size:13px">
    <small>Copyright &copy; 2025 - <b>WarungRizqi</b>. All Rights Reserved.</small>
  </div>
</div>

<script>
// Animation delay for blobs
if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
  const blobs = document.querySelectorAll('.bg-anim .blob');
  blobs.forEach((b, i) => {
    b.style.animationDelay = (i * 0.9) + 's';
  });
}

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
    createRipple(e, this);
  });
  btn.addEventListener('keydown', function(e){
    if (e.key === 'Enter' || e.key === ' ') {
      e.preventDefault();
      this.click();
    }
  });
});

// Modal functions
function openNoteModal(orderId){
  document.getElementById('noteOrderId').value = orderId;
  // Load existing catatan from server
  fetch('get_catatan.php?id_transaksi=' + encodeURIComponent(orderId))
  .then(response => response.text())
  .then(data => {
    document.getElementById('noteText').value = data;
    document.getElementById('noteModal').classList.add('active');
    document.getElementById('noteText').focus();
  })
  .catch(error => {
    console.error('Error:', error);
    document.getElementById('noteText').value = '';
    document.getElementById('noteModal').classList.add('active');
    document.getElementById('noteText').focus();
  });
}

function closeNoteModal(){
  document.getElementById('noteModal').classList.remove('active');
  document.getElementById('noteText').value = '';
}

function saveNote(){
  const orderId = document.getElementById('noteOrderId').value;
  const noteText = document.getElementById('noteText').value.trim();

  if(!noteText){
    alert('⚠️ Catatan tidak boleh kosong');
    return;
  }

  // Simpan ke server
  fetch('save_catatan.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: 'id_transaksi=' + encodeURIComponent(orderId) + '&catatan=' + encodeURIComponent(noteText)
  })
  .then(response => response.text())
  .then(data => {
    if (data === 'success') {
      alert('✅ Catatan berhasil disimpan');
      closeNoteModal();
    } else {
      alert('❌ Gagal menyimpan catatan');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('❌ Terjadi kesalahan');
  });
}

// Close modal saat klik di luar area modal
document.getElementById('noteModal').addEventListener('click', function(e){
  if(e.target === this){
    closeNoteModal();
  }
});

// Keyboard shortcut: ESC untuk tutup modal
document.addEventListener('keydown', function(e){
  if(e.key === 'Escape'){
    closeNoteModal();
  }
});
</script>
</body>
</html>
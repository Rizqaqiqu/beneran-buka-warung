<?php
session_start();
include 'db.php';

// Cek login user
if (empty($_SESSION['login_user']) || empty($_SESSION['user_id'])) {
    echo '<script>window.location="login-user.php"</script>';
    exit;
}

$user_id = $_SESSION['user_id'];
$user = mysqli_query($conn, "SELECT * FROM tb_user WHERE user_id = '$user_id'");
$u = mysqli_fetch_object($user);

// Ambil ID transaksi
$id_transaksi = isset($_GET['id']) ? mysqli_real_escape_string($conn, $_GET['id']) : '';

$transaksi_q = mysqli_query($conn, "SELECT * FROM tb_pembelian WHERE id_transaksi = '$id_transaksi' LIMIT 1");
$t = mysqli_fetch_object($transaksi_q);

if (!$t) {
    echo "<script>alert('Transaksi tidak ditemukan!'); window.location='index.php';</script>";
    exit;
}

// Ambil semua item untuk id_transaksi ini dari tb_pembelian dan gabungkan dengan tb_product untuk data produk
// (perbaikan: cek kolom terlebih dahulu agar tidak error jika kolom ongkir/total_harga tidak ada)
$items = [];
$total_belanja = 0;
$metode_label = $t->metode ?? '';
$metode_detail = $t->metode_detail ?? '';
$found_ongkir = null;
$found_total_harga = null;

// cek kolom pada tabel tb_pembelian
$availableCols = [];
$resCols = @mysqli_query($conn, "SHOW COLUMNS FROM tb_pembelian");
if ($resCols) {
    while ($c = mysqli_fetch_assoc($resCols)) $availableCols[] = $c['Field'];
}

// bangun daftar kolom SELECT dengan aman (tambahkan hanya jika ada)
$selectCols = [
    'bp.product_id',
    'bp.jumlah',
    'bp.metode',
    'bp.tanggal'
];
if (in_array('metode_detail', $availableCols)) $selectCols[] = 'bp.metode_detail';
if (in_array('ongkir', $availableCols)) $selectCols[] = 'bp.ongkir';
if (in_array('total_harga', $availableCols)) $selectCols[] = 'bp.total_harga';

$select = implode(', ', $selectCols) . ', p.product_name, p.product_price, p.product_image';

$sql = "
    SELECT {$select}
    FROM tb_pembelian bp
    LEFT JOIN tb_product p ON p.product_id = bp.product_id
    WHERE bp.id_transaksi = '" . mysqli_real_escape_string($conn, $id_transaksi) . "'
";
$item_q = mysqli_query($conn, $sql);

if (!$item_q) {
    // fallback: ambil minimal data tanpa kolom opsional
    $sql = "
        SELECT bp.product_id, bp.jumlah, bp.metode, bp.tanggal,
               p.product_name, p.product_price, p.product_image
        FROM tb_pembelian bp
        LEFT JOIN tb_product p ON p.product_id = bp.product_id
        WHERE bp.id_transaksi = '" . mysqli_real_escape_string($conn, $id_transaksi) . "'
    ";
    $item_q = mysqli_query($conn, $sql);
}

while ($row = mysqli_fetch_assoc($item_q)) {
    $harga = isset($row['product_price']) ? (float)$row['product_price'] : 0;
    $jumlah = (int)$row['jumlah'];
    $subtotal = $harga * $jumlah;
    $total_belanja += $subtotal;
    if ($found_ongkir === null && isset($row['ongkir'])) $found_ongkir = (float)$row['ongkir'];
    if ($found_total_harga === null && isset($row['total_harga'])) $found_total_harga = (float)$row['total_harga'];
    if (!$metode_label && !empty($row['metode'])) $metode_label = $row['metode'];
    if (!$metode_detail && !empty($row['metode_detail'])) $metode_detail = $row['metode_detail'];

    $items[] = [
        'nama' => $row['product_name'] ?? 'Produk',
        'harga' => $harga,
        'jumlah' => $jumlah,
        'subtotal' => $subtotal,
        'gambar' => $row['product_image'] ?? ''
    ];
}

$ongkir = ($found_ongkir !== null) ? $found_ongkir : 0.0;
$total_pembayaran = ($found_total_harga !== null) ? $found_total_harga : ($total_belanja + $ongkir);

// helper format
function rp($n){ return 'Rp' . number_format($n,0,',','.'); }
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Struk — WarungRizqi</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&family=Poppins:wght@600&display=swap" rel="stylesheet">
<style>
:root{
  --bg:#f6f8fb;
  --card:#ffffff;
  --accent:#0066ff;
  --muted:#6b7280;
  --success:#16a34a;
  --glass: rgba(0,0,0,0.03);
}
*{box-sizing:border-box}
body{font-family:Inter,system-ui,Arial; background:var(--bg); margin:0; color:#0f172a; -webkit-font-smoothing:antialiased}
.container{max-width:980px;margin:28px auto;padding:20px}
.header{
  display:flex;align-items:center;gap:16px;padding:18px;border-radius:12px;background:linear-gradient(90deg,#fff,#fbfdff);
  box-shadow:0 8px 30px rgba(15,23,42,0.06);border:1px solid rgba(10,30,60,0.04);
}
.logo{
  width:68px;height:68px;border-radius:12px;background:linear-gradient(135deg,#4facfe,#00f2fe);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-family:Poppins;
  font-size:20px;box-shadow:0 8px 20px rgba(0,102,255,0.08)
}
.header-info{flex:1}
.header-info h1{margin:0;font-size:20px}
.header-info p{margin:6px 0 0;color:var(--muted);font-size:13px}
.meta {display:flex;gap:12px;flex-wrap:wrap}
.badge{background:rgba(0,102,255,0.08);color:var(--accent);padding:6px 10px;border-radius:999px;font-weight:700;font-size:13px}

/* layout main */
.main {
  display:grid;
  grid-template-columns: 1fr 340px;
  gap:18px;
  margin-top:18px;
}
@media(max-width:980px){ .main{grid-template-columns:1fr} }

/* left panel */
.card{
  background:var(--card);padding:18px;border-radius:12px;border:1px solid rgba(10,30,60,0.04);box-shadow:0 6px 18px rgba(15,23,42,0.04)
}
.user-card{display:flex;gap:14px;align-items:center}
.user-avatar{width:64px;height:64px;border-radius:10px;background:linear-gradient(90deg,#f4f7ff,#eef7ff);display:flex;align-items:center;justify-content:center;font-weight:700;color:var(--accent)}
.user-details .name{font-weight:800;font-size:16px}
.user-details .meta{font-size:13px;color:var(--muted);margin-top:6px}

/* items table */
.items-table{width:100%;border-collapse:collapse;margin-top:14px}
.items-table th, .items-table td{padding:12px;border-bottom:1px dashed rgba(10,30,60,0.04);vertical-align:middle;text-align:left}
.items-table th{background:transparent;color:var(--muted);font-weight:700;font-size:13px}
.item-thumb{width:72px;height:72px;border-radius:8px;object-fit:cover;border:1px solid var(--glass)}

/* right summary */
.summary{position:sticky;top:24px}
.summary .box{padding:16px;border-radius:12px;background:linear-gradient(180deg,#fff,#fbfdff);border:1px solid rgba(10,30,60,0.04)}
.summary .row{display:flex;justify-content:space-between;align-items:center;padding:8px 0}
.summary .total{font-size:20px;font-weight:800;color:var(--accent)}

/* payment info */
.pay-info{margin-top:12px;padding:12px;border-radius:10px;background:linear-gradient(90deg,#f8faff,#ffffff);border:1px solid rgba(10,30,60,0.04)}
.pay-info .title{font-weight:700}
.pay-info .detail{color:var(--muted);margin-top:6px;font-size:14px}

/* qr */
.qr {text-align:center;padding:14px;background:linear-gradient(180deg,#fff,#fbfdff);border-radius:12px;border:1px solid rgba(10,30,60,0.04);margin-top:12px}

/* buttons */
.actions{display:flex;gap:10px;flex-wrap:wrap;margin-top:14px}
.btn{display:inline-flex;align-items:center;gap:8px;padding:10px 14px;border-radius:10px;border:none;cursor:pointer;font-weight:700}
.btn-primary{background:linear-gradient(90deg,var(--accent),#4facfe);color:#fff;box-shadow:0 10px 28px rgba(0,102,255,0.08)}
.btn-ghost{background:transparent;border:1px solid rgba(10,30,60,0.06);color:#0f172a}

/* footer */
.note{margin-top:18px;color:var(--muted);font-size:13px;text-align:center}

/* responsive tweaks */
@media(max-width:640px){
  .user-card{flex-direction:row}
  .header{flex-direction:column;align-items:flex-start;gap:10px}
  .meta{width:100%}
}
</style>
</head>
<body>
<div class="container">
  <div class="header" role="banner">
    <div class="logo">WR</div>
    <div class="header-info">
      <h1>Struk Pembayaran — WarungRizqi</h1>
      <div class="meta">
        <div class="badge">ID: <?= htmlspecialchars($t->id_transaksi) ?></div>
        <div style="color:var(--muted);font-size:13px">Tanggal: <?= htmlspecialchars($t->tanggal) ?></div>
      </div>
    </div>
    <div style="text-align:right">
      <div style="font-weight:800;color:var(--accent);font-size:16px"><?= rp($total_pembayaran) ?></div>
      <div style="color:var(--muted);font-size:13px">Total Pembayaran</div>
    </div>
  </div>

  <div class="main">
    <div class="card" id="strukArea">
      <div class="user-card">
        <div class="user-avatar"><?= strtoupper(substr($u->user_nama ?? 'P',0,1)) ?></div>
        <div class="user-details">
          <div class="name"><?= htmlspecialchars($u->user_nama) ?></div>
          <div class="meta"><?= htmlspecialchars($u->user_email) ?> • <?= htmlspecialchars($u->user_telp) ?></div>
        </div>
        <div style="margin-left:auto;text-align:right;color:var(--muted);font-size:13px">
          <div style="font-weight:700">Pembayaran</div>
          <div style="margin-top:6px"><?= htmlspecialchars($metode_label) ?><?php if(!empty($metode_detail)) echo ' — '.htmlspecialchars($metode_detail); ?></div>
        </div>
      </div>

      <table class="items-table" aria-label="Daftar produk">
        <thead>
          <tr>
            <th style="width:88px">Produk</th>
            <th>Nama</th>
            <th style="width:120px">Harga</th>
            <th style="width:80px">Jumlah</th>
            <th style="width:120px;text-align:right">Subtotal</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($items as $it): ?>
          <tr>
            <td>
              <?php if (!empty($it['gambar'])): ?>
                <img src="produk/<?= htmlspecialchars($it['gambar']) ?>" alt="<?= htmlspecialchars($it['nama']) ?>" class="item-thumb">
              <?php else: ?>
                <div style="width:72px;height:72px;border-radius:8px;background:var(--glass);display:flex;align-items:center;justify-content:center;color:var(--muted)">No Img</div>
              <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($it['nama']) ?></td>
            <td><?= rp($it['harga']) ?></td>
            <td><?= $it['jumlah'] ?></td>
            <td style="text-align:right"><?= rp($it['subtotal']) ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>

      <div style="display:flex;justify-content:flex-end;margin-top:8px;gap:12px">
        <div style="text-align:right;color:var(--muted)">
          <div>Subtotal</div>
          <div style="font-weight:800;margin-top:6px"><?= rp($total_belanja) ?></div>
        </div>
      </div>

      <div class="note" style="margin-top:12px;text-align:left">
        <strong>Catatan:</strong> Simpan struk ini sebagai bukti pembayaran. Untuk konfirmasi manual, lampirkan bukti transfer jika diperlukan.
      </div>
    </div>

    <aside class="summary">
      <div class="box">
        <div style="display:flex;justify-content:space-between;align-items:center">
          <div>
            <div style="color:var(--muted)">Ringkasan</div>
            <div style="font-weight:800;font-size:18px;margin-top:6px"><?= htmlspecialchars($u->user_nama) ?></div>
          </div>
          <div style="text-align:right">
            <div class="small" style="color:var(--muted)">Items</div>
            <div style="font-weight:800"><?= count($items) ?></div>
          </div>
        </div>

        <div style="margin-top:12px">
          <div class="row"><div class="small">Subtotal</div><div><?= rp($total_belanja) ?></div></div>
          <div class="row"><div class="small">Ongkir</div><div><?= rp($ongkir) ?></div></div>
          <div class="row" style="border-top:1px dashed rgba(10,30,60,0.04);padding-top:10px;margin-top:8px">
            <div class="small">Total</div><div class="total"><?= rp($total_pembayaran) ?></div>
          </div>
        </div>

        <div class="pay-info" style="margin-top:14px">
          <div class="title">Layanan Pembayaran</div>
          <div class="detail"><?= htmlspecialchars($metode_label) ?><?php if(!empty($metode_detail)) echo ' — '.htmlspecialchars($metode_detail); ?></div>
        </div>

        <div class="qr" aria-hidden="false" style="margin-top:12px">
          <img src="https://api.qrserver.com/v1/create-qr-code/?data=<?= urlencode($t->id_transaksi) ?>&amp;size=170x170" alt="QR <?= htmlspecialchars($t->id_transaksi) ?>">
          <div style="color:var(--muted);font-size:13px;margin-top:8px">Scan untuk verifikasi — ID <?= htmlspecialchars($t->id_transaksi) ?></div>
        </div>

        <div class="actions">
          <button class="btn btn-primary" onclick="printStruk()">🖨️ Cetak</button>
          <button class="btn btn-ghost" id="downloadPdf">📄 Simpan PDF</button>
          <a class="btn btn-ghost" href="index.php">🏠 Beranda</a>
        </div>
      </div>
    </aside>
  </div>

  <div style="text-align:center;margin-top:18px;color:var(--muted);font-size:13px">
    Terima kasih telah berbelanja di <strong>WarungRizqi</strong> 💚
  </div>
</div>

<!-- html2pdf -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
function printStruk(){
  window.print();
}

document.getElementById('downloadPdf').addEventListener('click', function(){
  const element = document.getElementById('strukArea');
  const opt = {
    margin:       0.25,
    filename:     'Struk_<?= preg_replace('/[^A-Za-z0-9_-]/','', $t->id_transaksi) ?>.pdf',
    image:        { type: 'jpeg', quality: 0.98 },
    html2canvas:  { scale: 2, useCORS: true },
    jsPDF:        { unit: 'in', format: 'a4', orientation: 'portrait' }
  };
  // show small feedback
  this.disabled = true;
  this.textContent = 'Membuat PDF...';
  setTimeout(()=>{
    html2pdf().set(opt).from(element).save().then(()=>{
      document.getElementById('downloadPdf').disabled = false;
      document.getElementById('downloadPdf').textContent = '📄 Simpan PDF';
    });
  }, 200);
});
</script>
</body>
</html>

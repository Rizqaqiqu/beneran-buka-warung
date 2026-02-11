<?php
session_start();
include 'db.php';

// pastikan ada checkout
if (!isset($_SESSION['checkout_produk']) || empty($_SESSION['checkout_produk'])) {
    echo "<script>alert('Tidak ada checkout. Kembali ke beranda.');window.location='index.php';</script>";
    exit;
}

// normalisasi produk
$raw_list = $_SESSION['checkout_produk'] ?? [];
$produk_list = [];
foreach ($raw_list as $k => $it) {
    if (isset($it['product_id'])) {
        $produk_list[] = $it;
    } elseif (is_array($it) && isset($it[0]['product_id'])) {
        foreach ($it as $sub) $produk_list[] = $sub;
    } elseif (is_array($it)) {
        $produk_list[] = $it;
    }
}

// data pembeli dari session
$user_nama   = $_SESSION['user_nama']   ?? '';
$user_email  = $_SESSION['user_email']  ?? '';
$user_telp   = $_SESSION['user_telp']   ?? '';
$user_alamat = $_SESSION['user_alamat'] ?? '';
$metode      = $_SESSION['metode_pembayaran'] ?? $_SESSION['metode'] ?? 'Lainnya';
$catatan     = $_SESSION['catatan'] ?? '';

// hitung subtotal menggunakan harga DB bila tersedia
$subtotal = 0;
$qty_total = 0;
$normalized = [];
foreach ($produk_list as $p) {
    $product_id = (int)($p['product_id'] ?? 0);
    $qty = max(1, (int)($p['jumlah'] ?? 1));
    $price = 0;
    $name = $p['product_name'] ?? '';
    $image = $p['product_image'] ?? '';
    if ($product_id > 0) {
        $res = mysqli_query($conn, "SELECT product_price, product_name, product_image FROM tb_product WHERE product_id = {$product_id} LIMIT 1");
        if ($res && ($row = mysqli_fetch_assoc($res))) {
            $price = (float)$row['product_price'];
            if (empty($name)) $name = $row['product_name'];
            if (empty($image)) $image = $row['product_image'];
        } else {
            $price = (float)($p['product_price'] ?? 0);
        }
    } else {
        $price = (float)($p['product_price'] ?? 0);
    }
    $subtotal_item = $price * $qty;
    $subtotal += $subtotal_item;
    $qty_total += $qty;
    $normalized[] = [
        'product_id' => $product_id,
        'product_name' => $name,
        'product_image' => $image,
        'product_price' => $price,
        'jumlah' => $qty,
        'subtotal' => $subtotal_item
    ];
}
$produk_list = $normalized;

// ongkir otomatis
function calculate_ongkir(float $subtotal, int $qty_total, string $address = ''): float {
    if ($subtotal >= 300000) return 0.0;
    if ($subtotal >= 150000) return 8000.0;
    $base = 12000.0;
    $per_item = 2500.0;
    $cost = $base + ($per_item * max(0, $qty_total - 1));
    $addrLower = strtolower($address);
    if ($addrLower && (strpos($addrLower, 'kepulauan') !== false || strpos($addrLower, 'kabupaten kepulauan') !== false)) {
        $cost += 15000.0;
    }
    return round($cost);
}

// use chosen ongkir if set in session (dari checkout page), else calculate
$chosen_ongkir = isset($_SESSION['ongkir']) ? (float)$_SESSION['ongkir'] : null;
$calculated_ongkir = calculate_ongkir($subtotal, $qty_total, $user_alamat);
$ongkir = ($chosen_ongkir !== null) ? $chosen_ongkir : $calculated_ongkir;

$total = $subtotal + $ongkir;
$id_transaksi = 'TRX' . date('YmdHis') . rand(10,99);

// simpan transaksi (sederhana)
$hasError = false;
mysqli_begin_transaction($conn);

$stmt = mysqli_prepare($conn, "
    INSERT INTO tb_pembelian
    (user_nama, user_email, user_telp, user_alamat, product_id, jumlah, metode, tanggal, id_transaksi, catatan)
    VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?)
");
if (!$stmt) {
    $hasError = true;
} else {
    foreach ($produk_list as $p) {
        $product_id = (int)$p['product_id'];
        $jumlah = (int)$p['jumlah'];

        // prepare bound variables (mysqli requires variables)
        $user_nama_db   = $user_nama;
        $user_email_db  = $user_email;
        $user_telp_db   = $user_telp;
        $user_alamat_db = $user_alamat;
        $metode_db      = $metode;
        $catatan_db     = $catatan;
        mysqli_stmt_bind_param($stmt, 'ssssiisss',
            $user_nama_db,
            $user_email_db,
            $user_telp_db,
            $user_alamat_db,
            $product_id,
            $jumlah,
            $metode_db,
            $id_transaksi,
            $catatan_db
        );
        if (!mysqli_stmt_execute($stmt)) {
            $hasError = true;
            break;
        }
    }
    mysqli_stmt_close($stmt);
}

// update kolom total/ongkir jika tersedia (cek kolom dinamis)
if (!$hasError) {
    $cols = [];
    $resCols = mysqli_query($conn, "SHOW COLUMNS FROM tb_pembelian");
    if ($resCols) {
        while ($r = mysqli_fetch_assoc($resCols)) $cols[] = $r['Field'];
    }
    $updates = [];
    if (in_array('total_harga', $cols)) {
        $updates[] = "total_harga = '" . mysqli_real_escape_string($conn, (string)$total) . "'";
    }
    if (in_array('ongkir', $cols)) {
        $updates[] = "ongkir = '" . mysqli_real_escape_string($conn, (string)$ongkir) . "'";
    }
    if (!empty($updates)) {
        $sql = "UPDATE tb_pembelian SET " . implode(', ', $updates) . " WHERE id_transaksi = '" . mysqli_real_escape_string($conn, $id_transaksi) . "'";
        mysqli_query($conn, $sql);
    }
}

if ($hasError) {
    mysqli_rollback($conn);
    echo "<script>alert('Terjadi kesalahan saat menyimpan transaksi.');window.location='keranjang.php';</script>";
    exit;
}
mysqli_commit($conn);

// bersihkan session
unset($_SESSION['checkout_produk']);
unset($_SESSION['checkout_sementara']);
unset($_SESSION['keranjang']);

// helper format
function rp($n){ return 'Rp. ' . number_format($n,0,',','.'); }
$eta_days = ($ongkir == 0) ? 0 : ($ongkir <= 8000 ? 2 : 3);
$eta_text = $eta_days === 0 ? 'Ambil di toko / Gratis (hari ini)' : 'Estimasi ' . $eta_days . ' hari kerja';

// ambil metode dan detail dari session untuk ditampilkan sebagai layanan yang digunakan
$chosen_method = $_SESSION['metode_pembayaran'] ?? ($_SESSION['metode'] ?? $metode ?? 'Transfer Bank');
$chosen_detail = $_SESSION['metode_detail'] ?? '';
if (stripos($chosen_method, 'transfer') !== false || stripos($chosen_method, 'bank') !== false) {
    $chosen_label = 'Bank: ' . (!empty($chosen_detail) ? htmlspecialchars($chosen_detail) : 'BCA — 1234567890');
} elseif (stripos($chosen_method, 'e-wallet') !== false) {
    $chosen_label = 'Provider: ' . (!empty($chosen_detail) ? htmlspecialchars($chosen_detail) : 'OVO / GoPay / Dana');
} else {
    $chosen_label = htmlspecialchars($chosen_method);
}

?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Pembayaran — WarungRizqi</title>

<!-- Google Fonts + Feather icons -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&family=Poppins:wght@600&display=swap" rel="stylesheet">
<script src="https://unpkg.com/feather-icons"></script>

<link rel="stylesheet" href="css/style.css">
<style>
:root{
  --bg-1: #f6fbff;
  --accent: #0066ff;
  --muted: #6b7280;
  --card: rgba(255,255,255,0.82);
  --glass: rgba(255,255,255,0.45);
  --success: #16a34a;
}
*{box-sizing:border-box}
body{
  font-family: Inter, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
  background: radial-gradient(1200px 600px at 10% 10%, rgba(99,102,241,0.06), transparent 8%),
              radial-gradient(1000px 500px at 90% 90%, rgba(16,185,129,0.04), transparent 8%),
              var(--bg-1);
  margin:0; padding:34px;
  -webkit-font-smoothing:antialiased; -moz-osx-font-smoothing:grayscale;
  color:#0f172a;
}
.container{max-width:1180px;margin:0 auto}
.header{
  display:flex;align-items:center;gap:16px;margin-bottom:18px;
}
.brand{
  display:flex;align-items:center;gap:14px;background:linear-gradient(135deg,#4facfe,#00f2fe);
  padding:12px;border-radius:12px;color:#fff;box-shadow:0 12px 30px rgba(8,70,173,0.09);
}
.brand .logo{font-weight:700;font-family:Poppins,sans-serif;font-size:18px}
.page-title{font-size:18px;margin:0}
.page-sub{color:var(--muted);font-size:13px;margin-top:4px}

/* layout */
.layout{display:grid;grid-template-columns:1fr 420px;gap:20px}
@media(max-width:980px){ .layout{grid-template-columns:1fr} }

/* left card */
.card{
  background: linear-gradient(180deg, rgba(255,255,255,0.9), rgba(250,253,255,0.75));
  border-radius:14px;padding:18px;border:1px solid rgba(8,70,173,0.06);
  box-shadow: 0 10px 30px rgba(15,23,42,0.05);
  overflow:hidden;
}
.order-steps{display:flex;gap:10px;align-items:center;margin-bottom:12px}
.step{
  background:#fff;padding:8px 12px;border-radius:999px;border:1px solid rgba(10,30,60,0.04);
  font-weight:600;color:var(--muted);font-size:13px;display:flex;gap:8px;align-items:center;
  transition:transform .16s,box-shadow .16s;
}
.step.active{background:linear-gradient(90deg,var(--accent),#4facfe);color:#fff;box-shadow:0 10px 30px rgba(0,102,255,0.12);transform:translateY(-3px)}

/* product list */
.products{display:flex;flex-direction:column;gap:12px}
.product{
  display:flex;gap:12px;align-items:center;padding:12px;border-radius:12px;background:linear-gradient(180deg,#fff,#fbfdff);
  border:1px solid rgba(10,30,60,0.04);transition:transform .16s,box-shadow .16s;
}
.product:hover{transform:translateY(-6px);box-shadow:0 18px 40px rgba(8,70,173,0.06)}
.thumb{width:86px;height:86px;border-radius:10px;background:#f4f7ff;display:flex;align-items:center;justify-content:center;overflow:hidden}
.thumb img{width:100%;height:100%;object-fit:cover}
.prod-meta{flex:1}
.prod-meta h4{margin:0;font-size:15px}
.prod-meta p{margin:6px 0 0;color:var(--muted);font-size:13px}
.qty{min-width:96px;text-align:right;font-weight:700;color:var(--accent)}

/* right sidebar */
.sidebar{position:sticky;top:24px;height:max-content}
.summary{background:linear-gradient(180deg,#fff,#fbfdff);padding:16px;border-radius:12px;border:1px solid rgba(10,30,60,0.04)}
.summary .row{display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px dashed rgba(10,30,60,0.04)}
.summary .row.total{font-size:20px;font-weight:800;color:var(--accent);border-bottom:none}
.pay-card{
  display:flex;justify-content:space-between;align-items:center;padding:10px;border-radius:10px;border:1px solid rgba(10,30,60,0.04);background:#fff;
}

/* badges and small */
.badge{display:inline-block;padding:6px 10px;border-radius:999px;background:rgba(0,102,255,0.08);color:var(--accent);font-weight:700}
.small{font-size:13px;color:var(--muted)}

/* toast */
.toast {
  position:fixed;right:22px;bottom:24px;background:rgba(2,6,23,0.95);color:#fff;padding:10px 14px;border-radius:10px;box-shadow:0 8px 30px rgba(2,6,23,0.3);opacity:0;transform:translateY(12px);transition:all .28s;
}
.toast.show{opacity:1;transform:translateY(0)}

/* confetti pieces (improved) */
.confetti-piece{position:fixed;z-index:9999;pointer-events:none;border-radius:2px;will-change:transform,opacity}
@keyframes confettiFall {
  0%{transform:translateY(-10vh) rotate(0) translateX(0);opacity:1}
  100%{transform:translateY(110vh) rotate(720deg) translateX(30vw);opacity:0}
}

/* small responsiveness */
@media(max-width:640px){
  .thumb{width:64px;height:64px}
  .qty{text-align:left}
}
</style>
</head>
<body>
<div class="container">
  <div class="header">
    <div class="brand"><div class="logo">WR</div><div>
      <div style="font-weight:700">WarungRizqi</div>
      <div class="small" style="color:rgba(255,255,255,0.92);font-size:12px">Terima kasih, pesananmu diterima</div>
    </div></div>
    <div style="margin-left:12px">
      <h2 class="page-title">Pembayaran</h2>
      <div class="page-sub">Ringkasan dan instruksi pembayaran — simpan bukti pembayaran</div>
    </div>
  </div>

  <div class="layout">
    <div class="card">
      <div class="order-steps" aria-hidden="true">
        <div class="step active"><i data-feather="shopping-cart" style="width:14px;height:14px"></i> Checkout</div>
        <div class="step"><i data-feather="clock" style="width:14px;height:14px"></i> Proses</div>
        <div class="step"><i data-feather="check-circle" style="width:14px;height:14px"></i> Selesai</div>
      </div>

      <div class="products" aria-live="polite">
        <?php foreach ($produk_list as $p):
            $foto = !empty($p['product_image']) ? 'produk/' . htmlspecialchars($p['product_image']) : 'images/default.png';
        ?>
        <div class="product" role="group" aria-label="<?= htmlspecialchars($p['product_name']); ?>">
          <div class="thumb"><img src="<?= $foto; ?>" alt="<?= htmlspecialchars($p['product_name']); ?>"></div>
          <div class="prod-meta">
            <h4><?= htmlspecialchars($p['product_name']); ?></h4>
            <p class="small"><?= (int)$p['jumlah']; ?> × <?= rp($p['product_price']); ?> &middot; subtotal <?= rp($p['subtotal']); ?></p>
          </div>
          <div class="qty"><?= rp($p['subtotal']); ?></div>
        </div>
        <?php endforeach; ?>
      </div>

      <div style="margin-top:14px;display:flex;gap:12px;align-items:center;justify-content:space-between">
        <div>
          <div class="small">ID Transaksi</div>
          <div style="font-weight:800" id="trx"><?= htmlspecialchars($id_transaksi); ?></div>
        </div>
        <div style="text-align:right">
          <div class="small">Total Items</div>
          <div style="font-weight:800"><?= $qty_total; ?></div>
        </div>
      </div>

      <!-- Rapi: Detail Pemesan yang lebih informatif -->
      <div style="margin-top:18px">
        <h3 style="margin:0 0 10px 0">Detail Pemesan</h3>
        <div style="display:grid;grid-template-columns:1fr 220px;gap:12px;align-items:start">
          <div style="background:linear-gradient(180deg,rgba(255,255,255,0.9),rgba(250,253,255,0.8));padding:14px;border-radius:12px;border:1px solid rgba(10,30,60,0.04);">
            <div style="display:flex;gap:12px;align-items:flex-start">
              <div style="width:56px;height:56px;border-radius:10px;background:#f4f7ff;display:flex;align-items:center;justify-content:center;font-weight:700;color:var(--accent)">IMG</div>
              <div style="flex:1">
                <div style="font-size:16px;font-weight:800"><?= htmlspecialchars($user_nama ?: 'Pembeli'); ?></div>
                <div class="small" style="margin-top:6px;color:var(--muted)"><?= htmlspecialchars($user_email ?: '-'); ?></div>
                <div class="small" style="margin-top:6px;color:var(--muted)"><?= htmlspecialchars($user_telp ?: '-'); ?></div>
              </div>
            </div>
            <div style="margin-top:12px;padding-top:12px;border-top:1px dashed rgba(10,30,60,0.04);color:var(--muted)">
              <div style="font-weight:700;margin-bottom:6px">Alamat Pengiriman</div>
              <div style="white-space:pre-line"><?= nl2br(htmlspecialchars($user_alamat ?: '-')); ?></div>
            </div>
          </div>
          <div style="display:flex;flex-direction:column;gap:10px">
            <div style="background:linear-gradient(180deg,#fff,#fbfdff);padding:12px;border-radius:12px;border:1px solid rgba(10,30,60,0.04);">
              <div class="small" style="color:var(--muted)">Metode Pilihan</div>
              <div style="font-weight:800;margin-top:6px"><?= htmlspecialchars($chosen_method); ?></div>
              <div class="small" style="margin-top:6px;color:var(--muted)"><?= $chosen_label; ?></div>
            </div>
            <div style="background:linear-gradient(180deg,#fff,#fbfdff);padding:12px;border-radius:12px;border:1px solid rgba(10,30,60,0.04);">
              <div class="small" style="color:var(--muted)">Estimasi Pengiriman</div>
              <div style="font-weight:700;margin-top:6px"><?= htmlspecialchars($eta_text); ?></div>
            </div>
          </div>
        </div>
      </div>

    </div>

    <aside class="sidebar">
      <div class="summary">
        <div style="display:flex;align-items:center;justify-content:space-between">
          <div><strong>Ringkasan Pesanan</strong><div class="small">Periksa sebelum membayar</div></div>
          <div class="badge">#{<?= htmlspecialchars($id_transaksi); ?>}</div>
        </div>

        <div style="margin-top:12px">
          <div class="row"><div class="small">Subtotal</div><div class="small"><?= rp($subtotal); ?></div></div>
          <div class="row"><div class="small">Ongkir</div><div class="small" id="ongkirText"><?= rp($ongkir); ?></div></div>
          <div class="row total"><div>Grand Total</div><div id="grandText"><?= rp($total); ?></div></div>
        </div>

        <div style="margin-top:12px" class="small">Metode Pembayaran (dipilih di checkout)</div>

        <!-- tampilkan metode yang sudah dipilih (read-only) -->
        <div style="margin-top:8px">
          <div class="pay-card" style="cursor:default" aria-hidden="true">
            <div>
              <div style="font-weight:700"><?= htmlspecialchars($chosen_method); ?></div>
              <?php if (!empty($chosen_detail)): ?>
                <div class="small" style="margin-top:4px;color:var(--muted)"><?= htmlspecialchars($chosen_detail); ?></div>
              <?php endif; ?>
            </div>
            <div style="align-self:center"><i data-feather="credit-card" style="color:var(--accent)"></i></div>
          </div>
        </div>

        <div style="margin-top:14px;display:flex;gap:8px">
          <a class="btn btn-ghost" href="index.php">Kembali Belanja</a>
          <a class="btn btn-primary" href="struk.php?id=<?= urlencode($id_transaksi); ?>" target="_blank">Download Struk</a>
          <button id="printBtn" class="btn btn-primary" style="background:linear-gradient(90deg,var(--accent),#4facfe);">Cetak Struk</button>
        </div>

        <!-- instruksi otomatis berdasarkan metode session -->
        <div style="margin-top:12px" class="small payment-instr" id="instrBox">
          <?php if (stripos($chosen_method,'transfer') !== false || stripos($chosen_method,'bank') !== false): ?>
            <div><strong>Transfer ke</strong></div>
            <?php if (!empty($chosen_detail)): ?>
              <div style="margin-top:6px"><?= htmlspecialchars($chosen_detail); ?></div>
            <?php else: ?>
              <div style="margin-top:6px">Bank BCA — <span class="copy" data-copy="1234567890">1234567890</span> (a.n. WarungRizqi)</div>
            <?php endif; ?>
            <div style="margin-top:8px">Jumlah: <strong id="payAmount"><?= rp($total); ?></strong></div>
            <div class="small" style="margin-top:8px">Setelah transfer, simpan bukti dan hubungi penjual jika perlu.</div>

          <?php elseif (stripos($chosen_method,'e-wallet') !== false): ?>
            <div><strong>E-Wallet</strong></div>
            <?php if (!empty($chosen_detail)): ?>
              <div style="margin-top:6px"><?= htmlspecialchars($chosen_detail); ?> — <span class="copy" data-copy="081234567890">081234567890</span></div>
            <?php else: ?>
              <div style="margin-top:6px">Pindai QR / kirim ke <span class="copy" data-copy="081234567890">081234567890</span></div>
            <?php endif; ?>
            <div class="small" style="margin-top:8px">Jumlah: <strong id="payAmount"><?= rp($total); ?></strong></div>

          <?php else: ?>
            <div><strong><?= htmlspecialchars($chosen_method); ?></strong></div>
            <div class="small" style="margin-top:6px">Instruksi: Siapkan pembayaran sesuai metode yang dipilih pada checkout.</div>
          <?php endif; ?>
        </div>
      </div>
    </aside>
  </div>
</div>

<div id="toast" class="toast" role="status" aria-live="polite"></div>

<script>
// feather icons
feather.replace();

// helpers
function toast(msg, time = 2500){
  const t = document.getElementById('toast');
  t.textContent = msg; t.classList.add('show');
  setTimeout(()=> t.classList.remove('show'), time);
}

// copy to clipboard
document.querySelectorAll('.copy').forEach(el=>{
  el.style.cursor='pointer';
  el.addEventListener('click', ()=>{
    const txt = el.getAttribute('data-copy') || el.textContent;
    navigator.clipboard?.writeText(txt);
    toast('Disalin ke clipboard');
  });
});

// print struk
const printBtn = document.getElementById('printBtn');
if (printBtn) {
  printBtn.addEventListener('click', function(){
    const w = window.open('struk.php?id=<?= urlencode($id_transaksi); ?>','_blank');
    setTimeout(()=> { try { w && w.print && w.print(); } catch(e){} }, 700);
  });
}

// improved confetti (kept for visual)
function launchConfetti(count=24){
  for(let i=0;i<count;i++){
    const el = document.createElement('div');
    el.className = 'confetti-piece';
    const w = 6 + Math.random()*12;
    el.style.width = w+'px';
    el.style.height = (w*0.6)+'px';
    el.style.left = (10 + Math.random()*80) + 'vw';
    el.style.top = (-10 - Math.random()*10) + 'vh';
    el.style.background = ['#ff6b6b','#ffd166','#6bcB77','#4facfe','#845ef7'][Math.floor(Math.random()*5)];
    el.style.transform = 'rotate('+ (Math.random()*360) +'deg)';
    el.style.opacity = 1;
    el.style.zIndex = 9999;
    el.style.borderRadius = (Math.random()>0.6? '2px' : '50%');
    const dur = 1200 + Math.random()*1000;
    el.style.transition = `transform ${dur}ms cubic-bezier(.2,.7,.2,1), opacity ${dur}ms linear`;
    document.body.appendChild(el);
    setTimeout(()=> {
      el.style.transform = `translateY(${110 + Math.random()*40}vh) translateX(${(-40 + Math.random()*80)}vw) rotate(${720 + Math.random()*360}deg)`;
      el.style.opacity = 0;
    }, 20 + Math.random()*220);
    setTimeout(()=> el.remove(), dur+300);
  }
}

// animate grand total number
(function animateGrand(){
  const el = document.getElementById('grandText');
  if(!el) return;
  const txt = el.textContent || '';
  const num = parseInt(txt.replace(/[^0-9]/g,'')) || 0;
  const dur = 900;
  const st = performance.now();
  function step(now){
    const t = Math.min(1,(now-st)/dur);
    const ease = (--t)*t*t+1;
    const val = Math.round(num*ease);
    el.textContent = 'Rp. ' + val.toLocaleString('id-ID');
    if(now-st < dur) requestAnimationFrame(step);
  }
  requestAnimationFrame(step);
})();

// visual confetti on load if you want
document.addEventListener('DOMContentLoaded', function(){
  // small pulse on transaction id
  const trx = document.getElementById('trx');
  if (trx) trx.animate([{transform:'scale(1)'},{transform:'scale(1.04)'},{transform:'scale(1)'}],{duration:900,iterations:1,easing:'ease-out'});
  // optional celebration
  // launchConfetti(18);
});
</script>
</body>
</html>

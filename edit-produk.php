<?php 
session_start();
include 'db.php';
if($_SESSION['status_login'] != true){
    echo '<script>window.location="login.php"</script>';
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$produk_q = mysqli_query($conn, "SELECT * FROM tb_product WHERE product_id = '{$id}' LIMIT 1");
if (!$produk_q || mysqli_num_rows($produk_q) == 0){
    echo '<script>window.location="data-produk.php"</script>';
    exit;
}
$p = mysqli_fetch_assoc($produk_q);

$err = '';
$success = '';
if(isset($_POST['submit'])){

    // sanitize input
    $kategori 	= mysqli_real_escape_string($conn, trim($_POST['kategori'] ?? ''));
    $nama 		= mysqli_real_escape_string($conn, trim($_POST['nama'] ?? ''));
    $harga 		= mysqli_real_escape_string($conn, trim($_POST['harga'] ?? '0'));
    $deskripsi 	= mysqli_real_escape_string($conn, trim($_POST['deskripsi'] ?? ''));
    $status 	= mysqli_real_escape_string($conn, trim($_POST['status'] ?? '0'));
    $foto_old 	= mysqli_real_escape_string($conn, trim($_POST['foto'] ?? $p['product_image']));
    $stok       = (int)($_POST['stok'] ?? 0);

    // file upload
    $namagambar = $foto_old;
    if (!empty($_FILES['gambar']['name'])) {
        $filename = $_FILES['gambar']['name'];
        $tmp_name = $_FILES['gambar']['tmp_name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif','webp'];

        if (!in_array($ext, $allowed)) {
            $err = 'Format file tidak diizinkan. (jpg, jpeg, png, gif, webp)';
        } else {
            $newname = 'produk_' . time() . '_' . rand(100,999) . '.' . $ext;
            $target = __DIR__ . '/produk/' . $newname;
            if (move_uploaded_file($tmp_name, $target)) {
                // remove old file if exists and not empty
                if (!empty($foto_old) && file_exists(__DIR__ . '/produk/' . $foto_old)) {
                    @unlink(__DIR__ . '/produk/' . $foto_old);
                }
                $namagambar = $newname;
            } else {
                $err = 'Gagal mengunggah gambar.';
            }
        }
    }

    // if no error, update
    if ($err === '') {
        $update_sql = "UPDATE tb_product SET 
            category_id = '". $kategori ."',
            product_name = '". $nama ."',
            product_price = '". $harga ."',
            product_description = '". $deskripsi ."',
            product_image = '". $namagambar ."',
            product_status = '". $status ."',
            product_stock = '". $stok ."'
            WHERE product_id = '". $id ."'";
        $update = mysqli_query($conn, $update_sql);
        if ($update) {
            $success = 'Ubah data berhasil.';
            echo '<script>alert("Ubah data berhasil")</script>';
            echo '<script>window.location="data-produk.php"</script>';
            exit;
        } else {
            $err = 'Gagal memperbarui data. ' . mysqli_error($conn);
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Edit Produk — WarungRizqi</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
<style>
:root{
  --bg:#071022; --card-elev: 0 10px 30px rgba(2,6,23,0.45);
  --accent:#4facfe; --muted:#9aa6b2; --glass: rgba(255,255,255,0.03);
}
*{box-sizing:border-box}
body{margin:0;font-family:Inter,system-ui,Arial;background:
  radial-gradient(900px 400px at 10% 10%, rgba(79,70,229,0.06), transparent 6%),
  #071022;color:#e6eef8;padding:28px;-webkit-font-smoothing:antialiased}

/* BACKGROUND ANIMATION: soft moving gradient blobs */
.container{max-width:980px;margin:0 auto;position:relative;z-index:1}

/* full-screen animated layer behind content */
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
  filter:blur(84px);
  opacity:0.16;
  transform:translate3d(0,0,0);
  will-change:transform;
  mix-blend-mode:screen;
}

/* individual blobs: size, color, start position, animation */
.bg-anim .b1{
  width:640px;height:640px;
  left:-12%;top:-20%;
  background:radial-gradient(circle at 20% 30%, rgba(124,58,237,0.95), rgba(6,182,212,0.85));
  animation:blobFloat1 16s ease-in-out infinite;
}
.bg-anim .b2{
  width:460px;height:460px;
  right:-8%;top=6%;
  background:radial-gradient(circle at 60% 40%, rgba(16,185,129,0.95), rgba(79,70,229,0.85));
  animation:blobFloat2 20s ease-in-out infinite;
  opacity:0.14;
}
.bg-anim .b3{
  width:520px;height:520px;
  left:8%;bottom:-18%;
  background:radial-gradient(circle at 30% 60%, rgba(255,186,116,0.95), rgba(255,99,132,0.85));
  animation:blobFloat3 22s ease-in-out infinite;
  opacity:0.12;
}

/* subtle movement keyframes */
@keyframes blobFloat1{
  0%{ transform: translate3d(0,0,0) scale(1); }
  25%{ transform: translate3d(50px,-30px,0) scale(1.05); }
  50%{ transform: translate3d(0,-70px,0) scale(1); }
  75%{ transform: translate3d(-30px,-10px,0) scale(0.98); }
  100%{ transform: translate3d(0,0,0) scale(1); }
}
@keyframes blobFloat2{
  0%{ transform: translate3d(0,0,0) scale(1); }
  20%{ transform: translate3d(-40px,30px,0) scale(1.03); }
  50%{ transform: translate3d(30px,50px,0) scale(1); }
  80%{ transform: translate3d(-10px,10px,0) scale(0.99); }
  100%{ transform: translate3d(0,0,0) scale(1); }
}
@keyframes blobFloat3{
  0%{ transform: translate3d(0,0,0) scale(1); }
  30%{ transform: translate3d(30px,50px,0) scale(1.04); }
  60%{ transform: translate3d(-30px,20px,0) scale(1); }
  90%{ transform: translate3d(10px,-10px,0) scale(0.97); }
  100%{ transform: translate3d(0,0,0) scale(1); }
}

/* MOVING STRIPES LAYER */
.stripes{
  position:fixed;
  inset:0;
  z-index:0;
  pointer-events:none;
  mix-blend-mode:overlay;
  opacity:0.06;
  background-image:
    linear-gradient(135deg, rgba(255,255,255,0.04) 12.5%, transparent 12.5%, transparent 50%, rgba(255,255,255,0.04) 50%, rgba(255,255,255,0.04) 62.5%, transparent 62.5%, transparent 100%);
  background-size: 96px 96px;
  animation: moveStripes 14s linear infinite;
}

/* slight color pulse to make motion feel alive */
@keyframes moveStripes{
  from { background-position: 0 0; filter: hue-rotate(0deg); }
  to   { background-position: 192px 192px; filter: hue-rotate(18deg); }
}

/* reduce motion if user prefers */
@media (prefers-reduced-motion: reduce){
  .stripes{ animation: none; opacity:0.03; }
  .bg-anim .blob{ animation: none; transform:none; filter:blur(64px); }
}

/* card */
.card{background:linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01));padding:18px;border-radius:12px;border:1px solid rgba(255,255,255,0.03);box-shadow:var(--card-elev)}

/* form */
.form-grid{display:grid;grid-template-columns:1fr 280px;gap:18px}
@media(max-width:880px){ .form-grid{grid-template-columns:1fr} }

 /* COLUMN EFFECTS: hover and focus highlighting for both form columns */
.form-grid > div,
.form-grid aside {
  border-radius:12px;
  padding:10px;
  transition: transform .18s cubic-bezier(.2,.9,.2,1), box-shadow .18s, border-color .18s;
  position:relative;
  z-index:2;
  will-change:transform,box-shadow;
  border:1px solid transparent;
}
.form-grid > div:hover,
.form-grid aside:hover,
.form-grid > div:focus-within,
.form-grid aside:focus-within {
  transform: translateY(-8px) scale(1.01);
  box-shadow: 0 22px 44px rgba(2,6,23,0.36);
  border-color: rgba(255,255,255,0.03);
}

/* subtle decorative line that animates under labels */
.label{font-weight:700;margin-bottom:6px;display:inline-block;position:relative}
.label::after{
  content:'';
  position:absolute;
  left:0;right:0;
  bottom:-6px;
  height:2px;
  background:linear-gradient(90deg,#4facfe,#00f2fe);
  transform:scaleX(0);
  transform-origin:left center;
  transition:transform .28s cubic-bezier(.2,.9,.2,1),opacity .18s;
  opacity:0;
}
.form-grid > div:hover .label::after,
.form-grid aside:hover .label::after,
.form-grid > div:focus-within .label::after,
.form-grid aside:focus-within .label::after{
  transform:scaleX(1);
  opacity:1;
}

/* inputs */
.input {width:100%;padding:10px;border-radius:10px;border:1px solid rgba(255,255,255,0.04);background:transparent;color:inherit;transition:box-shadow .14s,transform .12s}
.input:hover{transform:translateY(-3px);box-shadow:0 10px 30px rgba(2,6,23,0.08)}
.input:focus{outline:none;box-shadow:0 18px 40px rgba(79,70,229,0.08);border-color:rgba(79,70,229,0.18)}

/* buttons */
.btn{position:relative;overflow:hidden;padding:10px 14px;border-radius:10px;border:none;cursor:pointer;font-weight:800;background:linear-gradient(90deg,#4facfe,#00f2fe);color:#071022;transition:transform .14s,box-shadow .14s}
.btn.ghost{background:transparent;border:1px solid rgba(255,255,255,0.04);color:var(--muted)}
.btn:hover{transform:translateY(-6px) scale(1.02);box-shadow:0 20px 44px rgba(2,6,23,0.28)}
.btn:active{transform:translateY(-2px) scale(0.995)}
/* add chevron */
.btn::after{content:'›';position:absolute;right:12px;top:50%;transform:translateY(-50%) translateX(8px);opacity:0;color:rgba(0,0,0,0.12);font-weight:800;transition:transform .18s,opacity .18s}
.btn:hover::after{transform:translateY(-50%) translateX(0);opacity:0.9}

/* icon buttons (ghost) smaller chevron */
.btn.ghost::after{right:10px;font-size:15px}

/* preview */
.preview{background:rgba(255,255,255,0.02);padding:12px;border-radius:10px;text-align:center;border:1px solid rgba(255,255,255,0.03);transition:transform .18s,box-shadow .18s}
.preview:hover{transform:translateY(-8px);box-shadow:0 20px 40px rgba(2,6,23,0.28)}

/* helpers */
.note{font-size:13px;color:var(--muted);margin-top:8px}
.err{background:rgba(255,80,80,0.08);color:#ffdede;padding:10px;border-radius:8px;margin-bottom:10px;border:1px solid rgba(255,80,80,0.08)}
.small{font-size:13px;color:var(--muted)}

/* ripple */
.ripple{position:absolute;border-radius:50%;transform:scale(0);background:rgba(255,255,255,0.12);animation:ripple 600ms linear;pointer-events:none}
@keyframes ripple{to{transform:scale(2.8);opacity:0}}

/* responsive tweaks */
@media(max-width:880px){ .form-grid{grid-template-columns:1fr} .preview{margin-top:12px} }
</style>
</head>
<body>
  <!-- moving stripes layer (between blobs and page content) -->
  <div class="stripes" aria-hidden="true"></div>

  <!-- animated background layer (inserted) -->
  <div class="bg-anim" aria-hidden="true">
    <span class="blob b1"></span>
    <span class="blob b2"></span>
    <span class="blob b3"></span>
  </div>

<div class="container">
    <header class="header" role="banner">
        <div style="display:flex;gap:12px;align-items:center">
            <div class="logo">WR</div>
            <div>
                <div class="title">Edit Produk</div>
                <div class="sub">Ubah data produk dan gambar</div>
            </div>
        </div>
        <div>
            <button class="btn ghost" onclick="window.location='data-produk.php'">← Kembali</button>
        </div>
    </header>

    <section class="card" role="main">
        <?php if ($err): ?><div class="err" role="alert"><?= htmlspecialchars($err) ?></div><?php endif; ?>

        <form class="form-grid" action="" method="POST" enctype="multipart/form-data" novalidate>
            <div>
                <div class="label">Kategori</div>
                <select name="kategori" class="input" required>
                    <option value="">--Pilih--</option>
                    <?php 
                        $kategori_q = mysqli_query($conn, "SELECT * FROM tb_category ORDER BY category_name ASC");
                        while($r = mysqli_fetch_assoc($kategori_q)){
                            $sel = ($r['category_id'] == $p['category_id']) ? 'selected' : '';
                            echo '<option value="'.htmlspecialchars($r['category_id']).'" '.$sel.'>'.htmlspecialchars($r['category_name']).'</option>';
                        }
                    ?>
                </select>

                <div style="margin-top:12px">
                    <div class="label">Nama Produk</div>
                    <input name="nama" class="input" type="text" value="<?= htmlspecialchars($p['product_name']) ?>" required>
                </div>

                <div style="display:flex;gap:12px;margin-top:12px">
                    <div style="flex:1">
                        <div class="label">Harga</div>
                        <input name="harga" class="input" type="text" value="<?= htmlspecialchars($p['product_price']) ?>" required>
                    </div>
                    <div style="width:160px">
                        <div class="label">Stok</div>
                        <input name="stok" class="input" type="number" min="0" value="<?= htmlspecialchars($p['product_stock'] ?? ($p['product_qty'] ?? 0)) ?>">
                    </div>
                </div>

                <div style="margin-top:12px">
                    <div class="label">Deskripsi</div>
                    <textarea name="deskripsi" class="input" rows="6"><?= htmlspecialchars($p['product_description'] ?? $p['product_desc'] ?? '') ?></textarea>
                </div>

                <div style="margin-top:12px">
                    <div class="label">Status</div>
                    <select name="status" class="input">
                        <option value="1" <?= ($p['product_status']==1)?'selected':'' ?>>Aktif</option>
                        <option value="0" <?= ($p['product_status']==0)?'selected':'' ?>>Tidak Aktif</option>
                    </select>
                </div>

                <input type="hidden" name="foto" value="<?= htmlspecialchars($p['product_image']) ?>">
                
                <div style="margin-top:12px;display:flex;gap:8px">
                    <button type="submit" name="submit" class="btn">Simpan</button>
                    <button type="button" class="btn ghost" onclick="window.location='data-produk.php'">Batal</button>
                </div>

                <div class="note">Tip: Gunakan gambar ukuran wajar untuk performa. Format yang diterima: jpg, jpeg, png, gif, webp.</div>
            </div>

            <aside>
                <div class="label">Gambar Produk</div>
                <div class="preview">
                    <?php if (!empty($p['product_image']) && file_exists(__DIR__.'/produk/'.$p['product_image'])): ?>
                        <img src="produk/<?= htmlspecialchars($p['product_image']) ?>" alt="" style="max-width:100%;border-radius:8px">
                    <?php else: ?>
                        <div class="small">Tidak ada gambar</div>
                    <?php endif; ?>
                </div>

                <div style="margin-top:12px">
                    <input type="file" name="gambar" accept="image/*" class="input">
                </div>

                <div class="small" style="margin-top:12px">Preview akan ditampilkan setelah menyimpan perubahan.</div>
            </aside>
        </form>
    </section>

    <footer style="margin-top:18px;text-align:center;color:var(--muted)"><small>Copyright &copy; 2025 - WarungRizqi.</small></footer>
</div>

<!-- CKEditor -->
<script src="https://cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>
<script>CKEDITOR.replace( 'deskripsi' );</script>

<script>
// ripple helper
function rippleClick(e, el){
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

// attach ripple to buttons
document.querySelectorAll('.btn, .btn.ghost').forEach(btn=>{
  btn.addEventListener('click', function(e){
    rippleClick(e, this);
  });
  btn.addEventListener('keydown', function(e){
    if (e.key === 'Enter' || e.key === ' ') {
      e.preventDefault();
      this.click();
    }
  });
});

// make columns clickable to focus first input (small UX helper)
document.querySelectorAll('.form-grid > div, .form-grid aside').forEach(col=>{
  col.addEventListener('click', function(e){
    // avoid stealing focus when clicking buttons or inputs
    if (e.target.closest('button, input, textarea, select')) return;
    const input = this.querySelector('input, select, textarea');
    if (input) input.focus();
  });
});

/* optional: tiny phase-shift for organic motion (respects reduced-motion) */
if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
  const blobs = document.querySelectorAll('.bg-anim .blob');
  blobs.forEach((b, i) => b.style.animationDelay = (i * 0.9) + 's');
}

/* PARALLAX: gentle pointer-based motion for blobs (performance-friendly) */
(function(){
  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

  const blobs = document.querySelectorAll('.bg-anim .blob');
  if (!blobs.length) return;

  let mouseX = 0, mouseY = 0;
  let lx = 0, ly = 0;

  window.addEventListener('pointermove', (e) => {
    const vw = window.innerWidth;
    const vh = window.innerHeight;
    // normalize to -1 .. 1
    mouseX = (e.clientX / vw - 0.5) * 2;
    mouseY = (e.clientY / vh - 0.5) * 2;
  }, { passive: true });

  function tick(){
    // ease toward mouse
    lx += (mouseX - lx) * 0.06;
    ly += (mouseY - ly) * 0.06;

    blobs.forEach((b, i) => {
      // different depth for each blob
      const depth = (i + 1) * 8; // px multiplier
      const tx = -lx * depth; // invert for nicer parallax
      const ty = -ly * (depth * 0.6);
      // small rotation for organic feel
      const rot = lx * (i === 0 ? 2 : (i === 1 ? -1.5 : 1));
      b.style.transform = `translate3d(${tx}px, ${ty}px, 0) rotate(${rot}deg) scale(${1 + (i * 0.007)})`;
      // subtle opacity shift based on distance
      const o = 0.12 + Math.abs(lx * 0.02) + Math.abs(ly * 0.01) * (i + 1);
      b.style.opacity = Math.max(0.08, Math.min(0.26, parseFloat(getComputedStyle(b).opacity) + (o - 0.12)));
    });

    requestAnimationFrame(tick);
  }

  // start loop
  requestAnimationFrame(tick);
})();
</script>
</body>
</html>
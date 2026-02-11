<?php 
session_start();
include 'db.php';
if($_SESSION['status_login'] != true){
    echo '<script>window.location="login.php"</script>';
    exit;
}

$err = '';
if (isset($_POST['submit'])) {
    $kategori = mysqli_real_escape_string($conn, trim($_POST['kategori'] ?? ''));
    $nama     = mysqli_real_escape_string($conn, trim($_POST['nama'] ?? ''));
    $harga    = mysqli_real_escape_string($conn, trim($_POST['harga'] ?? '0'));
    $deskripsi= mysqli_real_escape_string($conn, trim($_POST['deskripsi'] ?? ''));
    $status   = mysqli_real_escape_string($conn, trim($_POST['status'] ?? ''));

    if ($kategori === '' || $nama === '' || $harga === '' || $status === '') {
        $err = 'Isi semua field yang wajib.';
    } else {
        // file upload handling
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
                if (!move_uploaded_file($tmp_name, $target)) {
                    $err = 'Gagal mengunggah gambar.';
                }
            }
        } else {
            $err = 'Silakan pilih gambar produk.';
        }

        if ($err === '') {
            $insert = mysqli_query($conn, "INSERT INTO tb_product (category_id, product_name, product_price, product_description, product_image, product_status) VALUES (
                '". $kategori ."',
                '". $nama ."',
                '". $harga ."',
                '". $deskripsi ."',
                '". ($newname ?? '') ."',
                '". $status ."'
            )");
            if ($insert) {
                echo '<script>alert("Tambah data berhasil")</script>';
                echo '<script>window.location="data-produk.php"</script>';
                exit;
            } else {
                $err = 'Gagal menambah data. '. mysqli_error($conn);
            }
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Tambah Produk — WarungRizqi</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
<style>
:root{
  --bg:#071022; --muted:#9aa6b2; --accent-start:#4facfe; --accent-end:#00f2fe;
}
*{box-sizing:border-box}
html,body{height:100%;margin:0;font-family:Inter,system-ui,Arial;background:
  radial-gradient(900px 400px at 10% 10%, rgba(79,70,229,0.06), transparent 6%),
  #071022;color:#e6eef8;-webkit-font-smoothing:antialiased;padding:28px;}
.container{max-width:980px;margin:0 auto;position:relative;z-index:1}

/* animated layers */
.bg-anim{position:fixed;inset:0;z-index:0;pointer-events:none;overflow:hidden}
.bg-anim .blob{position:absolute;border-radius:50%;filter:blur(84px);opacity:0.12;mix-blend-mode:screen;will-change:transform}
.bg-anim .b1{width:640px;height:640px;left:-12%;top:-20%;background:radial-gradient(circle at 20% 30%, rgba(124,58,237,0.95), rgba(6,182,212,0.85));animation:bf1 16s ease-in-out infinite}
.bg-anim .b2{width:460px;height:460px;right:-8%;top=6%;background:radial-gradient(circle at 60% 40%, rgba(16,185,129,0.95), rgba(79,70,229,0.85));animation:bf2 20s ease-in-out infinite;opacity:0.14}
.bg-anim .b3{width:520px;height:520px;left:8%;bottom:-18%;background:radial-gradient(circle at 30% 60%, rgba(255,186,116,0.95), rgba(255,99,132,0.85));animation:bf3 22s ease-in-out infinite;opacity:0.12}
@keyframes bf1{0%{transform:translate3d(0,0,0)}25%{transform:translate3d(50px,-30px,0)}50%{transform:translate3d(0,-70px,0)}75%{transform:translate3d(-30px,-10px,0)}100%{transform:translate3d(0,0,0)}}
@keyframes bf2{0%{transform:translate3d(0,0,0)}20%{transform:translate3d(-40px,30px,0)}50%{transform:translate3d(30px,50px,0)}80%{transform:translate3d(-10px,10px,0)}100%{transform:translate3d(0,0,0)}}
@keyframes bf3{0%{transform:translate3d(0,0,0)}30%{transform:translate3d(30px,50px,0)}60%{transform:translate3d(-30px,20px,0)}90%{transform:translate3d(10px,-10px,0)}100%{transform:translate3d(0,0,0)}}

/* stripes layer */
.stripes{position:fixed;inset:0;z-index:0;pointer-events:none;mix-blend-mode:overlay;opacity:0.06;background-image:
  linear-gradient(135deg, rgba(255,255,255,0.04) 12.5%, transparent 12.5%, transparent 50%, rgba(255,255,255,0.04) 50%, rgba(255,255,255,0.04) 62.5%, transparent 62.5%, transparent 100%);background-size:96px 96px;animation:moveStripes 14s linear infinite}
@keyframes moveStripes{from{background-position:0 0}to{background-position:192px 192px}}

/* card */
.card{position:relative;z-index:2;background:linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01));padding:20px;border-radius:12px;border:1px solid rgba(255,255,255,0.03);box-shadow:0 18px 40px rgba(2,6,23,0.36)}
.header{display:flex;align-items:center;justify-content:space-between;margin-bottom:14px}
.logo{width:48px;height:48px;border-radius:10px;background:linear-gradient(135deg,var(--accent-start),var(--accent-end));display:flex;align-items:center;justify-content:center;color:#071022;font-weight:800}
.title{font-weight:800}

/* form */
.form{display:grid;grid-template-columns:1fr 300px;gap:18px}
@media(max-width:880px){.form{grid-template-columns:1fr}}
.field{background:transparent;padding:10px;border-radius:10px;transition:transform .18s,box-shadow .18s;border:1px solid transparent}
.field:hover,.field:focus-within{transform:translateY(-8px);box-shadow:0 20px 40px rgba(2,6,23,0.28);border-color:rgba(255,255,255,0.03)}
.label{display:block;font-weight:700;margin-bottom:8px;position:relative}
.label::after{content:'';position:absolute;left:0;right:0;bottom:-8px;height:2px;background:linear-gradient(90deg,var(--accent-start),var(--accent-end));transform:scaleX(0);transform-origin:left center;transition:transform .28s,opacity .18s;opacity:0}
.field:hover .label::after,.field:focus-within .label::after{transform:scaleX(1);opacity:1}

.input {width:100%;padding:10px;border-radius:10px;border:1px solid rgba(255,255,255,0.04);background:transparent;color:inherit;transition:box-shadow .14s,transform .12s}
.input:hover{transform:translateY(-3px);box-shadow:0 10px 30px rgba(2,6,23,0.08)}
.input:focus{outline:none;box-shadow:0 18px 40px rgba(79,70,229,0.08);border-color:rgba(79,70,229,0.18)}

.preview{padding:12px;border-radius:10px;background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.03);text-align:center;transition:transform .18s,box-shadow .18s}
.preview img{max-width:100%;border-radius:8px}

/* buttons */
.actions{display:flex;gap:10px;margin-top:8px}
.btn{position:relative;overflow:hidden;padding:10px 14px;border-radius:10px;border:none;cursor:pointer;font-weight:800;background:linear-gradient(90deg,var(--accent-start),var(--accent-end));color:#071022;transition:transform .14s,box-shadow .14s}
.btn.ghost{background:transparent;border:1px solid rgba(255,255,255,0.04);color:var(--muted)}
.btn:hover{transform:translateY(-6px) scale(1.02);box-shadow:0 20px 44px rgba(2,6,23,0.28)}
.btn::after{content:'›';position:absolute;right:12px;top:50%;transform:translateY(-50%) translateX(8px);opacity:0;transition:transform .18s,opacity .18s}
.btn:hover::after{transform:translateY(-50%) translateX(0);opacity:0.9}

/* small text */
.small{font-size:13px;color:var(--muted);margin-top:8px}

/* error */
.err{background:rgba(255,80,80,0.08);color:#ffdede;padding:10px;border-radius:8px;margin-bottom:10px;border:1px solid rgba(255,80,80,0.08)}

/* ripple */
.ripple{position:absolute;border-radius:50%;transform:scale(0);background:rgba(255,255,255,0.12);animation:ripple 600ms linear;pointer-events:none}
@keyframes ripple{to{transform:scale(2.8);opacity:0}}

/* reduce motion */
@media (prefers-reduced-motion: reduce){
  .stripes,.bg-anim .blob{animation:none;opacity:0.06}
}
</style>
</head>
<body>
  <div class="stripes" aria-hidden="true"></div>
  <div class="bg-anim" aria-hidden="true">
    <span class="blob b1"></span>
    <span class="blob b2"></span>
    <span class="blob b3"></span>
  </div>

<div class="container">
  <div class="card" role="main">
    <div class="header">
      <div style="display:flex;gap:12px;align-items:center">
        <div class="logo">WR</div>
        <div>
          <div class="title">Tambah Produk</div>
          <div class="small">Tambah produk baru ke toko</div>
        </div>
      </div>
      <div>
        <button class="btn ghost" onclick="window.location='data-produk.php'">← Kembali</button>
      </div>
    </div>

    <?php if ($err): ?><div class="err" role="alert"><?= htmlspecialchars($err) ?></div><?php endif; ?>

    <form method="POST" enctype="multipart/form-data" novalidate>
      <div class="form">
        <div>
          <div class="field">
            <label class="label">Kategori</label>
            <select name="kategori" class="input" required>
              <option value="">--Pilih--</option>
              <?php 
                $kategori_q = mysqli_query($conn, "SELECT * FROM tb_category ORDER BY category_name ASC");
                while($r = mysqli_fetch_assoc($kategori_q)){
                  echo '<option value="'.htmlspecialchars($r['category_id']).'">'.htmlspecialchars($r['category_name']).'</option>';
                }
              ?>
            </select>
          </div>

          <div class="field" style="margin-top:12px">
            <label class="label">Nama Produk</label>
            <input type="text" name="nama" class="input" placeholder="Nama Produk" required>
          </div>

          <div style="display:flex;gap:12px;margin-top:12px">
            <div class="field" style="flex:1">
              <label class="label">Harga</label>
              <input type="text" name="harga" class="input" placeholder="Harga" required>
            </div>
            <div class="field" style="width:160px">
              <label class="label">Status</label>
              <select name="status" class="input" required>
                <option value="">--Pilih--</option>
                <option value="1">Aktif</option>
                <option value="0">Tidak Aktif</option>
              </select>
            </div>
          </div>

          <div class="field" style="margin-top:12px">
            <label class="label">Deskripsi</label>
            <textarea name="deskripsi" class="input" rows="6" placeholder="Deskripsi produk"></textarea>
          </div>

          <div class="actions">
            <button type="submit" name="submit" class="btn">Tambahkan</button>
            <button type="button" class="btn ghost" onclick="window.location='data-produk.php'">Batal</button>
          </div>

          <div class="small">Tip: Gunakan gambar ukuran wajar. Format yang diterima: jpg, jpeg, png, gif, webp.</div>
        </div>

        <aside>
          <div class="field preview">
            <label class="label">Gambar Produk</label>
            <div style="margin-top:8px">
              <img id="previewImg" src="" alt="Preview" style="display:none">
              <div id="noimg" class="small">Belum ada gambar dipilih</div>
            </div>
            <div style="margin-top:12px">
              <input type="file" name="gambar" id="gambar" accept="image/*" class="input" required>
            </div>
            <div class="small">Preview akan muncul sebelum upload.</div>
          </div>
        </aside>
      </div>
    </form>
  </div>

  <footer style="margin-top:18px;text-align:center;color:var(--muted)"><small>Copyright &copy; 2025 - WarungRizqi.</small></footer>
</div>

<script src="https://cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>
<script>CKEDITOR.replace('deskripsi');</script>

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
document.querySelectorAll('.btn, .btn.ghost').forEach(btn=>{
  btn.addEventListener('click', function(e){ rippleClick(e, this); });
  btn.addEventListener('keydown', function(e){ if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); this.click(); } });
});

// image preview
document.getElementById('gambar').addEventListener('change', function(e){
  const f = this.files[0];
  const p = document.getElementById('previewImg');
  const no = document.getElementById('noimg');
  if (!f) { p.style.display='none'; no.style.display='block'; return; }
  const reader = new FileReader();
  reader.onload = function(ev){
    p.src = ev.target.result;
    p.style.display = 'block';
    no.style.display = 'none';
  };
  reader.readAsDataURL(f);
});

// gentle parallax for blobs (disable if reduced motion)
if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
  const blobs = document.querySelectorAll('.bg-anim .blob');
  blobs.forEach((b,i)=> b.style.animationDelay = (i*0.9)+'s');
  (function(){
    let mx=0,my=0,lx=0,ly=0;
    window.addEventListener('pointermove', e => {
      mx = (e.clientX / window.innerWidth - 0.5) * 2;
      my = (e.clientY / window.innerHeight - 0.5) * 2;
    }, {passive:true});
    function loop(){
      lx += (mx - lx) * 0.06;
      ly += (my - ly) * 0.06;
      blobs.forEach((b,i)=>{
        const depth = (i+1)*8;
        b.style.transform = `translate3d(${-lx*depth}px, ${-ly*(depth*0.6)}px, 0) rotate(${lx*(i===0?2:(i===1?-1.5:1))}deg)`;
      });
      requestAnimationFrame(loop);
    }
    requestAnimationFrame(loop);
  })();
}
</script>
</body>
</html>
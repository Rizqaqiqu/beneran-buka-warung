<?php
session_start();
include 'db.php';
if(!isset($_SESSION['status_login']) || $_SESSION['status_login'] != true){
    header('Location: login.php');
    exit;
}

$kategori = mysqli_query($conn, "SELECT * FROM tb_category WHERE category_id = '".$_GET['id']."' ");
if(mysqli_num_rows($kategori) == 0){
    echo '<script>window.location="data-kategori.php"</script>';
    exit;
}
$k = mysqli_fetch_object($kategori);

$err = '';
if (isset($_POST['submit'])) {
    $nama = trim($_POST['nama'] ?? '');
    if ($nama === '') {
        $err = 'Nama kategori tidak boleh kosong.';
    } else {
        $nama_db = mysqli_real_escape_string($conn, ucwords($nama));
        $update = mysqli_query($conn, "UPDATE tb_category SET category_name = '{$nama_db}' WHERE category_id = '{$k->category_id}'");
        if ($update) {
            echo '<script>alert("Edit data berhasil")</script>';
            echo '<script>window.location="data-kategori.php"</script>';
            exit;
        } else {
            $err = 'Gagal mengedit data. ' . mysqli_error($conn);
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Edit Kategori — WarungRizqi</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
<style>
:root{
  --bg:#071022; --muted:#9aa6b2; --accent-start:#4facfe; --accent-end:#00f2fe;
}
*{box-sizing:border-box}
html,body{height:100%;margin:0;font-family:Inter,system-ui,Arial;background:
  radial-gradient(900px 400px at 10% 10%, rgba(79,70,229,0.06), transparent 6%),
  #071022;color:#e6eef8;-webkit-font-smoothing:antialiased;padding:28px}
.container{max-width:980px;margin:0 auto;position:relative;z-index:1}

/* animated background */
.bg-anim{position:fixed;inset:0;z-index:0;pointer-events:none;overflow:hidden}
.bg-anim .blob{position:absolute;border-radius:50%;filter:blur(84px);opacity:0.12;mix-blend-mode:screen;will-change:transform}
.bg-anim .b1{width:640px;height:640px;left:-12%;top:-20%;background:radial-gradient(circle at 20% 30%, rgba(124,58,237,0.95), rgba(6,182,212,0.85));animation:bf1 16s ease-in-out infinite}
.bg-anim .b2{width:460px;height:460px;right:-8%;top=6%;background:radial-gradient(circle at 60% 40%, rgba(16,185,129,0.95), rgba(79,70,229,0.85));animation:bf2 20s ease-in-out infinite;opacity:0.14}
.bg-anim .b3{width:520px;height:520px;left:8%;bottom:-18%;background:radial-gradient(circle at 30% 60%, rgba(255,186,116,0.95), rgba(255,99,132,0.85));animation:bf3 22s ease-in-out infinite;opacity:0.12}
@keyframes bf1{0%{transform:translate3d(0,0,0)}25%{transform:translate3d(50px,-30px,0)}50%{transform:translate3d(0,-70px,0)}75%{transform:translate3d(-30px,-10px,0)}100%{transform:translate3d(0,0,0)}}
@keyframes bf2{0%{transform:translate3d(0,0,0)}20%{transform:translate3d(-40px,30px,0)}50%{transform:translate3d(30px,50px,0)}80%{transform:translate3d(-10px,10px,0)}100%{transform:translate3d(0,0,0)}}
@keyframes bf3{0%{transform:translate3d(0,0,0)}30%{transform:translate3d(30px,50px,0)}60%{transform:translate3d(-30px,20px,0)}90%{transform:translate3d(10px,-10px,0)}100%{transform:translate3d(0,0,0)}}

/* stripes */
.stripes{position:fixed;inset:0;z-index:0;pointer-events:none;mix-blend-mode:overlay;opacity:0.05;background-image:
  linear-gradient(135deg, rgba(255,255,255,0.04) 12.5%, transparent 12.5%, transparent 50%, rgba(255,255,255,0.04) 50%, rgba(255,255,255,0.04) 62.5%, transparent 62.5%, transparent 100%);background-size:96px 96px;animation:moveStripes 14s linear infinite}
@keyframes moveStripes{from{background-position:0 0}to{background-position:192px 192px}}

/* layout */
.card{position:relative;z-index:2;background:linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01));padding:20px;border-radius:12px;border:1px solid rgba(255,255,255,0.03);box-shadow:0 18px 40px rgba(2,6,23,0.36)}
.header{display:flex;align-items:center;justify-content:space-between;margin-bottom:14px}
.logo{width:48px;height:48px;border-radius:10px;background:linear-gradient(135deg,var(--accent-start),var(--accent-end));display:flex;align-items:center;justify-content:center;color:#071022;font-weight:800}
.title{font-weight:800}
.sub{color:var(--muted);font-size:13px}

/* form */
.form{display:flex;flex-direction:column;gap:12px}
.input-control{padding:12px;border-radius:10px;border:1px solid rgba(255,255,255,0.04);background:transparent;color:inherit}
.input-control:focus{outline:none;box-shadow:0 12px 36px rgba(79,70,229,0.06);border-color:rgba(79,70,229,0.18)}
.btn{position:relative;overflow:hidden;padding:10px 14px;border-radius:10px;border:none;cursor:pointer;font-weight:800;background:linear-gradient(90deg,var(--accent-start),var(--accent-end));color:#071022;transition:transform .14s,box-shadow .14s}
.btn.ghost{background:transparent;border:1px solid rgba(255,255,255,0.04);color:var(--muted)}
.btn:hover{transform:translateY(-6px) scale(1.02);box-shadow:0 20px 44px rgba(2,6,23,0.28)}
.btn::after{content:'›';position:absolute;right:12px;top:50%;transform:translateY(-50%) translateX(8px);opacity:0;transition:transform .18s,opacity .18s}
.btn:hover::after{transform:translateY(-50%) translateX(0);opacity:0.9}

/* small */
.small{font-size:13px;color:var(--muted)}
.err{background:rgba(255,80,80,0.08);color:#ffdede;padding:10px;border-radius:8px;border:1px solid rgba(255,80,80,0.08)}

/* ripple */
.ripple{position:absolute;border-radius:50%;transform:scale(0);background:rgba(255,255,255,0.12);animation:ripple 600ms linear;pointer-events:none}
@keyframes ripple{to{transform:scale(2.8);opacity:0}}

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
  <div class="card" role="main" aria-labelledby="title">
    <div class="header">
      <div style="display:flex;gap:12px;align-items:center">
        <div class="logo">WR</div>
        <div>
          <div id="title" class="title">Edit Kategori</div>
          <div class="sub">Edit kategori untuk produk</div>
        </div>
      </div>
      <div>
        <button class="btn ghost" onclick="window.location='data-kategori.php'">← Kembali</button>
      </div>
    </div>

    <?php if($err): ?><div class="err" role="alert"><?= htmlspecialchars($err) ?></div><?php endif; ?>

    <form class="form" method="POST" action="">
      <input type="text" name="nama" class="input-control" placeholder="Nama Kategori" value="<?php echo htmlspecialchars($k->category_name); ?>" required autofocus>
      <div style="display:flex;gap:10px;align-items:center">
        <button type="submit" name="submit" class="btn">Submit</button>
        <button type="button" class="btn ghost" onclick="window.location='data-kategori.php'">Batal</button>
      </div>
      <div class="small">Nama kategori akan disimpan dengan format Title Case.</div>
    </form>
  </div>

  <footer style="margin-top:18px;text-align:center;color:var(--muted)"><small>Copyright &copy; 2025 - WarungRizqi.</small></footer>
</div>

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

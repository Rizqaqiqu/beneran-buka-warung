<?php
session_start();
include 'db.php';

// redirect jika sudah login
if (!empty($_SESSION['status_login'])) {
    header('Location: dashboard.php');
    exit;
}

// proses login
$err = '';
if (isset($_POST['submit'])) {
    $user = mysqli_real_escape_string($conn, trim($_POST['user'] ?? ''));
    $pass = mysqli_real_escape_string($conn, trim($_POST['pass'] ?? ''));

    if ($user === '' || $pass === '') {
        $err = 'Masukkan username dan password.';
    } else {
        $cek = mysqli_query($conn, "SELECT * FROM tb_admin WHERE username = '" . $user . "' AND password = '" . md5($pass) . "' LIMIT 1");
        if ($cek && mysqli_num_rows($cek) > 0) {
            $d = mysqli_fetch_object($cek);
            $_SESSION['status_login'] = true;
            $_SESSION['a_global'] = $d;
            $_SESSION['id'] = $d->admin_id;
            header('Location: dashboard.php');
            exit;
        } else {
            $err = 'Username atau password Anda salah.';
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Login — WarungRizqi</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
<style>
:root{
  --bg:#071022;
  --card:#071826;
  --accent-start:#4facfe;
  --accent-end:#00f2fe;
  --muted:#9aa6b2;
  --glass: rgba(255,255,255,0.03);
}
*{box-sizing:border-box}
html,body{height:100%;margin:0;font-family:Inter,system-ui,Segoe UI,Arial;background:
  radial-gradient(900px 400px at 10% 10%, rgba(79,70,229,0.06), transparent 6%),
  #071022;color:#e6eef8;display:flex;align-items:center;justify-content:center}
.container{width:100%;max-width:420px;padding:28px}
.card{background:linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01));padding:28px;border-radius:14px;border:1px solid rgba(255,255,255,0.03);box-shadow:0 20px 50px rgba(2,6,23,0.6)}
.brand{display:flex;align-items:center;gap:14px;margin-bottom:12px}
.logo{width:56px;height:56px;border-radius:12px;background:linear-gradient(135deg,var(--accent-start),var(--accent-end));display:flex;align-items:center;justify-content:center;color:#071022;font-weight:800;font-size:20px}
.title{font-size:18px;font-weight:800}
.subtitle{color:var(--muted);font-size:13px;margin-top:4px}

/* form */
.form {margin-top:14px;display:flex;flex-direction:column;gap:12px}
.input-control{width:100%;padding:12px;border-radius:10px;border:1px solid rgba(255,255,255,0.04);background:transparent;color:inherit;font-size:15px}
.input-control:focus{outline:none;box-shadow:0 8px 30px rgba(79,70,229,0.06);border-color:rgba(79,70,229,0.18)}
.actions{display:flex;gap:10px;align-items:center;margin-top:8px}
.btn{
  position:relative;overflow:hidden;display:inline-flex;align-items:center;gap:10px;padding:10px 14px;border-radius:10px;border:none;cursor:pointer;font-weight:800;
  transition:transform .14s,box-shadow .14s;
  background:linear-gradient(90deg,var(--accent-start),var(--accent-end));color:#071022;
}
.btn.secondary{background:transparent;border:1px solid rgba(255,255,255,0.06);color:#e6eef8;font-weight:700}
.btn:hover{transform:translateY(-4px);box-shadow:0 14px 36px rgba(2,6,23,0.14)}
.btn:active{transform:translateY(-1px)}

/* subtle helper */
.helper{font-size:13px;color:var(--muted);margin-top:8px;text-align:center}

/* error */
.err{background:rgba(255,80,80,0.08);color:#ffdede;padding:10px;border-radius:8px;margin-top:8px;border:1px solid rgba(255,80,80,0.08)}

/* ripple */
.ripple{position:absolute;border-radius:50%;transform:scale(0);background:rgba(255,255,255,0.14);animation:ripple 600ms linear;pointer-events:none}
@keyframes ripple{to{transform:scale(2.8);opacity:0}}

/* responsive */
@media(max-width:420px){ .container{padding:18px} .card{padding:18px} }
</style>
</head>
<body>
<div class="container">
  <div class="card" role="main" aria-labelledby="loginTitle">
    <div class="brand">
      <div class="logo">WR</div>
      <div>
        <div id="loginTitle" class="title">WarungRizqi Admin</div>
        <div class="subtitle">Masuk untuk mengelola toko</div>
      </div>
    </div>

    <?php if ($err): ?>
      <div class="err" role="alert"><?= htmlspecialchars($err) ?></div>
    <?php endif; ?>

    <form class="form" method="POST" action="">
      <input class="input-control" name="user" type="text" placeholder="Username" autofocus required>
      <input class="input-control" name="pass" type="password" placeholder="Password" required>
      <div class="actions">
        <button type="submit" name="submit" class="btn">Masuk</button>
        <a href="index.php" class="btn secondary" role="button">Kembali</a>
      </div>
      <div class="helper">Tidak punya akun? Hubungi administrator untuk akses.</div>
    </form>
  </div>
</div>

<script>
// ripple for buttons
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

document.querySelectorAll('.btn, .btn.secondary').forEach(btn=>{
  btn.addEventListener('click', function(e){
    // allow keyboard activation too
    rippleClick(e, this);
  });
  btn.addEventListener('keydown', function(e){
    if (e.key === 'Enter' || e.key === ' ') {
      e.preventDefault();
      this.click();
    }
  });
});
</script>
</body>
</html>
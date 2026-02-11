<?php
session_start();
include 'db.php';
if ($_SESSION['status_login'] != true) {
    echo '<script>window.location="login.php"</script>';
    exit;
}

$admin_id = intval($_SESSION['id']);
$q = mysqli_query($conn, "SELECT * FROM tb_admin WHERE admin_id = '{$admin_id}' LIMIT 1");
$d = mysqli_fetch_object($q);

// handle profile update
if (isset($_POST['submit'])) {
    $nama   = mysqli_real_escape_string($conn, ucwords($_POST['nama']));
    $user   = mysqli_real_escape_string($conn, $_POST['user']);
    $hp     = mysqli_real_escape_string($conn, $_POST['hp']);
    $email  = mysqli_real_escape_string($conn, $_POST['email']);
    $alamat = mysqli_real_escape_string($conn, ucwords($_POST['alamat']));

    $sql = "UPDATE tb_admin SET
            admin_name = '{$nama}',
            username = '{$user}',
            admin_telp = '{$hp}',
            admin_email = '{$email}',
            admin_address = '{$alamat}'
            WHERE admin_id = '{$admin_id}'";
    if (mysqli_query($conn, $sql)) {
        echo '<script>alert("Profil berhasil diperbarui");window.location="profil.php"</script>';
        exit;
    } else {
        $err = 'Gagal menyimpan: ' . mysqli_error($conn);
    }
}

// handle password change
if (isset($_POST['ubah_password'])) {
    $pass1 = $_POST['pass1'] ?? '';
    $pass2 = $_POST['pass2'] ?? '';
    if ($pass1 !== $pass2) {
        $err = 'Konfirmasi password tidak cocok.';
    } elseif (strlen($pass1) < 6) {
        $err = 'Password minimal 6 karakter.';
    } else {
        $hash = md5($pass1);
        $sql = "UPDATE tb_admin SET password = '{$hash}' WHERE admin_id = '{$admin_id}'";
        if (mysqli_query($conn, $sql)) {
            echo '<script>alert("Password berhasil diperbarui");window.location="profil.php"</script>';
            exit;
        } else {
            $err = 'Gagal menyimpan password: ' . mysqli_error($conn);
        }
    }
}

// refresh data
$q = mysqli_query($conn, "SELECT * FROM tb_admin WHERE admin_id = '{$admin_id}' LIMIT 1");
$d = mysqli_fetch_object($q);
?>
<!DOCTYPE html>
<html lang="id">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Profil - WarungRizqi</title>

	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&family=Poppins:wght@600&display=swap" rel="stylesheet">

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

		/* layout */
		.layout{display:grid;grid-template-columns:1fr 380px;gap:20px;margin-top:20px}
		@media(max-width:980px){ .layout{grid-template-columns:1fr} }

		/* cards */
		.card{background:linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01));padding:18px;border-radius:12px;border:1px solid rgba(255,255,255,0.03);box-shadow:var(--card-elev);backdrop-filter: blur(6px)}
		.profile-head{display:flex;gap:14px;align-items:center}
		.avatar{width:92px;height:92px;border-radius:12px;background:linear-gradient(90deg,#eef7ff,#f0faff);color:#0f172a;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:28px}
		.info h2{margin:0;font-size:18px}
		.info .muted{color:var(--muted);margin-top:6px}

		/* form */
		.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:12px}
		@media(max-width:640px){ .form-grid{grid-template-columns:1fr} }
		.input{width:100%;padding:10px;border-radius:10px;border:1px solid rgba(255,255,255,0.04);background:transparent;color:inherit}
		.label{font-size:13px;color:var(--muted);margin-bottom:6px}
		.section-title{font-weight:800;margin:0 0 10px 0}

		/* actions */
		.actions{display:flex;gap:10px;margin-top:12px}
		.btn{
		  position:relative;
		  overflow:hidden;
		  display:inline-flex;
		  align-items:center;
		  gap:10px;
		  padding:10px 14px;
		  border-radius:10px;
		  border:1px solid rgba(255,255,255,0.04);
		  background:linear-gradient(90deg,rgba(255,255,255,0.02),transparent);
		  color:#e6eef8;
		  cursor:pointer;
		  font-weight:700;
		  transition:transform .18s ease, box-shadow .18s ease;
		  will-change:transform;
		  text-decoration:none;
		}
		.btn.primary{background:linear-gradient(90deg,#4facfe,#00f2fe);color:#071022;box-shadow:0 12px 30px rgba(79,70,229,0.12)}
		.btn:hover{
		  transform:translateY(-4px);
		  box-shadow:0 12px 30px rgba(2,6,23,0.14);
		}
		.btn:active{ transform:translateY(-1px); }

		/* small chevron that appears on hover */
		.btn::after{
		  content: '›';
		  position:absolute;
		  right:12px;
		  top:50%;
		  transform:translateY(-50%) translateX(6px);
		  opacity:0;
		  color:inherit;
		  font-weight:800;
		  transition:transform .18s ease, opacity .18s ease;
		  pointer-events:none;
		}
		.btn:hover::after{
		  transform:translateY(-50%) translateX(0);
		  opacity:0.9;
		}

		/* Admin data container interactions (hover, chevron, ripple) */
		.summary .row{
		  position:relative;
		  padding:10px 14px;
		  border-bottom:1px dashed rgba(255,255,255,0.03);
		  transition:background .18s,transform .12s;
		  cursor:pointer;
		  overflow:hidden;
		}
		.summary .row:last-child{ border-bottom: none; }

		.summary .row:hover{
		  background: rgba(255,255,255,0.02);
		  transform:translateY(-4px);
		  box-shadow:0 8px 20px rgba(2,6,23,0.06);
		}

		/* chevron */
		.summary .row::after{
		  content: '›';
		  position:absolute;
		  right:12px;
		  top:50%;
		  transform:translateY(-50%) translateX(8px);
		  opacity:0;
		  color:rgba(255,255,255,0.7);
		  font-weight:800;
		  transition:transform .18s,opacity .18s;
		  pointer-events:none;
		}
		.summary .row:hover::after{
		  transform:translateY(-50%) translateX(0);
		  opacity:1;
		}

		/* ripple for rows */
		.summary .row .ripple{
		  position:absolute;
		  border-radius:50%;
		  transform:scale(0);
		  background:rgba(255,255,255,0.06);
		  animation:ripple 600ms linear;
		  pointer-events:none;
		  mix-blend-mode:screen;
		}
		@keyframes ripple { to { transform:scale(2.8); opacity:0; } }

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
        <div class="sub">Kelola profil & keamanan akun</div>
      </div>
    </div>
  </header>

  <nav class="nav" aria-label="Main navigation">
    <a href="dashboard.php">Dashboard</a>
    <a href="profil.php" class="active">Profil</a>
    <a href="data-kategori.php">Kategori</a>
    <a href="data-produk.php">Produk</a>
    <a href="data-pembelian.php">Pembelian</a>
    <a href="data-user.php">Admin</a>
    <a href="keluar.php">Keluar</a>
  </nav>

  <div class="layout">
    <main class="card" aria-labelledby="profilTitle">
      <div class="profile-head">
        <div class="avatar"><?= htmlspecialchars(strtoupper(substr($d->admin_name ?? 'P',0,1))) ?></div>
        <div class="info">
          <h2 id="profilTitle"><?= htmlspecialchars($d->admin_name ?? 'Admin') ?></h2>
          <div class="muted small"><?= htmlspecialchars($d->username ?? '-') ?> • <?= htmlspecialchars($d->admin_email ?? '-') ?></div>
          <div class="muted small" style="margin-top:6px">Telepon: <?= htmlspecialchars($d->admin_telp ?? '-') ?></div>
        </div>
      </div>

      <?php if (!empty($err)): ?>
        <div class="err"><?= htmlspecialchars($err) ?></div>
      <?php endif; ?>

      <h3 class="section-title" style="margin-top:16px">Ubah Profil</h3>
      <form method="POST" novalidate>
        <div class="form-grid">
          <div>
            <div class="label">Nama Lengkap</div>
            <input class="input" name="nama" type="text" required value="<?= htmlspecialchars($d->admin_name ?? '') ?>">
          </div>
          <div>
            <div class="label">Username</div>
            <input class="input" name="user" type="text" required value="<?= htmlspecialchars($d->username ?? '') ?>">
          </div>
          <div>
            <div class="label">No. HP</div>
            <input class="input" name="hp" type="text" required value="<?= htmlspecialchars($d->admin_telp ?? '') ?>">
          </div>
          <div>
            <div class="label">Email</div>
            <input class="input" name="email" type="email" required value="<?= htmlspecialchars($d->admin_email ?? '') ?>">
          </div>
          <div style="grid-column:1/-1">
            <div class="label">Alamat</div>
            <textarea class="input" name="alamat" rows="3" required style="resize:vertical"><?= htmlspecialchars($d->admin_address ?? '') ?></textarea>
          </div>
        </div>

        <div class="actions" style="margin-top:14px">
          <button type="submit" name="submit" class="btn primary">Simpan Perubahan</button>
          <a class="btn ghost" href="dashboard.php">Batal</a>
        </div>
      </form>

      <h3 class="section-title" style="margin-top:22px">Ubah Password</h3>
      <form method="POST" novalidate>
        <div class="form-grid" style="margin-top:8px">
          <div>
            <div class="label">Password Baru</div>
            <input class="input" name="pass1" type="password" required placeholder="Minimal 6 karakter">
          </div>
          <div>
            <div class="label">Konfirmasi Password</div>
            <input class="input" name="pass2" type="password" required>
          </div>
        </div>
        <div class="actions" style="margin-top:12px">
          <button type="submit" name="ubah_password" class="btn primary">Ubah Password</button>
          <a class="btn ghost" href="profil.php">Reset</a>
        </div>
      </form>
    </main>

    <aside class="card summary" aria-label="Ringkasan Akun">
      <div style="display:flex;justify-content:space-between;align-items:center">
        <div>
          <div class="small">Akun</div>
          <div style="font-weight:800;margin-top:6px"><?= htmlspecialchars($d->admin_name ?? '-') ?></div>
        </div>
        <div style="text-align:right">
          <div class="small">Status</div>
          <div style="font-weight:800;color:#8ef08a;margin-top:6px">Terverifikasi</div>
        </div>
      </div>

      <div style="margin-top:12px">
        <div class="row"><div class="small">Username</div><div><?= htmlspecialchars($d->username ?? '-') ?></div></div>
        <div class="row"><div class="small">Email</div><div><?= htmlspecialchars($d->admin_email ?? '-') ?></div></div>
        <div class="row"><div class="small">Telepon</div><div><?= htmlspecialchars($d->admin_telp ?? '-') ?></div></div>
        <div class="row"><div class="small">Alamat</div><div style="max-width:200px;word-wrap:break-word"><?= nl2br(htmlspecialchars($d->admin_address ?? '-')) ?></div></div>
      </div>

      <div style="margin-top:14px">
        <div class="small">Keamanan</div>
        <div style="margin-top:8px;color:var(--muted);font-size:13px">Aktifkan 2FA melalui aplikasi untuk lapisan keamanan tambahan.</div>
      </div>

      <div style="margin-top:14px;display:flex;gap:8px;flex-wrap:wrap">
        <a class="btn ghost" href="data-user.php">Kelola Admin</a>
        <a class="btn ghost" href="data-produk.php">Kelola Produk</a>
      </div>
    </aside>
  </div>

  <div class="footer">
    <small>Copyright &copy; 2025 - <b>WarungRizqi</b>. All Rights Reserved.</small>
  </div>
</div>
</body>
<script>
// Attach a small ripple effect to all .btn elements
document.querySelectorAll('.btn').forEach(btn=>{
  btn.addEventListener('click', function(e){
    // ignore clicks originating from keyboard where coords are 0,0
    const rect = this.getBoundingClientRect();
    const circle = document.createElement('span');
    circle.className = 'ripple';
    const size = Math.max(rect.width, rect.height);
    circle.style.width = circle.style.height = size + 'px';
    circle.style.left = ( (e.clientX || rect.left + rect.width/2) - rect.left - size/2 ) + 'px';
    circle.style.top = ( (e.clientY || rect.top + rect.height/2) - rect.top - size/2 ) + 'px';
    this.appendChild(circle);
    setTimeout(()=> { circle.remove(); }, 750);
  });
});

// Make summary rows interactive and add ripple + keyboard support
document.querySelectorAll('.summary .row').forEach(row=>{
  // make focusable for keyboard users
  if (!row.hasAttribute('tabindex')) row.setAttribute('tabindex','0');
  row.addEventListener('click', function(e){
    const rect = this.getBoundingClientRect();
    const circle = document.createElement('span');
    circle.className = 'ripple';
    const size = Math.max(rect.width, rect.height);
    circle.style.width = circle.style.height = size + 'px';
    circle.style.left = ( (e.clientX || rect.left + rect.width/2) - rect.left - size/2 ) + 'px';
    circle.style.top = ( (e.clientY || rect.top + rect.height/2) - rect.top - size/2 ) + 'px';
    this.appendChild(circle);
    setTimeout(()=> circle.remove(), 700);

    // optional: if row has data-action attribute, follow it (useful to add links)
    const action = this.getAttribute('data-action');
    if (action) {
      if (action.startsWith('http')) window.location = action;
      else if (action === 'copy') {
        const txt = this.getAttribute('data-copy') || this.textContent.trim();
        navigator.clipboard?.writeText(txt);
      }
    }
  });

  // keyboard support: Enter triggers click
  row.addEventListener('keydown', function(e){
    if (e.key === 'Enter' || e.key === ' ') {
      e.preventDefault();
      this.click();
    }
  });
});
</script>
</html>
<?php
session_start();
include 'db.php';

// Cek apakah user sudah login
if (empty($_SESSION['login_user']) || empty($_SESSION['user_id'])) {
    echo '<script>window.location="login-user.php"</script>';
    exit;
}

$user_id = $_SESSION['user_id'];
$user = mysqli_query($conn, "SELECT * FROM tb_user WHERE user_id = '$user_id'");
$u = mysqli_fetch_object($user);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Profil Saya - WarungRizqi</title>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;600&display=swap" rel="stylesheet">
    <style>
        :root{
            --blue-100: #e3f2fd;
            --blue-200: #bbdefb;
            --blue-500: #1e88e5;
            --blue-600: #1976d2;
            --blue-700: #1565c0;
            --accent-red: #d32f2f;
        }

        *{box-sizing:border-box}
        body {
            font-family: 'Quicksand', sans-serif;
            background: linear-gradient(135deg, var(--blue-100), var(--blue-200));
            margin: 0;
            padding: 0;
            -webkit-font-smoothing:antialiased;
        }

        header {
            background: linear-gradient(90deg, var(--blue-500), var(--blue-600));
            color: #fff;
            text-align: center;
            padding: 18px 0;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            position: sticky;
            top: 0;
            z-index: 20;
        }
        header h1 a { color:#fff; text-decoration:none; font-weight:600; letter-spacing:1px; }

        .profile-container {
            background: linear-gradient(180deg, rgba(255,255,255,0.95), #fff);
            margin: 60px auto;
            padding: 36px;
            border-radius: 16px;
            width: 90%;
            max-width: 640px;
            box-shadow: 0 12px 30px rgba(30,88,150,0.08), inset 0 1px 0 rgba(255,255,255,0.6);
            transition: transform .35s cubic-bezier(.2,.9,.2,1), box-shadow .35s;
            transform-origin: center;
            animation: fadeInUp .6s ease;
            position: relative;
            overflow: visible;
        }
        .profile-container:hover{
            transform: translateY(-6px);
            box-shadow: 0 18px 40px rgba(30,88,150,0.12);
        }

        @keyframes fadeInUp { from{opacity:0; transform:translateY(16px)} to{opacity:1; transform:none} }

        .emoji-profile{
            font-size:88px;
            display:block;
            text-align:center;
            margin-bottom:8px;
            animation: float 3s ease-in-out infinite;
            filter: drop-shadow(0 6px 18px rgba(30,88,150,0.12));
            transition: transform .25s;
        }
        .emoji-profile:hover { transform: translateY(-6px) rotate(-6deg) scale(1.03); }

        @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-6px)} }

        .profile-header { text-align:center; margin-bottom:18px; }
        .profile-header h2 { margin:6px 0; color: var(--blue-700); font-weight:700; letter-spacing:.2px; }
        .profile-header p { color:#5f6b78; font-size:15px; margin:0; }

        .profile-info {
            background: linear-gradient(180deg, rgba(232,244,255,0.9), rgba(232,244,255,0.95));
            padding: 16px 20px;
            border-radius: 10px;
            margin-bottom: 18px;
            border: 1px solid rgba(30,88,150,0.06);
        }
        .profile-info p { margin:8px 0; color:#263238; font-size:15px; }

        .btn-edit {
            display:inline-block;
            background: linear-gradient(90deg, #42a5f5, var(--blue-500));
            color: white;
            padding: 12px 26px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 700;
            transition: transform .15s ease, box-shadow .15s ease, opacity .15s;
            box-shadow: 0 8px 20px rgba(30,88,150,0.12);
            cursor: pointer;
            border: none;
        }
        .btn-edit:hover { transform: translateY(-3px); box-shadow: 0 14px 36px rgba(30,88,150,0.14); opacity:0.98; }

        .logout { text-align:center; margin-top:20px; }
        .logout a { color: var(--accent-red); text-decoration:none; font-weight:700; transition: color .12s; }
        .logout a:hover { color: #b71c1c; text-decoration: underline; }

        footer { text-align:center; padding:18px; font-size:14px; color:#555; margin-top:36px; }

        /* Modal */
        .modal-backdrop {
            position: fixed;
            left:0; right:0; top:0; bottom:0;
            background: rgba(10,20,40,0.45);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 60;
            backdrop-filter: blur(3px);
            -webkit-backdrop-filter: blur(3px);
            padding: 20px;
        }
        .modal-backdrop.show { display:flex; }

        .modal {
            width: 100%;
            max-width: 520px;
            background: linear-gradient(180deg, #fff, #f7fbff);
            border-radius: 12px;
            box-shadow: 0 20px 50px rgba(14,30,80,0.2);
            padding: 20px;
            transform: translateY(18px) scale(.98);
            opacity: 0;
            transition: transform .32s cubic-bezier(.2,.9,.2,1), opacity .28s;
        }
        .modal-backdrop.show .modal { transform: translateY(0) scale(1); opacity:1; }

        .modal-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; }
        .modal-header h3 { margin:0; color:var(--blue-700); font-size:18px; }
        .modal-close { background:transparent; border:0; font-size:20px; cursor:pointer; color:#556; }

        .form-row { margin-bottom:10px; }
        .form-row label { display:block; font-size:13px; color:#41525a; margin-bottom:6px; }
        .form-row input[type="text"], .form-row input[type="email"] , .form-row textarea {
            width:100%; padding:10px 12px; border-radius:8px; border:1px solid rgba(30,88,150,0.08);
            background:#fff; font-size:14px; color:#223;
            transition: box-shadow .12s, border-color .12s;
        }
        .form-row input:focus, .form-row textarea:focus { outline: none; box-shadow: 0 6px 18px rgba(30,88,150,0.08); border-color:var(--blue-600); }

        .modal-actions { display:flex; gap:10px; justify-content:flex-end; margin-top:10px; }
        .btn-cancel { background:transparent; border:0; color:#556; padding:10px 14px; border-radius:8px; cursor:pointer; }
        .btn-save { background: linear-gradient(90deg,#42a5f5,var(--blue-500)); color:#fff; padding:10px 16px; border-radius:8px; border:0; cursor:pointer; font-weight:700; }

        /* small responsive */
        @media (max-width:480px){
            .emoji-profile{font-size:64px}
            .profile-container{padding:22px}
            .modal{padding:16px}
        }
    </style>
</head>
<body>

<header>
    <h1><a href="index.php">WarungRizqi</a></h1>
</header>

<div class="profile-container" role="main" aria-label="Profil pengguna">
    <span class="emoji-profile" aria-hidden="true">👤</span>

    <div class="profile-header">
        <h2><?= htmlspecialchars($u->user_nama) ?></h2>
        <p><?= htmlspecialchars($u->user_email) ?></p>
    </div>

    <div class="profile-info">
        <p><strong>📞 Nomor Telepon :</strong> <?= htmlspecialchars($u->user_telp) ?></p>
        <p><strong>🏠 Alamat :</strong> <?= nl2br(htmlspecialchars($u->user_alamat)) ?></p>
        <p><strong>📅 Tanggal Bergabung :</strong> <?= isset($u->created_at) ? htmlspecialchars($u->created_at) : '-' ?></p>
    </div>

    <div style="text-align:center;">
        <!-- tombol sekarang membuka modal -->
        <button class="btn-edit" id="openEdit">✏️ Edit Profil</button>
    </div>

    <div class="logout">
        <p><a href="logout-user.php">🚪 Logout</a></p>
    </div>
</div>

<footer>
    <p>© 2025 WarungRizqi. Semua hak dilindungi.</p>
</footer>

<!-- Modal markup -->
<div class="modal-backdrop" id="modalBackdrop" aria-hidden="true">
    <div class="modal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
        <div class="modal-header">
            <h3 id="modalTitle">Edit Profil</h3>
            <button class="modal-close" id="closeModal" aria-label="Tutup">&times;</button>
        </div>

        <form method="post" action="edit-profil.php">
            <input type="hidden" name="user_id" value="<?= htmlspecialchars($u->user_id) ?>">
            <div class="form-row">
                <label for="nama">Nama</label>
                <input id="nama" name="user_nama" type="text" value="<?= htmlspecialchars($u->user_nama) ?>" required>
            </div>
            <div class="form-row">
                <label for="email">Email</label>
                <input id="email" name="user_email" type="email" value="<?= htmlspecialchars($u->user_email) ?>" required>
            </div>
            <div class="form-row">
                <label for="telp">Nomor Telepon</label>
                <input id="telp" name="user_telp" type="text" value="<?= htmlspecialchars($u->user_telp) ?>">
            </div>
            <div class="form-row">
                <label for="alamat">Alamat</label>
                <textarea id="alamat" name="user_alamat" rows="3"><?= htmlspecialchars($u->user_alamat) ?></textarea>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-cancel" id="cancelModal">Batal</button>
                <button type="submit" class="btn-save">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
    (function(){
        const openBtn = document.getElementById('openEdit');
        const backdrop = document.getElementById('modalBackdrop');
        const closeBtn = document.getElementById('closeModal');
        const cancelBtn = document.getElementById('cancelModal');

        function showModal(){
            backdrop.classList.add('show');
            backdrop.setAttribute('aria-hidden','false');
            // fokus ke nama
            setTimeout(()=> document.getElementById('nama').focus(), 120);
            document.body.style.overflow = 'hidden';
        }
        function hideModal(){
            backdrop.classList.remove('show');
            backdrop.setAttribute('aria-hidden','true');
            document.body.style.overflow = '';
        }

        openBtn.addEventListener('click', showModal);
        closeBtn.addEventListener('click', hideModal);
        cancelBtn.addEventListener('click', hideModal);

        // klik di luar modal untuk tutup
        backdrop.addEventListener('click', function(e){
            if(e.target === backdrop) hideModal();
        });

        // esc untuk tutup
        document.addEventListener('keydown', function(e){
            if(e.key === 'Escape' && backdrop.classList.contains('show')) hideModal();
        });
    })();
</script>

</body>
</html>

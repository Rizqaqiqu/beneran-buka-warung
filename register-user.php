<?php
include 'db.php';

if(isset($_POST['submit'])){
    $nama   = mysqli_real_escape_string($conn, $_POST['nama']);
    $email  = mysqli_real_escape_string($conn, $_POST['email']);
    $pass   = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $telp   = mysqli_real_escape_string($conn, $_POST['telp']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);

    $cek = mysqli_query($conn, "SELECT * FROM tb_user WHERE user_email='$email'");
    if(mysqli_num_rows($cek) > 0){
        echo "<script>alert('Email sudah digunakan');</script>";
    } else {
        $insert = mysqli_query($conn, "INSERT INTO tb_user (user_nama,user_email,user_password,user_telp,user_alamat) 
                                      VALUES('$nama','$email','$pass','$telp','$alamat')");
        if($insert){
            echo "<script>alert('Registrasi berhasil! Silakan login'); window.location='login-user.php';</script>";
        } else {
            echo "<script>alert('Registrasi gagal!');</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Pembeli - WarungRizqi</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&family=Poppins:wght@600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        :root { --primary: #4facfe; --secondary: #00f2fe; --dark: #0f172a; --muted: #9aa6b2; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { min-height: 100vh; font-family: Inter, system-ui, Arial; background: linear-gradient(135deg, #0f172a 0%, #1a1f3a 50%, #0d1b2a 100%); color: #e6eef8; overflow-y: auto; position: relative; }
        .bg-animated { position: fixed; inset: 0; z-index: 0; overflow: hidden; }
        .blob { position: absolute; border-radius: 50%; filter: blur(80px); opacity: 0.1; mix-blend-mode: screen; animation: blobmove 8s ease-in-out infinite; }
        @keyframes blobmove { 0%, 100% { transform: translate(0, 0); } 50% { transform: translate(30px, -30px); } }
        .blob-1 { width: 300px; height: 300px; background: radial-gradient(circle, #4facfe, transparent); top: 10%; left: 5%; animation-delay: 0s; }
        .blob-2 { width: 250px; height: 250px; background: radial-gradient(circle, #00f2fe, transparent); bottom: 15%; right: 10%; animation-delay: 2s; }
        .blob-3 { width: 200px; height: 200px; background: radial-gradient(circle, #a855f7, transparent); top: 50%; right: 5%; animation-delay: 4s; }
        .floating-icon { position: absolute; font-size: 40px; opacity: 0.1; animation: float 6s ease-in-out infinite; }
        @keyframes float { 0%, 100% { transform: translateY(0px); } 50% { transform: translateY(-20px); } }
        .pulse { animation: pulse 2s ease-in-out infinite; }
        @keyframes pulse { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.05); } }
        .container { position: relative; z-index: 1; height: 100%; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .login-card { width: 100%; max-width: 420px; background: linear-gradient(180deg, rgba(255,255,255,0.08), rgba(255,255,255,0.02)); border: 1px solid rgba(255,255,255,0.1); border-radius: 20px; padding: 40px; backdrop-filter: blur(20px); box-shadow: 0 25px 50px rgba(0,0,0,0.3); animation: slideUp 0.6s ease-out; }
        @keyframes slideUp { from { opacity: 0; transform: translateY(40px); } to { opacity: 1; transform: translateY(0); } }
        .logo-header { text-align: center; margin-bottom: 30px; }
        .logo-icon { width: 70px; height: 70px; background: linear-gradient(135deg, var(--primary), var(--secondary)); border-radius: 15px; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; font-size: 35px; box-shadow: 0 10px 30px rgba(79,70,229,0.3); animation: rotate 3s linear infinite, pulse 2s ease-in-out infinite; }
        @keyframes rotate { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        .logo-icon:hover { animation-play-state: paused; }
        .logo-text { font-size: 24px; font-weight: 700; background: linear-gradient(90deg, var(--primary), var(--secondary)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; margin-bottom: 8px; }
        .logo-subtitle { font-size: 13px; color: var(--muted); }
        h2 { font-size: 22px; font-weight: 700; margin-bottom: 8px; color: #fff; }
        .subtitle { font-size: 13px; color: var(--muted); margin-bottom: 28px; }
        .form-group { margin-bottom: 18px; display: flex; flex-direction: column; gap: 8px; }
        .form-group label { font-size: 12px; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: 0.5px; }
        input[type="text"], input[type="email"], input[type="password"], textarea { width: 100%; padding: 14px 16px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.03); color: #e6eef8; font-family: inherit; font-size: 14px; transition: all 0.3s ease; outline: none; }
        input[type="text"]:focus, input[type="email"]:focus, input[type="password"]:focus, textarea:focus { border-color: rgba(79,70,229,0.5); background: rgba(255,255,255,0.06); box-shadow: 0 0 20px rgba(79,70,229,0.2); }
        input[type="text"]::placeholder, input[type="email"]::placeholder, input[type="password"]::placeholder, textarea::placeholder { color: rgba(255,255,255,0.4); }
        textarea { resize: vertical; min-height: 80px; }
        .btn-login { width: 100%; padding: 14px 16px; margin-top: 10px; border-radius: 12px; border: none; background: linear-gradient(90deg, var(--primary), var(--secondary)); color: #071022; font-weight: 700; font-size: 14px; cursor: pointer; transition: all 0.3s ease; position: relative; overflow: hidden; }
        .btn-login::before { content: ''; position: absolute; inset: 0; background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent); transform: translateX(-100%); transition: transform 0.5s; }
        .btn-login:hover { transform: translateY(-3px); box-shadow: 0 15px 30px rgba(79,70,229,0.3); }
        .btn-login:hover::before { transform: translateX(100%); }
        .btn-login:active { transform: translateY(-1px); }
        .footer-link { text-align: center; margin-top: 20px; font-size: 13px; color: var(--muted); }
        .footer-link a { color: var(--primary); text-decoration: none; font-weight: 600; transition: all 0.3s ease; }
        .footer-link a:hover { color: var(--secondary); text-decoration: underline; }
        @media (max-width: 480px) { .login-card { padding: 30px 20px; } .logo-icon { width: 60px; height: 60px; font-size: 30px; } .logo-text { font-size: 20px; } }
    </style>
</head>
<body>
    <div class="bg-animated">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="blob blob-3"></div>
        <div class="floating-icon" style="top:15%; left:8%;">💻</div>
        <div class="floating-icon" style="top:30%; right:5%;">⚡</div>
        <div class="floating-icon" style="bottom:20%; left:10%;">🔌</div>
        <div class="floating-icon" style="bottom:25%; right:8%;">📱</div>
    </div>
    
    <div class="container">
        <div class="login-card">
            <div class="logo-header">
                <div class="logo-icon">⚡</div>
                <div class="logo-text">WarungRizqi</div>
                <div class="logo-subtitle">Daftar Pembeli</div>
            </div>
            
            <h2>Selamat Datang</h2>
            <p class="subtitle">Isi data untuk mendaftar akun baru</p>
            
            <form method="POST">
                <div class="form-group">
                    <label for="nama">Nama Lengkap</label>
                    <i class="fas fa-user input-icon"></i>
                    <input type="text" id="nama" name="nama" placeholder="Nama Lengkap" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <i class="fas fa-envelope input-icon"></i>
                    <input type="email" id="email" name="email" placeholder="your@email.com" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" id="password" name="password" placeholder="••••••••" required>
                </div>

                <div class="form-group">
                    <label for="telp">No. Telepon</label>
                    <i class="fas fa-phone input-icon"></i>
                    <input type="text" id="telp" name="telp" placeholder="No. Telepon" required>
                </div>

                <div class="form-group">
                    <label for="alamat">Alamat Lengkap</label>
                    <i class="fas fa-map-marker-alt input-icon"></i>
                    <textarea id="alamat" name="alamat" placeholder="Alamat Lengkap" required></textarea>
                </div>

                <button type="submit" name="submit" class="btn-login">Daftar Sekarang</button>
            </form>
            
            <div class="footer-link">
                Sudah punya akun? <a href="login-user.php">Login di sini</a>
            </div>
        </div>
    </div>
</body>
</html>

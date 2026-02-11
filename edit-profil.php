<?php
session_start();
include 'db.php';

// Cek login
if (empty($_SESSION['login_user']) || empty($_SESSION['user_id'])) {
    echo '<script>window.location="login-user.php"</script>';
    exit;
}

$user_id = $_SESSION['user_id'];
$user = mysqli_query($conn, "SELECT * FROM tb_user WHERE user_id='$user_id'");
$u = mysqli_fetch_object($user);

// Update data
if (isset($_POST['submit'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $telp = mysqli_real_escape_string($conn, $_POST['telp']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);

    $update = mysqli_query($conn, "UPDATE tb_user SET
        user_nama='$nama',
        user_email='$email',
        user_telp='$telp',
        user_alamat='$alamat'
        WHERE user_id='$user_id'
    ");

    if ($update) {
        echo "<script>alert('Profil berhasil diperbarui!'); window.location='profil-user.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui profil');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Edit Profil - WarungRizqi</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {font-family: 'Quicksand', sans-serif; background: #f5f5f5;}
        header {background: #2e7d32; color: #fff; padding: 15px 0; text-align: center;}
        .edit-container {background: #fff; margin: 50px auto; padding: 30px; border-radius: 10px; width: 90%; max-width: 600px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);}
        input, textarea {width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 6px;}
        .btn-submit {background: #2e7d32; color: #fff; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer;}
        .btn-submit:hover {background: #1b5e20;}
    </style>
</head>
<body>

<header>
    <h1>Edit Profil</h1>
</header>

<div class="edit-container">
    <form method="POST">
        <label>Nama Lengkap</label>
        <input type="text" name="nama" value="<?= $u->user_nama ?>" required>

        <label>Email</label>
        <input type="email" name="email" value="<?= $u->user_email ?>" required>

        <label>Nomor Telepon</label>
        <input type="text" name="telp" value="<?= $u->user_telp ?>">

        <label>Alamat</label>
        <textarea name="alamat"><?= $u->user_alamat ?></textarea>

        <input type="submit" name="submit" value="Simpan Perubahan" class="btn-submit">
    </form>
</div>

</body>
</html>

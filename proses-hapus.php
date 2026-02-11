<?php 
    session_start();
    include 'db.php';

    // Cek login
    if($_SESSION['status_login'] != true){
        echo '<script>alert("Akses ditolak"); window.location="login.php"</script>';
        exit;
    }

    // Ambil tipe dan ID dari parameter
    $type = isset($_GET['type']) ? strtolower($_GET['type']) : '';
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    // Jika type tidak ada, coba deteksi dari parameter lain
    if(empty($type)){
        if(isset($_GET['idk'])){
            $type = 'kategori';
            $id = intval($_GET['idk']);
        } elseif(isset($_GET['idp'])){
            $type = 'produk';
            $id = intval($_GET['idp']);
        } elseif(isset($_GET['idpembelian'])){
            $type = 'pembelian';
            $id = intval($_GET['idpembelian']);
        } elseif(isset($_GET['idu'])){
            $type = 'user';
            $id = intval($_GET['idu']);
        }
    }

    // Validasi
    if($id == 0 || empty($type)){
        echo '<script>alert("❌ Error: Type atau ID tidak valid"); window.history.back()</script>';
        exit;
    }

    switch($type){
        case 'kategori':
        case 'category':
            $cek = mysqli_query($conn, "SELECT category_id FROM tb_category WHERE category_id = $id LIMIT 1");
            
            if(mysqli_num_rows($cek) > 0){
                $delete = mysqli_query($conn, "DELETE FROM tb_category WHERE category_id = $id");
                if($delete){
                    echo '<script>alert("✅ Kategori berhasil dihapus"); window.location="data-kategori.php"</script>';
                } else {
                    echo '<script>alert("❌ Gagal menghapus: '.mysqli_error($conn).'"); window.location="data-kategori.php"</script>';
                }
            } else {
                echo '<script>alert("❌ Kategori dengan ID '.$id.' tidak ditemukan"); window.history.back()</script>';
            }
            break;

        case 'produk':
        case 'product':
            $produk = mysqli_query($conn, "SELECT product_image FROM tb_product WHERE product_id = $id LIMIT 1");
            
            if(mysqli_num_rows($produk) > 0){
                $p = mysqli_fetch_assoc($produk);
                
                if(!empty($p['product_image'])){
                    $image_path = './produk/'.$p['product_image'];
                    if(file_exists($image_path)){
                        @unlink($image_path);
                    }
                }
                
                $delete = mysqli_query($conn, "DELETE FROM tb_product WHERE product_id = $id");
                if($delete){
                    echo '<script>alert("✅ Produk berhasil dihapus"); window.location="data-produk.php"</script>';
                } else {
                    echo '<script>alert("❌ Gagal menghapus: '.mysqli_error($conn).'"); window.location="data-produk.php"</script>';
                }
            } else {
                echo '<script>alert("❌ Produk dengan ID '.$id.' tidak ditemukan"); window.history.back()</script>';
            }
            break;

        case 'pembelian':
        case 'order':
            $cek = mysqli_query($conn, "SELECT id_pembelian FROM tb_pembelian WHERE id_pembelian = $id LIMIT 1");
            
            if(mysqli_num_rows($cek) > 0){
                @mysqli_query($conn, "DELETE FROM tb_detail_pembelian WHERE id_pembelian = $id");
                $delete = mysqli_query($conn, "DELETE FROM tb_pembelian WHERE id_pembelian = $id");
                
                if($delete){
                    echo '<script>alert("✅ Pembelian berhasil dihapus"); window.location="data-pembelian.php"</script>';
                } else {
                    echo '<script>alert("❌ Gagal menghapus: '.mysqli_error($conn).'"); window.location="data-pembelian.php"</script>';
                }
            } else {
                echo '<script>alert("❌ Pembelian dengan ID '.$id.' tidak ditemukan"); window.history.back()</script>';
            }
            break;

        case 'user':
        case 'admin':
            // Cek keamanan: jangan hapus admin yang sedang login
            $current_admin_id = isset($_SESSION['a_global']->admin_id) ? $_SESSION['a_global']->admin_id : 0;
            
            if($id == $current_admin_id){
                echo '<script>alert("❌ Tidak bisa menghapus akun yang sedang login!"); window.history.back()</script>';
                exit;
            }
            
            // Debug: Tampilkan struktur tabel tb_admin
            $columns = mysqli_query($conn, "DESCRIBE tb_admin");
            $col_list = array();
            $primary_key = '';
            while($col = mysqli_fetch_assoc($columns)){
                $col_list[] = $col['Field'];
                if($col['Key'] == 'PRI') $primary_key = $col['Field'];
            }
            
            // Tentukan kolom primary key (biasanya admin_id atau id)
            $pk = !empty($primary_key) ? $primary_key : 'admin_id';
            
            // Cek admin ada atau tidak
            $cek = mysqli_query($conn, "SELECT * FROM tb_admin WHERE $pk = $id LIMIT 1");
            
            if(mysqli_num_rows($cek) > 0){
                $delete = mysqli_query($conn, "DELETE FROM tb_admin WHERE $pk = $id");
                if($delete){
                    echo '<script>alert("✅ Admin berhasil dihapus"); window.location="data-user.php"</script>';
                } else {
                    echo '<script>alert("❌ Gagal menghapus: '.mysqli_error($conn).'"); window.location="data-user.php"</script>';
                }
            } else {
                echo '<script>alert("❌ Admin dengan ID '.$id.' tidak ditemukan\\n\\nKolom yang tersedia:\\n'.implode(", ", $col_list).'\\n\\nPrimary Key: '.$pk.'"); window.history.back()</script>';
            }
            break;

        default:
            echo '<script>alert("❌ Tipe data tidak valid: '.$type.'"); window.history.back()</script>';
    }
?>
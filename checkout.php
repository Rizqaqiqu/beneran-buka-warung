<?php
session_start();
include 'db.php';

$produk_list = [];
$grand_total = 0;
$message = '';

// --- helper: load products into $produk_list and compute $grand_total ---
function load_products_from_post_or_cart($conn) {
    $list = [];
    $total = 0;

    // From "Beli Sekarang" single product
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id']) && !isset($_POST['nama'])) {
        $id = (int)$_POST['product_id'];
        $jumlah = isset($_POST['jumlah']) ? (int)$_POST['jumlah'] : 1;
        $result = mysqli_query($conn, "SELECT * FROM tb_product WHERE product_id = $id");
        if ($row = mysqli_fetch_assoc($result)) {
            $raw_price = (float)($row['product_price'] ?? 0.0);
            $effective = $raw_price;
            if (isset($row['product_discount']) && (float)$row['product_discount'] > 0) {
                $disc = (float)$row['product_discount'];
                $effective = round($raw_price * (1 - $disc / 100));
            } elseif (isset($row['product_discount_price']) && (float)$row['product_discount_price'] > 0) {
                $effective = (float)$row['product_discount_price'];
            }
            $row['product_price_raw'] = $raw_price;
            $row['product_price'] = $effective;
            $row['jumlah'] = $jumlah;
            $row['subtotal'] = $row['product_price'] * $jumlah;
            $list[$id] = $row;
            $total += $row['subtotal'];
        }
        $_SESSION['checkout_produk'] = $list;
        return [$list, $total];
    }

    // From keranjang/session
    if (isset($_SESSION['keranjang']) && !empty($_SESSION['keranjang'])) {
        $keranjang = $_SESSION['keranjang'];
        $ids = implode(',', array_map('intval', array_keys($keranjang)));
        if ($ids) {
            $result = mysqli_query($conn, "SELECT * FROM tb_product WHERE product_id IN ($ids)");
            while ($row = mysqli_fetch_assoc($result)) {
                $id = (int)$row['product_id'];
                $jumlah = isset($keranjang[$id]) ? (int)$keranjang[$id] : 0;
                if ($jumlah <= 0) continue;
                $raw_price = (float)($row['product_price'] ?? 0.0);
                $effective = $raw_price;
                if (isset($row['product_discount']) && (float)$row['product_discount'] > 0) {
                    $disc = (float)$row['product_discount'];
                    $effective = round($raw_price * (1 - $disc / 100));
                } elseif (isset($row['product_discount_price']) && (float)$row['product_discount_price'] > 0) {
                    $effective = (float)$row['product_discount_price'];
                }
                $row['product_price_raw'] = $raw_price;
                $row['product_price'] = $effective;
                $row['jumlah'] = $jumlah;
                $row['subtotal'] = $row['product_price'] * $jumlah;
                $list[$id] = $row;
                $total += $row['subtotal'];
            }
            $_SESSION['checkout_produk'] = $list;
        }
    } elseif (!empty($_SESSION['checkout_produk'])) {
        // Use existing session checkout_produk
        $list = $_SESSION['checkout_produk'];
        $total = 0;
        foreach ($list as $p) {
            $total += isset($p['subtotal']) ? (float)$p['subtotal'] : 0;
        }
    }

    return [$list, $total];
}

list($produk_list, $grand_total) = load_products_from_post_or_cart($conn);

// If checkout submit (with buyer info)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nama']) && isset($_POST['metode'])) {
    // update session quantities and recalc subtotals
    if (!empty($_SESSION['checkout_produk'])) {
        $produk_terbaru = [];
        foreach ($_SESSION['checkout_produk'] as $id => $p) {
            $jumlah = isset($_POST['jumlah_'.$id]) ? (int)$_POST['jumlah_'.$id] : $p['jumlah'];
            $price_used = isset($p['product_price']) ? (float)$p['product_price'] : (float)($p['product_price_raw'] ?? 0);
            $subtotal = $price_used * $jumlah;
            $produk_terbaru[$id] = [
                'product_id' => $id,
                'product_name' => $p['product_name'],
                'product_price_raw' => $p['product_price_raw'] ?? $price_used,
                'product_price' => $price_used,
                'product_image' => $p['product_image'],
                'jumlah' => $jumlah,
                'subtotal' => $subtotal
            ];
        }
        $_SESSION['checkout_produk'] = $produk_terbaru;
    }

    // save buyer & ongkir
    $_SESSION['user_nama'] = $_POST['nama'];
    $_SESSION['user_email'] = $_POST['email'];
    $_SESSION['user_telp'] = $_POST['telp'];
    $_SESSION['user_alamat'] = $_POST['alamat'];
    $_SESSION['metode_pembayaran'] = $_POST['metode'];
    $_SESSION['ongkir'] = isset($_POST['ongkir']) ? (float)$_POST['ongkir'] : 0;
    $_SESSION['catatan'] = $_POST['catatan'] ?? '';

    // redirect ke halaman pembayaran
    header('Location: pembayaran.php');
    exit;
}

// current shipping from session
$current_ongkir = isset($_SESSION['ongkir']) ? (float)$_SESSION['ongkir'] : 0;

// compute totals (server-side authoritative)
$subtotal_server = 0;
foreach ($produk_list as $p) $subtotal_server += $p['subtotal'] ?? 0;

$promo_amount = 0; // removed promo feature
$total_with_ongkir = $subtotal_server + $current_ongkir;
if ($total_with_ongkir < 0) $total_with_ongkir = 0;

// DEBUG sementara: catat semua POST ke file untuk memastikan form terkirim
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("CHECKOUT POST: " . print_r($_POST, true));
    file_put_contents(__DIR__ . '/tmp_post_log.txt', date('c') . " - " . print_r($_POST, true) . PHP_EOL, FILE_APPEND);
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Checkout - WarungRizqi</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        :root{--primary:#1976d2;--muted:#6b7280}
        body{font-family: 'Quicksand', system-ui, sans-serif;background:#f5f8ff;margin:0;padding:24px}
        .container{max-width:980px;margin:0 auto;background:#fff;border-radius:12px;padding:20px;box-shadow:0 8px 30px rgba(13,38,76,0.06)}
        h3{margin:0 0 18px;color:var(--primary)}
        .box{display:flex;flex-direction:column;gap:12px}
        .keranjang-box{display:flex;align-items:center;justify-content:space-between;padding:12px;border-radius:8px;border:1px solid #f0f3f8;background:#fafcff}
        .keranjang-left{display:flex;align-items:center;gap:12px;flex:1}
        .keranjang-box img{width:84px;border-radius:10px;object-fit:cover}
        .produk-info h4{margin:0;font-size:16px}
        .produk-info p{margin:6px 0;color:var(--muted)}
        .jumlah-input{width:80px;text-align:center;border-radius:6px;border:1px solid #ddd;padding:6px}
        .subtotal{min-width:120px;text-align:right;font-weight:600;color:var(--primary)}
        .total-panel{margin-top:14px;padding:12px;border-radius:10px;background:linear-gradient(180deg,#fff,#f8fbff);border:1px solid #e6f0ff}
        .total-line{display:flex;justify-content:space-between;padding:6px 0;color:#333}
        .total-line.small{color:var(--muted);font-size:14px}
        .btn{background:var(--primary);color:#fff;padding:10px 12px;border-radius:8px;border:0;cursor:pointer}
        .btn.ghost{background:#fff;border:1px solid #ddd;color:var(--primary)}
        .note{font-size:13px;color:var(--muted);margin-top:8px}
        .message{padding:8px;border-radius:8px;background:#eef7ff;color:#054a91;margin-bottom:10px}
        /* Shipping option cards */
        .ship-options { display:flex; gap:10px; margin-top:8px; flex-wrap:wrap; }
        .ship-card {
            flex: 1 1 180px;
            border:1px solid #e6eefc;
            background:#fff;
            border-radius:10px;
            padding:10px;
            display:flex;
            gap:10px;
            align-items:center;
            cursor:pointer;
            transition:box-shadow .18s, transform .12s;
        }
        .ship-card:hover { box-shadow:0 8px 24px rgba(13,38,76,0.06); transform:translateY(-4px); }
        .ship-card input[type="radio"] { display:none; }
        .ship-card .meta { flex:1 }
        .ship-card .meta .name { font-weight:700; color:#123456; }
        .ship-card .meta .eta { font-size:13px; color:var(--muted) }
        .ship-card .price { font-weight:800; color:var(--primary); min-width:90px; text-align:right; }
        .ship-card.selected { border-color: var(--primary); box-shadow:0 10px 30px rgba(25,118,210,0.08); }
        @media(max-width:720px){
            .keranjang-box{flex-direction:column;align-items:flex-start}
            .subtotal{text-align:left;width:100%}
            .keranjang-left{width:100%}
            .ship-options { flex-direction:column; }
        }
    </style>
</head>
<body>
<div class="container">
    <h3>Checkout</h3>

    <?php if (empty($produk_list)) : ?>
        <p>Keranjang kosong. Silakan pilih produk untuk dibeli.</p>
    <?php else : ?>
        <?php if ($message): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <div class="box">
            <?php foreach ($produk_list as $p): ?>
                <div class="keranjang-box">
                    <div class="keranjang-left">
                        <img src="produk/<?php echo htmlspecialchars($p['product_image']); ?>" alt="">
                        <div class="produk-info">
                            <h4><?php echo htmlspecialchars($p['product_name']); ?></h4>
                            <?php if (isset($p['product_price_raw']) && $p['product_price'] < $p['product_price_raw']): ?>
                                <p><span style="text-decoration:line-through;color:#9aa4ad">Rp. <?php echo number_format($p['product_price_raw']); ?></span>
                                &nbsp;<strong>Rp. <?php echo number_format($p['product_price']); ?></strong></p>
                            <?php else: ?>
                                <p>Harga: Rp. <?php echo number_format($p['product_price']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div style="display:flex;gap:12px;align-items:center">
                        <input type="number" min="1" class="jumlah-input qty-visible" data-id="<?php echo $p['product_id']; ?>" value="<?php echo $p['jumlah']; ?>">
                        <div class="subtotal" id="subtotal-<?php echo $p['product_id']; ?>">Rp. <?php echo number_format($p['subtotal']); ?></div>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="total-panel">
                <div class="total-line small"><span>Subtotal</span><span id="subTotalText">Rp. <?php echo number_format($subtotal_server); ?></span></div>

                <div class="total-line small" style="margin-top:8px">
                    <span>Pilih Ongkir</span>
                    <div style="flex:1"></div>
                </div>

                <div class="ship-options" id="shipOptions">
                    <label class="ship-card" data-value="0">
                        <input type="radio" name="ongkir_option" value="0" <?php echo ($current_ongkir==0)?'checked':''; ?>>
                        <div class="meta">
                            <div class="name">Ambil di Toko</div>
                            <div class="eta">Siap diambil — Bebas biaya</div>
                        </div>
                        <div class="price">Rp. 0</div>
                    </label>

                    <label class="ship-card" data-value="10000">
                        <input type="radio" name="ongkir_option" value="10000" <?php echo ($current_ongkir==10000)?'checked':''; ?>>
                        <div class="meta">
                            <div class="name">Kurir Lokal</div>
                            <div class="eta">Estimasi 1-2 hari kerja</div>
                        </div>
                        <div class="price">Rp. 10.000</div>
                    </label>

                    <label class="ship-card" data-value="15000">
                        <input type="radio" name="ongkir_option" value="15000" <?php echo ($current_ongkir==15000)?'checked':''; ?>>
                        <div class="meta">
                            <div class="name">JNE Reguler</div>
                            <div class="eta">Estimasi 2-4 hari kerja</div>
                        </div>
                        <div class="price">Rp. 15.000</div>
                    </label>

                    <label class="ship-card" data-value="18000">
                        <input type="radio" name="ongkir_option" value="18000" <?php echo ($current_ongkir==18000)?'checked':''; ?>>
                        <div class="meta">
                            <div class="name">J&T Express</div>
                            <div class="eta">Estimasi 1-3 hari kerja</div>
                        </div>
                        <div class="price">Rp. 18.000</div>
                    </label>
                </div>

                <div class="total-line" style="margin-top:12px;font-size:18px">
                    <strong>Grand Total</strong>
                    <strong id="grandTotal">Rp. <?php echo number_format($total_with_ongkir); ?></strong>
                </div>
                <div class="note">Harga sudah termasuk diskon (jika ada).</div>
            </div>

            <form id="checkoutForm" method="POST" action="" style="margin-top:14px">
                <div id="hiddenQuantities"></div>
                <input type="hidden" name="ongkir" id="ongkirHidden" value="<?php echo $current_ongkir; ?>">

                <style>
                    .form-grid { display:flex; gap:18px; align-items:flex-start; margin-top:6px; flex-wrap:wrap; }
                    .form-col { flex:1 1 420px; min-width:260px; }
                    .summary-col { flex:0 0 320px; min-width:280px; }
                    .field { display:flex; flex-direction:column; margin-bottom:10px; }
                    .field label { font-size:13px; color:#374151; margin-bottom:6px; font-weight:600; }
                    .field input[type="text"], .field input[type="email"], .field input[type="tel"], .field textarea, .field select {
                        padding:10px; border-radius:8px; border:1px solid #e6eefc; background:#fff; outline:none;
                        font-size:14px; color:#0f172a;
                    }
                    .field textarea { min-height:90px; resize:vertical; }
                    .summary-box { background:#fff; padding:12px; border-radius:10px; border:1px solid #e6f0ff; box-shadow: 0 6px 18px rgba(13,38,76,0.03); }
                    .summary-row { display:flex; justify-content:space-between; margin:8px 0; color:#374151; }
                    .summary-row.total { font-weight:800; color:#0b5ed7; font-size:18px; }
                    .small-note { font-size:13px; color:#6b7280; margin-top:8px; }
                    .btn-primary { width:100%; padding:12px; border-radius:10px; border:0; background:#1976d2; color:#fff; font-weight:700; cursor:pointer; }
                    .btn-secondary { display:inline-block; padding:10px 12px; border-radius:10px; border:1px solid #e6eefc; background:#fff; color:#1976d2; text-decoration:none; font-weight:600; text-align:center; }
                </style>

                <div class="form-grid">
                    <div class="form-col">
                        <div class="field">
                            <label for="inpNama">Nama Lengkap</label>
                            <input id="inpNama" name="nama" type="text" placeholder="Nama lengkap sesuai KTP / identitas" required
                                value="<?= htmlspecialchars($_SESSION['user_nama'] ?? '') ?>">
                        </div>

                        <div class="field">
                            <label for="inpEmail">Email</label>
                            <input id="inpEmail" name="email" type="email" placeholder="contoh@mail.com" required
                                value="<?= htmlspecialchars($_SESSION['user_email'] ?? '') ?>">
                        </div>

                        <div class="field">
                            <label for="inpTelp">No. Telepon</label>
                            <input id="inpTelp" name="telp" type="tel" placeholder="0812xxxxxx" required
                                pattern="^[0-9+\-\s()]{6,20}$"
                                value="<?= htmlspecialchars($_SESSION['user_telp'] ?? '') ?>">
                            <small class="small-note">Masukkan nomor aktif untuk konfirmasi pesanan.</small>
                        </div>

                        <div class="field">
                            <label for="inpAlamat">Alamat Pengiriman</label>
                            <textarea id="inpAlamat" name="alamat" placeholder="Jalan, RT/RW, Kelurahan, Kecamatan, Kota" required><?= htmlspecialchars($_SESSION['user_alamat'] ?? '') ?></textarea>
                        </div>

                        <div class="field">
                            <label for="inpCatatan">Catatan untuk Penjual (opsional)</label>
                            <textarea id="inpCatatan" name="catatan" placeholder="Contoh: minta packing rapih / kirim di jam tertentu"></textarea>
                        </div>

                        <!-- REPLACE select dengan payment card UI + hidden input (diperbarui dengan opsi bank/wallet) -->
                        <div class="field">
                            <label for="payMethods">Metode Pembayaran</label>

                            <!-- hidden yang dikirim ke server -->
                            <input type="hidden" name="metode" id="metodeHidden" value="<?php echo htmlspecialchars($_SESSION['metode_pembayaran'] ?? 'Transfer Bank'); ?>">
                            <input type="hidden" name="metode_detail" id="metodeDetailHidden" value="<?php echo htmlspecialchars($_SESSION['metode_detail'] ?? ''); ?>">

                            <style>
                                /* payment cards (local style) */
                                .pay-methods { display:flex; gap:8px; flex-wrap:wrap; margin-top:8px; }
                                .pay-card {
                                    flex:1 1 140px;
                                    min-width:120px;
                                    background:#fff;
                                    border:1px solid #e6eefc;
                                    border-radius:10px;
                                    padding:10px;
                                    display:flex;
                                    align-items:center;
                                    justify-content:space-between;
                                    gap:10px;
                                    cursor:pointer;
                                    transition:transform .14s,box-shadow .14s,border-color .14s;
                                }
                                .pay-card:hover{ transform:translateY(-6px); box-shadow:0 12px 30px rgba(13,38,76,0.06); }
                                .pay-card .left { display:flex; flex-direction:column; gap:4px; }
                                .pay-card .name { font-weight:700; color:#0f172a; font-size:14px; }
                                .pay-card .desc { font-size:12px; color:#6b7280; }
                                .pay-card .icon { color:#1976d2; opacity:.95; }
                                .pay-card.selected { border-color:#1976d2; box-shadow:0 18px 46px rgba(25,118,210,0.08); transform:translateY(-8px) scale(1.01); }

                                /* detail panels */
                                .pay-details { margin-top:10px; display:flex; gap:8px; flex-wrap:wrap; }
                                .detail-panel { flex:1 1 100%; display:none; gap:8px; align-items:center; }
                                .detail-panel.active { display:flex; }
                                .option { flex:1 1 100%; display:flex; align-items:center; gap:10px; padding:8px;border-radius:8px;border:1px solid #eef6ff;background:#fff; cursor:pointer; transition:transform .12s; }
                                .option:hover { transform:translateY(-4px); box-shadow:0 10px 30px rgba(13,38,76,0.04); }
                                .option input[type="radio"]{ margin-right:8px; }
                                .option .label { font-weight:600 }
                                @media(max-width:640px){ .pay-methods{flex-direction:column} .detail-panel{flex-direction:column} }
                            </style>

                            <div class="pay-methods" id="payMethods" role="radiogroup" aria-label="Metode Pembayaran">
                                <div class="pay-card" data-method="Transfer Bank" role="radio" aria-checked="false">
                                    <div class="left">
                                        <div class="name">Transfer Bank</div>
                                        <div class="desc">Rekening BCA / Mandiri / BNI / BRI</div>
                                    </div>
                                    <div class="icon">🏦</div>
                                </div>

                                <div class="pay-card" data-method="E-Wallet" role="radio" aria-checked="false">
                                    <div class="left">
                                        <div class="name">E-Wallet</div>
                                        <div class="desc">OVO, GoPay, Dana, ShopeePay</div>
                                    </div>
                                    <div class="icon">📱</div>
                                </div>

                                <div class="pay-card" data-method="COD" role="radio" aria-checked="false">
                                    <div class="left">
                                        <div class="name">COD</div>
                                        <div class="desc">Bayar di tempat</div>
                                    </div>
                                    <div class="icon">📦</div>
                                </div>
                            </div>

                            <div class="pay-details" id="payDetails">
                                <!-- bank options -->
                                <div id="bankOptions" class="detail-panel" aria-hidden="true">
                                    <label class="option"><input type="radio" name="bank_option" value="BCA"> <span class="label">BCA</span> <small class="small"> — 1234567890</small></label>
                                    <label class="option"><input type="radio" name="bank_option" value="Mandiri"> <span class="label">Mandiri</span> <small class="small"> — 9876543210</small></label>
                                    <label class="option"><input type="radio" name="bank_option" value="BNI"> <span class="label">BNI</span> <small class="small"> — 1122334455</small></label>
                                    <label class="option"><input type="radio" name="bank_option" value="BRI"> <span class="label">BRI</span> <small class="small"> — 5544332211</small></label>
                                </div>

                                <!-- e-wallet options -->
                                <div id="walletOptions" class="detail-panel" aria-hidden="true">
                                    <label class="option"><input type="radio" name="wallet_option" value="OVO"> <span class="label">OVO</span> <small class="small"> — 0812xxxxxx</small></label>
                                    <label class="option"><input type="radio" name="wallet_option" value="GoPay"> <span class="label">GoPay</span> <small class="small"> — 0812xxxxxx</small></label>
                                    <label class="option"><input type="radio" name="wallet_option" value="Dana"> <span class="label">Dana</span> <small class="small"> — 0812xxxxxx</small></label>
                                    <label class="option"><input type="radio" name="wallet_option" value="ShopeePay"> <span class="label">ShopeePay</span> <small class="small"> — 0812xxxxxx</small></label>
                                </div>

                                <!-- cod info -->
                                <div id="codPanel" class="detail-panel" aria-hidden="true">
                                    <div class="option" style="cursor:default"><div class="label">COD</div><small class="small">Siapkan uang tunai saat kurir tiba.</small></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="summary-col">
                        <div class="summary-box">
                            <div style="display:flex;justify-content:space-between;align-items:center">
                                <strong>Ringkasan Pesanan</strong>
                                <span class="small-note">#<?php echo date('Ymd').rand(10,99); ?></span>
                            </div>

                            <div class="summary-row" style="margin-top:12px">
                                <span>Subtotal</span>
                                <span id="subTotalText_side">Rp. <?php echo number_format($subtotal_server); ?></span>
                            </div>

                            <div class="summary-row">
                                <span>Ongkir</span>
                                <span id="ongkirText_side">Rp. <?php echo number_format($current_ongkir); ?></span>
                            </div>

                            <div class="summary-row total">
                                <span>Grand Total</span>
                                <span id="grandTotal_side">Rp. <?php echo number_format($total_with_ongkir); ?></span>
                            </div>

                            <div class="small-note">Harga sudah termasuk diskon produk.</div>

                            <div style="margin-top:12px; display:flex; gap:8px;">
                                <button type="submit" class="btn-primary" id="btnCheckout">Konfirmasi & Checkout</button>
                                <a href="keranjang.php" class="btn-secondary">Kembali</a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <script>
                // Simple localDraft for personal data
                (function(){
                    const fields = ['inpNama','inpEmail','inpTelp','inpAlamat','inpCatatan'];
                    const storageKey = 'checkout_form_draft';
                    const draft = JSON.parse(localStorage.getItem(storageKey) || '{}');
                    fields.forEach(id => {
                        const el = document.getElementById(id);
                        if (!el) return;
                        if (!el.value && draft[id]) el.value = draft[id];
                        el.addEventListener('input', () => {
                            const data = JSON.parse(localStorage.getItem(storageKey) || '{}');
                            data[id] = el.value;
                            localStorage.setItem(storageKey, JSON.stringify(data));
                        });
                    });
                    document.getElementById('checkoutForm').addEventListener('submit', function(){
                        localStorage.removeItem(storageKey);
                    });
                    function mirrorTotals() {
                        const sub = document.getElementById('subTotalText').textContent;
                        document.getElementById('subTotalText_side').textContent = sub;
                        document.getElementById('ongkirText_side').textContent = document.getElementById('ongkirText') ? document.getElementById('ongkirText').textContent : document.getElementById('ongkirText_side').textContent;
                        document.getElementById('grandTotal_side').textContent = document.getElementById('grandTotal').textContent;
                    }
                    mirrorTotals();
                    window.recalcTotals = (function(orig){
                        return function(){ if (typeof orig === 'function') orig(); mirrorTotals(); }
                    })(window.recalcTotals);
                })();
            </script>
            <!-- END form -->
        </div>
    <?php endif; ?>
</div>

<script>
const hargaMap = <?php echo json_encode(array_column($produk_list, 'product_price', 'product_id')); ?> || {};
const qtyInputs = document.querySelectorAll('.qty-visible');
const ongkirHidden = document.getElementById('ongkirHidden');
const shipOptions = document.getElementById('shipOptions');
const ongkirRadios = shipOptions ? shipOptions.querySelectorAll('input[name="ongkir_option"]') : [];
const subTotalText = document.getElementById('subTotalText');
const grandTotalText = document.getElementById('grandTotal');

function formatRp(n) { return 'Rp. ' + Number(n).toLocaleString(); }

function recalcTotals() {
    let subtotal = 0;
    qtyInputs.forEach(el => {
        const id = el.dataset.id;
        const q = parseInt(el.value) || 0;
        const harga = parseFloat(hargaMap[id]) || 0;
        const s = q * harga;
        const subtotalEl = document.getElementById('subtotal-' + id);
        if (subtotalEl) subtotalEl.textContent = formatRp(s);
        subtotal += s;
    });

    let ongkir = 0;
    ongkirRadios.forEach(r => { if (r.checked) ongkir = parseFloat(r.value) || 0; });
    subTotalText.textContent = formatRp(subtotal);
    ongkirHidden.value = ongkir;
    grandTotalText.textContent = formatRp(subtotal + ongkir);

    // update side summary
    const subSide = document.getElementById('subTotalText_side');
    const ongSide = document.getElementById('ongkirText_side');
    const grandSide = document.getElementById('grandTotal_side');
    if (subSide) subSide.textContent = subTotalText.textContent;
    if (ongSide) ongSide.textContent = document.getElementById('ongkirText') ? document.getElementById('ongkirText').textContent : formatRp(ongkir);
    if (grandSide) grandSide.textContent = grandTotalText.textContent;
}

// set initial selected card visual
document.querySelectorAll('.ship-card').forEach(card => {
    const radio = card.querySelector('input[type="radio"]');
    if (radio && radio.checked) card.classList.add('selected');
});

qtyInputs.forEach(el => {
    el.addEventListener('change', recalcTotals);
    el.addEventListener('input', recalcTotals);
});

// handle card clicks / radio change
shipOptions && shipOptions.addEventListener('click', function(e){
    let label = e.target.closest('.ship-card');
    if (!label) return;
    const radio = label.querySelector('input[type="radio"]');
    if (radio) radio.checked = true;
    document.querySelectorAll('.ship-card').forEach(c => c.classList.remove('selected'));
    label.classList.add('selected');
    recalcTotals();
});

ongkirRadios.forEach(r => r.addEventListener('change', function(){
    document.querySelectorAll('.ship-card').forEach(c => {
        c.classList.toggle('selected', c.querySelector('input[type="radio"]').checked);
    });
    recalcTotals();
}));

// before submit: copy quantities into hidden inputs for server
document.getElementById('checkoutForm').addEventListener('submit', function(e){
    const container = document.getElementById('hiddenQuantities');
    container.innerHTML = '';
    qtyInputs.forEach(el => {
        const id = el.dataset.id;
        const val = parseInt(el.value) || 1;
        const inp = document.createElement('input');
        inp.type = 'hidden';
        inp.name = 'jumlah_' + id;
        inp.value = val;
        container.appendChild(inp);
    });
    // also ensure ongkir hidden updated from selected radio
    let ong = 0;
    ongkirRadios.forEach(r => { if (r.checked) ong = parseFloat(r.value) || 0; });
    ongkirHidden.value = ong;
    // allow normal submit
});

// Payment card selection logic + bank/wallet details
(function(){
    const metodeHidden = document.getElementById('metodeHidden');
    const metodeDetailHidden = document.getElementById('metodeDetailHidden');
    const payMethods = document.getElementById('payMethods');
    const payDetails = document.getElementById('payDetails');
    if (!payMethods || !payDetails) return;

    const bankPanel = document.getElementById('bankOptions');
    const walletPanel = document.getElementById('walletOptions');
    const codPanel = document.getElementById('codPanel');

    const cards = Array.from(payMethods.querySelectorAll('.pay-card'));
    const initial = (metodeHidden && metodeHidden.value) ? metodeHidden.value : 'Transfer Bank';
    // apply initial selection
    cards.forEach(c => {
        const method = c.dataset.method;
        if (method === initial) {
            selectCard(c, false);
        } else {
            c.classList.remove('selected');
            c.setAttribute('aria-checked','false');
        }
    });

    // helper: show detail panel
    function showDetailFor(method){
        [bankPanel,walletPanel,codPanel].forEach(p=>{ if(p) p.classList.remove('active'); if(p) p.setAttribute('aria-hidden','true'); });
        if (method === 'Transfer Bank') {
            bankPanel.classList.add('active'); bankPanel.setAttribute('aria-hidden','false');
            // pick first bank if none selected
            const chosen = document.querySelector('input[name="bank_option"]:checked');
            if (!chosen) {
                const first = bankPanel.querySelector('input[name="bank_option"]');
                if (first) { first.checked = true; metodeDetailHidden.value = first.value; }
            } else metodeDetailHidden.value = chosen.value;
        } else if (method === 'E-Wallet') {
            walletPanel.classList.add('active'); walletPanel.setAttribute('aria-hidden','false');
            const chosen = document.querySelector('input[name="wallet_option"]:checked');
            if (!chosen) {
                const first = walletPanel.querySelector('input[name="wallet_option"]');
                if (first) { first.checked = true; metodeDetailHidden.value = first.value; }
            } else metodeDetailHidden.value = chosen.value;
        } else {
            codPanel.classList.add('active'); codPanel.setAttribute('aria-hidden','false');
            metodeDetailHidden.value = '';
        }
    }

    // card click handler
    function selectCard(card, announce=true){
        cards.forEach(x=>{ x.classList.remove('selected'); x.setAttribute('aria-checked','false'); });
        card.classList.add('selected');
        card.setAttribute('aria-checked','true');
        const method = card.dataset.method;
        if (metodeHidden) metodeHidden.value = method;
        showTinyToast('Metode: ' + method);
        showDetailFor(method);
        if (announce) card.animate([{transform:'scale(1)'},{transform:'scale(1.03)'},{transform:'scale(1)'}],{duration:320,easing:'ease-out'});
    }

    cards.forEach(c => c.addEventListener('click', ()=> selectCard(c)));

    // when user chooses bank/wallet option, update hidden detail
    payDetails.addEventListener('change', function(e){
        const t = e.target;
        if (!t) return;
        if (t.name === 'bank_option' || t.name === 'wallet_option') {
            metodeDetailHidden.value = t.value;
            showTinyToast('Pilihan: ' + t.value);
        }
    });

    // re-use tiny toast defined earlier (or define if missing)
    function showTinyToast(msg){
        let t = document.getElementById('checkoutTinyToast');
        if(!t){
            t = document.createElement('div');
            t.id = 'checkoutTinyToast';
            t.style.position='fixed';
            t.style.right='18px';
            t.style.bottom='18px';
            t.style.padding='10px 12px';
            t.style.background='rgba(2,6,23,0.9)';
            t.style.color='#fff';
            t.style.borderRadius='10px';
            t.style.boxShadow='0 8px 30px rgba(2,6,23,0.28)';
            t.style.zIndex = 9999;
            t.style.opacity = '0';
            t.style.transition = 'opacity .24s, transform .24s';
            document.body.appendChild(t);
        }
        t.textContent = msg;
        t.style.opacity = '1';
        t.style.transform = 'translateY(0)';
        clearTimeout(t._hideTimer);
        t._hideTimer = setTimeout(()=>{ t.style.opacity='0'; t.style.transform='translateY(8px)'; }, 1800);
    }

    // ensure hidden fields set before submit
    const checkoutForm = document.getElementById('checkoutForm');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(){
            // if metodeHidden missing, set to Transfer Bank
            if (!metodeHidden.value) metodeHidden.value = 'Transfer Bank';
            // ensure detail exists if bank/wallet active
            const method = metodeHidden.value;
            if (method === 'Transfer Bank' && !metodeDetailHidden.value) {
                const first = bankPanel.querySelector('input[name="bank_option"]');
                if (first) metodeDetailHidden.value = first.value;
            }
            if (method === 'E-Wallet' && !metodeDetailHidden.value) {
                const first = walletPanel.querySelector('input[name="wallet_option"]');
                if (first) metodeDetailHidden.value = first.value;
            }
        });
    }
})();
</script>
</body>
</html>

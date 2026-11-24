<?php
session_start();
include 'koneksi.php';

// Cek apakah user sudah login
$is_logged_in = isset($_SESSION['user_login']) && $_SESSION['user_login'] === true;
$user_nama    = $is_logged_in ? $_SESSION['user_nama'] : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MAMAH CATERING - Makanan Rumahan Enak</title>

    <!-- FONT AWESOME -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body>

<!-- ================= NAVBAR ================= -->
<header class="navbar">
    <a href="#" class="logo">
        <i class="fa-solid fa-utensils"></i>
        <div>
            <h2>MAMAH<span>CATERING</span></h2>
            <small>siap menerima pesanan</small>
        </div>
    </a>

    <nav>
        <a href="#" class="active">Beranda</a>
        <a href="#produk">Produk</a>
        <a href="#cara-order">Cara Order</a>
        <a href="#promo">Promo</a>
        <a href="#tentang">Tentang Kami</a>
    </nav>

    <div class="navbar-right">
        <a href="#status-modal" class="status-btn" onclick="openStatusModal(); return false;">
            <i class="fa-solid fa-clock-rotate-left"></i> Status Pesanan
        </a>
        
        <!-- INTEGRASI USER LOGIN / LOGOUT -->
        <?php if ($is_logged_in): ?>
            <span style="font-size: 14px; font-weight: 600; color: #333;">
                <i class="fa-solid fa-user"></i> Halo, <?php echo htmlspecialchars($user_nama); ?>
            </span>
            <a href="logout.php" class="admin-btn" style="background-color: #ef4444;" onclick="return confirm('Yakin ingin keluar?');">
                <i class="fa-solid fa-right-from-bracket"></i> Logout
            </a>
        <?php else: ?>
            <a href="login.php" class="admin-btn" style="background-color: #10b981;">
                <i class="fa-solid fa-right-to-bracket"></i> Login / Daftar
            </a>
        <?php endif; ?>

        <a href="#cart" class="cart" aria-label="Buka keranjang" onclick="checkCartAccess(event)">
            <i class="fa-solid fa-cart-shopping"></i>
            <span id="cartCount">0</span>
        </a>
    </div>
</header>


<!-- ================= HERO ================= -->
<section class="hero">
    <div class="hero-text">
        <p>Selamat Datang di</p>
        <h1>MAMAH<span>CATERING</span></h1>
        <h2>Makanan Rumahan Enak,<br>Dibuat Khusus Untukmu.</h2>
        <p class="hero-desc">
            Menerima pesanan untuk acara ulang tahun, pengajian, pernikahan, dll.
            <b>Pre-Order</b>. Pilih menu, tentukan tanggal, dan kami siapkan pesananmu.
        </p>
        <div class="hero-button">
            <a href="#produk" class="btn-primary"><i class="fa-solid fa-utensils"></i> Lihat Menu</a>
            <a href="#cara-order" class="btn-outline"><i class="fa-solid fa-circle-info"></i> Cara Order</a>
        </div>
    </div>

    <div class="hero-photo">
        <div class="circle"></div>
        <img src="logo.jpeg" alt="Makanan Catering">
    </div>
</section>


<!-- ================= KATEGORI ================= -->
<section class="section" id="produk">
    <div class="section-heading">
        <h2>Kategori Menu</h2>
        <p>Pilih makanan sesuai selera kamu dan untuk melengkapi acara kamu.</p>
    </div>

    <div class="category-list">
        <a href="#makanan-berat" class="category">
            <img src="uploads/mknberat.jpeg" alt="Makanan Berat" onerror="this.src='https://via.placeholder.com/150?text=Makanan+Berat'">
            <div>
                <h3>Makanan Berat</h3>
                <i class="fa-solid fa-arrow-right"></i>
            </div>
        </a>

        <a href="#makanan-ringan" class="category">
            <img src="uploads/mknringan.jpeg" alt="Makanan Ringan" onerror="this.src='https://via.placeholder.com/150?text=Makanan+Ringan'">
            <div>
                <h3>Makanan Ringan</h3>
                <i class="fa-solid fa-arrow-right"></i>
            </div>
        </a>

        <a href="#snack" class="category">
            <img src="uploads/snack.jpeg" alt="Snack" onerror="this.src='https://via.placeholder.com/150?text=Snack'">
            <div>
                <h3>Snack</h3>
                <i class="fa-solid fa-arrow-right"></i>
            </div>
        </a>

        <a href="#dessert" class="category">
            <img src="uploads/mknmanis.jpeg" alt="Makanan Manis" onerror="this.src='https://via.placeholder.com/150?text=Makanan+Manis'">
            <div>
                <h3>Makanan Manis</h3>
                <i class="fa-solid fa-arrow-right"></i>
            </div>
        </a>

        <a href="#drink" class="category">
            <img src="uploads/kopi.jpg" alt="Minuman" onerror="this.src='https://via.placeholder.com/150?text=Minuman'">
            <div>
                <h3>Minuman</h3>
                <i class="fa-solid fa-arrow-right"></i>
            </div>
        </a>
    </div>
</section>


<!-- ================= SEKSI MENU PRODUK ================= -->
<section class="section menu-section" id="menu">
    <?php
    $categories = [
        'makanan-berat'  => ['title' => 'Makanan Berat', 'desc' => 'Menu utama yang cocok untuk makan pagi, siang, malam di acara kamu.'],
        'makanan-ringan' => ['title' => 'Makanan Ringan', 'desc' => 'Cocok untuk teman santai dan menemani waktu berkumpul keluarga.'],
        'snack'          => ['title' => 'Snack', 'desc' => 'Pilihan snack untuk acara, rapat, maupun santai.'],
        'dessert'        => ['title' => 'Makanan Manis', 'desc' => 'Dessert manis untuk melengkapi acara besar kamu.'],
        'drink'          => ['title' => 'Minuman', 'desc' => 'Minuman segar untuk melengkapi hidangan kamu.']
    ];

    foreach ($categories as $cat_key => $cat_info) {
        $query_menu = mysqli_query($koneksi, "SELECT * FROM menu WHERE kategori = '$cat_key'");
        if (mysqli_num_rows($query_menu) > 0) {
    ?>
        <div class="menu-category" id="<?php echo $cat_key; ?>">
            <div class="category-title">
                <h2><?php echo $cat_info['title']; ?></h2>
                <p><?php echo $cat_info['desc']; ?></p>
            </div>

            <div class="products">
                <?php while ($row = mysqli_fetch_assoc($query_menu)) { ?>
                    <div class="product">
                        <div class="product-img">
                            <?php 
                                $foto = $row['foto'];
                                if (file_exists('uploads/' . $foto) && !empty($foto)) {
                                    $img_src = 'uploads/' . $foto;
                                } else {
                                    $img_src = $foto;
                                }
                            ?>
                            <img src="<?php echo $img_src; ?>" alt="<?php echo htmlspecialchars($row['nama_menu']); ?>" onerror="this.src='https://via.placeholder.com/200?text=No+Image'">
                            <span>PO 3 HARI</span>
                        </div>
                        <div class="product-info">
                            <h3><?php echo htmlspecialchars($row['nama_menu']); ?></h3>
                            <p><?php echo htmlspecialchars($row['deskripsi']); ?></p>
                            <div class="product-bottom">
                                <strong>Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></strong>
                                <a href="#cart" aria-label="Tambah ke Keranjang" onclick="checkCartAccess(event)"><i class="fa-solid fa-cart-plus"></i></a>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    <?php 
        }
    } 
    ?>
</section>


<!-- ================= SPECIAL PROMO ================= -->
<section class="promo-section" id="promo">
    <div class="promo-title">
        <h2>Special Promo</h2>
        <p>Dapatkan harga spesial untuk pemesanan dalam jumlah tertentu.</p>
    </div>

    <div class="promo-grid">
        <?php
        $query_promo = mysqli_query($koneksi, "SELECT * FROM promo WHERE status = 'aktif' ORDER BY id_promo DESC");

        if ($query_promo && mysqli_num_rows($query_promo) > 0) {
            while ($promo = mysqli_fetch_assoc($query_promo)) {
                $gambar_promo = !empty($promo['foto']) ? 'uploads/' . $promo['foto'] : 'https://images.unsplash.com/photo-1547592180-85f173990554?auto=format&fit=crop&w=700&q=80';
        ?>
                <div class="promo-card">
                    <img src="<?php echo $gambar_promo; ?>" alt="<?php echo htmlspecialchars($promo['nama_promo'] ?? $promo['kode_promo']); ?>" onerror="this.src='https://images.unsplash.com/photo-1547592180-85f173990554?auto=format&fit=crop&w=700&q=80'">
                    <div class="promo-content">
                        <span class="promo-label">KODE: <?php echo htmlspecialchars($promo['kode_promo']); ?></span>
                        <h3>Diskon <?php echo htmlspecialchars($promo['diskon']); ?>%</h3>
                        <h4><?php echo htmlspecialchars($promo['nama_promo'] ?? 'Promo Spesial'); ?></h4>
                        <p><?php echo htmlspecialchars($promo['deskripsi'] ?? 'Gunakan kode promo saat checkout!'); ?></p>
                    </div>
                </div>
        <?php 
            }
        } else {
        ?>
            <p style="text-align: center; width: 100%; color: #64748b;">Belum ada promo yang tersedia saat ini.</p>
        <?php } ?>
    </div>
</section>


<!-- ================= SEKSI CARA PEMESANAN ================= -->
<section class="cara-pemesanan-section" id="cara-order">
  <div class="container-pemesanan">
    <h2 class="title-pemesanan">Cara order</h2>
    <p class="subtitle-pemesanan">3 langkah mudah memesan di Mamah Catering</p>

    <div class="grid-langkah">
      <div class="card-langkah">
        <div class="badge-angka">1</div>
        <h3>Pilih Menu</h3>
        <p>Cari dan pilih paket makanan favoritmu pada daftar Kategori Menu di bawah ini.</p>
      </div>

      <div class="card-langkah">
        <div class="badge-angka">2</div>
        <h3>Isi Detail & Bayar</h3>
        <p>Lengkapi data pemesan dan selesaikan pembayaran via GoPay atau Transfer.</p>
      </div>

      <div class="card-langkah">
        <div class="badge-angka">3</div>
        <h3>Konfirmasi & Kirim</h3>
        <p>Admin akan memverifikasi pesananmu, lalu pesanan siap diantar ke lokasimu!</p>
      </div>
    </div>
  </div>
</section>


<!-- ================= TENTANG ================= -->
<section class="about" id="tentang">
    <div>
        <small>TENTANG MAMAH CATERING</small>
        <h2>Pesan Dulu,<br>Nikmati Nanti.</h2>
        <p>
            MAMAH CATERING adalah layanan pemesanan makanan dengan sistem Pre-Order yang memudahkan pelanggan memilih menu dan menentukan tanggal pesanan secara praktis dan terpercaya.
        </p>
    </div>
</section>


<!-- ================= FOOTER ================= -->
<footer>
    <div class="footer-content">
        <div>
            <h2>MAMAH<span>CATERING</span></h2>
            <p>Pesan Dulu, Nikmati Nanti</p>
        </div>

        <div class="footer-menu">
            <a href="#">Beranda</a>
            <a href="#produk">Kategori</a>
            <a href="#cara-order">Cara Order</a>
            <a href="#promo">Promo</a>
        </div>

        <div class="social">
            <i class="fa-brands fa-instagram"></i>
            <i class="fa-brands fa-whatsapp"></i>
            <i class="fa-brands fa-tiktok"></i>
        </div>
    </div>

    <div class="copyright">
        © 2026 MAMAH CATERING. Semua hak dilindungi.
    </div>
</footer>


<!-- ================= POPUP CHECKOUT / KERANJANG ================= -->
<div class="popup" id="cart">
    <div class="popup-box cart-popup-box">
        <a href="#" class="close" onclick="closeCart(); return false;">&times;</a>

        <!-- STEP 1: KERANJANG + FORMULIR -->
        <div id="step-1" class="checkout-step">
            <h2><i class="fa-solid fa-cart-shopping"></i> Keranjang Pesanan</h2>

            <div id="cartItems" class="cart-items"></div>

            <div class="cart-empty" id="cartEmpty">
                <i class="fa-solid fa-basket-shopping"></i>
                <h3>Keranjang masih kosong</h3>
                <p>Pilih menu terlebih dahulu untuk memasukkannya ke keranjang.</p>
            </div>

            <div id="cartCheckoutArea" style="display:none;">
                <div class="price-breakdown cart-total-box">
                    <div class="price-row">
                        <span>Subtotal:</span>
                        <span id="subtotalDisplay">Rp 0</span>
                    </div>
                    <div class="price-row discount-row" id="discountContainer" style="display:none;">
                        <span id="discountLabel">Diskon Promo:</span>
                        <span id="discountDisplay">- Rp 0</span>
                    </div>
                    <div class="price-row total-row">
                        <strong>Total:</strong>
                        <strong id="step1TotalDisplay">Rp 0</strong>
                    </div>
                </div>

                <h3 class="form-title">Data Pemesan</h3>

                <form id="formCheckoutStep1" onsubmit="goToStep2(event)">
                    <div class="form-group">
                        <label>Nama Pemesan *</label>
                        <input type="text" id="nama" value="<?php echo htmlspecialchars($user_nama); ?>" placeholder="Masukkan nama lengkap" required>
                    </div>

                    <div class="form-group">
                      <label>Nomor Telepon *</label>
                      <input type="tel" id="telepon" placeholder="08********" required>
                    </div>

                    <div class="form-group">
                        <label>Alamat Lengkap *</label>
                        <textarea id="alamat" rows="2" placeholder="Nama jalan, nomor rumah, RT/RW" required></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Kecamatan *</label>
                            <input type="text" id="kecamatan" placeholder="Kecamatan" required>
                        </div>
                        <div class="form-group">
                            <label>Kelurahan *</label>
                            <input type="text" id="kelurahan" placeholder="Kelurahan" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Kode Pos *</label>
                            <input type="text" id="kodePos" placeholder="5 angka" pattern="[0-9]{5}" required>
                        </div>
                        <div class="form-group">
                            <label>Tanggal Pesanan *</label>
                            <input type="date" id="tanggalPesanan" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Catatan Pesanan</label>
                        <textarea id="catatan" rows="2" placeholder="Contoh: sambal dipisah, acara jam 12 siang, dll."></textarea>
                    </div>

                    <button type="submit" class="btn-checkout-next">
                        Lanjut ke Pembayaran <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </form>
            </div>
        </div>

        <!-- STEP 2: METODE PEMBAYARAN -->
        <div id="step-2" class="checkout-step" style="display:none;">
            <button type="button" class="btn-back" onclick="goToStep1()">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Keranjang
            </button>

            <h2>Pilih Metode Pembayaran</h2>
            <p class="step-desc">Pilih salah satu metode pembayaran untuk menyelesaikan pesanan kamu.</p>

            <form id="formCheckoutStep2" onsubmit="goToStep3(event)"enctype="multipart/form-data">
                <div class="payment-grid">
                    <!-- Transfer Bank BCA -->
                    <label class="payment-card-grid">
                        <input type="radio" name="paymentMethod" value="Transfer Bank BCA" onchange="toggleQrisView(this)" required>
                        <div class="card-content">
                            <div class="card-header-icon">
                                <i class="fa-solid fa-landmark icon-blue"></i>
                                <i class="fa-solid fa-circle-check check-icon"></i>
                            </div>
                            <strong>Transfer Bank BCA</strong>
                            <small>Transfer melalui rekening bank</small>
                        </div>
                    </label>

                    <!-- QRIS -->
                    <label class="payment-card-grid">
                        <input type="radio" name="paymentMethod" value="QRIS" onchange="toggleQrisView(this)">
                        <div class="card-content">
                            <div class="card-header-icon">
                                <i class="fa-solid fa-qrcode icon-blue"></i>
                                <i class="fa-solid fa-circle-check check-icon"></i>
                            </div>
                            <strong>QRIS</strong>
                            <small>Scan menggunakan aplikasi pembayaran</small>
                        </div>
                    </label>

                    <!-- DANA -->
                    <label class="payment-card-grid">
                        <input type="radio" name="paymentMethod" value="DANA" onchange="toggleQrisView(this)">
                        <div class="card-content">
                            <div class="card-header-icon">
                                <i class="fa-solid fa-wallet icon-blue"></i>
                                <i class="fa-solid fa-circle-check check-icon"></i>
                            </div>
                            <strong>DANA</strong>
                            <small>Pembayaran melalui e-wallet</small>
                        </div>
                    </label>

                    <!-- GoPay -->
                    <label class="payment-card-grid">
                        <input type="radio" name="paymentMethod" value="GoPay" onchange="toggleQrisView(this)">
                        <div class="card-content">
                            <div class="card-header-icon">
                                <i class="fa-solid fa-mobile-screen-button icon-blue"></i>
                                <i class="fa-solid fa-circle-check check-icon"></i>
                            </div>
                            <strong>GoPay</strong>
                            <small>Pembayaran melalui e-wallet</small>
                        </div>
                    </label>
                </div>

                <!-- Area Tampilan Gambar QRIS -->
                <div id="qrisContainer" class="qris-display-box" style="display: none;">
                    <h4>Scan QRIS di bawah ini untuk membayar:</h4>
                    <img src="uploads/qris.jpeg" alt="Gambar QRIS Pembayaran" onerror="this.src='https://via.placeholder.com/250x250?text=Scan+QRIS+Disini'">
                    <p>Mendukung GoPay, OVO, DANA, ShopeePay, & Mobile Banking</p>
                </div>

                <div class="order-summary-box">
                    <div class="summary-row">
                        <span>Total Pembayaran:</span>
                        <strong id="finalPriceDisplay">Rp 0</strong>
                    </div>
                </div>

                <button type="submit" class="btn-checkout-next">
                    Bayar Sekarang <i class="fa-solid fa-arrow-right"></i>
                </button>
            </form>
        </div>

        <!-- STEP 3: KONFIRMASI PESANAN (SUKSES & UPLOAD BUKTI) -->
        <div id="step-3" class="checkout-step" style="display:none;">
            <div class="success-header">
                <div class="success-badge-icon">
                    <i class="fa-solid fa-check"></i>
                </div>
                <h2>Pesanan Berhasil Dibuat!</h2>
                <p class="success-subtext">
                    Terima kasih telah memesan di <strong>MAMAH CATERING</strong>.<br>
                    Silakan upload bukti pembayaran/transfer di bawah ini agar admin dapat segera memverifikasinya.
                </p>

                <div class="order-meta-card">
                    <div class="meta-item">
                        <div class="meta-icon"><i class="fa-solid fa-receipt"></i></div>
                        <div>
                            <small>Nomor Pesanan</small>
                            <strong id="confirmOrderId">MC-2026-0000</strong>
                        </div>
                    </div>
                    <div class="meta-divider"></div>
                    <div class="meta-item">
                        <div class="meta-icon"><i class="fa-solid fa-calendar-days"></i></div>
                        <div>
                            <small>Tanggal Pesan</small>
                            <strong id="confirmTanggalPesan">-</strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="success-layout">
                <!-- Kolom Kiri: Detail Pesanan -->
                <div class="success-col-left">
                    <div class="section-card">
                        <div class="card-title">
                            <i class="fa-solid fa-bag-shopping"></i>
                            <h3>Detail Pesanan</h3>
                        </div>

                        <div class="order-table-wrapper">
                            <table class="success-order-table">
                               <thead>
                                    <tr>
                                        <th>Menu</th>
                                        <th style="text-align:center;">Jumlah</th>
                                        <th style="text-align:right;">Harga Satuan</th>
                                        <th style="text-align:right;">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody id="confirmOrderItems">
                                    <!-- Diisi dinamis via JS -->
                                </tbody>
                            </table>
                        </div>

                        <div class="total-bar-green">
                            <div class="tb-info">
                                <div class="tb-icon"><i class="fa-solid fa-basket-shopping"></i></div>
                                <div>
                                    <strong>Total Pesanan</strong>
                                    <small id="confirmTotalItemsCount">0 menu</small>
                                </div>
                            </div>
                            <div class="tb-price" id="confirmLeftTotal">Rp 0</div>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan: Pembayaran & Upload Bukti -->
                <div class="success-col-right">
                    <div class="section-card">
                        <div class="card-title">
                            <i class="fa-solid fa-credit-card"></i>
                            <h3>Metode Pembayaran</h3>
                        </div>
                        <div class="selected-payment-box" id="confirmPaymentCard"></div>
                    </div>

                    <div class="section-card">
                        <div class="card-title">
                            <i class="fa-solid fa-receipt"></i>
                            <h3>Ringkasan Pembayaran</h3>
                        </div>
                        <div class="summary-breakdown">
                            <div class="sb-row">
                                <span>Subtotal</span>
                                <strong id="confirmSubtotal">Rp 0</strong>
                            </div>
                            <div class="sb-row sb-discount" id="confirmDiscountRow" style="display:none;">
                                <span>Diskon</span>
                                <strong id="confirmDiscount">- Rp 0</strong>
                            </div>
                            <hr>
                            <div class="sb-row sb-total">
                                <span>Total Pembayaran</span>
                                <strong id="confirmFinalTotal">Rp 0</strong>
                            </div>
                        </div>
                    </div>

                    <!-- INPUT UPLOAD BUKTI TRANSFER -->
                    <div class="section-card" style="border: 2px dashed #10b981; background: #f0fdf4;">
                        <div class="form-group file-upload-group" style="margin-bottom:0; text-align: center;">
                            <label style="font-weight:700; font-size:13px; color:#065f46; display: block; margin-bottom: 8px;">
                                <i class="fa-solid fa-camera"></i> Upload Bukti Transfer / Pembayaran *
                            </label>
                            <input type="file" id="buktiBayar" accept="image/*" style="display: none;">
                            <label for="buktiBayar" class="file-upload-btn" style="background: #10b981; color: white; padding: 10px 18px; border-radius: 8px; cursor: pointer; font-size: 13px; font-weight: 600; display: inline-block; box-shadow: 0 4px 10px rgba(16,185,129,0.2);">
                                <i class="fa-solid fa-cloud-arrow-up"></i> Pilih File Gambar Bukti
                            </label>
                            <span id="namaFileBukti" class="file-name" style="display: block; margin-top: 8px; font-size: 12px; color: #047857; font-weight: 600;">Belum ada file dipilih</span>
                        </div>
                    </div>

                    <button type="button" class="btn-checkout-finish" onclick="finishOrder()" style="margin-top:12px;">
                        Selesai & Simpan Pesanan
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- ================= POPUP CEK STATUS PESANAN (USER) ================= -->
<div class="popup" id="status-modal">
    <div class="popup-box">
        <a href="#" class="close" onclick="closeStatusModal(); return false;">&times;</a>

        <div class="status-search-box">
            <h2><i class="fa-solid fa-clock-rotate-left"></i> Cek Status Pesanan</h2>
            <p>Masukkan Nomor HP atau ID Pesanan kamu untuk melihat status terbaru:</p>

            <div class="search-form">
                <input type="text" id="statusPhoneInput" placeholder="Contoh: 08123456789 atau MC-1001">
                <button type="button" onclick="searchOrderStatus()"><i class="fa-solid fa-magnifying-glass"></i> Cari</button>
            </div>

            <div id="statusResultArea" class="status-result-area" style="display:none;"></div>
        </div>
    </div>
</div>


<!-- SCRIPT VALIDASI LOGIN PEMESANAN -->
<script>
    const isUserLoggedIn = <?php echo $is_logged_in ? 'true' : 'false'; ?>;

    function checkCartAccess(event) {
        if (!isUserLoggedIn) {
            if (event) event.preventDefault();
            alert("Silakan login atau daftar akun terlebih dahulu untuk melakukan pemesanan!");
            window.location.href = "login.php?redirect=index.php#cart";
        } else {
            if (typeof openCart === 'function') {
                openCart();
            }
        }
    }
</script>

<script src="script.js"></script>
</body>
</html>
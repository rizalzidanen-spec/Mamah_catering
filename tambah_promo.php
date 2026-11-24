<?php
include 'koneksi.php';

// 1. HANDLE TOGGLE STATUS PROMO (AKTIF / NONAKTIF)
if (isset($_GET['action']) && $_GET['action'] === 'toggle' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Cek status saat ini
    $check_query = mysqli_query($koneksi, "SELECT status FROM promo WHERE id_promo = $id");
    if ($check_query && $data = mysqli_fetch_assoc($check_query)) {
        $status_baru = ($data['status'] === 'Aktif') ? 'Nonaktif' : 'Aktif';
        
        // Update status di database
        mysqli_query($koneksi, "UPDATE promo SET status = '$status_baru' WHERE id_promo = $id");
    }
    
    // Redirect kembali ke halaman promo
    header("Location: tambah_promo.php");
    exit;
}

// 2. HANDLE SIMPAN PROMO BARU
if (isset($_POST['simpan_promo'])) {
    $kode_promo    = mysqli_real_escape_string($koneksi, $_POST['kode_promo']);
    $diskon        = (int) $_POST['diskon'];
    $min_transaksi = (int) $_POST['min_transaksi'];
    $tgl_berakhir  = mysqli_real_escape_string($koneksi, $_POST['tgl_berakhir']);
    $deskripsi     = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $status        = mysqli_real_escape_string($koneksi, $_POST['status']);

    $query = "INSERT INTO promo (kode_promo, diskon, min_transaksi, tgl_berakhir, deskripsi, status) 
              VALUES ('$kode_promo', $diskon, $min_transaksi, '$tgl_berakhir', '$deskripsi', '$status')";
    
    if (mysqli_query($koneksi, $query)) {
        header("Location: tambah_promo.php?pesan=berhasil");
        exit;
    } else {
        $error = "Gagal menambah promo: " . mysqli_error($koneksi);
    }
}

// 3. AMBIL SELURUH DATA PROMO DARI DATABASE
$query_promo = "SELECT * FROM promo ORDER BY id_promo DESC";
$result_promo = false;
try {
    $result_promo = mysqli_query($koneksi, $query_promo);
} catch (Exception $e) {
    $result_promo = false;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Promo - Mamah Catering</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; margin: 0; padding: 0; }
        body { background-color: #f8fafc; color: #1e293b; padding: 30px 20px; }
        
        .container-card { max-width: 1000px; margin: 0 auto 30px auto; background: #ffffff; padding: 28px; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px -2px rgba(0,0,0,0.03); }
        .header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; }
        .header h2 { font-size: 20px; font-weight: 700; color: #0f172a; }
        .header p { color: #64748b; font-size: 13px; margin-top: 2px; }
        
        .btn-back { color: #64748b; text-decoration: none; font-size: 14px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; border-radius: 8px; background: #f1f5f9; transition: 0.2s; }
        .btn-back:hover { color: #0f172a; background: #e2e8f0; }
        
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .form-group { margin-bottom: 16px; }
        .form-group.full { grid-column: span 2; }
        .form-group label { display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none; background: #f8fafc; transition: 0.2s; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-color: #10b981; background: #fff; }
        
        .btn-submit { background: #10b981; color: white; border: none; padding: 12px 20px; border-radius: 8px; font-weight: 700; font-size: 14px; cursor: pointer; transition: 0.2s; display: inline-flex; align-items: center; gap: 8px; }
        .btn-submit:hover { background: #059669; }
        
        .alert-success { background: #f0fdf4; color: #166534; padding: 12px; border-radius: 8px; font-size: 13px; margin-bottom: 16px; border: 1px solid #bbf7d0; }
        .alert-error { background: #fef2f2; color: #dc2626; padding: 12px; border-radius: 8px; font-size: 13px; margin-bottom: 16px; border: 1px solid #fecaca; }

        .table-responsive { overflow-x: auto; }
        table { width: 100%; border-collapse: separate; border-spacing: 0; font-size: 13px; text-align: left; }
        th { background: #f1f5f9; color: #475569; font-weight: 700; padding: 12px 16px; border-bottom: 1px solid #e2e8f0; }
        th:first-child { border-top-left-radius: 8px; border-bottom-left-radius: 8px; }
        th:last-child { border-top-right-radius: 8px; border-bottom-right-radius: 8px; }
        td { padding: 12px 16px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; color: #334155; }
        tr:hover td { background: #f8fafc; }
        
        .promo-tag { font-family: monospace; font-size: 12px; font-weight: 700; background: #ecfdf5; color: #047857; padding: 4px 10px; border-radius: 6px; border: 1px dashed #059669; display: inline-block; }
        
        /* Style Tombol Status Toggle */
        .btn-status { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; text-decoration: none; transition: all 0.2s; }
        .status-aktif { background: #dcfce7; color: #15803d; border: 1px solid #86efac; }
        .status-aktif:hover { background: #bbf7d0; }
        .status-nonaktif { background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }
        .status-nonaktif:hover { background: #fecaca; }
    </style>
</head>
<body>

<!-- FORM INPUT PROMO BARU -->
<div class="container-card">
    <div class="header">
        <div>
            <h2><i class="fa-solid fa-percent" style="color:#10b981;"></i> Kelola Promo Diskon</h2>
            <p>Buat program promo diskon baru berdasarkan jumlah pesanan box Mamah Catering</p>
        </div>
        <a href="admin.php" class="btn-back"><i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard</a>
    </div>

    <?php if (isset($_GET['pesan']) && $_GET['pesan'] === 'berhasil'): ?>
        <div class="alert-success"><i class="fa-solid fa-check-circle"></i> Promo baru berhasil disimpan!</div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <div class="alert-error"><?= $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="tambah_promo.php">
        <div class="form-grid">
            <div class="form-group">
                <label>Nama Event Promo</label>
                <input type="text" name="kode_promo" placeholder="Contoh: PROMO HEMAT" required>
            </div>

            <div class="form-group">
                <label>Diskon (%)</label>
                <input type="number" name="diskon" min="1" max="100" placeholder="Contoh: 10" required>
            </div>

            <div class="form-group">
                <label>Minimal Pesanan (Box)</label>
                <input type="number" name="min_transaksi" min="1" placeholder="Contoh: 100" required>
            </div>

            <div class="form-group">
                <label>Berlaku Sampai Tanggal</label>
                <input type="date" name="tgl_berakhir" required>
            </div>

            <div class="form-group">
                <label>Status Awal</label>
                <select name="status">
                    <option value="Aktif">Aktif</option>
                    <option value="Nonaktif">Nonaktif</option>
                </select>
            </div>

            <div class="form-group full">
                <label>Deskripsi / Syarat & Ketentuan Promo</label>
                <textarea name="deskripsi" rows="3" placeholder="Contoh: Dapatkan potongan harga 10% untuk pemesanan minimal 100 box."></textarea>
            </div>
        </div>

        <button type="submit" name="simpan_promo" class="btn-submit">
            <i class="fa-solid fa-plus"></i> Simpan Promo
        </button>
    </form>
</div>

<!-- TABEL DAFTAR PROMO -->
<div class="container-card">
    <div class="header">
        <div>
            <h2>Daftar Promo</h2>
            <p>Klik tombol status untuk Mengaktifkan / Menonaktifkan promo secara langsung</p>
        </div>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Nama Event Promo</th>
                    <th>Diskon</th>
                    <th>Minimal Pesanan</th>
                    <th>Berlaku Sampai</th>
                    <th>Deskripsi</th>
                    <th>Status (Klik u/ Ubah)</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result_promo && mysqli_num_rows($result_promo) > 0): ?>
                    <!-- DATA DARI DATABASE MYSQL -->
                    <?php while ($p = mysqli_fetch_assoc($result_promo)): ?>
                        <?php 
                            $status_skrg = !empty($p['status']) ? $p['status'] : 'Aktif';
                        ?>
                        <tr>
                            <td><span class="promo-tag"><?= htmlspecialchars($p['kode_promo']); ?></span></td>
                            <td><b><?= htmlspecialchars($p['diskon']); ?>%</b></td>
                            <td><?= htmlspecialchars($p['min_transaksi']); ?> Box</td>
                            <td><?= date('d M Y', strtotime($p['tgl_berakhir'])); ?></td>
                            <td><?= htmlspecialchars($p['deskripsi']); ?></td>
                            <td>
                                <a href="tambah_promo.php?action=toggle&id=<?= $p['id_promo']; ?>" 
                                   class="btn-status <?= ($status_skrg === 'Aktif') ? 'status-aktif' : 'status-nonaktif'; ?>"
                                   title="Klik untuk mengubah status">
                                   <i class="fa-solid <?= ($status_skrg === 'Aktif') ? 'fa-toggle-on' : 'fa-toggle-off'; ?>"></i>
                                   <?= $status_skrg; ?>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; color: #94a3b8; padding: 20px;">Belum ada data promo di database. Silahkan jalankan query INSERT di phpMyAdmin atau tambah promo baru lewat form di atas.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
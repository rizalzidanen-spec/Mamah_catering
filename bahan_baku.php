<?php
session_start();
if (!isset($_SESSION['admin_login']) || $_SESSION['admin_login'] !== true) {
    header("Location: login_admin.php");
    exit;
}
include 'koneksi.php';

// 1. Tambah bahan baru
if (isset($_POST['tambah'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_bahan']);
    $stok = (float) $_POST['stok'];
    $satuan = mysqli_real_escape_string($koneksi, $_POST['satuan']);
    $harga = (float) $_POST['harga_perkiraan'];
    mysqli_query($koneksi, "INSERT INTO bahan_baku (nama_bahan, stok, satuan, harga_satuan) VALUES ('$nama', $stok, '$satuan', $harga)");
    header("Location: bahan_baku.php");
    exit;
}

// 2. Update stok cepat
if (isset($_POST['update_stok'])) {
    $id = (int) $_POST['id_bahan'];
    $stok_baru = (float) $_POST['stok_baru'];
    mysqli_query($koneksi, "UPDATE bahan_baku SET stok = $stok_baru WHERE id_bahan = $id");
    header("Location: bahan_baku.php");
    exit;
}

// 3. Catat pengeluaran belanja harian
if (isset($_POST['catat_belanja'])) {
    $tgl = date('Y-m-d');
    $keperluan = "Belanja Pasar: " . mysqli_real_escape_string($koneksi, $_POST['keperluan']);
    $biaya = (float) $_POST['total_biaya'];
    mysqli_query($koneksi, "INSERT INTO catat_belanja (tanggal, keperluan, total_biaya) VALUES ('$tgl', '$keperluan', $biaya)");
    header("Location: bahan_baku.php");
    exit;
}

// 4. Reset Semua Stok Jadi 0
if (isset($_POST['kosongkan_stok'])) {
    mysqli_query($koneksi, "UPDATE bahan_baku SET stok = 0");
    header("Location: bahan_baku.php");
    exit;
}

// Hapus bahan
if (isset($_GET['hapus'])) {
    $id = (int) $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM bahan_baku WHERE id_bahan = $id");
    header("Location: bahan_baku.php");
    exit;
}

$bahan = mysqli_query($koneksi, "SELECT * FROM bahan_baku ORDER BY nama_bahan ASC");

// Ambil riwayat belanja pasar manual (tidak mengandung kata 'Modal Otomatis')
$riwayat_belanja = mysqli_query($koneksi, "SELECT * FROM catat_belanja WHERE keperluan NOT LIKE '%Modal Otomatis%' ORDER BY id_belanja DESC LIMIT 10");

// Ambil riwayat pemotongan bahan dari pesanan (mengandung kata 'Modal Otomatis')
$riwayat_pemakaian = mysqli_query($koneksi, "SELECT * FROM catat_belanja WHERE keperluan LIKE '%Modal Otomatis%' ORDER BY id_belanja DESC LIMIT 10");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Stok & Belanja - Mamah Catering</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; font-family: sans-serif; margin: 0; padding: 0; }
        body { background: #f8fafc; color: #1e293b; padding: 25px 30px; }
        
        /* Lebar container diperlebar jadi 1400px agar sejajar dengan dashboard */
        .box { max-width: 1400px; margin: 0 auto; background: #fff; padding: 32px; border-radius: 16px; border: 1px solid #cbd5e1; box-shadow: 0 4px 25px -4px rgba(0,0,0,0.05); }
        
        h2 { margin-bottom: 24px; color: #0f172a; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f5f9; padding-bottom: 16px; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 30px; }
        .card { background: #f8fafc; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; }
        .card h3 { margin-bottom: 12px; font-size: 15px; color: #334155; }
        input, select { width: 100%; padding: 10px 14px; margin-bottom: 10px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; }
        button { padding: 10px 16px; background: #3b82f6; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; }
        button:hover { background: #2563eb; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 13px; }
        th, td { padding: 12px 16px; border-bottom: 1px solid #e2e8f0; text-align: left; }
        th { background: #f1f5f9; color: #475569; font-weight: 700; }
        .btn-kembali { background: #64748b; color: #fff; text-decoration: none; padding: 8px 14px; font-size: 13px; border-radius: 8px; font-weight: 600; }
        
        .section-title { margin-top: 35px; margin-bottom: 10px; font-size: 18px; color: #0f172a; display: flex; align-items: center; gap: 8px; }
    </style>
</head>
<body>

<div class="box">
    <h2>
        <span><i class="fa-solid fa-boxes-stacked"></i> Kelola Stok & Riwayat Belanja Bahan Baku</span>
        <a href="admin.php" class="btn-kembali"><i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard</a>
    </h2>

    <div class="grid">
        <!-- Form Tambah Master Bahan -->
        <div class="card">
            <h3><i class="fa-solid fa-plus"></i> Tambah Master Bahan Baru</h3>
            <form method="POST">
                <input type="text" name="nama_bahan" placeholder="Nama Bahan (Contoh: Ayam, Beras)" required>
                <div style="display:flex; gap:8px;">
                    <input type="number" step="0.01" name="stok" value="0" placeholder="Stok Awal" required style="flex:2;">
                    <input type="text" name="satuan" placeholder="Satuan (kg/pcs/liter)" required style="flex:1;">
                </div>
                <input type="number" step="100" name="harga_perkiraan" placeholder="Harga Satuan (Rp)" required>
                <button type="submit" name="tambah">Simpan Master Bahan</button>
            </form>
        </div>

        <!-- Form Catat Belanja -->
        <div class="card">
            <h3><i class="fa-solid fa-cart-shopping"></i> Catat Pengeluaran Belanja Pasar</h3>
            <form method="POST">
                <input type="text" name="keperluan" placeholder="Keterangan (Contoh: Beli Ayam & Beras di Pasar Pagi)" required>
                <input type="number" step="100" name="total_biaya" placeholder="Total Uang Keluar (Rp)" required>
                <button type="submit" name="catat_belanja" style="background: #10b981;">Catat Pengeluaran Belanja</button>
            </form>
        </div>
    </div>

    <!-- Tabel Daftar Stok -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
        <h3 class="section-title" style="margin-top:0;"><i class="fa-solid fa-warehouse"></i> Stok Bahan Baku di Gudang</h3>
        <form method="POST" onsubmit="return confirm('Reset semua sisa stok menjadi 0?')">
            <button type="submit" name="kosongkan_stok" style="background: #ef4444; padding: 6px 12px; font-size: 12px;">
                <i class="fa-solid fa-rotate-left"></i> Reset Semua Stok ke 0
            </button>
        </form>
    </div>

    <table>
        <tr>
            <th>Nama Bahan</th>
            <th>Sisa Stok Gudang</th>
            <th>Harga Satuan</th>
            <th>Aksi</th>
        </tr>
        <?php while($b = mysqli_fetch_assoc($bahan)): ?>
        <tr>
            <td><b><?= htmlspecialchars($b['nama_bahan']); ?></b></td>
            <td>
                <form method="POST" style="display:flex; gap:8px; align-items:center; margin:0;">
                    <input type="hidden" name="id_bahan" value="<?= $b['id_bahan']; ?>">
                    <input type="number" step="0.01" name="stok_baru" value="<?= $b['stok']; ?>" style="width:100px; margin-bottom:0;">
                    <span><?= htmlspecialchars($b['satuan']); ?></span>
                    <button type="submit" name="update_stok" style="padding:6px 12px; font-size:12px; background:#10b981;">Update</button>
                </form>
            </td>
            <td>Rp <?= number_format($b['harga_satuan'], 0, ',', '.'); ?></td>
            <td>
                <a href="bahan_baku.php?hapus=<?= $b['id_bahan']; ?>" onclick="return confirm('Hapus bahan ini?')" style="color:#ef4444; font-weight:600;"><i class="fa-solid fa-trash"></i> Hapus</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <!-- Tabel 1: Riwayat Belanja Pasar Manual -->
    <h3 class="section-title"><i class="fa-solid fa-receipt"></i> Riwayat Belanja Pasar (Manual)</h3>
    <table>
        <tr>
            <th>Tanggal</th>
            <th>Keterangan Belanja</th>
            <th>Total Biaya Keluar</th>
        </tr>
        <?php if(mysqli_num_rows($riwayat_belanja) > 0): ?>
            <?php while($r1 = mysqli_fetch_assoc($riwayat_belanja)): ?>
            <tr>
                <td><?= date('d M Y', strtotime($r1['tanggal'])); ?></td>
                <td><?= htmlspecialchars($r1['keperluan']); ?></td>
                <td style="color:#ef4444; font-weight:bold;">Rp <?= number_format($r1['total_biaya'], 0, ',', '.'); ?></td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="3" style="text-align:center; color:#94a3b8; padding:20px;">Belum ada catatan belanja pasar.</td></tr>
        <?php endif; ?>
    </table>

    <!-- Tabel 2: Riwayat Pemakaian Bahan dari Pesanan (Otomatis) -->
    <h3 class="section-title"><i class="fa-solid fa-clipboard-list"></i> Riwayat Pemotongan Bahan dari Pesanan (Otomatis)</h3>
    <table>
        <tr>
            <th>Tanggal Diproses</th>
            <th>Keterangan Pesanan Masuk</th>
            <th>Estimasi Nilai Bahan Terpakai</th>
        </tr>
        <?php if(mysqli_num_rows($riwayat_pemakaian) > 0): ?>
            <?php while($r2 = mysqli_fetch_assoc($riwayat_pemakaian)): ?>
            <tr>
                <td><?= date('d M Y', strtotime($r2['tanggal'])); ?></td>
                <td><?= htmlspecialchars($r2['keperluan']); ?></td>
                <td style="color:#2563eb; font-weight:bold;">Rp <?= number_format($r2['total_biaya'], 0, ',', '.'); ?></td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="3" style="text-align:center; color:#94a3b8; padding:20px;">Belum ada riwayat pemotongan pesanan otomatis.</td></tr>
        <?php endif; ?>
    </table>
</div>

</body>
</html>
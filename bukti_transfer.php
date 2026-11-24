<?php
session_start();
// Pastikan koneksi database terhubung
include 'koneksi.php';

// Query untuk mengambil seluruh data pesanan, diurutkan dari pesanan terbaru
$query = "SELECT * FROM pesanan ORDER BY id_pesanan DESC";
$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Bukti Transfer - Mamah Catering</title>
    <!-- FONT AWESOME & GOOGLE FONTS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 0; }
        body { background-color: #f8fafc; color: #1e293b; padding: 30px; }
        
        .container { 
            max-width: 1200px; 
            margin: 0 auto; 
            background: #ffffff; 
            padding: 30px; 
            border-radius: 16px; 
            border: 1px solid #e2e8f0; 
            box-shadow: 0 4px 20px -2px rgba(0,0,0,0.05); 
        }
        
        .header { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-bottom: 24px; 
            border-bottom: 1px solid #f1f5f9; 
            padding-bottom: 18px; 
        }
        
        .header-title h1 { 
            font-size: 22px; 
            font-weight: 800; 
            color: #0f172a; 
            margin-bottom: 4px; 
        }
        
        .header-title p { 
            color: #64748b; 
            font-size: 13px; 
        }
        
        /* TOMBOL KEMBALI KE DASHBOARD ADMIN */
        .btn-back { 
            background: #475569; 
            color: white; 
            padding: 10px 18px; 
            border-radius: 8px; 
            text-decoration: none; 
            font-size: 13px; 
            font-weight: 600; 
            display: inline-flex; 
            align-items: center; 
            gap: 8px; 
            transition: all 0.2s ease;
        }
        .btn-back:hover { 
            background: #334155; 
            transform: translateY(-1px);
        }
        
        .table-responsive { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; text-align: left; }
        th { background: #f8fafc; color: #475569; font-weight: 700; padding: 14px 16px; border-bottom: 2px solid #e2e8f0; }
        td { padding: 14px 16px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; color: #334155; }
        tr:hover td { background: #f8fafc; }
        
        /* STYLE GAMBAR LANGSUNG MUNCUL */
        .bukti-img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .bukti-img:hover {
            transform: scale(1.1);
        }

        .badge-none {
            color: #94a3b8; 
            font-size: 12px; 
            font-style: italic;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <div class="header-title">
            <h1>Verifikasi Bukti Transfer & Pembayaran</h1>
            <p>Daftar bukti pembayaran langsung dari pemesan</p>
        </div>
        <!-- Link Kembali Ke Dashboard Utama Admin (admin.php) -->
        <a href="admin.php" class="btn-back">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard
        </a>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>ID Pesanan</th>
                    <th>Nama Pelanggan</th>
                    <th>Metode Pembayaran</th>
                    <th>Total Harga</th>
                    <th>Tanggal Pesan</th>
                    <th>Bukti Transfer</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <?php 
                            $formatted_id = 'MC-' . sprintf("%03d", $row['id_pesanan']);
                            $metode = !empty($row['metode_pembayaran']) ? $row['metode_pembayaran'] : 'Transfer Bank';
                            $bukti = !empty($row['bukti_pembayaran']) ? trim($row['bukti_pembayaran']) : null;
                        ?>
                        <tr>
                            <td><b><?= $formatted_id; ?></b></td>
                            <td>
                                <b><?= htmlspecialchars($row['nama_pemesan']); ?></b><br>
                                <small style="color:#64748b; font-size:11px;"><?= htmlspecialchars($row['nomor_telepon']); ?></small>
                            </td>
                            <td>
                                <span style="color: #0284c7; font-weight: 600;"><?= htmlspecialchars($metode); ?></span>
                            </td>
                            <td><b>Rp <?= number_format($row['total_harga'], 0, ',', '.'); ?></b></td>
                            <td><?= date('d M Y, H:i', strtotime($row['tanggal_pesan'])); ?> WIB</td>
                            <td>
                                <?php if (!empty($bukti) && file_exists('uploads/bukti_transfer/' . $bukti)): ?>
                                    <!-- Langsung Munculkan Gambar Kecil, Bisa Diklik Buat Zoom -->
                                    <img src="uploads/bukti_transfer/<?= htmlspecialchars($bukti); ?>" alt="Bukti Transfer" class="bukti-img" onclick="openModal('uploads/bukti_transfer/<?= htmlspecialchars($bukti); ?>', '<?= $formatted_id; ?>')">
                                <?php else: ?>
                                    <span class="badge-none">Belum ada file</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align:center; color:#94a3b8; padding:30px;">Belum ada data pesanan masuk.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL ZOOM GAMBAR JIKA DIKLIK -->
<div id="modalPreview" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(15,23,42,0.75); backdrop-filter:blur(3px); z-index:9999; justify-content:center; align-items:center;">
    <div style="background:#fff; padding:20px; border-radius:14px; max-width:500px; width:90%; text-align:center; box-shadow:0 20px 25px -5px rgba(0,0,0,0.3);">
        <h3 id="modalTitle" style="margin-bottom:12px; font-size:16px; color:#0f172a; font-weight:700;">Zoom Bukti Transfer</h3>
        <div style="max-height: 450px; overflow-y: auto; margin-bottom: 16px; border:1px solid #e2e8f0; border-radius:8px; padding:4px; background:#f8fafc;">
            <img id="previewImg" src="" alt="Zoom Bukti" style="max-width:100%; height:auto; border-radius:6px; display:block; margin:0 auto;">
        </div>
        <button type="button" style="background:#475569; color:white; border:none; width:100%; padding:10px; border-radius:6px; font-weight:600; cursor:pointer;" onclick="closeModal()">Tutup</button>
    </div>
</div>

<script>
function openModal(url, id) {
    document.getElementById('modalTitle').innerText = 'Bukti Pembayaran - ' + id;
    document.getElementById('previewImg').src = url;
    document.getElementById('modalPreview').style.display = 'flex';
}

function closeModal() {
    document.getElementById('modalPreview').style.display = 'none';
}

window.onclick = function(event) {
    const modal = document.getElementById('modalPreview');
    if (event.target === modal) {
        closeModal();
    }
}
</script>

</body>
</html>
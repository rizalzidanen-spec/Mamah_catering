<?php
session_start();

// Cek apakah admin sudah login
if (!isset($_SESSION['admin_login']) || $_SESSION['admin_login'] !== true) {
    header("Location: login_admin.php");
    exit;
}

include 'koneksi.php';

// Handle Update Status Pesanan & Potong Stok Multi-Menu Berdasarkan Qty Per Item
if (isset($_POST['update_status'])) {
    $id_pesanan = (int) $_POST['id_pesanan'];
    $status_baru = mysqli_real_escape_string($koneksi, $_POST['status']);

    // Ambil data pesanan terlebih dahulu sebelum status diubah
    $q_pesanan = mysqli_query($koneksi, "SELECT * FROM pesanan WHERE id_pesanan = $id_pesanan");
    $pesanan = mysqli_fetch_assoc($q_pesanan);

    // 1. Update status pesanan di database
    mysqli_query($koneksi, "UPDATE pesanan SET status = '$status_baru' WHERE id_pesanan = $id_pesanan");

    // 2. Jika status diubah menjadi 'Diproses', potong stok & hitung modal multi-menu
    if ($status_baru === 'Diproses' && $pesanan) {
        $detail_pesanan_str = $pesanan['detail_pesanan'] ?? '';
        $total_modal_bahan = 0;

        // Pecah string menu berdasarkan koma (contoh: "Nasi Ayam (50x), Martabak (50x)")
        $items = explode(',', $detail_pesanan_str);

        foreach ($items as $item) {
            $item_lower = strtolower(trim($item));

            // Ambil angka porsi (qty) dari format "(50x)" di setiap item menu
            preg_match('/\((\d+)\s*x\)/i', $item, $match_qty);
            $qty = isset($match_qty[1]) ? (int)$match_qty[1] : 1;
            if ($qty <= 0) $qty = 1;

            // --- A. KATEGORI NASI AYAM / AYAM GEPREK / NASI KUNING / AYAM BAKAR ---
            if (strpos($item_lower, 'ayam') !== false || strpos($item_lower, 'nasi') !== false) {
                $rasio_ayam = 0.10 * $qty;
                $rasio_beras = 0.10 * $qty;
                $rasio_lalapan = 0.02 * $qty;
                $rasio_cabe = 0.02 * $qty;
                $rasio_box = 1 * $qty;

                $modal_sub = ($rasio_ayam * 40000) + ($rasio_beras * 14000) + ($rasio_lalapan * 15000) + ($rasio_cabe * 60000) + ($rasio_box * 1500);
                $total_modal_bahan += $modal_sub;

                // Eksekusi potong stok gudang
                mysqli_query($koneksi, "UPDATE bahan_baku SET stok = GREATEST(0, stok - $rasio_ayam) WHERE nama_bahan LIKE '%Ayam%'");
                mysqli_query($koneksi, "UPDATE bahan_baku SET stok = GREATEST(0, stok - $rasio_beras) WHERE nama_bahan LIKE '%Beras%'");
                mysqli_query($koneksi, "UPDATE bahan_baku SET stok = GREATEST(0, stok - $rasio_lalapan) WHERE nama_bahan LIKE '%lalapan%'");
                mysqli_query($koneksi, "UPDATE bahan_baku SET stok = GREATEST(0, stok - $rasio_cabe) WHERE nama_bahan LIKE '%cabe%'");
                mysqli_query($koneksi, "UPDATE bahan_baku SET stok = GREATEST(0, stok - $rasio_box) WHERE nama_bahan LIKE '%Box%'");

                if (strpos($item_lower, 'goreng') !== false || strpos($item_lower, 'kuning') !== false || strpos($item_lower, 'bakar') !== false) {
                    $rasio_minyak = 0.02 * $qty;
                    $total_modal_bahan += ($rasio_minyak * 16000);
                    mysqli_query($koneksi, "UPDATE bahan_baku SET stok = GREATEST(0, stok - $rasio_minyak) WHERE nama_bahan LIKE '%Minyak%'");
                }
            } 
            // --- B. KATEGORI MARTABAK TELUR ---
            elseif (strpos($item_lower, 'martabak') !== false) {
                $rasio_lumpia = 0.02 * $qty;
                $rasio_telur = 0.02 * $qty;
                
                $modal_sub = ($rasio_lumpia * 17000) + ($rasio_telur * 28000);
                $total_modal_bahan += $modal_sub;

                mysqli_query($koneksi, "UPDATE bahan_baku SET stok = GREATEST(0, stok - $rasio_lumpia) WHERE nama_bahan LIKE '%lumpia%'");
                mysqli_query($koneksi, "UPDATE bahan_baku SET stok = GREATEST(0, stok - $rasio_telur) WHERE nama_bahan LIKE '%Telur%'");
            }
            // --- C. KATEGORI PEYEK ---
            elseif (strpos($item_lower, 'peyek') !== false) {
                $rasio_tepung_beras = 0.01 * $qty;
                $rasio_kacang = 0.01 * $qty;

                $modal_sub = ($rasio_tepung_beras * 14000) + ($rasio_kacang * 17000);
                $total_modal_bahan += $modal_sub;

                mysqli_query($koneksi, "UPDATE bahan_baku SET stok = GREATEST(0, stok - $rasio_tepung_beras) WHERE nama_bahan LIKE '%tepung beras%'");
                mysqli_query($koneksi, "UPDATE bahan_baku SET stok = GREATEST(0, stok - $rasio_kacang) WHERE nama_bahan LIKE '%kacang tanah%'");
            }
            // --- D. KATEGORI PASTEL / SOSIS SOLO ---
            elseif (strpos($item_lower, 'pastel') !== false || strpos($item_lower, 'sosis') !== false) {
                $rasio_terigu = 0.01 * $qty;
                $rasio_wortel = 0.01 * $qty;
                $rasio_kentang = 0.01 * $qty;

                $modal_sub = ($rasio_terigu * 15000) + ($rasio_wortel * 10000) + ($rasio_kentang * 10000);
                $total_modal_bahan += $modal_sub;

                mysqli_query($koneksi, "UPDATE bahan_baku SET stok = GREATEST(0, stok - $rasio_terigu) WHERE nama_bahan LIKE '%tepung terigu%'");
                mysqli_query($koneksi, "UPDATE bahan_baku SET stok = GREATEST(0, stok - $rasio_wortel) WHERE nama_bahan LIKE '%wortel%'");
                mysqli_query($koneksi, "UPDATE bahan_baku SET stok = GREATEST(0, stok - $rasio_kentang) WHERE nama_bahan LIKE '%kentang%'");
            }
        }

        // Catat ke riwayat pemotongan bahan otomatis
        if ($total_modal_bahan > 0) {
            $tgl = date('Y-m-d');
            $ket_pemakaian = "Modal Otomatis Pesanan ID: MC-" . (1000 + $id_pesanan) . " (Multi-Menu)";
            mysqli_query($koneksi, "INSERT INTO catat_belanja (tanggal, keperluan, total_biaya) VALUES ('$tgl', '$ket_pemakaian', $total_modal_bahan)");
        }
    }

    header("Location: admin.php");
    exit;
}

// Data Statistik Pesanan
$q_omset = mysqli_query($koneksi, "SELECT SUM(total_harga) AS total FROM pesanan WHERE status = 'Selesai'");
$omset = mysqli_fetch_assoc($q_omset)['total'] ?? 0;

$q_selesai_count = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM pesanan WHERE status = 'Selesai'");
$cnt_selesai = mysqli_fetch_assoc($q_selesai_count)['total'] ?? 0;

$q_baru = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM pesanan WHERE status = 'Pending' OR status = 'Menunggu'");
$cnt_baru = mysqli_fetch_assoc($q_baru)['total'] ?? 0;

$q_proses = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM pesanan WHERE status IN ('Diproses', 'Proses', 'Sedang Dibuat', 'Siap Dikirim', 'Dalam Perjalanan')");
$cnt_proses = mysqli_fetch_assoc($q_proses)['total'] ?? 0;

// Ambil Data Pesanan
$query_pesanan = "SELECT * FROM pesanan ORDER BY id_pesanan DESC";
$result_pesanan = mysqli_query($koneksi, $query_pesanan);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin Mamah Catering - ERP</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; margin: 0; padding: 0; }
        
        body { background-color: #f8fafc; color: #1e293b; padding: 25px 30px; }
        .dashboard-card { max-width: 1400px; margin: 0 auto; background: #ffffff; padding: 32px; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 25px -4px rgba(0,0,0,0.05); }

        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; border-bottom: 1px solid #f1f5f9; padding-bottom: 16px; }
        .header-title h1 { font-size: 26px; font-weight: 700; color: #0f172a; margin-bottom: 4px; }
        .header-title p { color: #64748b; font-size: 14px; }
        
        .admin-profile { display: flex; align-items: center; gap: 12px; }
        .admin-profile span { font-weight: 600; font-size: 14px; color: #334155; }
        .btn-logout-top { background: #fee2e2; color: #dc2626; padding: 8px 14px; border-radius: 8px; text-decoration: none; font-size: 13px; font-weight: 600; transition: 0.2s; }
        .btn-logout-top:hover { background: #fecaca; }

        .stat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 28px; }
        .stat-card { background: #f8fafc; border: 1px solid #e2e8f0; padding: 20px; border-radius: 12px; }
        .stat-label { font-size: 13px; color: #64748b; font-weight: 500; margin-bottom: 6px; }
        .stat-value { font-size: 24px; font-weight: 800; color: #0f172a; margin-bottom: 4px; }
        .stat-sub { font-size: 12px; font-weight: 600; }
        .sub-green { color: #16a34a; }
        .sub-orange { color: #ea580c; }
        .sub-blue { color: #2563eb; }

        .toolbar { display: flex; justify-content: space-between; flex-wrap: wrap; gap: 16px; align-items: center; margin-bottom: 24px; background: #f8fafc; padding: 16px; border-radius: 12px; border: 1px solid #e2e8f0; }
        .search-box { flex: 1; min-width: 280px; position: relative; }
        .search-box input { width: 100%; padding: 10px 16px; border-radius: 20px; border: 1px solid #cbd5e1; background: #fff; font-size: 14px; outline: none; transition: 0.2s; }
        .search-box input:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }

        .action-buttons { display: flex; gap: 10px; flex-wrap: wrap; }
        .btn-action-primary { background: #3b82f6; color: white; border: none; padding: 9px 16px; border-radius: 20px; font-size: 13px; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap; transition: 0.2s; }
        .btn-action-primary:hover { background: #2563eb; }

        .btn-action-inventory { background: #10b981; color: white; border: none; padding: 9px 16px; border-radius: 20px; font-size: 13px; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap; transition: 0.2s; }
        .btn-action-inventory:hover { background: #059669; }

        .filter-pills { display: flex; gap: 8px; flex-wrap: wrap; width: 100%; margin-top: 4px; padding-top: 12px; border-top: 1px solid #e2e8f0; }
        .pill { padding: 7px 14px; border-radius: 20px; font-size: 13px; font-weight: 600; border: none; background: #e2e8f0; color: #475569; cursor: pointer; transition: 0.2s; }
        .pill.active, .pill:hover { background: #3b82f6; color: white; }

        .table-responsive { overflow-x: auto; }
        table { width: 100%; border-collapse: separate; border-spacing: 0; font-size: 13px; text-align: left; }
        th { background: #f1f5f9; color: #475569; font-weight: 700; padding: 14px 16px; border-bottom: 2px solid #e2e8f0; }
        th:first-child { border-top-left-radius: 8px; border-bottom-left-radius: 8px; }
        th:last-child { border-top-right-radius: 8px; border-bottom-right-radius: 8px; }
        td { padding: 14px 16px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; color: #334155; }
        tr:hover td { background: #f8fafc; }

        .id-cell { font-weight: 700; color: #2563eb; }
        .date-cell { font-size: 11px; color: #94a3b8; font-weight: 500; display: block; margin-top: 2px; }
        .customer-name { font-weight: 700; color: #0f172a; display: block; }
        .customer-phone { font-size: 12px; color: #64748b; }
        .price-cell { font-weight: 800; color: #0f172a; }
        .select-status { padding: 6px 12px; border-radius: 8px; border: 1px solid #cbd5e1; background: #fff; font-weight: 600; font-size: 13px; outline: none; cursor: pointer; }
    </style>
</head>
<body>

<div class="dashboard-card">
    <div class="header">
        <div class="header-title">
            <h1>Dashboard Admin Mamah Catering</h1>
            <p>Manajemen Pesanan & Operasional</p>
        </div>
        <div class="admin-profile">
            <span><i class="fa-solid fa-user-shield"></i> <?= htmlspecialchars($_SESSION['admin_nama'] ?? 'Admin'); ?></span>
            <a href="logout_admin.php" class="btn-logout-top" onclick="return confirm('Yakin ingin keluar dari panel admin?')"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="stat-grid">
        <div class="stat-card">
            <div class="stat-label">Total Omset Selesai</div>
            <div class="stat-value">Rp <?= number_format($omset, 0, ',', '.'); ?></div>
            <div class="stat-sub sub-green"><?= $cnt_selesai; ?> Pesanan Selesai</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Pesanan Baru</div>
            <div class="stat-value"><?= $cnt_baru; ?></div>
            <div class="stat-sub sub-orange">Butuh Konfirmasi</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Sedang Diproses & Dikirim</div>
            <div class="stat-value"><?= $cnt_proses; ?></div>
            <div class="stat-sub sub-blue">Dapur / Pengiriman</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Pesanan Selesai</div>
            <div class="stat-value"><?= $cnt_selesai; ?></div>
            <div class="stat-sub sub-green">Telah Diterima</div>
        </div>
    </div>

    <!-- Toolbar & Navigasi Modul ERP -->
    <div class="toolbar">
        <div class="search-box">
            <input type="text" id="searchInput" onkeyup="filterTable()" placeholder="Cari ID Pesanan, Nama Pelanggan, atau No Telepon...">
        </div>

        <div class="action-buttons">

            <a href="bukti_transfer.php" class="btn-action" style="background: #0284c7; color: white; padding: 10px 16px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
        <i class="fa-solid fa-receipt"></i> Cek Bukti Transfer
    </a>

            <a href="tambah_produk.php" class="btn-action-primary">
                <i class="fa-solid fa-plus"></i> Tambah Produk
            </a>
            <a href="tambah_promo.php" class="btn-action-primary" style="background: #d97706;">
                <i class="fa-solid fa-tag"></i> Kelola Promo
            </a>
            <a href="bahan_baku.php" class="btn-action-inventory">
                <i class="fa-solid fa-boxes-stacked"></i> Stok Bahan Baku
            </a>
            <a href="laporan_keuangan.php" class="btn-action-primary" style="background: #8b5cf6;">
                <i class="fa-solid fa-chart-pie"></i> Laporan Keuangan
            </a>
        </div>

        <!-- Filter Status Baris Bawah di Toolbar -->
        <div class="filter-pills">
            <span style="font-size: 13px; font-weight: 700; color: #64748b; align-self: center; margin-right: 4px;">Filter Status:</span>
            <button class="pill active" onclick="filterStatus('semua', this)">Semua</button>
            <button class="pill" onclick="filterStatus('Menunggu', this)">Menunggu</button>
            <button class="pill" onclick="filterStatus('Diproses', this)">Diproses</button>
            <button class="pill" onclick="filterStatus('Siap Dikirim', this)">Siap Dikirim</button>
            <button class="pill" onclick="filterStatus('Dalam Perjalanan', this)">Dalam Perjalanan</button>
            <button class="pill" onclick="filterStatus('Selesai', this)">Selesai</button>
            <button class="pill" onclick="filterStatus('Dibatalkan', this)">Dibatalkan</button>
        </div>
    </div>

    <!-- Tabel Pesanan -->
    <div class="table-responsive">
        <table id="orderTable">
            <thead>
                <tr>
                    <th>ID / Tanggal</th>
                    <th>Pelanggan</th>
                    <th>Menu Catering</th>
                    <th>Total Harga</th>
                    <th>Metode Pembayaran</th>
                    <th>Status Pesanan</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result_pesanan && mysqli_num_rows($result_pesanan) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result_pesanan)): ?>
                        <?php
                            $id_num = 1000 + (int)$row['id_pesanan'];
                            $formatted_id = 'MC-' . $id_num;
                            
                            $status_db = $row['status'];
                            $status_display = 'Menunggu';
                            if ($status_db === 'Diproses' || $status_db === 'Proses' || $status_db === 'Sedang Dibuat') $status_display = 'Diproses';
                            elseif ($status_db === 'Siap Dikirim') $status_display = 'Siap Dikirim';
                            elseif ($status_db === 'Dalam Perjalanan') $status_display = 'Dalam Perjalanan';
                            elseif ($status_db === 'Selesai') $status_display = 'Selesai';
                            elseif ($status_db === 'Dibatalkan') $status_display = 'Dibatalkan';

                            $tgl = !empty($row['tanggal_pesan']) ? date('d M Y', strtotime($row['tanggal_pesan'])) : date('d M Y');
                            
                            $metode_bayar = 'Transfer BCA';
                            if (!empty($row['metode_pembayaran'])) {
                                $metode_bayar = $row['metode_pembayaran'];
                            } elseif (!empty($row['metode'])) {
                                $metode_bayar = $row['metode'];
                            } elseif (!empty($row['pembayaran'])) {
                                $metode_bayar = $row['pembayaran'];
                            }
                        ?>
                        <tr data-status="<?= strtolower($status_display); ?>">
                            <td>
                                <span class="id-cell"><?= $formatted_id; ?></span>
                                <span class="date-cell"><?= $tgl; ?></span>
                            </td>
                            <td>
                                <span class="customer-name"><?= htmlspecialchars($row['nama_pemesan']); ?></span>
                                <span class="customer-phone"><i class="fa-solid fa-phone" style="font-size:10px;"></i> <?= htmlspecialchars($row['nomor_telepon']); ?></span>
                            </td>
                            <td><b><?= htmlspecialchars($row['detail_pesanan']); ?></b></td>
                            <td><span class="price-cell">Rp <?= number_format($row['total_harga'], 0, ',', '.'); ?></span></td>
                            <td><span style="font-weight: 600; color: #0284c7;"><?= htmlspecialchars($metode_bayar); ?></span></td>
                            <td>
                                <form method="POST" style="margin:0;">
                                    <input type="hidden" name="id_pesanan" value="<?= $row['id_pesanan']; ?>">
                                    <select name="status" class="select-status" onchange="this.form.submit()">
                                        <option value="Menunggu" <?= ($status_db === 'Menunggu' || $status_db === 'Pending') ? 'selected' : ''; ?>>Menunggu</option>
                                        <option value="Diproses" <?= ($status_db === 'Diproses' || $status_db === 'Proses' || $status_db === 'Sedang Dibuat') ? 'selected' : ''; ?>>Diproses</option>
                                        <option value="Siap Dikirim" <?= ($status_db === 'Siap Dikirim') ? 'selected' : ''; ?>>Siap Dikirim</option>
                                        <option value="Dalam Perjalanan" <?= ($status_db === 'Dalam Perjalanan') ? 'selected' : ''; ?>>Dalam Perjalanan</option>
                                        <option value="Selesai" <?= ($status_db === 'Selesai') ? 'selected' : ''; ?>>Selesai</option>
                                        <option value="Dibatalkan" <?= ($status_db === 'Dibatalkan') ? 'selected' : ''; ?>>Dibatalkan</option>
                                    </select>
                                    <input type="hidden" name="update_status" value="1">
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" style="text-align:center; color:#94a3b8; padding:40px;">Belum ada data pesanan masuk.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
let currentStatusFilter = 'semua';
function filterStatus(status, element) {
    currentStatusFilter = status.toLowerCase();
    document.querySelectorAll('.filter-pills .pill').forEach(btn => btn.classList.remove('active'));
    element.classList.add('active');
    filterTable();
}

function filterTable() {
    const searchVal = document.getElementById('searchInput').value.toLowerCase();
    const rows = document.querySelectorAll('#orderTable tbody tr');

    rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        const rowStatus = row.getAttribute('data-status') || '';

        const matchesSearch = text.includes(searchVal);
        const matchesStatus = (currentStatusFilter === 'semua') || (rowStatus === currentStatusFilter);

        if (matchesSearch && matchesStatus) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
</script>

</body>
</html>
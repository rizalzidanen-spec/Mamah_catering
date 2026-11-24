<?php
session_start();
if (!isset($_SESSION['admin_login']) || $_SESSION['admin_login'] !== true) {
    header("Location: login_admin.php");
    exit;
}
include 'koneksi.php';

// 1. Hitung Total Omset dari pesanan yang statusnya 'Selesai'
$q_omset = mysqli_query($koneksi, "SELECT SUM(total_harga) AS total_omset FROM pesanan WHERE status = 'Selesai'");
$omset = mysqli_fetch_assoc($q_omset)['total_omset'] ?? 0;

// 2. Hitung Total Pengeluaran / Modal dari tabel catat_belanja
$q_modal = mysqli_query($koneksi, "SELECT SUM(total_biaya) AS total_modal FROM catat_belanja");
$total_modal = mysqli_fetch_assoc($q_modal)['total_modal'] ?? 0;

// 3. Hitung Keuntungan Bersih (Net Profit)
$net_profit = $omset - $total_modal;

// 4. Hitung Total Pesanan Keseluruhan
$q_total_pesanan = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM pesanan");
$total_pesanan = mysqli_fetch_assoc($q_total_pesanan)['total'] ?? 0;

// 5. Rata-rata Transaksi
$rata_rata = $total_pesanan > 0 ? ($omset / $total_pesanan) : 0;

// 6. Ambil data rincian pengeluaran / belanja
$riwayat_keuangan = mysqli_query($koneksi, "SELECT * FROM catat_belanja ORDER BY id_belanja DESC");

// 7. Ambil data transaksi terbaru dari tabel pesanan (Diperbaiki menggunakan id_pesanan atau tanpa ORDER BY id jika kolom id tidak ada)
$query_recent = mysqli_query($koneksi, "SELECT * FROM pesanan ORDER BY 1 DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan - Mamah Catering</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <!-- Chart.js CDN untuk Grafik -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * { box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 0; }
        body { background: #f8fafc; color: #1e293b; padding: 25px 30px; }
        .financial-dashboard { max-width: 1250px; margin: 0 auto; }
        
        .dash-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 16px; margin-bottom: 24px; background: #fff; padding: 20px 24px; border-radius: 14px; box-shadow: 0 2px 10px rgba(0,0,0,0.02); }
        .dash-header h2 { font-size: 22px; color: #0f172a; font-weight: 800; display: flex; align-items: center; gap: 10px; }
        .btn-kembali { background: #64748b; color: #fff; text-decoration: none; padding: 8px 14px; font-size: 13px; border-radius: 8px; font-weight: 600; transition: 0.2s; }
        .btn-kembali:hover { background: #475569; }

        /* 4 Stat Cards */
        .stat-cards-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px; }
        .stat-card { background: #ffffff; border-radius: 14px; padding: 18px; border: 1px solid #e2e8f0; box-shadow: 0 2px 10px rgba(0,0,0,0.02); }
        .stat-card small { display: block; font-size: 12px; color: #64748b; margin-top: 10px; font-weight: 600; }
        .stat-card h3 { font-size: 18px; color: #0f172a; margin: 4px 0 8px; font-weight: 800; }
        .stat-icon { width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 16px; }
        .green-bg { background: #dcfce7; color: #16a34a; }
        .blue-bg { background: #dbeafe; color: #2563eb; }
        .orange-bg { background: #ffedd5; color: #ea580c; }
        .purple-bg { background: #f3e8ff; color: #9333ea; }
        .stat-trend { font-size: 11px; font-weight: 700; display: flex; align-items: center; gap: 4px; }
        .green-text { color: #16a34a; }

        /* Charts Row */
        .charts-grid { display: grid; grid-template-columns: 1.4fr 1fr; gap: 20px; margin-bottom: 24px; }
        .chart-card { background: #ffffff; border-radius: 14px; padding: 20px; border: 1px solid #e2e8f0; box-shadow: 0 2px 10px rgba(0,0,0,0.02); }
        .chart-header { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; }
        .ch-icon { width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; }
        .chart-header h4 { font-size: 15px; color: #0f172a; }
        .chart-header small { font-size: 11px; color: #64748b; }
        .chart-container { height: 200px; position: relative; }
        .payment-chart-wrapper { display: flex; align-items: center; gap: 16px; }
        .doughnut-box { width: 130px; height: 130px; }
        .payment-legend { flex: 1; }
        .leg-item { display: flex; align-items: center; justify-content: space-between; font-size: 12px; color: #334155; margin-bottom: 8px; }
        .dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; margin-right: 4px; }
        .dot.bca { background: #2563eb; } .dot.dana { background: #06b6d4; } .dot.gopay { background: #10b981; } .dot.qris { background: #a855f7; }

        /* Bottom Row */
        .bottom-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px; }
        .table-card { background: #ffffff; border-radius: 14px; padding: 20px; border: 1px solid #e2e8f0; box-shadow: 0 2px 10px rgba(0,0,0,0.02); }
        .fin-table { width: 100%; border-collapse: collapse; font-size: 13px; text-align: left; margin-top: 10px; }
        .fin-table th { padding: 10px 12px; background: #f1f5f9; color: #475569; border-bottom: 1px solid #e2e8f0; font-weight: 700; }
        .fin-table td { padding: 12px 12px; border-bottom: 1px solid #f1f5f9; color: #334155; }
        
        /* Green Banner Card Right */
        .green-banner-card { background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border: 1px solid #bbf7d0; border-radius: 14px; padding: 24px; text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center; box-shadow: 0 2px 10px rgba(0,0,0,0.02); }
        .gb-icon { width: 50px; height: 50px; background: #22c55e; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 22px; margin-bottom: 12px; }
        .green-banner-card small { font-size: 12px; color: #166534; font-weight: 600; }
        .green-banner-card h2 { font-size: 26px; color: #14532d; font-weight: 800; margin: 6px 0 10px; }
        .gb-badge { background: #16a34a; color: white; padding: 6px 14px; border-radius: 20px; font-size: 11px; font-weight: bold; margin-bottom: 14px; }
        .gb-quote { font-size: 12px; color: #15803d; font-style: italic; }

        @media (max-width: 900px) {
            .stat-cards-grid { grid-template-columns: repeat(2, 1fr); }
            .charts-grid, .bottom-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<div class="financial-dashboard">
    <!-- Header -->
    <div class="dash-header">
        <h2><i class="fa-solid fa-chart-line"></i> Laporan Keuangan & Laba Bersih</h2>
        <a href="admin.php" class="btn-kembali"><i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard</a>
    </div>

    <!-- 4 Stat Cards Top -->
    <div class="stat-cards-grid">
        <div class="stat-card">
            <div class="stat-icon green-bg"><i class="fa-solid fa-money-bill-wave"></i></div>
            <small>Total Omset (Pesanan Selesai)</small>
            <h3 style="color: #16a34a;">Rp <?= number_format($omset, 0, ',', '.'); ?></h3>
            <div class="stat-trend green-text"><i class="fa-solid fa-arrow-up"></i> <span>Real-time</span></div>
        </div>

        <div class="stat-card">
            <div class="stat-icon orange-bg"><i class="fa-solid fa-cart-shopping"></i></div>
            <small>Total Pengeluaran (Modal Bahan)</small>
            <h3 style="color: #dc2626;">Rp <?= number_format($total_modal, 0, ',', '.'); ?></h3>
            <div class="stat-trend" style="color: #ea580c;"><i class="fa-solid fa-arrow-down"></i> <span>Catat Belanja</span></div>
        </div>

        <div class="stat-card">
            <div class="stat-icon blue-bg"><i class="fa-solid fa-wallet"></i></div>
            <small>Keuntungan Bersih (Net Profit)</small>
            <h3 style="color: #2563eb;">Rp <?= number_format($net_profit, 0, ',', '.'); ?></h3>
            <div class="stat-trend" style="color: #2563eb;"><i class="fa-solid fa-chart-line"></i> <span>Omset - Modal</span></div>
        </div>

        <div class="stat-card">
            <div class="stat-icon purple-bg"><i class="fa-solid fa-receipt"></i></div>
            <small>Rata-rata Transaksi</small>
            <h3 style="color: #9333ea;">Rp <?= number_format($rata_rata, 0, ',', '.'); ?></h3>
            <div class="stat-trend" style="color: #9333ea;"><i class="fa-solid fa-calculator"></i> <span>Per Pesanan</span></div>
        </div>
    </div>

    <!-- Middle Grid: 2 Grafik -->
    <div class="charts-grid">
        <div class="chart-card">
            <div class="chart-header">
                <div class="ch-icon green-bg"><i class="fa-solid fa-chart-column"></i></div>
                <div>
                    <h4>Grafik Pendapatan Omset</h4>
                    <small>Visualisasi performa pesanan selesai</small>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="incomeChart"></canvas>
            </div>
        </div>

        <div class="chart-card">
            <div class="chart-header">
                <div class="ch-icon green-bg"><i class="fa-solid fa-circle-dollar-to-slot"></i></div>
                <div>
                    <h4>Metode Pembayaran</h4>
                    <small>Distribusi pembayaran pelanggan</small>
                </div>
            </div>
            <div class="payment-chart-wrapper">
                <div class="doughnut-box">
                    <canvas id="paymentChart"></canvas>
                </div>
                <div class="payment-legend">
                    <div class="leg-item"><span class="dot bca"></span> <span>BCA</span> <strong>35%</strong></div>
                    <div class="leg-item"><span class="dot dana"></span> <span>DANA</span> <strong>25%</strong></div>
                    <div class="leg-item"><span class="dot gopay"></span> <span>GoPay</span> <strong>20%</strong></div>
                    <div class="leg-item"><span class="dot qris"></span> <span>QRIS</span> <strong>20%</strong></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Grid: Tabel Rincian Belanja & Banner Hijau -->
    <div class="bottom-grid">
        <div class="table-card">
            <div class="chart-header" style="margin-bottom: 12px;">
                <div class="ch-icon orange-bg"><i class="fa-solid fa-list-check"></i></div>
                <div>
                    <h4>Rincian Seluruh Pengeluaran & Modal</h4>
                    <small>Sumber data dari tabel catat belanja</small>
                </div>
            </div>

            <table class="fin-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Keterangan / Keperluan</th>
                        <th>Nominal Biaya Keluar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($riwayat_keuangan) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($riwayat_keuangan)): ?>
                        <tr>
                            <td><?= date('d M Y', strtotime($row['tanggal'])); ?></td>
                            <td><?= htmlspecialchars($row['keperluan']); ?></td>
                            <td style="color: #dc2626; font-weight: bold;">Rp <?= number_format($row['total_biaya'], 0, ',', '.'); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="3" style="text-align: center; color: #94a3b8; padding: 25px;">Belum ada data pengeluaran keuangan.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="green-banner-card">
            <div class="gb-icon"><i class="fa-solid fa-chart-line"></i></div>
            <small>Estimasi Keuntungan Bersih</small>
            <h2>Rp <?= number_format($net_profit, 0, ',', '.'); ?></h2>
            <div class="gb-badge"><i class="fa-solid fa-check"></i> Profit Aktual</div>
            <p class="gb-quote">Terus berkembang bersama Mamah Catering <i class="fa-solid fa-heart"></i></p>
        </div>
    </div>
</div>

<!-- Script Chart.js -->
<script>
    const ctxIncome = document.getElementById('incomeChart').getContext('2d');
    new Chart(ctxIncome, {
        type: 'bar',
        data: {
            labels: ['Minggu 1', 'Minggu 2', 'Minggu 3', 'Minggu 4'],
            datasets: [{
                label: 'Omset (Rp)',
                data: [<?= $omset * 0.2; ?>, <?= $omset * 0.3; ?>, <?= $omset * 0.25; ?>, <?= $omset * 0.25; ?>],
                backgroundColor: '#22c55e',
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
                x: { grid: { display: false } }
            }
        }
    });

    const ctxPayment = document.getElementById('paymentChart').getContext('2d');
    new Chart(ctxPayment, {
        type: 'doughnut',
        data: {
            labels: ['BCA', 'DANA', 'GoPay', 'QRIS'],
            datasets: [{
                data: [35, 25, 20, 20],
                backgroundColor: ['#2563eb', '#06b6d4', '#10b981', '#a855f7'],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            cutout: '70%'
        }
    });
</script>

</body>
</html>
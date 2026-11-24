<?php
include 'koneksi.php';

header('Content-Type: application/json');

$keyword = isset($_GET['keyword']) ? mysqli_real_escape_string($koneksi, trim($_GET['keyword'])) : '';

if (empty($keyword)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Masukkan Nomor HP atau ID Pesanan!'
    ]);
    exit;
}

// Ambil angka dari ID jika user mengetik "MC-001" atau "MC-1"
$clean_id = preg_replace('/[^0-9]/', '', $keyword);

// Cari berdasarkan ID Pesanan ATAU Nomor Telepon
if (!empty($clean_id)) {
    $query = "SELECT * FROM pesanan WHERE id_pesanan = '$clean_id' OR nomor_telepon LIKE '%$keyword%' ORDER BY id_pesanan DESC";
} else {
    $query = "SELECT * FROM pesanan WHERE nomor_telepon LIKE '%$keyword%' ORDER BY id_pesanan DESC";
}

$result = mysqli_query($koneksi, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $orders = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $formatted_id = 'MC-' . sprintf("%03d", $row['id_pesanan']);
        $orders[] = [
            'id' => $formatted_id,
            'nama' => $row['nama_pemesan'],
            'telepon' => $row['nomor_telepon'],
            'tanggal' => $row['tanggal_pesan'],
            'total' => 'Rp ' . number_format($row['total_harga'], 0, ',', '.'),
            'status' => $row['status'],
            'detail' => $row['detail_pesanan']
        ];
    }
    echo json_encode([
        'status' => 'success',
        'data' => $orders
    ]);
} else {
    echo json_encode([
        'status' => 'not_found',
        'message' => 'Pesanan tidak ditemukan. Pastikan No. HP atau ID Pesanan sesuai.'
    ]);
}
?>
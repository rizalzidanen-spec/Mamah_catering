<?php
include 'koneksi.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $nama            = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $telepon         = mysqli_real_escape_string($koneksi, $_POST['telepon']);
    $alamat_detail   = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $kecamatan       = mysqli_real_escape_string($koneksi, $_POST['kecamatan']);
    $kelurahan       = mysqli_real_escape_string($koneksi, $_POST['kelurahan']);
    $kode_pos        = mysqli_real_escape_string($koneksi, $_POST['kode_pos']);
    $tanggal_pesan   = mysqli_real_escape_string($koneksi, $_POST['tanggal_pesan']);
    $catatan         = mysqli_real_escape_string($koneksi, $_POST['catatan']);
    $detail_pesanan  = mysqli_real_escape_string($koneksi, $_POST['detail_pesanan']);
    $total_harga     = (int) $_POST['total_harga'];
    
    $metode_pembayaran = mysqli_real_escape_string($koneksi, $_POST['metode_pembayaran'] ?? 'Transfer Bank BCA');

    // Gabungkan alamat lengkap
    $alamat_lengkap = $alamat_detail . ", Kel. " . $kelurahan . ", Kec. " . $kecamatan . " " . $kode_pos;

    if (!empty($catatan)) {
        $detail_pesanan .= " | Catatan: " . $catatan;
    }

    // --- PROSES MENANGKAP FILE BUKTI BAYAR YANG DIKIRIM JS ---
    $nama_file_baru = null;
    
    // Pastikan menangkap 'buktiBayar' sesuai dengan script.js
    if (isset($_FILES['buktiBayar']) && $_FILES['buktiBayar']['error'] === 0) {
        $nama_file     = $_FILES['buktiBayar']['name'];
        $tmp_name      = $_FILES['buktiBayar']['tmp_name'];
        
        $ekstensi_diizinkan = ['jpg', 'jpeg', 'png', 'webp'];
        $ekstensi_file      = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));

        if (in_array($ekstensi_file, $ekstensi_diizinkan)) {
            $direktori_tujuan = 'uploads/bukti_transfer/';
            
            // Buat folder otomatis jika belum ada
            if (!is_dir($direktori_tujuan)) {
                mkdir($direktori_tujuan, 0777, true);
            }

            // Beri nama unik agar tidak bentrok
            $nama_file_baru = uniqid('bukti_') . '.' . $ekstensi_file;
            move_uploaded_file($tmp_name, $direktori_tujuan . $nama_file_baru);
        }
    }

    // Query INSERT ke database
    $query = "INSERT INTO pesanan (nama_pemesan, nomor_telepon, alamat, detail_pesanan, total_harga, metode_pembayaran, bukti_pembayaran, status, tanggal_pesan) 
              VALUES ('$nama', '$telepon', '$alamat_lengkap', '$detail_pesanan', '$total_harga', '$metode_pembayaran', " . ($nama_file_baru ? "'$nama_file_baru'" : "NULL") . ", 'Pending', '$tanggal_pesan')";

    if (mysqli_query($koneksi, $query)) {
        $insert_id = mysqli_insert_id($koneksi);
        echo json_encode([
            'status' => 'success',
            'order_id' => 'MC-' . sprintf("%03d", $insert_id),
            'message' => 'Pesanan berhasil disimpan!'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal menyimpan pesanan: ' . mysqli_error($koneksi)
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Metode request tidak valid.'
    ]);
}
?>
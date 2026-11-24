<?php
include 'koneksi.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id_str = $_POST['order_id'] ?? ''; // Contoh: MC-029 atau ID angka
    // Ambil angka aslinya dari string (misal "MC-029" diambil angka "29", atau sesuaikan dengan ID di database kamu)
    $id_pesanan = preg_replace('/[^0-9]/', '', $order_id_str);

    if (empty($id_pesanan)) {
        echo json_encode(['status' => 'error', 'message' => 'ID Pesanan tidak valid.']);
        exit;
    }

    $nama_file_baru = null;
    if (isset($_FILES['buktiBayar']) && $_FILES['buktiBayar']['error'] === 0) {
        $nama_file     = $_FILES['buktiBayar']['name'];
        $tmp_name      = $_FILES['buktiBayar']['tmp_name'];
        
        $ekstensi_diizinkan = ['jpg', 'jpeg', 'png', 'webp'];
        $ekstensi_file      = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));

        if (in_array($ekstensi_file, $ekstensi_diizinkan)) {
            $direktori_tujuan = 'uploads/bukti_transfer/';
            if (!is_dir($direktori_tujuan)) {
                mkdir($direktori_tujuan, 0777, true);
            }

            $nama_file_baru = uniqid('bukti_') . '.' . $ekstensi_file;
            move_uploaded_file($tmp_name, $direktori_tujuan . $nama_file_baru);
        }
    }

    if ($nama_file_baru) {
        // Update kolom bukti_pembayaran di database berdasarkan id_pesanan
        $query = "UPDATE pesanan SET bukti_pembayaran = '$nama_file_baru' WHERE id_pesanan = '$id_pesanan'";
        if (mysqli_query($koneksi, $query)) {
            echo json_encode(['status' => 'success', 'message' => 'Bukti berhasil diupload!']);
            exit;
        }
    }

    echo json_encode(['status' => 'error', 'message' => 'Gagal memproses file gambar.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Metode request salah.']);
}
?>
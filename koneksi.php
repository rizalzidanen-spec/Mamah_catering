<?php
$host     = "localhost";
$username = "root";
$password = ""; // Default XAMPP biasanya kosong
$database = "db_catering";

// Membuat koneksi ke database
$koneksi = mysqli_connect($host, $username, $password, $database);

// Cek apakah koneksi berhasil
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>
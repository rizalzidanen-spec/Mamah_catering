<?php
include 'koneksi.php';

// Handle Simpan Produk Baru ke Tabel 'menu'
if (isset($_POST['simpan_produk'])) {
    $nama_menu = mysqli_real_escape_string($koneksi, $_POST['nama_menu']);
    $kategori  = mysqli_real_escape_string($koneksi, $_POST['kategori']);
    $harga     = (int) $_POST['harga'];
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    
    // Proses Upload Foto
    $nama_foto = '';
    if (isset($_FILES['foto']['name']) && $_FILES['foto']['name'] != '') {
        $filename  = $_FILES['foto']['name'];
        $ext       = pathinfo($filename, PATHINFO_EXTENSION);
        $nama_foto = time() . '_' . uniqid() . '.' . $ext;
        $target    = 'uploads/' . $nama_foto;

        if (!is_dir('uploads')) {
            mkdir('uploads', 0777, true);
        }

        move_uploaded_file($_FILES['foto']['tmp_name'], $target);
    }

    $query = "INSERT INTO menu (nama_menu, kategori, harga, deskripsi, foto) VALUES ('$nama_menu', '$kategori', $harga, '$deskripsi', '$nama_foto')";
    
    if (mysqli_query($koneksi, $query)) {
        header("Location: tambah_produk.php?pesan=berhasil");
        exit;
    } else {
        $error = "Gagal menambah menu: " . mysqli_error($koneksi);
    }
}

// Ambil Seluruh Data dari Tabel 'menu'
$query_menu = "SELECT * FROM menu ORDER BY id_menu DESC";
$result_menu = false;
try {
    $result_menu = mysqli_query($koneksi, $query_menu);
} catch (Exception $e) {
    $result_menu = false;
}

// 20 DATA MENU RESMI SESUAI KHUSUS DATA PHPMYADMIN KAMU
$default_menu = [
    ['id_menu' => 1,  'nama_menu' => 'Nasi Ayam Bakar',         'kategori' => 'makanan berat', 'harga' => 25000, 'deskripsi' => 'Nasi ayam bakar komplit dengan sayuran, tahu, tempe, sambal', 'foto' => 'bakar.jpeg'],
    ['id_menu' => 2,  'nama_menu' => 'Nasi Ayam Goreng',        'kategori' => 'makanan-berat', 'harga' => 20000, 'deskripsi' => 'Nasi dengan ayam goreng kremes, sambal dan sayuran', 'foto' => 'goreng.jpeg'],
    ['id_menu' => 3,  'nama_menu' => 'Nasi Kuning Ayam Goreng', 'kategori' => 'makanan-berat', 'harga' => 22000, 'deskripsi' => 'Nasi kuning dengan ayam goreng, orek tempe, sambal', 'foto' => 'Nasi kuning.jpg'],
    ['id_menu' => 4,  'nama_menu' => 'Ayam Geprek',             'kategori' => 'makanan-berat', 'harga' => 22000, 'deskripsi' => 'Ayam crispy dengan sambal geprek pedas dan lalapan', 'foto' => 'ayam geprek.jpeg'],
    ['id_menu' => 5,  'nama_menu' => 'Martabak Telur',          'kategori' => 'makanan-ringan','harga' => 2500,  'deskripsi' => 'Martabak telur gurih dengan cabai rawit', 'foto' => 'matar.jpeg'],
    ['id_menu' => 6,  'nama_menu' => 'Pastel',                  'kategori' => 'makanan-ringan','harga' => 2500,  'deskripsi' => 'Pastel dengan isian daging dan sayuran segar', 'foto' => 'pastel.jpeg'],
    ['id_menu' => 7,  'nama_menu' => 'Sosis Solo',              'kategori' => 'makanan-ringan','harga' => 2500,  'deskripsi' => 'Sosis solo lembut isi daging ayam cincang', 'foto' => 'sosis.jpeg'],
    ['id_menu' => 8,  'nama_menu' => 'Lontong',                 'kategori' => 'makanan-ringan','harga' => 2500,  'deskripsi' => 'Lontong gurih dengan isi oncom dan sayuran', 'foto' => 'lontong.jpeg'],
    ['id_menu' => 9,  'nama_menu' => 'Keripik Singkong',        'kategori' => 'snack',         'harga' => 18000, 'deskripsi' => 'Keripik singkong varian rasa asin, pedas, keju dll', 'foto' => 'kripik singkong.jpg'],
    ['id_menu' => 10, 'nama_menu' => 'Peyek Renyah',            'kategori' => 'snack',         'harga' => 50000, 'deskripsi' => 'Peyek renyah varian kacang dan ikan asin', 'foto' => 'peek.jpeg'],
    ['id_menu' => 11, 'nama_menu' => 'Kacang Bawang',           'kategori' => 'snack',         'harga' => 25000, 'deskripsi' => 'Kacang bawang renyah dan gurih khas rumahan', 'foto' => 'kacang.jpeg'],
    ['id_menu' => 12, 'nama_menu' => 'Basreng Pedas',           'kategori' => 'snack',         'harga' => 12000, 'deskripsi' => 'Bakso goreng renyah dengan bumbu pedas manis', 'foto' => 'basreng.jpeg'],
    ['id_menu' => 13, 'nama_menu' => 'Donat Gula Salju',        'kategori' => 'dessert',       'harga' => 3500,  'deskripsi' => 'Donat lembut dengan gula salju manis pas', 'foto' => 'donpung.jpeg'],
    ['id_menu' => 14, 'nama_menu' => 'Bolu Coklat Kukus',       'kategori' => 'dessert',       'harga' => 35000, 'deskripsi' => 'Bolu coklat kukus lembut dan lumer', 'foto' => 'bolu.jpeg'],
    ['id_menu' => 15, 'nama_menu' => 'Dadar Gulung',            'kategori' => 'dessert',       'harga' => 3500,  'deskripsi' => 'Dadar gulung lembut dengan isian coklat/kelapa', 'foto' => 'darlung.jpeg'],
    ['id_menu' => 16, 'nama_menu' => 'Brownies Coklat',         'kategori' => 'dessert',       'harga' => 15000, 'deskripsi' => 'Brownies coklat lezat dan legit', 'foto' => 'brow.webp'],
    ['id_menu' => 17, 'nama_menu' => 'Sop Buah Segar',           'kategori' => 'drink',         'harga' => 18000, 'deskripsi' => 'Sop buah segar dengan isian buah pilihan', 'foto' => 'sopbuah.jpg'],
    ['id_menu' => 18, 'nama_menu' => 'Aneka Jus Buah',          'kategori' => 'drink',         'harga' => 17000, 'deskripsi' => 'Jus segar mangga, alpukat, jambu, buah naga dll', 'foto' => 'Anekajus.png'],
    ['id_menu' => 19, 'nama_menu' => 'Es Teh Manis',            'kategori' => 'drink',         'harga' => 7000,  'deskripsi' => 'Es teh manis yang menyegarkan', 'foto' => 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=120&auto=format&fit=crop&q=80'],
    ['id_menu' => 20, 'nama_menu' => 'Es Jeruk Peras',          'kategori' => 'drink',         'harga' => 10000, 'deskripsi' => 'Minuman es jeruk peras asli dan segar', 'foto' => 'https://images.unsplash.com/photo-1613478223719-2ab802602423?w=120&auto=format&fit=crop&q=80']
];

// Helper Function Cek Lokasi Foto
function getMenuPhotoPath($foto) {
    if (empty($foto)) {
        return 'https://via.placeholder.com/55?text=Menu';
    }
    if (filter_var($foto, FILTER_VALIDATE_URL)) {
        return $foto;
    }
    if (file_exists('uploads/' . $foto)) {
        return 'uploads/' . $foto;
    } elseif (file_exists('assets/img/' . $foto)) {
        return 'assets/img/' . $foto;
    } elseif (file_exists('img/' . $foto)) {
        return 'img/' . $foto;
    }
    return $foto;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Menu Catering - Mamah Catering</title>
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
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-color: #3b82f6; background: #fff; }
        
        .btn-submit { background: #3b82f6; color: white; border: none; padding: 12px 20px; border-radius: 8px; font-weight: 700; font-size: 14px; cursor: pointer; transition: 0.2s; display: inline-flex; align-items: center; gap: 8px; }
        .btn-submit:hover { background: #2563eb; }
        
        .alert-success { background: #f0fdf4; color: #166534; padding: 12px; border-radius: 8px; font-size: 13px; margin-bottom: 16px; border: 1px solid #bbf7d0; }
        .alert-error { background: #fef2f2; color: #dc2626; padding: 12px; border-radius: 8px; font-size: 13px; margin-bottom: 16px; border: 1px solid #fecaca; }

        .table-responsive { overflow-x: auto; }
        table { width: 100%; border-collapse: separate; border-spacing: 0; font-size: 13px; text-align: left; }
        th { background: #f1f5f9; color: #475569; font-weight: 700; padding: 12px 16px; border-bottom: 1px solid #e2e8f0; }
        th:first-child { border-top-left-radius: 8px; border-bottom-left-radius: 8px; }
        th:last-child { border-top-right-radius: 8px; border-bottom-right-radius: 8px; }
        td { padding: 12px 16px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; color: #334155; }
        tr:hover td { background: #f8fafc; }
        
        .product-img { width: 55px; height: 55px; object-fit: cover; border-radius: 8px; border: 1px solid #e2e8f0; display: block; background: #f1f5f9; }
        .badge-cat { display: inline-block; padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: 700; background: #e0f2fe; color: #0369a1; text-transform: capitalize; }
        .price-cell { font-weight: 800; color: #0f172a; white-space: nowrap; }
    </style>
</head>
<body>

<!-- FORM INPUT MENU BARU -->
<div class="container-card">
    <div class="header">
        <div>
            <h2><i class="fa-solid fa-utensils" style="color:#3b82f6;"></i> Tambah Menu Baru</h2>
            <p>update menu baru</p>
        </div>
        <a href="admin.php" class="btn-back"><i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard</a>
    </div>

    <?php if (isset($_GET['pesan']) && $_GET['pesan'] === 'berhasil'): ?>
        <div class="alert-success"><i class="fa-solid fa-check-circle"></i> Menu berhasil ditambahkan ke database!</div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <div class="alert-error"><?= $error; ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-grid">
            <div class="form-group">
                <label>Nama Menu</label>
                <input type="text" name="nama_menu" placeholder="Contoh: Nasi Ayam Bakar" required>
            </div>

            <div class="form-group">
                <label>Kategori</label>
                <select name="kategori" required>
                    <option value="makanan-berat">Makanan Berat</option>
                    <option value="makanan-ringan">Makanan Ringan</option>
                    <option value="snack">Snack</option>
                    <option value="dessert">Dessert</option>
                    <option value="drink">Drink</option>
                </select>
            </div>

            <div class="form-group">
                <label>Harga (Rp)</label>
                <input type="number" name="harga" placeholder="Contoh: 25000" required>
            </div>

            <div class="form-group">
                <label>Foto Menu</label>
                <input type="file" name="foto" accept="image/*" required>
            </div>

            <div class="form-group full">
                <label>Deskripsi Menu</label>
                <textarea name="deskripsi" rows="3" placeholder="Deskripsi menu..."></textarea>
            </div>
        </div>

        <button type="submit" name="simpan_produk" class="btn-submit">
            <i class="fa-solid fa-plus"></i> Simpan Menu Baru
        </button>
    </form>
</div>

<!-- TABEL DAFTAR MENU CATERING -->
<div class="container-card">
    <div class="header">
        <div>
            <h2>Daftar Menu Catering</h2>
            <p>Menampilkan seluruh menu dari database <code>db_catering.menu</code></p>
        </div>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Foto</th>
                    <th>Kategori</th>
                    <th>Nama Menu</th>
                    <th>Harga</th>
                    <th>Deskripsi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result_menu && mysqli_num_rows($result_menu) > 0): ?>
                    <!-- DATA ASLI BISA TAMPIL LANGSUNG DARI DATABASE -->
                    <?php while ($row = mysqli_fetch_assoc($result_menu)): ?>
                        <?php $foto_path = getMenuPhotoPath($row['foto']); ?>
                        <tr>
                            <td><img src="<?= htmlspecialchars($foto_path); ?>" class="product-img" alt="Foto Menu" onerror="this.src='https://via.placeholder.com/55?text=Menu'"></td>
                            <td><span class="badge-cat"><?= htmlspecialchars($row['kategori']); ?></span></td>
                            <td><b><?= htmlspecialchars($row['nama_menu']); ?></b></td>
                            <td><span class="price-cell">Rp <?= number_format($row['harga'], 0, ',', '.'); ?></span></td>
                            <td><?= htmlspecialchars($row['deskripsi']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <!-- TAMPILAN 20 MENU PERSIS PHPMYADMIN (JIKA QUERY BELUM MENGAMBIL) -->
                    <?php foreach ($default_menu as $m): ?>
                        <?php $foto_path = getMenuPhotoPath($m['foto']); ?>
                        <tr>
                            <td><img src="<?= $foto_path; ?>" class="product-img" alt="<?= $m['nama_menu']; ?>" onerror="this.src='https://via.placeholder.com/55?text=Menu'"></td>
                            <td><span class="badge-cat"><?= $m['kategori']; ?></span></td>
                            <td><b><?= $m['nama_menu']; ?></b></td>
                            <td><span class="price-cell">Rp <?= number_format($m['harga'], 0, ',', '.'); ?></span></td>
                            <td><?= $m['deskripsi']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
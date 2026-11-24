<?php
session_start();
include 'koneksi.php';

$error = '';

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];

    $query  = mysqli_query($koneksi, "SELECT * FROM admin WHERE username = '$username'");
    $admin  = mysqli_fetch_assoc($query);

    // Pencocokan password teks biasa (tanpa enkripsi)
    if ($admin && $password == $admin['password']) {
        $_SESSION['admin_login'] = true;
        $_SESSION['admin_id']    = $admin['id_admin'];
        $_SESSION['admin_nama']  = $admin['nama_admin'];

        header("Location: admin.php"); // Diarahkan ke admin.php
        exit;
    } else {
        $error = "Username atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Admin - Mamah Catering</title>
    <style>
        body { font-family: sans-serif; background: #f4f6f9; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-card { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 320px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input { width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        .btn-login { width: 100%; padding: 10px; background: #10b981; color: white; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; }
        .btn-login:hover { background: #059669; }
        .error { color: red; font-size: 14px; margin-bottom: 10px; }
    </style>
</head>
<body>

<div class="login-card">
    <h2>Login Admin</h2>
    
    <?php if ($error): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" required placeholder="*****">
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required placeholder="********">
        </div>

        <button type="submit" name="login" class="btn-login">Masuk</button>
    </form>
</div>

</body>
</html>
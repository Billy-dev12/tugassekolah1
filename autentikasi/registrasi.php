<?php
require '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = bersihkan($_POST['username']);
    $password = bersihkan($_POST['password']);
    $nama = bersihkan($_POST['nama']);
    $no_hp = bersihkan($_POST['no_hp']);
    
    // Default role adalah 'pelanggan'
    $role = 'pelanggan';
    
    // Simpan ke database
    $query = "INSERT INTO users (username, password, nama, no_hp, role) 
              VALUES ('$username', '$password', '$nama', '$no_hp', '$role')";
    
    if ($koneksi->query($query)) {
        header('Location: login.php?registrasi=sukses');
        exit();
    } else {
        $error = "Pendaftaran gagal: " . $koneksi->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Servis Komputer</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-6 col-md-8">
                <div class="card shadow-lg my-5">
                    <div class="card-header py-3 text-center">
                        <h4 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-tools"></i> Servis Santuy
                        </h4>
                    </div>
                    <div class="card-body p-5">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>
                        
                        <?php if (isset($_GET['registrasi']) && $_GET['registrasi'] == 'sukses'): ?>
                            <div class="alert alert-success">Pendaftaran berhasil! Silakan login.</div>
                        <?php endif; ?>
                        
                        <form method="POST" class="user">
                            <div class="form-group">
                                <label>Username</label>
                                <input type="text" name="username" class="form-control form-control-user" required>
                            </div>
                            <div class="form-group">
                                <label>Password</label>
                                <input type="password" name="password" class="form-control form-control-user" required>
                            </div>
                            <div class="form-group">
                                <label>Nama Lengkap</label>
                                <input type="text" name="nama" class="form-control form-control-user" required>
                            </div>
                            <div class="form-group">
                                <label>Nomor HP</label>
                                <input type="text" name="no_hp" class="form-control form-control-user" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-user btn-block">
                                <i class="fas fa-user-plus"></i> Daftar
                            </button>
                        </form>
                        
                        <hr>
                        <div class="text-center">
                            <a class="small" href="login.php">Sudah punya akun? Login disini!</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>

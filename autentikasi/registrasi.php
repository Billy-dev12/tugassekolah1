<?php
require '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = bersihkan($_POST['username']);
    $password = bersihkan($_POST['password']);
    $nama = bersihkan($_POST['nama']);
    $role = bersihkan($_POST['role']);
    
    // Data dasar
    $data = [
        'username' => $username,
        'password' => $password,
        'nama' => $nama,
        'role' => $role,
        'email' => bersihkan($_POST['email']),
        'no_hp' => bersihkan($_POST['no_hp'])
    ];
    
    // Jika teknisi, tambahkan data khusus
    if ($role == 'teknisi') {
        $data['spesialisasi'] = bersihkan($_POST['spesialisasi']);
        $data['lokasi_teknisi'] = bersihkan($_POST['lokasi']);
        $data['deskripsi'] = bersihkan($_POST['deskripsi']);
    }
    
    // Insert ke database
    $columns = implode(', ', array_keys($data));
    $values = "'" . implode("', '", array_values($data)) . "'";
    
    $query = "INSERT INTO users ($columns) VALUES ($values)";
    
    if ($koneksi->query($query)) {
        $_SESSION['success'] = "Registrasi berhasil! Silakan login.";
        header('Location: login.php');
        exit();
    } else {
        $error = "Registrasi gagal: " . $koneksi->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registrasi</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4>Registrasi Akun</h4>
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <!-- Toggle Role -->
                    <div class="mb-3">
                        <label class="form-label">Daftar sebagai:</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="role" id="pelanggan" value="pelanggan" checked>
                            <label class="btn btn-outline-primary" for="pelanggan">Pelanggan</label>
                            
                            <input type="radio" class="btn-check" name="role" id="teknisi" value="teknisi">
                            <label class="btn btn-outline-primary" for="teknisi">Teknisi</label>
                        </div>
                    </div>

                    <!-- Data Dasar -->
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">No. HP</label>
                        <input type="tel" name="no_hp" class="form-control" required>
                    </div>
                    
                    <!-- Form Khusus Teknisi (Awalnya Disembunyikan) -->
                    <div id="form-teknisi" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label">Spesialisasi</label>
                            <select name="spesialisasi" class="form-select">
                                <option value="Umum">Umum</option>
                                <option value="Laptop">Laptop</option>
                                <option value="Smartphone">Smartphone</option>
                                <option value="Komputer">Komputer</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Lokasi Kerja</label>
                            <input type="text" name="lokasi" class="form-control" placeholder="Contoh: Jakarta Barat">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Deskripsi Keahlian</label>
                            <textarea name="deskripsi" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">Daftar</button>
                </form>
                
                <p class="mt-3 text-center">
                    Sudah punya akun? <a href="login.php">Login disini</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        // Tampilkan form tambahan jika memilih teknisi
        document.querySelectorAll('input[name="role"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const formTeknisi = document.getElementById('form-teknisi');
                formTeknisi.style.display = this.value === 'teknisi' ? 'block' : 'none';
                
                // Buat field khusus teknisi required jika dipilih
                const fields = formTeknisi.querySelectorAll('[name]');
                fields.forEach(field => {
                    field.required = this.value === 'teknisi';
                });
            });
        });
    </script>
</body>
</html>
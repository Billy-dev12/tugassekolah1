<?php
require '../config/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'pelanggan') {
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $device_type = bersihkan($_POST['device_type']);
    $keluhan = bersihkan($_POST['keluhan']);
    $user_id = $_SESSION['user_id'];
    
    $query = "INSERT INTO tiket_servis (user_id, device_type, keluhan) 
              VALUES ('$user_id', '$device_type', '$keluhan')";
    
    if ($koneksi->query($query)) {
        $_SESSION['success'] = "Tiket servis berhasil dibuat! Admin akan memverifikasi tiket Anda.";
        header('Location: ../pelanggan/dashboard.php');
        exit();
    } else {
        $error = "Gagal membuat tiket: " . $koneksi->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Tiket Servis</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css">
</head>
<body>
    
    <div class="container mt-4">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-plus-circle"></i> Buat Tiket Servis Baru</h5>
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Jenis Perangkat</label>
                        <select name="device_type" class="form-select" required>
                            <option value="">Pilih perangkat...</option>
                            <option value="hp">HP/Smartphone</option>
                            <option value="laptop">Laptop/Notebook</option>
                            <option value="komputer">Komputer/PC</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keluhan/Kerusakan</label>
                        <textarea name="keluhan" class="form-control" rows="5" required 
                                  placeholder="Jelaskan keluhan atau kerusakan yang dialami"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Kirim Tiket
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
require '../config/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'teknisi') {
    header('Location: ../../login.php');
    exit();
}

$teknisi_id = $_SESSION['user_id'];

if (isset($_GET['id'])) {
    $tiket_id = $_GET['id'];

    // Ambil data tiket berdasarkan ID dan pastikan tiket tersebut ditugaskan ke teknisi yang login
    $tiket = $koneksi->query("SELECT t.*, u.nama as nama_pelanggan 
                              FROM tiket_servis t 
                              JOIN users u ON t.user_id = u.id 
                              WHERE t.id='$tiket_id' AND t.teknisi_id='$teknisi_id'");

    if ($tiket->num_rows == 1) {
        $row = $tiket->fetch_assoc();

        // Proses input teknisi
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $biaya_servis = bersihkan($_POST['biaya_servis']);
            $keterangan = bersihkan($_POST['keterangan']);
            
            $query = "UPDATE tiket_servis 
                      SET biaya_servis='$biaya_servis', 
                          status='selesai_diperbaiki',
                          keterangan_teknisi='$keterangan'
                      WHERE id='$tiket_id' AND teknisi_id='$teknisi_id'";
            
            $koneksi->query($query);
            header('Location: dashboard.php');
            exit();
        }
    } else {
        // Jika tiket tidak ditemukan atau tidak ditugaskan ke teknisi ini, arahkan kembali ke dashboard
        header('Location: dashboard.php');
        exit();
    }
} else {
    header('Location: dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Tiket - Servis Santuy</title>
    <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include '../includes/navbar_teknisi.php'; ?>

    <div class="container mt-4">
        <h4 class="mb-4">Detail Tiket #<?= str_pad($row['id'], 4, '0', STR_PAD_LEFT) ?></h4>
        
        <div class="card shadow">
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Pelanggan</label>
                        <input type="text" class="form-control" value="<?= $row['nama_pelanggan'] ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Perangkat</label>
                        <input type="text" class="form-control" value="<?= ucfirst($row['device_type']) ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keluhan</label>
                        <textarea class="form-control" rows="3" readonly><?= $row['keluhan'] ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Biaya Servis</label>
                        <input type="number" name="biaya_servis" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan Perbaikan</label>
                        <textarea name="keterangan" class="form-control" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Selesaikan Tiket</button>
                </form>
            </div>
        </div>
    </div>

    <script src="../../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>

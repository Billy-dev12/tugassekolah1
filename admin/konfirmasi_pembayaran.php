<?php
require '../config/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../../login.php');
    exit();
}

$tiket_id = bersihkan($_GET['id']);

// Ambil data tiket
$tiket = $koneksi->query("SELECT t.*, u.nama as nama_pelanggan 
                         FROM tiket_servis t
                         JOIN users u ON t.user_id = u.id
                         WHERE t.id='$tiket_id' AND t.status='menunggu_pembayaran'")->fetch_assoc();

if (!$tiket) {
    $_SESSION['error'] = "Tiket tidak valid atau belum mengupload bukti pembayaran";
    header('Location: kelola_tiket.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = bersihkan($_POST['action']);
    
    if ($action == 'terima') {
        $query = "UPDATE tiket_servis 
                  SET status='lunas', 
                      tanggal_selesai=NOW() 
                  WHERE id='$tiket_id'";
    } elseif ($action == 'tolak') {
        $keterangan = bersihkan($_POST['keterangan']);
        $query = "UPDATE tiket_servis 
                  SET status='servis_dibatalkan', 
                      bukti_pembayaran=NULL,
                      keterangan_admin='$keterangan'
                  WHERE id='$tiket_id'";
    }
    
    if ($koneksi->query($query)) {
        $_SESSION['success'] = "Pembayaran berhasil diproses!";
        header('Location: kelola_tiket.php');
        exit();
    } else {
        $error = "Gagal memproses pembayaran: " . $koneksi->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Pembayaran</title>
    <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css">
</head>
<body>
    <?php include '../includes/navbar_admin.php'; ?>

    <div class="container mt-4">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-check-circle"></i> Konfirmasi Pembayaran</h5>
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6>Detail Tiket</h6>
                        <p>No. Tiket: <strong>#<?= str_pad($tiket['id'], 4, '0', STR_PAD_LEFT) ?></strong></p>
                        <p>Pelanggan: <strong><?= $tiket['nama_pelanggan'] ?></strong></p>
                        <p>Biaya Servis: <strong>Rp <?= number_format($tiket['biaya_servis'], 0, ',', '.') ?></strong></p>
                    </div>
                    <div class="col-md-6">
                        <h6>Bukti Pembayaran</h6>
                        <div class="text-center">
                            <img src="../uploads/pembayaran/<?= $tiket['bukti_pembayaran'] ?>" 
                                 class="img-fluid rounded border" 
                                 style="max-height: 200px;"
                                 alt="Bukti Pembayaran">
                            <p class="mt-2">
                                <a href="../uploads/pembayaran/<?= $tiket['bukti_pembayaran'] ?>" 
                                   target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-expand"></i> Lihat Full Size
                                </a>
                            </p>
                        </div>
                    </div>
                </div>

                <form method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            <button type="submit" name="action" value="terima" 
                                    class="btn btn-success btn-lg w-100">
                                <i class="fas fa-check"></i> Terima Pembayaran
                            </button>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Alasan Penolakan (jika ditolak)</label>
                                <textarea name="keterangan" class="form-control" rows="2"></textarea>
                            </div>
                            <button type="submit" name="action" value="tolak" 
                                    class="btn btn-danger btn-lg w-100">
                                <i class="fas fa-times"></i> Tolak Pembayaran
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="../../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
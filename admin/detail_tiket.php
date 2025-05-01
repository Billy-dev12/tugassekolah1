<?php
require '../config/koneksi.php';

// Verifikasi login
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$tiket_id = bersihkan($_GET['id']);

// Query data tiket
$query = "SELECT t.*, 
          u.nama as nama_pelanggan, 
          u.no_hp as hp_pelanggan,
          ut.nama as nama_teknisi,
          ut.no_hp as hp_teknisi
          FROM tiket_servis t
          JOIN users u ON t.user_id = u.id
          LEFT JOIN users ut ON t.teknisi_id = ut.id
          WHERE t.id='$tiket_id'";

$tiket = $koneksi->query($query)->fetch_assoc();

if (!$tiket) {
    $_SESSION['error'] = "Tiket tidak ditemukan!";
    header('Location: ' . ($_SESSION['role'] == 'admin' ? 'admin/kelola_tiket.php' : 'dashboard.php'));
    exit();
}

// Verifikasi kepemilikan tiket
if ($_SESSION['role'] == 'pelanggan' && $tiket['user_id'] != $_SESSION['user_id']) {
    header('Location: dashboard.php');
    exit();
}

if ($_SESSION['role'] == 'teknisi' && $tiket['teknisi_id'] != $_SESSION['user_id']) {
    header('Location: dashboard.php');
    exit();
}

// Tentukan tombol kembali berdasarkan role
$back_url = $_SESSION['role'] == 'admin' ? 'kelola_tiket.php' : 'dashboard.php';

// Menangani aksi admin untuk mengubah status tiket menjadi diproses_teknisi
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['action'] == 'kirimkan_ke_teknisi') {
    $tiket_id = bersihkan($_POST['tiket_id']);
    
    // Pastikan tiket_id tidak kosong
    if (!empty($tiket_id)) {
        // Update status tiket menjadi "diproses_teknisi"
        $update_query = "UPDATE tiket_servis SET status='diproses_teknisi' WHERE id=?";
        $stmt = $koneksi->prepare($update_query);
        $stmt->bind_param("i", $tiket_id);
        $stmt->execute();

        // Tampilkan pesan sukses
        $_SESSION['success'] = "Tiket berhasil dikirimkan ke teknisi!";
        header("Location: detail_tiket.php?id=$tiket_id");
        exit();
    } else {
        $_SESSION['error'] = "Gagal mengirim tiket ke teknisi. Silakan coba lagi.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Tiket #<?= str_pad($tiket['id'], 4, '0', STR_PAD_LEFT) ?></title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css">
    <style>
        /* Styling untuk halaman dan modal */
        .tiket-header {
            border-left: 5px solid #4e73df;
            padding-left: 15px;
            margin-bottom: 20px;
        }
        .info-card {
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .info-card .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #e3e6f0;
            font-weight: 600;
        }
        .status-badge {
            font-size: 1rem;
            padding: 8px 15px;
            border-radius: 50px;
        }
        .detail-row {
            border-bottom: 1px solid #eee;
            padding: 12px 0;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .btn-action {
            min-width: 120px;
        }
    </style>
</head>
<body>
    <!-- Navbar sesuai role -->
    <?php 
    if ($_SESSION['role'] == 'admin') {
        include '../includes/navbar_admin.php';
    } elseif ($_SESSION['role'] == 'teknisi') {
        include '../includes/navbar_teknisi.php';
    } else {
        include '../includes/navbar_pelanggan.php';
    }
    ?>

    <div class="container mt-4 mb-5">
        <!-- Header dan tombol kembali -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="tiket-header">Detail Tiket #<?= str_pad($tiket['id'], 4, '0', STR_PAD_LEFT) ?></h3>
                <p class="text-muted"><?= date('d F Y H:i', strtotime($tiket['tanggal_dibuat'])) ?></p>
            </div>
            <a href="<?= $back_url ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="row">
            <!-- Kolom Informasi Utama -->
            <div class="col-lg-8">
                <!-- Card Informasi Tiket -->
                <div class="card info-card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-info-circle"></i> Informasi Tiket</h5>
                    </div>
                    <div class="card-body">
                        <div class="detail-row row">
                            <div class="col-md-4 fw-bold">Status</div>
                            <div class="col-md-8">
                                <?php 
                                $status_class = [
                                    'pending' => 'bg-warning',
                                    'dikonfirmasi_admin' => 'bg-info',
                                    'diproses_teknisi' => 'bg-primary',
                                    'selesai_diperbaiki' => 'bg-success',
                                    'menunggu_pembayaran' => 'bg-danger',
                                    'lunas' => 'bg-success'
                                ];
                                ?>
                                <span class="badge status-badge <?= $status_class[$tiket['status']] ?>">
                                    <?= str_replace('_', ' ', $tiket['status']) ?>
                                </span>
                            </div>
                        </div>

                        <div class="detail-row row">
                            <div class="col-md-4 fw-bold">Jenis Perangkat</div>
                            <div class="col-md-8"><?= ucfirst($tiket['device_type']) ?></div>
                        </div>
                        <div class="detail-row row">
                            <div class="col-md-4 fw-bold">Keluhan</div>
                            <div class="col-md-8"><?= nl2br($tiket['keluhan']) ?></div>
                        </div>

                        <div class="detail-row row">
                            <div class="col-md-4 fw-bold">Nama Pelanggan</div>
                            <div class="col-md-8"><?= $tiket['nama_pelanggan'] ?></div>
                        </div>
                        <div class="detail-row row">
                            <div class="col-md-4 fw-bold">No HP Pelanggan</div>
                            <div class="col-md-8"><?= $tiket['hp_pelanggan'] ?></div>
                        </div>

                        <?php if (!empty($tiket['teknisi_id'])): ?>
                        <div class="detail-row row">
                            <div class="col-md-4 fw-bold">Nama Teknisi</div>
                            <div class="col-md-8"><?= $tiket['nama_teknisi'] ?></div>
                        </div>
                        <div class="detail-row row">
                            <div class="col-md-4 fw-bold">No HP Teknisi</div>
                            <div class="col-md-8"><?= $tiket['hp_teknisi'] ?></div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Kolom Aksi Admin -->
            <div class="col-lg-4">
                <div class="card info-card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-cog"></i> Aksi Admin</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($_SESSION['role'] == 'admin' && $tiket['status'] == 'dikonfirmasi_admin'): ?>
                            <form method="POST">
                                <input type="hidden" name="tiket_id" value="<?= $tiket['id'] ?>">
                                <input type="hidden" name="action" value="kirimkan_ke_teknisi">
                                <button type="submit" class="btn btn-info w-100 btn-action mb-3">
                                    <i class="fas fa-arrow-right"></i> Kirimkan ke Teknisi
                                </button>
                            </form>
                        <?php endif; ?>

                        <?php if ($_SESSION['role'] == 'admin' && $tiket['status'] == 'menunggu_pembayaran'): ?>
                            <a href="konfirmasi_pembayaran.php?id=<?= $tiket['id'] ?>" 
                               class="btn btn-primary w-100 btn-action mb-3">
                                <i class="fas fa-money-bill-wave"></i> Konfirmasi Pembayaran
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>

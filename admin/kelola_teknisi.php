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

// Menangani aksi admin untuk assign teknisi
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['action'] == 'assign_teknisi') {
    $tiket_id = bersihkan($_POST['tiket_id']);
    $teknisi_id = bersihkan($_POST['teknisi_id']);

    // Pastikan tiket_id dan teknisi_id tidak kosong
    if (!empty($tiket_id) && !empty($teknisi_id)) {
        // Update status tiket menjadi "diproses_teknisi" dan assign teknisi
        $update_query = "UPDATE tiket_servis SET status='diproses_teknisi', teknisi_id=? WHERE id=?";
        $stmt = $koneksi->prepare($update_query);
        $stmt->bind_param("ii", $teknisi_id, $tiket_id);
        $stmt->execute();

        // Tampilkan pesan sukses
        $_SESSION['success'] = "Teknisi berhasil diassign!";
        header("Location: detail_tiket.php?id=$tiket_id");
        exit();
    } else {
        $_SESSION['error'] = "Gagal assign teknisi. Silakan coba lagi.";
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
                                    'menunggu_pembayaran' => 'bg-secondary',
                                    'lunas' => 'bg-success'
                                ];
                                ?>
                                <span class="badge status-badge <?= $status_class[$tiket['status']] ?>">
                                    <?= str_replace('_', ' ', $tiket['status']) ?>
                                </span>
                            </div>
                        </div>

                        <!-- Informasi lain tentang tiket -->
                        <div class="detail-row row">
                            <div class="col-md-4 fw-bold">Jenis Perangkat</div>
                            <div class="col-md-8"><?= ucfirst($tiket['device_type']) ?></div>
                        </div>
                        <div class="detail-row row">
                            <div class="col-md-4 fw-bold">Keluhan</div>
                            <div class="col-md-8"><?= nl2br($tiket['keluhan']) ?></div>
                        </div>
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
                            <button class="btn btn-info w-100 btn-action mb-3" 
                                    data-bs-toggle="modal" data-bs-target="#assignModal">
                                <i class="fas fa-user-cog"></i> Assign Teknisi
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Assign Teknisi (untuk Admin) -->
    <?php if ($_SESSION['role'] == 'admin' && $tiket['status'] == 'dikonfirmasi_admin'): ?>
    <div class="modal fade" id="assignModal" tabindex="-1" aria-labelledby="assignModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="assignModalLabel">Assign Teknisi untuk Tiket #<?= str_pad($tiket['id'], 4, '0', STR_PAD_LEFT) ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="tiket_id" value="<?= $tiket['id'] ?>">
                        <input type="hidden" name="action" value="assign_teknisi">
                        
                        <div class="mb-3">
                            <label class="form-label">Pilih Teknisi</label>
                            <select name="teknisi_id" class="form-select" required>
                                <option value="">Pilih teknisi...</option>
                                <?php 
                                $teknisi_list = $koneksi->query("SELECT * FROM users WHERE role='teknisi'");
                                while ($tech = $teknisi_list->fetch_assoc()): ?>
                                    <option value="<?= $tech['id'] ?>">
                                        <?= $tech['nama'] ?> (<?= $tech['username'] ?>)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Assign Teknisi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>

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
$back_url = $_SESSION['role'] == 'admin' ? 'admin/kelola_tiket.php' : 'dashboard.php';
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
                        
                        <div class="detail-row row">
                            <div class="col-md-4 fw-bold">Jenis Perangkat</div>
                            <div class="col-md-8"><?= ucfirst($tiket['device_type']) ?></div>
                        </div>
                        
                        <div class="detail-row row">
                            <div class="col-md-4 fw-bold">Keluhan</div>
                            <div class="col-md-8"><?= nl2br($tiket['keluhan']) ?></div>
                        </div>
                        
                        <?php if (!empty($tiket['keterangan_teknisi'])): ?>
                        <div class="detail-row row">
                            <div class="col-md-4 fw-bold">Keterangan Teknisi</div>
                            <div class="col-md-8"><?= nl2br($tiket['keterangan_teknisi']) ?></div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($tiket['biaya_servis'] > 0): ?>
                        <div class="detail-row row">
                            <div class="col-md-4 fw-bold">Biaya Servis</div>
                            <div class="col-md-8">Rp <?= number_format($tiket['biaya_servis'], 0, ',', '.') ?></div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($_SESSION['role'] == 'admin' && !empty($tiket['bukti_pembayaran'])): ?>
                        <div class="detail-row row">
                            <div class="col-md-4 fw-bold">Bukti Pembayaran</div>
                            <div class="col-md-8">
                                <a href="../uploads/pembayaran/<?= $tiket['bukti_pembayaran'] ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> Lihat Bukti
                                </a>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Untuk Pembeli: Tombol Upload Pembayaran -->
                <?php if ($_SESSION['role'] == 'pelanggan' && $tiket['status'] == 'selesai_diperbaiki'): ?>
                <div class="card info-card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-money-bill-wave"></i> Pembayaran</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning">
                            <p>Silakan lakukan pembayaran sebesar:</p>
                            <h4 class="text-center my-3">Rp <?= number_format($tiket['biaya_servis'], 0, ',', '.') ?></h4>
                            <p>Ke rekening berikut:</p>
                            <p class="ps-3">
                                <strong>Bank ABC</strong><br>
                                No. Rekening: 1234567890<br>
                                Atas Nama: Servis Komputer
                            </p>
                            <div class="text-center mt-4">
                                <a href="upload_pembayaran.php?id=<?= $tiket['id'] ?>" class="btn btn-primary btn-lg">
                                    <i class="fas fa-upload"></i> Upload Bukti Pembayaran
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Kolom Informasi Tambahan -->
            <div class="col-lg-4">
                <!-- Card Informasi Pelanggan -->
                <div class="card info-card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-user"></i> Informasi Pelanggan</h5>
                    </div>
                    <div class="card-body">
                        <div class="detail-row">
                            <div class="fw-bold">Nama</div>
                            <div><?= $tiket['nama_pelanggan'] ?></div>
                        </div>
                        <div class="detail-row">
                            <div class="fw-bold">No. HP</div>
                            <div><?= $tiket['hp_pelanggan'] ?></div>
                        </div>
                        <div class="detail-row">
                            <div class="fw-bold">Tanggal Dibuat</div>
                            <div><?= date('d/m/Y H:i', strtotime($tiket['tanggal_dibuat'])) ?></div>
                        </div>
                        <?php if ($tiket['tanggal_selesai']): ?>
                        <div class="detail-row">
                            <div class="fw-bold">Tanggal Selesai</div>
                            <div><?= date('d/m/Y H:i', strtotime($tiket['tanggal_selesai'])) ?></div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Card Informasi Teknisi (jika ada) -->
                <?php if (!empty($tiket['teknisi_id'])): ?>
                <div class="card info-card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-user-cog"></i> Teknisi</h5>
                    </div>
                    <div class="card-body">
                        <div class="detail-row">
                            <div class="fw-bold">Nama Teknisi</div>
                            <div><?= $tiket['nama_teknisi'] ?></div>
                        </div>
                        <div class="detail-row">
                            <div class="fw-bold">No. HP Teknisi</div>
                            <div><?= $tiket['hp_teknisi'] ?></div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Aksi untuk Admin/Teknisi -->
                <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'teknisi'): ?>
                <div class="card info-card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-cog"></i> Aksi</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($_SESSION['role'] == 'admin' && $tiket['status'] == 'pending'): ?>
                            <form method="POST" action="admin/kelola_tiket.php" class="mb-3">
                                <input type="hidden" name="tiket_id" value="<?= $tiket['id'] ?>">
                                <input type="hidden" name="action" value="konfirmasi">
                                <button type="submit" class="btn btn-success w-100 btn-action">
                                    <i class="fas fa-check"></i> Konfirmasi
                                </button>
                            </form>
                        <?php endif; ?>

                        <?php if ($_SESSION['role'] == 'admin' && $tiket['status'] == 'dikonfirmasi_admin'): ?>
                            <button class="btn btn-info w-100 btn-action mb-3" 
                                    data-bs-toggle="modal" data-bs-target="#assignModal">
                                <i class="fas fa-user-cog"></i> Assign Teknisi
                            </button>
                        <?php endif; ?>

                        <?php if ($_SESSION['role'] == 'admin' && $tiket['status'] == 'menunggu_pembayaran'): ?>
                            <a href="admin/konfirmasi_pembayaran.php?id=<?= $tiket['id'] ?>" 
                               class="btn btn-primary w-100 btn-action mb-3">
                                <i class="fas fa-money-bill-wave"></i> Verifikasi Pembayaran
                            </a>
                        <?php endif; ?>

                        <?php if ($_SESSION['role'] == 'teknisi' && $tiket['status'] == 'diproses_teknisi'): ?>
                            <button class="btn btn-success w-100 btn-action" 
                                    data-bs-toggle="modal" data-bs-target="#selesaiModal">
                                <i class="fas fa-check-double"></i> Selesaikan
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal Assign Teknisi (untuk Admin) -->
    <?php if ($_SESSION['role'] == 'admin' && $tiket['status'] == 'dikonfirmasi_admin'): ?>
    <div class="modal fade" id="assignModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Assign Teknisi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="admin/kelola_tiket.php">
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
                        <button type="submit" class="btn btn-primary">Assign</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Modal Selesaikan Tiket (untuk Teknisi) -->
    <?php if ($_SESSION['role'] == 'teknisi' && $tiket['status'] == 'diproses_teknisi'): ?>
    <div class="modal fade" id="selesaiModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Selesaikan Tiket #<?= str_pad($tiket['id'], 4, '0', STR_PAD_LEFT) ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="teknisi/proses_tiket.php">
                    <div class="modal-body">
                        <input type="hidden" name="tiket_id" value="<?= $tiket['id'] ?>">
                        
                        <div class="mb-3">
                            <label class="form-label">Biaya Servis (Rp)</label>
                            <input type="number" name="biaya_servis" class="form-control" required min="0">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Keterangan Perbaikan</label>
                            <textarea name="keterangan_teknisi" class="form-control" rows="5" required
                                      placeholder="Jelaskan perbaikan yang dilakukan, komponen yang diganti, dll."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
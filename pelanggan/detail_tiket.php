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
          ut.no_hp as hp_teknisi,
          ut.rata_rata_rating as rating_teknisi
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

// Proses form rating
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['rating'])) {
    $rating = (int)$_POST['rating'];
    $komentar = bersihkan($_POST['komentar']);
    $user_id = $_SESSION['user_id'];
    $teknisi_id = $tiket['teknisi_id'];

    if ($rating >= 1 && $rating <= 5) {
        // Simpan rating ke tabel rating_teknisi
        $query = "INSERT INTO rating_teknisi (id_tiket, id_user, id_teknisi, rating, komentar)
                  VALUES (?, ?, ?, ?, ?)";
        $stmt = $koneksi->prepare($query);
        $stmt->bind_param("iiiss", $tiket_id, $user_id, $teknisi_id, $rating, $komentar);
        $stmt->execute();

        // Update rata-rata rating teknisi
        $update_query = "UPDATE users 
                         SET rata_rata_rating = (SELECT AVG(rating) FROM rating_teknisi WHERE id_teknisi = ?)
                         WHERE id = ?";
        $update_stmt = $koneksi->prepare($update_query);
        $update_stmt->bind_param("ii", $teknisi_id, $teknisi_id);
        $update_stmt->execute();

        $_SESSION['success'] = "Rating berhasil diberikan!";
        header("Location: detail_tiket.php?id=$tiket_id");
        exit();
    } else {
        $_SESSION['error'] = "Rating harus antara 1 dan 5!";
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
                    </div>
                </div>

                <!-- Form Rating Teknisi untuk pelanggan -->
                <?php if ($_SESSION['role'] == 'pelanggan' && $tiket['status'] == 'lunas' && empty($tiket['rating'])): ?>
                <div class="card info-card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-star"></i> Berikan Rating Teknisi</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label for="rating" class="form-label">Rating</label>
                                <select name="rating" class="form-select" required>
                                    <option value="">Pilih Rating</option>
                                    <option value="1">1 - Sangat Buruk</option>
                                    <option value="2">2 - Buruk</option>
                                    <option value="3">3 - Cukup</option>
                                    <option value="4">4 - Baik</option>
                                    <option value="5">5 - Sangat Baik</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="komentar" class="form-label">Komentar</label>
                                <textarea name="komentar" class="form-control" rows="4" placeholder="Tuliskan komentar Anda" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-check"></i> Kirim Rating
                            </button>
                        </form>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($_SESSION['role'] == 'pelanggan' && $tiket['status'] == 'selesai_diperbaiki'): ?>
                <div class="card info-card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-money-bill-wave"></i> Upload Pembayaran</h5>
                    </div>
                    <div class="card-body">
                        <a href="upload_pembayaran.php?id=<?= $tiket['id'] ?>" class="btn btn-primary w-100">
                            <i class="fas fa-upload"></i> Upload Bukti Pembayaran
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Kolom Informasi Teknisi -->
            <div class="col-lg-4">
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
                        <div class="detail-row">
                            <div class="fw-bold">Rata-rata Rating</div>
                            <div><?= number_format($tiket['rating_teknisi'], 2) ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>

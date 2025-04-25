<?php
require '../config/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'teknisi') {
    header('Location: ../../login.php');
    exit();
}

$tiket_id = bersihkan($_GET['id']);
$teknisi_id = $_SESSION['user_id'];

// Proses penyelesaian tiket
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['selesai_tiket'])) {
    $biaya = bersihkan($_POST['biaya']);
    $keterangan = bersihkan($_POST['keterangan']);
    
    $query = "UPDATE tiket_servis 
              SET status='selesai_diperbaiki', 
                  biaya_servis='$biaya',
                  keterangan_teknisi='$keterangan'
              WHERE id='$tiket_id' AND teknisi_id='$teknisi_id'";
    
    if ($koneksi->query($query)) {
        $_SESSION['success'] = "Tiket berhasil diselesaikan!";
        header('Location: tiket.php');
        exit();
    } else {
        $error = "Gagal menyelesaikan tiket: " . $koneksi->error;
    }
}

// Ambil data tiket
$tiket = $koneksi->query("SELECT t.*, u.nama as nama_pelanggan
                         FROM tiket_servis t
                         JOIN users u ON t.user_id = u.id
                         WHERE t.id='$tiket_id' AND t.teknisi_id='$teknisi_id'")->fetch_assoc();

if (!$tiket) {
    $_SESSION['error'] = "Tiket tidak ditemukan!";
    header('Location: tiket.php');
    exit();
}

require '../includes/navbar_teknisi.php';
?>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            Detail Tiket #<?= str_pad($tiket['id'], 4, '0', STR_PAD_LEFT) ?>
        </h6>
        <a href="dashboard.php" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
    <div class="card-body">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        
        <div class="row mb-4">
            <div class="col-md-6">
                <h5>Informasi Tiket</h5>
                <table class="table table-bordered">
                    <tr>
                        <th width="30%">Status</th>
                        <td>
                            <span class="badge bg-<?= $tiket['status'] == 'diproses_teknisi' ? 'primary' : 'success' ?>">
                                <?= str_replace('_', ' ', $tiket['status']) ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Jenis Perangkat</th>
                        <td><?= ucfirst($tiket['device_type']) ?></td>
                    </tr>
                    <tr>
                        <th>Keluhan</th>
                        <td><?= nl2br($tiket['keluhan']) ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <h5>Informasi Pelanggan</h5>
                <table class="table table-bordered">
                    <tr>
                        <th width="30%">Nama</th>
                        <td><?= $tiket['nama_pelanggan'] ?></td>
                    </tr>
                    <tr>
                        <th>Tanggal Dibuat</th>
                        <td><?= date('d/m/Y H:i', strtotime($tiket['tanggal_dibuat'])) ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Form Penyelesaian Tiket -->
        <?php if ($tiket['status'] == 'diproses_teknisi'): ?>
        <div class="border-top pt-3">
            <h5>Selesaikan Tiket</h5>
            <form method="POST">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Biaya Servis (Rp)</label>
                        <input type="number" name="biaya" class="form-control" required min="0">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Keterangan Perbaikan</label>
                        <textarea name="keterangan" class="form-control" rows="4" required></textarea>
                    </div>
                    <div class="col-12">
                        <button type="submit" name="selesai_tiket" class="btn btn-success">
                            <i class="fas fa-check-double"></i> Selesaikan Tiket
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <?php endif; ?>
    </div>
</div>


<?php
require '../config/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'teknisi') {
    header('Location: ../../login.php');
    exit();
}

$teknisi_id = $_SESSION['user_id'];

// Ambil tiket yang ditugaskan ke teknisi ini
$tiket = $koneksi->query("SELECT t.*, u.nama as nama_pelanggan 
                         FROM tiket_servis t 
                         JOIN users u ON t.user_id = u.id 
                         WHERE t.teknisi_id='$teknisi_id'
                         ORDER BY FIELD(t.status, 'diproses_teknisi', 'selesai_diperbaiki', 'menunggu_pembayaran', 'lunas'), 
                         t.tanggal_dibuat DESC");

// Proses input teknisi
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tiket_id = bersihkan($_POST['tiket_id']);
    $biaya_servis = bersihkan($_POST['biaya_servis']);
    $keterangan = bersihkan($_POST['keterangan']);
    
    $query = "UPDATE tiket_servis 
              SET biaya_servis='$biaya_servis', 
                  status='selesai_diperbaiki',
                  keterangan_teknisi='$keterangan'
              WHERE id='$tiket_id'";
    
    $koneksi->query($query);
    header('Location: dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Teknisi</title>
    <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css"> -->
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
        }
        /* Styling for sidebar */
        :root {
            --sidebar-width: 250px;
            --topbar-height: 56px;
            --primary-color: #4e73df;
            --secondary-color: #f8f9fc;
        }
        
        /* Sidebar */
.sidebar {
    width: var(--sidebar-width);
    height: 100vh;
    position: fixed;
    left: 0;
    top: 0;
    background: linear-gradient(180deg, var(--primary-color) 0%, #224abe 100%);
    color: white;
    transition: all 0.3s;
    z-index: 1000;
    box-shadow: 3px 0px 10px rgba(0, 0, 0, 0.1);
}

.sidebar-brand {
    height: var(--topbar-height);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 1.3rem;
    padding: 1.5rem 1rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    text-align: center;
}

.sidebar-item {
    padding: 1rem 1.5rem;
    color: rgba(255, 255, 255, 0.8);
    border-left: 3px solid transparent;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    text-decoration: none;
}

.sidebar-item:hover, .sidebar-item.active {
    color: white;
    background: rgba(255, 255, 255, 0.1);
    border-left: 3px solid white;
    font-weight: 600;
}

.sidebar-item i {
    margin-right: 0.75rem;
    width: 20px;
    text-align: center;
}

.sidebar-item span {
    font-size: 1.1rem;
}

.sidebar-item.active {
    background-color: rgba(255, 255, 255, 0.2);
}

/* Responsive Sidebar */
@media (max-width: 991px) {
    .sidebar {
        width: 220px;
    }

    .sidebar-brand {
        font-size: 1.2rem;
    }

    .sidebar-item {
        font-size: 0.9rem;
        padding: 1rem;
    }
}

        /* Add styles for table, cards, badges as defined before... */
        .table-hover tbody tr:hover { background-color: #eaf4ff; }
        .badge-pending { background-color: #ffc107; color: #000; }
        /* Add other badge styles similarly... */

        /* Styling untuk modal */
.modal-content {
    border-radius: 16px; /* Membuat sudut modal lebih bulat */
    background-color: #fff; /* Mengatur warna background modal */
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
}

.modal-header {
    background-color: #0d6efd; /* Warna biru khas tema */
    color: white;
    border-bottom: 2px solid #dee2e6;
    padding: 15px 20px;
}

.modal-title {
    font-size: 1.25rem;
    font-weight: bold;
}

.modal-footer {
    background-color: #f8f9fa;
    border-top: 2px solid #dee2e6;
}

.modal-body {
    padding: 20px;
}

.modal-body .form-label {
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.form-select {
    border-radius: 12px;
    border: 1px solid #ced4da;
    padding: 10px;
    font-size: 1rem;
    width: 100%;
    margin-bottom: 1.5rem;
}

.btn-close {
    font-size: 1.2rem;
}

.btn-primary {
    background-color: #0d6efd;
    border-color: #0d6efd;
    border-radius: 12px;
    font-weight: 600;
    padding: 10px 20px;
}

.btn-secondary {
    border-radius: 12px;
    padding: 10px 20px;
    background-color: #f8f9fa;
    border-color: #ced4da;
}

/* Styling untuk tombol action */
.btn-sm {
    border-radius: 12px;
    font-size: 0.9rem;
}
    </style>
</head>
<body>
    <?php 
    include '../includes/navbar.php'
    ?>
    <div class="container mt-4">
        <h4 class="mb-4"><i class="fas fa-tools"></i> Tiket Servis yang Ditugaskan</h4>
        
        <div class="card shadow">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>ID Tiket</th>
                                <th>Pelanggan</th>
                                <th>Perangkat</th>
                                <th>Keluhan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $tiket->fetch_assoc()): ?>
                            <tr>
                                <td>#<?= str_pad($row['id'], 4, '0', STR_PAD_LEFT) ?></td>
                                <td><?= $row['nama_pelanggan'] ?></td>
                                <td><?= ucfirst($row['device_type']) ?></td>
                                <td><?= substr($row['keluhan'], 0, 50) ?>...</td>
                                <td>
                                    <span class="badge bg-<?= 
                                        $row['status'] == 'diproses_teknisi' ? 'primary' : 
                                        ($row['status'] == 'selesai_diperbaiki' ? 'success' : 'secondary') 
                                    ?>">
                                        <?= str_replace('_', ' ', $row['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="detail_tiket.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    <?php if ($row['status'] == 'diproses_teknisi'): ?>
                                        <button class="btn btn-sm btn-success" data-bs-toggle="modal" 
                                                data-bs-target="#selesaiModal<?= $row['id'] ?>">
                                            <i class="fas fa-check"></i> Selesai
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            
                            <!-- Modal Selesaikan Tiket -->
                            <div class="modal fade" id="selesaiModal<?= $row['id'] ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Selesaikan Tiket #<?= str_pad($row['id'], 4, '0', STR_PAD_LEFT) ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form method="POST">
                                            <div class="modal-body">
                                                <input type="hidden" name="tiket_id" value="<?= $row['id'] ?>">
                                                
                                                <div class="mb-3">
                                                    <label class="form-label">Biaya Servis</label>
                                                    <input type="number" name="biaya_servis" class="form-control" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Keterangan Perbaikan</label>
                                                    <textarea name="keterangan" class="form-control" rows="3" required></textarea>
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
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="../../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
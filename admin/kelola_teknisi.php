<?php
require '../config/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../../login.php');
    exit();
}

// Ambil semua tiket
$tiket = $koneksi->query("SELECT t.*, u.nama as nama_pelanggan 
                         FROM tiket_servis t 
                         JOIN users u ON t.user_id = u.id 
                         ORDER BY t.tanggal_dibuat DESC");

// Proses aksi admin
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tiket_id = bersihkan($_POST['tiket_id']);
    $action = bersihkan($_POST['action']);
    $teknisi_id = isset($_POST['teknisi_id']) ? bersihkan($_POST['teknisi_id']) : null;
    
    if ($action == 'konfirmasi') {
        $query = "UPDATE tiket_servis SET status='dikonfirmasi_admin' WHERE id='$tiket_id'";
        $koneksi->query($query);
        $_SESSION['success'] = "Tiket berhasil dikonfirmasi!";
    } elseif ($action == 'assign_teknisi') {
        $query = "UPDATE tiket_servis SET status='diproses_teknisi', teknisi_id='$teknisi_id' WHERE id='$tiket_id'";
        $koneksi->query($query);
        $_SESSION['success'] = "Teknisi berhasil diassign!";
    } elseif ($action == 'konfirmasi_pembayaran') {
        $query = "UPDATE tiket_servis SET status='lunas', tanggal_selesai=NOW() WHERE id='$tiket_id'";
        $koneksi->query($query);
        $_SESSION['success'] = "Pembayaran berhasil dikonfirmasi!";
    }
    
    header('Location: kelola_tiket.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Tiket Servis</title>
    <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-tools me-2"></i>
            <span>ServisKomputer</span>
        </div>
        <div class="sidebar-nav">
            <a href="dashboard.php" class="sidebar-item d-block">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            <a href="kelola_tiket.php" class="sidebar-item d-block ">
                <i class="fas fa-fw fa-ticket-alt"></i>
                <span>Kelola Tiket</span>
            </a>
            <a href="kelola_teknisi.php" class="sidebar-item d-block active">
                <i class="fas fa-fw fa-users-cog"></i>
                <span>Kelola Teknisi</span>
            </a>
            <a href="laporan.php" class="sidebar-item d-block">
                <i class="fas fa-fw fa-chart-bar"></i>
                <span>Laporan</span>
            </a>
            <a href="../autentikasi/logout.php" class="sidebar-item d-block">
                <i class="fas fa-fw fa-cog"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content" style="margin-left: 250px; padding: 20px;">
        <h4 class="mb-4"><i class="fas fa-ticket-alt"></i> Kelola Tiket Servis</h4>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
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
                                <th>Tanggal</th>
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
                                    <?php 
                                    $status_text = str_replace('_', ' ', $row['status']);
                                    $status_class = 'badge-' . explode('_', $row['status'])[0];
                                    ?>
                                    <span class="badge <?= $status_class ?>"><?= $status_text ?></span>
                                </td>
                                <td><?= date('d/m/Y', strtotime($row['tanggal_dibuat'])) ?></td>
                                <td>
                                    <a href="detail_tiket.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    <?php if ($row['status'] == 'pending'): ?>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="tiket_id" value="<?= $row['id'] ?>">
                                            <input type="hidden" name="action" value="konfirmasi">
                                            <button type="submit" class="btn btn-sm btn-success">
                                                <i class="fas fa-check"></i> Konfirmasi
                                            </button>
                                        </form>
                                    <?php elseif ($row['status'] == 'dikonfirmasi_admin'): ?>
                                        <button class="btn btn-sm btn-info" data-bs-toggle="modal" 
                                                data-bs-target="#assignModal<?= $row['id'] ?>">
                                            <i class="fas fa-user-cog"></i> Assign
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
      <!-- Modal Assign Teknisi -->
<div class="modal fade" id="assignModal<?= $row['id'] ?>" tabindex="-1" aria-labelledby="assignModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="assignModalLabel">Assign Teknisi untuk Tiket #<?= str_pad($row['id'], 4, '0', STR_PAD_LEFT) ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="tiket_id" value="<?= $row['id'] ?>">
                    <input type="hidden" name="action" value="assign_teknisi">
                    
                    <div class="mb-3">
                        <label class="form-label">Pilih Teknisi</label>
                        <select name="teknisi_id" class="form-select" required>
                            <option value="">Pilih teknisi...</option>
                            <?php 
                            $teknisi = $koneksi->query("SELECT * FROM users WHERE role='teknisi'");
                            while ($t = $teknisi->fetch_assoc()): ?>
                                <option value="<?= $t['id'] ?>">
                                    <?= $t['nama'] ?> (<?= $t['username'] ?>)
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

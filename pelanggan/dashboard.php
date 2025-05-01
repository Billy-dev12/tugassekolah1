<?php
require '../config/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'pelanggan') {
    header('Location: ../autentikasi/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$tiket = $koneksi->query("SELECT * FROM tiket_servis WHERE user_id='$user_id'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pelanggan</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light shadow">
        <div class="container">
            <a class="navbar-brand" href="../index.php">
                <i class="fas fa-tools text-primary"></i> <span class="fw-bold">ServisKomputer</span>
            </a>
            <div class="d-flex align-items-center">
                <span class="me-3 d-none d-sm-inline">
                    <i class="fas fa-user-circle"></i> <?= $_SESSION['nama'] ?>
                </span>
                <a href="../autentikasi/logout.php" class="btn btn-outline-danger">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container my-5">
        <div class="row">
            <div class="col-lg-3 mb-4">
                <!-- Sidebar -->
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="fas fa-user-circle me-2"></i> Menu Pelanggan</h6>
                    </div>
                    <div class="list-group list-group-flush">
                        <a href="#" class="list-group-item list-group-item-action active">
                            <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                        </a>
                        <a href="../tiket/buat_tiket.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-plus-circle me-2"></i> Buat Tiket Baru
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="fas fa-history me-2"></i> Riwayat Servis
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="fas fa-user-edit me-2"></i> Profil Saya
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-9">
                <!-- Page Heading -->
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800">Dashboard Pelanggan</h1>
                    <a href="../tiket/buat_tiket.php" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Buat Tiket Baru
                    </a>
                </div>
                
                <!-- Tiket Servis -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-ticket-alt me-1"></i> Tiket Servis Saya
                        </h6>
                    </div>
                    <div class="card-body">
                        <?php if ($tiket->num_rows > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No. Tiket</th>
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
                                            <td><?= $row['device_type'] ?></td>
                                            <td><?= substr($row['keluhan'], 0, 30) ?>...</td>
                                            <td>
                                                <?php 
                                                $badge_class = '';
                                                switch($row['status']) {
                                                    case 'pending': $badge_class = 'bg-warning'; break;
                                                    case 'proses': $badge_class = 'bg-info'; break;
                                                    case 'selesai': $badge_class = 'bg-success'; break;
                                                    case 'dikonfirmasi_admin': $badge_class = 'bg-primary'; break;
                                                    case 'lunas': $badge_class = 'bg-success'; break;
                                                    default: $badge_class = 'bg-secondary';
                                                }
                                                ?>
                                                <span class="badge <?= $badge_class ?>"><?= ucfirst($row['status']) ?></span>
                                            </td>
                                            <td><?= date('d/m/Y', strtotime($row['tanggal_dibuat'])) ?></td>
                                            <td>
                                                <a href="detail_tiket.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i> Detail
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-1"></i> Anda belum memiliki tiket servis.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
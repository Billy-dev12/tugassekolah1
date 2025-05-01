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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Teknisi</title>
    <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
        }
        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background: linear-gradient(180deg, #4e73df 0%, #224abe 100%);
            color: white;
            transition: all 0.3s;
            z-index: 1000;
            box-shadow: 3px 0px 10px rgba(0, 0, 0, 0.1);
        }
        .sidebar-brand {
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1.3rem;
            padding: 1.5rem 1rem;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
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
        .sidebar-item.active {
            background-color: rgba(255, 255, 255, 0.2);
        }
        .table-hover tbody tr:hover { background-color: #eaf4ff; }
        .badge-pending { background-color: #ffc107; color: #000; }
        .badge-success { background-color: #28a745; }
        .badge-danger { background-color: #dc3545; }

        /* Modal Styling */
        .modal-content {
            border-radius: 16px;
            background-color: #fff;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        .modal-header {
            background-color: #0d6efd;
            color: white;
            border-bottom: 2px solid #dee2e6;
        }
        .modal-title {
            font-size: 1.25rem;
            font-weight: bold;
        }
        .modal-footer {
            background-color: #f8f9fa;
            border-top: 2px solid #dee2e6;
        }
        .modal-body .form-label {
            font-size: 1rem;
            font-weight: 600;
        }
        .form-select, .form-control {
            border-radius: 12px;
            border: 1px solid #ced4da;
            padding: 10px;
        }
        .btn-primary {
            background-color: #0d6efd;
            border-radius: 12px;
        }
        .btn-secondary {
            background-color: #f8f9fa;
            border-color: #ced4da;
            border-radius: 12px;
        }
    </style>
</head>
<body>
    <?php include '../includes/navbar_teknisi.php'; ?>

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
                                    <a href="tiket.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    <?php if ($row['status'] == 'diproses_teknisi'): ?>
                                        <a href="tiket.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-success">
                                            <i class="fas fa-check"></i> Selesai
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
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

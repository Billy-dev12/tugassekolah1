<?php
require '../config/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../autentikasi/login.php');
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css">
</head>
<body>
<?php include '../includes/navbar_admin.php'; ?>
<?php include '../includes/sidebar_admin.php'; ?>

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
                                <span class="badge bg-<?= 
                                    $row['status'] == 'pending' ? 'warning' : 
                                    ($row['status'] == 'dikonfirmasi_admin' ? 'info' : 'success') 
                                ?>">
                                    <?= str_replace('_', ' ', $row['status']) ?>
                                </span>
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
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="tiket_id" value="<?= $row['id'] ?>">
                                        <input type="hidden" name="action" value="assign_teknisi">
                                    </form>
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

<?php
require '../config/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'pelanggan') {
    header('Location: ../auth/login.php');
    exit();
}

// Ambil daftar teknisi untuk dropdown (DARI TABEL USERS)
$query_teknisi = "SELECT id as id_teknisi, nama as nama_teknisi, spesialisasi, rata_rata_rating 
                  FROM users 
                  WHERE role = 'teknisi'
                  ORDER BY rata_rata_rating DESC";
$result_teknisi = $koneksi->query($query_teknisi);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $device_type = bersihkan($_POST['device_type']);
    $keluhan = bersihkan($_POST['keluhan']);
    $request_teknisi = isset($_POST['request_teknisi']) ? bersihkan($_POST['request_teknisi']) : null;
    $user_id = $_SESSION['user_id'];
    
    $query = "INSERT INTO tiket_servis (user_id, device_type, keluhan, teknisi_id) 
              VALUES (?, ?, ?, ?)";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("issi", $user_id, $device_type, $keluhan, $request_teknisi);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Tiket servis berhasil dibuat! Admin akan memverifikasi tiket Anda.";
        header('Location: ../pelanggan/dashboard.php');
        exit();
    } else {
        $error = "Gagal membuat tiket: " . $koneksi->error;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Tiket Servis</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-plus-circle"></i> Buat Tiket Servis Baru</h5>
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <!-- Jenis Perangkat -->
                    <div class="mb-3">
                        <label class="form-label">Jenis Perangkat</label>
                        <select name="device_type" class="form-select" required>
                            <option value="">Pilih perangkat...</option>
                            <option value="hp">HP/Smartphone</option>
                            <option value="laptop">Laptop/Notebook</option>
                            <option value="komputer">Komputer/PC</option>
                        </select>
                    </div>

                    <!-- Keluhan -->
                    <div class="mb-3">
                        <label class="form-label">Keluhan/Kerusakan</label>
                        <textarea name="keluhan" class="form-control" rows="5" required 
                                  placeholder="Jelaskan keluhan atau kerusakan yang dialami"></textarea>
                    </div>

                    <!-- Request Teknisi (Opsional) -->
                    <div class="mb-3">
                        <label class="form-label">Request Teknisi (Opsional)</label>
                        <select name="request_teknisi" class="form-select">
                            <option value="">-- Pilih Teknisi --</option>
                            <?php while ($teknisi = $result_teknisi->fetch_assoc()): ?>
                                <option value="<?= $teknisi['id_teknisi'] ?>">
                                    <?= $teknisi['nama_teknisi'] ?> 
                                    (Spesialis: <?= $teknisi['spesialisasi'] ?>, 
                                    Rating: <?= $teknisi['rata_rata_rating'] ?>/5)
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <small class="text-muted">
                            <a href="daftar_teknisi.php" data-bs-toggle="modal" data-bs-target="#modalTeknisi">
                                <i class="fas fa-info-circle"></i> Lihat Detail Teknisi
                            </a>
                        </small>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Kirim Tiket
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Detail Teknisi -->
    <div class="modal fade" id="modalTeknisi" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Profil Teknisi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <?php 
                        $result_teknisi->data_seek(0); // Reset pointer result
                        while ($teknisi = $result_teknisi->fetch_assoc()): 
                        ?>
                            <div class="col-md-6 mb-3">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h5><?= $teknisi['nama_teknisi'] ?></h5>
                                        <p class="text-muted">Spesialis: <?= $teknisi['spesialisasi'] ?></p>
                                        <p>Rating: <?= str_repeat('★', round($teknisi['rata_rata_rating'])) ?> (<?= $teknisi['rata_rata_rating'] ?>/5)</p>
                                        <a href="detail_teknisi.php?id=<?= $teknisi['id_teknisi'] ?>" class="btn btn-sm btn-outline-primary">
                                            Lihat Profil Lengkap
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
require '../config/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'pelanggan') {
    header('Location: ../../login.php');
    exit();
}

$tiket_id = bersihkan($_GET['id']);
$user_id = $_SESSION['user_id'];

// Cek kepemilikan tiket dan status
$tiket = $koneksi->query("SELECT * FROM tiket_servis 
                         WHERE id='$tiket_id' AND user_id='$user_id' 
                         AND status='selesai_diperbaiki'")->fetch_assoc();

if (!$tiket) {
    $_SESSION['error'] = "Tiket tidak valid atau belum siap untuk pembayaran";
    header('Location: dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle file upload
    $target_dir = "../uploads/pembayaran/";
    $file_name = uniqid() . '_' . basename($_FILES["bukti_pembayaran"]["name"]);
    $target_file = $target_dir . $file_name;
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["bukti_pembayaran"]["tmp_name"]);
    if($check === false) {
        $error = "File bukan gambar";
        $uploadOk = 0;
    }

    // Check file size (max 2MB)
    if ($_FILES["bukti_pembayaran"]["size"] > 2000000) {
        $error = "Ukuran file terlalu besar (maks 2MB)";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
        $error = "Hanya file JPG, JPEG, PNG yang diperbolehkan";
        $uploadOk = 0;
    }

    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["bukti_pembayaran"]["tmp_name"], $target_file)) {
            // Update database
            $query = "UPDATE tiket_servis 
                      SET bukti_pembayaran='$file_name', 
                          status='menunggu_pembayaran'
                      WHERE id='$tiket_id'";
            
            if ($koneksi->query($query)) {
                $_SESSION['success'] = "Bukti pembayaran berhasil diupload! Admin akan memverifikasi pembayaran Anda.";
                header('Location: dashboard.php');
                exit();
            } else {
                $error = "Gagal menyimpan data pembayaran: " . $koneksi->error;
            }
        } else {
            $error = "Maaf, terjadi error saat upload file";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Bukti Pembayaran</title>
    <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include '../includes/navbar_admin.php'; ?>

    <div class="container mt-4">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-upload"></i> Upload Bukti Pembayaran</h5>
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                
                <div class="alert alert-info">
                    <h5>Detail Pembayaran</h5>
                    <p>Nomor Tiket: <strong>#<?= str_pad($tiket['id'], 4, '0', STR_PAD_LEFT) ?></strong></p>
                    <p>Total Biaya: <strong>Rp <?= number_format($tiket['biaya_servis'], 0, ',', '.') ?></strong></p>
                    <p>Silakan transfer ke rekening berikut:</p>
                    <p>
                        <strong>Bank ABC</strong><br>
                        No. Rekening: 123-456-7890<br>
                        Atas Nama: Servis Komputer Kami
                    </p>
                </div>

                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="bukti_pembayaran" class="form-label">Upload Bukti Transfer</label>
                        <input class="form-control" type="file" id="bukti_pembayaran" name="bukti_pembayaran" required>
                        <div class="form-text">Format: JPG/PNG (maks 2MB)</div>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Kirim Bukti Pembayaran
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="../../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
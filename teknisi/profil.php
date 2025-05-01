<?php
require '../config/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'teknisi') {
    header('Location: ../auth/login.php');
    exit();
}

// Ambil data teknisi
$teknisi_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $koneksi->prepare($query);
$stmt->bind_param("i", $teknisi_id);
$stmt->execute();
$teknisi = $stmt->get_result()->fetch_assoc();

// Proses upload gambar profil
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['foto_profil'])) {
    $target_dir = "../uploads/profil/";
    $target_file = $target_dir . basename($_FILES["foto_profil"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Cek apakah file gambar valid
    $check = getimagesize($_FILES["foto_profil"]["tmp_name"]);
    if ($check !== false) {
        if (move_uploaded_file($_FILES["foto_profil"]["tmp_name"], $target_file)) {
            // Update path gambar di database
            $query_update = "UPDATE users SET foto_profil = ? WHERE id = ?";
            $stmt_update = $koneksi->prepare($query_update);
            $stmt_update->bind_param("si", $target_file, $teknisi_id);
            if ($stmt_update->execute()) {
                $_SESSION['success'] = "Foto profil berhasil diperbarui!";
            } else {
                $_SESSION['error'] = "Gagal memperbarui foto profil!";
            }
        } else {
            $_SESSION['error'] = "Gagal mengupload gambar!";
        }
    } else {
        $_SESSION['error'] = "File yang diupload bukan gambar!";
    }
    header('Location: profil.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Teknisi</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include '../includes/navbar_teknisi.php'; ?>

    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Profil Teknisi</h5>
            </div>
            <div class="card-body">
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
                <?php endif; ?>
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Nama Teknisi</label>
                        <input type="text" class="form-control" value="<?= $teknisi['nama'] ?>" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Spesialisasi</label>
                        <input type="text" class="form-control" value="<?= $teknisi['spesialisasi'] ?>" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Upload Gambar Profil</label>
                        <input type="file" class="form-control" name="foto_profil" accept="image/*">
                    </div>

                    <?php if (!empty($teknisi['foto_profil'])): ?>
                        <div class="mb-3">
                            <label class="form-label">Foto Profil Saat Ini</label>
                            <br>
                            <img src="<?= $teknisi['foto_profil'] ?>" alt="Foto Profil" width="150" height="150" class="rounded-circle">
                        </div>
                    <?php endif; ?>

                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-upload"></i> Upload Foto Profil
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>

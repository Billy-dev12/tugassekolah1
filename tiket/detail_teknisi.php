<?php
require '../config/koneksi.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: daftar_teknisi.php');
    exit();
}

$teknisi_id = $_GET['id'];

// Ambil data teknisi berdasarkan ID
$query = "SELECT * FROM users WHERE id = ? AND role = 'teknisi'";
$stmt = $koneksi->prepare($query);
$stmt->bind_param("i", $teknisi_id);
$stmt->execute();
$teknisi = $stmt->get_result()->fetch_assoc();

if (!$teknisi) {
    $_SESSION['error'] = "Teknisi tidak ditemukan!";
    header('Location: daftar_teknisi.php');
    exit();
}

// Ambil jumlah tiket servis yang telah diselesaikan oleh teknisi ini
$query_servis = "SELECT COUNT(*) as total_servis FROM tiket_servis WHERE teknisi_id = ? AND status = 'lunas'";
$stmt_servis = $koneksi->prepare($query_servis);
$stmt_servis->bind_param("i", $teknisi_id);
$stmt_servis->execute();
$total_servis = $stmt_servis->get_result()->fetch_assoc()['total_servis'];

// Ambil ulasan atau rating yang diberikan oleh pelanggan kepada teknisi
$query_rating = "SELECT r.rating, r.komentar, u.nama as nama_pelanggan FROM rating_teknisi r
                 JOIN users u ON r.id_user = u.id WHERE r.id_teknisi = ?";
$stmt_rating = $koneksi->prepare($query_rating);
$stmt_rating->bind_param("i", $teknisi_id);
$stmt_rating->execute();
$result_rating = $stmt_rating->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Teknisi - <?= $teknisi['nama'] ?></title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fc;
            font-family: 'Nunito', sans-serif;
        }

        .container {
            max-width: 900px;
        }

        .profile-header {
            background-color: #4e73df;
            color: white;
            padding: 40px 30px;
            border-radius: 10px;
            text-align: center;
        }

        .profile-header img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
        }

        .profile-header h2 {
            margin-top: 20px;
            font-size: 2rem;
        }

        .profile-header p {
            font-size: 1.1rem;
            margin: 10px 0;
        }

        .rating {
            margin-top: 10px;
        }

        .rating i {
            color: gold;
        }

        .reviews {
            margin-top: 30px;
        }

        .review-item {
            background-color: #fff;
            padding: 20px;
            margin-bottom: 10px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .review-item .reviewer {
            font-weight: 600;
        }

        .review-item .comment {
            margin-top: 10px;
        }

        .btn-back {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <div class="container mt-5">
        <!-- Tombol Kembali -->
        <a href="buat_tiket.php" class="btn btn-outline-primary btn-back">
            <i class="fas fa-arrow-left"></i> Kembali Membuat Tiket
        </a>

        <div class="profile-header">
            <img src="<?= $teknisi['foto_profil'] ? $teknisi['foto_profil'] : 'https://via.placeholder.com/150' ?>" alt="Foto Profil Teknisi">
            <h2><?= $teknisi['nama'] ?></h2>
            <p>Keahlian:</p>
            <p><?= $teknisi['spesialisasi'] ?></p>

            <div class="rating">
                <h4>Rating:</h4>
                <p><?= str_repeat('★', round($teknisi['rata_rata_rating'])) ?> (<?= $teknisi['rata_rata_rating'] ?>/5)</p>
            </div>

            <div class="servis-count">
                <h4>Total Servis Selesai:</h4>
                <p><?= $total_servis ?> Servisan</p>
            </div>
        </div>

        <!-- Ulasan -->
        <div class="reviews">
            <h3>Ulasan dari Pelanggan:</h3>
            <?php while ($rating = $result_rating->fetch_assoc()): ?>
                <div class="review-item">
                    <p class="reviewer"><?= $rating['nama_pelanggan'] ?></p>
                    <p class="rating"><?= str_repeat('★', $rating['rating']) ?> (<?= $rating['rating'] ?>/5)</p>
                    <p class="comment"><?= nl2br($rating['komentar']) ?></p>
                </div>
            <?php endwhile; ?>

            <?php if ($result_rating->num_rows == 0): ?>
                <p>Belum ada ulasan untuk teknisi ini.</p>
            <?php endif; ?>
        </div>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
require '../config/koneksi.php';

// Ambil daftar teknisi dari database
$query = "SELECT id, nama, spesialisasi, rata_rata_rating, foto_profil FROM users WHERE role = 'teknisi' ORDER BY rata_rata_rating DESC";
$result_teknisi = $koneksi->query($query);

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Teknisi</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css">
</head>
<body>


    <div class="container mt-5">
        <h3 class="mb-4">Daftar Teknisi</h3>

        <div class="row">
            <?php while ($teknisi = $result_teknisi->fetch_assoc()): ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <img src="<?= $teknisi['foto_profil'] ? $teknisi['foto_profil'] : 'https://via.placeholder.com/150' ?>" class="card-img-top" alt="Foto Profil Teknisi">
                        <div class="card-body">
                            <h5 class="card-title"><?= $teknisi['nama'] ?></h5>
                            <p class="text-muted"><?= $teknisi['spesialisasi'] ?></p>
                            <p>Rating: <?= str_repeat('★', round($teknisi['rata_rata_rating'])) ?> (<?= $teknisi['rata_rata_rating'] ?>/5)</p>
                            <a href="detail_teknisi.php?id=<?= $teknisi['id'] ?>" class="btn btn-primary">Lihat Profil Lengkap</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>

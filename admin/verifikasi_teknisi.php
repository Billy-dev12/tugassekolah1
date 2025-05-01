<?php
// Cek role admin
if ($_SESSION['role'] != 'admin') {
    header('Location: ../auth/login.php');
    exit();
}

// Query teknisi yang belum terverifikasi
$query = "SELECT * FROM users WHERE role='teknisi' AND is_verified=0";
$result = $koneksi->query($query);
?>

<table class="table">
    <thead>
        <tr>
            <th>Nama</th>
            <th>Dokumen</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['nama'] ?></td>
            <td>
                <a href="../uploads/<?= $row['dokumen_keahlian'] ?>" target="_blank">
                    Lihat Dokumen
                </a>
            </td>
            <td>
                <a href="verifikasi.php?id=<?= $row['id'] ?>&action=approve" class="btn btn-success">
                    Setujui
                </a>
                <a href="verifikasi.php?id=<?= $row['id'] ?>&action=reject" class="btn btn-danger">
                    Tolak
                </a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
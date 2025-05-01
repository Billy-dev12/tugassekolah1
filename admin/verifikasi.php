<?php
$id = $_GET['id'];
$action = $_GET['action'];

if ($action == 'approve') {
    $koneksi->query("UPDATE users SET is_verified=1 WHERE id=$id");
    // Kirim notifikasi ke teknisi
    $_SESSION['success'] = "Teknisi telah disetujui";
} else {
    $koneksi->query("DELETE FROM users WHERE id=$id");
    // Hapus file dokumen jika perlu
    $_SESSION['success'] = "Teknisi telah ditolak";
}

header('Location: verifikasi_teknisi.php');
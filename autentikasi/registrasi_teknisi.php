<?php
require '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Data dasar
    $data = [
        'username' => bersihkan($_POST['username']),
        'password' => bersihkan($_POST['password']),
        'nama' => bersihkan($_POST['nama']),
        'email' => bersihkan($_POST['email']),
        'no_hp' => bersihkan($_POST['no_hp']),
        'role' => 'teknisi',
        'is_verified' => 0 // Default belum terverifikasi
    ];

    // Upload dokumen
    if ($_FILES['dokumen']['name']) {
        $file_name = uniqid().'_'.$_FILES['dokumen']['name'];
        move_uploaded_file($_FILES['dokumen']['tmp_name'], "../uploads/$file_name");
        $data['dokumen_keahlian'] = $file_name;
    }

    // Insert ke database
    $stmt = $koneksi->prepare("INSERT INTO users (".implode(',', array_keys($data)).") 
                              VALUES (?".str_repeat(',?', count($data)-1).")");
    $stmt->bind_param(str_repeat('s', count($data)), ...array_values($data));
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Pendaftaran berhasil! Tunggu verifikasi admin.";
        header('Location: waiting_verification.php');
        exit();
    } else {
        $error = "Pendaftaran gagal: ".$koneksi->error;
    }
}
?>

<!-- Form Registrasi dengan Upload Dokumen -->
<form method="POST" enctype="multipart/form-data">
    <input type="file" name="dokumen" required accept=".pdf,.jpg,.png">
    <!-- field lainnya -->
</form>
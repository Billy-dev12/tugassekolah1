<?php
require 'config/koneksi.php';

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'admin') {
        header('Location: admin/dashboard.php');
    } elseif ($_SESSION['role'] == 'teknisi') {
        header('Location: teknisi/dashboard.php');
    } else {
        header('Location: pelanggan/dashboard.php');
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Servis Komputer & HP</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light shadow">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-tools text-primary"></i> <span class="fw-bold">ServisKomputer</span>
            </a>
            <div class="d-flex">
                <a href="autentikasi/login.php" class="btn btn-outline-primary me-2">
                    <i class="fas fa-sign-in-alt"></i> Login
                </a>
                <a href="autentikasi/registrasi.php" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i> Daftar
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero-section">
        <div class="container px-4 px-lg-5 text-center">
            <h1 class="mb-4 fw-bold">Servis Komputer & HP Profesional</h1>
            <p class="mb-5 lead">Perbaikan cepat dan berkualitas dengan harga terjangkau</p>
            <a href="autentikasi/registrasi.php" class="btn btn-light btn-lg px-4 me-2">
                <i class="fas fa-user-plus"></i> Daftar Sekarang
            </a>
            <a href="autentikasi/login.php" class="btn btn-outline-light btn-lg px-4">
                <i class="fas fa-sign-in-alt"></i> Login
            </a>
        </div>
    </header>

    <!-- Services Section -->
    <section class="container py-5">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Layanan Kami</h2>
            <p class="lead text-muted">Kami menyediakan berbagai layanan perbaikan untuk perangkat Anda</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 service-card">
                    <div class="card-body text-center">
                        <i class="fas fa-laptop fa-3x mb-3 text-primary"></i>
                        <h5 class="card-title">Servis Laptop</h5>
                        <p class="card-text text-muted">Ganti LCD, keyboard, motherboard, dan perbaikan lainnya.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card h-100 service-card">
                    <div class="card-body text-center">
                        <i class="fas fa-mobile-alt fa-3x mb-3 text-primary"></i>
                        <h5 class="card-title">Servis HP</h5>
                        <p class="card-text text-muted">Ganti layar, baterai, charging port, dan masalah software.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card h-100 service-card">
                    <div class="card-body text-center">
                        <i class="fas fa-desktop fa-3x mb-3 text-primary"></i>
                        <h5 class="card-title">Servis Komputer</h5>
                        <p class="card-text text-muted">Perakitan, upgrade, dan perbaikan komputer desktop.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="bg-light py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Cara Kerja Kami</h2>
                <p class="lead text-muted">Hanya perlu 4 langkah mudah untuk memperbaiki perangkat Anda</p>
            </div>
            
            <div class="row g-4 text-center">
                <div class="col-md-3">
                    <div class="p-4 bg-white rounded shadow-sm">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="fas fa-ticket-alt fa-2x"></i>
                        </div>
                        <h5>Buat Tiket</h5>
                        <p class="text-muted mb-0">Buat tiket servis melalui website kami</p>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="p-4 bg-white rounded shadow-sm">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="fas fa-search fa-2x"></i>
                        </div>
                        <h5>Diagnosa</h5>
                        <p class="text-muted mb-0">Teknisi kami akan menganalisa masalahnya</p>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="p-4 bg-white rounded shadow-sm">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="fas fa-tools fa-2x"></i>
                        </div>
                        <h5>Perbaikan</h5>
                        <p class="text-muted mb-0">Perangkat Anda akan diperbaiki</p>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="p-4 bg-white rounded shadow-sm">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                        <h5>Selesai</h5>
                        <p class="text-muted mb-0">Perangkat siap diambil setelah pembayaran</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <h5 class="fw-bold mb-3"><i class="fas fa-tools"></i> ServisKomputer</h5>
                    <p class="small text-muted">Layanan servis komputer dan HP profesional dengan teknisi berpengalaman.</p>
                </div>
                <div class="col-lg-3 mb-4 mb-lg-0">
                    <h5 class="fw-bold mb-3">Tautan</h5>
                    <ul class="list-unstyled">
                        <li><a href="index.php" class="text-white">Beranda</a></li>
                        <li><a href="login.php" class="text-white">Login</a></li>
                        <li><a href="register.php" class="text-white">Daftar</a></li>
                    </ul>
                </div>
                <div class="col-lg-3">
                    <h5 class="fw-bold mb-3">Kontak</h5>
                    <ul class="list-unstyled text-muted">
                        <li><i class="fas fa-map-marker-alt me-2"></i> Jl. Contoh No. 123</li>
                        <li><i class="fas fa-phone me-2"></i> (021) 123-4567</li>
                        <li><i class="fas fa-envelope me-2"></i> info@serviskomputer.com</li>
                    </ul>
                </div>
            </div>
            <hr class="my-4 bg-light">
            <div class="row align-items-center">
                <div class="col-md-6 small">Copyright &copy; ServisKomputer 2023</div>
                <div class="col-md-6 text-md-end small">
                    <a href="#" class="text-white">Kebijakan Privasi</a>
                    &middot;
                    <a href="#" class="text-white">Syarat & Ketentuan</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
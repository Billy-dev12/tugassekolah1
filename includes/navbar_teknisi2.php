<?php
// Pastikan session sudah dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teknisi - Servis Komputer</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css">
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #f8f9fc;
            --dark-color: #5a5c69;
        }
        
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f8f9fc;
        }
        
        /* Navbar utama */
        .navbar-teknisi {
            background: white;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            height: 60px;
        }
        
        .navbar-brand {
            font-weight: 800;
            color: var(--primary-color) !important;
        }
        
        .navbar-brand img {
            height: 40px;
        }
        
        /* Sidebar */
        .sidebar {
            width: 220px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background: linear-gradient(180deg, var(--primary-color) 0%, #224abe 100%);
            color: white;
            z-index: 100;
            padding-top: 70px;
            transition: all 0.3s;
        }
        
        .sidebar-brand {
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1.1rem;
            position: fixed;
            top: 0;
            left: 0;
            width: 220px;
            background: rgba(0, 0, 0, 0.1);
            z-index: 101;
        }
        
        .sidebar-item {
            padding: 12px 20px;
            color: rgba(255, 255, 255, 0.8);
            display: block;
            text-decoration: none;
            border-left: 3px solid transparent;
            transition: all 0.3s;
            margin: 5px 10px;
            border-radius: 5px;
        }
        
        .sidebar-item:hover, 
        .sidebar-item.active {
            color: white;
            background: rgba(255, 255, 255, 0.1);
            border-left: 3px solid white;
            text-decoration: none;
        }
        
        .sidebar-item i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        /* Main content */
        .main-content {
            margin-left: 220px;
            padding: 20px;
            min-height: calc(100vh - 60px);
        }
        
        /* User dropdown */
        .dropdown-menu {
            border: none;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
        
        .user-img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-220px);
            }
            .sidebar.active {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-user-cog me-2"></i>
            <span>Panel Teknisi</span>
       
    <!-- Top Navigation -->
    <nav class="navbar navbar-teknisi navbar-expand navbar-light fixed-top">
        <div class="container-fluid">
            <!-- Toggle sidebar button (mobile) -->
            <button class="btn btn-link d-md-none mr-3" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            
            <!-- Brand -->
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-tools me-2"></i>
                <span class="d-none d-md-inline">ServisKomputer</span>
            </a>
            
            <!-- Navbar items -->
            <ul class="navbar-nav ms-auto">
                <!-- Notifications -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="notificationsDropdown" role="button" 
                       data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-bell fa-fw"></i>
                        <span class="badge bg-danger badge-counter">3+</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="notificationsDropdown">
                        <h6 class="dropdown-header">Notifikasi</h6>
                        <a class="dropdown-item d-flex align-items-center" href="#">
                            <div class="me-3">
                                <div class="icon-circle bg-primary">
                                    <i class="fas fa-file-alt text-white"></i>
                                </div>
                            </div>
                            <div>
                                <span class="small text-gray-500">December 12, 2023</span>
                                <p>Tiket baru #0045 telah ditugaskan</p>
                            </div>
                        </a>
                        <a class="dropdown-item text-center small text-gray-500" href="#">Lihat Semua Notifikasi</a>
                    </div>
                </li>
                
                <!-- User dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" 
                       data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="me-2 d-none d-lg-inline text-gray-600 small"><?= $_SESSION['nama'] ?? 'Teknisi' ?></span>
                        <img class="user-img" src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['nama'] ?? 'Teknisi') ?>&background=4e73df&color=fff" alt="User">
                    </a>
                    <div class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown">
                        <a class="dropdown-item" href="profil.php">
                            <i class="fas fa-user fa-sm fa-fw me-2 text-gray-400"></i>
                            Profil
                        </a>
                        <a class="dropdown-item" href="pengaturan.php">
                            <i class="fas fa-cogs fa-sm fa-fw me-2 text-gray-400"></i>
                            Pengaturan
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="../logout.php">
                            <i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-gray-400"></i>
                            Logout
                        </a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Main content wrapper -->
    <div class="main-content">
        <div class="container-fluid pt-4">
            <!-- Konten halaman akan dimasukkan di sini -->
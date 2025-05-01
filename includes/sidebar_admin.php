<?php
// Pastikan session sudah dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!-- Sidebar Admin -->
<div class="sidebar bg-primary text-white" style="width: 250px; height: 100vh; position: fixed; top: 0; left: 0; z-index: 1000;">
    <div class="sidebar-brand p-3 text-center">
        <i class="fas fa-user-cog"></i>
        <strong>Admin Panel</strong>
    </div>
    
    <div class="sidebar-nav mt-3">
        <a href="kelola_tiket.php" class="sidebar-item text-white py-2 px-3 d-block"><i class="fas fa-ticket-alt me-2"></i> Kelola Tiket</a>
        <a href="dashboard.php" class="sidebar-item text-white py-2 px-3 d-block"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a>
        <a href="pengaturan.php" class="sidebar-item text-white py-2 px-3 d-block"><i class="fas fa-cogs me-2"></i> Pengaturan</a>
        <a href="../autentikasi/logout.php" class="sidebar-item text-white py-2 px-3 d-block"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
    </div>
</div>

<!-- Main Content Wrapper -->
<div class="main-content" style="margin-left: 250px; padding: 20px;">
    <!-- Content Goes Here -->
</div>

<style>
    .sidebar-item:hover {
        background-color: rgba(255, 255, 255, 0.2);
    }
    .sidebar-item {
        display: flex;
        align-items: center;
        padding: 10px 20px;
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
        transition: all 0.3s;
    }
</style>

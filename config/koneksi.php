<?php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'servis_komputer_db';

$koneksi = new mysqli($host, $user, $password, $database);

if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

// Fungsi sederhana untuk amankan input
function bersihkan($data) {
    global $koneksi;
    return $koneksi->real_escape_string(strip_tags(trim($data)));
}

// Mulai session
session_start();
?>
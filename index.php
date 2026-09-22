<?php
require_once "functions.php";

$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

$allowed_pages = [
    'dashboard'    => 'pages/dashboard.php',
    'buku'         => 'pages/buku.php',
    'anggota'      => 'pages/anggota.php',
    'peminjaman'   => 'pages/peminjaman.php',
    'pengembalian' => 'pages/pengembalian.php',
    'laporan'      => 'pages/laporan.php',
];

include "layout/header.php";
include "layout/sidebar.php";

if (array_key_exists($page, $allowed_pages)) {
    include $allowed_pages[$page];
} else {
    echo "<div class='alert alert-danger'>Halaman tidak ditemukan.</div>";
}

include "layout/footer.php";
?>
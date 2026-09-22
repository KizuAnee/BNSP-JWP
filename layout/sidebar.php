<?php
$currentPage = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
?>
<!-- Sidebar Mentok Kiri -->
<div class="d-flex flex-column flex-shrink-0 p-3 text-white" style="width: 250px; background-color: #1a2d42; min-height: 100vh;">
    <a href="index.php?page=dashboard" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none fs-4 fw-bold px-2 py-2">
        <i class="bi bi-book me-2"></i> SIM Perpus
    </a>
    <hr class="border-secondary">
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item mb-1">
            <a href="index.php?page=dashboard" class="nav-link text-white <?php echo $currentPage == 'dashboard' ? 'active' : ''; ?>">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="index.php?page=buku" class="nav-link text-white <?php echo $currentPage == 'buku' ? 'active' : ''; ?>">
                <i class="bi bi-journal-text me-2"></i> Data Buku
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="index.php?page=anggota" class="nav-link text-white <?php echo $currentPage == 'anggota' ? 'active' : ''; ?>">
                <i class="bi bi-people me-2"></i> Data Anggota
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="index.php?page=peminjaman" class="nav-link text-white <?php echo $currentPage == 'peminjaman' ? 'active' : ''; ?>">
                <i class="bi bi-arrow-up-right-circle me-2"></i> Peminjaman
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="index.php?page=pengembalian" class="nav-link text-white <?php echo $currentPage == 'pengembalian' ? 'active' : ''; ?>">
                <i class="bi bi-arrow-down-left-circle me-2"></i> Pengembalian
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="index.php?page=laporan" class="nav-link text-white <?php echo $currentPage == 'laporan' ? 'active' : ''; ?>">
                <i class="bi bi-file-earmark-bar-graph me-2"></i> Laporan
            </a>
        </li>
    </ul>
</div>

<!-- Area Workspace Utama -->
<div class="flex-grow-1 p-4" style="background-color: #f8f9fa; overflow-y: auto;">
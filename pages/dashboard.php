<?php
$stats = getDashboardStats($conn);
?>
<h2 class="mb-4">Dashboard Perpustakaan</h2>

<div class="row">
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <h5 class="card-title">Total Buku</h5>
                <p class="card-text fs-3"><?php echo $stats['total_buku']; ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h5 class="card-title">Total Anggota</h5>
                <p class="card-text fs-3"><?php echo $stats['total_anggota']; ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <h5 class="card-title">Sedang Dipinjam</h5>
                <p class="card-text fs-3"><?php echo $stats['buku_dipinjam']; ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-white bg-danger">
            <div class="card-body">
                <h5 class="card-title">Keterlambatan</h5>
                <p class="card-text fs-3"><?php echo $stats['terlambat']; ?></p>
            </div>
        </div>
    </div>
</div>
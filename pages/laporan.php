<?php
$tglMulai = isset($_GET['tgl_mulai']) ? $_GET['tgl_mulai'] : '';
$tglSelesai = isset($_GET['tgl_selesai']) ? $_GET['tgl_selesai'] : '';

$laporanData = getLaporan($conn, $tglMulai, $tglSelesai);
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Laporan Rekapitulasi Perpustakaan</h2>
    <button onclick="window.print()" class="btn btn-secondary btn-sm">Cetak Laporan</button>
</div>

<!-- Form Filter Rentang Tanggal -->
<div class="card mb-4">
    <div class="card-body">
        <form action="index.php" method="GET" class="row g-3 align-items-end">
            <input type="hidden" name="page" value="laporan">
            <div class="col-md-4">
                <label class="form-label fw-bold">Dari Tanggal</label>
                <input type="date" name="tgl_mulai" class="form-control" value="<?php echo $tglMulai; ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Sampai Tanggal</label>
                <input type="date" name="tgl_selesai" class="form-control" value="<?php echo $tglSelesai; ?>" required>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100">Filter Laporan</button>
                <?php if (!empty($tglMulai) || !empty($tglSelesai)) : ?>
                    <a href="index.php?page=laporan" class="btn btn-outline-secondary">Reset</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<table class="table table-bordered table-striped align-middle">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nama Anggota</th>
            <th>Judul Buku</th>
            <th>Tgl Pinjam</th>
            <th>Jatuh Tempo</th>
            <th>Tgl Kembali</th>
            <th>Denda</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($laporanData)) : ?>
            <tr>
                <td colspan="8" class="text-center text-muted">Data transaksi tidak ditemukan untuk periode ini.</td>
            </tr>
        <?php else : ?>
            <?php foreach ($laporanData as $l) : ?>
                <tr>
                    <td><?php echo $l['id_pinjam']; ?></td>
                    <td><?php echo htmlspecialchars($l['nama_anggota']); ?></td>
                    <td><?php echo htmlspecialchars($l['judul']); ?></td>
                    <td><?php echo $l['tgl_pinjam']; ?></td>
                    <td><?php echo $l['tgl_jatuh_tempo']; ?></td>
                    <td><?php echo $l['tgl_kembali'] ? $l['tgl_kembali'] : '-'; ?></td>
                    <td>Rp <?php echo number_format($l['denda'], 0, ',', '.'); ?></td>
                    <td>
                        <?php if ($l['status'] == 'Dipinjam') : ?>
                            <span class="badge bg-warning text-dark">Dipinjam</span>
                        <?php else : ?>
                            <span class="badge bg-success">Kembali</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

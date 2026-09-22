<?php
$laporanData = getLaporan($conn);
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Laporan Rekapitulasi Perpustakaan</h2>
    <button onclick="window.print()" class="btn btn-secondary btn-sm">Cetak Laporan</button>
</div>

<table class="table table-bordered table-striped">
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
    </tbody>
</table>
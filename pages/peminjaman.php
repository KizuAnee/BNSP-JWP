<?php
$pesan = "";

if (isset($_POST['proses_pinjam'])) {
    if (empty($_POST['id_anggota']) || empty($_POST['id_buku']) || empty($_POST['tgl_pinjam']) || empty($_POST['tgl_jatuh_tempo'])) {
        $pesan = "<div class='alert alert-danger'>Seluruh formulir peminjaman wajib diisi.</div>";
    } else {
        $hasil = tambahPeminjaman($conn, $_POST);
        if ($hasil === true) {
            $pesan = "<div class='alert alert-success'>Transaksi peminjaman berhasil diproses.</div>";
        } else {
            $pesan = "<div class='alert alert-danger'>" . $hasil . "</div>";
        }
    }
}

$daftarBuku = getAllBuku($conn);
$daftarAnggota = getAllAnggota($conn);
$peminjamanAktif = getPeminjamanAktif($conn);
?>

<h2 class="mb-3">Transaksi Peminjaman Buku</h2>
<?php echo $pesan; ?>

<div class="card mb-4">
    <div class="card-header">Formulir Peminjaman</div>
    <div class="card-body">
        <form action="index.php?page=peminjaman" method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Pilih Anggota</label>
                    <select name="id_anggota" class="form-select" required>
                        <option value="">-- Pilih Anggota --</option>
                        <?php foreach ($daftarAnggota as $a) : ?>
                            <option value="<?php echo $a['id_anggota']; ?>"><?php echo htmlspecialchars($a['nama_anggota']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Pilih Buku</label>
                    <select name="id_buku" class="form-select" required>
                        <option value="">-- Pilih Buku --</option>
                        <?php foreach ($daftarBuku as $b) : ?>
                            <option value="<?php echo $b['id_buku']; ?>"><?php echo htmlspecialchars($b['judul']); ?> (Stok: <?php echo $b['stok']; ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tanggal Pinjam</label>
                    <input type="date" name="tgl_pinjam" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tanggal Jatuh Tempo</label>
                    <input type="date" name="tgl_jatuh_tempo" class="form-control" value="<?php echo date('Y-m-d', strtotime('+7 days')); ?>" required>
                </div>
            </div>
            <button type="submit" name="proses_pinjam" class="btn btn-primary">Proses Peminjaman</button>
        </form>
    </div>
</div>

<h4>Daftar Buku Sedang Dipinjam</h4>
<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>ID Pinjam</th>
            <th>Nama Anggota</th>
            <th>Judul Buku</th>
            <th>Tgl Pinjam</th>
            <th>Tgl Jatuh Tempo</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($peminjamanAktif as $p) : ?>
            <tr>
                <td><?php echo $p['id_pinjam']; ?></td>
                <td><?php echo htmlspecialchars($p['nama_anggota']); ?></td>
                <td><?php echo htmlspecialchars($p['judul']); ?></td>
                <td><?php echo $p['tgl_pinjam']; ?></td>
                <td><?php echo $p['tgl_jatuh_tempo']; ?></td>
                <td><span class="badge bg-warning text-dark"><?php echo $p['status']; ?></span></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
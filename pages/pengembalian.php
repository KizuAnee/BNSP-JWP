<?php
$pesan = "";

if (isset($_POST['proses_kembali'])) {
    if (empty($_POST['id_pinjam']) || empty($_POST['tgl_kembali'])) {
        $pesan = "<div class='alert alert-danger'>Seluruh formulir pengembalian wajib diisi.</div>";
    } else {
        if (prosesPengembalian($conn, $_POST)) {
            $pesan = "<div class='alert alert-success'>Transaksi pengembalian berhasil diproses. Stok buku telah dikembalikan.</div>";
        } else {
            $pesan = "<div class='alert alert-danger'>Gagal memproses pengembalian.</div>";
        }
    }
}

$peminjamanAktif = getPeminjamanAktif($conn);
?>

<h2 class="mb-3">Transaksi Pengembalian Buku</h2>
<?php echo $pesan; ?>

<div class="card mb-4">
    <div class="card-header">Formulir Pengembalian</div>
    <div class="card-body">
        <form action="index.php?page=pengembalian" method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Pilih Transaksi Peminjaman</label>
                    <select name="id_pinjam" class="form-select" required>
                        <option value="">-- Pilih Transaksi --</option>
                        <?php foreach ($peminjamanAktif as $p) : ?>
                            <option value="<?php echo $p['id_pinjam']; ?>">
                                ID: <?php echo $p['id_pinjam']; ?> - <?php echo htmlspecialchars($p['nama_anggota']); ?> (<?php echo htmlspecialchars($p['judul']); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tanggal Pengembalian</label>
                    <input type="date" name="tgl_kembali" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
            </div>
            <p class="text-muted small">* Denda dihitung otomatis Rp 2.000 / hari jika melewati tanggal jatuh tempo.</p>
            <button type="submit" name="proses_kembali" class="btn btn-success">Proses Pengembalian</button>
        </form>
    </div>
</div>
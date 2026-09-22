<?php
$pesan = "";

if (isset($_POST['tambah_buku'])) {
    if (empty($_POST['judul']) || empty($_POST['penulis']) || empty($_POST['penerbit']) || empty($_POST['tahun_terbit']) || !isset($_POST['stok'])) {
        $pesan = "<div class='alert alert-danger'>Semua kolom data buku wajib diisi.</div>";
    } else {
        if (tambahBuku($conn, $_POST)) {
            $pesan = "<div class='alert alert-success'>Data buku berhasil ditambahkan.</div>";
        } else {
            $pesan = "<div class='alert alert-danger'>Gagal menambahkan data buku.</div>";
        }
    }
}

if (isset($_POST['edit_buku'])) {
    if (updateBuku($conn, $_POST)) {
        $pesan = "<div class='alert alert-success'>Data buku berhasil diperbarui.</div>";
    } else {
        $pesan = "<div class='alert alert-danger'>Gagal memperbarui data buku.</div>";
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'hapus') {
    $id = (int)$_GET['id'];
    if (hapusBuku($conn, $id)) {
        echo "<script>window.location.href='index.php?page=buku';</script>";
    }
}

// Tangkap kata kunci pencarian
$keyword = isset($_GET['search']) ? trim($_GET['search']) : '';
$daftarBuku = getAllBuku($conn, $keyword);
?>

<h2 class="mb-3">Kelola Master Buku</h2>
<?php echo $pesan; ?>

<div class="card mb-4">
    <div class="card-header">Tambah Buku Baru</div>
    <div class="card-body">
        <form action="index.php?page=buku" method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Judul Buku</label>
                    <input type="text" name="judul" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Penulis</label>
                    <input type="text" name="penulis" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Penerbit</label>
                    <input type="text" name="penerbit" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Tahun Terbit</label>
                    <input type="number" name="tahun_terbit" class="form-control" min="1900" max="2099" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Stok</label>
                    <input type="number" name="stok" class="form-control" min="0" required>
                </div>
            </div>
            <button type="submit" name="tambah_buku" class="btn btn-primary">Simpan Buku</button>
        </form>
    </div>
</div>

<!-- Form Pencarian Data Buku -->
<form action="index.php" method="GET" class="mb-3">
    <input type="hidden" name="page" value="buku">
    <div class="input-group">
        <input type="text" name="search" class="form-control" placeholder="Cari judul, penulis, atau penerbit..." value="<?php echo htmlspecialchars($keyword); ?>">
        <button class="btn btn-outline-primary" type="submit">Cari Data</button>
        <?php if (!empty($keyword)) : ?>
            <a href="index.php?page=buku" class="btn btn-outline-secondary">Reset Filter</a>
        <?php endif; ?>
    </div>
</form>

<table class="table table-bordered table-striped align-middle">
    <thead>
        <tr>
            <th>ID</th>
            <th>Judul</th>
            <th>Penulis</th>
            <th>Penerbit</th>
            <th>Tahun</th>
            <th>Stok</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($daftarBuku)) : ?>
            <tr>
                <td colspan="7" class="text-center text-muted">Data buku tidak ditemukan.</td>
            </tr>
        <?php else : ?>
            <?php foreach ($daftarBuku as $b) : ?>
                <tr>
                    <td><?php echo $b['id_buku']; ?></td>
                    <td><?php echo htmlspecialchars($b['judul']); ?></td>
                    <td><?php echo htmlspecialchars($b['penulis']); ?></td>
                    <td><?php echo htmlspecialchars($b['penerbit']); ?></td>
                    <td><?php echo $b['tahun_terbit']; ?></td>
                    <td><?php echo $b['stok']; ?></td>
                    <td>
                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditBuku<?php echo $b['id_buku']; ?>">Edit</button>
                        <a href="index.php?page=buku&action=hapus&id=<?php echo $b['id_buku']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?');">Hapus</a>
                    </td>
                </tr>

                <!-- Modal Edit Buku -->
                <div class="modal fade" id="modalEditBuku<?php echo $b['id_buku']; ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="index.php?page=buku" method="POST">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Data Buku</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="id_buku" value="<?php echo $b['id_buku']; ?>">
                                    <div class="mb-3">
                                        <label class="form-label">Judul Buku</label>
                                        <input type="text" name="judul" class="form-control" value="<?php echo htmlspecialchars($b['judul']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Penulis</label>
                                        <input type="text" name="penulis" class="form-control" value="<?php echo htmlspecialchars($b['penulis']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Penerbit</label>
                                        <input type="text" name="penerbit" class="form-control" value="<?php echo htmlspecialchars($b['penerbit']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Tahun Terbit</label>
                                        <input type="number" name="tahun_terbit" class="form-control" value="<?php echo $b['tahun_terbit']; ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Stok</label>
                                        <input type="number" name="stok" class="form-control" value="<?php echo $b['stok']; ?>" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" name="edit_buku" class="btn btn-primary">Simpan Perubahan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

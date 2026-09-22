<?php
$pesan = "";

if (isset($_POST['tambah_anggota'])) {
    if (empty($_POST['nama_anggota']) || empty($_POST['telepon']) || empty($_POST['alamat'])) {
        $pesan = "<div class='alert alert-danger'>Semua kolom data anggota wajib diisi.</div>";
    } else {
        if (tambahAnggota($conn, $_POST)) {
            $pesan = "<div class='alert alert-success'>Data anggota berhasil ditambahkan.</div>";
        } else {
            $pesan = "<div class='alert alert-danger'>Gagal menambahkan data anggota.</div>";
        }
    }
}

if (isset($_POST['edit_anggota'])) {
    if (updateAnggota($conn, $_POST)) {
        $pesan = "<div class='alert alert-success'>Data anggota berhasil diperbarui.</div>";
    } else {
        $pesan = "<div class='alert alert-danger'>Gagal memperbarui data anggota.</div>";
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'hapus') {
    $id = (int)$_GET['id'];
    if (hapusAnggota($conn, $id)) {
        echo "<script>window.location.href='index.php?page=anggota';</script>";
    }
}

$daftarAnggota = getAllAnggota($conn);
?>

<h2 class="mb-3">Kelola Master Anggota</h2>
<?php echo $pesan; ?>

<div class="card mb-4">
    <div class="card-header">Tambah Anggota Baru</div>
    <div class="card-body">
        <form action="index.php?page=anggota" method="POST">
            <div class="mb-3">
                <label class="form-label">Nama Anggota</label>
                <input type="text" name="nama_anggota" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">No. Telepon</label>
                <input type="text" name="telepon" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Alamat</label>
                <textarea name="alamat" class="form-control" rows="2" required></textarea>
            </div>
            <button type="submit" name="tambah_anggota" class="btn btn-primary">Simpan Anggota</button>
        </form>
    </div>
</div>

<table class="table table-bordered table-striped align-middle">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nama Anggota</th>
            <th>Telepon</th>
            <th>Alamat</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($daftarAnggota as $a) : ?>
            <tr>
                <td><?php echo $a['id_anggota']; ?></td>
                <td><?php echo htmlspecialchars($a['nama_anggota']); ?></td>
                <td><?php echo htmlspecialchars($a['telepon']); ?></td>
                <td><?php echo htmlspecialchars($a['alamat']); ?></td>
                <td>
                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditAnggota<?php echo $a['id_anggota']; ?>">Edit</button>
                    <a href="index.php?page=anggota&action=hapus&id=<?php echo $a['id_anggota']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?');">Hapus</a>
                </td>
            </tr>

            <!-- Modal Edit Anggota -->
            <div class="modal fade" id="modalEditAnggota<?php echo $a['id_anggota']; ?>" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="index.php?page=anggota" method="POST">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Data Anggota</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="id_anggota" value="<?php echo $a['id_anggota']; ?>">
                                <div class="mb-3">
                                    <label class="form-label">Nama Anggota</label>
                                    <input type="text" name="nama_anggota" class="form-control" value="<?php echo htmlspecialchars($a['nama_anggota']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">No. Telepon</label>
                                    <input type="text" name="telepon" class="form-control" value="<?php echo htmlspecialchars($a['telepon']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Alamat</label>
                                    <textarea name="alamat" class="form-control" rows="2" required><?php echo htmlspecialchars($a['alamat']); ?></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" name="edit_anggota" class="btn btn-primary">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </tbody>
</table>
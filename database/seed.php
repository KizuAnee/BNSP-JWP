<?php
require_once "../config/koneksi.php";

echo "<h2>Memulai Proses Seeding Database...</h2>";

// Matikan sementara pengecekan Foreign Key agar TRUNCATE tidak error
$conn->query("SET FOREIGN_KEY_CHECKS = 0");

// 1. Kosongkan semua tabel
$tables = ['pengembalian', 'peminjaman', 'anggota', 'buku'];
foreach ($tables as $table) {
    $conn->query("TRUNCATE TABLE $table");
    echo "Tabel <strong>$table</strong> berhasil dikosongkan.<br>";
}

$conn->query("SET FOREIGN_KEY_CHECKS = 1");

// 2. Seed Data Buku (Minimal 5 data untuk syarat sertifikasi)
$bukuSql = "INSERT INTO buku (id_buku, judul, penulis, penerbit, tahun_terbit, stok) VALUES
(1, 'Laskar Pelangi', 'Andrea Hirata', 'Bentang Pustaka', 2005, 5),
(2, 'Bumi Manusia', 'Pramoedya Ananta Toer', 'Hasta Mitra', 1980, 3),
(3, 'Filosfi Teras', 'Henry Manampiring', 'Kompas', 2018, 7),
(4, 'Pemrograman Web dengan PHP', 'Budi Raharjo', 'Informatika', 2022, 10),
(5, 'Clean Code', 'Robert C. Martin', 'Prentice Hall', 2008, 2)";

if ($conn->query($bukuSql)) {
    echo "Data master <strong>Buku</strong> berhasil diisi (5 data).<br>";
}

// 3. Seed Data Anggota (Minimal 5 data)
$anggotaSql = "INSERT INTO anggota (id_anggota, nama_anggota, telepon, alamat) VALUES
(1, 'Ahmad Fauzi', '081234567890', 'Jl. Ahmad Yani No. 12, Banjarmasin'),
(2, 'Siti Rahmah', '085298765432', 'Jl. Veteran No. 45, Banjarbaru'),
(3, 'Budi Santoso', '087811223344', 'Jl. Hasan Basri No. 88, Banjarmasin'),
(4, 'Dewi Lestari', '081399887766', 'Jl. Martapura Lama No. 3'),
(5, 'Eko Prasetyo', '082155443322', 'Jl. Lambung Mangkurat No. 17')";

if ($conn->query($anggotaSql)) {
    echo "Data master <strong>Anggota</strong> berhasil diisi (5 data).<br>";
}

// 4. Seed Data Peminjaman Awal (Untuk bahan uji coba transaksi)
$tglPinjam = date('Y-m-d', strtotime('-5 days'));
$tglJatuhTempo = date('Y-m-d', strtotime('+2 days'));

$pinjamSql = "INSERT INTO peminjaman (id_pinjam, id_anggota, id_buku, tgl_pinjam, tgl_jatuh_tempo, status) VALUES
(1, 1, 1, '$tglPinjam', '$tglJatuhTempo', 'Dipinjam')";

if ($conn->query($pinjamSql)) {
    // Kurangi stok buku ID 1
    $conn->query("UPDATE buku SET stok = stok - 1 WHERE id_buku = 1");
    echo "Data transaksi <strong>Peminjaman</strong> berhasil diisi.<br>";
}

echo "<br><h3 style='color:green;'>Seeding Selesai! Database siap digunakan untuk testing.</h3>";
echo "<a href='../index.php?page=dashboard'>Kembali ke Dashboard</a>";
?>
<?php
require_once "config/koneksi.php";

/**
 * Mengambil ringkasan data statistik untuk Dashboard
 * @param mysqli $conn
 * @return array
 */
function getDashboardStats($conn) {
    $stats = [];
    
    $resBuku = $conn->query("SELECT COUNT(*) as total FROM buku");
    $stats['total_buku'] = $resBuku->fetch_assoc()['total'];

    $resAnggota = $conn->query("SELECT COUNT(*) as total FROM anggota");
    $stats['total_anggota'] = $resAnggota->fetch_assoc()['total'];

    $resPinjam = $conn->query("SELECT COUNT(*) as total FROM peminjaman WHERE status = 'Dipinjam'");
    $stats['buku_dipinjam'] = $resPinjam->fetch_assoc()['total'];

    $today = date('Y-m-d');
    $resTerlambat = $conn->query("SELECT COUNT(*) as total FROM peminjaman WHERE status = 'Dipinjam' AND tgl_jatuh_tempo < '$today'");
    $stats['terlambat'] = $resTerlambat->fetch_assoc()['total'];

    return $stats;
}

/**
 * Menampilkan daftar seluruh buku dengan fitur pencarian kata kunci
 * @param mysqli $conn
 * @param string $keyword
 * @return array
 */
function getAllBuku($conn, $keyword = "") {
    if (!empty($keyword)) {
        $param = "%" . $keyword . "%";
        $stmt = $conn->prepare("SELECT * FROM buku WHERE judul LIKE ? OR penulis LIKE ? OR penerbit LIKE ? ORDER BY id_buku DESC");
        $stmt->bind_param("sss", $param, $param, $param);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $conn->query("SELECT * FROM buku ORDER BY id_buku DESC");
    }
    
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

/**
 * Menambahkan buku baru menggunakan Prepared Statement
 * @param mysqli $conn
 * @param array $data
 * @return bool
 */
function tambahBuku($conn, $data) {
    $stmt = $conn->prepare("INSERT INTO buku (judul, penulis, penerbit, tahun_terbit, stok) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssii", $data['judul'], $data['penulis'], $data['penerbit'], $data['tahun_terbit'], $data['stok']);
    return $stmt->execute();
}

/**
 * Menghapus data buku berdasarkan ID
 * @param mysqli $conn
 * @param int $id
 * @return bool
 */
function hapusBuku($conn, $id) {
    $stmt = $conn->prepare("DELETE FROM buku WHERE id_buku = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

/**
 * Menampilkan daftar seluruh anggota
 * @param mysqli $conn
 * @return array
 */
function getAllAnggota($conn) {
    $result = $conn->query("SELECT * FROM anggota ORDER BY id_anggota DESC");
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

/**
 * Menambahkan data anggota baru
 * @param mysqli $conn
 * @param array $data
 * @return bool
 */
function tambahAnggota($conn, $data) {
    $stmt = $conn->prepare("INSERT INTO anggota (nama_anggota, telepon, alamat) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $data['nama_anggota'], $data['telepon'], $data['alamat']);
    return $stmt->execute();
}

/**
 * Menghapus data anggota
 * @param mysqli $conn
 * @param int $id
 * @return bool
 */
function hapusAnggota($conn, $id) {
    $stmt = $conn->prepare("DELETE FROM anggota WHERE id_anggota = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

/**
 * Memproses transaksi peminjaman buku dan mengoperasikan pengurangan stok
 * @param mysqli $conn
 * @param array $data
 * @return bool|string
 */
function tambahPeminjaman($conn, $data) {
    $idBuku = (int)$data['id_buku'];
    $idAnggota = (int)$data['id_anggota'];
    $tglPinjam = $data['tgl_pinjam'];
    $tglJatuhTempo = $data['tgl_jatuh_tempo'];

    $checkStok = $conn->query("SELECT stok FROM buku WHERE id_buku = $idBuku");
    $buku = $checkStok->fetch_assoc();

    if (!$buku || $buku['stok'] < 1) {
        return "Stok buku tidak mencukupi untuk dipinjam.";
    }

    $stmt = $conn->prepare("INSERT INTO peminjaman (id_anggota, id_buku, tgl_pinjam, tgl_jatuh_tempo, status) VALUES (?, ?, ?, ?, 'Dipinjam')");
    $stmt->bind_param("iiss", $idAnggota, $idBuku, $tglPinjam, $tglJatuhTempo);

    if ($stmt->execute()) {
        $conn->query("UPDATE buku SET stok = stok - 1 WHERE id_buku = $idBuku");
        return true;
    }
    return "Gagal memproses transaksi peminjaman.";
}

/**
 * Mengambil daftar peminjaman aktif
 * @param mysqli $conn
 * @return array
 */
function getPeminjamanAktif($conn) {
    $sql = "SELECT p.*, a.nama_anggota, b.judul 
            FROM peminjaman p 
            JOIN anggota a ON p.id_anggota = a.id_anggota 
            JOIN buku b ON p.id_buku = b.id_buku 
            WHERE p.status = 'Dipinjam'
            ORDER BY p.id_pinjam DESC";
    $result = $conn->query($sql);
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

/**
 * Memproses pengembalian buku, menghitung denda, serta mengembalikan stok buku
 * @param mysqli $conn
 * @param array $data
 * @return bool
 */
function prosesPengembalian($conn, $data) {
    $idPinjam = (int)$data['id_pinjam'];
    $tglKembali = $data['tgl_kembali'];

    $resPinjam = $conn->query("SELECT id_buku, tgl_jatuh_tempo FROM peminjaman WHERE id_pinjam = $idPinjam");
    $pinjam = $resPinjam->fetch_assoc();
    
    if (!$pinjam) {
        return false;
    }

    $idBuku = $pinjam['id_buku'];
    $tglJatuhTempo = new DateTime($pinjam['tgl_jatuh_tempo']);
    $tglKembaliObj = new DateTime($tglKembali);

    $denda = 0;
    if ($tglKembaliObj > $tglJatuhTempo) {
        $selisih = $tglJatuhTempo->diff($tglKembaliObj)->days;
        $denda = $selisih * 2000;
    }

    $stmt = $conn->prepare("INSERT INTO pengembalian (id_pinjam, tgl_kembali, denda) VALUES (?, ?, ?)");
    $stmt->bind_param("isi", $idPinjam, $tglKembali, $denda);

    if ($stmt->execute()) {
        $conn->query("UPDATE peminjaman SET status = 'Kembali' WHERE id_pinjam = $idPinjam");
        $conn->query("UPDATE buku SET stok = stok + 1 WHERE id_buku = $idBuku");
        return true;
    }
    return false;
}

/**
 * Mengambil laporan rekapitulasi transaksi dengan filter rentang tanggal
 * @param mysqli $conn
 * @param string $tglMulai
 * @param string $tglSelesai
 * @return array
 */
function getLaporan($conn, $tglMulai = "", $tglSelesai = "") {
    $sql = "SELECT p.id_pinjam, a.nama_anggota, b.judul, p.tgl_pinjam, p.tgl_jatuh_tempo, p.status, 
                   k.tgl_kembali, IFNULL(k.denda, 0) AS denda
            FROM peminjaman p
            JOIN anggota a ON p.id_anggota = a.id_anggota
            JOIN buku b ON p.id_buku = b.id_buku
            LEFT JOIN pengembalian k ON p.id_pinjam = k.id_pinjam";

    if (!empty($tglMulai) && !empty($tglSelesai)) {
        $sql .= " WHERE p.tgl_pinjam BETWEEN '$tglMulai' AND '$tglSelesai'";
    }

    $sql .= " ORDER BY p.id_pinjam DESC";
    $result = $conn->query($sql);
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

/**
 * Memperbarui data buku
 */
function updateBuku($conn, $data) {
    $stmt = $conn->prepare("UPDATE buku SET judul = ?, penulis = ?, penerbit = ?, tahun_terbit = ?, stok = ? WHERE id_buku = ?");
    $stmt->bind_param("sssiii", $data['judul'], $data['penulis'], $data['penerbit'], $data['tahun_terbit'], $data['stok'], $data['id_buku']);
    return $stmt->execute();
}

/**
 * Memperbarui data anggota
 */
function updateAnggota($conn, $data) {
    $stmt = $conn->prepare("UPDATE anggota SET nama_anggota = ?, telepon = ?, alamat = ? WHERE id_anggota = ?");
    $stmt->bind_param("sssi", $data['nama_anggota'], $data['telepon'], $data['alamat'], $data['id_anggota']);
    return $stmt->execute();
}
?>

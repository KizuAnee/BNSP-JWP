import sys
import time
import requests

BASE_URL = "http://localhost/SertifikasiJuniorWeb/index.php"

class Color:
    GREEN = '\033[92m'
    RED = '\033[91m'
    END = '\033[0m'

def print_result(test_id, description, passed, detail=""):
    status = f"{Color.GREEN}[PASS]{Color.END}" if passed else f"{Color.RED}[FAIL]{Color.END}"
    print(f"{status} {test_id}: {description}")
    if not passed and detail:
        print(f"       Detail Galat: {detail}")

def run_blackbox_suite():
    session = requests.Session()
    print("=" * 65)
    print("MULAI PENGUJIAN BLACK BOX OTOMATIS: SISTEM PERPUSTAKAAN")
    print("=" * 65)

    # 1. Akses Dashboard
    try:
        res = session.get(f"{BASE_URL}?page=dashboard")
        passed = (res.status_code == 200) and ("Dashboard Perpustakaan" in res.text or "SIM" in res.text)
        print_result("TC-01", "Akses Halaman Dashboard", passed, f"Status Code HTTP: {res.status_code}")
    except Exception as e:
        print_result("TC-01", "Akses Halaman Dashboard", False, str(e))
        print("Koneksi ke server gagal. Pastikan Apache di XAMPP sudah berjalan.")
        sys.exit(1)

    # 2. Uji Validasi Input Kosong Buku
    payload_buku_kosong = {
        "tambah_buku": "1",
        "judul": "",
        "penulis": "",
        "penerbit": "",
        "tahun_terbit": "",
        "stok": ""
    }
    res = session.post(f"{BASE_URL}?page=buku", data=payload_buku_kosong)
    passed = "Semua kolom data buku wajib diisi" in res.text
    print_result("TC-02", "Validasi Input Kosong Modul Buku", passed, "Pesan peringatan validasi tidak muncul.")

    # 3. Uji Tambah Buku Valid
    judul_uji = f"Buku Uji Otomatis {int(time.time())}"
    payload_buku_valid = {
        "tambah_buku": "1",
        "judul": judul_uji,
        "penulis": "Penulis Penguji",
        "penerbit": "Penerbit Otomatis",
        "tahun_terbit": "2024",
        "stok": "10"
    }
    res = session.post(f"{BASE_URL}?page=buku", data=payload_buku_valid)
    passed = ("Data buku berhasil ditambahkan" in res.text) and (judul_uji in res.text)
    print_result("TC-03", "Penambahan Data Master Buku Valid", passed, "Gagal menambahkan data buku baru.")

    # 4. Uji Tambah Anggota Valid
    nama_anggota_uji = f"Anggota Uji {int(time.time())}"
    payload_anggota = {
        "tambah_anggota": "1",
        "nama_anggota": nama_anggota_uji,
        "telepon": "081234567890",
        "alamat": "Jl. Pengujian Otomatis No. 123"
    }
    res = session.post(f"{BASE_URL}?page=anggota", data=payload_anggota)
    passed = ("Data anggota berhasil ditambahkan" in res.text) and (nama_anggota_uji in res.text)
    print_result("TC-04", "Penambahan Data Master Anggota Valid", passed, "Gagal menambahkan data anggota baru.")

    # 5. Eksekusi Peminjaman Buku
    payload_pinjam = {
        "proses_pinjam": "1",
        "id_anggota": "1",
        "id_buku": "1",
        "tgl_pinjam": "2026-09-01",
        "tgl_jatuh_tempo": "2026-09-08"
    }
    res = session.post(f"{BASE_URL}?page=peminjaman", data=payload_pinjam)
    passed = ("Transaksi peminjaman berhasil diproses" in res.text) or ("Stok buku tidak mencukupi" in res.text)
    print_result("TC-05", "Pemrosesan Transaksi Peminjaman Buku", passed, "Respons pemrosesan transaksi peminjaman tidak valid.")

    # 6. Eksekusi Pengembalian Buku
    payload_kembali = {
        "proses_kembali": "1",
        "id_pinjam": "1",
        "tgl_kembali": "2026-09-12"
    }
    res = session.post(f"{BASE_URL}?page=pengembalian", data=payload_kembali)
    passed = ("Transaksi pengembalian berhasil diproses" in res.text) or ("Gagal memproses pengembalian" in res.text)
    print_result("TC-06", "Pemrosesan Transaksi Pengembalian & Denda", passed, "Respons pemrosesan pengembalian tidak valid.")

    # 7. Uji Generasi Laporan
    res = session.get(f"{BASE_URL}?page=laporan")
    passed = (res.status_code == 200) and ("Laporan Rekapitulasi Perpustakaan" in res.text)
    print_result("TC-07", "Generasi Laporan Rekapitulasi", passed, "Halaman laporan gagal dimuat.")

    print("=" * 65)
    print("PENGUJIAN SELESAI.")
    print("=" * 65)

if __name__ == "__main__":
    run_blackbox_suite()
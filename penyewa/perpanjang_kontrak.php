<?php 
session_start();
include "../koneksi.php";

if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'penyewa') {
    header("Location: ../index.php");
    exit;
}

$id_user = $_SESSION['id_user'];
$id_pesanan = isset($_GET['id_pesanan']) ? intval($_GET['id_pesanan']) : 0;

// Ambil data pesanan lama
$query = mysqli_query($koneksi, "SELECT p.*, k.nama_kamar FROM tbl_pesanan p 
                                 JOIN tbl_kamar k ON p.id_kamar = k.id_kamar 
                                 WHERE p.id_pesanan = '$id_pesanan' AND p.id_user = '$id_user'");
$pesanan = mysqli_fetch_assoc($query);

if (!$pesanan) {
    echo "<script>alert('Data tidak ditemukan'); window.location='status_pesanan.php';</script>";
    exit;
}

// Proses Perpanjangan
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tambahan_bulan = intval($_POST['tambahan_bulan']);

    if ($tambahan_bulan <= 0) {
        echo "<script>alert('Durasi harus lebih dari 0 bulan');</script>";
    } else {
        // Hitung tanggal baru
        $tanggal_masuk_baru = $pesanan['tanggal_berakhir'];
        $tanggal_berakhir_baru = date('Y-m-d', strtotime("+$tambahan_bulan months", strtotime($tanggal_masuk_baru)));
        $tanggal_pesan = date('Y-m-d');

        // Simpan pesanan baru
        $insert = mysqli_query($koneksi, "INSERT INTO tbl_pesanan (id_user, id_kamar, tanggal_pesan, tanggal_masuk, tanggal_berakhir, durasi_bulan, status) 
                                          VALUES ('$id_user', '{$pesanan['id_kamar']}', '$tanggal_pesan', '$tanggal_masuk_baru', '$tanggal_berakhir_baru', '$tambahan_bulan', 'menunggu')");

        if ($insert) {
            echo "<script>alert('Perpanjangan berhasil diajukan. Menunggu persetujuan admin.'); window.location='status_pesanan.php';</script>";
        } else {
            echo "Gagal insert: " . mysqli_error($koneksi);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Perpanjang Kontrak</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5>Perpanjang Kontrak Kamar: <?= htmlspecialchars($pesanan['nama_kamar']); ?></h5>
        </div>
        <div class="card-body">
            <p><strong>Tanggal Berakhir Saat Ini:</strong> <?= date('d-m-Y', strtotime($pesanan['tanggal_berakhir'])); ?></p>

            <form method="post">
                <div class="mb-3">
                    <label for="tambahan_bulan" class="form-label">Tambah Durasi (bulan)</label>
                    <select name="tambahan_bulan" id="tambahan_bulan" class="form-select" required>
                        <?php for ($i = 1; $i <= 12; $i++): ?>
                            <option value="<?= $i; ?>"><?= $i; ?> bulan</option>
                        <?php endfor; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-success">Ajukan Perpanjangan</button>
                <a href="status_pesanan.php" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>

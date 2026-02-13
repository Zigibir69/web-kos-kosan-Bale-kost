<?php
session_start();
include "../koneksi.php";

// Cek login dan role
if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'penyewa') {
    header("Location: ../index.php");
    exit;
}

$id_user = $_SESSION['id_user'];

// Validasi id_kamar dari URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>alert('ID kamar tidak valid!'); window.location='dashboard.php';</script>";
    exit;
}

$id_kamar = intval($_GET['id']);

// Ambil data kamar
$queryKamar = $koneksi->prepare("SELECT * FROM tbl_kamar WHERE id_kamar = ?");
$queryKamar->bind_param("i", $id_kamar);
$queryKamar->execute();
$resultKamar = $queryKamar->get_result();

if ($resultKamar->num_rows === 0) {
    echo "<script>alert('Kamar tidak ditemukan!'); window.location='dashboard.php';</script>";
    exit;
}

$kamar = $resultKamar->fetch_assoc();

// Jika status kamar bukan tersedia, tolak pemesanan
if ($kamar['status'] != 'tersedia') {
    echo "<script>alert('Kamar tidak tersedia saat ini!'); window.location='dashboard.php';</script>";
    exit;
}

// Handle submit form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $durasi = intval($_POST['durasi_bulan']);
    $tanggal_pesan = date('Y-m-d');
    $status = 'menunggu';

    // Simpan pesanan
    $stmt = $koneksi->prepare("INSERT INTO tbl_pesanan (id_user, id_kamar, tanggal_pesan, durasi_bulan, status) VALUES (?, ?, ?, ?, ?)");

    $stmt->bind_param("iisis", $id_user, $id_kamar, $tanggal_pesan, $durasi, $status);

    if ($stmt->execute()) {
        echo "<script>alert('Pesanan berhasil dikirim!'); window.location='status_pesanan.php';</script>";
    } else {
        echo "<script>alert('Gagal memesan kamar.'); window.history.back();</script>";
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Pesan Kamar - <?= htmlspecialchars($kamar['nama_kamar']); ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container mt-5">
    <div class="card shadow-sm">
      <div class="card-header bg-primary text-white">
        <h4>Form Pemesanan Kamar</h4>
      </div>
      <div class="card-body">
        <h5 class="mb-3"><?= htmlspecialchars($kamar['nama_kamar']); ?></h5>
        <p><strong>Harga:</strong> Rp<?= number_format($kamar['harga'], 0, ',', '.'); ?><?= $kamar['satuan'] ? '/' . $kamar['satuan'] : ''; ?></p>
        <p><strong>Status:</strong> <span class="badge <?= $kamar['status'] == 'tersedia' ? 'bg-success' : 'bg-danger'; ?>"><?= ucfirst($kamar['status']); ?></span></p>
        <hr>
        <form method="post">
          <div class="mb-3">
            <label for="durasi_bulan" class="form-label">Durasi Sewa (bulan)</label>
            <input type="number" name="durasi_bulan" id="durasi_bulan" class="form-control" min="1" max="12" required>
          </div>
          <button type="submit" class="btn btn-success">Kirim Pesanan</button>
          <a href="dashboard_penyewa.php" class="btn btn-secondary">Batal</a>
        </form>
      </div>
    </div>
  </div>
</body>
</html>

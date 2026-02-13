<?php  
session_start();
include "../koneksi.php";

// Cek apakah admin sudah login
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

// Fungsi badge status
function getBadgeStatus($status) {
    switch ($status) {
        case 'disetujui': return '<span class="badge bg-success">Disetujui</span>';
        case 'ditolak': return '<span class="badge bg-danger">Ditolak</span>';
        case 'bayar': return '<span class="badge bg-info text-dark">Bayar</span>';
        default: return '<span class="badge bg-warning text-dark">Menunggu</span>';
    }
}

// Aksi status pesanan
if (isset($_GET['aksi']) && isset($_GET['id'])) {
    $id_pesanan = intval($_GET['id']);
    $aksi = $_GET['aksi'];

    $aksi_valid = ['konfirmasi', 'tolak', 'setujui', 'batal'];
    if (!in_array($aksi, $aksi_valid)) {
        echo "<script>alert('Aksi tidak valid!'); window.location='admin_pesanan.php';</script>";
        exit;
    }

    $cek = $koneksi->query("SELECT * FROM tbl_pesanan WHERE id_pesanan = $id_pesanan");

    if ($cek->num_rows > 0) {
        $pesanan = $cek->fetch_assoc();

        switch ($aksi) {
            case 'konfirmasi':
                $koneksi->query("UPDATE tbl_pesanan SET status = 'bayar' WHERE id_pesanan = $id_pesanan");
                echo "<script>alert('Pesanan dikonfirmasi, menunggu pembayaran.'); window.location='admin_pesanan.php';</script>";
                break;

            case 'tolak':
                $koneksi->query("UPDATE tbl_pesanan SET status = 'ditolak', tanggal_masuk = NULL, tanggal_berakhir = NULL WHERE id_pesanan = $id_pesanan");
                echo "<script>alert('Pesanan ditolak.'); window.location='admin_pesanan.php';</script>";
                break;

            case 'setujui':
                if ($pesanan['status'] !== 'bayar') {
                    echo "<script>alert('Pesanan belum dibayar! Tidak bisa disetujui.'); window.location='admin_pesanan.php';</script>";
                    exit;
                }
                $tanggal_masuk = date('Y-m-d');
                $durasi = $pesanan['durasi_bulan'];
                $tanggal_berakhir = date('Y-m-d', strtotime("+$durasi months", strtotime($tanggal_masuk)));
                $koneksi->query("UPDATE tbl_pesanan 
                    SET status = 'disetujui', tanggal_masuk = '$tanggal_masuk', tanggal_berakhir = '$tanggal_berakhir' 
                    WHERE id_pesanan = $id_pesanan");
                $koneksi->query("UPDATE tbl_kamar SET status = 'tidak tersedia' WHERE id_kamar = " . $pesanan['id_kamar']);
                echo "<script>alert('Pesanan disetujui, tanggal otomatis terisi!'); window.location='admin_pesanan.php';</script>";
                break;

            case 'batal':
                $koneksi->query("UPDATE tbl_pesanan SET status = 'menunggu', tanggal_masuk = NULL, tanggal_berakhir = NULL WHERE id_pesanan = $id_pesanan");
                echo "<script>alert('Status dikembalikan ke menunggu.'); window.location='admin_pesanan.php';</script>";
                break;
        }
    }
}

// Ambil semua data pesanan
$sql = "SELECT p.*, u.nama AS nama_user, k.nama_kamar 
        FROM tbl_pesanan p 
        JOIN tbl_user u ON p.id_user = u.id_user
        JOIN tbl_kamar k ON p.id_kamar = k.id_kamar
        ORDER BY p.id_pesanan DESC";
$result = $koneksi->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin - Kelola Pesanan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar {
            height: 100vh; background-color: #343a40; padding-top: 20px;
            position: fixed; width: 250px; color: white;
        }
        .sidebar a {
            color: #ddd; display: block; padding: 12px 20px; text-decoration: none;
        }
        .sidebar a:hover, .sidebar a.active {
            background-color: #495057; color: white;
        }
        .main-content {
            margin-left: 250px; padding: 30px;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <div class="text-center mb-4">
        <h4><i class="fa fa-user-shield me-2"></i>Admin</h4>
    </div>
    <a href="admin_dashboard.php"><i class="fa fa-chart-line me-2"></i>Dashboard</a>
    <a href="kelola_kos.php"><i class="fa fa-bed me-2"></i>Kelola Kamar</a>
    <a href="admin_pesanan.php" class="active"><i class="fa fa-clipboard-check me-2"></i>Kelola Pesanan</a>
    <a href="kelola_penyewa.php"><i class="fa fa-users me-2"></i>Kelola Penyewa</a>
    <a href="laporan.php"><i class="fa fa-file-alt me-2"></i>Laporan</a>
    <a href="../logout.php" onclick="return confirm('Yakin ingin logout?')"><i class="fa fa-sign-out-alt me-2"></i>Logout</a>
</div>

<!-- Main Content -->
<div class="main-content">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="alert alert-secondary mb-0">
                <strong><?= date('d M Y'); ?></strong>
            </div>
            <a href="rekening.php" class="btn btn-success">
                <i class="fa fa-plus"></i> Tambah Rekening
            </a>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header bg-dark text-white">
                <h4>Daftar Pesanan</h4>
            </div>
            <div class="card-body">
                <?php if ($result->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark text-center">
                                <tr>
                                    <th>No</th>
                                    <th>Penyewa</th>
                                    <th>Kamar</th>
                                    <th>Durasi</th>
                                    <th>Tanggal Masuk</th>
                                    <th>Tanggal Berakhir</th>
                                    <th>Status</th>
                                    <th>Bukti</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $no++; ?></td>
                                        <td><?= htmlspecialchars($row['nama_user']); ?></td>
                                        <td><?= htmlspecialchars($row['nama_kamar']); ?></td>
                                        <td><?= htmlspecialchars($row['durasi_bulan']); ?> bulan</td>
                                        <td><?= $row['tanggal_masuk'] ?? '-'; ?></td>
                                        <td><?= $row['tanggal_berakhir'] ?? '-'; ?></td>
                                        <td><?= getBadgeStatus($row['status']); ?></td>
                                        <td>
                                            <?php if (!empty($row['bukti_pembayaran'])): ?>
                                                <button class="btn btn-sm btn-info"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modalGambar"
                                                        data-img="../uploads/<?= htmlspecialchars($row['bukti_pembayaran']); ?>">
                                                    Lihat
                                                </button>
                                            <?php else: ?>
                                                <em>Belum bayar</em>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($row['status'] == 'menunggu'): ?>
                                                <a href="?aksi=konfirmasi&id=<?= $row['id_pesanan']; ?>" class="btn btn-primary btn-sm" onclick="return confirm('Konfirmasi pesanan ini?')">Konfirmasi</a>
                                                <a href="?aksi=tolak&id=<?= $row['id_pesanan']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tolak pesanan ini?')">Tolak</a>
                                            <?php elseif ($row['status'] == 'bayar'): ?>
                                                <a href="?aksi=setujui&id=<?= $row['id_pesanan']; ?>" class="btn btn-success btn-sm" onclick="return confirm('Setujui pesanan ini?')">Setujui</a>
                                                <a href="?aksi=batal&id=<?= $row['id_pesanan']; ?>" class="btn btn-warning btn-sm" onclick="return confirm('Kembalikan ke menunggu?')">Batal</a>
                                            <?php elseif ($row['status'] == 'disetujui'): ?>
                                                <span class="badge bg-success">Selesai</span>
                                            <?php elseif ($row['status'] == 'ditolak'): ?>
                                                <span class="badge bg-danger">Ditolak</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">Belum ada pesanan.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal Gambar -->
<div class="modal fade" id="modalGambar" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-dark text-white">
        <h5 class="modal-title" id="modalLabel">Bukti Pembayaran</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body text-center">
        <img id="previewGambar" src="" alt="Bukti Pembayaran" class="img-fluid rounded">
      </div>
    </div>
  </div>
</div>

<!-- Script Bootstrap + Modal Preview Gambar -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const modalGambar = document.getElementById('modalGambar');
    modalGambar.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const imageUrl = button.getAttribute('data-img');
        const imgPreview = modalGambar.querySelector('#previewGambar');
        imgPreview.src = imageUrl;
    });
</script>

</body>
</html>

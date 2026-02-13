<?php  
session_start();
include "../koneksi.php";

// Cek apakah admin sudah login
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

// Filter bulan dan tahun
$bulan = isset($_GET['bulan']) && is_numeric($_GET['bulan']) ? str_pad($_GET['bulan'], 2, '0', STR_PAD_LEFT) : date('m');
$tahun = isset($_GET['tahun']) && is_numeric($_GET['tahun']) ? $_GET['tahun'] : date('Y');

// Ambil data laporan berdasarkan bulan dan tahun
$query = "
SELECT u.nama AS username, u.nama_lengkap, u.no_telepon, u.alamat, k.nama_kamar, 
       p.tanggal_masuk, p.tanggal_berakhir,
       (k.harga * p.durasi_bulan) AS jumlah_bayar,
       p.durasi_bulan,
       (k.harga * p.durasi_bulan) AS total
FROM tbl_pesanan p
JOIN tbl_user u ON p.id_user = u.id_user
JOIN tbl_kamar k ON p.id_kamar = k.id_kamar
WHERE MONTH(p.tanggal_masuk) = ? AND YEAR(p.tanggal_masuk) = ? AND p.status = 'disetujui'
";

$stmt = $koneksi->prepare($query);
$stmt->bind_param("ss", $bulan, $tahun);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penyewa - Bale Kost</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f8f9fa; }
        .sidebar {
            height: 100vh; background-color: #343a40; padding-top: 20px; position: fixed;
            width: 250px; color: white;
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
    <a href="admin_pesanan.php"><i class="fa fa-clipboard-check me-2"></i>Kelola Pesanan</a>
    <a href="kelola_penyewa.php"><i class="fa fa-users me-2"></i>Kelola Penyewa</a>
    <a href="laporan.php" class="active"><i class="fa fa-file-alt me-2"></i>Laporan</a>
    <a href="../logout.php" onclick="return confirm('Yakin ingin logout?')"><i class="fa fa-sign-out-alt me-2"></i>Logout</a>
</div>

<!-- Main Content -->
<div class="main-content">
    <div class="container-fluid">
        <h2 class="mb-4">📊 Laporan Penyewa</h2>

        <!-- Filter -->
        <form method="get" class="row g-3 mb-4">
            <div class="col-md-3">
                <label for="bulan" class="form-label">Filter Bulan</label>
                <select name="bulan" id="bulan" class="form-select" required>
                    <?php for ($i = 1; $i <= 12; $i++): 
                        $value = str_pad($i, 2, '0', STR_PAD_LEFT); ?>
                        <option value="<?= $value ?>" <?= $value == $bulan ? 'selected' : '' ?>>
                            <?= date("F", mktime(0, 0, 0, $i, 10)) ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label for="tahun" class="form-label">Filter Tahun</label>
                <select name="tahun" id="tahun" class="form-select" required>
                    <?php
                    $currentYear = date('Y');
                    for ($y = $currentYear; $y >= $currentYear - 10; $y--):
                    ?>
                        <option value="<?= $y ?>" <?= $tahun == $y ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <div class="col-md-4 align-self-end">
                <button type="submit" class="btn btn-primary">🔍 Tampilkan</button>
                <a href="cetak_laporan.php?bulan=<?= $bulan ?>&tahun=<?= $tahun ?>" target="_blank" class="btn btn-success">
                    🖨️ Cetak
                </a>
            </div>
        </form>

        <!-- Tabel -->
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Username</th>
                        <th>Nama Lengkap</th>
                        <th>No Telepon</th>
                        <th>Alamat</th>
                        <th>Kamar Disewa</th>
                        <th>Tanggal Masuk</th>
                        <th>Tanggal Berakhir</th>
                        <th>Jumlah Bayar</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $total_semua = 0;
                    if ($result->num_rows > 0):
                        while ($row = $result->fetch_assoc()): 
                            $total_semua += $row['total'];
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($row['username']) ?></td>
                            <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                            <td><?= htmlspecialchars($row['no_telepon']) ?></td>
                            <td><?= htmlspecialchars($row['alamat']) ?></td>
                            <td><?= htmlspecialchars($row['nama_kamar']) ?></td>
                            <td><?= $row['tanggal_masuk'] ?></td>
                            <td><?= $row['tanggal_berakhir'] ?></td>
                            <td>Rp <?= number_format($row['jumlah_bayar'], 0, ',', '.') ?></td>
                            <td>Rp <?= number_format($row['total'], 0, ',', '.') ?></td>
                        </tr>
                    <?php endwhile; else: ?>
                        <tr>
                            <td colspan="9" class="text-center text-danger">Tidak ada data untuk bulan ini.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="8" class="text-end">Total Keseluruhan:</th>
                        <th>Rp <?= number_format($total_semua, 0, ',', '.') ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

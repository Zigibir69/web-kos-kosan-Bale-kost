<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include "../koneksi.php";

if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

// Ambil data user + seluruh kamar yang disetujui
$query = "SELECT 
            u.*, 
            GROUP_CONCAT(k.nama_kamar ORDER BY k.nama_kamar SEPARATOR ', ') AS daftar_kamar
          FROM tbl_user u 
          LEFT JOIN tbl_pesanan p ON u.id_user = p.id_user AND p.status = 'disetujui'
          LEFT JOIN tbl_kamar k ON p.id_kamar = k.id_kamar
          WHERE u.role = 'penyewa'
          GROUP BY u.id_user 
          ORDER BY u.created_at DESC";

$result = $koneksi->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Penyewa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f0f2f5;
        }
        .sidebar {
            height: 100vh;
            width: 250px;
            position: fixed;
            background-color: #343a40;
            padding-top: 20px;
            color: white;
        }
        .sidebar a {
            display: block;
            color: #ccc;
            padding: 12px 20px;
            text-decoration: none;
        }
        .sidebar a:hover,
        .sidebar a.active {
            background-color: #495057;
            color: #fff;
        }
        .main-content {
            margin-left: 250px;
            padding: 30px;
        }
        .table img {
            border-radius: 50%;
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
    <a href="kelola_penyewa.php" class="active"><i class="fa fa-users me-2"></i>Kelola Penyewa</a>
    <a href="laporan.php"><i class="fa fa-file-alt me-2"></i>Laporan</a>
    <a href="../logout.php" onclick="return confirm('Yakin ingin logout?')"><i class="fa fa-sign-out-alt me-2"></i>Logout</a>
</div>

<!-- Main Content -->
<div class="main-content">
    <div class="container">
        <h3 class="mb-4 text-center">Daftar Akun Penyewa</h3>

        <?php if (isset($_SESSION['pesan'])): ?>
            <div class="alert alert-info text-center"><?= $_SESSION['pesan']; unset($_SESSION['pesan']); ?></div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark text-center">
                    <tr>
                        <th>No</th>
                        <th>Username</th>
                        <th>Nama Lengkap</th>
                        <th>No. Telepon</th>
                        <th>Alamat</th>
                        <th>Kamar Disewa</th>
                        <th>Tanggal Daftar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($result->num_rows > 0): $no = 1; ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr class="text-center">
                            <td><?= $no++ ?></td>
                            <td>@<?= htmlspecialchars($row['nama']) ?></td>
                            <td class="text-start"><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                            <td><?= htmlspecialchars($row['no_telepon'] ?: '-') ?></td>
                            <td class="text-start"><?= htmlspecialchars($row['alamat']) ?></td>
                            <td><?= $row['daftar_kamar'] ? htmlspecialchars($row['daftar_kamar']) : '-' ?></td>
                            <td><?= date("d-m-Y H:i", strtotime($row['created_at'])) ?></td>
                            <td>
                                <a href="hapus_penyewa.php?id=<?= $row['id_user'] ?>" class="btn btn-sm btn-danger"
                                   onclick="return confirm('Yakin ingin menghapus akun penyewa ini?')">
                                    <i class="fa fa-trash-alt"></i> Hapus
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">Tidak ada data penyewa.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="text-end mt-3">
            <a href="dashboard_admin.php" class="btn btn-secondary">
                <i class="fa fa-arrow-left me-1"></i> Kembali ke Dashboard
            </a>
        </div>
    </div>
</div>

</body>
</html>

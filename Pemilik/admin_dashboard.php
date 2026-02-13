<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

include "../koneksi.php";

// Hitung jumlah data
$jumlah_kamar = $koneksi->query("SELECT COUNT(*) as total FROM tbl_kamar")->fetch_assoc()['total'];
$jumlah_fasilitas = $koneksi->query("SELECT COUNT(*) as total FROM tbl_master_fasilitas")->fetch_assoc()['total'];
$jumlah_pesanan = $koneksi->query("SELECT COUNT(*) as total FROM tbl_pesanan")->fetch_assoc()['total'];
$jumlah_penyewa = $koneksi->query("SELECT COUNT(*) as total FROM tbl_user WHERE role = 'penyewa'")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard Admin - Bale Kost</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f8f9fa;
    }
    .sidebar {
      height: 100vh;
      background-color: #343a40;
      padding-top: 20px;
      position: fixed;
      width: 250px;
      color: white;
    }
    .sidebar a {
      color: #ddd;
      display: block;
      padding: 12px 20px;
      text-decoration: none;
    }
    .sidebar a:hover, .sidebar a.active {
      background-color: #495057;
      color: white;
    }
    .main-content {
      margin-left: 250px;
      padding: 30px;
    }
    .card i {
      font-size: 2rem;
      margin-bottom: 10px;
    }
  </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
  <div class="text-center mb-4">
    <h4><i class="fa fa-user-shield me-2"></i>Admin</h4>
  </div>
  <a href="dashboard_admin.php" class="active"><i class="fa fa-chart-line me-2"></i>Dashboard</a>
  <a href="kelola_kos.php"><i class="fa fa-bed me-2"></i>Kelola Kamar</a>
  <a href="admin_pesanan.php"><i class="fa fa-clipboard-check me-2"></i>Kelola Pesanan</a>
  <a href="kelola_penyewa.php"><i class="fa fa-users me-2"></i>Kelola Penyewa</a>
  <a href="laporan.php"><i class="fa fa-file-alt me-2"></i>Laporan</a>
  <a href="../logout.php" onclick="return confirm('Yakin ingin logout?')"><i class="fa fa-sign-out-alt me-2"></i>Logout</a>
</div>

<!-- Main Content -->
<div class="main-content">
  <div class="container-fluid">
    <h2 class="mb-4">Dashboard Admin</h2>
    <div class="row g-4">
      <div class="col-md-3">
        <div class="card bg-primary text-white shadow-sm p-3">
          <div class="text-center">
            <i class="fa fa-bed"></i>
            <h5 class="mt-2">Total Kamar</h5>
            <h3><?= $jumlah_kamar ?></h3>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card bg-success text-white shadow-sm p-3">
          <div class="text-center">
            <i class="fa fa-tools"></i>
            <h5 class="mt-2">Total Fasilitas</h5>
            <h3><?= $jumlah_fasilitas ?></h3>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card bg-warning text-dark shadow-sm p-3">
          <div class="text-center">
            <i class="fa fa-clipboard-list"></i>
            <h5 class="mt-2">Total Pesanan</h5>
            <h3><?= $jumlah_pesanan ?></h3>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card bg-danger text-white shadow-sm p-3">
          <div class="text-center">
            <i class="fa fa-user"></i>
            <h5 class="mt-2">Total Penyewa</h5>
            <h3><?= $jumlah_penyewa ?></h3>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

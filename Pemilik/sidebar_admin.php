<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
          <style>
    body {
      background-color: #f1f2f6;
      font-family: 'Segoe UI', sans-serif;
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
    .card-kamar {
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .card img {
      height: 200px;
      object-fit: cover;
      border-top-left-radius: 10px;
      border-top-right-radius: 10px;
    }
    .badge-fasilitas {
      background: #e9ecef;
      color: #000;
      margin: 3px 5px 3px 0;
      padding: 6px 10px;
      font-size: 0.85rem;
      border-radius: 20px;
      display: inline-block;
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
  <a href="../laporan.php"><i class="fa fa-file-alt me-2"></i>Laporan</a>
  <a href="../logout.php" onclick="return confirm('Yakin ingin logout?')"><i class="fa fa-sign-out-alt me-2"></i>Logout</a>
</div>
</body>
</html>
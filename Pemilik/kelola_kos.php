<?php
include '../koneksi.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$data = $koneksi->query("SELECT * FROM tbl_kamar");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Kelola Kamar</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
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
  <a href="admin_dashboard.php"><i class="fa fa-chart-line me-2"></i>Dashboard</a>
  <a href="kelola_kos.php" class="active"><i class="fa fa-bed me-2"></i>Kelola Kamar</a>
  <a href="admin_pesanan.php"><i class="fa fa-clipboard-check me-2"></i>Kelola Pesanan</a>
  <a href="kelola_penyewa.php"><i class="fa fa-users me-2"></i>Kelola Penyewa</a>
  <a href="laporan.php"><i class="fa fa-file-alt me-2"></i>Laporan</a>
  <a href="../logout.php" onclick="return confirm('Yakin ingin logout?')"><i class="fa fa-sign-out-alt me-2"></i>Logout</a>
</div>

<!-- Main Content -->
<div class="main-content">
  <div class="container-fluid">
    <h2 class="mb-4">Kelola Kamar</h2>
    <a href="tambah_kos.php" class="btn btn-primary mb-3"><i class="fa fa-plus"></i> Tambah Kamar</a>
    <a href="tambah_fasilitas.php" class="btn btn-success mb-3 ms-2"><i class="fa fa-plus-circle"></i> Tambah Fasilitas</a>

    <div class="row g-4">
      <?php while($row = $data->fetch_assoc()): ?>
        <div class="col-md-4">
          <div class="card card-kamar">
            <?php if ($row['gambar_kamar']): ?>
              <img src="../gambar/<?= $row['gambar_kamar']; ?>" class="card-img-top" alt="Gambar Kamar">
            <?php else: ?>
              <img src="https://via.placeholder.com/300x200?text=No+Image" class="card-img-top" alt="No Image">
            <?php endif; ?>

            <div class="card-body">
              <h5 class="card-title"><?= $row['nama_kamar']; ?> </h5>

              <p>
                <?php if ($row['status'] == 'tersedia'): ?>
                  <span class="badge bg-success"><i class="fa fa-check-circle"></i> Kosong</span>
                <?php else: ?>
                  <span class="badge bg-danger"><i class="fa fa-times-circle"></i> Terisi</span>
                <?php endif; ?>
              </p>

              <!-- FASILITAS KAMAR -->
              <div class="mb-2">
                <?php
                  $id_kamar = $row['id_kamar'];
                  $fasilitas_q = $koneksi->query("
                    SELECT m.nama_fasilitas, m.icon_fasilitas
                    FROM tbl_kamar_fasilitas kf
                    JOIN tbl_master_fasilitas m ON kf.id_fasilitas = m.id_fasilitas
                    WHERE kf.id_kamar = '$id_kamar'
                  ");
                  while ($fas = $fasilitas_q->fetch_assoc()):
                ?>
                  <span class="badge badge-fasilitas">
                    <i class="<?= $fas['icon_fasilitas']; ?>"></i> <?= $fas['nama_fasilitas']; ?>
                  </span>
                <?php endwhile; ?>
              </div>

              <p class="fw-bold text-success">
                <i class="fa fa-tag"></i> Rp <?= number_format($row['harga'], 0, ',', '.'); ?> 
              </p>

              <div class="d-flex justify-content-between">
                <a href="edit_kos.php?id=<?= $row['id_kamar']; ?>" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i> Edit</a>
                <a href="hapus.php?id=<?= $row['id_kamar']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus kamar ini?');"><i class="fa fa-trash"></i> Hapus</a>
              </div>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  </div>
</div>

</body>
</html>

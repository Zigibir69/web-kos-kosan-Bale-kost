<?php
session_start();
include "../koneksi.php";

// Proteksi login dan role
if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'penyewa') {
    header("Location: ../index.php");
    exit;
}

$id_user = $_SESSION['id_user'];

/// Cek apakah akun masih ada di database
$stmt = $koneksi->prepare("SELECT nama FROM tbl_user WHERE id_user = ?");
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Akun tidak ditemukan, hapus session dan paksa logout
    session_unset();
    session_destroy();
    header("Location: ../index.php?pesan=akun_dihapus");
    exit;
}

$user = $result->fetch_assoc();
$namaPenyewa = $user['nama'];

// Ambil data kamar
$sql = "SELECT * FROM tbl_kamar";
$result = $koneksi->query($sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Penyewa - Bale Kost</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body { font-family: 'Segoe UI', sans-serif; }
    .hero {
      background: url('https://images.unsplash.com/photo-1600585154340-be6161a56a0c') no-repeat center center/cover;
      height: 300px; color: white;
      display: flex; align-items: center; justify-content: center; text-align: center;
    }
    .hero .container {
      background-color: rgba(0, 0, 0, 0.5);
      padding: 10px 20px;
      border-radius: 20px;
    }
    .card-img-top {
      height: 200px;
      object-fit: cover;
    }
    .footer {
      background-color: #212529;
      color: white;
      padding: 2rem 0;
    }
    .footer-title { font-weight: bold; margin-bottom: 1rem; }
    .social-icons a {
      margin-right: 10px;
      font-size: 1.2rem;
      color: white;
    }
    .social-icons a:hover { color: #0d6efd; }
    .copyright {
      text-align: center;
      padding: 1rem 0;
      font-size: 0.9rem;
      color: #aaa;
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
  <div class="container">
    <a class="navbar-brand" href="dashboard_penyewa.php"><i class="fas fa-home me-2"></i>Bale Kost</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarUser">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarUser">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="status_pesanan.php"><i class="fa fa-clipboard-check me-1"></i>Status Pesanan</a></li>
        <li class="nav-item"><a class="nav-link" href="profil.php"><i class="fa fa-user me-1"></i>Profil</a></li>
        <li class="nav-item"><a class="nav-link" href="../logout.php"><i class="fa fa-sign-out-alt me-1"></i>Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- Hero -->
<section class="hero py-5">
  <div class="container text-center">
    <div class="d-flex justify-content-center align-items-center gap-3 flex-wrap">
      <h1 class="display-5 fw-bold mb-0">
        Selamat Datang, <?= htmlspecialchars($namaPenyewa); ?>
      </h1>
      <img src="../Gambar/birbhaus-hello.gif" alt="Wave GIF" width="100" height="100" style="margin-top:-10px; border-radius:10px;">
    </div>
    <p class="lead mt-3">Lihat dan pesan kamar kos terbaik untuk kamu</p>
    <a href="https://www.google.com/maps?q=-8.6019790,116.0984380" 
       class="btn btn-success mt-2" 
       target="_blank">📍 Cek Lokasi di Google Maps</a>
  </div>
</section>

<!-- Daftar Kamar -->
<section class="py-5 bg-light">
  <div class="container">
    <div class="section-title text-center mb-4">
      <h2>Daftar Kamar Tersedia</h2>
      <p>Pilih kamar yang sesuai dengan kebutuhanmu</p>
    </div>
    <div class="row g-4">
      <?php while ($data = $result->fetch_assoc()): ?>
        <div class="col-md-4">
          <div class="card shadow-sm h-100">
            <img src="../gambar/<?= $data['gambar_kamar']; ?>" class="card-img-top" alt="<?= $data['nama_kamar']; ?>">
            <div class="card-body">
              <h5 class="card-title"><?= $data['nama_kamar']; ?></h5>
              <p><span class="badge <?= $data['status'] == 'tersedia' ? 'bg-success' : 'bg-danger'; ?>"><?= ucfirst($data['status']); ?></span></p>
              <p class="text-success fw-bold">Rp<?= number_format($data['harga'], 0, ',', '.'); ?><?= $data['satuan'] ? '/' . $data['satuan'] : ''; ?></p>
              <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal<?= $data['id_kamar']; ?>">Lihat Detail</button>
            </div>
          </div>
        </div>

        <!-- Modal Detail -->
        <div class="modal fade" id="modal<?= $data['id_kamar']; ?>" tabindex="-1">
          <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title"><?= $data['nama_kamar']; ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body row">
                <div class="col-md-6">
                  <img src="../gambar/<?= $data['gambar_kamar']; ?>" class="img-fluid rounded shadow-sm">
                </div>
                <div class="col-md-6">
                  <p><strong>Harga:</strong> Rp<?= number_format($data['harga'], 0, ',', '.'); ?><?= $data['satuan'] ? '/' . $data['satuan'] : ''; ?></p>
                  <p><strong>Deskripsi:</strong><br><?= nl2br($data['deskripsi']); ?></p>
                  <p><strong>Status:</strong> 
                    <span class="badge <?= $data['status'] == 'tersedia' ? 'bg-success' : 'bg-danger'; ?>"><?= ucfirst($data['status']); ?></span>
                  </p>
                  <p><strong>Fasilitas:</strong></p>
                  <ul class="list-unstyled">
                    <?php
                    $fasilitas = $koneksi->query("
                      SELECT mf.nama_fasilitas, mf.icon_fasilitas
                      FROM tbl_kamar_fasilitas kf
                      JOIN tbl_master_fasilitas mf ON kf.id_fasilitas = mf.id_fasilitas
                      WHERE kf.id_kamar = {$data['id_kamar']}
                    ");
                    while ($fas = $fasilitas->fetch_assoc()):
                    ?>
                      <li><i class="fa <?= $fas['icon_fasilitas']; ?>"></i> <?= $fas['nama_fasilitas']; ?></li>
                    <?php endwhile; ?>
                  </ul>
                </div>
              </div>
              <div class="modal-footer">
                <?php if ($data['status'] == 'tersedia'): ?>
                  <a href="pesan_kamar.php?id=<?= $data['id_kamar']; ?>" class="btn btn-success">Pesan Sekarang</a>
                <?php endif; ?>
                <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
              </div>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  </div>
</section>

<!-- Footer -->
<footer class="footer">
  <div class="container">
    <div class="row">
      <div class="col-lg-6 mb-4">
        <h3 class="footer-title">Bale Kost</h3>
        <p>Menyediakan kamar kos nyaman dengan fasilitas lengkap dan harga terjangkau.</p>
        <div class="social-icons mt-3">
          <a href="https://www.instagram.com/ur_villain1"><i class="fab fa-instagram"></i></a>
          <a href="https://wa.me/6282341826840"><i class="fab fa-whatsapp"></i></a>
        </div>
      </div>
      <div class="col-lg-4 mb-4">
        <h4 class="footer-title">Kontak Kami</h4>
        <ul class="list-unstyled">
          <li><i class="fas fa-map-marker-alt me-2"></i> Jl. Singa Raja</li>
          <li><i class="fas fa-envelope me-2"></i> panjigagah8@gmail.com</li>
          <li><i class="fab fa-whatsapp me-2"></i> +62 8234-1826-840</li>
        </ul>
      </div>
    </div>
    <div class="copyright text-center">
      <p>&copy; 2025 Bale Kost. All rights reserved.</p>
    </div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

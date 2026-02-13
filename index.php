<?php
include "koneksi.php";

// Ambil semua kamar
$sql = "SELECT * FROM tbl_kamar";
$result = $koneksi->query($sql);

// Cek apakah ada redirect dari parameter URL
// if (isset($_GET['redirect'])) {
//     $redirect = urldecode($_GET['redirect']);
//     header("Location: $redirect");
//     exit;
// } else {
//     // Atau arahkan ke dashboard biasa
//     if ($_SESSION['role'] == 'penyewa') {
//         header("Location: penyewa/dashboard_penyewa.php");
//     }
//     // ... role lain
//     exit;
// }

?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bale Kost - Temukan Kamar Kos Impianmu</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
    }
    .hero {
      background: url('https://images.unsplash.com/photo-1600585154340-be6161a56a0c') no-repeat center center/cover;
      height: 350px;
      color: white;
      display: flex;
      align-items: center;
      text-align: center;
    }
    .hero .container {
      background-color: rgba(0, 0, 0, 0.5);
      padding: 30px;
      border-radius: 15px;
    }
    .card-img-top {
      height: 200px;
      object-fit: cover;
    }
    .card-body {
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }
    .footer {
      background-color: #212529;
      color: white;
      padding: 2rem 0;
    }
    .footer-title {
      font-weight: bold;
      margin-bottom: 1rem;
    }
    .social-icons a {
      margin-right: 10px;
      font-size: 1.2rem;
      color: white;
    }
    .social-icons a:hover {
      color: #0d6efd;
    }
    .copyright {
      text-align: center;
      padding: 1rem 0;
      font-size: 0.9rem;
      color: #aaa;
    }
    .fasilitas i {
      margin-right: 6px;
    }
    .modal-body {
      max-height: 70vh;
      overflow-y: auto;
    }
  </style>
</head>
<body>

<!-- Modal Login -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content shadow">
      <div class="modal-header bg-dark text-white">
        <h5 class="modal-title" id="loginModalLabel"><i class="fa fa-sign-in-alt me-2"></i>Login</h5>
        <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="proses_login.php" method="POST">
        <div class="modal-body">
          <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']); ?></div>
          <?php endif; ?>
          <div class="mb-3">
            <label for="nama" class="form-label">Nama</label>
            <input type="text" class="form-control" id="nama" name="nama" placeholder="Masukkan nama Anda" required>
          </div>
          <div class="mb-3">
            <label for="password" class="form-label">Kata Sandi</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan kata sandi" required>
          </div>
          <div class="text-end">
            <small>Belum punya akun? <a href="daftar.php">Daftar Akun</a></small>
          </div>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Login</button>
        </div>
      </form>
    </div>
  </div>
</div>


<!-- Modal Daftar -->
<div class="modal fade" id="daftarModal" tabindex="-1" aria-labelledby="daftarModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content shadow">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="daftarModalLabel"><i class="fa fa-user-plus me-2"></i>Daftar Akun</h5>
        <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="proses_daftar.php" method="POST">
        <div class="modal-body">
          <div class="mb-3">
            <label for="nama" class="form-label">Nama Lengkap</label>
            <input type="text" class="form-control" name="nama" required>
          </div>
          <div class="mb-3">
            <label for="no_telepon" class="form-label">No Telepon</label>
            <input type="text" class="form-control" name="no_telepon" required>
          </div>
          <div class="mb-3">
            <label for="password" class="form-label">Kata Sandi</label>
            <input type="password" class="form-control" name="password" required>
          </div>
        </div>
        <div class="modal-footer justify-content-end">
          <button type="submit" class="btn btn-success">Daftar</button>
        </div>
      </form>
    </div>
  </div>
</div>



<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
  <div class="container">
    <a class="navbar-brand" href="index.php"><i class="fas fa-home me-2"></i>Bale Kost</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarUser">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarUser">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#loginModal">
            <i class="fa fa-sign-in-alt me-1"></i>Login
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- Hero -->
<section class="hero">
  <div class="container">
    <h1 class="display-5 fw-bold">Temukan Kamar Kos Impianmu</h1>
    <p class="lead">Nyaman, strategis, dan harga terjangkau untuk mahasiswa dan pekerja</p>
    <a href="https://www.google.com/maps?q=-8,6019790,116.0984380" 
     class="btn btn-success" 
     target="_blank">
     📍 Cek Lokasi di Google Maps
  </a>
  </div>

</section>

<!-- Kamar Tersedia -->
<section class="py-5 bg-light">
  <div class="container">
    <div class="section-title text-center mb-4">
      <h2>Kamar Tersedia</h2>
      <p>Temukan kamar yang sesuai dengan kebutuhan dan gaya hidup Anda</p>
    </div>
    <div class="row g-4">
      <?php while($data = $result->fetch_assoc()): ?>
        <?php
          $gambar = $data['gambar_kamar'] ?: 'default.jpg';
          $status = strtolower($data['status']);
          $harga = number_format($data['harga'], 0, ',', '.');
          $idKamar = intval($data['id_kamar']);
        ?>
      <div class="col-md-4">
        <div class="card shadow-sm h-100">
          <img src="gambar/<?= $gambar; ?>" class="card-img-top" alt="<?= $data['nama_kamar']; ?>">
          <div class="card-body">
            <h5 class="card-title"><?= $data['nama_kamar']; ?></h5>
            <p class="card-text">
              <span class="badge <?= ($status == 'tersedia') ? 'bg-success' : 'bg-danger'; ?>">
                <?= ucfirst($status); ?>
              </span>
            </p>
            <p class="card-text text-success fw-semibold">
              Harga: Rp<?= $harga; ?> <?= $data['satuan'] ? '/' . $data['satuan'] : ''; ?>
            </p>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal<?= $idKamar; ?>">
              Lihat Detail
            </button>
          </div>
        </div>
      </div>

      <!-- Modal Detail Kamar -->
      <div class="modal fade" id="modal<?= $idKamar; ?>" tabindex="-1" aria-labelledby="modalLabel<?= $idKamar; ?>" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title"><?= $data['nama_kamar']; ?></h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
              <div class="row">
                <div class="col-md-6">
                  <img src="gambar/<?= $gambar; ?>" class="img-fluid rounded shadow-sm" alt="<?= $data['nama_kamar']; ?>">
                </div>
                <div class="col-md-6">
                  <p><strong>Harga:</strong> Rp<?= $harga; ?> <?= $data['satuan'] ? '/' . $data['satuan'] : ''; ?></p>
                  <p><strong>Deskripsi:</strong><br><?= nl2br($data['deskripsi']); ?></p>
                  <p><strong>Status:</strong> 
                    <span class="badge <?= ($status == 'tersedia') ? 'bg-success' : 'bg-danger'; ?>">
                      <?= ucfirst($status); ?>
                    </span>
                  </p>
                  <!-- Fasilitas -->
                  <p><strong>Fasilitas:</strong></p>
                  <ul class="list-unstyled fasilitas">
                    <?php
                      $qFasilitas = $koneksi->query("
                        SELECT mf.nama_fasilitas, mf.icon_fasilitas
                        FROM tbl_kamar_fasilitas kf
                        JOIN tbl_master_fasilitas mf ON kf.id_fasilitas = mf.id_fasilitas
                        WHERE kf.id_kamar = $idKamar
                      ");
                      while ($fasilitas = $qFasilitas->fetch_assoc()):
                    ?>
                      <li><i class="fa <?= $fasilitas['icon_fasilitas']; ?>"></i> <?= $fasilitas['nama_fasilitas']; ?></li>
                    <?php endwhile; ?>
                  </ul>
                </div>
              </div>
            </div>
            <div class="modal-footer">
  <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
  <?php if (isset($_SESSION['id_user']) && $_SESSION['role'] === 'penyewa'): ?>
    <a href="pesan_kamar.php?id=<?= $idKamar; ?>" class="btn btn-success">Pesan Sekarang</a>
  <?php else: ?>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#loginModal">Login untuk Memesan</button>
  <?php endif; ?>
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
        <p>Menyediakan kamar kos nyaman dengan fasilitas lengkap dan harga terjangkau di lokasi strategis.</p>
        <div class="social-icons mt-3">
          <a href="https://www.instagram.com/ur_villain1"><i class="fab fa-instagram"></i></a>
          <a href="https://wa.me/<082341826840>"><i class="fab fa-whatsapp"></i></a>
        </div>
      </div>
      <div class="col-lg-4 mb-4">
        <h4 class="footer-title">Kontak Kami</h4>
        <ul class="list-unstyled">
          <li><i class="fas fa-map-marker-alt me-2"></i> Jl. singa Raja </li>
         
          <li><i class="fas fa-envelope me-2"></i> panjigagah8@gmail.com</li>
          <li><i class="fab fa-whatsapp me-2"></i> +62 8234-1826-840</li>
        </ul>
      </div>
    </div>
    <div class="copyright text-center mt-3">
      <p>&copy; <?= date('Y'); ?> Bale Kost. Hak Cipta Dilindungi.</p>
    </div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

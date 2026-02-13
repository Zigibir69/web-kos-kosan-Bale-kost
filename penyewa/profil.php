<?php   
session_start();
include "../koneksi.php";

if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit;
}

$id_user = $_SESSION['id_user'];

// Ambil data user
$query = $koneksi->prepare("SELECT * FROM tbl_user WHERE id_user = ?");
$query->bind_param("i", $id_user);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();

// Ambil pesanan tanpa duplikat berdasarkan id_kamar (pesanan terakhir per kamar)
$stmt = $koneksi->prepare("
    SELECT p.*, k.nama_kamar 
    FROM tbl_pesanan p
    JOIN tbl_kamar k ON p.id_kamar = k.id_kamar
    WHERE p.id_user = ? AND p.id_pesanan IN (
        SELECT MAX(id_pesanan) 
        FROM tbl_pesanan 
        WHERE id_user = ?
        GROUP BY id_kamar
    )
    ORDER BY p.tanggal_pesan DESC
");
$stmt->bind_param("ii", $id_user, $id_user);
$stmt->execute();
$pesanan_result = $stmt->get_result();
$daftar_pesanan = $pesanan_result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Profil Saya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            animation: waveGradient 15s linear infinite;
            background: linear-gradient(90deg, #ff00cc, #3333ff, #00ffcc, #ffcc00, #ff0066);
            background-size: 300% 300%;
            min-height: 100vh;
            margin: 0;
        }
        @keyframes waveGradient {
            0% { background-position: 0% 0%; }
            100% { background-position: 300% 0%; }
        }
        .profile-card {
            border: none;
            background: rgba(255, 255, 255, 0.85);
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            padding: 30px;
            backdrop-filter: blur(6px);
        }
        .profile-img {
            width: 220px;
            height: 220px;
            object-fit: cover;
            border-radius: 50%;
            border: 5px solid #0d6efd;
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-top: 25px;
            color: #222;
            border-bottom: 2px solid #0d6efd;
            display: inline-block;
            padding-bottom: 5px;
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

<div class="container py-5">
    <?php if (isset($_SESSION['pesan'])): ?>
        <div class="alert alert-success text-center col-md-8 mx-auto">
            <?= $_SESSION['pesan']; unset($_SESSION['pesan']); ?>
        </div>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="profile-card text-center">
                <img src="../uploads/<?= htmlspecialchars($user['foto'] ?: 'default.png') ?>" alt="Foto Profil" class="profile-img">

                <h3 class="mb-0"><?= htmlspecialchars($user['nama_lengkap']) ?></h3>
                <p class="text-muted">@<?= htmlspecialchars($user['nama']) ?></p>

                <div class="row text-start mt-4">
                    <div class="col-md-6">
                        <h6 class="section-title">Data Diri</h6>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><strong>No. Telepon:</strong> <?= htmlspecialchars($user['no_telepon'] ?: '-') ?></li>
                            <li class="list-group-item"><strong>Alamat:</strong> <?= htmlspecialchars($user['alamat'] ?: '-') ?></li>
                            <li class="list-group-item"><strong>Role:</strong> <?= ucfirst($user['role']) ?></li>
                            <li class="list-group-item"><strong>Dibuat:</strong> <?= date("d-m-Y H:i", strtotime($user['created_at'])) ?></li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="section-title">Kamar Dipesan</h6>
                        <?php if (!empty($daftar_pesanan)): ?>
                            <?php foreach ($daftar_pesanan as $pesanan): ?>
                                <ul class="list-group list-group-flush mb-3">
                                    <li class="list-group-item"><strong>Nama Kamar:</strong> <?= htmlspecialchars($pesanan['nama_kamar']) ?></li>
                                    <li class="list-group-item"><strong>Tanggal Masuk:</strong> <?= !empty($pesanan['tanggal_masuk']) ? date("d-m-Y", strtotime($pesanan['tanggal_masuk'])) : '-' ?></li>
                                    <li class="list-group-item"><strong>Berakhir:</strong> <?= !empty($pesanan['tanggal_berakhir']) ? date("d-m-Y", strtotime($pesanan['tanggal_berakhir'])) : '-' ?></li>
                                    <li class="list-group-item"><strong>Durasi:</strong> <?= $pesanan['durasi_bulan'] ?> bulan</li>
                                    <li class="list-group-item"><strong>Status:</strong>
                                        <span class="badge bg-<?= 
                                            $pesanan['status'] == 'disetujui' ? 'success' :
                                            ($pesanan['status'] == 'bayar' ? 'info' :
                                            ($pesanan['status'] == 'menunggu' ? 'warning' : 'danger')) ?>">
                                            <?= ucfirst($pesanan['status']) ?>
                                        </span>
                                    </li>
                                </ul>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-muted">Belum ada pesanan kamar.</div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Tombol Edit & Batal -->
                <div class="mt-4">
                    <a href="editprofil.php" class="btn btn-primary me-2">Edit Profil</a>
                    <a href="dashboard_penyewa.php" class="btn btn-secondary">Batal</a>
                </div>

            </div>
        </div>
    </div>
</div>

</body>
</html>

<?php
include "koneksi.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $nama = trim($_POST['nama']);
  $nama_lengkap = trim($_POST['nama_lengkap']);
  $no_telepon = trim($_POST['no_telepon']);
  $password = trim($_POST['password']);
  $alamat = trim($_POST['alamat']);

  // Validasi sederhana
  if (empty($nama) || empty($nama_lengkap) || empty($no_telepon) || empty($password) || empty($alamat)) {
    $error = "Semua field wajib diisi.";
  } else {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Cek apakah user sudah ada
    $cek = $koneksi->prepare("SELECT * FROM tbl_user WHERE no_telepon = ?");
    $cek->bind_param("s", $no_telepon);
    $cek->execute();
    $result = $cek->get_result();

    if ($result->num_rows > 0) {
      $error = "Nomor telepon sudah terdaftar. Silakan login.";
    } else {
      // Simpan user baru
      $stmt = $koneksi->prepare("INSERT INTO tbl_user (nama, nama_lengkap, no_telepon, password, alamat, role) VALUES (?, ?, ?, ?, ?, 'penyewa')");
      $stmt->bind_param("sssss", $nama, $nama_lengkap, $no_telepon, $hashedPassword, $alamat);

      if ($stmt->execute()) {
        header("Location: index.php?success=daftar_berhasil");
        exit;
      } else {
        $error = "Gagal mendaftar: " . $stmt->error;
      }
    }
  }
}
?>

<!-- HTML Form Daftar -->
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Daftar Akun</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-5">
        <div class="card shadow">
          <div class="card-header bg-primary text-white text-center">
            <h5>Daftar Akun Penyewa</h5>
          </div>
          <form method="POST">
            <div class="card-body">
              <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
              <?php endif; ?>
              <div class="mb-3">
                <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                <input type="text" class="form-control" name="nama_lengkap" required>
              </div>
              <div class="mb-3">
                <label for="nama" class="form-label">Username</label>
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
              <div class="mb-3">
                <label for="alamat" class="form-label">Alamat</label>
                <textarea class="form-control" name="alamat" required></textarea>
              </div>
            </div>
            <div class="card-footer d-flex justify-content-between">
              <a href="index.php" class="btn btn-secondary">Kembali</a>
              <button type="submit" class="btn btn-success">Daftar</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</body>
</html>

<?php 
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

include '../koneksi.php';

// Ambil semua fasilitas
$data_fasilitas = $koneksi->query("SELECT * FROM tbl_master_fasilitas ORDER BY nama_fasilitas ASC");

// Proses tambah kamar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_kamar = $_POST['nama_kamar'];
    $harga = $_POST['harga'];
    $satuan = $_POST['satuan'];
    $status = $_POST['status'];
    $deskripsi = $_POST['deskripsi'];
    $gambar = '';

    // Upload gambar
    if ($_FILES['gambar']['name']) {
        $target_dir = "../gambar/";
        $gambar = basename($_FILES["gambar"]["name"]);
        $target_file = $target_dir . $gambar;
        move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file);
    }

    // Simpan kamar
    $query = "INSERT INTO tbl_kamar (nama_kamar, harga, satuan, status, deskripsi, gambar_kamar) 
              VALUES ('$nama_kamar', '$harga', '$satuan', '$status', '$deskripsi', '$gambar')";
    $koneksi->query($query);
    $id_kamar_baru = $koneksi->insert_id;

    // Simpan fasilitas jika ada
    if (!empty($_POST['fasilitas'])) {
        foreach ($_POST['fasilitas'] as $idf) {
            $idf = (int) $idf;
            $koneksi->query("INSERT INTO tbl_kamar_fasilitas (id_kamar, id_fasilitas) VALUES ('$id_kamar_baru', '$idf')");
        }
    }

    header("Location: kelola_kos.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Tambah Kamar</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">
  <h3 class="mb-4">🛏️ Tambah Kamar Baru</h3>

  <!-- FORM TAMBAH KAMAR -->
  <form method="POST" enctype="multipart/form-data">
    <div class="mb-3">
      <label>Nama Kamar</label>
      <input type="text" name="nama_kamar" class="form-control" required>
    </div>
    <div class="mb-3">
      <label>Harga</label>
      <input type="number" name="harga" class="form-control" required>
    </div>
    <div class="mb-3">
      <label>Satuan</label>
      <input type="text" name="satuan" class="form-control" placeholder="/bulan" required>
    </div>
    <div class="mb-3">
      <label>Status</label>
      <select name="status" class="form-control" required>
        <option value="tersedia">Tersedia</option>
        <option value="terisi">Terisi</option>
      </select>
    </div>
    <div class="mb-3">
      <label>Deskripsi</label>
      <textarea name="deskripsi" class="form-control" required></textarea>
    </div>
    <div class="mb-3">
      <label>Gambar Kamar</label>
      <input type="file" name="gambar" class="form-control">
    </div>

    <!-- ✅ Checkbox Fasilitas -->
    <div class="mb-4">
      <label><strong>Fasilitas Kamar</strong></label>
      <div class="row">
        <?php while ($f = $data_fasilitas->fetch_assoc()): ?>
          <?php
            $icon = $f['icon_fasilitas'];
            $icon_html = '';
            if (str_starts_with($icon, 'fa')) {
                $icon_html = '<i class="' . htmlspecialchars($icon) . ' me-1"></i>';
            } elseif (str_starts_with($icon, 'bi')) {
                $icon_html = '<i class="' . htmlspecialchars($icon) . ' me-1"></i>';
            } else {
                $icon_html = '<span class="text-muted me-1">-</span>';
            }
          ?>
          <div class="col-md-4 mb-2">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="fasilitas[]" value="<?= $f['id_fasilitas'] ?>" id="fas<?= $f['id_fasilitas'] ?>">
              <label class="form-check-label" for="fas<?= $f['id_fasilitas'] ?>">
                <?= $icon_html ?> <?= htmlspecialchars($f['nama_fasilitas']); ?>
              </label>
            </div>
          </div>
        <?php endwhile; ?>
      </div>
    </div>

    <button type="submit" class="btn btn-success"><i class="bi bi-check-circle me-1"></i> Simpan</button>
    <a href="kelola_kos.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Batal</a>
  </form>
</div>

</body>
</html>

<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

include '../koneksi.php';

$id = $_GET['id'];
$data = $koneksi->query("SELECT * FROM tbl_kamar WHERE id_kamar = '$id'")->fetch_assoc();

// Ambil fasilitas yang sudah dipilih
$fasilitas_terpilih = [];
$res = $koneksi->query("SELECT id_fasilitas FROM tbl_kamar_fasilitas WHERE id_kamar = '$id'");
while ($row = $res->fetch_assoc()) {
    $fasilitas_terpilih[] = $row['id_fasilitas'];
}

// Proses edit kamar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_kamar'])) {
    $nama_kamar = $_POST['nama_kamar'];
    $harga = $_POST['harga'];
    $satuan = $_POST['satuan'];
    $status = $_POST['status'];
    $deskripsi = $_POST['deskripsi'];
    $gambar = $data['gambar_kamar'];

    if ($_FILES['gambar']['name']) {
        $target_dir = "../gambar/";
        $gambar = basename($_FILES["gambar"]["name"]);
        $target_file = $target_dir . $gambar;
        move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file);
    }

    $query = "UPDATE tbl_kamar SET 
                nama_kamar = '$nama_kamar',
                harga = '$harga',
                satuan = '$satuan',
                status = '$status',
                deskripsi = '$deskripsi',
                gambar_kamar = '$gambar'
              WHERE id_kamar = '$id'";
    $koneksi->query($query);

    // Update fasilitas
    $koneksi->query("DELETE FROM tbl_kamar_fasilitas WHERE id_kamar = '$id'");
    if (!empty($_POST['fasilitas'])) {
        foreach ($_POST['fasilitas'] as $idf) {
            $idf = (int) $idf;
            $koneksi->query("INSERT INTO tbl_kamar_fasilitas (id_kamar, id_fasilitas) VALUES ('$id', '$idf')");
        }
    }

    header("Location: kelola_kos.php");
    exit;
}

// Ambil semua fasilitas
$data_fasilitas = $koneksi->query("SELECT * FROM tbl_master_fasilitas ORDER BY nama_fasilitas ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Edit Kamar</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">
  <h3 class="mb-4">Edit Kamar</h3>

  <?php if (isset($error)): ?>
      <div class="alert alert-danger"><?= $error; ?></div>
  <?php endif; ?>
  <?php if (isset($success)): ?>
      <div class="alert alert-success"><?= $success; ?></div>
  <?php endif; ?>

  <!-- FORM EDIT KAMAR -->
  <form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="update_kamar" value="1">
    <div class="mb-3">
      <label>Nama Kamar</label>
      <input type="text" name="nama_kamar" class="form-control" value="<?= $data['nama_kamar'] ?>" required>
    </div>
    <div class="mb-3">
      <label>Harga</label>
      <input type="number" name="harga" class="form-control" value="<?= $data['harga'] ?>" required>
    </div>
    <div class="mb-3">
      <label>Status</label>
      <select name="status" class="form-control">
        <option value="tersedia" <?= $data['status'] == 'tersedia' ? 'selected' : '' ?>>Tersedia</option>
        <option value="terisi" <?= $data['status'] == 'terisi' ? 'selected' : '' ?>>Terisi</option>
      </select>
    </div>
    <div class="mb-3">
      <label>Deskripsi</label>
      <textarea name="deskripsi" class="form-control" required><?= $data['deskripsi'] ?></textarea>
    </div>
    <div class="mb-3">
      <label>Ganti Gambar (jika perlu)</label>
      <input type="file" name="gambar" class="form-control">
    </div>

    <!-- ✅ Checkbox Fasilitas -->
    <div class="mb-4">
      <label><strong>Pilih Fasilitas untuk Kamar</strong></label>
      <div class="row">
        <?php while ($f = $data_fasilitas->fetch_assoc()): ?>
          <div class="col-md-4 mb-2">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="fasilitas[]" value="<?= $f['id_fasilitas'] ?>"
                <?= in_array($f['id_fasilitas'], $fasilitas_terpilih) ? 'checked' : '' ?>>
              <label class="form-check-label">
                <i class="<?= $f['icon_fasilitas']; ?>"></i> <?= $f['nama_fasilitas']; ?>
              </label>
            </div>
          </div>
        <?php endwhile; ?>
      </div>
    </div>

    <button type="submit" class="btn btn-primary">Update Kamar</button>
    <a href="kelola_kos.php" class="btn btn-secondary">Batal</a>
  </form>
</div>
</body>
</html>

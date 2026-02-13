<?php  
session_start();
include "../koneksi.php";

if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'penyewa') {
    header("Location: ../index.php");
    exit;
}

$id_pesanan = $_GET['id_pesanan'] ?? 0;
$id_user = $_SESSION['id_user'];

// Ambil detail pesanan
$stmt = $koneksi->prepare("SELECT p.*, k.nama_kamar, k.harga, k.satuan 
                           FROM tbl_pesanan p 
                           JOIN tbl_kamar k ON p.id_kamar = k.id_kamar 
                           WHERE p.id_pesanan = ? AND p.id_user = ?");
$stmt->bind_param("ii", $id_pesanan, $id_user);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "<script>alert('Data tidak ditemukan.'); window.location='status_pesanan.php';</script>";
    exit;
}

$data = $result->fetch_assoc();
$total = $data['harga'] * $data['durasi_bulan'];

// Proses upload bukti pembayaran
if (isset($_POST['upload'])) {
    $file = $_FILES['bukti']['name'];
    $tmp = $_FILES['bukti']['tmp_name'];
    $target = "../uploads/" . $file;

    if (move_uploaded_file($tmp, $target)) {
        $status = "menunggu";
        $update = $koneksi->prepare("UPDATE tbl_pesanan SET bukti_pembayaran = ?, status = ? WHERE id_pesanan = ?");
        $update->bind_param("ssi", $file, $status, $id_pesanan);
        $update->execute();

        echo "<script>alert('Bukti pembayaran berhasil diupload!'); window.location='status_pesanan.php';</script>";
        exit;
    } else {
        echo "<script>alert('Upload gagal. Coba lagi.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Detail Pembayaran</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<!-- Modal Bukti Pembayaran -->
<div class="modal fade" id="modalBukti" tabindex="-1" aria-labelledby="modalBuktiLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="modalBuktiLabel">Bukti Pembayaran</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center">
        <img id="gambarBukti" src="" alt="Bukti Pembayaran" class="img-fluid rounded shadow">
      </div>
    </div>
  </div>
</div>

<div class="container mt-5">
  <div class="card shadow-sm">
    <div class="card-header bg-success text-white">
      <h4>Detail Pembayaran</h4>
    </div>
    <div class="card-body">
      <table class="table table-bordered">
        <tr>
          <th>Nama Kamar</th>
          <td><?= htmlspecialchars($data['nama_kamar']); ?></td>
        </tr>
        <tr>
          <th>Harga per Bulan</th>
          <td>Rp<?= number_format($data['harga'], 0, ',', '.'); ?>/<?= $data['satuan']; ?></td>
        </tr>
        <tr>
          <th>Durasi</th>
          <td><?= $data['durasi_bulan']; ?> bulan</td>
        </tr>
        <tr class="table-success">
          <th>Total Pembayaran</th>
          <td><strong>Rp<?= number_format($total, 0, ',', '.'); ?></strong></td>
        </tr>
        <tr>
          <th>Status</th>
          <td>
            <?php
              $badge = 'secondary';
              if ($data['status'] == 'konfirmasi') $badge = 'warning';
              elseif ($data['status'] == 'menunggu') $badge = 'info';
              elseif ($data['status'] == 'disetujui') $badge = 'success';
              elseif ($data['status'] == 'ditolak') $badge = 'danger';
            ?>
            <span class="badge bg-<?= $badge; ?>"><?= ucfirst($data['status']); ?></span>
          </td>
        </tr>
        <tr>
          <th>Rekening Pembayaran</th>
          <td>
            <div class="row">
              <?php
              $rekening_result = $koneksi->query("SELECT * FROM tbl_rekening");
              while ($rek = $rekening_result->fetch_assoc()):
              ?>
              <div class="col-md-6 col-lg-4 mb-3">
                <div class="card h-100 border-success">
                  <div class="card-body">
                    <h5 class="card-title text-success"><?= htmlspecialchars($rek['nama_bank']); ?></h5>
                    <p class="card-text mb-1">
                      <strong><?= htmlspecialchars($rek['no_rekening']); ?></strong>
                    </p>
                    <p class="card-text mb-0">
                      Nama : <em><?= htmlspecialchars($rek['atas_nama']); ?></em>
                    </p>
                  </div>
                </div>
              </div>
              <?php endwhile; ?>
            </div>
          </td>
        </tr>
      </table>

      <?php if (empty($data['bukti_pembayaran'])): ?>
        <h5 class="mt-4">Upload Bukti Pembayaran</h5>
        <form method="POST" enctype="multipart/form-data">
          <div class="mb-3">
            <input type="file" name="bukti" class="form-control" required>
          </div>
          <button type="submit" name="upload" class="btn btn-primary">Upload Bukti</button>
          <a href="status_pesanan.php" class="btn btn-secondary">Kembali</a>
        </form>
      <?php else: ?>
        <h5 class="mt-4">Bukti Telah Diupload</h5>
        <button class="btn btn-outline-success" onclick="tampilkanBukti('../uploads/<?= $data['bukti_pembayaran']; ?>')">Lihat Bukti Pembayaran</button>
        <a href="status_pesanan.php" class="btn btn-secondary">Kembali</a>
      <?php endif; ?>

    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  function tampilkanBukti(gambar) {
    document.getElementById('gambarBukti').src = gambar;
    var modal = new bootstrap.Modal(document.getElementById('modalBukti'));
    modal.show();
  }
</script>

</body>
</html>

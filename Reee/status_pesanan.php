<?php 
session_start();
include "../koneksi.php";

// Validasi role penyewa
if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'penyewa') {
    header("Location: ../index.php");
    exit;
}

$id_user = $_SESSION['id_user'];

// Hapus bukti pembayaran
if (isset($_POST['hapus_bukti'])) {
    $id_pesanan = $_POST['id_pesanan'];
    $stmt = $koneksi->prepare("SELECT bukti_pembayaran FROM tbl_pesanan WHERE id_pesanan = ? AND id_user = ?");
    $stmt->bind_param("ii", $id_pesanan, $id_user);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($data = $result->fetch_assoc()) {
        $file_path = "../uploads/" . $data['bukti_pembayaran'];
        if (file_exists($file_path)) unlink($file_path);
        $update = $koneksi->prepare("UPDATE tbl_pesanan SET bukti_pembayaran = NULL, status = 'bayar' WHERE id_pesanan = ? AND id_user = ?");
        $update->bind_param("ii", $id_pesanan, $id_user);
        $update->execute();
    }
    echo "<script>alert('Bukti pembayaran berhasil dihapus.'); window.location='status_pesanan.php';</script>";
    exit;
}

// Hapus pesanan
if (isset($_POST['hapus_pesanan'])) {
    $id_pesanan = $_POST['id_pesanan'];
    $cek = $koneksi->prepare("SELECT bukti_pembayaran FROM tbl_pesanan WHERE id_pesanan = ? AND id_user = ?");
    $cek->bind_param("ii", $id_pesanan, $id_user);
    $cek->execute();
    $res = $cek->get_result();
    if ($data = $res->fetch_assoc()) {
        if (!empty($data['bukti_pembayaran'])) {
            $file = "../uploads/" . $data['bukti_pembayaran'];
            if (file_exists($file)) unlink($file);
        }
    }
    $hapus = $koneksi->prepare("DELETE FROM tbl_pesanan WHERE id_pesanan = ? AND id_user = ?");
    $hapus->bind_param("ii", $id_pesanan, $id_user);
    $hapus->execute();

    echo "<script>alert('Data pesanan berhasil dihapus.'); window.location='status_pesanan.php';</script>";
    exit;
}

// Ambil data pesanan user
$query = $koneksi->prepare("SELECT p.*, k.nama_kamar, k.harga, k.satuan 
                            FROM tbl_pesanan p 
                            JOIN tbl_kamar k ON p.id_kamar = k.id_kamar 
                            WHERE p.id_user = ? 
                            ORDER BY p.tanggal_pesan DESC");
$query->bind_param("i", $id_user);
$query->execute();
$result = $query->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Status Pesanan - ANATHEMA Kost</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f1f3f5;
        }
        .badge-status {
            font-size: 0.85rem;
            padding: 0.4em 0.65em;
        }
        .table th, .table td {
            vertical-align: middle;
        }
    </style>
</head>
<body>
    <div class="container mt-5 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">Status Pesanan Kamar Anda</h5>
            </div>
            <div class="card-body">
                <?php if ($result->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-dark text-center">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Kamar</th>
                                    <th>Harga</th>
                                    <th>Durasi</th>
                                    <th>Total Bayar</th>
                                    <th>Tanggal Masuk</th>
                                    <th>Tanggal Berakhir</th>
                                    <th>Status</th>
                                    <th>Pembayaran</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                                    <?php
                                        $tanggal_masuk = $row['tanggal_pesan'] ? date('d-m-Y', strtotime($row['tanggal_pesan'])) : '-';
                                        $tanggal_berakhir = $row['tanggal_berakhir'] ? date('d-m-Y', strtotime($row['tanggal_berakhir'])) : '-';
                                        $total_bayar = $row['harga'] * $row['durasi_bulan'];
                                    ?>
                                    <tr class="text-center">
                                        <td><?= $no++; ?></td>
                                        <td class="text-start"><?= htmlspecialchars($row['nama_kamar']); ?></td>
                                        <td class="text-start">Rp<?= number_format($row['harga'], 0, ',', '.'); ?><?= $row['satuan'] ? '/' . $row['satuan'] : ''; ?></td>
                                        <td><?= $row['durasi_bulan']; ?> bulan</td>
                                        <td>Rp<?= number_format($total_bayar, 0, ',', '.'); ?></td>
                                        <td><?= $tanggal_masuk; ?></td>
                                        <td><?= $tanggal_berakhir; ?></td>
                                        <td>
                                            <span class="badge badge-status 
                                                <?= $row['status'] == 'disetujui' ? 'bg-success' : 
                                                    ($row['status'] == 'ditolak' ? 'bg-danger' : 
                                                    ($row['status'] == 'bayar' ? 'bg-info text-dark' : 'bg-warning text-dark')); ?>">
                                                <?= ucfirst($row['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($row['status'] == 'bayar' && !$row['bukti_pembayaran']): ?>
                                                <a href="pembayaran.php?id_pesanan=<?= $row['id_pesanan']; ?>" class="btn btn-sm btn-success">Upload</a>
                                            <?php elseif ($row['bukti_pembayaran']): ?>
                                                <button class="btn btn-sm btn-info" onclick="tampilkanBukti('../uploads/<?= $row['bukti_pembayaran']; ?>', <?= $row['id_pesanan']; ?>)">Lihat</button>
                                            <?php else: ?>
                                                <em>-</em>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (in_array($row['status'], ['menunggu', 'ditolak'])): ?>
                                                <form method="post" class="d-inline" onsubmit="return confirm('Hapus pesanan ini?')">
                                                    <input type="hidden" name="id_pesanan" value="<?= $row['id_pesanan']; ?>">
                                                    <button type="submit" name="hapus_pesanan" class="btn btn-sm btn-danger">Hapus</button>
                                                </form>
                                            <?php elseif ($row['status'] == 'bayar' && $row['bukti_pembayaran']): ?>
                                                <form method="post" class="d-inline" onsubmit="return confirm('Hapus bukti pembayaran ini?')">
                                                    <input type="hidden" name="id_pesanan" value="<?= $row['id_pesanan']; ?>">
                                                    <button type="submit" name="hapus_bukti" class="btn btn-sm btn-warning">Hapus Bukti</button>
                                                </form>
                                            <?php else: ?>
                                                <em>-</em>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info text-center">Belum ada pesanan kamar.</div>
                <?php endif; ?>
                <a href="dashboard_penyewa.php" class="btn btn-secondary mt-3">← Kembali ke Dashboard</a>
            </div>
        </div>
    </div>

    <!-- Modal Bukti -->
    <div class="modal fade" id="modalBukti" tabindex="-1" aria-labelledby="modalBuktiLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-sm">
                <div class="card">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Bukti Pembayaran</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="card-body text-center">
                        <img id="gambarBukti" src="" alt="Bukti Pembayaran" class="img-fluid rounded shadow mb-3">
                        <form method="post" onsubmit="return confirm('Yakin ingin menghapus bukti ini?')">
                            <input type="hidden" name="id_pesanan" id="id_pesanan_hapus">
                            <button type="submit" name="hapus_bukti" class="btn btn-danger">Hapus Bukti</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script Modal -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function tampilkanBukti(src, idPesanan) {
            document.getElementById('gambarBukti').src = src;
            document.getElementById('id_pesanan_hapus').value = idPesanan;
            const modal = new bootstrap.Modal(document.getElementById('modalBukti'));
            modal.show();
        }
    </script>
</body>
</html>

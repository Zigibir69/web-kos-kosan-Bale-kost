<?php
session_start();
include "../koneksi.php";

// Cek login dan role admin
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

// Proses Tambah Rekening
if (isset($_POST['tambah'])) {
    $nama_bank = $_POST['nama_bank'];
    $no_rekening = $_POST['no_rekening'];
    $atas_nama = $_POST['atas_nama'];

    $koneksi->query("INSERT INTO tbl_rekening (nama_bank, no_rekening, atas_nama) VALUES ('$nama_bank', '$no_rekening', '$atas_nama')");
    header("Location: rekening.php");
    exit;
}

// Proses Edit Rekening
if (isset($_POST['edit'])) {
    $id = $_POST['id_rekening'];
    $nama_bank = $_POST['nama_bank'];
    $no_rekening = $_POST['no_rekening'];
    $atas_nama = $_POST['atas_nama'];

    $koneksi->query("UPDATE tbl_rekening SET nama_bank='$nama_bank', no_rekening='$no_rekening', atas_nama='$atas_nama' WHERE id_rekening=$id");
    header("Location: rekening.php");
    exit;
}

// Proses Hapus Rekening
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $koneksi->query("DELETE FROM tbl_rekening WHERE id_rekening=$id");
    header("Location: rekening.php");
    exit;
}

// Ambil data rekening
$data = $koneksi->query("SELECT * FROM tbl_rekening ORDER BY id_rekening DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Rekening</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Daftar Rekening</h3>
        <a href="admin_pesanan.php" class="btn btn-secondary"><i class="fa fa-arrow-left"></i> Kembali</a>
    </div>

    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalTambah">
        <i class="fa fa-plus"></i> Tambah Rekening
    </button>

    <table class="table table-bordered table-striped">
        <thead class="table-dark text-center">
            <tr>
                <th>No</th>
                <th>Nama Bank</th>
                <th>No Rekening</th>
                <th>Atas Nama</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody class="text-center">
            <?php $no = 1; while ($row = $data->fetch_assoc()): ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= htmlspecialchars($row['nama_bank']); ?></td>
                    <td><?= htmlspecialchars($row['no_rekening']); ?></td>
                    <td><?= htmlspecialchars($row['atas_nama']); ?></td>
                    <td>
                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $row['id_rekening']; ?>"><i class="fa fa-edit"></i></button>
                        <a href="?hapus=<?= $row['id_rekening']; ?>" onclick="return confirm('Hapus rekening ini?')" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></a>
                    </td>
                </tr>

                <!-- Modal Edit -->
                <div class="modal fade" id="modalEdit<?= $row['id_rekening']; ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <form method="POST">
                            <input type="hidden" name="id_rekening" value="<?= $row['id_rekening']; ?>">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Rekening</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-2">
                                        <label>Nama Bank</label>
                                        <input type="text" name="nama_bank" class="form-control" value="<?= htmlspecialchars($row['nama_bank']); ?>" required>
                                    </div>
                                    <div class="mb-2">
                                        <label>No Rekening</label>
                                        <input type="text" name="no_rekening" class="form-control" value="<?= htmlspecialchars($row['no_rekening']); ?>" required>
                                    </div>
                                    <div class="mb-2">
                                        <label>Atas Nama</label>
                                        <input type="text" name="atas_nama" class="form-control" value="<?= htmlspecialchars($row['atas_nama']); ?>" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" name="edit" class="btn btn-success">Simpan Perubahan</button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Rekening</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <label>Nama Bank</label>
                        <input type="text" name="nama_bank" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label>No Rekening</label>
                        <input type="text" name="no_rekening" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label>Atas Nama</label>
                        <input type="text" name="atas_nama" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="tambah" class="btn btn-primary">Tambah</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

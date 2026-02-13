<?php
session_start();
include "../koneksi.php";

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

$id = $_GET['id'];
$query = $koneksi->prepare("SELECT * FROM tbl_rekening WHERE id_rekening = ?");
$query->bind_param("i", $id);
$query->execute();
$data = $query->get_result()->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_bank = trim($_POST['nama_bank']);
    $no_rekening = trim($_POST['no_rekening']);
    $atas_nama = trim($_POST['atas_nama']);

    $update = $koneksi->prepare("UPDATE tbl_rekening SET nama_bank=?, no_rekening=?, atas_nama=? WHERE id_rekening=?");
    $update->bind_param("sssi", $nama_bank, $no_rekening, $atas_nama, $id);
    $update->execute();

    echo "<script>alert('Data rekening berhasil diupdate!'); window.location='admin_pesanan.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Rekening</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-warning text-white">
            <h4>Edit Rekening</h4>
        </div>
        <div class="card-body">
            <form method="post">
                <div class="mb-3">
                    <label>Nama Bank</label>
                    <input type="text" name="nama_bank" class="form-control" value="<?= htmlspecialchars($data['nama_bank']) ?>" required>
                </div>
                <div class="mb-3">
                    <label>No Rekening</label>
                    <input type="text" name="no_rekening" class="form-control" value="<?= htmlspecialchars($data['no_rekening']) ?>" required>
                </div>
                <div class="mb-3">
                    <label>Atas Nama</label>
                    <input type="text" name="atas_nama" class="form-control" value="<?= htmlspecialchars($data['atas_nama']) ?>" required>
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="admin_pesanan.php" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>

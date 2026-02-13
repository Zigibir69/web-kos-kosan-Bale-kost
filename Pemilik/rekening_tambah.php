<?php
session_start();
include "../koneksi.php";

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_bank = trim($_POST['nama_bank']);
    $no_rekening = trim($_POST['no_rekening']);
    $atas_nama = trim($_POST['atas_nama']);

    $stmt = $koneksi->prepare("INSERT INTO tbl_rekening (nama_bank, no_rekening, atas_nama) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nama_bank, $no_rekening, $atas_nama);
    $stmt->execute();

    echo "<script>alert('Rekening berhasil ditambahkan!'); window.location='admin_pesanan.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Rekening</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4>Tambah Rekening</h4>
        </div>
        <div class="card-body">
            <form method="post">
                <div class="mb-3">
                    <label>Nama Bank</label>
                    <input type="text" name="nama_bank" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>No Rekening</label>
                    <input type="text" name="no_rekening" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Atas Nama</label>
                    <input type="text" name="atas_nama" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-success">Simpan</button>
                <a href="admin_pesanan.php" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>

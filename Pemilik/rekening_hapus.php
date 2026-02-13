<?php
session_start();
include "../koneksi.php";

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $hapus = $koneksi->prepare("DELETE FROM tbl_rekening WHERE id_rekening = ?");
    $hapus->bind_param("i", $id);
    $hapus->execute();

    echo "<script>alert('Rekening berhasil dihapus.'); window.location='admin_pesanan.php';</script>";
}
?>

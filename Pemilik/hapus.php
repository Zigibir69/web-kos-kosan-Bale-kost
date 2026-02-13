<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$id = $_GET['id'];
$koneksi->query("DELETE FROM tbl_kamar_fasilitas WHERE id_kamar = '$id'");
$koneksi->query("DELETE FROM tbl_kamar WHERE id_kamar = '$id'");

header("Location: kelola_kos.php");
exit;
?>

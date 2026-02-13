<?php
session_start();
include "../koneksi.php";

// Validasi hanya admin
if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

if (isset($_GET['id'])) {
    $id_user = $_GET['id'];

    // Pastikan user yang akan dihapus adalah penyewa
    $cek = $koneksi->prepare("SELECT * FROM tbl_user WHERE id_user = ? AND role = 'penyewa'");
    $cek->bind_param("i", $id_user);
    $cek->execute();
    $result = $cek->get_result();

    if ($result->num_rows > 0) {
        $hapus = $koneksi->prepare("DELETE FROM tbl_user WHERE id_user = ?");
        $hapus->bind_param("i", $id_user);
        if ($hapus->execute()) {
            $_SESSION['pesan'] = "Akun penyewa berhasil dihapus.";
        } else {
            $_SESSION['pesan'] = "Gagal menghapus akun.";
        }
    } else {
        $_SESSION['pesan'] = "Data tidak ditemukan.";
    }
}

header("Location: kelola_penyewa.php");
exit;

<?php  
session_start();
include "koneksi.php";

// Cegah akses langsung tanpa POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header("Location: index.php");
  exit;
}

// Ambil data dari form login
$nama = trim($_POST['nama']);
$password = trim($_POST['password']);

// Validasi input
if (empty($nama) || empty($password)) {
  header("Location: index.php?error=Isi semua field");
  exit;
}

// Cek user
$query = $koneksi->prepare("SELECT * FROM tbl_user WHERE nama = ?");
$query->bind_param("s", $nama);
$query->execute();
$result = $query->get_result();

if ($result->num_rows === 1) {
  $user = $result->fetch_assoc();

  if (password_verify($password, $user['password'])) {
    // Set session
    $_SESSION['id_user'] = $user['id_user'];
    $_SESSION['nama'] = $user['nama'];
    $_SESSION['role'] = $user['role'];

    // Redirect berdasarkan role
    if ($user['role'] === 'admin') {
      header("Location: Pemilik/admin_dashboard.php");
    } elseif ($user['role'] === 'penyewa') {
      header("Location: penyewa/dashboard_penyewa.php");
    } else {
      header("Location: index.php?error=Role tidak dikenali");
    }
    exit;
  } else {
    header("Location: index.php?error=Password salah");
    exit;
  }
} else {
  header("Location: index.php?error=Nama tidak ditemukan");
  exit;
}

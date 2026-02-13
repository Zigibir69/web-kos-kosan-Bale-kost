<?php 
session_start();
include "../koneksi.php";

if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit;
}

$id_user = $_SESSION['id_user'];
$query = mysqli_query($koneksi, "SELECT * FROM tbl_user WHERE id_user = '$id_user'");
$user = mysqli_fetch_assoc($query);
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = trim($_POST['nama']);
    $nama_lengkap = trim($_POST['nama_lengkap']);
    $no_telepon = trim($_POST['no_telepon']);
    $alamat = trim($_POST['alamat']);
    $hapus_foto = isset($_POST['hapus_foto']);
    $password_baru = trim($_POST['password_baru']);
    $password_konfirmasi = trim($_POST['password_konfirmasi']);

    $foto = $user['foto'] ?? 'default.png';

    // Hapus foto jika diminta
    if ($hapus_foto && $foto !== 'default.png' && file_exists("../uploads/$foto")) {
        unlink("../uploads/$foto");
        $foto = 'default.png';
    }

    // Upload foto baru jika ada
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
        $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($ext, $allowed)) {
            $filename = "foto_" . time() . "." . $ext;
            $path = "../uploads/" . $filename;

            if (move_uploaded_file($_FILES['foto']['tmp_name'], $path)) {
                if ($foto !== 'default.png' && file_exists("../uploads/$foto")) {
                    unlink("../uploads/$foto");
                }
                $foto = $filename;
            }
        }
    }

    // Validasi dan ubah password jika dimasukkan
    $password_sql = "";
    if (!empty($password_baru)) {
        if ($password_baru !== $password_konfirmasi) {
            $error = "Konfirmasi password tidak cocok!";
        } elseif (strlen($password_baru) < 5) {
            $error = "Password minimal 5 karakter!";
        } else {
            $hashed = password_hash($password_baru, PASSWORD_DEFAULT);
            $password_sql = ", password='$hashed'";
        }
    }

    if (!$error) {
        $update = mysqli_query($koneksi, "UPDATE tbl_user SET 
            nama='$nama', 
            nama_lengkap='$nama_lengkap', 
            no_telepon='$no_telepon', 
            alamat='$alamat', 
            foto='$foto'
            $password_sql 
            WHERE id_user='$id_user'");

        if ($update) {
            $_SESSION['pesan'] = "Profil berhasil diperbarui.";
            header("Location: profil.php");
            exit;
        } else {
            $error = "Gagal menyimpan perubahan: " . mysqli_error($koneksi);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Profil</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .profil-preview {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 10px;
            border: 3px solid #0d6efd;
        }
        body {
            animation: waveGradient 15s linear infinite;
            background: linear-gradient(90deg, #ff00cc, #3333ff, #00ffcc, #ffcc00, #ff0066);
            background-size: 300% 300%;
            min-height: 100vh;
            margin: 0;
        }
        @keyframes waveGradient {
            0% { background-position: 0% 0%; }
            100% { background-position: 300% 0%; }
        }
        .profile-card {
            border: none;
            background: rgba(255, 255, 255, 0.85);
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            padding: 30px;
            backdrop-filter: blur(6px);
        }
        .profile-img {
            width: 220px;
            height: 220px;
            object-fit: cover;
            border-radius: 50%;
            border: 5px solid #0d6efd;
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-top: 25px;
            color: #222;
            border-bottom: 2px solid #0d6efd;
            display: inline-block;
            padding-bottom: 5px;
        }
    </style>
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card shadow col-md-6 mx-auto">
        <div class="card-header bg-info text-white">
            <h5>Edit Profil</h5>
        </div>
        <form method="POST" enctype="multipart/form-data">
            <div class="card-body">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <div class="text-center mb-3">
                    <img src="../uploads/<?= htmlspecialchars($user['foto'] ?: 'default.png') ?>" class="profil-preview" alt="Foto Profil">
                </div>

                <div class="mb-3">
                    <label for="foto" class="form-label">Ganti Foto Profil</label>
                    <input type="file" name="foto" class="form-control">
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" name="hapus_foto" id="hapus_foto">
                    <label class="form-check-label" for="hapus_foto">Hapus Foto Profil</label>
                </div>

                <div class="mb-3">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" class="form-control" value="<?= htmlspecialchars($user['nama_lengkap']) ?>" required>
                </div>
                <div class="mb-3">
                    <label>Username</label>
                    <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($user['nama']) ?>" required>
                </div>
                <div class="mb-3">
                    <label>No Telepon</label>
                    <input type="text" name="no_telepon" class="form-control" value="<?= htmlspecialchars($user['no_telepon']) ?>" required>
                </div>
                <div class="mb-3">
                    <label>Alamat</label>
                    <textarea name="alamat" class="form-control" required><?= htmlspecialchars($user['alamat']) ?></textarea>
                </div>

                <hr class="my-4">
                <h6 class="text-primary">Ubah Password (opsional)</h6>

                <div class="mb-3">
                    <label>Password Baru</label>
                    <input type="password" name="password_baru" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah">
                </div>
                <div class="mb-3">
                    <label>Konfirmasi Password Baru</label>
                    <input type="password" name="password_konfirmasi" class="form-control" placeholder="Ulangi password baru">
                </div>
            </div>
            <div class="card-footer text-end">
                <a href="profil.php" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-success">Simpan</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>

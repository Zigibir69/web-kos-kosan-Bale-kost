<?php  
// Koneksi ke database
$koneksi = new mysqli("localhost", "root", "", "bale_kos");

// Tambah fasilitas
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = trim($_POST['nama_fasilitas']);
    $icon = trim($_POST['icon_fasilitas']);

    // Cek duplikat
    $cek = $koneksi->prepare("SELECT * FROM tbl_master_fasilitas WHERE nama_fasilitas = ?");
    $cek->bind_param("s", $nama);
    $cek->execute();
    $cek_result = $cek->get_result();

    if ($cek_result->num_rows > 0) {
        $error = "Fasilitas dengan nama tersebut sudah ada!";
    } else {
        $stmt = $koneksi->prepare("INSERT INTO tbl_master_fasilitas (nama_fasilitas, icon_fasilitas) VALUES (?, ?)");
        $stmt->bind_param("ss", $nama, $icon);
        if ($stmt->execute()) {
            $success = "Fasilitas berhasil ditambahkan!";
        } else {
            $error = "Gagal menambahkan fasilitas!";
        }
    }
}

// Hapus fasilitas
if (isset($_GET['hapus'])) {
    $id = (int) $_GET['hapus'];
    $hapus = $koneksi->query("DELETE FROM tbl_master_fasilitas WHERE id_fasilitas = $id");
    if ($hapus) {
        $success = "Fasilitas berhasil dihapus!";
    } else {
        $error = "Gagal menghapus fasilitas!";
    }
}

// Ambil data fasilitas
$data_fasilitas = $koneksi->query("SELECT * FROM tbl_master_fasilitas ORDER BY id_fasilitas DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Fasilitas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="container my-5">

    <h3 class="mb-4">✨ Tambah Fasilitas Baru</h3>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error; ?></div>
    <?php endif; ?>
    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?= $success; ?></div>
    <?php endif; ?>

    <form method="POST" class="border p-3 rounded shadow-sm bg-light mb-4">
        <div class="mb-3">
            <label class="form-label">Nama Fasilitas</label>
            <input type="text" name="nama_fasilitas" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Icon Fasilitas (class Font Awesome / Bootstrap Icon)</label>
            <input type="text" name="icon_fasilitas" class="form-control" placeholder="cth: fa-solid fa-bath atau bi bi-wifi" required>
            <small class="text-muted">
                Lihat ikon di 
                <a href="https://fontawesome.com/icons" target="_blank">Font Awesome</a> dan 
                <a href="https://icons.getbootstrap.com" target="_blank">Bootstrap Icons</a>
            </small>
        </div>
        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan</button>
    </form>

    <h4 class="mb-3">📋 Daftar Fasilitas</h4>
    <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
            <tr>
                <th width="5%">#</th>
                <th>Nama Fasilitas</th>
                <th width="30%">Icon</th>
                <th width="15%">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; while ($fas = $data_fasilitas->fetch_assoc()): ?>
                <?php
                    $icon = $fas['icon_fasilitas'];
                    $icon_html = '';

                    // Tampilkan icon jika cocok prefix-nya
                    if (str_starts_with($icon, 'fa')) {
                        $icon_html = '<i class="' . htmlspecialchars($icon) . '" style="font-size: 1.5rem;"></i>';
                    } elseif (str_starts_with($icon, 'bi')) {
                        $icon_html = '<i class="' . htmlspecialchars($icon) . '" style="font-size: 1.5rem;"></i>';
                    } else {
                        $icon_html = '<span class="text-muted">-</span>';
                    }
                ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= htmlspecialchars($fas['nama_fasilitas']); ?></td>
                    <td><?= $icon_html ?> <small class="text-muted"><?= htmlspecialchars($icon); ?></small></td>
                    <td>
                        <a href="?hapus=<?= $fas['id_fasilitas']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus fasilitas ini?')">
                            <i class="bi bi-trash"></i> Hapus
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div class="mt-4">
        <a href="kelola_kos.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali ke Pesanan
        </a>
    </div>

</body>
</html>

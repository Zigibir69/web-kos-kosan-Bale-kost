<?php
include "../koneksi.php";

// Ambil parameter bulan & tahun dari GET
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

// Header agar browser download sebagai Word
header("Content-type: application/vnd.ms-word");
header("Content-Disposition: attachment;Filename=Laporan_Pemesanan_Kamar_{$bulan}_{$tahun}.doc");

// Query
$sql = "SELECT 
            u.nama, u.nama_lengkap, u.no_telepon, u.alamat,
            k.nama_kamar,
            p.tanggal_masuk, p.tanggal_berakhir,
            (p.durasi_bulan * k.harga) AS jumlah_bayar
        FROM tbl_pesanan p
        JOIN tbl_user u ON p.id_user = u.id_user
        JOIN tbl_kamar k ON p.id_kamar = k.id_kamar
        WHERE MONTH(p.tanggal_masuk) = ? AND YEAR(p.tanggal_masuk) = ?
        AND p.status = 'disetujui'
        ORDER BY p.tanggal_masuk ASC";

$stmt = $koneksi->prepare($sql);
if (!$stmt) {
    die("Query error: " . $koneksi->error);
}
$stmt->bind_param("ss", $bulan, $tahun);
$stmt->execute();
$result = $stmt->get_result();
?>

<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Pemesanan Kamar</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            color: #000;
        }
        h2 {
            text-align: center;
            margin-bottom: 25px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            font-size: 12pt;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
        }
        th {
            background-color: #ddd;
            text-align: center;
        }
        .total {
            font-weight: bold;
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

<h2>Laporan Pemesanan Kamar<br>Bulan <?= date('F', mktime(0, 0, 0, $bulan, 10)) ?> <?= $tahun ?></h2>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Username</th>
            <th>Nama Lengkap</th>
            <th>No Telepon</th>
            <th>Alamat</th>
            <th>Kamar Disewa</th>
            <th>Tanggal Masuk</th>
            <th>Tanggal Berakhir</th>
            <th>Jumlah Bayar</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        $total = 0;
        while ($row = $result->fetch_assoc()) :
            $total += $row['jumlah_bayar'];
        ?>
        <tr>
            <td align="center"><?= $no++ ?></td>
            <td><?= htmlspecialchars($row['nama']) ?></td>
            <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
            <td><?= htmlspecialchars($row['no_telepon']) ?></td>
            <td><?= htmlspecialchars($row['alamat']) ?></td>
            <td><?= htmlspecialchars($row['nama_kamar']) ?></td>
            <td><?= date('d-m-Y', strtotime($row['tanggal_masuk'])) ?></td>
            <td><?= date('d-m-Y', strtotime($row['tanggal_berakhir'])) ?></td>
            <td>Rp <?= number_format($row['jumlah_bayar'], 0, ',', '.') ?></td>
        </tr>
        <?php endwhile; ?>
        <tr class="total">
            <td colspan="8" align="right">Total Keseluruhan</td>
            <td><strong>Rp <?= number_format($total, 0, ',', '.') ?></strong></td>
        </tr>
    </tbody>
</table>

<br><br>
<table style="width: 100%; border: none;">
    <tr>
        <td style="text-align: right; border: none;">
            <p>Mataram, <?= date('d-m-Y') ?><br><br><br>_________________________<br>Admin BaleKost</p>
        </td>
    </tr>
</table>

</body>
</html>

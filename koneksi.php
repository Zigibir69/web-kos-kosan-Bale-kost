<?php
$koneksi = new mysqli("localhost", "root", "", "bale_kos");

if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}
?>

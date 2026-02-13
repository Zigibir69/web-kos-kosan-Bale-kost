<?php
session_start();
session_unset();  // Menghapus semua variabel sesi
session_destroy(); // Menghancurkan sesi

// Arahkan kembali ke halaman utama
header("Location: index.php");
exit;

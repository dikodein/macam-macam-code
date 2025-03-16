<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "toko_online_v1.0";

$koneksi = new mysqli($host, $user, $pass, $db);

if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

// Alias agar bisa dipakai dengan $conn juga
$conn = $koneksi;
?>

<?php
require "../koneksi.php";
require "session.php";

$id = $_GET['id'];

// Pastikan ID adalah angka untuk menghindari SQL Injection
if (!is_numeric($id)) {
    echo "<script>
        alert('ID tidak valid!');
        window.location='index.php';
    </script>";
    exit();
}

// Eksekusi query hapus
$query = $koneksi->prepare("DELETE FROM users WHERE id = ?");
$query->bind_param("i", $id);

if ($query->execute()) {
    echo "<script>
        alert('User berhasil dihapus!');
        window.location='index.php';
    </script>";
} else {
    echo "<script>
        alert('Gagal menghapus user: " . $koneksi->error . "');
        window.location='index.php';
    </script>";
}
?>

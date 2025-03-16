<?php
require "../koneksi.php";
require "session.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Cek apakah kategori ada
    $cek_query = $koneksi->prepare("SELECT id FROM kategori WHERE id = ?");
    $cek_query->bind_param("i", $id);
    $cek_query->execute();
    $cek_query->store_result();

    if ($cek_query->num_rows == 0) {
        echo "<script>
            alert('Kategori tidak ditemukan!');
            window.location='index.php';
        </script>";
        exit();
    }

    // Hapus kategori
    $query = $koneksi->prepare("DELETE FROM kategori WHERE id = ?");
    $query->bind_param("i", $id);

    if ($query->execute()) {
        echo "<script>
            alert('Kategori berhasil dihapus!');
            window.location='index.php';
        </script>";
    } else {
        echo "<script>
            alert('Gagal menghapus kategori: " . $koneksi->error . "');
            window.location='index.php';
        </script>";
    }
} else {
    echo "<script>
        alert('ID kategori tidak valid!');
        window.location='index.php';
    </script>";
}
?>

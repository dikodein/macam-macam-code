<?php
require "../koneksi.php";
require "session.php";

// Pastikan ada parameter ID di URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
    
    // Cek apakah produk ada dalam database
    $query = $koneksi->prepare("SELECT foto FROM produk WHERE id = ?");
    $query->bind_param("i", $id);
    $query->execute();
    $result = $query->get_result();
    
    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        $foto_path = "../" . $data['foto']; // Path foto produk
        
        // Hapus data dari database
        $delete_query = $koneksi->prepare("DELETE FROM produk WHERE id = ?");
        $delete_query->bind_param("i", $id);
        
        if ($delete_query->execute()) {
            // Hapus file foto jika ada
            if (!empty($data['foto']) && file_exists($foto_path)) {
                unlink($foto_path);
            }
            echo "<script>alert('Produk berhasil dihapus!'); window.location='index.php';</script>";
        } else {
            echo "<script>alert('Gagal menghapus produk!'); window.location='index.php';</script>";
        }
    } else {
        echo "<script>alert('Produk tidak ditemukan!'); window.location='index.php';</script>";
    }
} else {
    echo "<script>alert('ID tidak valid!'); window.location='index.php';</script>";
}
?>
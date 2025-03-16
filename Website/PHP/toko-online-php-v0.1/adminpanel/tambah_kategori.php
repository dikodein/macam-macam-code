<?php
require "../koneksi.php";
require "session.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = trim($_POST['nama']);

    // Cek apakah kategori sudah ada
    $cek_query = $koneksi->prepare("SELECT id FROM kategori WHERE nama = ?");
    $cek_query->bind_param("s", $nama);
    $cek_query->execute();
    $cek_query->store_result();

    if ($cek_query->num_rows > 0) {
        echo "<script>
            alert('Kategori sudah ada! Gunakan nama lain.');
            window.location='tambah_kategori.php';
        </script>";
        exit();
    }

    // Insert kategori baru jika belum ada
    $query = $koneksi->prepare("INSERT INTO kategori (nama) VALUES (?)");
    $query->bind_param("s", $nama);

    if ($query->execute()) {
        echo "<script>
            alert('Kategori berhasil ditambahkan!');
            window.location='index.php';
        </script>";
    } else {
        echo "<script>
            alert('Gagal menambahkan kategori: " . $koneksi->error . "');
            window.location='tambah_kategori.php';
        </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Kategori</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        background: linear-gradient(135deg, #1e3c72, #2a5298);
        color: #fff;
        font-family: 'Arial', sans-serif;
    }

    .container {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        max-width: 600px;
        margin: auto;
    }

    h2 {
        text-align: center;
        font-weight: bold;
    }

    .form-control, .form-select {
        background: rgba(255, 255, 255, 0.2);
        color: #fff;
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .form-control:focus, .form-select:focus {
        background: rgba(255, 255, 255, 0.3);
        border-color: #ff8c00;
        box-shadow: 0 0 5px #ff8c00;
    }

    .btn-primary {
        background-color: #ff8c00;
        border: none;
    }

    .btn-primary:hover {
        background-color: #e07b00;
    }

    .btn-secondary {
        background-color: rgba(255, 255, 255, 0.3);
        border: none;
    }

    .btn-secondary:hover {
        background-color: rgba(255, 255, 255, 0.5);
    }

    img {
        display: block;
        margin-top: 10px;
        border-radius: 8px;
    }

    .badge {
        font-size: 0.9em;
        padding: 5px 10px;
        border-radius: 8px;
    }
</style>

</head>
<body>
<div class="container mt-5">
    <h2>Tambah Kategori</h2>
    <form method="post">
        <div class="mb-3">
            <label class="form-label">Nama Kategori</label>
            <input type="text" name="nama" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Tambah</button>
        <a href="index.php" class="btn btn-secondary">Kembali</a>
    </form>
</div>
</body>
</html>

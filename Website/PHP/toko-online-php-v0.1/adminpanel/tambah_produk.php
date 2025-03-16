<?php
require "../koneksi.php";
require "session.php";

// Ambil daftar kategori dari database
$query_kategori = $koneksi->query("SELECT id, nama FROM kategori");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_produk = trim($_POST['nama_produk']);
    $harga = trim($_POST['harga']);
    $stok = $_POST['stok']; // "Tersedia" atau "Habis"
    $kategori_id = $_POST['kategori_id'];
    $detail = trim($_POST['detail']);
    $penjual_nama = trim($_POST['penjual_nama']);
    $whatsapp = trim($_POST['whatsapp']);

    // Validasi input tidak boleh kosong
    if (empty($nama_produk) || empty($harga) || empty($stok) || empty($kategori_id) || empty($detail) || empty($penjual_nama) || empty($whatsapp)) {
        echo "<script>alert('Semua kolom harus diisi!');</script>";
    } elseif (!preg_match('/^[0-9]+$/', $whatsapp)) {
        echo "<script>alert('Nomor WhatsApp hanya boleh berisi angka!');</script>";
    } else {
        // Konversi harga ke angka murni (hapus tanda titik)
        $harga = str_replace('.', '', $harga);

        // **UPLOAD FOTO**
        $foto_nama = $_FILES['foto']['name'];
        $foto_tmp = $_FILES['foto']['tmp_name'];
        $foto_size = $_FILES['foto']['size'];
        $foto_ext = strtolower(pathinfo($foto_nama, PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
        $foto_path = "";

        // Validasi jika ada file yang diupload
        if (!empty($foto_nama)) {
            if (in_array($foto_ext, $allowed_ext) && $foto_size <= 2097152) { // 2MB
                $foto_path = "uploads/" . time() . "_" . $foto_nama;
                move_uploaded_file($foto_tmp, "../" . $foto_path);
            } else {
                echo "<script>alert('Format gambar harus JPG, JPEG, PNG, atau GIF dan max 2MB!');</script>";
                exit;
            }
        }

        // Simpan data ke database
        $query = $koneksi->prepare("INSERT INTO produk (nama, harga, stok, kategori_id, foto, detail, penjual_nama, whatsapp) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $query->bind_param("sisissss", $nama_produk, $harga, $stok, $kategori_id, $foto_path, $detail, $penjual_nama, $whatsapp);

        if ($query->execute()) {
            echo "<script>
                alert('Produk berhasil ditambahkan!');
                window.location='index.php';
            </script>";
        } else {
            echo "<script>alert('Gagal menambahkan produk: " . $koneksi->error . "');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function formatRupiah(input) {
            let value = input.value.replace(/\D/g, ""); // Hanya angka
            value = new Intl.NumberFormat("id-ID").format(value); // Format angka ribuan
            input.value = value;
        }
    </script>
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
    <h2>Tambah Produk</h2>
    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Nama Produk</label>
            <input type="text" name="nama_produk" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Harga (IDR)</label>
            <input type="text" name="harga" class="form-control" oninput="formatRupiah(this)" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Stok</label>
            <select name="stok" class="form-control" required>
                <option value="Tersedia" selected>Tersedia</option>
                <option value="Habis">Habis</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Kategori</label>
            <select name="kategori_id" class="form-control" required>
                <option value="">Pilih Kategori</option>
                <?php while ($kategori = $query_kategori->fetch_assoc()): ?>
                    <option value="<?= $kategori['id'] ?>"><?= htmlspecialchars($kategori['nama']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Detail Produk</label>
            <textarea name="detail" class="form-control" rows="3" required></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Nama Penjual</label>
            <input type="text" name="penjual_nama" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Nomor WhatsApp</label>
            <input type="text" name="whatsapp" class="form-control" required>
            <small class="text-muted">Masukkan nomor tanpa spasi atau tanda lain, contoh: 081234567890</small>
        </div>
        <div class="mb-3">
            <label class="form-label">Foto Produk</label>
            <input type="file" name="foto" class="form-control" accept="image/*">
            <small class="text-muted">Format: JPG, JPEG, PNG, GIF. Max 2MB</small>
        </div>
        <button type="submit" class="btn btn-primary">Tambah</button>
        <a href="index.php" class="btn btn-secondary">Kembali</a>
    </form>
</div>
</body>
</html>

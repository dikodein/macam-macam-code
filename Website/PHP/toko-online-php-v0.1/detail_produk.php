<?php
require 'koneksi.php'; // Pastikan ada koneksi ke database

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("<div class='container mt-5'><div class='alert alert-danger'>Produk tidak ditemukan.</div></div>");
}

$id = intval($_GET['id']);
$query = $koneksi->prepare("SELECT p.*, k.nama as kategori_nama FROM produk p INNER JOIN kategori k ON p.kategori_id = k.id WHERE p.id = ?");
$query->bind_param("i", $id);
$query->execute();
$result = $query->get_result();
$produk = $result->fetch_assoc();

if (!$produk) {
    die("<div class='container mt-5'><div class='alert alert-danger'>Produk tidak ditemukan.</div></div>");
}

// Ambil data WhatsApp dari tabel produk
$wa_number = preg_replace('/[^0-9]/', '', $produk['whatsapp']);
$wa_message = urlencode("Halo, saya tertarik dengan produk \"{$produk['nama']}\" yang Anda jual. Bisa saya dapatkan informasi lebih lanjut?");
$wa_link = "https://wa.me/{$wa_number}?text={$wa_message}";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Produk - <?= htmlspecialchars($produk['nama']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    /* Background */
    body {
        background: linear-gradient(135deg, #000428, #004e92);
        color: #fff;
        font-family: Arial, sans-serif;
    }

    /* Navbar */
    .navbar {
        background-color: rgba(0, 0, 0, 0.6) !important;
        backdrop-filter: blur(10px);
        padding: 15px;
    }

    .navbar-brand {
        color: #fff !important;
        font-weight: bold;
        font-size: 1.5rem;
    }

    /* Form Pencarian */
    .form-control {
        background: rgba(255, 255, 255, 0.1);
        border: none;
        color: white;
        backdrop-filter: blur(5px);
    }

    .form-control::placeholder {
        color: rgba(255, 255, 255, 0.6);
    }

    .btn-primary {
        background-color: #ff8b00;
        border: none;
        transition: 0.3s;
    }

    .btn-primary:hover {
        background-color: #ff3300;
    }

    /* Card Produk */
    .card {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        color: white;
        backdrop-filter: blur(10px);
        transition: transform 0.3s ease;
        overflow: hidden;
    }

    .card:hover {
        transform: scale(1.05);
    }

    .card-img-top {
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
        transition: 0.3s;
    }

    .card-img-top:hover {
        filter: brightness(90%);
    }

    .card-title {
        font-weight: bold;
    }

    .badge {
        font-size: 0.9rem;
    }

    /* Pagination */
    .pagination .page-item .page-link {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        border: none;
        transition: 0.3s;
    }

    .pagination .page-item.active .page-link {
        background: #ff8b00;
        border-radius: 5px;
    }

    .pagination .page-item .page-link:hover {
        background: #ff3300;
    }

</style>

</head>
<body>
<nav class="navbar navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="index.php">Toko Online</a>
    </div>
</nav>

<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="row g-0">
            <div class="col-md-6">
                <img src="<?= htmlspecialchars($produk['foto']); ?>" class="img-fluid rounded-start" alt="<?= htmlspecialchars($produk['nama']); ?>">
            </div>
            <div class="col-md-6">
                <div class="card-body">
                    <h3 class="card-title"> <?= htmlspecialchars($produk['nama']); ?> </h3>
                    <p class="text-muted"><span class="badge bg-primary"> <?= htmlspecialchars($produk['kategori_nama']); ?> </span></p>
                    <h4 class="text-primary">Rp <?= number_format($produk['harga'], 0, ',', '.'); ?></h4>
                    <p class="card-text mt-3"> <?= nl2br(htmlspecialchars($produk['detail'])); ?> </p>
                    <p class="card-text"><span class="badge bg-warning"><b style="color:gray;"><?= htmlspecialchars($produk['penjual_nama']); ?></b></span></p>
                    <a href="<?= $wa_link; ?>" class="btn btn-success" target="_blank">Hubungi Penjual via WhatsApp</a>
                    <a href="produk_search.php" class="btn btn-secondary">Kembali</a>
                </div>
            </div>
        </div>
    </div>
</div>
<footer>
    <div class="footer-container">
        <div class="footer-about">
            <h3>Toko Online</h3>
            <p>Toko murah dan serba ada.</p>
        </div>
        <div class="footer-links">
            <h3>Link Cepat</h3>
            <ul>
                <li><a href="#about">Tentang Kami</a></li>
                <li><a href="#services">Layanan</a></li>
                <li><a href="#contact">Kontak</a></li>
                <li><a href="#">Discord</a></li>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; 2025 Toko Online. All Rights Reserved.</p>
    </div>
</footer>

<style>
footer {
    background: rgba(0, 0, 0, 0.7);
    color: #fff;
    text-align: center;
    padding: 20px 0;
    font-size: 14px;
    margin-top:30px;
}

.footer-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-around;
    padding: 20px;
}

.footer-container div {
    margin: 10px;
    max-width: 300px;
}

.footer-links ul {
    list-style: none;
    padding: 0;
}

.footer-links ul li {
    margin: 5px 0;
}

.footer-links ul li a {
    color: #fff;
    text-decoration: none;
}

.social-icons a img {
    width: 30px;
    margin: 5px;
    transition: transform 0.3s;
}

.social-icons a img:hover {
    transform: scale(1.2);
}

.footer-bottom {
    border-top: 1px solid rgba(255, 255, 255, 0.2);
    padding-top: 10px;
    margin-top: 10px;
}

@media (max-width: 768px) {
    .footer-container {
        flex-direction: column;
        align-items: center;
    }
}
</style>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

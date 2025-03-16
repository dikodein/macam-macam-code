<?php
require "koneksi.php";

// Ambil daftar kategori
$kategori_result = $koneksi->query("SELECT * FROM kategori");

// Ambil produk terbaru
$query_produk = "SELECT produk.*, kategori.nama as kategori_nama FROM produk 
                 JOIN kategori ON produk.kategori_id = kategori.id 
                 WHERE produk.stok = 'Tersedia' 
                 ORDER BY produk.id DESC LIMIT 6";
$produk_query = $koneksi->query($query_produk);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda - Toko Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        background: linear-gradient(135deg, #000428, #004e92);
        color: #fff;
        font-family: Arial, sans-serif;
    }

    .navbar {
        background-color: rgba(0, 0, 0, 0.6) !important;
        backdrop-filter: blur(10px);
    }

    .navbar-brand {
        color: #fff !important;
        font-weight: bold;
    }

    .card {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        color: white;
        backdrop-filter: blur(10px);
        transition: transform 0.3s ease;
    }

    .card:hover {
        transform: scale(1.05);
    }

    .card-img-top {
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
    }

    .btn-primary {
        background-color: #ff8b00;
        border: none;
    }

    .btn-primary:hover {
        background-color: #ff3300;
    }

    .pagination .page-item .page-link {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        border: none;
    }

    .pagination .page-item.active .page-link {
        background: #ff8b00;
        border-radius: 5px;
    }
</style>

</head>
<body>
<nav class="navbar fixed-top navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">Toko Online</a>
        <a href="produk_search.php" class="btn btn-primary">Cari Produk</a>
    </div>
</nav>

<br><br><br><br>
<div class="container">
    <h2 class="mb-4">🛍️ Kategori Produk</h2>
    <div class="row">
        <?php while ($kategori = $kategori_result->fetch_assoc()): ?>
            <div class="col-md-3">
                <a href="produk_search.php?category=<?= $kategori['id']; ?>" class="btn btn-outline-primary btn-block mb-3">
                    <?= htmlspecialchars($kategori['nama']); ?>
                </a>
            </div>
        <?php endwhile; ?>
    </div>
    
    <h2 class="mt-5 mb-4">🔥 Produk Terbaru</h2>
    <div class="row">
        <?php while ($produk = $produk_query->fetch_assoc()): ?>
            <div class="col-md-4">
                <div class="card mb-4 shadow-sm">
                    <img src="<?= $produk['foto']; ?>" class="card-img-top" alt="<?= htmlspecialchars($produk['nama']); ?>" style="height: 250px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title"> <?= htmlspecialchars($produk['nama']); ?> </h5>
                        <p class="text-muted"><span class="badge bg-primary"> <?= htmlspecialchars($produk['kategori_nama']); ?> </span></p>
                        <h6 class="text-primary">Rp <?= number_format($produk['harga'], 0, ',', '.'); ?></h6>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <a href="detail_produk.php?id=<?= $produk['id']; ?>" class="btn btn-primary">Lihat detail</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
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

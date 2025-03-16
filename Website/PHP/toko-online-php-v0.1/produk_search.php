<?php
require "koneksi.php";

$limit = 10; // Jumlah data per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;
$search = isset($_GET['search']) ? trim($_GET['search']) : "";
$category = isset($_GET['category']) ? (int)$_GET['category'] : 0;

// Ambil data kategori untuk dropdown
$kategori_result = $koneksi->query("SELECT * FROM kategori");

// Query Produk
$query_produk = "SELECT produk.*, kategori.nama as kategori_nama, produk.penjual_nama, produk.whatsapp 
                 FROM produk 
                 JOIN kategori ON produk.kategori_id = kategori.id 
                 WHERE produk.nama LIKE ? AND produk.stok = 'Tersedia'";
$params = ["%$search%"];
$types = "s";

if ($category > 0) {
    $query_produk .= " AND produk.kategori_id = ?";
    $params[] = $category;
    $types .= "i";
}

$query_produk .= " LIMIT ?, ?";
$params[] = $start;
$params[] = $limit;
$types .= "ii";

$stmt_produk = $koneksi->prepare($query_produk);
$stmt_produk->bind_param($types, ...$params);
$stmt_produk->execute();
$produk_query = $stmt_produk->get_result();

// Hitung total produk
$total_query = "SELECT COUNT(*) AS total FROM produk 
                WHERE nama LIKE ? AND stok = 'Tersedia'";
$total_params = ["%$search%"];
$total_types = "s";

if ($category > 0) {
    $total_query .= " AND kategori_id = ?";
    $total_params[] = $category;
    $total_types .= "i";
}

$stmt_total = $koneksi->prepare($total_query);
$stmt_total->bind_param($total_types, ...$total_params);
$stmt_total->execute();
$total_produk = $stmt_total->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_produk / $limit);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Toko Online</title>
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
<nav class="navbar fixed-top navbar-light bg-light">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">Toko Online</a>
  </div>
</nav>

<br><br><br><br>
<div class="container">
    <form method="GET" class="mb-3">
        <div class="row">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Cari produk..." value="<?= htmlspecialchars($search); ?>">
            </div>
            <div class="col-md-4">
                <select name="category" class="form-control">
                    <option value="0">Semua Kategori</option>
                    <?php while ($kategori = $kategori_result->fetch_assoc()): ?>
                        <option value="<?= $kategori['id']; ?>" <?= ($category == $kategori['id']) ? 'selected' : ''; ?>><?= htmlspecialchars($kategori['nama']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary">Cari</button>
            </div>
        </div>
    </form>

    <h3 class="mb-4">📦 Produk</h3>
    
    <div class="row">
        <?php while ($produk = $produk_query->fetch_assoc()): ?>
            <div class="col-md-4">
                <div class="card mb-4 shadow-sm">
                    <img src="<?= $produk['foto']; ?>" class="card-img-top" alt="<?= htmlspecialchars($produk['nama']); ?>" style="height: 250px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title"> <?= htmlspecialchars($produk['nama']); ?> </h5>
                        <p class="text-muted"><span class="badge bg-primary"> <?= htmlspecialchars($produk['kategori_nama']); ?> </span></p>
                        <h6 class="text-primary">Rp <?= number_format($produk['harga'], 0, ',', '.'); ?></h6>
                        <p class="text-muted"><span class="badge bg-warning"><b style="color:gray;"><?= htmlspecialchars($produk['penjual_nama']); ?></b></span></p>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <a href="detail_produk.php?id=<?= $produk['id']; ?>" class="btn btn-primary">Lihat detail</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <?php if ($total_produk > 0): ?>
    <nav>
        <ul class="pagination justify-content-center">
            <li class="page-item <?= ($page == 1) ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=<?= $page - 1; ?>&search=<?= htmlspecialchars($search); ?>&category=<?= $category; ?>">Previous</a>
            </li>
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= ($i == $page) ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?= $i; ?>&search=<?= htmlspecialchars($search); ?>&category=<?= $category; ?>"> <?= $i; ?> </a>
                </li>
            <?php endfor; ?>
            <li class="page-item <?= ($page == $total_pages) ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=<?= $page + 1; ?>&search=<?= htmlspecialchars($search); ?>&category=<?= $category; ?>">Next</a>
            </li>
        </ul>
    </nav>
    <?php endif; ?>
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

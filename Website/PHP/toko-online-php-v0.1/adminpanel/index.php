<?php
require "../koneksi.php";
require "session.php";

$limit = 10; // Jumlah data per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;
$search = isset($_GET['search']) ? trim($_GET['search']) : "";

// Prepared Statements untuk menghindari SQL Injection
$stmt_users = $koneksi->prepare("SELECT id, username, email, nomor_telepon FROM users WHERE username LIKE ? OR email LIKE ? LIMIT ?, ?");
$search_param = "%$search%";
$stmt_users->bind_param("ssii", $search_param, $search_param, $start, $limit);
$stmt_users->execute();
$users_query = $stmt_users->get_result();

$stmt_kategori = $koneksi->prepare("SELECT * FROM kategori WHERE nama LIKE ? LIMIT ?, ?");
$stmt_kategori->bind_param("sii", $search_param, $start, $limit);
$stmt_kategori->execute();
$kategori_query = $stmt_kategori->get_result();

$stmt_produk = $koneksi->prepare("SELECT produk.*, kategori.nama as kategori_nama FROM produk JOIN kategori ON produk.kategori_id = kategori.id WHERE produk.nama LIKE ? LIMIT ?, ?");
$stmt_produk->bind_param("sii", $search_param, $start, $limit);
$stmt_produk->execute();
$produk_query = $stmt_produk->get_result();

// Hitung total data untuk paginasi
$total_users = $koneksi->query("SELECT COUNT(*) AS total FROM users WHERE username LIKE '%$search%' OR email LIKE '%$search%' ")->fetch_assoc()['total'];
$total_kategori = $koneksi->query("SELECT COUNT(*) AS total FROM kategori WHERE nama LIKE '%$search%' ")->fetch_assoc()['total'];
$total_produk = $koneksi->query("SELECT COUNT(*) AS total FROM produk WHERE nama LIKE '%$search%' ")->fetch_assoc()['total'];

$total_pages = ceil(max($total_users, $total_kategori, $total_produk) / $limit);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #000428, #004e92);
            color: #fff;
            min-height: 100vh;
            padding-top: 50px;
        }
        .container {
            background: rgba(255, 255, 255, 0.1);
            padding: 30px;
            border-radius: 10px;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .btn-logout {
            background: #dc3545;
            border: none;
        }
        .btn-logout:hover {
            background: #b02a37;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>🔹 Halo, <?= htmlspecialchars($_SESSION['admin_username']); ?>!</h2>
    <a href="logout.php" class="btn btn-danger">Logout</a>
    <hr>

    <form method="GET" class="mb-3">
        <input type="text" name="search" class="form-control" placeholder="Cari..." value="<?= htmlspecialchars($search); ?>">
        <button type="submit" class="btn btn-primary mt-2">Cari</button>
    </form>

    <h3>📂 Data Users</h3>
    <a href="tambah_user.php" class="btn btn-success">Tambah</a>
    <table class="table table-dark table-striped">
        <thead>
            <tr><th>ID</th><th>Username</th><th>Email</th><th>Nomor Telepon</th><th>Aksi</th></tr>
        </thead>
        <tbody>
            <?php while ($user = $users_query->fetch_assoc()): ?>
                <tr>
                    <td><?= $user['id']; ?></td>
                    <td><?= htmlspecialchars($user['username']); ?></td>
                    <td><?= htmlspecialchars($user['email']); ?></td>
                    <td><?= htmlspecialchars($user['nomor_telepon']); ?></td>
                    <td><a href="edit_user.php?id=<?= $user['id']; ?>" class="btn btn-warning">Edit</a>
                    <a href="hapus_user.php?id=<?= $produk['id']; ?>" class="btn btn-danger" onclick="return confirm('Hapus user ini?');">Hapus</a></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <h3>📂 Data Kategori</h3>
    <a href="tambah_kategori.php" class="btn btn-success">Tambah</a>
    <table class="table table-dark table-striped">
        <thead>
            <tr><th>ID</th><th>Nama Kategori</th><th>Aksi</th></tr>
        </thead>
        <tbody>
            <?php while ($kategori = $kategori_query->fetch_assoc()): ?>
                <tr>
                    <td><?= $kategori['id']; ?></td>
                    <td><?= htmlspecialchars($kategori['nama']); ?></td>
                    <td><a href="edit_kategori.php?id=<?= $kategori['id']; ?>" class="btn btn-warning">Edit</a>
                    <a href="hapus_kategori.php?id=<?= $produk['id']; ?>" class="btn btn-danger" onclick="return confirm('Hapus kategori ini?');">Hapus</a></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <h3>📂 Data Produk</h3>
    <a href="tambah_produk.php" class="btn btn-success">Tambah</a>
    <table class="table table-dark table-striped">
        <thead>
            <tr><th>ID</th><th>Nama Produk</th><th>Kategori</th><th>Harga</th><th>Aksi</th></tr>
        </thead>
        <tbody>
            <?php while ($produk = $produk_query->fetch_assoc()): ?>
                <tr>
                    <td><?= $produk['id']; ?></td>
                    <td><?= htmlspecialchars($produk['nama']); ?></td>
                    <td><?= htmlspecialchars($produk['kategori_nama']); ?></td>
                    <td><?= htmlspecialchars($produk['harga']); ?></td>
                    <td><a href="edit_produk.php?id=<?= $produk['id']; ?>" class="btn btn-warning">Edit</a>
                    <a href="hapus_produk.php?id=<?= $produk['id']; ?>" class="btn btn-danger" onclick="return confirm('Hapus produk ini?');">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <nav>
        <ul class="pagination">
            <li class="page-item <?= ($page == 1) ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=<?= $page - 1; ?>&search=<?= htmlspecialchars($search); ?>">Previous</a>
            </li>
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= ($i == $page) ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?= $i; ?>&search=<?= htmlspecialchars($search); ?>"> <?= $i; ?> </a>
                </li>
            <?php endfor; ?>
            <li class="page-item <?= ($page == $total_pages) ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=<?= $page + 1; ?>&search=<?= htmlspecialchars($search); ?>">Next</a>
            </li>
        </ul>
    </nav>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
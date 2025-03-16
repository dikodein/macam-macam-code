<?php
require "../koneksi.php";
require "session.php";

// Pastikan ada parameter ID di URL
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = intval($_GET['id']);

// Ambil data user dari database
$user_query = $koneksi->query("SELECT * FROM users WHERE id = $id");
$user = $user_query->fetch_assoc();

if (!$user) {
    echo "<script>alert('Pengguna tidak ditemukan.'); window.location='index.php';</script>";
    exit();
}

// Proses update data user
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $koneksi->real_escape_string($_POST['username']);
    $email = $koneksi->real_escape_string($_POST['email']);
    $kode_negara = $koneksi->real_escape_string($_POST['kode_negara']);
    $nomor_telepon = $koneksi->real_escape_string($_POST['nomor_telepon']);
    
    // Validasi nomor telepon (hanya angka, panjang 7-15 digit, dan tidak boleh diawali dengan 0)
    if (!preg_match("/^[1-9][0-9]{6,14}$/", $nomor_telepon)) {
        echo "<script>alert('Nomor telepon tidak valid. Harap masukkan hanya angka (7-15 digit) tanpa awalan 0.');</script>";
    } else {
        $nomor_telepon_full = $kode_negara . $nomor_telepon;
        $update_query = "UPDATE users SET username='$username', email='$email', nomor_telepon='$nomor_telepon_full' WHERE id=$id";

        if ($koneksi->query($update_query)) {
            echo "<script>alert('Data berhasil diperbarui!'); window.location='index.php';</script>";
        } else {
            echo "<script>alert('Terjadi kesalahan: " . $koneksi->error . "');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
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
    <h2>Edit User</h2>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user['username']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Kode Negara</label>
            <select name="kode_negara" class="form-control" required>
                <option value="+62" <?= strpos($user['nomor_telepon'], '+62') === 0 ? 'selected' : ''; ?>>Indonesia (+62)</option>
                <option value="+1" <?= strpos($user['nomor_telepon'], '+1') === 0 ? 'selected' : ''; ?>>USA (+1)</option>
                <option value="+44" <?= strpos($user['nomor_telepon'], '+44') === 0 ? 'selected' : ''; ?>>UK (+44)</option>
                <option value="+91" <?= strpos($user['nomor_telepon'], '+91') === 0 ? 'selected' : ''; ?>>India (+91)</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Nomor Telepon</label>
            <input type="text" name="nomor_telepon" class="form-control" pattern="[1-9][0-9]{6,14}" title="Masukkan hanya angka (7-15 digit) tanpa awalan 0" value="<?= preg_replace('/^\+\d{1,3}/', '', $user['nomor_telepon']); ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        <a href="index.php" class="btn btn-secondary">Batal</a>
    </form>
</div>
</body>
</html>
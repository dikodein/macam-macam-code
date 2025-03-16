<?php
require "../koneksi.php";
require "session.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $kode_negara = $_POST['kode_negara'];
    $nomor_telepon = $_POST['nomor_telepon'];

    // Validasi nomor telepon: tidak boleh diawali angka 0 dan hanya boleh angka (7-15 digit)
    if (!preg_match("/^[1-9][0-9]{6,14}$/", $nomor_telepon)) {
        echo "<script>alert('Nomor telepon tidak valid. Harap masukkan hanya angka (7-15 digit) dan tidak diawali dengan 0.');</script>";
    } else {
        $nomor_telepon_full = $kode_negara . $nomor_telepon;

        // Cek apakah username, email, atau nomor telepon sudah digunakan
        $cek_query = $koneksi->prepare("SELECT * FROM users WHERE username = ? OR email = ? OR nomor_telepon = ?");
        $cek_query->bind_param("sss", $username, $email, $nomor_telepon_full);
        $cek_query->execute();
        $result = $cek_query->get_result();

        if ($result->num_rows > 0) {
            echo "<script>alert('Username, Email, atau Nomor Telepon sudah digunakan. Gunakan data lain!');</script>";
        } else {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

            $query = $koneksi->prepare("INSERT INTO users (username, email, nomor_telepon, password) VALUES (?, ?, ?, ?)");
            $query->bind_param("ssss", $username, $email, $nomor_telepon_full, $password);

            if ($query->execute()) {
                echo "<script>alert('User berhasil ditambahkan!'); window.location='index.php';</script>";
                exit();
            } else {
                echo "<script>alert('Terjadi kesalahan: " . $koneksi->error . "');</script>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah User</title>
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
    <h2>Tambah User</h2>
    <form method="post">
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Kode Negara</label>
            <select name="kode_negara" class="form-control" required>
                <option value="+62">Indonesia (+62)</option>
                <option value="+1">USA (+1)</option>
                <option value="+44">UK (+44)</option>
                <option value="+91">India (+91)</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Nomor Telepon</label>
            <input type="text" name="nomor_telepon" class="form-control" pattern="[1-9][0-9]{6,14}" title="Masukkan hanya angka (7-15 digit) tanpa diawali 0" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Tambah</button>
        <a href="index.php" class="btn btn-secondary">Kembali</a>
    </form>
</div>
</body>
</html>

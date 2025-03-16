<?php
session_start();

// Cek apakah admin sudah login
if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_username'])) {
    header('Location: login.php');
    exit();
}
?>

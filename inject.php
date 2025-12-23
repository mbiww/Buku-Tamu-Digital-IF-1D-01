<?php
session_start();
require_once 'koneksi.php';

$no_id = 9680;
$nama_lengkap = 'admin';
$email = 'admin@gmail.com';
$role = 'admin';
$institusi = 'admin';
$password = 'the1975';
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Simpan ke database
$stmt = $koneksi->prepare("INSERT INTO users (no_id, nama_lengkap, email, role, institusi, password,created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");

if ($stmt->execute([$no_id, $nama_lengkap, $email, $role, $institusi, $hashedPassword])) {
    $success = "Registrasi berhasil! Silakan login.";
    // Redirect ke halaman login setelah 2 detik
    header("refresh:2;url=login_user.php");
} else {
    $error = "Terjadi kesalahan saat registrasi!";
}
?>
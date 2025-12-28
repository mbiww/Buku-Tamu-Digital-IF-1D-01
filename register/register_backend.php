<?php
session_start();
require_once dirname(__DIR__) . '/koneksi.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $no_id = $_POST['no_id'];
    $nama_lengkap = $_POST['nama_lengkap'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $institusi = $_POST['institusi'];
    $password = $_POST['password'];
    $confirmpassword = $_POST['confirmpassword']; // Tambahkan ini

    // Validasi input
    if (empty($no_id) || empty($nama_lengkap) || empty($email) || empty($role) || empty($institusi) || empty($password) || empty($confirmpassword)) {
        $error = "Semua field harus diisi!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid!";
    } elseif (strlen($password) < 6) {
        $error = "Password minimal 6 karakter!";
    } elseif ($password !== $confirmpassword) {
        $error = "Password dan konfirmasi password tidak cocok!";
    } else {
        // Cek apakah no_id atau email sudah terdaftar
        $stmt = mysqli_prepare(
            $koneksi,
            "SELECT * FROM users WHERE no_id = ? OR email = ?"
        );

        mysqli_stmt_bind_param($stmt, "ss", $no_id, $email);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $jumlah = mysqli_num_rows($result);

        if ($jumlah > 0) {
            $error = "Nomor ID atau email sudah terdaftar!";

        } else {
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Simpan ke database
            $stmt = $koneksi->prepare("INSERT INTO users (no_id, nama_lengkap, email, role, institusi, password,created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");

            if ($stmt->execute([$no_id, $nama_lengkap, $email, $role, $institusi, $hashedPassword])) {
                $success = "Registrasi berhasil! Silakan login.";
                // Redirect ke halaman login setelah 2 detik
                header("refresh:2;url=../login/login.php");
            } else {
                $error = "Terjadi kesalahan saat registrasi!";
            }
        }
    }
}
?>
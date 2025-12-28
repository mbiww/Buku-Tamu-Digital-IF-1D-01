<?php
session_start();
require_once dirname(__DIR__) . '/koneksi.php';

/* =============================
   CEK JIKA SUDAH LOGIN
============================= */
if (isset($_SESSION['role']) || isset($_COOKIE['login'])) {

    $role = $_SESSION['role'] ?? $_COOKIE['role'];

    if ($role == 'admin') {
        header('location: ../dashboard_admin.php');
    } elseif ($role == 'mahasiswa') {
        header('Location: ../form_tamu/form_mahasiswa/form.php');
    } elseif ($role == 'instansi') {
        header('Location: ../form_tamu/form_instansi/form.php');
    }
    exit;
}

/* =============================
   PROSES LOGIN
============================= */
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $no_id = $_POST['no_id'];
    $password = $_POST['password'];

    $stmt = mysqli_prepare($koneksi, "SELECT * FROM users WHERE no_id = ?");
    mysqli_stmt_bind_param($stmt, "s", $no_id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user['password'])) {

        /* =============================
           SIMPAN SESSION
        ============================= */
        $_SESSION['no_id'] = $user['no_id'];
        $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['type_tamu'] = $user['type_tamu'];

        /* =============================
           SIMPAN COOKIE 5 Menit
        ============================= */
        setcookie('login', $user['no_id'], time() + 300, "/"); 
        setcookie('role', $user['role'], time() + 300, "/");


        /* =============================
           REDIRECT SESUAI ROLE
        ============================= */
        if ($user['role'] == "mahasiswa") {
            header('Location: ../form_tamu/form_mahasiswa/form.php');
        } elseif ($user['role'] == "instansi") {
            header('Location: ../form_tamu/form_instansi/form.php');
        } elseif ($user['role'] == "admin") {
            header('Location: ../dashboard_admin.php');
        }
        exit;

    } else {
        $error = "No ID atau password salah!";
    }
}
?>
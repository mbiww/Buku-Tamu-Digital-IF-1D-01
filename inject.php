<?php
session_start();
require_once 'koneksi.php';

// Data admin default
$no_id = '9680';
$nama_lengkap = 'admin';
$email = 'admin@gmail.com';
$role = 'admin';
$institusi = 'admin';
$password = 'the1975';
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Cek apakah admin sudah ada
$check_sql = "SELECT id, no_id, nama_lengkap, email, role, institusi FROM users WHERE email = ? OR no_id = ?";
$check_stmt = $koneksi->prepare($check_sql);
$check_stmt->bind_param("ss", $email, $no_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();
$existing_admin = $check_result->fetch_assoc();

if (!$existing_admin) {
    // Simpan ke database
    $insert_sql = "INSERT INTO users (no_id, nama_lengkap, email, role, institusi, password, created_at) 
                   VALUES (?, ?, ?, ?, ?, ?, NOW())";
    $insert_stmt = $koneksi->prepare($insert_sql);
    $insert_stmt->bind_param("ssssss", $no_id, $nama_lengkap, $email, $role, $institusi, $hashedPassword);
    
    if ($insert_stmt->execute()) {
        $success = "Admin berhasil dibuat!";
        
        // Ambil ID yang baru saja diinsert
        $user_id = $insert_stmt->insert_id;
        
        // SET SEMUA SESSION VARIABLE YANG DIBUTUHKAN DASHBOARD_ADMIN.PHP
        $_SESSION['no_id'] = $no_id;
        $_SESSION['role'] = $role;
        $_SESSION['user_id'] = $user_id;
        $_SESSION['nama_lengkap'] = $nama_lengkap;
        $_SESSION['nama'] = $nama_lengkap;
        $_SESSION['email'] = $email;
        $_SESSION['institusi'] = $institusi;
        
        echo "<script>
            alert('Admin berhasil dibuat dan login otomatis!');
            window.location.href = 'dashboard_admin.php';
        </script>";
        exit();
    } else {
        $error = "Terjadi kesalahan saat membuat admin! Error: " . $insert_stmt->error;
        echo "<div style='padding:20px; background:#f8d7da; color:#721c24; border-radius:5px;'>
                <h3>Error!</h3>
                <p>$error</p>
                <a href='login/login.php'>Kembali ke Login</a>
              </div>";
    }
    
    $insert_stmt->close();
} else {
    // Jika admin sudah ada, langsung login
    $login_sql = "SELECT id, no_id, nama_lengkap, email, role, institusi FROM users WHERE email = ?";
    $login_stmt = $koneksi->prepare($login_sql);
    $login_stmt->bind_param("s", $email);
    $login_stmt->execute();
    $result = $login_stmt->get_result();
    $admin_data = $result->fetch_assoc();
    
    if ($admin_data) {
        // SET SEMUA SESSION VARIABLE YANG DIBUTUHKAN DASHBOARD_ADMIN.PHP
        $_SESSION['no_id'] = $admin_data['no_id'];
        $_SESSION['role'] = $admin_data['role'];
        $_SESSION['user_id'] = $admin_data['id'];
        $_SESSION['nama_lengkap'] = $admin_data['nama_lengkap'];
        $_SESSION['nama'] = $admin_data['nama_lengkap'];
        $_SESSION['email'] = $admin_data['email'];
        $_SESSION['institusi'] = $admin_data['institusi'];
        
        echo "<script>
            alert('Admin sudah ada. Login otomatis berhasil!');
            window.location.href = 'dashboard_admin.php';
        </script>";
        exit();
    } else {
        echo "<div style='padding:20px; background:#f8d7da; color:#721c24; border-radius:5px;'>
                <h3>Error!</h3>
                <p>Gagal mengambil data admin.</p>
                <a href='login/login.php'>Kembali ke Login</a>
              </div>";
    }
}

$check_stmt->close();
$koneksi->close();
?>
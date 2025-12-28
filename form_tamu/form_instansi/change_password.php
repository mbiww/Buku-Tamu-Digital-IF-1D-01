<?php
// change_password.php
session_start();
require_once dirname(__DIR__, 2) . '/koneksi.php';

// Cek apakah user sudah login
if (!isset($_SESSION['no_id']) || $_SESSION['role'] != 'instansi') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Cek apakah request method adalah POST
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Ambil data dari POST request
$current_password = $_POST['current_password'] ?? '';
$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// Validasi input
$errors = [];

if (empty($current_password)) {
    $errors[] = 'Password saat ini harus diisi';
}

if (empty($new_password)) {
    $errors[] = 'Password baru harus diisi';
} elseif (strlen($new_password) < 6) {
    $errors[] = 'Password baru minimal 6 karakter';
}

if (empty($confirm_password)) {
    $errors[] = 'Konfirmasi password harus diisi';
}

if ($new_password !== $confirm_password) {
    $errors[] = 'Password baru dan konfirmasi password tidak sama';
}

// Jika ada error, kembalikan response error
if (!empty($errors)) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => implode('<br>', $errors)]);
    exit;
}

$no_id_user = $_SESSION['no_id'];

// Ambil password user saat ini dari database
$query = "SELECT password FROM users WHERE no_id = ?";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "s", $no_id_user);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if (!$user) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'User tidak ditemukan']);
    exit;
}

// Verifikasi password saat ini
if (!password_verify($current_password, $user['password'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Password saat ini salah']);
    exit;
}

// Hash password baru
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

// Update password di database
$update_query = "UPDATE users SET password = ? WHERE no_id = ?";
$update_stmt = mysqli_prepare($koneksi, $update_query);
mysqli_stmt_bind_param($update_stmt, "ss", $hashed_password, $no_id_user);

if (mysqli_stmt_execute($update_stmt)) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true, 
        'message' => 'Password berhasil diubah! Silakan login kembali dengan password baru.'
    ]);
} else {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false, 
        'message' => 'Gagal mengubah password: ' . mysqli_error($koneksi)
    ]);
}

// Tutup statement
mysqli_stmt_close($stmt);
if (isset($update_stmt)) {
    mysqli_stmt_close($update_stmt);
}
?>
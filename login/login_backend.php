<?php
session_start();
require_once dirname(__DIR__) . '/koneksi.php';

/* =============================
   FUNGSI BANTUAN (HELPER FUNCTIONS)
============================= */

/**
 * Redirect pengguna berdasarkan role
 * @param string $role
 */
function redirectByRole(string $role): void
{
    switch ($role) {
        case 'admin':
            header('Location: ../dashboard_admin.php');
            break;
        case 'mahasiswa':
            header('Location: ../form_tamu/form_mahasiswa/form.php');
            break;
        case 'instansi':
            header('Location: ../form_tamu/form_instansi/form.php');
            break;
        default:
            // Fallback ke halaman login jika role tidak dikenali
            header('Location: ../login.php');
    }
    exit;
}

/**
 * Set session pengguna
 * @param array $userData
 */
function setUserSession(array $userData): void
{
    $_SESSION['no_id'] = $userData['no_id'];
    $_SESSION['nama_lengkap'] = $userData['nama_lengkap'];
    $_SESSION['email'] = $userData['email'];
    $_SESSION['role'] = $userData['role'];
    $_SESSION['type_tamu'] = $userData['type_tamu'];
}

/**
 * Set cookie autentikasi
 * @param string $noId
 * @param string $role
 */
function setAuthCookies(string $noId, string $role): void
{
    $expiryTime = time() + 300; // 5 menit
    setcookie('login', $noId, $expiryTime, "/");
    setcookie('role', $role, $expiryTime, "/");
}

/**
 * Validasi login pengguna
 * @param mysqli $koneksi
 * @param string $noId
 * @param string $password
 * @return array|null
 */
function validateUserCredentials(mysqli $koneksi, string $noId, string $password): ?array
{
    $query = "SELECT * FROM users WHERE no_id = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    
    if (!$stmt) {
        throw new Exception("Gagal menyiapkan statement SQL.");
    }
    
    mysqli_stmt_bind_param($stmt, "s", $noId);
    
    if (!mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        throw new Exception("Gagal mengeksekusi query.");
    }
    
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    
    if (!$user || !password_verify($password, $user['password'])) {
        return null;
    }
    
    return $user;
}

/* =============================
   CEK JIKA SUDAH LOGIN
============================= */
function isUserAlreadyLoggedIn(): bool
{
    return isset($_SESSION['role']) || isset($_COOKIE['login']);
}

if (isUserAlreadyLoggedIn()) {
    $role = $_SESSION['role'] ?? $_COOKIE['role'] ?? 'guest';
    
    if ($role !== 'guest') {
        redirectByRole($role);
    }
}

/* =============================
   PROSES LOGIN UTAMA
============================= */
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validasi input
    $noId = trim($_POST['no_id'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($noId) || empty($password)) {
        $error = "ID dan kata sandi harus diisi.";
    } else {
        try {
            $user = validateUserCredentials($koneksi, $noId, $password);
            
            if ($user) {
                // Set session
                setUserSession($user);
                
                // Set cookie
                setAuthCookies($user['no_id'], $user['role']);
                
                // Redirect berdasarkan role
                redirectByRole($user['role']);
            } else {
                $error = "ID atau kata sandi salah, atau akun belum terdaftar.";
            }
        } catch (Exception $e) {
            // Log error untuk debugging (dalam produksi, simpan ke file log)
            error_log("Login error: " . $e->getMessage());
            
            // Pesan error untuk pengguna
            $error = "Terjadi kesalahan sistem. Silakan coba lagi nanti.";
        }
    }
}
?>
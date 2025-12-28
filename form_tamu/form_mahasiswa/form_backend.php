<?php
require_once dirname(__DIR__, 2) . '/koneksi.php';

date_default_timezone_set('Asia/Jakarta');

if (!isset($_SESSION['no_id']) || $_SESSION['role'] != 'mahasiswa') {
    header('Location: ../../login/login.php');
    exit;
}

$no_id_user = $_SESSION['no_id'];
$role_user = $_SESSION['role'];

// Query untuk mendapatkan nama dan institusi user
$query_user = "SELECT nama_lengkap, institusi FROM users WHERE no_id = ?";
$stmt_user = mysqli_prepare($koneksi, $query_user);
mysqli_stmt_bind_param($stmt_user, "s", $no_id_user);
mysqli_stmt_execute($stmt_user);
$result_user = mysqli_stmt_get_result($stmt_user);
$user_data = mysqli_fetch_assoc($result_user);

if ($user_data) {
    $nama_user = $user_data['nama_lengkap'];
    $institusi_user = $user_data['institusi'];
} else {
    $nama_user = $_SESSION['nama'] ?? 'User';
    $institusi_user = $_SESSION['institusi'] ?? 'Instansi';
}

$success = '';
$error = '';
$tamu_aktif = null;

// CEK STATUS BERTAMU USER SAAT INI
$query_status = "SELECT * FROM data_tamu 
                WHERE user_id = ? 
                AND status_aktif = 'aktif'
                ORDER BY waktu_checkin DESC 
                LIMIT 1";
$stmt_status = mysqli_prepare($koneksi, $query_status);
mysqli_stmt_bind_param($stmt_status, "s", $no_id_user);
mysqli_stmt_execute($stmt_status);
$result_status = mysqli_stmt_get_result($stmt_status);
$tamu_aktif = mysqli_fetch_assoc($result_status);
mysqli_stmt_close($stmt_status);

// HANDLE CHECK OUT
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['checkout'])) {
    if ($tamu_aktif) {
        $update_query = "UPDATE data_tamu 
                        SET status_aktif = 'tidak aktif', 
                            waktu_checkout = NOW() 
                        WHERE id = ? AND user_id = ?";
        $update_stmt = mysqli_prepare($koneksi, $update_query);
        mysqli_stmt_bind_param($update_stmt, "is", $tamu_aktif['id'], $no_id_user);
        
        if (mysqli_stmt_execute($update_stmt)) {
            $success = "Check out berhasil! Status bertamu telah dinonaktifkan.";
            $tamu_aktif = null;
        } else {
            $error = "Gagal melakukan check out: " . mysqli_error($koneksi);
        }
        mysqli_stmt_close($update_stmt);
    } else {
        $error = "Tidak ada tamu aktif untuk di-check out.";
    }
}

// HANDLE CHECK IN / FORM PENDAFTARAN
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nama_lengkap'])) {
    
    // CEK APAKAH USER SUDAH MEMILIKI TAMU AKTIF
    if ($tamu_aktif) {
        $error = "Anda masih memiliki tamu aktif. Silakan check out terlebih dahulu sebelum mendaftarkan tamu baru.";
    } else {
        // Sanitize input
        $nama_lengkap = htmlspecialchars(trim($_POST['nama_lengkap']));
        $no_id_tamu = htmlspecialchars(trim($_POST['no_id']));
        $institusi = htmlspecialchars(trim($_POST['institusi']));
        $alamat = htmlspecialchars(trim($_POST['alamat']));
        $no_wa = htmlspecialchars(trim($_POST['number']));
        $keperluan = htmlspecialchars(trim($_POST['message']));
        
        // Validasi input
        $errors = [];
        
        if (empty($nama_lengkap)) {
            $errors[] = "Nama lengkap harus diisi!";
        }
        
        if (empty($no_id_tamu) || !is_numeric($no_id_tamu)) {
            $errors[] = "No identitas (NIM) harus berupa angka!";
        }
        
        if (empty($institusi)) {
            $errors[] = "Universitas harus diisi!";
        }
        
        if (empty($alamat)) {
            $errors[] = "Alamat harus diisi!";
        }
        
        if (empty($no_wa) || !is_numeric($no_wa)) {
            $errors[] = "Nomor WA harus berupa angka!";
        }
        
        if (empty($keperluan)) {
            $errors[] = "Keperluan harus diisi!";
        }
        
        if (strlen($no_wa) < 10 || strlen($no_wa) > 15) {
            $errors[] = "Nomor WA harus antara 10-15 digit!";
        }
        
        if (empty($errors)) {
            $stmt = mysqli_prepare(
                    $koneksi,
                    "INSERT INTO data_tamu 
                    (nama_lengkap, no_id, institusi, alamat, no_wa, keperluan, 
                     jenis_pengguna, status_aktif, waktu_checkin, user_id)
                    VALUES (?, ?, ?, ?, ?, ?, 'mahasiswa', 'aktif', NOW(), ?)"
            );
            
            if ($stmt) {
                mysqli_stmt_bind_param(
                    $stmt,
                    "sssssss",
                    $nama_lengkap,
                    $no_id_tamu,
                    $institusi,
                    $alamat,
                    $no_wa,
                    $keperluan,
                    $no_id_user
                );
                
                if (mysqli_stmt_execute($stmt)) {
                    $success = "Check in berhasil! Tamu telah terdaftar.";
                    
                    // Ambil data tamu yang baru saja diinput
                    $last_id = mysqli_insert_id($koneksi);
                    $query_new = "SELECT * FROM data_tamu WHERE id = ?";
                    $stmt_new = mysqli_prepare($koneksi, $query_new);
                    mysqli_stmt_bind_param($stmt_new, "i", $last_id);
                    mysqli_stmt_execute($stmt_new);
                    $result_new = mysqli_stmt_get_result($stmt_new);
                    $tamu_aktif = mysqli_fetch_assoc($result_new);
                    mysqli_stmt_close($stmt_new);
                    
                    $_POST = array();
                } else {
                    $error = "Terjadi kesalahan saat menyimpan data: " . mysqli_error($koneksi);
                }
                
                mysqli_stmt_close($stmt);
            } else {
                $error = "Terjadi kesalahan dalam persiapan query: " . mysqli_error($koneksi);
            }
        } else {
            $error = implode("<br>", $errors);
        }
    }
}
?>
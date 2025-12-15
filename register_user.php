<?php
session_start();
require_once 'koneksi.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $no_id = $_POST['no_id'];
    $nama_lengkap = $_POST['nama_lengkap'];
    $email = $_POST['email'];
    $role     = $_POST['role'];
    $institusi = $_POST['institusi'];
    $password = $_POST['password'];

    // Validasi input
    if (empty($no_id) || empty($nama_lengkap) || empty($email) || empty($role) || empty($institusi) || empty($password)) {
        $error = "Semua field harus diisi!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid!";
    } else {
        // Cek apakah no_id atau email sudah terdaftar
        $stmt = $koneksi->prepare("SELECT * FROM users WHERE no_id = ? OR email = ?");
        $stmt->execute([$no_id, $email]); 
        
        if ($stmt->rowCount() > 0) {
            $error = "Nomor ID atau email sudah terdaftar!";
        } else {
            // Hash password
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
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
    }
    </style>
</head>

<body class="bg-light">

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4">

                <div class="card p-4 shadow">
                    <h4 class="text-center mb-3">Register Sebagai Mahasiswa</h4>
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
          
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>

                    <form action="register_user.php" method="POST">

                        <div class="mb-3">
                            <label>No Identitas (NIM, NIDN, NIK)</label>
                            <input type="number" name="no_id" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Daftar Sebagai</label>
                            <select name="role" class="form-control" required>
                            <option value="">-- Pilih Jenis Pengguna --</option>
                            <option value="mahasiswa">Mahasiswa</option>
                            <option value="instansi">Instansi</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label>Kampus / Instansi</label>
                            <input type="text" name="institusi" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Daftar</button>

                    </form>
                </div>

            </div>
        </div>
    </div>

</body>
</html>

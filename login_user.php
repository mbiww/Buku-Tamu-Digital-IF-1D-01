<?php
session_start();
require_once 'koneksi.php';

/* =============================
   CEK JIKA SUDAH LOGIN
============================= */
if (isset($_SESSION['role']) || isset($_COOKIE['login'])) {

    $role = $_SESSION['role'] ?? $_COOKIE['role'];

    if ($role == 'admin') {
        header('Location: dashboard_admin.php');
    } elseif ($role == 'mahasiswa') {
        header('Location: form_tamu_mahasiswa.php');
    } elseif ($role == 'instansi') {
        header('Location: form_tamu_instansi.php');
    }
    exit;
}

/* =============================
   PROSES LOGIN
============================= */
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $no_id    = $_POST['no_id'];
    $password = $_POST['password'];

    $stmt = mysqli_prepare($koneksi, "SELECT * FROM users WHERE no_id = ?");
    mysqli_stmt_bind_param($stmt, "s", $no_id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $user   = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user['password'])) {

        /* =============================
           SIMPAN SESSION
        ============================= */
        $_SESSION['no_id']        = $user['no_id'];
        $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
        $_SESSION['email']        = $user['email'];
        $_SESSION['role']         = $user['role'];
        $_SESSION['type_tamu']    = $user['type_tamu'];

        /* =============================
           SIMPAN COOKIE (7 HARI)
        ============================= */
        setcookie('login', $user['no_id'], time() + (86400 * 7), "/");
        setcookie('role', $user['role'], time() + (86400 * 7), "/");

        /* =============================
           REDIRECT SESUAI ROLE
        ============================= */
        if ($user['role'] == "mahasiswa") {
            header('Location: form_tamu_mahasiswa.php');
        } elseif ($user['role'] == "instansi") {
            header('Location: form_tamu_instansi.php');
        } elseif ($user['role'] == "admin") {
            header('Location: dashboard_admin.php');
        }
        exit;

    } else {
        $error = "No ID atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Pengguna</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        .register-link {
            font-size: 14px;
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>

<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">

            <div class="card p-4 shadow">
                <h4 class="text-center mb-3">Login</h4>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label>No Identitas (NIM / NIDN / NIK)</label>
                        <input type="number" name="no_id" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        Login
                    </button>
                </form>

                <div class="register-link">
                    <small>
                        Belum Punya Akun?
                        <a href="register_user.php" class="text-secondary">
                            Daftar Disini!
                        </a>
                    </small>
                </div>

            </div>
        </div>
    </div>
</div>

</body>
</html>

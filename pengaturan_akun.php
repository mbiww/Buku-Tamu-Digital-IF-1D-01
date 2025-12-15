<?php
include 'koneksi.php';

/* =========================
   PROSES TAMBAH ADMIN
========================= */
$success = '';
$error = '';

if (isset($_POST['tambah_admin'])) {
    $nama_lengkap = $_POST['nama_lengkap'];
    $email = $_POST['email'];
    $no_id = $_POST['no_id'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // cek apakah email / no_id sudah ada
    $cek = mysqli_prepare(
        $koneksi,
        "SELECT id FROM users WHERE email = ? OR no_id = ?"
    );
    mysqli_stmt_bind_param($cek, "ss", $email, $no_id);
    mysqli_stmt_execute($cek);
    $result = mysqli_stmt_get_result($cek);

    if (mysqli_num_rows($result) > 0) {
        $error = "Email atau No ID sudah terdaftar!";
    } else {
        $stmt = mysqli_prepare(
            $koneksi,
            "INSERT INTO users 
            (nama_lengkap, email, no_id, password, role, institusi)
            VALUES (?, ?, ?, ?, 'admin', 'admin')"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "ssss",
            $nama_lengkap,
            $email,
            $no_id,
            $password
        );

        if (mysqli_stmt_execute($stmt)) {
            $success = "Akun admin berhasil ditambahkan!";
        } else {
            $error = "Gagal menambahkan akun admin!";
        }
    }
}

/* =========================
   AMBIL DATA ADMIN
========================= */
$admin = mysqli_query(
    $koneksi,
    "SELECT * FROM users 
     WHERE role = 'admin' 
     AND institusi = 'admin'
     ORDER BY id DESC"
);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Pengaturan Akun</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .navbar {
            background-color: rgba(0, 0, 0, .8);
        }

        .navbar-brand,
        .nav-link {
            color: white !important;
        }

        .card {
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, .1);
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold">Pengaturan Akun Admin</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="dashboard_admin.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="data_tamu.php">Data Tamu</a></li>
                    <li class="nav-item"><a class="nav-link" href="laporan.php">Laporan</a></li>
                    <li class="nav-item"><a class="nav-link" href="pengaturan_akun.php">Pengaturan Akun</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>

    </nav>

    <div class="container my-4">

        <!-- ALERT -->
        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success; ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error; ?></div>
        <?php endif; ?>

        <!-- TAMBAH ADMIN -->
        <div class="card p-4 mb-4">
            <h5 class="fw-bold mb-3">Tambah Akun Admin</h5>

            <form method="post">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>No ID</label>
                        <input type="text" name="no_id" class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                </div>

                <button type="submit" name="tambah_admin" class="btn btn-primary">
                    Tambah Admin
                </button>
            </form>
        </div>

        <!-- DAFTAR ADMIN -->
        <div class="card p-4">
            <h5 class="fw-bold mb-3">Daftar Akun Admin</h5>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>nama_lengkap</th>
                        <th>Email</th>
                        <th>No ID</th>
                        <th>Role</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    while ($a = mysqli_fetch_assoc($admin)) { ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= $a['nama_lengkap']; ?></td>
                            <td><?= $a['email']; ?></td>
                            <td><?= $a['no_id']; ?></td>
                            <td><?= $a['role']; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

    </div>

</body>

</html>
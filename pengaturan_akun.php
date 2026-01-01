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
   DATA ADMIN
========================= */
$admin = mysqli_query(
    $koneksi,
    "SELECT * FROM users 
     WHERE role='admin' AND institusi='admin'
     ORDER BY id DESC"
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Pengaturan Akun</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
  background: #f5f7fb;
  font-family: 'Segoe UI', sans-serif;
}

/* NAVBAR */
.navbar {
  background: linear-gradient(90deg, #3b6df6, #4f8cff);
}
.navbar-brand,
.nav-link {
  color: #fff !important;
}
.nav-link.active {
  font-weight: 600;
  background: rgba(255,255,255,0.2);
  border-radius: 12px;
  padding: 6px 14px;
}

/* CARD */
.card-custom {
  background: #ffffff;
  border-radius: 18px;
  padding: 30px;
  box-shadow: 0 15px 35px rgba(59,109,246,0.15);
}

/* FORM */
.form-control {
  border-radius: 12px;
  border: 1px solid #dce3ff;
}
.form-control:focus {
  border-color: #3b6df6;
  box-shadow: 0 0 0 0.2rem rgba(59,109,246,.15);
}

/* BUTTON */
.btn-primary {
  background: linear-gradient(90deg, #3b6df6, #4f8cff);
  border: none;
  border-radius: 20px;
  padding: 8px 24px;
}
.btn-primary:hover {
  background: linear-gradient(90deg, #325fd8, #3f7ae0);
}

/* TABLE */
.table thead {
  background: #eef3ff;
}
.table th {
  font-weight: 600;
  color: #2c3e50;
  border: none;
}
.table td {
  border: none;
}
.table tbody tr {
  border-bottom: 1px solid #e6ebff;
}   
</style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg">
  <div class="container-fluid px-4">
    <span class="navbar-brand fw-bold">Pengaturan Akun Admin</span>
    <ul class="navbar-nav ms-auto">
      <li class="nav-item"><a class="nav-link" href="dashboard_admin.php">Home</a></li>
      <li class="nav-item"><a class="nav-link" href="data_tamu.php">Data Tamu</a></li>
      <li class="nav-item"><a class="nav-link" href="laporan.php">Laporan</a></li>
      <li class="nav-item"><a class="nav-link active" href="pengaturan_akun.php">Pengaturan Akun</a></li>
      <li class="nav-item"><a class="nav-link text-warning" href="logout.php">Logout</a></li>
    </ul>
  </div>
</nav>


<div class="container my-5">

<!-- ALERT -->
<?php if ($success): ?>
  <div class="alert alert-success"><?= $success; ?></div>
<?php endif; ?>

<?php if ($error): ?>
  <div class="alert alert-danger"><?= $error; ?></div>
<?php endif; ?>

<!-- TAMBAH ADMIN -->
<div class="card-custom mb-4">
  <h4 class="fw-bold mb-4">Tambah Akun Admin</h4>

  <form method="post">
    <div class="row">
      <div class="col-md-6 mb-3">
        <label class="fw-semibold">Nama Lengkap</label>
        <input type="text" name="nama_lengkap" class="form-control" required>
      </div>

      <div class="col-md-6 mb-3">
        <label class="fw-semibold">Email</label>
        <input type="email" name="email" class="form-control" required>
      </div>

      <div class="col-md-6 mb-3">
        <label class="fw-semibold">No ID</label>
        <input type="text" name="no_id" class="form-control" required>
      </div>

      <div class="col-md-6 mb-3">
        <label class="fw-semibold">Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>
    </div>

    <button type="submit" name="tambah_admin" class="btn btn-primary mt-2">
      Tambah Admin
    </button>
  </form>
</div>

<!-- DAFTAR ADMIN -->
<div class="card-custom">
  <h4 class="fw-bold mb-4">Daftar Akun Admin</h4>

  <div class="table-responsive">
    <table class="table">
      <thead>
        <tr>
          <th>No</th>
          <th>Nama Lengkap</th>
          <th>Email</th>
          <th>No ID</th>
          <th>Role</th>
        </tr>
      </thead>
      <tbody>
        <?php $no=1; while($a=mysqli_fetch_assoc($admin)) { ?>
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

</div>

</body>
</html>

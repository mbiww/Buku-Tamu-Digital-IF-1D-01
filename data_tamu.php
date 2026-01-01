<?php
include 'koneksi.php';
$tamu = mysqli_query($koneksi, "SELECT * FROM data_tamu");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Data Tamu</title>

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
.navbar .nav-link,
.navbar-brand {
  color: #fff !important;
}
.navbar .nav-link.active {
  font-weight: 600;
  text-decoration: underline;
}
.logo {
  width: 110px;
}

/* CARD */
.card-custom {
  background: #fff;
  border-radius: 18px;
  padding: 25px;
  margin-top: 40px;
  box-shadow: 0 15px 30px rgba(0,0,0,0.06);
}

/* SEARCH */
.search-box {
  max-width: 300px;
  border-radius: 20px;
}

/* TABLE */
.table thead {
  background: #f0f4ff;
}
.table th {
  border: none;
  font-weight: 600;
}
.table td {
  border: none;
  vertical-align: middle;
}
.table tbody tr {
  border-bottom: 1px solid #eee;
}

/* BADGE */
.badge-mahasiswa {
  background: #6c757d;
  color: #fff;
  padding: 6px 14px;
  border-radius: 20px;
  font-size: 12px;
}

/* BUTTON */
.btn-edit {
  background: #e83e8c;
  color: white;
  padding: 6px 16px;
  border-radius: 20px;
  font-size: 12px;
  text-decoration: none;
}
.btn-edit:hover {
  background: #d63384;
  color: #fff;
}
</style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg">
  <div class="container">
    <a class="navbar-brand fw-bold" href="#">
      <i class="bi bi-journal-text me-2"></i>Data Tamu
    </a>

    <div class="collapse navbar-collapse justify-content-end">
      <ul class="navbar-nav">
        <li class="nav-item"><a class="nav-link" href="dashboard_admin.php">Home</a></li>
        <li class="nav-item"><a class="nav-link active" href="data_tamu.php">Data Tamu</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Laporan</a></li>
        <li class="nav-item"><a class="nav-link" href="pengaturan_akun.php">Pengaturan Akun</a></li>
        <li class="nav-item"><a class="nav-link text-warning" href="logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- CONTENT -->
<div class="container">
  <div class="card-custom">
    <h4 class="fw-bold mb-4">Daftar Buku Tamu</h4>

    <input type="text" class="form-control search-box mb-3" placeholder="Cari nama, instansi, keperluan...">

    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th>#</th>
            <th>Nama Tamu</th>
            <th>Instansi</th>
            <th>Jenis Tamu</th>
            <th>Keperluan</th>
            <th>Waktu Kunjungan</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>

        <?php $no=1; while($data = mysqli_fetch_array($tamu)) { ?>
          <tr>
            <td><?= $no++; ?></td>
            <td><?= $data['nama_lengkap']; ?></td>
            <td><?= $data['institusi']; ?></td>
            <td>
              <span class="badge-mahasiswa">
                <?= $data['jenis_pengguna']; ?>
              </span>
            </td>
            <td><?= $data['keperluan']; ?></td>
            <td><?= $data['created_at']; ?></td>
            <td>
              <a href="edit.php?id=<?= $data['id']; ?>" class="btn-edit">Edit</a>
            </td>
          </tr>
        <?php } ?>

        </tbody>
      </table>
    </div>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Jika mau mengambil nama staff dari session
// session_start();
// $nama_staff = $_SESSION['nama'] ?? 'Nama Staff';
include 'koneksi.php';
$tamu = mysqli_query($koneksi, "SELECT * FROM data_tamu ORDER BY id DESC");
$q1 = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total 
    FROM data_tamu
    WHERE DATE(created_at) = CURDATE()
");
$data_hari_ini = mysqli_fetch_assoc($q1)['total'];
$q2 = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total 
    FROM data_tamu
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)
");
$data_bulan_terakhir = mysqli_fetch_assoc($q2)['total'];
$q3 = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total 
    FROM data_tamu
    WHERE DATE(created_at) = CURDATE()
    AND jenis_pengguna = 'mahasiswa'
");
$mahasiswa_hari_ini = mysqli_fetch_assoc($q3)['total'];
$q4 = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total 
    FROM data_tamu  
    WHERE DATE(created_at) = CURDATE()
    AND jenis_pengguna = 'instansi'
");
$instansi_hari_ini = mysqli_fetch_assoc($q4)['total'];
$nama_staff = "Nama Staff"; // sementara pakai dummy
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Buku Tamu</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .navbar {
      background-color: rgba(0, 0, 0, 0.8);
    }
    .navbar-brand, .nav-link, .navbar-text {
      color: white !important;
    }
    .card {
      text-align: center;
      border-radius: 10px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .table th {
      background-color: #f1f1f1;
    }
    .content-section {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
    }
    .left-section {
      flex: 2;
      min-width: 400px;
    }
    .right-section {
      flex: 1;
      min-width: 300px;
    }
  </style>
</head>
<body>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg">
    <div class="container-fluid px-4">
      <a class="navbar-brand fw-bold" href="#">
        <img src="logo_polibatam.png" alt="Logo" width="40" class="me-2">
        Selamat Datang, <?= $nama_staff ?>
      </a>
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

  <!-- Statistik -->
  <div class="container my-4">
    <div class="row g-3">

      <div class="col-md-3">
        <div class="card p-3">
          <h5>Total Tamu Hari Ini</h5>
          <h3>
            <?= $data_hari_ini;?>
          </h3>
        </div>
      </div>

      <div class="col-md-3">
        <div class="card p-3">
          <h5>Total Kunjungan Bulanan</h5>
          <h3>
            <?= $data_bulan_terakhir;?>
          </h3>
        </div>
      </div>

      <div class="col-md-3">
        <div class="card p-3">
          <h5>Tamu Mahasiswa</h5>
          <h3>
          <?= $mahasiswa_hari_ini;?>
          </h3>
        </div>
      </div>

      <div class="col-md-3">
        <div class="card p-3">
          <h5>Tamu Non-Mahasiswa</h5>
          <h3>
            <?= $instansi_hari_ini;?>
          </h3>
        </div>
      </div>

    </div>
  </div>

  <!-- Konten Utama -->
  <div class="container mb-5">
    <div class="content-section">

      <!-- Daftar Buku Tamu -->
      <div class="left-section card p-3">
        <h5 class="fw-bold mb-3">Daftar Buku Tamu Terbaru</h5>
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>No.</th>
              <th>Nama Tamu</th>
              <th>Jenis Tamu</th>
              <th>Instansi</th>
              <th>Keperluan</th>
            </tr>
          </thead>
          <tbody>

            <!-- Contoh data (bisa diganti dari database nanti) -->
            <?php
    $no = 1;
    while ($data = mysqli_fetch_array($tamu)) {
    ?>
    <tr>
        <td><?= $no++; ?></td>
        <td><?= $data['nama_lengkap']; ?></td>
        <td><?= $data['jenis_pengguna']; ?></td>
        <td><?= $data['institusi']; ?></td>
        <td><?= $data['keperluan']; ?></td>
    </tr>
    <?php } ?>
          </tbody>
        </table>

        <a href="data_tamu.php" class="btn btn-secondary">Lihat Semua Data</a>
      </div>

      <!-- Filter dan Pencarian -->
      <div class="right-section card p-3">
        <h5 class="fw-bold mb-3">Filter & Data Pencarian</h5>

        <form>
          <div class="mb-3">
            <label class="form-label">Nama Tamu</label>
            <input type="text" class="form-control" placeholder="Masukkan nama tamu">
          </div>

          <div class="mb-3">
            <label class="form-label">Tanggal Kunjungan</label>
            <input type="date" class="form-control">
          </div>

          <div class="mb-3">
            <label class="form-label">Jenis Tamu</label>
            <select class="form-select">
              <option>Mahasiswa</option>
              <option>Non-Mahasiswa</option>
            </select>
          </div>

          <a href="laporan.php" class="btn btn-success w-100">Cetak Laporan</a>

        </form>
      </div>

    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

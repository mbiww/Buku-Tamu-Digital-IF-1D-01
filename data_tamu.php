<?php
// Jika nanti mau ambil data dari database, taruh query di sini.
// Contoh:
// include 'koneksi.php';
// $tamu = mysqli_query($conn, "SELECT * FROM tamu");
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
      background-color: #f4f4f4;
      font-family: "Times New Roman", serif;
    }
    .navbar {
      background-color: #d9d9d9;
    }
    .card-custom {
      background-color: #d9d9d9;
      border-radius: 15px;
      padding: 25px;
      margin-top: 40px;
    }
    .table th, .table td {
      vertical-align: middle;
    }
    .logo {
      width: 120px;
    }
    .search-box {
      width: 300px;
      margin-bottom: 15px;
    }
    .table th {
      border-bottom: 2px solid black;
    }
  </style>
</head>
<body>

  <!-- NAVBAR -->
  <nav class="navbar navbar-expand-lg navbar-light">
    <div class="container">
      <a class="navbar-brand fw-bold" href="#">
        <img src="https://upload.wikimedia.org/wikipedia/commons/0/09/Logo_Politeknik_Negeri_Batam.png" alt="Logo Polibatam" class="logo">
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav">
          <li class="nav-item"><a class="nav-link" href="dashboard_admin.php">Home</a></li>
          <li class="nav-item"><a class="nav-link active fw-bold" href="data_tamu.php">Data Tamu</a></li>
          <li class="nav-item"><a class="nav-link" href="#">Laporan</a></li>
          <li class="nav-item"><a class="nav-link" href="#">Pengaturan Akun</a></li>
          <li class="nav-item"><a class="nav-link" href="#">Logout</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- MAIN CONTENT -->
  <div class="container">
    <div class="card-custom">
      <h4 class="fw-bold mb-4">Daftar Tamu</h4>
      <input type="text" class="form-control search-box" placeholder="Search...">

      <table class="table table-bordered mt-3">
        <thead>
          <tr>
            <th>No.</th>
            <th>Nama Tamu</th>
            <th>Instansi</th>
            <th>Jenis Tamu</th>
            <th>Keperluan</th>
            <th>Waktu Kunjungan</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>1.</td>
            <td>Dimas Setiawan</td>
            <td>Mahasiswa</td>
            <td>Mahasiswa</td>
            <td>Mengambil Surat</td>
            <td>10.30-11.00, 01/08/25</td>
            <td><a href="#" class="text-decoration-none text-dark fw-bold">Edit</a></td>
          </tr>
          <tr>
            <td>2.</td>
            <td>Bu Rani</td>
            <td>PT. Maju Jaya</td>
            <td>Non-Mahasiswa</td>
            <td>Menemui Kepala TU</td>
            <td>13.00-14.00, 02/08/25</td>
            <td><a href="#" class="text-decoration-none text-dark fw-bold">Edit</a></td>
          </tr>
          <tr>
            <td>3.</td>
            <td>Andi Pratama</td>
            <td>Alumni</td>
            <td>Non-Mahasiswa</td>
            <td>Menagambil Ijazah</td>
            <td>13.30-14.00, 02/08/25</td>
            <td><a href="#" class="text-decoration-none text-dark fw-bold">Edit</a></td>
          </tr>
          <tr>
            <td>4.</td>
            <td>Umar</td>
            <td>Mahasiswa</td>
            <td>Mahasiswa</td>
            <td>Mengambil Surat</td>
            <td>15.00-15.30, 02/08/25</td>
            <td><a href="#" class="text-decoration-none text-dark fw-bold">Edit</a></td>
          </tr>
        </tbody>
      </table>

    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

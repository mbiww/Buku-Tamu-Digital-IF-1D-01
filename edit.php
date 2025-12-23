<?php
session_start();
include 'koneksi.php';

/* =====================
   CEK LOGIN ADMIN
===================== */
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: login_user.php');
    exit;
}

/* =====================
   AMBIL ID TAMU
===================== */
if (!isset($_GET['id'])) {
    header('Location: data_tamu.php');
    exit;
}

$id = $_GET['id'];

/* =====================
   HAPUS DATA
===================== */
if (isset($_GET['hapus'])) {
    mysqli_query($koneksi, "DELETE FROM data_tamu WHERE id='$id'");
    header('Location: data_tamu.php');
    exit;
}

/* =====================
   AMBIL DATA TAMU
===================== */
$query = mysqli_query($koneksi, "SELECT * FROM data_tamu WHERE id='$id'");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    header('Location: data_tamu.php');
    exit;
}

/* =====================
   UPDATE DATA
===================== */
if (isset($_POST['update'])) {

    $nama       = $_POST['nama_lengkap'];
    $institusi  = $_POST['institusi'];
    $no_wa      = $_POST['no_wa'];
    $keperluan  = $_POST['keperluan'];

    mysqli_query($koneksi, "
        UPDATE data_tamu SET
        nama_lengkap='$nama',
        institusi='$institusi',
        no_wa='$no_wa',
        keperluan='$keperluan'
        WHERE id='$id'
    ");

    header('Location: data_tamu.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Edit Data Tamu</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-4">
  <div class="card p-4 shadow">

    <h4 class="mb-3">Edit Data Tamu</h4>

    <form method="POST">

      <div class="mb-3">
        <label>Nama Lengkap</label>
        <input type="text" name="nama_lengkap" class="form-control"
               value="<?= $data['nama_lengkap']; ?>" required>
      </div>

      <div class="mb-3">
        <label>Institusi</label>
        <input type="text" name="institusi" class="form-control"
               value="<?= $data['institusi']; ?>">
      </div>

      <div class="mb-3">
        <label>No WhatsApp</label>
        <input type="text" name="no_wa" class="form-control"
               value="<?= $data['no_wa']; ?>">
      </div>

      <div class="mb-3">
        <label>Keperluan</label>
        <textarea name="keperluan" class="form-control" rows="4"><?= $data['keperluan']; ?></textarea>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" name="update" class="btn btn-success">
          Simpan Perubahan
        </button>

        <a href="edit.php?id=<?= $id ?>&hapus=true"
           class="btn btn-danger"
           onclick="return confirm('Yakin ingin menghapus data ini?')">
          Hapus Data
        </a>

        <a href="data_tamu.php" class="btn btn-secondary">
          Kembali
        </a>
      </div>

    </form>

  </div>
</div>

</body>
</html>

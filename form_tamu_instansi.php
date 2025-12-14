<?php
// Kode PHP untuk menangani form tamu
session_start();
require_once 'koneksi.php';

if (!isset($_SESSION['no_id']) || $_SESSION['role'] != 'instansi') {
    header('Location: login_user.php');
    exit;
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_lengkap = $_POST['nama_lengkap'];
    $no_id = $_POST['no_id'];
    $institusi = $_POST['institusi'];
    $alamat = $_POST['alamat'];
    $no_wa = $_POST['number'];
    $keperluan = $_POST['message'];
    
    // Validasi input
    if (empty($nama_lengkap) || empty($no_id) || empty($institusi) || empty($alamat) || empty($no_wa) || empty($keperluan)) {
        $error = "Semua field harus diisi!";
    } else {
        try {
            // Simpan data tamu ke database
            $stmt = $pdo->prepare("INSERT INTO data_tamu (nama_lengkap, no_id, institusi, alamat, no_wa, keperluan, jenis_pengguna, created_at) VALUES (?, ?, ?, ?, ?, ?, 'instansi', NOW())");
            
            if ($stmt->execute([$nama_lengkap, $no_id, $institusi, $alamat, $no_wa, $keperluan])) {
                $success = "Data tamu berhasil dikirim!";
                // Reset form setelah berhasil
                $_POST = array();
            } else {
                $error = "Terjadi kesalahan saat menyimpan data!";
            }
        } catch (PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pendaftaran Tamu Instansi</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f4f4f4;
      font-family: "Times New Roman", serif;
    }
    .login-container {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .login-card {
      background-color: #e6e6e6;
      border-radius: 10px;
      padding: 30px;
      width: 350px;
      box-shadow: 0 2px 8px rgb(0,0,0,0.2);
    }
    .logo {
      max-width: 100%;
      height: auto;
      margin-right: 60px;
    }
    .login-btn {
      background-color: #fc671a;
      color: #fff;
      font-weight: bold;
      border: none;
      transition: 0.3s;
    }
    .login-btn:hover {
      background-color: #af4712;
      color: #fff;
    }
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
    }
    textarea {
    border-radius: 6px;
    resize: none;
    outline: none;
    padding: 0;
    font-family: inherit;
    font-size: inherit;
    }
  </style>
</head>
<body>

  <div class="container login-container">
    <div class="row align-items-center">
      <div class="col-md-6 text-center">
        <img src="//learning-if.polibatam.ac.id/pluginfile.php/1/theme_moove/logo/1756270195/01_Logo_1_Utama_Polibatam_Vertikal%402x.png" alt="Polibatam Logo" class="logo">
      </div>

      <div class="col-md-6 d-flex justify-content-center">
        <div class="login-card">
          <h5 class="text-center mb-4 fw-bold">Pendaftaran Tamu</h5>
          
          <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
          <?php endif; ?>
          
          <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
          <?php endif; ?>

          <form action="form_tamu_instansi.php" method="POST">
            <div class="mb-3">
              <label>Nama Lengkap :</label>
              <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
            </div>
            <div class="mb-3">
              <label>No Identitas (NIM, NIDN, NIK) :</label>
              <input type="number" class="form-control" id="no_id" name="no_id" value="<?php echo isset($_POST['no_id']) ? htmlspecialchars($_POST['no_id']) : ''; ?>" required>
            </div>
            <div class="mb-3">
              <label>Institusi :</label>
              <input type="text" class="form-control" id="institusi" name="institusi" value="<?php echo isset($_POST['institusi']) ? htmlspecialchars($_POST['institusi']) : ''; ?>" required>
            </div>
            <div class="mb-3">
              <label>Alamat :</label>
              <input type="text" class="form-control" id="alamat" name="alamat" value="<?php echo isset($_POST['alamat']) ? htmlspecialchars($_POST['alamat']) : ''; ?>" required>
            </div>
            <div class="mb-3">
              <label>Nomor WA :</label>
              <input type="number" class="form-control" id="number" name="number" value="<?php echo isset($_POST['number']) ? htmlspecialchars($_POST['number']) : ''; ?>" required>
            </div>
            <div class="mb-3">
              <label>Keperluan Kunjungan/Bertamu :</label>
              <textarea name="message" rows="5" cols="34" placeholder="Tuliskan Pesan Anda" required><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
            </div>
            <button type="submit" class="btn login-btn w-100">Kirim</button>
          </form>
        </div>
      </div>
    </div>
  </div>

</body>
</html>
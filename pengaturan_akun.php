<?php
session_start();
include 'koneksi.php';

// CEK SESSION DAN ROLE
if (!isset($_SESSION['no_id']) || $_SESSION['role'] != 'admin') {
    header('Location: login/login.php');
    exit;
}

$no_id_user = $_SESSION['no_id'];
$role_user = $_SESSION['role'];

// AMBIL DATA USER
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
   PROSES HAPUS ADMIN
========================= */
if (isset($_GET['hapus'])) {
    $id_hapus = intval($_GET['hapus']);
    $current_admin_id = $_SESSION['no_id']; // ID admin yang sedang login
    
    // Cek apakah admin yang akan dihapus adalah admin yang sedang login
    $cek_admin = mysqli_query($koneksi, 
        "SELECT no_id FROM users WHERE id = '$id_hapus' AND role = 'admin'");
    
    if (mysqli_num_rows($cek_admin) > 0) {
        $admin_data = mysqli_fetch_assoc($cek_admin);
        
        // Cegah admin menghapus akun sendiri
        if ($admin_data['no_id'] == $current_admin_id) {
            $error = "Tidak dapat menghapus akun Anda sendiri!";
        } else {
            // Hapus akun admin
            $hapus = mysqli_query($koneksi, "DELETE FROM users WHERE id = '$id_hapus'");
            
            if ($hapus) {
                $success = "Akun admin berhasil dihapus!";
            } else {
                $error = "Gagal menghapus akun admin!";
            }
        }
    } else {
        $error = "Akun admin tidak ditemukan!";
    }
    
    // Redirect untuk menghapus parameter GET
    header("Location: pengaturan_akun.php");
    exit;
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
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<style>
body {
  background: #f5f7fb;
  font-family: 'Segoe UI', sans-serif;
}

/* NAVBAR STYLING */
.navbar {
    background: #0d6efd;
    padding: 12px 0;
    box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
    position: sticky;
    top: 0;
    z-index: 1000;
}

.navbar-brand {
    font-size: 1.5rem;
    display: flex;
    align-items: center;
}

.navbar-brand i {
    font-size: 1.8rem;
    background: rgba(255, 255, 255, 0.2);
    padding: 8px;
    border-radius: 10px;
    margin-right: 10px;
}

.nav-link {
    font-weight: 500;
    padding: 8px 15px !important;
    margin: 0 5px;
    border-radius: 8px;
}

.nav-link:hover, .nav-link.active {
    background-color: rgba(255, 255, 255, 0.15);
}

.nav-link.text-danger:hover {
    background-color: rgba(220, 53, 69, 0.15);
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

.btn-danger {
  border-radius: 8px;
  padding: 5px 15px;
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

<script>
function confirmDelete(id, nama) {
    if (confirm(`Apakah Anda yakin ingin menghapus akun admin "${nama}"?`)) {
        window.location.href = `pengaturan_akun.php?hapus=${id}`;
    }
    return false;
}
</script>
</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-dark shadow-sm">
        <div class="container-fluid px-3 px-md-4">
            <a class="navbar-brand fw-bold" href="dashboard_admin.php">
                <i class="bi bi-journal-text me-2"></i>Dashboard Admin
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="dashboard_admin.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="data_tamu.php">Data Tamu</a></li>
                    <li class="nav-item"><a class="nav-link" href="laporan.php">Laporan</a></li>
                    <li class="nav-item"><a class="nav-link active" href="pengaturan_akun.php">Pengaturan Akun</a></li>
                    <li class="nav-item"><a class="nav-link text-danger" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>


<div class="container my-5">

<!-- ALERT -->
<?php if ($success): ?>
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    <?= $success; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php endif; ?>

<?php if ($error): ?>
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    <?= $error; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
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
      <i class="bi bi-person-plus"></i> Tambah Admin
    </button>
  </form>
</div>

<!-- DAFTAR AKUN ADMIN -->
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
          <th>Tanggal Dibuat</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php 
        $no = 1; 
        $current_admin_no_id = $_SESSION['no_id']; // ID admin yang sedang login
        while($a = mysqli_fetch_assoc($admin)) { 
            $is_current_admin = ($a['no_id'] == $current_admin_no_id);
        ?>
        <tr>
          <td><?= $no++; ?></td>
          <td>
            <?= $a['nama_lengkap']; ?>
            <?php if ($is_current_admin): ?>
              <span class="badge bg-info ms-2">Anda</span>
            <?php endif; ?>
          </td>
          <td><?= $a['email']; ?></td>
          <td><?= $a['no_id']; ?></td>
          <td><?= $a['role']; ?></td>
          <td><?= date('d/m/Y', strtotime($a['created_at'])); ?></td>
          <td>
            <?php if (!$is_current_admin): ?>
              <button onclick="return confirmDelete(<?= $a['id']; ?>, '<?= addslashes($a['nama_lengkap']); ?>')" 
                      class="btn btn-sm btn-danger">
                <i class="bi bi-trash"></i> Hapus
              </button>
            <?php else: ?>
              <span class="text-muted">-</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</div>

</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

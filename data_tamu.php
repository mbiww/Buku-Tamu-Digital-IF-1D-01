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

// Inisialisasi variabel pencarian
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Query dengan kondisi pencarian
if (!empty($search)) {
    $search = mysqli_real_escape_string($koneksi, $search);
    $query = "SELECT * FROM data_tamu 
              WHERE nama_lengkap LIKE '%$search%' 
                 OR institusi LIKE '%$search%' 
                 OR keperluan LIKE '%$search%' 
                 OR no_id LIKE '%$search%'
              ORDER BY created_at DESC";
} else {
    $query = "SELECT * FROM data_tamu ORDER BY created_at DESC";
}

$tamu = mysqli_query($koneksi, $query);

// Hitung total hasil
$total_results = mysqli_num_rows($tamu);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Data Tamu</title>

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
  border: 2px solid #dee2e6;
  transition: all 0.3s ease;
}

.search-box:focus {
  border-color: #0d6efd;
  box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

.search-btn {
  border-radius: 20px;
  padding: 8px 20px;
}

.search-container {
  display: flex;
  gap: 10px;
  align-items: center;
  margin-bottom: 20px;
}

.search-info {
  font-size: 14px;
  color: #6c757d;
  margin-left: auto;
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
  transition: background-color 0.2s;
}
.table tbody tr:hover {
  background-color: #f8f9fa;
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
  border: none;
  transition: background 0.3s;
}
.btn-edit:hover {
  background: #d63384;
  color: #fff;
}

/* NO RESULTS */
.no-results {
  text-align: center;
  padding: 40px;
  color: #6c757d;
}
.no-results i {
  font-size: 48px;
  margin-bottom: 15px;
  color: #dee2e6;
}
</style>
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
                <li class="nav-item"><a class="nav-link active" href="data_tamu.php">Data Tamu</a></li>
                <li class="nav-item"><a class="nav-link" href="pengaturan_akun.php">Pengaturan Akun</a></li>
                <li class="nav-item"><a class="nav-link text-danger" href="logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- CONTENT -->
<div class="container">
  <div class="card-custom">
    <h4 class="fw-bold mb-4">Daftar Buku Tamu</h4>

    <!-- FORM PENCARIAN -->
    <form method="GET" action="" class="search-container">
      <div class="input-group">
        <input type="text" 
               class="form-control search-box" 
               name="search" 
               placeholder="Cari nama, instansi, keperluan, atau ID..." 
               value="<?php echo htmlspecialchars($search); ?>">
        <button class="btn btn-primary search-btn" type="submit">
          <i class="bi bi-search"></i> Cari
        </button>
      </div>
      
      <?php if (!empty($search)): ?>
        <div class="search-info">
          <i class="bi bi-info-circle"></i> 
          Ditemukan <?php echo $total_results; ?> hasil untuk "<?php echo htmlspecialchars($search); ?>"
        </div>
      <?php endif; ?>
    </form>

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
            <th>Selesai Kunjungan</th>  <!-- Kolom baru -->
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>

        <?php 
        if ($total_results > 0) {
          $no = 1; 
          while($data = mysqli_fetch_array($tamu)) { 
        ?>
          <tr>
            <td><?= $no++; ?></td>
            <td><?= htmlspecialchars($data['nama_lengkap']); ?></td>
            <td><?= htmlspecialchars($data['institusi']); ?></td>
            <td>
              <span class="badge-mahasiswa">
                <?= htmlspecialchars($data['jenis_pengguna']); ?>
              </span>
            </td>
            <td><?= htmlspecialchars($data['keperluan']); ?></td>
            <td>
              <?php 
              if ($data['waktu_checkin']) {
                  echo date('d/m/Y H:i', strtotime($data['waktu_checkin']));
              } else {
                  echo 'Belum check-in';
              }
              ?>
            </td>
            <td>
              <?php 
              if ($data['waktu_checkout']) {
                  echo date('d/m/Y H:i', strtotime($data['waktu_checkout']));
              } else {
                  echo 'Belum check-out';
              }
              ?>
            </td>
            <td>
              <a href="edit.php?id=<?= $data['id']; ?>" class="btn-edit">
                <i class="bi bi-pencil-square"></i> Edit
              </a>
            </td>
          </tr>
        <?php 
          }
        } else {
        ?>
          <tr>
            <td colspan="8" class="no-results">  <!-- Ganti dari 7 ke 8 -->
              <i class="bi bi-search"></i>
              <h5 class="mt-3">Tidak ada data ditemukan</h5>
              <?php if (!empty($search)): ?>
                <p class="text-muted">Tidak ada hasil untuk "<?php echo htmlspecialchars($search); ?>"</p>
              <?php else: ?>
                <p class="text-muted">Belum ada data tamu yang terdaftar</p>
              <?php endif; ?>
            </td>
          </tr>
        <?php } ?>

        </tbody>
      </table>
    </div>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Fungsi untuk menyorot teks hasil pencarian
function highlightSearchText() {
  const searchTerm = "<?php echo addslashes($search); ?>";
  if (searchTerm.trim() === '') return;
  
  const tableCells = document.querySelectorAll('table tbody td');
  const regex = new RegExp(`(${searchTerm})`, 'gi');
  
  tableCells.forEach(cell => {
    if (cell.textContent.match(regex)) {
      const originalText = cell.innerHTML;
      const highlightedText = originalText.replace(
        regex, 
        '<mark class="bg-warning p-1 rounded">$1</mark>'
      );
      cell.innerHTML = highlightedText;
    }
  });
}

// Jalankan saat halaman dimuat
document.addEventListener('DOMContentLoaded', highlightSearchText);

// Auto-focus pada input pencarian
document.addEventListener('DOMContentLoaded', function() {
  const searchInput = document.querySelector('input[name="search"]');
  if (searchInput) {
    searchInput.focus();
    // Tempatkan kursor di akhir teks
    searchInput.setSelectionRange(searchInput.value.length, searchInput.value.length);
  }
});
</script>

</body>
</html>
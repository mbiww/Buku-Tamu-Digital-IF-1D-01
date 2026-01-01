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

// AMBIL PARAMETER FILTER
$keyword = $_GET['keyword'] ?? '';
$jenis_filter = $_GET['jenis_tamu'] ?? '';
$tanggal_filter = $_GET['tanggal'] ?? '';
$status_filter = $_GET['status'] ?? '';

// PAGINATION
$limit = 10; // Jumlah data per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// QUERY DINAMIS DATA TAMU
$sql_tamu = "SELECT * FROM data_tamu WHERE 1=1";
$sql_count = "SELECT COUNT(*) as total FROM data_tamu WHERE 1=1";

if (!empty($keyword)) {
    $keyword_escaped = mysqli_real_escape_string($koneksi, $keyword);
    $sql_tamu .= " AND (nama_lengkap LIKE '%$keyword_escaped%' 
                      OR institusi LIKE '%$keyword_escaped%' 
                      OR keperluan LIKE '%$keyword_escaped%')";
    $sql_count .= " AND (nama_lengkap LIKE '%$keyword_escaped%' 
                      OR institusi LIKE '%$keyword_escaped%' 
                      OR keperluan LIKE '%$keyword_escaped%')";
}

if (!empty($jenis_filter) && $jenis_filter != 'semua') {
    $jenis_filter_escaped = mysqli_real_escape_string($koneksi, $jenis_filter);
    $sql_tamu .= " AND jenis_pengguna = '$jenis_filter_escaped'";
    $sql_count .= " AND jenis_pengguna = '$jenis_filter_escaped'";
}

if (!empty($tanggal_filter)) {
    $tanggal_filter_escaped = mysqli_real_escape_string($koneksi, $tanggal_filter);
    $sql_tamu .= " AND DATE(created_at) = '$tanggal_filter_escaped'";
    $sql_count .= " AND DATE(created_at) = '$tanggal_filter_escaped'";
}

if (!empty($status_filter) && $status_filter != 'semua') {
    $status_filter_escaped = mysqli_real_escape_string($koneksi, $status_filter);
    $sql_tamu .= " AND status_aktif = '$status_filter_escaped'";
    $sql_count .= " AND status_aktif = '$status_filter_escaped'";
}

$sql_tamu .= " ORDER BY id DESC LIMIT $offset, $limit";
$tamu = mysqli_query($koneksi, $sql_tamu);

// HITUNG TOTAL DATA UNTUK PAGINATION
$count_result = mysqli_query($koneksi, $sql_count);
$total_data = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_data / $limit);

// QUERY STATISTIK
$q1 = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM data_tamu WHERE DATE(created_at) = CURDATE()");
$data_hari_ini = mysqli_fetch_assoc($q1)['total'];

$q2 = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM data_tamu WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)");
$data_bulan_terakhir = mysqli_fetch_assoc($q2)['total'];

$q3 = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM data_tamu WHERE jenis_pengguna = 'mahasiswa'");
$mahasiswa_hari_ini = mysqli_fetch_assoc($q3)['total'];

$q4 = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM data_tamu WHERE jenis_pengguna = 'instansi'");
$instansi_hari_ini = mysqli_fetch_assoc($q4)['total'];

// PROSES CHECK OUT JIKA ADA REQUEST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'checkout') {
    $id = intval($_POST['id']);
    
    if ($id > 0) {
        $sql = "UPDATE data_tamu 
                SET status_aktif = 'tidak aktif', 
                    waktu_checkout = NOW() 
                WHERE id = ? AND status_aktif = 'aktif'";
        
        $stmt = mysqli_prepare($koneksi, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        
        if (mysqli_stmt_execute($stmt)) {
            $message = mysqli_stmt_affected_rows($stmt) > 0 
                ? ['type' => 'success', 'text' => 'Check out berhasil'] 
                : ['type' => 'warning', 'text' => 'Data tidak ditemukan atau sudah check out'];
        } else {
            $message = ['type' => 'danger', 'text' => 'Database error'];
        }
        
        mysqli_stmt_close($stmt);
    } else {
        $message = ['type' => 'danger', 'text' => 'ID tidak valid'];
    }
    
    // Redirect untuk refresh data
    header("Location: dashboard_admin.php?message=" . urlencode($message['text']) . "&type=" . $message['type']);
    exit;
}

// Tampilkan pesan jika ada
$message = isset($_GET['message']) ? $_GET['message'] : '';
$message_type = isset($_GET['type']) ? $_GET['type'] : '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Buku Tamu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background-color: #f5f7fb;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            line-height: 1.6;
            min-height: 100vh;
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
        
        /* STATISTICS CARDS */
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            height: 100%;
            border: none;
            position: relative;
            overflow: hidden;
        }
        
        .stat-card:hover {
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
        }
        
        .stat-card:nth-child(1)::before {
            background: linear-gradient(to right, #4361ee, #4895ef);
        }
        
        .stat-card:nth-child(2)::before {
            background: linear-gradient(to right, #4cc9f0, #3a0ca3);
        }
        
        .stat-card:nth-child(3)::before {
            background: linear-gradient(to right, #7209b7, #f72585);
        }
        
        .stat-card:nth-child(4)::before {
            background: linear-gradient(to right, #f8961e, #f9c74f);
        }
        
        .stat-card h6 {
            font-size: 0.9rem;
            color: #6c757d;
            margin-bottom: 10px;
            font-weight: 600;
        }
        
        .stat-card h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0;
        }
        
        /* MAIN CONTENT CARDS */
        .main-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: none;
            height: 100%;
            overflow: hidden;
        }
        
        .main-card:hover {
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }
        
        .main-card .card-body {
            padding: 25px;
        }
        
        .main-card .card-title {
            color: #2d3748;
            font-weight: 700;
            font-size: 1.3rem;
            position: relative;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .main-card .card-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 4px;
            background: linear-gradient(to right, #4361ee, #4cc9f0);
            border-radius: 2px;
        }
        
        /* TABLE STYLING */
        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        .table {
            margin-bottom: 0;
        }
        
        .table thead {
            background: linear-gradient(135deg, #4361ee, #3a0ca3);
            color: white;
        }
        
        .table thead th {
            border: none;
            padding: 15px 12px;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .table tbody tr:hover {
            background-color: rgba(67, 97, 238, 0.05);
        }
        
        .table tbody td {
            padding: 15px 12px;
            vertical-align: middle;
            border-bottom: 1px solid #e9ecef;
        }
        
        .badge {
            padding: 6px 12px;
            font-weight: 600;
            font-size: 0.75rem;
            border-radius: 20px;
            border: none;
            cursor: default;
        }
        
        .badge.bg-secondary {
            background: linear-gradient(135deg, #6c757d, #495057) !important;
        }
        
        .badge.bg-success {
            background: linear-gradient(135deg, #4cc9f0, #4895ef) !important;
        }
        
        .badge.bg-danger {
            background: linear-gradient(135deg, #f72585, #b5179e) !important;
        }
        
        .badge-checkout {
            padding: 6px 12px;
            font-weight: 600;
            font-size: 0.75rem;
            border-radius: 20px;
            background: linear-gradient(135deg, #dc3545, #c82333) !important;
            color: white !important;
            text-decoration: none;
            display: inline-block;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
        }
        
        .badge-checkout:hover {
            background: linear-gradient(135deg, #c82333, #bd2130) !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
        }
        
        .badge-checkout:active {
            transform: translateY(0);
        }
        
        /* FORM STYLING */
        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
        }
        
        .form-control, .form-select {
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 15px;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #4361ee;
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
        }
        
        /* BUTTON STYLING */
        .btn {
            border-radius: 10px;
            padding: 12px 20px;
            font-weight: 600;
            border: none;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #4361ee, #3a0ca3);
            border: none;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #3a56d4, #2f0c91);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
        }
        
        .btn-outline-primary {
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
        }
        
        .btn-outline-primary:hover {
            background: linear-gradient(135deg, #4361ee, #3a0ca3);
        }
        
        .btn-success {
            background: linear-gradient(135deg, #4cc9f0, #3a0ca3);
            border: none;
        }
        
        .btn-success:hover {
            background: linear-gradient(135deg, #3ab7dc, #2f0c91);
            box-shadow: 0 5px 15px rgba(76, 201, 240, 0.3);
        }
        
        .btn-sm {
            padding: 8px 16px;
            font-size: 0.85rem;
        }

        /* PAGINATION STYLING */
        .pagination-container {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
        }
        
        .pagination-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 16px;
            border-radius: 8px;
            background: linear-gradient(135deg, #4361ee, #3a0ca3);
            color: white;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            border: none;
        }
        
        .pagination-btn:hover {
            background: linear-gradient(135deg, #3a56d4, #2f0c91);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(67, 97, 238, 0.3);
            color: white;
        }
        
        .pagination-btn:disabled {
            background: #6c757d;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        
        .pagination-btn i {
            margin-right: 5px;
        }
        
        /* NO DATA STYLING */
        .no-data {
            padding: 40px 20px;
            text-align: center;
            color: #6c757d;
        }
        
        .no-data i {
            font-size: 3rem;
            margin-bottom: 15px;
            opacity: 0.5;
        }
        
        /* HR STYLING */
        hr {
            background: linear-gradient(to right, transparent, #e2e8f0, transparent);
            height: 2px;
            opacity: 0.7;
            margin: 25px 0;
        }
        
        /* MESSAGE ALERT */
        .alert-fixed {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            animation: slideIn 0.3s ease-out;
        }
        
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        /* RESPONSIVE ADJUSTMENTS */
        @media (max-width: 768px) {
            .stat-card h2 {
                font-size: 2rem;
            }
            
            .main-card .card-body {
                padding: 20px;
            }
            
            .table thead th {
                padding: 12px 8px;
                font-size: 0.8rem;
            }
            
            .table tbody td {
                padding: 12px 8px;
            }
            
            .btn {
                padding: 10px 16px;
            }
            
            .navbar-brand {
                font-size: 1.2rem;
            }
            
            .alert-fixed {
                left: 10px;
                right: 10px;
                min-width: auto;
            }
            
            .badge, .badge-checkout {
                padding: 5px 10px;
                font-size: 0.7rem;
            }

            .pagination-container {
                justify-content: center;
            }
            
            .pagination-btn {
                padding: 6px 12px;
                font-size: 0.8rem;
            }
        }
    </style>
</head>
<body class="bg-light">
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
                    <li class="nav-item"><a class="nav-link active" href="dashboard_admin.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="data_tamu.php">Data Tamu</a></li>
                    <li class="nav-item"><a class="nav-link" href="pengaturan_akun.php">Pengaturan Akun</a></li>
                    <li class="nav-item"><a class="nav-link text-danger" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- PESAN ALERT -->
    <?php if ($message): ?>
    <div class="alert-fixed">
        <div class="alert alert-<?= $message_type ?> alert-dismissible fade show shadow-lg" role="alert">
            <?= htmlspecialchars($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
    <?php endif; ?>

    <!-- STATISTIK -->
    <div class="container-fluid mt-4">
        <div class="row g-3 px-3 px-md-4">
            <div class="col-12 col-sm-6 col-md-3">
                <div class="stat-card">
                    <h6 class="card-subtitle">Total Tamu Hari Ini</h6>
                    <h2 class="text-primary"><?= $data_hari_ini; ?></h2>
                    <div class="mt-3">
                        <i class="bi bi-people-fill text-primary fs-4"></i>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="stat-card">
                    <h6 class="card-subtitle">Kunjungan Bulanan</h6>
                    <h2 class="text-success"><?= $data_bulan_terakhir; ?></h2>
                    <div class="mt-3">
                        <i class="bi bi-calendar-month-fill text-success fs-4"></i>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="stat-card">
                    <h6 class="card-subtitle">Tamu Mahasiswa</h6>
                    <h2 class="text-info"><?= $mahasiswa_hari_ini; ?></h2>
                    <div class="mt-3">
                        <i class="bi bi-person-vcard-fill text-info fs-4"></i>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="stat-card">
                    <h6 class="card-subtitle">Tamu Instansi</h6>
                    <h2 class="text-warning"><?= $instansi_hari_ini; ?></h2>
                    <div class="mt-3">
                        <i class="bi bi-building-fill text-warning fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- KONTEN UTAMA -->
    <div class="container-fluid mt-4 mb-5">
        <div class="row g-4 px-3 px-md-4">
            <!-- DAFTAR TAMU -->
            <div class="col-12 col-lg-8">
                <div class="main-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title fw-bold">Daftar Buku Tamu Terbaru</h5>
                            <a href="data_tamu.php" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-eye me-1"></i> Lihat Semua
                            </a>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-hover table-sm">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Nama Tamu</th>
                                        <th scope="col">Jenis Tamu</th>
                                        <th scope="col">Instansi</th>
                                        <th scope="col">Keperluan</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(mysqli_num_rows($tamu) > 0): ?>
                                        <?php 
                                        $no = $offset + 1; 
                                        while($data = mysqli_fetch_array($tamu)): 
                                        ?>
                                            <tr>
                                                <td><strong><?= $no++; ?></strong></td>
                                                <td><?= htmlspecialchars($data['nama_lengkap']); ?><br><small>(<?= htmlspecialchars($data['no_id']); ?>)</small></td>
                                                <td>
                                                    <span class="badge bg-secondary">
                                                        <?= htmlspecialchars($data['jenis_pengguna']); ?>
                                                    </span>
                                                </td>
                                                <td><?= htmlspecialchars($data['institusi']); ?></td>
                                                <td><?= htmlspecialchars($data['keperluan']); ?></td>
                                                <td>
                                                    <?php if ($data['status_aktif'] === 'aktif'): ?>
                                                        <span class="badge bg-success">Check In</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">Check Out</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($data['status_aktif'] === 'aktif'): ?>
                                                        <form method="POST" style="display:inline;">
                                                            <input type="hidden" name="action" value="checkout">
                                                            <input type="hidden" name="id" value="<?= $data['id']; ?>">
                                                            <button type="submit" 
                                                                    class="badge-checkout"
                                                                    onclick="return confirm('Apakah Anda yakin ingin melakukan Check Out untuk tamu ini?')">
                                                                Check Out
                                                            </button>
                                                        </form>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="no-data">
                                                <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                                                Tidak ada data ditemukan
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- PAGINATION BUTTONS -->
                        <?php if(mysqli_num_rows($tamu) > 0 && $total_pages > 1): ?>
                        <div class="pagination-container">
                            <?php 
                            // Build query string for pagination while preserving filters
                            $query_params = [];
                            if (!empty($keyword)) $query_params['keyword'] = $keyword;
                            if (!empty($jenis_filter) && $jenis_filter != 'semua') $query_params['jenis_tamu'] = $jenis_filter;
                            if (!empty($tanggal_filter)) $query_params['tanggal'] = $tanggal_filter;
                            if (!empty($status_filter) && $status_filter != 'semua') $query_params['status'] = $status_filter;
                            
                            $prev_page = $page - 1;
                            $next_page = $page + 1;
                            ?>
                            
                            <?php if ($page > 1): ?>
                                <?php $query_params['page'] = $prev_page; ?>
                                <a href="dashboard_admin.php?<?= http_build_query($query_params) ?>" 
                                   class="pagination-btn me-2">
                                    <i class="bi bi-chevron-left"></i> Previous
                                </a>
                            <?php endif; ?>
                            
                            <?php if ($page < $total_pages): ?>
                                <?php $query_params['page'] = $next_page; ?>
                                <a href="dashboard_admin.php?<?= http_build_query($query_params) ?>" 
                                   class="pagination-btn">
                                    Next <i class="bi bi-chevron-right"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- FILTER DAN PENCARIAN -->
            <div class="col-12 col-lg-4">
                <div class="main-card">
                    <div class="card-body">
                        <h5 class="card-title fw-bold mb-4">Filter & Pencarian</h5>
                        
                        <form method="GET" action="">
                            <!-- Hidden input untuk page agar reset ke page 1 saat filter diterapkan -->
                            <input type="hidden" name="page" value="1">
                            
                            <div class="mb-3">
                                <label class="form-label fw-medium">Kata Kunci</label>
                                <input type="text" class="form-control" name="keyword" 
                                       placeholder="Nama, instansi, keperluan..." 
                                       value="<?= htmlspecialchars($keyword) ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-medium">Tanggal Kunjungan</label>
                                <input type="date" class="form-control" name="tanggal" 
                                       value="<?= htmlspecialchars($tanggal_filter) ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-medium">Jenis Tamu</label>
                                <select class="form-select" name="jenis_tamu">
                                    <option value="semua">Semua Jenis</option>
                                    <option value="mahasiswa" <?= $jenis_filter == 'mahasiswa' ? 'selected' : '' ?>>Mahasiswa</option>
                                    <option value="instansi" <?= $jenis_filter == 'instansi' ? 'selected' : '' ?>>Instansi</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-medium">Status Aktif</label>
                                <select class="form-select" name="status">
                                    <option value="semua">Semua Status</option>
                                    <option value="aktif" <?= $status_filter == 'aktif' ? 'selected' : '' ?>>Aktif</option>
                                    <option value="tidak aktif" <?= $status_filter == 'tidak aktif' ? 'selected' : '' ?>>Tidak Aktif</option>
                                </select>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-funnel me-1"></i> Terapkan Filter
                                </button>
                                <a href="dashboard_admin.php" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-clockwise me-1"></i> Reset Filter
                                </a>
                            </div>
                        </form>

                        <hr class="my-4">
                        <div class="d-grid gap-2">
                            <a href="export_pdf.php" class="btn btn-success">
                                <i class="bi bi-printer me-1"></i> Cetak Laporan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Tambahkan efek interaktif sederhana
        document.addEventListener('DOMContentLoaded', function() {
            // Tambahkan kelas aktif pada navbar berdasarkan halaman
            const currentPage = window.location.pathname.split('/').pop();
            const navLinks = document.querySelectorAll('.nav-link');
            
            navLinks.forEach(link => {
                if (link.getAttribute('href') === currentPage) {
                    link.classList.add('active');
                } else {
                    link.classList.remove('active');
                }
            });
            
            // Auto-hide alert setelah 5 detik
            const alert = document.querySelector('.alert');
            if (alert) {
                setTimeout(() => {
                    alert.classList.remove('show');
                    setTimeout(() => alert.remove(), 300);
                }, 5000);
            }
        });
    </script>
</body>
</html>
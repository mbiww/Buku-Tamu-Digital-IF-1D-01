<?php
include 'koneksi.php';

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Buku_Tamu.xls");
header("Pragma: no-cache");
header("Expires: 0");

// Query sesuai database kamu
$query = mysqli_query($koneksi, "
    SELECT 
        nama_lengkap,
        no_id,
        institusi,
        alamat,
        no_wa,
        keperluan,
        jenis_pengguna,
        waktu_checkin,
        waktu_checkout,
        status_aktif,
        user_id,
        created_at
    FROM data_tamu
    ORDER BY created_at DESC
");
?>

<h3>LAPORAN BUKU TAMU</h3>
<p>Tanggal Cetak: <?= date('d-m-Y') ?></p>

<table border="1" cellpadding="5">
    <thead>
        <tr style="background-color:#D9EDF7; font-weight:bold;">
            <th>No</th>
            <th>Nama Lengkap</th>
            <th>No Identitas</th>
            <th>Institusi</th>
            <th>Alamat</th>
            <th>No WhatsApp</th>
            <th>Keperluan</th>
            <th>Jenis Pengguna</th>
            <th>Check In</th>
            <th>Check Out</th>
            <th>Status</th>
            <th>User ID</th>
            <th>Dibuat Pada</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        while ($row = mysqli_fetch_assoc($query)) {
        ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= $row['nama_lengkap'] ?></td>
            <td><?= $row['no_id'] ?></td>
            <td><?= $row['institusi'] ?></td>
            <td><?= $row['alamat'] ?></td>
            <td><?= $row['no_wa'] ?></td>
            <td><?= $row['keperluan'] ?></td>
            <td><?= $row['jenis_pengguna'] ?></td>
            <td><?= $row['waktu_checkin'] ?? '-' ?></td>
            <td><?= $row['waktu_checkout'] ?? '-' ?></td>
            <td><?= $row['status_aktif'] ?></td>
            <td><?= $row['user_id'] ?></td>
            <td><?= $row['created_at'] ?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>
            
<?php
$host = 'localhost';
$dbname = 'buku_tamu';
$username = 'root';
$password = '';


try {
    $koneksi = mysqli_connect($host, $username, $password, $dbname);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}

mysqli_query($koneksi, "SET time_zone = '+07:00'");
?>
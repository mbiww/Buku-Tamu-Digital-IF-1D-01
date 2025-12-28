<?php
// Path ke koneksi.php (satu tingkat di atas folder register)
require_once dirname(__DIR__) . '/koneksi.php';

// Include file lain yang ada di folder yang sama
require_once __DIR__ . '/register_backend.php';
require_once __DIR__ . '/register_frontend.php';
?>
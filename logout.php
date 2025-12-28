<?php
session_start();
session_destroy();

setcookie('login', '', time() - 3600, '/');
setcookie('role', '', time() - 3600, '/');

header('Location: login/login.php');
exit;

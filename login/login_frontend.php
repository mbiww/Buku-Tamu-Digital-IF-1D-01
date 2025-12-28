<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Tamu</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style_login.css">
</head>

<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-12 col-md-8 col-lg-6 col-xl-4">
                <div class="card login-card p-4 p-md-5">
                    <div class="card-body">
                        <h3 class="login-title text-center">Login Tamu</h3>
                        
                        <?php if (isset($error) && $error): ?>
                            <div class="alert alert-danger mb-4"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <form action="login.php" method="POST">
                            <div class="mb-4">
                                <label class="form-label">No Identitas (NIM, NIDN, No. Badge)</label>
                                <input type="number" name="no_id" class="form-control" required 
                                       placeholder="Masukkan nomor identitas Anda">
                            </div>
                            
    <div class="mb-4">
        <label class="form-label">Password</label>
        <div class="input-group">
            <input type="password" name="password" class="form-control" required 
                   placeholder="Masukkan password Anda" id="passwordInput">
            <button type="button" class="btn btn-outline-secondary toggle-password" 
                    onclick="togglePassword('passwordInput')" aria-label="Tampilkan password">
                <i class="bi bi-eye"></i>
            </button>
        </div>
    </div>
                            
                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary btn-lg">Login</button>
                            </div>
                        </form>
                        
                        <div class="text-center mt-4 pt-3 border-top">
                            <small class="register-link">
                                Belum punya akun? 
                                <a href="../register/register.php">
                                    Daftar di sini
                                </a>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
function togglePassword(inputId) {
    const passwordInput = document.getElementById(inputId);
    const toggleButton = passwordInput.nextElementSibling;
    const icon = toggleButton.querySelector('i');
    
    // Toggle tipe input
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
        toggleButton.setAttribute('aria-label', 'Sembunyikan password');
    } else {
        passwordInput.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
        toggleButton.setAttribute('aria-label', 'Tampilkan password');
    }
}

    </script>
</body>
</html>

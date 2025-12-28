<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Tamu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style_register.css">
    <!-- Ikon Font Awesome untuk mata -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100 py-4">
            <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                <div class="card register-card p-0">
                    <div class="card-body">
                        <h3 class="register-title text-center">Register Tamu</h3>
                        
                        <!-- Pesan Error/Success dari Backend -->
                        <?php if (isset($error) && $error): ?>
                            <div class="alert alert-danger mb-4"><?php echo $error; ?></div>
                        <?php endif; ?>
              
                        <?php if (isset($success) && $success): ?>
                            <div class="alert alert-success mb-4"><?php echo $success; ?></div>
                        <?php endif; ?>

                        <form id="registerForm" action="register.php" method="POST">
                            <div class="mb-3">
                                <label class="form-label">No Identitas (NIM, NIDN, NIK)</label>
                                <input type="number" name="no_id" class="form-control" required 
                                       placeholder="Masukkan nomor identitas">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="nama_lengkap" class="form-control" required 
                                       placeholder="Masukkan nama lengkap">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required 
                                       placeholder="Masukkan alamat email">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Daftar Sebagai</label>
                                <select name="role" class="form-control" required>
                                    <option value="">-- Pilih Jenis Pengguna --</option>
                                    <option value="mahasiswa">Mahasiswa</option>
                                    <option value="instansi">Instansi</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Kampus / Instansi</label>
                                <input type="text" name="institusi" class="form-control" required 
                                       placeholder="Masukkan nama institusi">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <div class="input-group">
                                    <input type="password" name="password" id="password" class="form-control" required 
                                           placeholder="Buat password Anda">
                                    <button type="button" class="btn btn-outline-secondary toggle-password" 
                                            data-target="password">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="form-text">Minimal 6 karakter</div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Konfirmasi Password</label>
                                <div class="input-group">
                                    <input type="password" name="confirmpassword" id="confirmpassword" class="form-control" required 
                                           placeholder="Konfirmasi password Anda">
                                    <button type="button" class="btn btn-outline-secondary toggle-password" 
                                            data-target="confirmpassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div id="passwordMatchMessage" class="form-text"></div>
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="agree_terms" required>
                                <label class="form-check-label" for="agree_terms">
                                    Saya menyatakan bahwa data yang diisi adalah benar.
                                </label>
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary btn-lg">Daftar</button>
                            </div>
                        </form>
                        
                        <div class="text-center mt-4 pt-3 border-top">
                            <small class="login-link">
                                Sudah punya akun? 
                                <a href="../login/login.php">
                                    Masuk di sini
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
    
    <!-- JavaScript untuk validasi konfirmasi password dan show/hide password -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('confirmpassword');
            const passwordMatchMessage = document.getElementById('passwordMatchMessage');
            const registerForm = document.getElementById('registerForm');
            
            // Fungsi untuk validasi kecocokan password
            function validatePasswordMatch() {
                const password = passwordInput.value;
                const confirmPassword = confirmPasswordInput.value;
                
                if (confirmPassword === '') {
                    passwordMatchMessage.textContent = '';
                    passwordMatchMessage.className = 'form-text';
                    return;
                }
                
                if (password === confirmPassword) {
                    passwordMatchMessage.textContent = '✓ Password cocok';
                    passwordMatchMessage.className = 'form-text text-success';
                    return true;
                } else {
                    passwordMatchMessage.textContent = '✗ Password tidak cocok';
                    passwordMatchMessage.className = 'form-text text-danger';
                    return false;
                }
            }
            
            // Fungsi untuk toggle show/hide password
            function togglePasswordVisibility(targetId) {
                const input = document.getElementById(targetId);
                const button = document.querySelector(`.toggle-password[data-target="${targetId}"]`);
                const icon = button.querySelector('i');
                
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                    button.setAttribute('aria-label', 'Sembunyikan password');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                    button.setAttribute('aria-label', 'Tampilkan password');
                }
                
                // Fokus kembali ke input setelah toggle
                input.focus();
            }
            
            // Tambahkan event listener untuk tombol show/hide password
            document.querySelectorAll('.toggle-password').forEach(button => {
                button.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-target');
                    togglePasswordVisibility(targetId);
                });
                
                // Tambahkan aksesibilitas dengan keyboard
                button.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        const targetId = this.getAttribute('data-target');
                        togglePasswordVisibility(targetId);
                    }
                });
            });
            
            // Validasi real-time saat mengetik
            passwordInput.addEventListener('input', validatePasswordMatch);
            confirmPasswordInput.addEventListener('input', validatePasswordMatch);
            
            // Validasi sebelum submit form
            registerForm.addEventListener('submit', function(event) {
                if (!validatePasswordMatch()) {
                    event.preventDefault();
                    alert('Password dan konfirmasi password tidak cocok!');
                    confirmPasswordInput.focus();
                    return;
                }
                
                // Validasi panjang password
                if (passwordInput.value.length < 6) {
                    event.preventDefault();
                    alert('Password minimal 6 karakter!');
                    passwordInput.focus();
                    return;
                }
                
                // Validasi checkbox terms
                const agreeTerms = document.getElementById('agree_terms');
                if (!agreeTerms.checked) {
                    event.preventDefault();
                    alert('Anda harus menyetujui pernyataan bahwa data yang diisi adalah benar.');
                    agreeTerms.focus();
                    return;
                }
            });
        });
    </script>
</body>
</html>
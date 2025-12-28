<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Tamu Instansi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style_instansi.css">
</head>
<body>
    <div class="header">
        <div class="container">
            <div class="header-content">
                <h4 class="mb-0">Sistem Pendaftaran Tamu</h4>
                <div class="d-flex align-items-center gap-3">
                    <div class="user-info dropdown">
                        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <div>
                                <span id="userName"><?php echo htmlspecialchars($nama_user ?? 'User'); ?></span> 
                                <small class="text-light">(<?php echo htmlspecialchars($role_user ?? 'instansi'); ?>)</small>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#changePasswordModal">Change Password</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="../../logout.php">Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Change Password -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="passwordMessage" class="alert d-none"></div>
                    <form id="passwordForm">
                        <div class="mb-3">
                            <label for="currentPassword" class="form-label">Password Saat Ini *</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="currentPassword" name="current_password" required>
                                <button type="button" class="btn btn-outline-secondary toggle-password" data-target="currentPassword">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback">Password saat ini harus diisi</div>
                        </div>
                        <div class="mb-3">
                            <label for="newPassword" class="form-label">Password Baru *</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="newPassword" name="new_password" required>
                                <button type="button" class="btn btn-outline-secondary toggle-password" data-target="newPassword">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback">Password baru minimal 6 karakter</div>
                        </div>
                        <div class="mb-3">
                            <label for="confirmPassword" class="form-label">Konfirmasi Password Baru *</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="confirmPassword" name="confirm_password" required>
                                <button type="button" class="btn btn-outline-secondary toggle-password" data-target="confirmPassword">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback">Konfirmasi password harus sama dengan password baru</div>
                        </div>
                        <div class="mb-3 form-text">
                            <small>* Password minimal 6 karakter</small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="submitPassword">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <div class="container form-container pt-4">
        <div class="row">
            <div class="col-md-6">
                <div class="info-card mt-4">
                    <div class="p-4 p-md-5">
                        <div class="status-section mb-4">
                            <div class="status-info-row">
                                <span class="status-label">Status Bertamu :</span>
                                <span class="ticket-badge <?php echo $tamu_aktif ? 'status-active' : 'status-inactive'; ?>">
                                    <?php echo $tamu_aktif ? 'AKTIF' : 'TIDAK AKTIF'; ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-6 mb-4 mb-md-0">
                                <div class="mb-3">
                                    <div class="info-label d-flex align-items-center gap-2 mb-2">
                                        <i class="bi bi-clock"></i>
                                        Waktu Check In
                                    </div>
                                    <div>
                                        <?php if ($tamu_aktif): ?>
                                            <span class="info-value"><?php echo date('H:i', strtotime($tamu_aktif['waktu_checkin'])); ?></span>
                                            <span class="info-value-small ms-1">WIB</span>
                                            <p class="text-gray-600 small fw-medium mb-0"><?php echo date('l, d F Y', strtotime($tamu_aktif['waktu_checkin'])); ?></p>
                                        <?php else: ?>
                                            <span class="info-value">--:--</span>
                                            <p class="text-gray-600 small fw-medium mb-0">Belum check in</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <div class="mb-3">
                                    <div class="info-label d-flex align-items-center gap-2 mb-2">
                                        <i class="bi bi-geo-alt"></i>
                                        Instansi
                                    </div>
                                    <p class="h4 fw-bold text-gray-800 mb-1"><?php echo htmlspecialchars($institusi_user ?? 'Politeknik Negeri Batam'); ?></p>
                                    <p class="text-gray-600 small mb-0"><?php echo htmlspecialchars($nama_user ?? 'User'); ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <div class="info-label d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-card-text"></i>
                                Keperluan Kunjungan
                            </div>
                            <div class="purpose-box purpose-box-gradient">
                                <p class="text-gray-800 mb-0">
                                    <?php if ($tamu_aktif): ?>
                                        <?php echo htmlspecialchars($tamu_aktif['keperluan'] ?? 'Keperluan kunjungan'); ?>
                                    <?php else: ?>
                                        Anda belum mengisi pendaftaran dan belum check in
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <form action="form.php" method="POST">
                                <button type="submit" name="checkout" class="btn btn-lg py-3 checkout-btn" id="checkoutBtn" <?php echo !$tamu_aktif ? 'disabled' : ''; ?>>
                                    <i class="bi bi-box-arrow-right me-2"></i>
                                    Check Out Tamu
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="guidelines">
                    <h6>Panduan Pengisian:</h6>
                    <ul>
                        <li>Isi semua data dengan lengkap dan benar</li>
                        <li>Nomor identitas harus berupa angka</li>
                        <li>Nomor WhatsApp harus aktif</li>
                        <li>Datang sesuai jadwal yang telah dibuat</li>
                        <li>Selesaikan kunjungan dengan melakukan check out</li>
                    </ul>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-card mt-4">
                    <h5 class="text-center mb-4 fw-bold">Pendaftaran Tamu Instansi</h5>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <?php if (!$tamu_aktif): ?>
                        <form action="form.php" method="POST" id="tamuForm">
                            <div class="mb-3">
                                <label>Nama Lengkap :</label>
                                <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" 
                                       placeholder="Masukkan nama lengkap" 
                                       value="<?php echo $_POST['nama_lengkap'] ?? ''; ?>">
                            </div>
                            <div class="mb-3">
                                <label>No Identitas (NIM, NIDN, No. Badge) :</label>
                                <input type="number" class="form-control" id="no_id" name="no_id" 
                                       placeholder="Contoh: 1234567890" 
                                       value="<?php echo $_POST['no_id'] ?? ''; ?>">
                            </div>
                            <div class="mb-3">
                                <label>Institusi :</label>
                                <input type="text" class="form-control" id="institusi" name="institusi" 
                                       placeholder="Nama institusi atau perusahaan" 
                                       value="<?php echo $_POST['institusi'] ?? ''; ?>">
                            </div>
                            <div class="mb-3">
                                <label>Alamat :</label>
                                <input type="text" class="form-control" id="alamat" name="alamat" 
                                       placeholder="Alamat lengkap" 
                                       value="<?php echo $_POST['alamat'] ?? ''; ?>">
                            </div>
                            <div class="mb-3">
                                <label>Nomor HP / WhatsApp :</label>
                                <input type="number" class="form-control" id="number" name="number" 
                                       placeholder="81234567890" 
                                       value="<?php echo $_POST['number'] ?? ''; ?>">
                            </div>
                            <div class="mb-3">
                                <label>Keperluan Kunjungan/Bertamu :</label>
                                <textarea name="message" rows="5" cols="34" 
                                          placeholder="Tuliskan tujuan dan keperluan kunjungan Anda secara detail"><?php echo $_POST['message'] ?? ''; ?></textarea>
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="agree_terms" required>
                                <label class="form-check-label" for="agree_terms">
                                    Saya menyatakan bahwa data yang diisi adalah benar.
                                </label>
                            </div>
                            <button type="submit" class="btn checkin-btn w-100"> 
                                <i class="bi bi-box-arrow-right me-2"></i> 
                                Check In Tamu
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <h6><i class="bi bi-info-circle me-2"></i>Tamu Aktif</h6>
                            <p>Anda memiliki tamu aktif. Silakan check out tamu terlebih dahulu sebelum mendaftarkan tamu baru.</p>
                            <hr>
                            <p><strong>Nama Tamu:</strong> <?php echo htmlspecialchars($tamu_aktif['nama_lengkap']); ?></p>
                            <p><strong>Waktu Check In:</strong> <?php echo date('H:i', strtotime($tamu_aktif['waktu_checkin'])); ?> WIB</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <div class="container">
            <p class="mb-0">Sistem Keamanan Data Tamu | © 2025 Politeknik Negeri Batam. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordForm = document.getElementById('passwordForm');
            const submitBtn = document.getElementById('submitPassword');
            const messageDiv = document.getElementById('passwordMessage');
            
            document.getElementById('changePasswordModal').addEventListener('hidden.bs.modal', function () {
                passwordForm.reset();
                messageDiv.classList.add('d-none');
                messageDiv.textContent = '';
                
                const inputs = passwordForm.querySelectorAll('input');
                inputs.forEach(input => {
                    input.classList.remove('is-invalid');
                    input.classList.remove('is-valid');
                });
            });
            
            document.querySelectorAll('.toggle-password').forEach(button => {
                button.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-target');
                    const passwordInput = document.getElementById(targetId);
                    const icon = this.querySelector('i');
                    
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    
                    if (type === 'text') {
                        icon.classList.remove('bi-eye');
                        icon.classList.add('bi-eye-slash');
                        this.setAttribute('aria-label', 'Sembunyikan password');
                    } else {
                        icon.classList.remove('bi-eye-slash');
                        icon.classList.add('bi-eye');
                        this.setAttribute('aria-label', 'Tampilkan password');
                    }
                });
            });
            
            document.querySelectorAll('.toggle-password').forEach(button => {
                button.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        this.click();
                    }
                });
            });

            function validateForm() {
                let isValid = true;
                const currentPassword = document.getElementById('currentPassword');
                const newPassword = document.getElementById('newPassword');
                const confirmPassword = document.getElementById('confirmPassword');
                
                [currentPassword, newPassword, confirmPassword].forEach(input => {
                    input.classList.remove('is-invalid');
                    input.classList.remove('is-valid');
                });
                
                if (!currentPassword.value.trim()) {
                    currentPassword.classList.add('is-invalid');
                    isValid = false;
                }
                
                if (!newPassword.value.trim() || newPassword.value.length < 6) {
                    newPassword.classList.add('is-invalid');
                    isValid = false;
                }
                
                if (!confirmPassword.value.trim() || newPassword.value !== confirmPassword.value) {
                    confirmPassword.classList.add('is-invalid');
                    isValid = false;
                }
                
                return isValid;
            }
            
            submitBtn.addEventListener('click', function(e) {
                e.preventDefault();
                
                if (!validateForm()) {
                    return;
                }
                
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...';
                
                const formData = new FormData();
                formData.append('current_password', document.getElementById('currentPassword').value);
                formData.append('new_password', document.getElementById('newPassword').value);
                formData.append('confirm_password', document.getElementById('confirmPassword').value);
                
                fetch('change_password.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    messageDiv.classList.remove('d-none');
                    messageDiv.classList.remove('alert-success', 'alert-danger');
                    
                    if (data.success) {
                        messageDiv.classList.add('alert-success');
                        messageDiv.innerHTML = '<i class="bi bi-check-circle me-2"></i>' + data.message;
                        
                        passwordForm.reset();
                        
                        setTimeout(() => {
                            const modal = bootstrap.Modal.getInstance(document.getElementById('changePasswordModal'));
                            modal.hide();
                        }, 3000);
                    } else {
                        messageDiv.classList.add('alert-danger');
                        messageDiv.innerHTML = '<i class="bi bi-exclamation-triangle me-2"></i>' + data.message;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    messageDiv.classList.remove('d-none');
                    messageDiv.classList.add('alert-danger');
                    messageDiv.innerHTML = '<i class="bi bi-exclamation-triangle me-2"></i>Terjadi kesalahan. Silakan coba lagi.';
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Simpan';
                });
            });
            
            const inputs = passwordForm.querySelectorAll('input');
            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    if (this.classList.contains('is-invalid')) {
                        this.classList.remove('is-invalid');
                    }
                    
                    if (this.id === 'newPassword' && this.value.length >= 6) {
                        this.classList.add('is-valid');
                    }
                    
                    if (this.id === 'confirmPassword' && this.value === document.getElementById('newPassword').value) {
                        this.classList.add('is-valid');
                    }
                });
            });
            
            const tamuForm = document.getElementById('tamuForm');
            if (tamuForm) {
                tamuForm.addEventListener('submit', function(e) {
                    const noWa = document.getElementById('number');
                    if (noWa.value.length < 10 || noWa.value.length > 15) {
                        e.preventDefault();
                        alert('Nomor WA harus antara 10-15 digit!');
                        noWa.focus();
                    }
                });
            }
        });
    </script>
</body>
</html>
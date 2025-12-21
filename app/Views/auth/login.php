<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Tapak Bersih</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .auth-card {
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }
        .is-invalid {
            border-color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="card auth-card">
                    <div class="card-body p-5">
                        <!-- Logo -->
                        <div class="text-center mb-4">
                            <h2 class="fw-bold text-primary">👟 Tapak Bersih</h2>
                            <p class="text-muted">Masuk ke akun Anda</p>
                        </div>

                        <!-- Login Form -->
                        <form id="loginForm">
                            <?= csrf_field() ?>
                            
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input type="email" class="form-control" name="email" id="email" placeholder="email@example.com" required>
                                </div>
                                <div class="invalid-feedback" id="error-email"></div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                    <input type="password" class="form-control" name="password" id="password" placeholder="••••••••" required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback" id="error-password"></div>
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" name="remember" id="remember">
                                <label class="form-check-label" for="remember">Ingat saya</label>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 mb-3" id="btnLogin">
                                <span id="btnText">Masuk</span>
                                <span id="btnLoading" class="d-none">
                                    <span class="spinner-border spinner-border-sm me-2"></span>Loading...
                                </span>
                            </button>
                        </form>

                        <!-- Divider -->
                        <div class="text-center my-3">
                            <span class="text-muted">atau</span>
                        </div>

                        <!-- Guest Checkout Button -->
                        <button type="button" class="btn btn-outline-secondary w-100 mb-3" data-bs-toggle="modal" data-bs-target="#guestModal">
                            <i class="bi bi-person"></i> Lanjutkan sebagai Tamu
                        </button>

                        <!-- Register Link -->
                        <div class="text-center">
                            <span class="text-muted">Belum punya akun? </span>
                            <a href="<?= base_url('auth/register') ?>" class="text-primary fw-bold">Daftar</a>
                        </div>

                        <!-- Back to Home -->
                        <div class="text-center mt-3">
                            <a href="<?= base_url('/') ?>" class="text-muted"><i class="bi bi-arrow-left"></i> Kembali ke Beranda</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Guest Checkout Modal -->
    <div class="modal fade" id="guestModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Checkout sebagai Tamu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">Isi data Anda untuk melanjutkan tanpa registrasi</p>
                    
                    <form id="guestForm">
                        <?= csrf_field() ?>
                        
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap *</label>
                            <input type="text" class="form-control" name="guest_name" id="guest_name" required>
                            <div class="invalid-feedback" id="error-guest_name"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">No. WhatsApp *</label>
                            <input type="tel" class="form-control" name="guest_phone" id="guest_phone" placeholder="08123456789" required>
                            <div class="invalid-feedback" id="error-guest_phone"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email (Opsional)</label>
                            <input type="email" class="form-control" name="guest_email" id="guest_email">
                            <div class="invalid-feedback" id="error-guest_email"></div>
                        </div>

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> Data Anda hanya digunakan untuk pesanan ini saja.
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="btnGuestCheckout">Lanjutkan</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const BASE_URL = '<?= base_url() ?>';

        // Clear all validation errors
        function clearErrors() {
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            document.querySelectorAll('.invalid-feedback').forEach(el => {
                el.textContent = '';
                el.style.display = 'none';
            });
        }

        // Show validation errors
        function showErrors(errors) {
            clearErrors();
            for (const [field, messages] of Object.entries(errors)) {
                const input = document.getElementById(field);
                const errorDiv = document.getElementById(`error-${field}`);
                
                if (input && errorDiv) {
                    input.classList.add('is-invalid');
                    errorDiv.textContent = Array.isArray(messages) ? messages[0] : messages;
                    errorDiv.style.display = 'block';
                }
            }
        }

        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            }
        });

        // Login Form Submit
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            clearErrors();

            const btnLogin = document.getElementById('btnLogin');
            const btnText = document.getElementById('btnText');
            const btnLoading = document.getElementById('btnLoading');

            // Disable button and show loading
            btnLogin.disabled = true;
            btnText.classList.add('d-none');
            btnLoading.classList.remove('d-none');

            const formData = new FormData(this);
            const data = Object.fromEntries(formData);

            try {
                const response = await fetch(`${BASE_URL}/auth/process-login`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams(data)
                });

                const result = await response.json();
                
                console.log('Login response:', result); // Debug log

                // Re-enable button
                btnLogin.disabled = false;
                btnText.classList.remove('d-none');
                btnLoading.classList.add('d-none');

                if (result.success) {
                    // Success SweetAlert
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: result.message || 'Login berhasil!',
                        confirmButtonColor: '#667eea',
                        confirmButtonText: 'OK',
                        timer: 1500,
                        timerProgressBar: true
                    }).then(() => {
                        window.location.href = result.redirect;
                    });
                } else {
                    // Check if there are validation errors
                    if (result.errors && Object.keys(result.errors).length > 0) {
                        showErrors(result.errors);
                    }
                    
                    // Always show SweetAlert with the error message
                    Swal.fire({
                        icon: 'error',
                        title: 'Login Gagal',
                        text: result.message || 'Email/Password salah',
                        confirmButtonColor: '#dc3545',
                        confirmButtonText: 'OK'
                    });
                }
            } catch (error) {
                console.error('Login error:', error);
                
                // Re-enable button
                btnLogin.disabled = false;
                btnText.classList.remove('d-none');
                btnLoading.classList.add('d-none');

                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Koneksi',
                    text: 'Terjadi kesalahan saat menghubungi server. Silakan coba lagi.',
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: 'OK'
                });
            }
        });

        // Guest Checkout
        document.getElementById('btnGuestCheckout').addEventListener('click', async function() {
            clearErrors();

            const guestForm = document.getElementById('guestForm');
            const formData = new FormData(guestForm);
            const data = Object.fromEntries(formData);

            // Basic validation
            if (!data.guest_name || !data.guest_phone) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Data Tidak Lengkap',
                    text: 'Nama dan nomor telepon wajib diisi',
                    confirmButtonColor: '#ffc107',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Disable button
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Loading...';

            try {
                const response = await fetch(`${BASE_URL}/auth/guest-checkout`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams(data)
                });

                const result = await response.json();

                if (result.success) {
                    window.location.href = result.redirect;
                } else {
                    if (result.errors) {
                        showErrors(result.errors);
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: result.message || 'Terjadi kesalahan',
                        confirmButtonColor: '#dc3545',
                        confirmButtonText: 'OK'
                    });

                    this.disabled = false;
                    this.innerHTML = 'Lanjutkan';
                }
            } catch (error) {
                console.error('Guest checkout error:', error);
                
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Koneksi',
                    text: 'Terjadi kesalahan. Silakan coba lagi.',
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: 'OK'
                });
                
                this.disabled = false;
                this.innerHTML = 'Lanjutkan';
            }
        });
    </script>
</body>
</html>
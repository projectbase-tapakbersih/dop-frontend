<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Tapak Bersih</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
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

                        <!-- Alert -->
                        <div id="alert-container"></div>

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
                                <input type="checkbox" class="form-check-input" id="remember">
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
    <script src="<?= base_url('assets/js/auth.js') ?>"></script>
</body>
</html>
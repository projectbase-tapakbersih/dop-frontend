<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Tapak Bersih</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 30px 0;
        }
        .auth-card {
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card auth-card">
                    <div class="card-body p-5">
                        <!-- Logo -->
                        <div class="text-center mb-4">
                            <h2 class="fw-bold text-primary">👟 Tapak Bersih</h2>
                            <p class="text-muted">Buat akun baru</p>
                        </div>

                        <!-- Alert -->
                        <div id="alert-container"></div>

                        <!-- Register Form -->
                        <form id="registerForm">
                            <?= csrf_field() ?>
                            
                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap *</label>
                                <input type="text" class="form-control" name="name" id="name" required>
                                <div class="invalid-feedback" id="error-name"></div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email *</label>
                                <input type="email" class="form-control" name="email" id="email" required>
                                <div class="invalid-feedback" id="error-email"></div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">No. WhatsApp *</label>
                                <input type="tel" class="form-control" name="phone" id="phone" placeholder="08123456789" required>
                                <small class="text-muted">Untuk notifikasi status pesanan</small>
                                <div class="invalid-feedback" id="error-phone"></div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Password *</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" name="password" id="password" required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                <small class="text-muted">Minimal 6 karakter</small>
                                <div class="invalid-feedback" id="error-password"></div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Konfirmasi Password *</label>
                                <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" required>
                                <div class="invalid-feedback" id="error-password_confirmation"></div>
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="terms" required>
                                <label class="form-check-label" for="terms">
                                    Saya setuju dengan <a href="#" class="text-primary">Syarat & Ketentuan</a>
                                </label>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 mb-3" id="btnRegister">
                                <span id="btnText">Daftar</span>
                                <span id="btnLoading" class="d-none">
                                    <span class="spinner-border spinner-border-sm me-2"></span>Loading...
                                </span>
                            </button>
                        </form>

                        <!-- Login Link -->
                        <div class="text-center">
                            <span class="text-muted">Sudah punya akun? </span>
                            <a href="<?= base_url('auth/login') ?>" class="text-primary fw-bold">Masuk</a>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url('assets/js/auth.js') ?>"></script>
</body>
</html>
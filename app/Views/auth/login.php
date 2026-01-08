<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-lg border-0" style="border-radius: 16px;">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="bi bi-person-circle text-primary" style="font-size: 4rem;"></i>
                        <h3 class="fw-bold mt-3">Selamat Datang!</h3>
                        <p class="text-muted">Masuk ke akun Tapak Bersih Anda</p>
                    </div>

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="bi bi-exclamation-circle"></i> <?= session()->getFlashdata('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="bi bi-check-circle"></i> <?= session()->getFlashdata('success') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Form action ke /auth/login (POST) -->
                    <form action="<?= base_url('auth/login') ?>" method="POST">
                        <?= csrf_field() ?>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control" name="email" 
                                       value="<?= old('email') ?>" 
                                       placeholder="nama@email.com" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" class="form-control" name="password" 
                                       placeholder="Masukkan password" required>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember_me" id="rememberMe">
                                <label class="form-check-label" for="rememberMe">
                                    Ingat saya
                                </label>
                            </div>
                            <a href="<?= base_url('auth/forgot-password') ?>" class="text-decoration-none small">
                                Lupa password?
                            </a>
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-box-arrow-in-right"></i> Masuk
                            </button>
                        </div>

                        <div class="text-center">
                            <p class="mb-0">Belum punya akun? 
                                <a href="<?= base_url('auth/register') ?>" class="text-decoration-none fw-bold">
                                    Daftar sekarang
                                </a>
                            </p>
                        </div>
                    </form>

                    <hr class="my-4">

                    <div class="text-center">
                        <p class="text-muted small mb-2">Atau masuk sebagai tamu</p>
                        <a href="<?= base_url('guest/track') ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-search"></i> Lacak Pesanan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Breadcrumb -->
<section class="py-3 bg-light">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= base_url() ?>">Beranda</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('branches') ?>">Cabang</a></li>
                <li class="breadcrumb-item active"><?= esc($branch['name'] ?? 'Detail') ?></li>
            </ol>
        </nav>
    </div>
</section>

<?php if (!empty($error)): ?>
    <div class="container mt-4">
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle"></i> <?= esc($error) ?>
        </div>
        <a href="<?= base_url('branches') ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
<?php elseif (empty($branch)): ?>
    <div class="container mt-4">
        <div class="alert alert-warning">Cabang tidak ditemukan</div>
        <a href="<?= base_url('branches') ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
<?php else: ?>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-lg border-0" style="border-radius: 20px;">
                    <div class="card-body p-5">
                        <!-- Header -->
                        <div class="text-center mb-4">
                            <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                                 style="width: 80px; height: 80px;">
                                <i class="bi bi-shop text-success" style="font-size: 2.5rem;"></i>
                            </div>
                            <h1 class="fw-bold mb-2"><?= esc($branch['name'] ?? 'Cabang') ?></h1>
                            <?php if (isset($branch['is_active'])): ?>
                                <span class="badge bg-<?= $branch['is_active'] ? 'success' : 'secondary' ?> fs-6">
                                    <?= $branch['is_active'] ? 'Buka' : 'Tutup' ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Info Grid -->
                        <div class="row g-4 mb-4">
                            <?php if (!empty($branch['address'])): ?>
                            <div class="col-12">
                                <div class="bg-light rounded-3 p-4">
                                    <div class="d-flex align-items-start">
                                        <i class="bi bi-geo-alt text-danger fs-4 me-3"></i>
                                        <div>
                                            <h6 class="fw-bold mb-1">Alamat</h6>
                                            <p class="mb-0"><?= esc($branch['address']) ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($branch['phone'])): ?>
                            <div class="col-md-6">
                                <div class="bg-light rounded-3 p-4">
                                    <div class="d-flex align-items-start">
                                        <i class="bi bi-telephone text-primary fs-4 me-3"></i>
                                        <div>
                                            <h6 class="fw-bold mb-1">Telepon</h6>
                                            <a href="tel:<?= esc($branch['phone']) ?>" class="text-decoration-none">
                                                <?= esc($branch['phone']) ?>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($branch['email'])): ?>
                            <div class="col-md-6">
                                <div class="bg-light rounded-3 p-4">
                                    <div class="d-flex align-items-start">
                                        <i class="bi bi-envelope text-info fs-4 me-3"></i>
                                        <div>
                                            <h6 class="fw-bold mb-1">Email</h6>
                                            <a href="mailto:<?= esc($branch['email']) ?>" class="text-decoration-none">
                                                <?= esc($branch['email']) ?>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($branch['opening_hours'])): ?>
                            <div class="col-12">
                                <div class="bg-light rounded-3 p-4">
                                    <div class="d-flex align-items-start">
                                        <i class="bi bi-clock text-warning fs-4 me-3"></i>
                                        <div>
                                            <h6 class="fw-bold mb-1">Jam Operasional</h6>
                                            <p class="mb-0"><?= esc($branch['opening_hours']) ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- CTA -->
                        <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                            <a href="<?= base_url('branches') ?>" class="btn btn-outline-secondary btn-lg">
                                <i class="bi bi-arrow-left"></i> Lihat Cabang Lain
                            </a>
                            <a href="<?= base_url('order/create') ?>" class="btn btn-primary btn-lg">
                                <i class="bi bi-cart-plus"></i> Pesan Sekarang
                            </a>
                            <?php if (!empty($branch['phone'])): ?>
                            <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $branch['phone']) ?>" 
                               class="btn btn-success btn-lg" target="_blank">
                                <i class="bi bi-whatsapp"></i> WhatsApp
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php endif; ?>

<?= $this->endSection() ?>
<?= $this->extend('layouts/main') ?>

<?php
if (!function_exists('format_rupiah')) {
    function format_rupiah($number) {
        return 'Rp ' . number_format((float)$number, 0, ',', '.');
    }
}
?>

<?= $this->section('content') ?>

<!-- Breadcrumb -->
<section class="py-3 bg-light">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= base_url() ?>">Beranda</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('services') ?>">Layanan</a></li>
                <li class="breadcrumb-item active"><?= esc($service['name'] ?? 'Detail') ?></li>
            </ol>
        </nav>
    </div>
</section>

<?php if (!empty($error)): ?>
    <div class="container mt-4">
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle"></i> <?= esc($error) ?>
        </div>
        <a href="<?= base_url('services') ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
<?php elseif (empty($service)): ?>
    <div class="container mt-4">
        <div class="alert alert-warning">Layanan tidak ditemukan</div>
        <a href="<?= base_url('services') ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
<?php else: ?>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-lg border-0" style="border-radius: 20px;">
                    <div class="card-body p-5 text-center">
                        <!-- Icon -->
                        <div class="mb-4">
                            <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" 
                                 style="width: 100px; height: 100px;">
                                <i class="bi bi-droplet text-primary" style="font-size: 3rem;"></i>
                            </div>
                        </div>
                        
                        <!-- Title -->
                        <h1 class="fw-bold mb-3"><?= esc($service['name'] ?? 'Layanan') ?></h1>
                        
                        <!-- Price -->
                        <div class="mb-4">
                            <span class="display-5 fw-bold text-primary">
                                <?= format_rupiah($service['price'] ?? 0) ?>
                            </span>
                        </div>
                        
                        <!-- Duration -->
                        <?php if (!empty($service['estimated_duration'])): ?>
                            <p class="text-muted mb-4">
                                <i class="bi bi-clock"></i> Estimasi: <?= esc($service['estimated_duration']) ?>
                            </p>
                        <?php endif; ?>
                        
                        <!-- Description -->
                        <div class="text-start bg-light rounded-3 p-4 mb-4">
                            <h5 class="fw-bold mb-3">Deskripsi Layanan</h5>
                            <p class="mb-0"><?= esc($service['description'] ?? 'Layanan premium untuk sepatu Anda dengan hasil terbaik dan penanganan profesional.') ?></p>
                        </div>
                        
                        <!-- Features -->
                        <div class="text-start mb-4">
                            <h5 class="fw-bold mb-3">Keunggulan</h5>
                            <ul class="list-unstyled">
                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i> Penanganan profesional</li>
                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i> Bahan berkualitas tinggi</li>
                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i> Garansi kepuasan</li>
                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i> Free pickup & delivery</li>
                            </ul>
                        </div>
                        
                        <!-- CTA -->
                        <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                            <a href="<?= base_url('services') ?>" class="btn btn-outline-secondary btn-lg">
                                <i class="bi bi-arrow-left"></i> Lihat Layanan Lain
                            </a>
                            <a href="<?= base_url('order/create') ?>" class="btn btn-primary btn-lg">
                                <i class="bi bi-cart-plus"></i> Pesan Sekarang
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php endif; ?>

<?= $this->endSection() ?>
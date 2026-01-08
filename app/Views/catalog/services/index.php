<?= $this->extend('layouts/main') ?>

<?php
if (!function_exists('format_rupiah')) {
    function format_rupiah($number) {
        return 'Rp ' . number_format((float)$number, 0, ',', '.');
    }
}
?>

<?= $this->section('styles') ?>
<style>
    .service-card {
        border-radius: 16px;
        transition: all 0.3s ease;
        border: none;
        overflow: hidden;
    }
    .service-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    }
    .service-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        margin: 0 auto 1rem;
    }
    .price-badge {
        font-size: 1.5rem;
        font-weight: 700;
    }
    .hero-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Hero Section -->
<section class="hero-section py-5 text-white text-center">
    <div class="container">
        <h1 class="display-4 fw-bold mb-3">
            <i class="bi bi-tags"></i> Layanan Kami
        </h1>
        <p class="lead mb-0">Pilih layanan terbaik untuk sepatu kesayangan Anda</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-warning text-center">
                <i class="bi bi-exclamation-triangle"></i> <?= esc($error) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($services)): ?>
            <div class="row g-4">
                <?php foreach ($services as $service): ?>
                    <?php if (!is_array($service) || !isset($service['id'])) continue; ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card service-card h-100 shadow">
                            <div class="card-body text-center p-4">
                                <div class="service-icon bg-primary bg-opacity-10 text-primary">
                                    <i class="bi bi-droplet"></i>
                                </div>
                                <h4 class="card-title fw-bold mb-3"><?= esc($service['name'] ?? 'Service') ?></h4>
                                <p class="text-muted mb-4"><?= esc($service['description'] ?? 'Layanan premium untuk sepatu Anda') ?></p>
                                
                                <div class="price-badge text-primary mb-3">
                                    <?= format_rupiah($service['price'] ?? 0) ?>
                                </div>
                                
                                <?php if (!empty($service['estimated_duration'])): ?>
                                    <p class="text-muted small mb-3">
                                        <i class="bi bi-clock"></i> <?= esc($service['estimated_duration']) ?>
                                    </p>
                                <?php endif; ?>
                                
                                <a href="<?= base_url('services/' . $service['id']) ?>" class="btn btn-outline-primary">
                                    <i class="bi bi-info-circle"></i> Detail
                                </a>
                                <a href="<?= base_url('order/create') ?>" class="btn btn-primary">
                                    <i class="bi bi-cart-plus"></i> Pesan
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="bi bi-tags fs-1 text-muted mb-3 d-block"></i>
                <h4 class="text-muted">Layanan Belum Tersedia</h4>
                <p class="text-muted">Layanan akan segera ditampilkan</p>
                <a href="<?= base_url() ?>" class="btn btn-primary">
                    <i class="bi bi-house"></i> Kembali ke Beranda
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5 bg-light">
    <div class="container text-center">
        <h3 class="fw-bold mb-3">Siap Merawat Sepatu Anda?</h3>
        <p class="text-muted mb-4">Pesan layanan sekarang dan rasakan perbedaannya</p>
        <a href="<?= base_url('order/create') ?>" class="btn btn-primary btn-lg">
            <i class="bi bi-cart-plus"></i> Pesan Sekarang
        </a>
    </div>
</section>

<?= $this->endSection() ?>
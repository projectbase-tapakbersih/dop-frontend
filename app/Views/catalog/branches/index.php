<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
<style>
    .branch-card {
        border-radius: 16px;
        transition: all 0.3s ease;
        border: none;
        overflow: hidden;
    }
    .branch-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    }
    .branch-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    .hero-section {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Hero Section -->
<section class="hero-section py-5 text-white text-center">
    <div class="container">
        <h1 class="display-4 fw-bold mb-3">
            <i class="bi bi-shop"></i> Cabang Kami
        </h1>
        <p class="lead mb-0">Temukan cabang Tirta Bersih terdekat dari lokasi Anda</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-warning text-center">
                <i class="bi bi-exclamation-triangle"></i> <?= esc($error) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($branches)): ?>
            <div class="row g-4">
                <?php foreach ($branches as $branch): ?>
                    <?php if (!is_array($branch) || !isset($branch['id'])) continue; ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card branch-card h-100 shadow">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-start mb-3">
                                    <div class="branch-icon bg-success bg-opacity-10 text-success me-3">
                                        <i class="bi bi-geo-alt"></i>
                                    </div>
                                    <div>
                                        <h5 class="card-title fw-bold mb-1"><?= esc($branch['name'] ?? 'Cabang') ?></h5>
                                        <?php if (isset($branch['is_active'])): ?>
                                            <span class="badge bg-<?= $branch['is_active'] ? 'success' : 'secondary' ?>">
                                                <?= $branch['is_active'] ? 'Buka' : 'Tutup' ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <?php if (!empty($branch['address'])): ?>
                                    <p class="mb-3">
                                        <i class="bi bi-map text-muted me-2"></i>
                                        <small><?= esc($branch['address']) ?></small>
                                    </p>
                                <?php endif; ?>
                                
                                <?php if (!empty($branch['phone'])): ?>
                                    <p class="mb-2">
                                        <i class="bi bi-telephone text-primary me-2"></i>
                                        <a href="tel:<?= esc($branch['phone']) ?>" class="text-decoration-none">
                                            <?= esc($branch['phone']) ?>
                                        </a>
                                    </p>
                                <?php endif; ?>
                                
                                <?php if (!empty($branch['email'])): ?>
                                    <p class="mb-2">
                                        <i class="bi bi-envelope text-info me-2"></i>
                                        <a href="mailto:<?= esc($branch['email']) ?>" class="text-decoration-none">
                                            <?= esc($branch['email']) ?>
                                        </a>
                                    </p>
                                <?php endif; ?>
                                
                                <?php if (!empty($branch['opening_hours'])): ?>
                                    <p class="mb-3">
                                        <i class="bi bi-clock text-warning me-2"></i>
                                        <small><?= esc($branch['opening_hours']) ?></small>
                                    </p>
                                <?php endif; ?>
                                
                                <a href="<?= base_url('branches/' . $branch['id']) ?>" class="btn btn-outline-success btn-sm">
                                    <i class="bi bi-info-circle"></i> Detail
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="bi bi-shop fs-1 text-muted mb-3 d-block"></i>
                <h4 class="text-muted">Cabang Belum Tersedia</h4>
                <p class="text-muted">Informasi cabang akan segera ditampilkan</p>
                <a href="<?= base_url() ?>" class="btn btn-primary">
                    <i class="bi bi-house"></i> Kembali ke Beranda
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Contact Section -->
<section class="py-5 bg-light">
    <div class="container text-center">
        <h3 class="fw-bold mb-3">Butuh Bantuan?</h3>
        <p class="text-muted mb-4">Hubungi kami untuk informasi lebih lanjut</p>
        <a href="https://wa.me/6281234567890" class="btn btn-success btn-lg" target="_blank">
            <i class="bi bi-whatsapp"></i> WhatsApp Kami
        </a>
    </div>
</section>

<?= $this->endSection() ?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
<style>
    .hero-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .comparison-container {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
    }
    .comparison-side {
        flex: 1;
        min-width: 300px;
    }
    .comparison-side img {
        width: 100%;
        height: auto;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }
    .comparison-label {
        display: inline-block;
        padding: 8px 20px;
        border-radius: 25px;
        font-weight: 600;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 15px;
    }
    .comparison-label.before {
        background: #333;
        color: white;
    }
    .comparison-label.after {
        background: #198754;
        color: white;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Hero Section -->
<section class="hero-section py-4 text-white">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2">
                <li class="breadcrumb-item"><a href="<?= base_url() ?>" class="text-white-50">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('gallery') ?>" class="text-white-50">Gallery</a></li>
                <li class="breadcrumb-item active text-white">Detail</li>
            </ol>
        </nav>
        <h1 class="fw-bold mb-0">
            <i class="bi bi-image"></i> Detail Gallery
        </h1>
    </div>
</section>

<section class="py-5">
    <div class="container">
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-warning text-center">
                <i class="bi bi-exclamation-triangle"></i> <?= esc($error) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($item)): ?>
            <?php
            $beforeImage = $item['before_image'] ?? 'https://via.placeholder.com/600x400?text=Before';
            $afterImage = $item['after_image'] ?? 'https://via.placeholder.com/600x400?text=After';
            $caption = $item['caption'] ?? $item['description'] ?? '';
            $serviceName = $item['service']['name'] ?? $item['service_name'] ?? 'Layanan';
            $createdAt = $item['created_at'] ?? '';
            ?>
            
            <!-- Service Badge -->
            <div class="text-center mb-4">
                <span class="badge bg-primary px-4 py-2 fs-6">
                    <i class="bi bi-tag"></i> <?= esc($serviceName) ?>
                </span>
            </div>
            
            <!-- Before/After Comparison -->
            <div class="comparison-container justify-content-center mb-4">
                <div class="comparison-side text-center">
                    <span class="comparison-label before">
                        <i class="bi bi-arrow-left"></i> Before
                    </span>
                    <img src="<?= esc($beforeImage) ?>" alt="Before" class="img-fluid">
                </div>
                <div class="comparison-side text-center">
                    <span class="comparison-label after">
                        After <i class="bi bi-arrow-right"></i>
                    </span>
                    <img src="<?= esc($afterImage) ?>" alt="After" class="img-fluid">
                </div>
            </div>
            
            <!-- Caption -->
            <?php if (!empty($caption)): ?>
                <div class="card shadow-sm mb-4">
                    <div class="card-body text-center">
                        <i class="bi bi-chat-quote text-primary fs-2 mb-2 d-block"></i>
                        <p class="lead mb-0"><?= esc($caption) ?></p>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Date -->
            <?php if (!empty($createdAt)): ?>
                <div class="text-center text-muted mb-4">
                    <i class="bi bi-calendar"></i> <?= format_tanggal($createdAt) ?>
                </div>
            <?php endif; ?>
            
            <!-- Action Buttons -->
            <div class="text-center">
                <a href="<?= base_url('order/create?service=' . ($item['service_id'] ?? '')) ?>" class="btn btn-primary btn-lg me-2">
                    <i class="bi bi-cart-plus"></i> Pesan Layanan Ini
                </a>
                <a href="<?= base_url('gallery') ?>" class="btn btn-outline-secondary btn-lg">
                    <i class="bi bi-arrow-left"></i> Kembali ke Gallery
                </a>
            </div>
            
        <?php else: ?>
            <div class="text-center py-5">
                <i class="bi bi-exclamation-circle fs-1 text-muted mb-3 d-block"></i>
                <h4 class="text-muted">Gallery Tidak Ditemukan</h4>
                <a href="<?= base_url('gallery') ?>" class="btn btn-primary mt-3">
                    <i class="bi bi-arrow-left"></i> Kembali ke Gallery
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5 bg-light">
    <div class="container text-center">
        <h3 class="fw-bold mb-3">Ingin Hasil Seperti Ini?</h3>
        <p class="text-muted mb-4">Pesan layanan kami dan lihat transformasi sepatu kesayangan Anda</p>
        <a href="<?= base_url('order/create') ?>" class="btn btn-primary btn-lg">
            <i class="bi bi-cart-plus"></i> Pesan Sekarang
        </a>
    </div>
</section>

<?= $this->endSection() ?>
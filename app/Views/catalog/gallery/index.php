<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
<style>
    .gallery-item {
        position: relative;
        border-radius: 16px;
        overflow: hidden;
        cursor: pointer;
        aspect-ratio: 1;
    }
    .gallery-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }
    .gallery-item:hover img {
        transform: scale(1.1);
    }
    .gallery-item .overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, transparent 50%);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-end;
        padding: 1rem;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .gallery-item:hover .overlay {
        opacity: 1;
    }
    .gallery-item .overlay i {
        font-size: 2rem;
        color: white;
        margin-bottom: 0.5rem;
    }
    .gallery-item .overlay span {
        color: white;
        font-size: 0.9rem;
    }
    .hero-section {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Hero Section -->
<section class="hero-section py-5 text-white text-center">
    <div class="container">
        <h1 class="display-4 fw-bold mb-3">
            <i class="bi bi-images"></i> Gallery
        </h1>
        <p class="lead mb-0">Lihat hasil kerja kami dalam merawat sepatu kesayangan Anda</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-warning text-center">
                <i class="bi bi-exclamation-triangle"></i> <?= esc($error) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($gallery)): ?>
            <div class="row g-4">
                <?php foreach ($gallery as $item): ?>
                    <?php if (!is_array($item) || !isset($item['id'])) continue; ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="gallery-item shadow" 
                             data-bs-toggle="modal" 
                             data-bs-target="#imageModal" 
                             onclick="showImage('<?= esc($item['image_url'] ?? $item['image'] ?? '') ?>', '<?= esc($item['title'] ?? $item['caption'] ?? '') ?>')">
                            <img src="<?= esc($item['image_url'] ?? $item['image'] ?? 'https://via.placeholder.com/400x400?text=No+Image') ?>" 
                                 alt="<?= esc($item['title'] ?? $item['caption'] ?? 'Gallery Image') ?>"
                                 loading="lazy">
                            <div class="overlay">
                                <i class="bi bi-zoom-in"></i>
                                <?php if (!empty($item['title']) || !empty($item['caption'])): ?>
                                    <span><?= esc($item['title'] ?? $item['caption']) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="bi bi-images fs-1 text-muted mb-3 d-block"></i>
                <h4 class="text-muted">Gallery Belum Tersedia</h4>
                <p class="text-muted">Foto-foto hasil pekerjaan akan segera ditampilkan</p>
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
        <h3 class="fw-bold mb-3">Ingin Sepatu Anda Seperti Ini?</h3>
        <p class="text-muted mb-4">Pesan layanan kami sekarang dan lihat transformasinya</p>
        <a href="<?= base_url('order/create') ?>" class="btn btn-primary btn-lg">
            <i class="bi bi-cart-plus"></i> Pesan Sekarang
        </a>
    </div>
</section>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content bg-dark border-0">
            <div class="modal-header border-0">
                <h5 class="modal-title text-white" id="imageModalTitle"></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-0">
                <img src="" id="modalImage" class="img-fluid" style="max-height: 80vh; border-radius: 0 0 8px 8px;">
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function showImage(url, title) {
    document.getElementById('modalImage').src = url || 'https://via.placeholder.com/800x600?text=No+Image';
    document.getElementById('imageModalTitle').textContent = title || 'Gallery Image';
}
</script>
<?= $this->endSection() ?>
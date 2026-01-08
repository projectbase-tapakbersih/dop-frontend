<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
<style>
    .hero-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    /* Gallery Card */
    .gallery-card {
        border-radius: 16px;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        cursor: pointer;
    }
    .gallery-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.2);
    }
    
    /* Before/After Container */
    .before-after-wrapper {
        position: relative;
        display: flex;
        height: 280px;
    }
    .before-after-wrapper .image-side {
        flex: 1;
        position: relative;
        overflow: hidden;
    }
    .before-after-wrapper .image-side img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }
    .gallery-card:hover .before-after-wrapper .image-side img {
        transform: scale(1.05);
    }
    .before-after-wrapper .image-label {
        position: absolute;
        top: 10px;
        padding: 4px 12px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        border-radius: 20px;
    }
    .before-after-wrapper .image-side.before .image-label {
        left: 10px;
        background: rgba(0,0,0,0.7);
        color: white;
    }
    .before-after-wrapper .image-side.after .image-label {
        right: 10px;
        background: rgba(25, 135, 84, 0.9);
        color: white;
    }
    .before-after-wrapper .divider {
        position: absolute;
        top: 0;
        bottom: 0;
        left: 50%;
        width: 3px;
        background: white;
        z-index: 10;
        transform: translateX(-50%);
    }
    .before-after-wrapper .divider::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 30px;
        height: 30px;
        background: white;
        border-radius: 50%;
        box-shadow: 0 2px 10px rgba(0,0,0,0.3);
    }
    .before-after-wrapper .divider::after {
        content: '↔';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 14px;
        color: #333;
        font-weight: bold;
    }
    
    /* Gallery Info */
    .gallery-info {
        padding: 1rem;
        background: white;
    }
    .gallery-info .service-badge {
        font-size: 11px;
        padding: 4px 10px;
        border-radius: 20px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    .gallery-info .caption {
        color: #666;
        font-size: 14px;
        line-height: 1.5;
        margin-top: 8px;
    }
    
    /* Modal Styles */
    .modal-gallery .before-after-compare {
        display: flex;
        gap: 10px;
    }
    .modal-gallery .compare-side {
        flex: 1;
        text-align: center;
    }
    .modal-gallery .compare-side img {
        width: 100%;
        height: auto;
        max-height: 60vh;
        object-fit: contain;
        border-radius: 8px;
    }
    .modal-gallery .compare-side .label {
        display: inline-block;
        padding: 6px 16px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 10px;
    }
    .modal-gallery .compare-side.before .label {
        background: #333;
        color: white;
    }
    .modal-gallery .compare-side.after .label {
        background: #198754;
        color: white;
    }
    
    /* Filter Buttons */
    .filter-btn {
        border-radius: 20px;
        padding: 8px 20px;
        font-size: 14px;
        transition: all 0.3s ease;
    }
    .filter-btn.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-color: transparent;
        color: white;
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
        <p class="lead mb-0">Lihat transformasi sepatu yang telah kami tangani</p>
        <p class="mt-2 opacity-75">Before & After hasil kerja profesional kami</p>
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
            
            <!-- Gallery Stats -->
            <div class="text-center mb-4">
                <span class="badge bg-primary bg-opacity-10 text-primary px-4 py-2 fs-6">
                    <i class="bi bi-images"></i> <?= count($gallery) ?> Transformasi
                </span>
            </div>
            
            <!-- Gallery Grid -->
            <div class="row g-4">
                <?php foreach ($gallery as $item): ?>
                    <?php if (!is_array($item) || !isset($item['id'])) continue; ?>
                    <?php
                    $beforeImage = $item['before_image'] ?? 'https://via.placeholder.com/400x300?text=Before';
                    $afterImage = $item['after_image'] ?? 'https://via.placeholder.com/400x300?text=After';
                    $caption = $item['caption'] ?? $item['description'] ?? '';
                    $serviceName = $item['service']['name'] ?? $item['service_name'] ?? 'Layanan';
                    ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card gallery-card shadow-sm h-100" 
                             data-bs-toggle="modal" 
                             data-bs-target="#galleryModal"
                             onclick="showGalleryDetail('<?= esc($beforeImage) ?>', '<?= esc($afterImage) ?>', '<?= esc(addslashes($caption)) ?>', '<?= esc($serviceName) ?>')">
                            
                            <!-- Before/After Images -->
                            <div class="before-after-wrapper">
                                <div class="image-side before">
                                    <img src="<?= esc($beforeImage) ?>" alt="Before" loading="lazy">
                                    <span class="image-label">Before</span>
                                </div>
                                <div class="divider"></div>
                                <div class="image-side after">
                                    <img src="<?= esc($afterImage) ?>" alt="After" loading="lazy">
                                    <span class="image-label">After</span>
                                </div>
                            </div>
                            
                            <!-- Gallery Info -->
                            <div class="gallery-info">
                                <span class="service-badge">
                                    <i class="bi bi-tag"></i> <?= esc($serviceName) ?>
                                </span>
                                <?php if (!empty($caption)): ?>
                                    <p class="caption mb-0 mt-2">
                                        <?= esc(strlen($caption) > 80 ? substr($caption, 0, 80) . '...' : $caption) ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
        <?php else: ?>
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="bi bi-images" style="font-size: 5rem; color: #dee2e6;"></i>
                </div>
                <h4 class="text-muted mb-3">Gallery Belum Tersedia</h4>
                <p class="text-muted mb-4">Foto-foto hasil pekerjaan akan segera ditampilkan</p>
                <a href="<?= base_url() ?>" class="btn btn-primary">
                    <i class="bi bi-house"></i> Kembali ke Beranda
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h3 class="fw-bold mb-2">Ingin Sepatu Anda Seperti Ini?</h3>
                <p class="text-muted mb-lg-0">Pesan layanan kami sekarang dan lihat transformasi sepatu kesayangan Anda!</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="<?= base_url('order/create') ?>" class="btn btn-primary btn-lg">
                    <i class="bi bi-cart-plus"></i> Pesan Sekarang
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Gallery Detail Modal -->
<div class="modal fade modal-gallery" id="galleryModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <div>
                    <h5 class="modal-title fw-bold" id="modalServiceName">
                        <i class="bi bi-images text-primary"></i> Detail Gallery
                    </h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Before/After Comparison -->
                <div class="before-after-compare">
                    <div class="compare-side before">
                        <span class="label"><i class="bi bi-arrow-left"></i> Before</span>
                        <img src="" id="modalBeforeImage" alt="Before">
                    </div>
                    <div class="compare-side after">
                        <span class="label">After <i class="bi bi-arrow-right"></i></span>
                        <img src="" id="modalAfterImage" alt="After">
                    </div>
                </div>
                
                <!-- Caption -->
                <div id="modalCaptionContainer" class="mt-4 text-center" style="display: none;">
                    <div class="bg-light rounded-3 p-3">
                        <i class="bi bi-chat-quote text-primary"></i>
                        <p class="mb-0 mt-2" id="modalCaption"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <a href="<?= base_url('order/create') ?>" class="btn btn-primary">
                    <i class="bi bi-cart-plus"></i> Pesan Layanan Ini
                </a>
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function showGalleryDetail(beforeImage, afterImage, caption, serviceName) {
    // Set images
    document.getElementById('modalBeforeImage').src = beforeImage || 'https://via.placeholder.com/600x400?text=Before';
    document.getElementById('modalAfterImage').src = afterImage || 'https://via.placeholder.com/600x400?text=After';
    
    // Set service name
    document.getElementById('modalServiceName').innerHTML = '<i class="bi bi-tag text-primary"></i> ' + (serviceName || 'Detail Gallery');
    
    // Set caption
    const captionContainer = document.getElementById('modalCaptionContainer');
    const captionElement = document.getElementById('modalCaption');
    
    if (caption && caption.trim() !== '') {
        captionElement.textContent = caption;
        captionContainer.style.display = 'block';
    } else {
        captionContainer.style.display = 'none';
    }
}

// Lazy loading for images
document.addEventListener('DOMContentLoaded', function() {
    const lazyImages = document.querySelectorAll('img[loading="lazy"]');
    
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver(function(entries, observer) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    const image = entry.target;
                    image.classList.add('loaded');
                    imageObserver.unobserve(image);
                }
            });
        });
        
        lazyImages.forEach(function(image) {
            imageObserver.observe(image);
        });
    }
});
</script>
<?= $this->endSection() ?>
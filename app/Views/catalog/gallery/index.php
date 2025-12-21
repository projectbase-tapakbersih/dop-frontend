<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
<style>
.gallery-item {
    cursor: pointer;
    transition: transform 0.3s ease;
}
.gallery-item:hover {
    transform: scale(1.05);
}
.modal-fullscreen-image {
    max-width: 100%;
    max-height: 80vh;
    object-fit: contain;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<section class="py-5 text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container text-center">
        <h1 class="display-4 fw-bold mb-3">Gallery Hasil Kerja</h1>
        <p class="lead">Lihat transformasi sepatu yang kami tangani</p>
    </div>
</section>

<!-- Gallery Grid -->
<section class="py-5">
    <div class="container">
        
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle"></i> <?= esc($error) ?>
            </div>
        <?php endif; ?>
        
        <?php if (empty($galleries)): ?>
            <div class="text-center py-5">
                <i class="bi bi-images display-1 text-muted"></i>
                <h3 class="mt-3">Belum Ada Gallery</h3>
                <p class="text-muted">Gallery hasil kerja kami akan segera ditampilkan</p>
            </div>
        <?php else: ?>
            
            <div class="row mb-4">
                <div class="col">
                    <p class="text-muted mb-0">
                        Menampilkan <strong><?= count($galleries) ?></strong> hasil pekerjaan
                    </p>
                </div>
            </div>
            
            <div class="row g-4">
                <?php foreach ($galleries as $index => $gallery): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card shadow-sm h-100">
                            <!-- Before/After Images -->
                            <div class="row g-0 gallery-item" 
                                 data-bs-toggle="modal" 
                                 data-bs-target="#galleryModal<?= $gallery['id'] ?>">
                                <div class="col-6 position-relative">
                                    <img src="<?= esc($gallery['before_image']) ?>" 
                                         class="img-fluid w-100" 
                                         style="height: 200px; object-fit: cover;" 
                                         alt="Before">
                                    <span class="badge bg-dark position-absolute top-0 start-0 m-2">
                                        Before
                                    </span>
                                </div>
                                <div class="col-6 position-relative">
                                    <img src="<?= esc($gallery['after_image']) ?>" 
                                         class="img-fluid w-100" 
                                         style="height: 200px; object-fit: cover;" 
                                         alt="After">
                                    <span class="badge bg-success position-absolute top-0 end-0 m-2">
                                        After
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Description -->
                            <div class="card-body">
                                <p class="card-text text-muted mb-2">
                                    <?= esc($gallery['description']) ?>
                                </p>
                                
                                <?php if (isset($gallery['service'])): ?>
                                    <div class="mt-2">
                                        <a href="<?= base_url('services/' . $gallery['service']['id']) ?>" 
                                           class="badge bg-primary text-decoration-none">
                                            <?= esc($gallery['service']['name']) ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Card Footer -->
                            <div class="card-footer bg-transparent">
                                <button class="btn btn-sm btn-outline-primary w-100" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#galleryModal<?= $gallery['id'] ?>">
                                    <i class="bi bi-zoom-in"></i> Lihat Detail
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Modal for Each Gallery Item -->
                    <div class="modal fade" id="galleryModal<?= $gallery['id'] ?>" tabindex="-1">
                        <div class="modal-dialog modal-xl modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Detail Gallery</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row g-4">
                                        <div class="col-md-6 text-center">
                                            <h6 class="fw-bold mb-3">Before</h6>
                                            <img src="<?= esc($gallery['before_image']) ?>" 
                                                 class="modal-fullscreen-image rounded" 
                                                 alt="Before">
                                        </div>
                                        <div class="col-md-6 text-center">
                                            <h6 class="fw-bold mb-3">After</h6>
                                            <img src="<?= esc($gallery['after_image']) ?>" 
                                                 class="modal-fullscreen-image rounded" 
                                                 alt="After">
                                        </div>
                                    </div>
                                    
                                    <?php if ($gallery['description']): ?>
                                        <div class="mt-4">
                                            <h6 class="fw-bold">Deskripsi:</h6>
                                            <p class="text-muted"><?= esc($gallery['description']) ?></p>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (isset($gallery['service'])): ?>
                                        <div class="mt-3">
                                            <h6 class="fw-bold">Layanan:</h6>
                                            <a href="<?= base_url('services/' . $gallery['service']['id']) ?>" 
                                               class="btn btn-primary">
                                                Lihat Layanan: <?= esc($gallery['service']['name']) ?>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
        <?php endif; ?>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5 bg-light">
    <div class="container text-center">
        <h2 class="fw-bold mb-3">Ingin Sepatu Anda Sebersih Ini?</h2>
        <p class="lead text-muted mb-4">Percayakan pada ahlinya</p>
        <a href="<?= base_url('order/create') ?>" class="btn btn-primary btn-lg">
            <i class="bi bi-bag-plus"></i> Pesan Sekarang
        </a>
    </div>
</section>

<?= $this->endSection() ?>
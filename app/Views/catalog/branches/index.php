<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<section class="py-5 text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container text-center">
        <h1 class="display-4 fw-bold mb-3">Lokasi Cabang Kami</h1>
        <p class="lead">Temukan cabang terdekat dari lokasi Anda</p>
    </div>
</section>

<!-- Branches List -->
<section class="py-5">
    <div class="container">
        
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle"></i> <?= esc($error) ?>
            </div>
        <?php endif; ?>
        
        <?php if (empty($branches)): ?>
            <div class="text-center py-5">
                <i class="bi bi-geo-alt display-1 text-muted"></i>
                <h3 class="mt-3">Belum Ada Cabang Tersedia</h3>
                <p class="text-muted">Kami akan segera hadir di kota Anda</p>
            </div>
        <?php else: ?>
            
            <div class="row mb-4">
                <div class="col">
                    <p class="text-muted mb-0">
                        Ditemukan <strong><?= count($branches) ?></strong> cabang
                    </p>
                </div>
            </div>
            
            <div class="row g-4">
                <?php foreach ($branches as $branch): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm">
                            <!-- Status Badge -->
                            <div class="position-absolute top-0 end-0 m-3 z-1">
                                <?php if ($branch['is_active']): ?>
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle"></i> Buka
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-danger">
                                        <i class="bi bi-x-circle"></i> Tutup
                                    </span>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Map Preview (Static) -->
                            <div class="bg-light" style="height: 200px; position: relative;">
                                <iframe 
                                    width="100%" 
                                    height="200" 
                                    frameborder="0" 
                                    style="border:0"
                                    src="https://www.google.com/maps?q=<?= $branch['latitude'] ?>,<?= $branch['longitude'] ?>&output=embed"
                                    allowfullscreen>
                                </iframe>
                            </div>
                            
                            <div class="card-body">
                                <!-- Branch Name -->
                                <h5 class="card-title fw-bold">
                                    <i class="bi bi-shop text-primary"></i>
                                    <?= esc($branch['name']) ?>
                                </h5>
                                
                                <!-- Address -->
                                <p class="card-text text-muted">
                                    <i class="bi bi-geo-alt-fill text-danger"></i>
                                    <?= esc($branch['address']) ?>
                                </p>
                                
                                <!-- Coverage Info -->
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <span class="badge bg-info">
                                        <i class="bi bi-geo"></i> 
                                        Radius Layanan: <?= $branch['coverage_radius_km'] ?> km
                                    </span>
                                </div>
                                
                                <!-- Contact Info -->
                                <div class="mb-3">
                                    <small class="text-muted d-block mb-1">
                                        <i class="bi bi-telephone"></i> Hubungi Cabang:
                                    </small>
                                    <a href="https://wa.me/6281234567890" 
                                       target="_blank" 
                                       class="btn btn-success btn-sm">
                                        <i class="bi bi-whatsapp"></i> WhatsApp
                                    </a>
                                </div>
                            </div>
                            
                            <!-- Card Footer -->
                            <div class="card-footer bg-transparent border-top">
                                <div class="d-grid gap-2">
                                    <a href="<?= base_url('branches/' . $branch['id']) ?>" 
                                       class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-info-circle"></i> Lihat Detail
                                    </a>
                                    <a href="https://www.google.com/maps/dir/?api=1&destination=<?= $branch['latitude'] ?>,<?= $branch['longitude'] ?>" 
                                       target="_blank" 
                                       class="btn btn-primary btn-sm">
                                        <i class="bi bi-compass"></i> Buka di Google Maps
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
        <?php endif; ?>
    </div>
</section>

<!-- Coverage Info -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h2 class="fw-bold mb-3">Area Jangkauan Layanan</h2>
                <p class="lead text-muted">Kami melayani pickup & delivery di seluruh area coverage cabang</p>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                        Gratis pickup dari lokasi Anda
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                        Antar kembali setelah selesai
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                        Tracking real-time via WhatsApp
                    </li>
                </ul>
            </div>
            <div class="col-md-6 text-center">
                <img src="https://images.unsplash.com/photo-1578575437130-527eed3abbec?w=500" 
                     alt="Delivery" 
                     class="img-fluid rounded shadow">
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-5">
    <div class="container text-center">
        <h2 class="fw-bold mb-3">Siap Melayani Sepatu Anda</h2>
        <p class="lead text-muted mb-4">Pilih cabang terdekat dan pesan sekarang</p>
        <a href="<?= base_url('order/create') ?>" class="btn btn-primary btn-lg">
            <i class="bi bi-bag-plus"></i> Pesan Sekarang
        </a>
    </div>
</section>

<?= $this->endSection() ?>
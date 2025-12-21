<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Hero Section -->
<section class="hero bg-gradient py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 500px;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 text-white">
                <h1 class="display-4 fw-bold mb-4">Sepatu Bersih, Tampil Percaya Diri! 👟</h1>
                <p class="lead mb-4">Layanan perawatan sepatu profesional dengan hasil maksimal. Pickup & delivery gratis!</p>
                <div class="d-flex gap-3">
                    <a href="<?= base_url('order/create') ?>" class="btn btn-light btn-lg">
                        <i class="bi bi-bag-plus"></i> Pesan Sekarang
                    </a>
                    <a href="<?= base_url('services') ?>" class="btn btn-outline-light btn-lg">
                        <i class="bi bi-grid"></i> Lihat Layanan
                    </a>
                </div>
                
                <!-- Stats -->
                <div class="row mt-5">
                    <div class="col-4">
                        <h3 class="fw-bold">1000+</h3>
                        <p class="mb-0">Sepatu Ditangani</p>
                    </div>
                    <div class="col-4">
                        <h3 class="fw-bold">500+</h3>
                        <p class="mb-0">Pelanggan Puas</p>
                    </div>
                    <div class="col-4">
                        <h3 class="fw-bold">24 Jam</h3>
                        <p class="mb-0">Express Service</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <img src="https://images.unsplash.com/photo-1549298916-b41d501d3772?w=600" alt="Sepatu Bersih" class="img-fluid rounded shadow-lg" style="max-height: 400px; object-fit: cover;">
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-3 text-center">
                <div class="p-4">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex p-4 mb-3">
                        <i class="bi bi-truck fs-1 text-primary"></i>
                    </div>
                    <h5 class="fw-bold">Pickup & Delivery</h5>
                    <p class="text-muted">Gratis pickup dan antar ke lokasi Anda</p>
                </div>
            </div>
            <div class="col-md-3 text-center">
                <div class="p-4">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex p-4 mb-3">
                        <i class="bi bi-shield-check fs-1 text-primary"></i>
                    </div>
                    <h5 class="fw-bold">Garansi Kualitas</h5>
                    <p class="text-muted">Dijamin bersih atau uang kembali</p>
                </div>
            </div>
            <div class="col-md-3 text-center">
                <div class="p-4">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex p-4 mb-3">
                        <i class="bi bi-clock-history fs-1 text-primary"></i>
                    </div>
                    <h5 class="fw-bold">Cepat & Tepat</h5>
                    <p class="text-muted">Proses 24-72 jam sesuai layanan</p>
                </div>
            </div>
            <div class="col-md-3 text-center">
                <div class="p-4">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex p-4 mb-3">
                        <i class="bi bi-star-fill fs-1 text-primary"></i>
                    </div>
                    <h5 class="fw-bold">Profesional</h5>
                    <p class="text-muted">Dikerjakan oleh tenaga ahli</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center section-title">Layanan Kami</h2>
        
        <?php if (empty($services)): ?>
            <div class="alert alert-info text-center">
                <i class="bi bi-info-circle"></i> Belum ada layanan tersedia
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($services as $service): ?>
                    <div class="col-md-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h5 class="card-title fw-bold"><?= esc($service['name']) ?></h5>
                                    <span class="badge bg-primary"><?= $service['duration_hours'] ?> Jam</span>
                                </div>
                                <p class="card-text text-muted"><?= esc($service['description']) ?></p>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <span class="price-tag"><?= format_rupiah($service['price']) ?></span>
                                    <a href="<?= base_url('services/' . $service['id']) ?>" class="btn btn-outline-primary btn-sm">
                                        Lihat Detail <i class="bi bi-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="text-center mt-4">
                <a href="<?= base_url('services') ?>" class="btn btn-primary btn-lg">
                    Lihat Semua Layanan <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Gallery Section -->
<?php if (!empty($galleries)): ?>
<section class="py-5">
    <div class="container">
        <h2 class="text-center section-title">Hasil Kerja Kami</h2>
        
        <div class="row g-4">
            <?php foreach ($galleries as $gallery): ?>
                <div class="col-md-4">
                    <div class="card">
                        <div class="row g-0">
                            <div class="col-6">
                                <img src="<?= esc($gallery['before_image']) ?>" class="img-fluid h-100 object-fit-cover" alt="Before">
                                <div class="position-absolute top-0 start-0 m-2">
                                    <span class="badge bg-dark">Before</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <img src="<?= esc($gallery['after_image']) ?>" class="img-fluid h-100 object-fit-cover" alt="After">
                                <div class="position-absolute top-0 end-0 m-2">
                                    <span class="badge bg-success">After</span>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <p class="card-text text-muted mb-0"><?= esc($gallery['description']) ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-4">
            <a href="<?= base_url('gallery') ?>" class="btn btn-primary btn-lg">
                Lihat Gallery Lengkap <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Branches Section -->
<?php if (!empty($branches)): ?>
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center section-title">Lokasi Cabang Kami</h2>
        
        <div class="row g-4">
            <?php foreach ($branches as $branch): ?>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title fw-bold">
                                <i class="bi bi-geo-alt-fill text-primary"></i>
                                <?= esc($branch['name']) ?>
                            </h5>
                            <p class="text-muted mb-2">
                                <i class="bi bi-pin-map"></i> <?= esc($branch['address']) ?>
                            </p>
                            <div class="d-flex gap-2 mt-3">
                                <span class="badge bg-info">Radius: <?= $branch['coverage_radius_km'] ?> km</span>
                                <?php if ($branch['is_active']): ?>
                                    <span class="badge bg-success">Buka</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Tutup</span>
                                <?php endif; ?>
                            </div>
                            <a href="<?= base_url('branches/' . $branch['id']) ?>" class="btn btn-outline-primary btn-sm mt-3">
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CTA Section -->
<section class="py-5 text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container text-center">
        <h2 class="fw-bold mb-4">Siap Memberikan Sepatu Terbaik untuk Anda!</h2>
        <p class="lead mb-4">Pesan sekarang dan rasakan perbedaannya</p>
        <div class="d-flex gap-3 justify-content-center">
            <a href="<?= base_url('order/create') ?>" class="btn btn-light btn-lg">
                <i class="bi bi-bag-plus"></i> Pesan Sekarang
            </a>
            <a href="https://wa.me/6281234567890" target="_blank" class="btn btn-success btn-lg">
                <i class="bi bi-whatsapp"></i> Hubungi Kami
            </a>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
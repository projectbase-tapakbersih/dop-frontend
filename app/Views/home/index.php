<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
<style>
    .hero-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 600px;
        position: relative;
        overflow: hidden;
    }
    .hero-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse"><path d="M 40 0 L 0 0 0 40" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
        opacity: 0.3;
    }
    .hero-content {
        position: relative;
        z-index: 1;
    }
    .service-card {
        transition: all 0.3s ease;
        border: none;
        height: 100%;
    }
    .service-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.2);
    }
    .service-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
    }
    .feature-box {
        background: white;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        height: 100%;
        transition: transform 0.3s ease;
    }
    .feature-box:hover {
        transform: translateY(-5px);
    }
    .stat-card {
        background: white;
        padding: 30px;
        border-radius: 15px;
        text-align: center;
    }
    .cta-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        position: relative;
        overflow: hidden;
    }
    .pulse-button {
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.7); }
        70% { box-shadow: 0 0 0 20px rgba(255, 255, 255, 0); }
        100% { box-shadow: 0 0 0 0 rgba(255, 255, 255, 0); }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- HERO SECTION -->
<section class="hero-section">
    <div class="container hero-content">
        <div class="row align-items-center" style="min-height: 600px;">
            <div class="col-lg-6 text-white">
                <!-- Brand Name -->
                <div class="mb-4">
                    <h1 class="display-3 fw-bold mb-3">
                        👟 Tapak Bersih
                    </h1>
                    <div class="h4 mb-4" style="letter-spacing: 2px; font-weight: 300;">
                        PROFESSIONAL SHOE CARE
                    </div>
                </div>

                <!-- Tagline -->
                <p class="lead mb-4" style="font-size: 1.3rem; line-height: 1.8;">
                    Sepatu bersih, penampilan percaya diri! 
                    <br>Layanan perawatan sepatu profesional dengan hasil maksimal.
                </p>

                <!-- Key Points -->
                <div class="mb-4">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-check-circle-fill me-3 fs-5"></i>
                        <span class="fs-6">Gratis Jemput & Antar ke Lokasi Anda</span>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-check-circle-fill me-3 fs-5"></i>
                        <span class="fs-6">Tracking Real-Time via WhatsApp</span>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-check-circle-fill me-3 fs-5"></i>
                        <span class="fs-6">Pembayaran Digital & Mudah</span>
                    </div>
                </div>

                <!-- CTA Buttons -->
                <div class="d-flex gap-3 mb-4">
                    <a href="<?= base_url('order/create') ?>" class="btn btn-light btn-lg px-5 py-3 pulse-button">
                        <i class="bi bi-bag-plus"></i> <strong>Pesan Sekarang</strong>
                    </a>
                    <a href="<?= base_url('services') ?>" class="btn btn-outline-light btn-lg px-4 py-3">
                        <i class="bi bi-grid"></i> Lihat Layanan
                    </a>
                </div>

                <!-- Stats Mini -->
                <div class="row mt-5">
                    <div class="col-4">
                        <h3 class="fw-bold mb-0"><?= $stats['total_customers'] ?></h3>
                        <small>Pelanggan</small>
                    </div>
                    <div class="col-4">
                        <h3 class="fw-bold mb-0"><?= $stats['shoes_cleaned'] ?></h3>
                        <small>Sepatu Ditangani</small>
                    </div>
                    <div class="col-4">
                        <h3 class="fw-bold mb-0"><?= $stats['satisfaction_rate'] ?></h3>
                        <small>Kepuasan</small>
                    </div>
                </div>
            </div>

            <!-- Hero Image -->
            <div class="col-lg-6 text-center">
                <img src="https://images.unsplash.com/photo-1460353581641-37baddab0fa2?w=600&h=600&fit=crop" 
                     alt="Sepatu Bersih" 
                     class="img-fluid rounded-4 shadow-lg" 
                     style="max-height: 500px; object-fit: cover; border: 5px solid rgba(255,255,255,0.2);">
            </div>
        </div>
    </div>
</section>

<!-- HIGHLIGHT KEUNGGULAN -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title fw-bold">Mengapa Memilih Tapak Bersih?</h2>
            <p class="text-muted">Keunggulan layanan kami untuk kenyamanan Anda</p>
        </div>

        <div class="row g-4">
            <!-- Feature 1: Jemput Antar -->
            <div class="col-md-4">
                <div class="feature-box text-center">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex p-4 mb-3">
                        <i class="bi bi-truck fs-1 text-primary"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Gratis Jemput & Antar</h5>
                    <p class="text-muted mb-0">
                        Kami jemput sepatu di lokasi Anda dan antar kembali setelah selesai. 
                        Tanpa biaya tambahan!
                    </p>
                </div>
            </div>

            <!-- Feature 2: Tracking Real-time -->
            <div class="col-md-4">
                <div class="feature-box text-center">
                    <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex p-4 mb-3">
                        <i class="bi bi-geo-alt fs-1 text-success"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Tracking Real-Time</h5>
                    <p class="text-muted mb-0">
                        Pantau status pesanan Anda secara real-time via WhatsApp. 
                        Dari pickup hingga delivery!
                    </p>
                </div>
            </div>

            <!-- Feature 3: Pembayaran Digital -->
            <div class="col-md-4">
                <div class="feature-box text-center">
                    <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex p-4 mb-3">
                        <i class="bi bi-credit-card fs-1 text-warning"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Pembayaran Digital</h5>
                    <p class="text-muted mb-0">
                        Bayar dengan mudah via QRIS atau transfer bank. 
                        Aman, cepat, dan praktis!
                    </p>
                </div>
            </div>

            <!-- Feature 4: Profesional -->
            <div class="col-md-4">
                <div class="feature-box text-center">
                    <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex p-4 mb-3">
                        <i class="bi bi-award fs-1 text-info"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Tenaga Profesional</h5>
                    <p class="text-muted mb-0">
                        Dikerjakan oleh tenaga ahli berpengalaman dengan produk berkualitas premium.
                    </p>
                </div>
            </div>

            <!-- Feature 5: Garansi -->
            <div class="col-md-4">
                <div class="feature-box text-center">
                    <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex p-4 mb-3">
                        <i class="bi bi-shield-check fs-1 text-danger"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Garansi Kepuasan</h5>
                    <p class="text-muted mb-0">
                        Jaminan hasil memuaskan atau uang kembali. Kami berkomitmen pada kualitas terbaik.
                    </p>
                </div>
            </div>

            <!-- Feature 6: Cepat -->
            <div class="col-md-4">
                <div class="feature-box text-center">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex p-4 mb-3">
                        <i class="bi bi-lightning-charge fs-1 text-primary"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Express Service</h5>
                    <p class="text-muted mb-0">
                        Proses cepat 24-72 jam sesuai jenis layanan. Ada layanan express untuk kebutuhan mendesak!
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- RINGKASAN JENIS LAYANAN -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title fw-bold">Layanan Kami</h2>
            <p class="text-muted">Pilih layanan sesuai kebutuhan sepatu Anda</p>
        </div>

        <?php if (empty($services)): ?>
            <div class="alert alert-info text-center">
                <i class="bi bi-info-circle"></i> Sedang mempersiapkan layanan terbaik untuk Anda
            </div>
        <?php else: ?>
            <div class="row g-4 mb-4">
                <?php foreach ($services as $index => $service): ?>
                    <?php if ($index < 6): // Show first 6 services ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card service-card shadow-sm h-100">
                                <div class="card-body p-4">
                                    <!-- Service Icon -->
                                    <div class="service-icon">
                                        <i class="bi bi-stars fs-1 text-white"></i>
                                    </div>

                                    <!-- Service Info -->
                                    <h5 class="fw-bold text-center mb-3"><?= esc($service['name']) ?></h5>
                                    <p class="text-muted text-center mb-4">
                                        <?= esc($service['description']) ?>
                                    </p>

                                    <!-- Service Details -->
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <small class="text-muted d-block">Harga</small>
                                            <h5 class="text-primary mb-0 fw-bold">
                                                <?= format_rupiah($service['price']) ?>
                                            </h5>
                                        </div>
                                        <div class="text-end">
                                            <small class="text-muted d-block">Estimasi</small>
                                            <strong><?= $service['duration_hours'] ?> Jam</strong>
                                        </div>
                                    </div>

                                    <!-- CTA Button -->
                                    <div class="d-grid gap-2">
                                        <a href="<?= base_url('order/create?service=' . $service['id']) ?>" 
                                           class="btn btn-primary">
                                            <i class="bi bi-bag-plus"></i> Pilih Layanan
                                        </a>
                                        <a href="<?= base_url('services/' . $service['id']) ?>" 
                                           class="btn btn-outline-secondary btn-sm">
                                            <i class="bi bi-info-circle"></i> Detail
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>

            <div class="text-center">
                <a href="<?= base_url('services') ?>" class="btn btn-outline-primary btn-lg px-5">
                    Lihat Semua Layanan <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- HOW IT WORKS -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title fw-bold">Cara Pesan</h2>
            <p class="text-muted">Mudah dan cepat, hanya 4 langkah!</p>
        </div>

        <div class="row g-4">
            <div class="col-md-3 text-center">
                <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex p-4 mb-3" style="width: 100px; height: 100px;">
                    <h1 class="text-primary fw-bold m-auto">1</h1>
                </div>
                <h5 class="fw-bold">Pesan Online</h5>
                <p class="text-muted">Pilih layanan dan jadwalkan pickup melalui website</p>
            </div>

            <div class="col-md-3 text-center">
                <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex p-4 mb-3" style="width: 100px; height: 100px;">
                    <h1 class="text-primary fw-bold m-auto">2</h1>
                </div>
                <h5 class="fw-bold">Kami Jemput</h5>
                <p class="text-muted">Kurir kami datang ke lokasi Anda sesuai jadwal</p>
            </div>

            <div class="col-md-3 text-center">
                <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex p-4 mb-3" style="width: 100px; height: 100px;">
                    <h1 class="text-primary fw-bold m-auto">3</h1>
                </div>
                <h5 class="fw-bold">Proses Cuci</h5>
                <p class="text-muted">Sepatu dirawat oleh tenaga profesional</p>
            </div>

            <div class="col-md-3 text-center">
                <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex p-4 mb-3" style="width: 100px; height: 100px;">
                    <h1 class="text-primary fw-bold m-auto">4</h1>
                </div>
                <h5 class="fw-bold">Kami Antar</h5>
                <p class="text-muted">Sepatu bersih diantar kembali ke lokasi Anda</p>
            </div>
        </div>
    </div>
</section>

<!-- GALLERY PREVIEW -->
<?php if (!empty($galleries)): ?>
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title fw-bold">Hasil Kerja Kami</h2>
            <p class="text-muted">Lihat transformasi sepatu yang kami tangani</p>
        </div>

        <div class="row g-4">
            <?php foreach ($galleries as $gallery): ?>
                <div class="col-md-4">
                    <div class="card shadow-sm h-100">
                        <div class="row g-0">
                            <div class="col-6 position-relative">
                                <img src="<?= esc($gallery['before_image']) ?>" 
                                     class="img-fluid h-100" 
                                     style="object-fit: cover;" 
                                     alt="Before">
                                <span class="badge bg-dark position-absolute top-0 start-0 m-2">Before</span>
                            </div>
                            <div class="col-6 position-relative">
                                <img src="<?= esc($gallery['after_image']) ?>" 
                                     class="img-fluid h-100" 
                                     style="object-fit: cover;" 
                                     alt="After">
                                <span class="badge bg-success position-absolute top-0 end-0 m-2">After</span>
                            </div>
                        </div>
                        <?php if ($gallery['description']): ?>
                            <div class="card-body">
                                <p class="card-text text-muted small mb-0">
                                    <?= esc($gallery['description']) ?>
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-4">
            <a href="<?= base_url('gallery') ?>" class="btn btn-outline-primary btn-lg px-5">
                Lihat Gallery Lengkap <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CTA SECTION (FINAL) -->
<section class="cta-section py-5 text-white">
    <div class="container text-center py-5">
        <h2 class="display-5 fw-bold mb-4">Siap Memberikan Sepatu Terbaik untuk Anda!</h2>
        <p class="lead mb-5">Pesan sekarang dan rasakan perbedaannya. Gratis jemput dan antar!</p>
        
        <div class="d-flex gap-3 justify-content-center flex-wrap">
            <a href="<?= base_url('order/create') ?>" class="btn btn-light btn-lg px-5 py-3 pulse-button">
                <i class="bi bi-bag-plus"></i> <strong>Pesan Sekarang</strong>
            </a>
            <a href="https://wa.me/6281234567890?text=Halo%20Tapak%20Bersih,%20saya%20ingin%20bertanya" 
               target="_blank" 
               class="btn btn-success btn-lg px-5 py-3">
                <i class="bi bi-whatsapp"></i> <strong>Hubungi Kami</strong>
            </a>
        </div>

        <!-- Trust Badges -->
        <div class="row mt-5 pt-5">
            <div class="col-md-3">
                <h3 class="fw-bold"><?= $stats['satisfaction_rate'] ?></h3>
                <p class="mb-0">Kepuasan Pelanggan</p>
            </div>
            <div class="col-md-3">
                <h3 class="fw-bold"><?= $stats['shoes_cleaned'] ?></h3>
                <p class="mb-0">Sepatu Telah Dirawat</p>
            </div>
            <div class="col-md-3">
                <h3 class="fw-bold"><?= $stats['total_customers'] ?></h3>
                <p class="mb-0">Pelanggan Setia</p>
            </div>
            <div class="col-md-3">
                <h3 class="fw-bold"><?= $stats['branches'] ?></h3>
                <p class="mb-0">Cabang Aktif</p>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
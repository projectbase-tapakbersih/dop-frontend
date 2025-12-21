<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php if ($error): ?>
    <!-- Error State -->
    <section class="py-5">
        <div class="container">
            <div class="text-center py-5">
                <i class="bi bi-exclamation-triangle display-1 text-danger"></i>
                <h2 class="mt-3"><?= esc($error) ?></h2>
                <a href="<?= base_url('branches') ?>" class="btn btn-primary mt-3">
                    <i class="bi bi-arrow-left"></i> Kembali ke Daftar Cabang
                </a>
            </div>
        </div>
    </section>

<?php elseif ($branch): ?>

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="py-3 bg-light">
        <div class="container">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Beranda</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('branches') ?>">Cabang</a></li>
                <li class="breadcrumb-item active"><?= esc($branch['name']) ?></li>
            </ol>
        </div>
    </nav>

    <!-- Branch Detail -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <!-- Branch Info -->
                <div class="col-lg-8">
                    <!-- Map -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-body p-0">
                            <iframe 
                                width="100%" 
                                height="400" 
                                frameborder="0" 
                                style="border:0; border-radius: 15px 15px 0 0;"
                                src="https://www.google.com/maps?q=<?= $branch['latitude'] ?>,<?= $branch['longitude'] ?>&output=embed"
                                allowfullscreen>
                            </iframe>
                        </div>
                    </div>

                    <!-- Branch Details Card -->
                    <div class="card shadow-sm">
                        <div class="card-body p-4">
                            <!-- Status Badge -->
                            <div class="mb-3">
                                <?php if ($branch['is_active']): ?>
                                    <span class="badge bg-success fs-6">
                                        <i class="bi bi-check-circle"></i> Cabang Buka
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-danger fs-6">
                                        <i class="bi bi-x-circle"></i> Cabang Tutup
                                    </span>
                                <?php endif; ?>
                            </div>

                            <!-- Branch Name -->
                            <h1 class="display-5 fw-bold mb-4">
                                <i class="bi bi-shop text-primary"></i>
                                <?= esc($branch['name']) ?>
                            </h1>

                            <!-- Address -->
                            <div class="mb-4">
                                <h5 class="fw-bold mb-2">
                                    <i class="bi bi-geo-alt-fill text-danger"></i> Alamat
                                </h5>
                                <p class="lead text-muted"><?= esc($branch['address']) ?></p>
                            </div>

                            <!-- Coverage Area -->
                            <div class="mb-4">
                                <h5 class="fw-bold mb-2">
                                    <i class="bi bi-geo text-primary"></i> Area Jangkauan
                                </h5>
                                <p class="text-muted">
                                    Kami melayani pickup dan delivery dalam radius 
                                    <strong class="text-primary"><?= $branch['coverage_radius_km'] ?> km</strong> 
                                    dari lokasi cabang ini.
                                </p>
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle"></i>
                                    <strong>Catatan:</strong> Pastikan lokasi Anda berada dalam area jangkauan untuk mendapatkan layanan gratis pickup & delivery.
                                </div>
                            </div>

                            <!-- Coordinates -->
                            <div class="mb-4">
                                <h5 class="fw-bold mb-2">
                                    <i class="bi bi-pin-map"></i> Koordinat
                                </h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <small class="text-muted">Latitude</small>
                                        <p class="fw-bold"><?= $branch['latitude'] ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted">Longitude</small>
                                        <p class="fw-bold"><?= $branch['longitude'] ?></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Facilities -->
                            <div class="mb-4">
                                <h5 class="fw-bold mb-3">
                                    <i class="bi bi-star-fill text-warning"></i> Fasilitas
                                </h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                                            <span>Pickup & Delivery</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                                            <span>Express Service</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                                            <span>Parkir Luas</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                                            <span>Ruang Tunggu Nyaman</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Operating Hours -->
                            <div class="mb-4">
                                <h5 class="fw-bold mb-2">
                                    <i class="bi bi-clock-fill text-primary"></i> Jam Operasional
                                </h5>
                                <p class="text-muted mb-0">Senin - Jumat: 08:00 - 20:00</p>
                                <p class="text-muted mb-0">Sabtu - Minggu: 09:00 - 18:00</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Contact Card -->
                    <div class="card shadow-sm sticky-top" style="top: 20px;">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-3">Hubungi Cabang Ini</h5>
                            
                            <div class="d-grid gap-2">
                                <a href="https://wa.me/6281234567890?text=Halo,%20saya%20ingin%20bertanya%20tentang%20layanan%20di%20<?= urlencode($branch['name']) ?>" 
                                   target="_blank" 
                                   class="btn btn-success btn-lg">
                                    <i class="bi bi-whatsapp"></i> WhatsApp
                                </a>
                                
                                <a href="tel:+6281234567890" class="btn btn-outline-primary">
                                    <i class="bi bi-telephone"></i> Telepon
                                </a>
                                
                                <a href="https://www.google.com/maps/dir/?api=1&destination=<?= $branch['latitude'] ?>,<?= $branch['longitude'] ?>" 
                                   target="_blank" 
                                   class="btn btn-outline-primary">
                                    <i class="bi bi-compass"></i> Buka di Google Maps
                                </a>

                                <?php if ($branch['is_active']): ?>
                                    <hr>
                                    <a href="<?= base_url('order/create?branch=' . $branch['id']) ?>" 
                                       class="btn btn-primary btn-lg">
                                        <i class="bi bi-bag-plus"></i> Pesan dari Cabang Ini
                                    </a>
                                <?php endif; ?>
                            </div>

                            <hr>

                            <!-- Quick Info -->
                            <div class="small text-muted">
                                <p class="mb-2">
                                    <i class="bi bi-info-circle"></i>
                                    <strong>Info Penting:</strong>
                                </p>
                                <ul class="list-unstyled ps-3">
                                    <li class="mb-1">• Gratis pickup & delivery</li>
                                    <li class="mb-1">• Area coverage <?= $branch['coverage_radius_km'] ?> km</li>
                                    <li class="mb-1">• Buka setiap hari</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Other Branches -->
                    <div class="card shadow-sm mt-3">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3">Cabang Lainnya</h6>
                            <a href="<?= base_url('branches') ?>" class="btn btn-outline-primary btn-sm w-100">
                                <i class="bi bi-list"></i> Lihat Semua Cabang
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php endif; ?>

<?= $this->endSection() ?>
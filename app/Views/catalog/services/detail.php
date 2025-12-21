<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php if ($error): ?>
    <!-- Error State -->
    <section class="py-5">
        <div class="container">
            <div class="text-center py-5">
                <i class="bi bi-exclamation-triangle display-1 text-danger"></i>
                <h2 class="mt-3"><?= esc($error) ?></h2>
                <a href="<?= base_url('services') ?>" class="btn btn-primary mt-3">
                    <i class="bi bi-arrow-left"></i> Kembali ke Katalog
                </a>
            </div>
        </div>
    </section>

<?php elseif ($service): ?>

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="py-3 bg-light">
        <div class="container">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Beranda</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('services') ?>">Layanan</a></li>
                <li class="breadcrumb-item active"><?= esc($service['name']) ?></li>
            </ol>
        </div>
    </nav>

    <!-- Service Detail -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <!-- Service Info -->
                <div class="col-lg-8">
                    <div class="card shadow-sm mb-4">
                        <div class="card-body p-4">
                            <!-- Status Badge -->
                            <div class="mb-3">
                                <?php if ($service['is_active']): ?>
                                    <span class="badge bg-success fs-6">
                                        <i class="bi bi-check-circle"></i> Tersedia
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary fs-6">
                                        <i class="bi bi-x-circle"></i> Tidak Tersedia
                                    </span>
                                <?php endif; ?>
                            </div>

                            <!-- Service Name -->
                            <h1 class="display-5 fw-bold mb-3"><?= esc($service['name']) ?></h1>
                            
                            <!-- Service Description -->
                            <p class="lead text-muted mb-4"><?= esc($service['description']) ?></p>
                            
                            <!-- Service Details -->
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <div class="p-3 bg-light rounded">
                                        <i class="bi bi-tag-fill text-primary fs-4"></i>
                                        <div class="mt-2">
                                            <small class="text-muted d-block">Harga</small>
                                            <h4 class="mb-0 fw-bold text-primary"><?= format_rupiah($service['price']) ?></h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-3 bg-light rounded">
                                        <i class="bi bi-clock-fill text-primary fs-4"></i>
                                        <div class="mt-2">
                                            <small class="text-muted d-block">Estimasi Waktu</small>
                                            <h4 class="mb-0 fw-bold"><?= $service['duration_hours'] ?> Jam</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- What's Included -->
                            <div class="mb-4">
                                <h4 class="fw-bold mb-3">Yang Anda Dapatkan:</h4>
                                <ul class="list-unstyled">
                                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Pembersihan menyeluruh</li>
                                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Perawatan material sepatu</li>
                                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Pickup & delivery gratis</li>
                                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Garansi kepuasan</li>
                                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Plastik packaging</li>
                                </ul>
                            </div>

                            <!-- Process Steps -->
                            <div class="mb-4">
                                <h4 class="fw-bold mb-3">Proses Layanan:</h4>
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex p-3 mb-2">
                                                <i class="bi bi-1-circle-fill text-primary fs-3"></i>
                                            </div>
                                            <p class="small mb-0">Pesan Online</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex p-3 mb-2">
                                                <i class="bi bi-2-circle-fill text-primary fs-3"></i>
                                            </div>
                                            <p class="small mb-0">Pickup Sepatu</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex p-3 mb-2">
                                                <i class="bi bi-3-circle-fill text-primary fs-3"></i>
                                            </div>
                                            <p class="small mb-0">Proses Cuci</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex p-3 mb-2">
                                                <i class="bi bi-4-circle-fill text-primary fs-3"></i>
                                            </div>
                                            <p class="small mb-0">Delivery</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Gallery Section -->
                    <?php if (!empty($gallery)): ?>
                        <div class="card shadow-sm">
                            <div class="card-header bg-white">
                                <h4 class="mb-0 fw-bold">Hasil Kerja Kami</h4>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <?php foreach ($gallery as $item): ?>
                                        <div class="col-md-6">
                                            <div class="card">
                                                <div class="row g-0">
                                                    <div class="col-6 position-relative">
                                                        <img src="<?= esc($item['before_image']) ?>" class="img-fluid h-100 object-fit-cover" alt="Before">
                                                        <span class="badge bg-dark position-absolute top-0 start-0 m-2">Before</span>
                                                    </div>
                                                    <div class="col-6 position-relative">
                                                        <img src="<?= esc($item['after_image']) ?>" class="img-fluid h-100 object-fit-cover" alt="After">
                                                        <span class="badge bg-success position-absolute top-0 end-0 m-2">After</span>
                                                    </div>
                                                </div>
                                                <?php if ($item['description']): ?>
                                                    <div class="card-body">
                                                        <p class="card-text small text-muted mb-0">
                                                            <?= esc($item['description']) ?>
                                                        </p>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Order Card -->
                    <div class="card shadow-sm sticky-top" style="top: 20px;">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-3">Pesan Layanan Ini</h5>
                            
                            <div class="d-grid gap-2">
                                <?php if ($service['is_active']): ?>
                                    <a href="<?= base_url('order/create?service=' . $service['id']) ?>" class="btn btn-primary btn-lg">
                                        <i class="bi bi-bag-plus"></i> Pesan Sekarang
                                    </a>
                                <?php else: ?>
                                    <button class="btn btn-secondary btn-lg" disabled>
                                        <i class="bi bi-x-circle"></i> Tidak Tersedia
                                    </button>
                                <?php endif; ?>
                                
                                <a href="https://wa.me/6281234567890?text=Halo, saya tertarik dengan layanan <?= urlencode($service['name']) ?>" 
                                   target="_blank" 
                                   class="btn btn-success btn-lg">
                                    <i class="bi bi-whatsapp"></i> Hubungi via WhatsApp
                                </a>
                            </div>

                            <hr>

                            <!-- Info -->
                            <div class="small text-muted">
                                <p class="mb-2">
                                    <i class="bi bi-info-circle"></i>
                                    <strong>Info:</strong>
                                </p>
                                <ul class="list-unstyled ps-3">
                                    <li class="mb-1">• Pembayaran setelah sepatu selesai</li>
                                    <li class="mb-1">• Gratis pickup & delivery</li>
                                    <li class="mb-1">• Garansi 100% memuaskan</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Share Card -->
                    <div class="card shadow-sm mt-3">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3">Bagikan Layanan Ini</h6>
                            <div class="d-flex gap-2">
                                <a href="#" class="btn btn-outline-primary btn-sm flex-fill">
                                    <i class="bi bi-facebook"></i>
                                </a>
                                <a href="#" class="btn btn-outline-info btn-sm flex-fill">
                                    <i class="bi bi-twitter"></i>
                                </a>
                                <a href="#" class="btn btn-outline-success btn-sm flex-fill">
                                    <i class="bi bi-whatsapp"></i>
                                </a>
                                <button class="btn btn-outline-secondary btn-sm flex-fill" onclick="copyLink()">
                                    <i class="bi bi-link-45deg"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function copyLink() {
    const url = window.location.href;
    navigator.clipboard.writeText(url).then(() => {
        alert('Link berhasil disalin!');
    });
}
</script>
<?= $this->endSection() ?>
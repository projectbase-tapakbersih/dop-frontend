<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<section class="py-5 text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="display-4 fw-bold mb-3">Katalog Layanan</h1>
                <p class="lead mb-0">Pilih layanan terbaik untuk sepatu kesayangan Anda</p>
            </div>
            <div class="col-md-4 text-end">
                <a href="<?= base_url('order/create') ?>" class="btn btn-light btn-lg">
                    <i class="bi bi-bag-plus"></i> Pesan Sekarang
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Services List -->
<section class="py-5">
    <div class="container">
        
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle"></i> <?= esc($error) ?>
            </div>
        <?php endif; ?>
        
        <?php if (empty($services)): ?>
            <div class="text-center py-5">
                <i class="bi bi-inbox display-1 text-muted"></i>
                <h3 class="mt-3">Belum Ada Layanan</h3>
                <p class="text-muted">Silakan cek kembali nanti</p>
            </div>
        <?php else: ?>
            
            <!-- Filter & Sort (Optional) -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <p class="text-muted mb-0">
                        Menampilkan <strong><?= count($services) ?></strong> layanan
                    </p>
                </div>
                <div class="col-md-4">
                    <select class="form-select" id="sortServices">
                        <option value="">Urutkan Berdasarkan</option>
                        <option value="price-asc">Harga: Rendah ke Tinggi</option>
                        <option value="price-desc">Harga: Tinggi ke Rendah</option>
                        <option value="duration-asc">Durasi: Cepat</option>
                        <option value="duration-desc">Durasi: Lama</option>
                    </select>
                </div>
            </div>
            
            <!-- Services Grid -->
            <div class="row g-4" id="servicesGrid">
                <?php foreach ($services as $service): ?>
                    <div class="col-md-6 col-lg-4 service-item" 
                         data-price="<?= $service['price'] ?>" 
                         data-duration="<?= $service['duration_hours'] ?>">
                        <div class="card h-100 shadow-sm">
                            <!-- Service Badge -->
                            <div class="position-absolute top-0 end-0 m-3">
                                <?php if ($service['is_active']): ?>
                                    <span class="badge bg-success">Tersedia</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Tidak Tersedia</span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="card-body">
                                <!-- Service Icon -->
                                <div class="mb-3">
                                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex p-3">
                                        <i class="bi bi-stars fs-3 text-primary"></i>
                                    </div>
                                </div>
                                
                                <!-- Service Name -->
                                <h4 class="card-title fw-bold mb-3">
                                    <?= esc($service['name']) ?>
                                </h4>
                                
                                <!-- Service Description -->
                                <p class="card-text text-muted mb-4">
                                    <?= esc($service['description']) ?>
                                </p>
                                
                                <!-- Service Info -->
                                <div class="mb-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-clock text-primary me-2"></i>
                                        <span>Estimasi: <strong><?= $service['duration_hours'] ?> Jam</strong></span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-tag text-primary me-2"></i>
                                        <span class="price-tag fs-5">
                                            <?= format_rupiah($service['price']) ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Card Footer -->
                            <div class="card-footer bg-transparent border-top-0">
                                <div class="d-grid gap-2">
                                    <a href="<?= base_url('services/' . $service['id']) ?>" class="btn btn-outline-primary">
                                        <i class="bi bi-info-circle"></i> Lihat Detail
                                    </a>
                                    <?php if ($service['is_active']): ?>
                                        <a href="<?= base_url('order/create?service=' . $service['id']) ?>" class="btn btn-primary">
                                            <i class="bi bi-bag-plus"></i> Pesan Layanan Ini
                                        </a>
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

<!-- Why Choose Us -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center section-title mb-5">Mengapa Memilih Tapak Bersih?</h2>
        <div class="row g-4">
            <div class="col-md-3">
                <div class="text-center">
                    <i class="bi bi-award-fill text-primary" style="font-size: 3rem;"></i>
                    <h5 class="mt-3 fw-bold">Profesional</h5>
                    <p class="text-muted">Tenaga ahli berpengalaman</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center">
                    <i class="bi bi-lightning-charge-fill text-primary" style="font-size: 3rem;"></i>
                    <h5 class="mt-3 fw-bold">Cepat</h5>
                    <p class="text-muted">Proses sesuai estimasi</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center">
                    <i class="bi bi-shield-fill-check text-primary" style="font-size: 3rem;"></i>
                    <h5 class="mt-3 fw-bold">Garansi</h5>
                    <p class="text-muted">Jaminan hasil memuaskan</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center">
                    <i class="bi bi-wallet2 text-primary" style="font-size: 3rem;"></i>
                    <h5 class="mt-3 fw-bold">Terjangkau</h5>
                    <p class="text-muted">Harga kompetitif</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Sort Services
document.getElementById('sortServices').addEventListener('change', function() {
    const sortValue = this.value;
    const grid = document.getElementById('servicesGrid');
    const items = Array.from(grid.getElementsByClassName('service-item'));
    
    if (!sortValue) return;
    
    items.sort((a, b) => {
        const priceA = parseFloat(a.dataset.price);
        const priceB = parseFloat(b.dataset.price);
        const durationA = parseFloat(a.dataset.duration);
        const durationB = parseFloat(b.dataset.duration);
        
        switch(sortValue) {
            case 'price-asc':
                return priceA - priceB;
            case 'price-desc':
                return priceB - priceA;
            case 'duration-asc':
                return durationA - durationB;
            case 'duration-desc':
                return durationB - durationA;
            default:
                return 0;
        }
    });
    
    // Re-append sorted items
    items.forEach(item => grid.appendChild(item));
});
</script>
<?= $this->endSection() ?>
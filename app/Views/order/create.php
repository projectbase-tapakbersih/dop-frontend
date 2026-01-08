<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
    .step-indicator {
        display: flex;
        justify-content: space-between;
        margin-bottom: 30px;
    }
    .step {
        flex: 1;
        text-align: center;
        padding: 15px;
        position: relative;
    }
    .step::before {
        content: '';
        position: absolute;
        top: 25px;
        left: 0;
        right: 0;
        height: 3px;
        background: #e0e0e0;
        z-index: -1;
    }
    .step:first-child::before {
        left: 50%;
    }
    .step:last-child::before {
        right: 50%;
    }
    .step-number {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: #e0e0e0;
        color: #999;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        margin-bottom: 10px;
    }
    .step.active .step-number {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    .step.completed .step-number {
        background: #28a745;
        color: white;
    }
    .service-option, .branch-option {
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        padding: 15px;
        cursor: pointer;
        transition: all 0.3s;
    }
    .service-option:hover, .branch-option:hover {
        border-color: #667eea;
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.2);
    }
    .service-option.selected, .branch-option.selected {
        border-color: #667eea;
        background: #f8f9ff;
    }
    .order-summary {
        position: sticky;
        top: 20px;
    }
    .plastic-bag-warning {
        border: 2px dashed #ffc107;
        background: #fff3cd;
        padding: 15px;
        border-radius: 10px;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<section class="py-4 bg-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-1 fw-bold">Buat Pesanan</h2>
                <p class="text-muted mb-0">Isi form di bawah untuk memesan layanan perawatan sepatu</p>
            </div>
            <a href="<?= base_url('/') ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
</section>

<!-- Order Form -->
<section class="py-5">
    <div class="container">
        
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle"></i> <?= esc($error) ?>
            </div>
        <?php endif; ?>

        <!-- Guest Login Prompt -->
        <?php if (!$user): ?>
            <div class="alert alert-info">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <i class="bi bi-info-circle"></i>
                        <strong>Sudah punya akun?</strong> Login untuk checkout lebih cepat
                    </div>
                    <div>
                        <a href="<?= base_url('auth/login') ?>" class="btn btn-sm btn-primary me-2">Login</a>
                        <a href="<?= base_url('auth/register') ?>" class="btn btn-sm btn-outline-primary">Daftar</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- Main Form -->
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        
                        <!-- Step Indicator -->
                        <div class="step-indicator mb-4">
                            <div class="step active" id="step1">
                                <div class="step-number">1</div>
                                <div>Pilih Layanan</div>
                            </div>
                            <div class="step" id="step2">
                                <div class="step-number">2</div>
                                <div>Detail Sepatu</div>
                            </div>
                            <div class="step" id="step3">
                                <div class="step-number">3</div>
                                <div>Penjemputan</div>
                            </div>
                            <div class="step" id="step4">
                                <div class="step-number">4</div>
                                <div>Konfirmasi</div>
                            </div>
                        </div>

                        <form id="orderForm">
                            <?= csrf_field() ?>

                            <!-- STEP 1: Pilih Layanan -->
                            <div class="form-step" id="formStep1">
                                <h4 class="fw-bold mb-4">
                                    <i class="bi bi-stars text-primary"></i> Pilih Layanan
                                </h4>

                                <?php if (empty($services)): ?>
                                    <div class="alert alert-warning">
                                        Tidak ada layanan tersedia saat ini.
                                    </div>
                                <?php else: ?>
                                    <div class="row g-3">
                                        <?php foreach ($services as $service): ?>
                                            <div class="col-md-6">
                                                <div class="service-option <?= ($selected_service && $selected_service['id'] == $service['id']) ? 'selected' : '' ?>" 
                                                     data-service-id="<?= $service['id'] ?>"
                                                     data-service-name="<?= esc($service['name']) ?>"
                                                     data-service-price="<?= $service['price'] ?>"
                                                     data-service-duration="<?= $service['duration_hours'] ?>">
                                                    <div class="form-check">
                                                        <input class="form-check-input" 
                                                               type="radio" 
                                                               name="service_id" 
                                                               value="<?= $service['id'] ?>"
                                                               <?= ($selected_service && $selected_service['id'] == $service['id']) ? 'checked' : '' ?>
                                                               required>
                                                        <label class="form-check-label w-100">
                                                            <strong class="d-block"><?= esc($service['name']) ?></strong>
                                                            <small class="text-muted d-block mb-2">
                                                                <?= esc($service['description']) ?>
                                                            </small>
                                                            <div class="d-flex justify-content-between">
                                                                <span class="badge bg-primary"><?= format_rupiah($service['price']) ?></span>
                                                                <span class="badge bg-secondary"><?= $service['duration_hours'] ?> Jam</span>
                                                            </div>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

                                <div class="mt-4">
                                    <button type="button" class="btn btn-primary" onclick="nextStep(2)">
                                        Lanjutkan <i class="bi bi-arrow-right"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- STEP 2: Detail Sepatu -->
                            <div class="form-step d-none" id="formStep2">
                                <h4 class="fw-bold mb-4">
                                    <i class="bi bi-shoe text-primary"></i> Detail Sepatu
                                </h4>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Jenis Sepatu *</label>
                                        <select class="form-select" name="shoe_type" id="shoe_type" required>
                                            <option value="">Pilih Jenis Sepatu</option>
                                            <option value="Sneakers">Sneakers</option>
                                            <option value="Boots">Boots</option>
                                            <option value="Formal">Formal</option>
                                            <option value="Casual">Casual</option>
                                            <option value="Sport">Sport</option>
                                            <option value="Sandals">Sandal</option>
                                            <option value="Other">Lainnya</option>
                                        </select>
                                        <div class="invalid-feedback" id="error-shoe_type"></div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Ukuran Sepatu</label>
                                        <input type="text" 
                                               class="form-control" 
                                               name="shoe_size" 
                                               id="shoe_size" 
                                               placeholder="Contoh: 42">
                                        <small class="text-muted">Opsional</small>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label fw-bold">Catatan Kondisi Sepatu</label>
                                        <textarea class="form-control" 
                                                  name="special_notes" 
                                                  id="special_notes" 
                                                  rows="4"
                                                  placeholder="Contoh: Ada noda membandel di bagian samping, sol agak lepas, dll."></textarea>
                                        <small class="text-muted">Jelaskan kondisi sepatu untuk penanganan yang lebih baik</small>
                                    </div>
                                </div>

                                <div class="mt-4 d-flex gap-2">
                                    <button type="button" class="btn btn-outline-secondary" onclick="prevStep(1)">
                                        <i class="bi bi-arrow-left"></i> Kembali
                                    </button>
                                    <button type="button" class="btn btn-primary" onclick="nextStep(3)">
                                        Lanjutkan <i class="bi bi-arrow-right"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- STEP 3: Lokasi Penjemputan -->
                            <div class="form-step d-none" id="formStep3">
                                <h4 class="fw-bold mb-4">
                                    <i class="bi bi-geo-alt text-primary"></i> Lokasi Penjemputan
                                </h4>

                                <!-- Branch Selection -->
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Pilih Cabang Terdekat *</label>
                                    <?php if (empty($branches)): ?>
                                        <div class="alert alert-warning">Tidak ada cabang aktif saat ini.</div>
                                    <?php else: ?>
                                        <div class="row g-3">
                                            <?php foreach ($branches as $branch): ?>
                                                <div class="col-md-6">
                                                    <div class="branch-option <?= ($selected_branch && $selected_branch['id'] == $branch['id']) ? 'selected' : '' ?>"
                                                         data-branch-id="<?= $branch['id'] ?>">
                                                        <div class="form-check">
                                                            <input class="form-check-input" 
                                                                   type="radio" 
                                                                   name="branch_id" 
                                                                   value="<?= $branch['id'] ?>"
                                                                   <?= ($selected_branch && $selected_branch['id'] == $branch['id']) ? 'checked' : '' ?>
                                                                   required>
                                                            <label class="form-check-label w-100">
                                                                <strong class="d-block"><?= esc($branch['name']) ?></strong>
                                                                <small class="text-muted d-block">
                                                                    <i class="bi bi-pin-map"></i> <?= esc($branch['address']) ?>
                                                                </small>
                                                                <small class="badge bg-info mt-1">
                                                                    Radius <?= $branch['coverage_radius_km'] ?> km
                                                                </small>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="invalid-feedback" id="error-branch_id"></div>
                                </div>

                                <!-- Pickup Address -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Alamat Lengkap Penjemputan *</label>
                                    <textarea class="form-control" 
                                              name="pickup_address" 
                                              id="pickup_address" 
                                              rows="3"
                                              placeholder="Contoh: Jl. Raya Darmo No. 123, Surabaya"
                                              minlength="10"
                                              required></textarea>
                                    <small class="text-muted">Alamat lengkap untuk kurir menjemput sepatu Anda (minimal 10 karakter)</small>
                                    <div class="invalid-feedback" id="error-pickup_address"></div>
                                </div>

                                <!-- Pickup Date & Time -->
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Tanggal Penjemputan *</label>
                                        <input type="date" 
                                               class="form-control" 
                                               name="pickup_date" 
                                               id="pickup_date"
                                               min="<?= date('Y-m-d') ?>"
                                               required>
                                        <div class="invalid-feedback" id="error-pickup_date"></div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Waktu Penjemputan *</label>
                                        <input type="time" 
                                               class="form-control" 
                                               name="pickup_time" 
                                               id="pickup_time"
                                               required>
                                        <small class="text-muted">Jam operasional: 08:00 - 20:00</small>
                                        <div class="invalid-feedback" id="error-pickup_time"></div>
                                    </div>
                                </div>

                                <div class="mt-4 d-flex gap-2">
                                    <button type="button" class="btn btn-outline-secondary" onclick="prevStep(2)">
                                        <i class="bi bi-arrow-left"></i> Kembali
                                    </button>
                                    <button type="button" class="btn btn-primary" onclick="nextStep(4)">
                                        Lanjutkan <i class="bi bi-arrow-right"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- STEP 4: Konfirmasi -->
                            <div class="form-step d-none" id="formStep4">
                                <h4 class="fw-bold mb-4">
                                    <i class="bi bi-check-circle text-primary"></i> Konfirmasi & Pembayaran
                                </h4>

                                <!-- Contact Info (ALWAYS SHOW - required by API) -->
                                <div class="card bg-light mb-4">
                                    <div class="card-body">
                                        <h6 class="fw-bold mb-3">
                                            <i class="bi bi-person text-primary"></i> Informasi Pemesan
                                        </h6>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Nama Lengkap *</label>
                                                <input type="text" 
                                                       class="form-control" 
                                                       name="name" 
                                                       id="guest_name" 
                                                       placeholder="Masukkan nama lengkap"
                                                       value="<?= $user && isset($user['name']) ? esc($user['name']) : '' ?>"
                                                       required>
                                                <div class="invalid-feedback" id="error-name"></div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Nomor HP (WhatsApp) *</label>
                                                <input type="tel" 
                                                       class="form-control" 
                                                       name="phone" 
                                                       id="guest_phone" 
                                                       placeholder="08xxxxxxxxxx"
                                                       value="<?= $user && isset($user['phone']) ? esc($user['phone']) : '' ?>"
                                                       required>
                                                <div class="invalid-feedback" id="error-phone"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Plastic Bag Confirmation (WAJIB) -->
                                <div class="plastic-bag-warning mb-4">
                                    <h6 class="fw-bold mb-3">
                                        <i class="bi bi-exclamation-triangle text-warning"></i>
                                        Konfirmasi Penggunaan Kantong Plastik
                                    </h6>
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               name="plastic_bag_confirmed" 
                                               id="plastic_bag_confirmed"
                                               value="true"
                                               required>
                                        <label class="form-check-label" for="plastic_bag_confirmed">
                                            <strong>Saya mengerti dan setuju</strong> bahwa sepatu saya akan dikemas menggunakan kantong plastik untuk melindungi dari debu dan kotoran selama proses pengiriman. *
                                        </label>
                                    </div>
                                    <small class="text-muted d-block mt-2">
                                        <i class="bi bi-info-circle"></i> Kantong plastik diperlukan untuk menjaga kebersihan sepatu setelah dicuci.
                                    </small>
                                    <div class="invalid-feedback" id="error-plastic_bag_confirmed"></div>
                                </div>

                                <!-- Payment Method -->
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Metode Pembayaran *</label>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="form-check border rounded p-3">
                                                <input class="form-check-input" 
                                                       type="radio" 
                                                       name="payment_method" 
                                                       value="qris" 
                                                       id="payment_qris"
                                                       required>
                                                <label class="form-check-label w-100" for="payment_qris">
                                                    <strong>QRIS</strong>
                                                    <small class="d-block text-muted">Scan & Pay (Semua e-wallet)</small>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check border rounded p-3">
                                                <input class="form-check-input" 
                                                       type="radio" 
                                                       name="payment_method" 
                                                       value="transfer" 
                                                       id="payment_transfer"
                                                       required>
                                                <label class="form-check-label w-100" for="payment_transfer">
                                                    <strong>Transfer Bank</strong>
                                                    <small class="d-block text-muted">BCA, Mandiri, BNI, BRI</small>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="invalid-feedback" id="error-payment_method"></div>
                                </div>

                                <!-- Terms -->
                                <div class="alert alert-info">
                                    <small>
                                        <i class="bi bi-info-circle"></i>
                                        Dengan melanjutkan, Anda menyetujui <a href="#">Syarat & Ketentuan</a> kami.
                                    </small>
                                </div>

                                <div class="mt-4 d-flex gap-2">
                                    <button type="button" class="btn btn-outline-secondary" onclick="prevStep(3)">
                                        <i class="bi bi-arrow-left"></i> Kembali
                                    </button>
                                    <button type="submit" class="btn btn-success btn-lg" id="btnSubmit">
                                        <i class="bi bi-check-circle"></i>
                                        <span id="btnText">Buat Pesanan</span>
                                        <span id="btnLoading" class="d-none">
                                            <span class="spinner-border spinner-border-sm me-2"></span>Memproses...
                                        </span>
                                    </button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>

            <!-- Order Summary Sidebar -->
            <div class="col-lg-4">
                <div class="card shadow-sm order-summary">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-receipt"></i> Ringkasan Pesanan
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Service Summary -->
                        <div id="summaryService" class="mb-3">
                            <small class="text-muted d-block">Layanan</small>
                            <strong id="selectedServiceName"><?= $selected_service ? esc($selected_service['name']) : '-' ?></strong>
                            <div class="mt-1">
                                <span class="badge bg-secondary" id="selectedServiceDuration">
                                    <?= $selected_service ? $selected_service['duration_hours'] . ' Jam' : '- Jam' ?>
                                </span>
                            </div>
                        </div>

                        <hr>

                        <!-- Shoe Details -->
                        <div id="summaryShoe" class="mb-3">
                            <small class="text-muted d-block">Detail Sepatu</small>
                            <div id="shoeDetails" class="text-muted small">Belum diisi</div>
                        </div>

                        <hr>

                        <!-- Pickup Info -->
                        <div id="summaryPickup" class="mb-3">
                            <small class="text-muted d-block">Penjemputan</small>
                            <div id="pickupDetails" class="text-muted small">Belum diisi</div>
                        </div>

                        <hr>

                        <!-- Price -->
                        <div class="d-flex justify-content-between align-items-center">
                            <strong>Total Biaya</strong>
                            <h4 class="text-primary mb-0 fw-bold" id="totalPrice">
                                <?= $selected_service ? format_rupiah($selected_service['price']) : 'Rp 0' ?>
                            </h4>
                        </div>

                        <small class="text-muted d-block mt-2">
                            <i class="bi bi-info-circle"></i> Harga sudah termasuk pickup & delivery
                        </small>
                    </div>
                </div>

                <!-- Help Box -->
                <div class="card shadow-sm mt-3">
                    <div class="card-body text-center">
                        <i class="bi bi-whatsapp fs-1 text-success mb-2"></i>
                        <h6 class="fw-bold">Butuh Bantuan?</h6>
                        <p class="small text-muted mb-3">Hubungi kami via WhatsApp</p>
                        <a href="https://wa.me/6281234567890?text=Halo%20Tapak%20Bersih,%20saya%20butuh%20bantuan%20untuk%20pemesanan" 
                           target="_blank" 
                           class="btn btn-success btn-sm">
                            <i class="bi bi-whatsapp"></i> Chat Sekarang
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Pass PHP variables to JavaScript -->
<script>
    // User login status
    const USER_LOGGED_IN = <?= $user ? 'true' : 'false' ?>;
    const USER_DATA = <?= $user ? json_encode($user) : 'null' ?>;
</script>

<!-- Order Form Script -->
<script src="<?= base_url('assets/js/order.js') ?>"></script>
<?= $this->endSection() ?>
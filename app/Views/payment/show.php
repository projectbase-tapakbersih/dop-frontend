<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
    .payment-method {
        border: 2px solid #e0e0e0;
        border-radius: 15px;
        padding: 20px;
        cursor: pointer;
        transition: all 0.3s ease;
        height: 100%;
    }
    .payment-method:hover {
        border-color: #667eea;
        box-shadow: 0 5px 20px rgba(102, 126, 234, 0.2);
    }
    .payment-method.selected {
        border-color: #667eea;
        background: #f8f9ff;
    }
    .payment-method input[type="radio"] {
        display: none;
    }
    .qr-code-container {
        background: white;
        padding: 20px;
        border-radius: 15px;
        text-align: center;
    }
    .bank-account-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 15px;
    }
    .countdown-timer {
        background: #fff3cd;
        border: 2px solid #ffc107;
        border-radius: 10px;
        padding: 15px;
        text-align: center;
    }
    .timer-value {
        font-size: 2rem;
        font-weight: bold;
        color: #dc3545;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php 
// Debug - Uncomment untuk melihat struktur data
// echo '<pre>' . print_r($order, true) . '</pre>'; 

// Extract order data dengan multiple fallbacks
$orderNumber = $order['order_number'] ?? $order['id'] ?? 'N/A';

// Get total price - coba berbagai kemungkinan struktur
$totalPrice = $order['total_price'] 
    ?? $order['total'] 
    ?? $order['total_amount']
    ?? $order['grand_total']
    ?? 0;

// Jika total masih 0, coba hitung dari items
if ($totalPrice == 0 && isset($order['items']) && is_array($order['items'])) {
    foreach ($order['items'] as $item) {
        $totalPrice += ($item['price'] ?? 0) * ($item['quantity'] ?? 1);
    }
}

// Jika masih 0, coba dari service price
if ($totalPrice == 0) {
    $totalPrice = $order['service']['price'] 
        ?? $order['service_price'] 
        ?? $order['items'][0]['service']['price'] 
        ?? $order['items'][0]['price']
        ?? 0;
}

// Get service name
$serviceName = $order['service']['name'] 
    ?? $order['service_name'] 
    ?? $order['items'][0]['service']['name'] 
    ?? $order['items'][0]['service_name']
    ?? '-';

// Get branch name
$branchName = $order['branch']['name'] 
    ?? $order['branch_name'] 
    ?? '-';
?>

<?php if (!empty($error)): ?>
    <!-- Error State -->
    <section class="py-5">
        <div class="container">
            <div class="text-center py-5">
                <i class="bi bi-exclamation-triangle display-1 text-danger"></i>
                <h2 class="mt-3"><?= esc($error) ?></h2>
                <a href="<?= base_url('/') ?>" class="btn btn-primary mt-3">
                    <i class="bi bi-house"></i> Kembali ke Beranda
                </a>
            </div>
        </div>
    </section>

<?php elseif (!empty($order)): ?>

    <!-- Page Header -->
    <section class="py-4 bg-light">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1 fw-bold">
                        <i class="bi bi-credit-card"></i> Pembayaran
                    </h2>
                    <p class="text-muted mb-0">Pesanan: <?= esc($orderNumber) ?></p>
                </div>
                <span class="badge bg-warning text-dark px-3 py-2">
                    <i class="bi bi-clock"></i> Menunggu Pembayaran
                </span>
            </div>
        </div>
    </section>

    <!-- Payment Content -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <!-- Payment Methods -->
                <div class="col-lg-8 mb-4">
                    
                    <!-- Countdown Timer -->
                    <div class="countdown-timer mb-4">
                        <p class="mb-2 fw-bold">Selesaikan pembayaran dalam:</p>
                        <div class="timer-value" id="countdown">24:00:00</div>
                        <small class="text-muted">Pesanan akan dibatalkan otomatis jika tidak dibayar</small>
                    </div>

                    <!-- Choose Payment Method -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0 fw-bold">
                                <i class="bi bi-wallet2"></i> Pilih Metode Pembayaran
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3" id="paymentMethods">
                                
                                <!-- QRIS -->
                                <div class="col-md-6">
                                    <div class="payment-method" data-method="qris" onclick="selectPaymentMethod('qris')">
                                        <input type="radio" name="payment_method_select" value="qris" id="qris_select">
                                        <label class="w-100" for="qris_select">
                                            <div class="text-center mb-3">
                                                <i class="bi bi-qr-code display-4 text-primary"></i>
                                            </div>
                                            <h5 class="fw-bold text-center">QRIS</h5>
                                            <p class="text-muted text-center mb-0 small">
                                                Scan & Pay dengan semua e-wallet
                                            </p>
                                            <div class="mt-3 d-flex justify-content-center gap-2 flex-wrap">
                                                <span class="badge bg-light text-dark border">GoPay</span>
                                                <span class="badge bg-light text-dark border">OVO</span>
                                                <span class="badge bg-light text-dark border">Dana</span>
                                                <span class="badge bg-light text-dark border">ShopeePay</span>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <!-- Bank Transfer -->
                                <div class="col-md-6">
                                    <div class="payment-method" data-method="bank_transfer" onclick="selectPaymentMethod('bank_transfer')">
                                        <input type="radio" name="payment_method_select" value="bank_transfer" id="bank_transfer_select">
                                        <label class="w-100" for="bank_transfer_select">
                                            <div class="text-center mb-3">
                                                <i class="bi bi-bank display-4 text-success"></i>
                                            </div>
                                            <h5 class="fw-bold text-center">Transfer Bank</h5>
                                            <p class="text-muted text-center mb-0 small">
                                                Transfer ke rekening bank kami
                                            </p>
                                            <div class="mt-3 d-flex justify-content-center gap-2 flex-wrap">
                                                <span class="badge bg-light text-dark border">BCA</span>
                                                <span class="badge bg-light text-dark border">Mandiri</span>
                                                <span class="badge bg-light text-dark border">BNI</span>
                                                <span class="badge bg-light text-dark border">BRI</span>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- QRIS Payment Details (Hidden by default) -->
                    <div class="card shadow-sm mb-4 d-none" id="qrisDetails">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-qr-code"></i> Pembayaran QRIS
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="qr-code-container">
                                <!-- QR Code -->
                                <div class="mb-3">
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=<?= urlencode('QRIS-TapakBersih-' . $orderNumber . '-' . $totalPrice) ?>" 
                                         alt="QR Code" 
                                         class="img-fluid"
                                         style="max-width: 300px;">
                                </div>
                                <h4 class="fw-bold mb-3">Scan QR Code</h4>
                                <p class="text-muted mb-4">
                                    Buka aplikasi e-wallet favorit Anda dan scan QR code di atas
                                </p>
                                <div class="alert alert-info">
                                    <strong>Total Pembayaran:</strong><br>
                                    <h3 class="text-primary mb-0"><?= format_rupiah($totalPrice) ?></h3>
                                </div>
                            </div>

                            <hr>

                            <h6 class="fw-bold mb-3">Cara Pembayaran:</h6>
                            <ol class="text-muted">
                                <li>Buka aplikasi e-wallet (GoPay, OVO, Dana, ShopeePay, dll)</li>
                                <li>Pilih menu "Scan QR" atau "Bayar"</li>
                                <li>Scan QR code di atas</li>
                                <li>Konfirmasi pembayaran</li>
                                <li>Simpan bukti pembayaran</li>
                            </ol>

                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle"></i>
                                <strong>Penting:</strong> Setelah pembayaran berhasil, upload bukti pembayaran di bawah ini
                            </div>

                            <!-- Upload Proof QRIS -->
                            <div class="mt-4">
                                <h6 class="fw-bold mb-3">Upload Bukti Pembayaran</h6>
                                <form id="qrisProofForm" enctype="multipart/form-data">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="payment_method" value="qris">
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Bukti Pembayaran (Screenshot) *</label>
                                        <input type="file" 
                                               class="form-control" 
                                               name="payment_proof" 
                                               accept="image/*"
                                               required>
                                        <small class="text-muted">Format: JPG, PNG (Max 2MB)</small>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-success btn-lg w-100">
                                        <i class="bi bi-check-circle"></i> Konfirmasi Pembayaran QRIS
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Bank Transfer Details (Hidden by default) -->
                    <div class="card shadow-sm mb-4 d-none" id="bankTransferDetails">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-bank"></i> Transfer Bank
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            
                            <h6 class="fw-bold mb-3">Pilih Bank Tujuan:</h6>

                            <!-- BCA -->
                            <div class="bank-account-card">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h5 class="mb-0 fw-bold">Bank BCA</h5>
                                    <button type="button" class="btn btn-light btn-sm" onclick="copyText('1234567890')">
                                        <i class="bi bi-clipboard"></i> Copy
                                    </button>
                                </div>
                                <p class="mb-1">No. Rekening</p>
                                <h4 class="fw-bold mb-2">1234 5678 90</h4>
                                <p class="mb-0">a.n. <strong>PT Tapak Bersih Indonesia</strong></p>
                            </div>

                            <!-- Mandiri -->
                            <div class="bank-account-card">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h5 class="mb-0 fw-bold">Bank Mandiri</h5>
                                    <button type="button" class="btn btn-light btn-sm" onclick="copyText('0987654321')">
                                        <i class="bi bi-clipboard"></i> Copy
                                    </button>
                                </div>
                                <p class="mb-1">No. Rekening</p>
                                <h4 class="fw-bold mb-2">0987 6543 21</h4>
                                <p class="mb-0">a.n. <strong>PT Tapak Bersih Indonesia</strong></p>
                            </div>

                            <div class="alert alert-info">
                                <strong>Total Transfer:</strong><br>
                                <h3 class="text-primary mb-0"><?= format_rupiah($totalPrice) ?></h3>
                            </div>

                            <hr>

                            <h6 class="fw-bold mb-3">Cara Pembayaran:</h6>
                            <ol class="text-muted">
                                <li>Transfer sesuai nominal di atas ke salah satu rekening</li>
                                <li>Simpan bukti transfer</li>
                                <li>Upload bukti transfer di bawah ini</li>
                                <li>Tunggu konfirmasi dari kami (maks 1x24 jam)</li>
                            </ol>

                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle"></i>
                                <strong>Penting:</strong> Transfer harus sesuai nominal EXACT untuk verifikasi otomatis
                            </div>

                            <!-- Upload Proof Transfer -->
                            <div class="mt-4">
                                <h6 class="fw-bold mb-3">Upload Bukti Transfer</h6>
                                <form id="transferProofForm" enctype="multipart/form-data">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="payment_method" value="bank_transfer">
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Bank Asal *</label>
                                        <select class="form-select" name="sender_bank" required>
                                            <option value="">Pilih Bank</option>
                                            <option value="BCA">BCA</option>
                                            <option value="Mandiri">Mandiri</option>
                                            <option value="BNI">BNI</option>
                                            <option value="BRI">BRI</option>
                                            <option value="Other">Lainnya</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Nama Pengirim *</label>
                                        <input type="text" 
                                               class="form-control" 
                                               name="sender_name"
                                               placeholder="Nama sesuai rekening"
                                               required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Bukti Transfer *</label>
                                        <input type="file" 
                                               class="form-control" 
                                               name="payment_proof" 
                                               accept="image/*"
                                               required>
                                        <small class="text-muted">Format: JPG, PNG (Max 2MB)</small>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-success btn-lg w-100">
                                        <i class="bi bi-check-circle"></i> Konfirmasi Transfer Bank
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Order Summary Sidebar -->
                <div class="col-lg-4">
                    <div class="card shadow-sm sticky-top" style="top: 20px;">
                        <div class="card-header bg-white">
                            <h5 class="mb-0 fw-bold">
                                <i class="bi bi-receipt"></i> Ringkasan Pesanan
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <small class="text-muted">Order Number</small>
                                <h6 class="fw-bold"><?= esc($orderNumber) ?></h6>
                            </div>

                            <div class="mb-3">
                                <small class="text-muted">Layanan</small>
                                <p class="mb-0 fw-bold"><?= esc($serviceName) ?></p>
                            </div>

                            <div class="mb-3">
                                <small class="text-muted">Cabang</small>
                                <p class="mb-0"><?= esc($branchName) ?></p>
                            </div>

                            <hr>

                            <div class="d-flex justify-content-between mb-2">
                                <span>Biaya Layanan</span>
                                <strong><?= format_rupiah($totalPrice) ?></strong>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span>Pickup & Delivery</span>
                                <strong class="text-success">GRATIS</strong>
                            </div>

                            <hr>

                            <div class="d-flex justify-content-between align-items-center">
                                <strong>Total Bayar</strong>
                                <h4 class="text-primary mb-0 fw-bold">
                                    <?= format_rupiah($totalPrice) ?>
                                </h4>
                            </div>
                        </div>
                    </div>

                    <!-- Help Card -->
                    <div class="card shadow-sm mt-3">
                        <div class="card-body text-center">
                            <i class="bi bi-headset fs-1 text-primary mb-2"></i>
                            <h6 class="fw-bold">Butuh Bantuan?</h6>
                            <p class="small text-muted mb-3">Hubungi customer service kami</p>
                            <a href="https://wa.me/6281234567890?text=Halo,%20saya%20butuh%20bantuan%20untuk%20pembayaran%20<?= urlencode($orderNumber) ?>" 
                               target="_blank" 
                               class="btn btn-success btn-sm w-100">
                                <i class="bi bi-whatsapp"></i> Chat WhatsApp
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php else: ?>
    <!-- No Order State -->
    <section class="py-5">
        <div class="container">
            <div class="text-center py-5">
                <i class="bi bi-inbox display-1 text-muted"></i>
                <h2 class="mt-3">Pesanan Tidak Ditemukan</h2>
                <p class="text-muted">Pesanan yang Anda cari tidak ditemukan atau sudah tidak valid.</p>
                <a href="<?= base_url('/') ?>" class="btn btn-primary mt-3">
                    <i class="bi bi-house"></i> Kembali ke Beranda
                </a>
            </div>
        </div>
    </section>
<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
const BASE_URL = '<?= base_url() ?>';
const ORDER_NUMBER = '<?= $orderNumber ?? '' ?>';
let selectedMethod = null;

// Select payment method - ONLY show ONE at a time
function selectPaymentMethod(method) {
    selectedMethod = method;
    
    // Remove selected from all payment method cards
    document.querySelectorAll('.payment-method').forEach(m => {
        m.classList.remove('selected');
    });
    
    // Add selected to clicked method
    const selectedCard = document.querySelector(`.payment-method[data-method="${method}"]`);
    if (selectedCard) {
        selectedCard.classList.add('selected');
        const radio = selectedCard.querySelector('input[type="radio"]');
        if (radio) radio.checked = true;
    }
    
    // HIDE ALL payment detail cards first
    document.getElementById('qrisDetails').classList.add('d-none');
    document.getElementById('bankTransferDetails').classList.add('d-none');
    
    // Show ONLY the selected method details
    if (method === 'qris') {
        document.getElementById('qrisDetails').classList.remove('d-none');
    } else if (method === 'bank_transfer') {
        document.getElementById('bankTransferDetails').classList.remove('d-none');
    }
    
    // Smooth scroll to the details card
    setTimeout(() => {
        const detailsCard = method === 'qris' 
            ? document.getElementById('qrisDetails')
            : document.getElementById('bankTransferDetails');
        if (detailsCard) {
            detailsCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }, 100);
}

// Copy text function
function copyText(text) {
    navigator.clipboard.writeText(text).then(() => {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Nomor rekening berhasil disalin',
            timer: 1500,
            showConfirmButton: false
        });
    }).catch(() => {
        // Fallback
        const textarea = document.createElement('textarea');
        textarea.value = text;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Nomor rekening berhasil disalin',
            timer: 1500,
            showConfirmButton: false
        });
    });
}

// QRIS form submission
document.getElementById('qrisProofForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    await submitPaymentProof(this);
});

// Transfer form submission
document.getElementById('transferProofForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    await submitPaymentProof(this);
});

async function submitPaymentProof(form) {
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;
    
    // Client-side validation
    const paymentProof = form.querySelector('input[name="payment_proof"]');
    if (!paymentProof || !paymentProof.files || paymentProof.files.length === 0) {
        Swal.fire({
            icon: 'error',
            title: 'Oops!',
            text: 'Silakan upload bukti pembayaran'
        });
        return;
    }
    
    // Check file size (max 2MB)
    if (paymentProof.files[0].size > 2 * 1024 * 1024) {
        Swal.fire({
            icon: 'error',
            title: 'File Terlalu Besar',
            text: 'Ukuran file maksimal 2MB'
        });
        return;
    }
    
    // For bank transfer, validate additional fields
    const paymentMethod = formData.get('payment_method');
    if (paymentMethod === 'bank_transfer') {
        const senderBank = form.querySelector('select[name="sender_bank"]');
        const senderName = form.querySelector('input[name="sender_name"]');
        
        if (!senderBank || !senderBank.value) {
            Swal.fire({
                icon: 'error',
                title: 'Oops!',
                text: 'Silakan pilih bank asal'
            });
            return;
        }
        
        if (!senderName || !senderName.value || senderName.value.length < 3) {
            Swal.fire({
                icon: 'error',
                title: 'Oops!',
                text: 'Nama pengirim wajib diisi (minimal 3 karakter)'
            });
            return;
        }
    }
    
    // Disable button and show loading
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
    
    try {
        const response = await fetch(`${BASE_URL}/payment/${ORDER_NUMBER}/confirm`, {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: result.message || 'Bukti pembayaran berhasil diupload',
                confirmButtonColor: '#667eea'
            }).then(() => {
                if (result.redirect) {
                    window.location.href = result.redirect;
                }
            });
        } else {
            let errorMessage = result.message || 'Gagal mengupload bukti pembayaran';
            
            if (result.errors) {
                const errorMessages = [];
                for (const [field, messages] of Object.entries(result.errors)) {
                    const msg = Array.isArray(messages) ? messages[0] : messages;
                    errorMessages.push(`• ${msg}`);
                }
                errorMessage = errorMessages.join('<br>');
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                html: errorMessage
            });
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Oops!',
            text: 'Terjadi kesalahan. Silakan coba lagi.'
        });
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
    }
}

// Countdown timer
function startCountdown() {
    const endTime = new Date().getTime() + (24 * 60 * 60 * 1000);
    
    const timer = setInterval(() => {
        const now = new Date().getTime();
        const distance = endTime - now;
        
        if (distance < 0) {
            clearInterval(timer);
            document.getElementById('countdown').innerHTML = "EXPIRED";
            Swal.fire({
                icon: 'warning',
                title: 'Waktu Habis',
                text: 'Waktu pembayaran telah habis.',
                confirmButtonColor: '#667eea'
            });
            return;
        }
        
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
        document.getElementById('countdown').innerHTML = 
            `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    }, 1000);
}

// Start countdown on page load
if (document.getElementById('countdown')) {
    startCountdown();
}
</script>
<?= $this->endSection() ?>
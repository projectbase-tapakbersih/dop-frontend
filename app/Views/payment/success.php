<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
<style>
    .success-animation {
        animation: scaleIn 0.5s ease-out, pulse 2s ease-in-out infinite;
    }
    @keyframes scaleIn {
        from {
            transform: scale(0);
            opacity: 0;
        }
        to {
            transform: scale(1);
            opacity: 1;
        }
    }
    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.05);
        }
    }
    .confetti {
        position: fixed;
        width: 10px;
        height: 10px;
        background: #667eea;
        animation: confetti-fall 3s linear infinite;
    }
    @keyframes confetti-fall {
        to {
            transform: translateY(100vh) rotate(360deg);
            opacity: 0;
        }
    }
    .success-card {
        animation: slideUp 0.6s ease-out;
    }
    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .timeline {
        position: relative;
        padding-left: 30px;
    }
    .timeline::before {
        content: '';
        position: absolute;
        left: 10px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: linear-gradient(to bottom, #667eea, #764ba2);
    }
    .timeline-item {
        position: relative;
        margin-bottom: 20px;
        padding-left: 10px;
    }
    .timeline-item::before {
        content: '';
        position: absolute;
        left: -24px;
        top: 5px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #667eea;
        border: 2px solid white;
        box-shadow: 0 0 0 2px #667eea;
    }
    .timeline-item.active::before {
        background: #28a745;
        box-shadow: 0 0 0 2px #28a745;
        animation: pulse 2s infinite;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                
                <!-- Success Icon -->
                <div class="text-center mb-4">
                    <div class="success-animation">
                        <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex p-4 mb-3" 
                             style="width: 150px; height: 150px; align-items: center; justify-content: center;">
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 6rem;"></i>
                        </div>
                    </div>
                    <h1 class="fw-bold mb-2">Pembayaran Berhasil!</h1>
                    <p class="lead text-muted">
                        Terima kasih, pembayaran Anda telah berhasil dikonfirmasi
                    </p>
                </div>

                <!-- Main Success Card -->
                <div class="card shadow-lg border-0 success-card mb-4">
                    <div class="card-body p-4">
                        
                        <!-- Order Number -->
                        <div class="alert alert-success mb-4">
                            <div class="text-center">
                                <small class="d-block text-muted mb-2">Nomor Pesanan</small>
                                <h3 class="text-success mb-0 fw-bold"><?= esc($order_number) ?></h3>
                            </div>
                        </div>

                        <?php if (isset($order) && is_array($order)): ?>
                            <!-- Order Summary -->
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <div class="card bg-light border-0">
                                        <div class="card-body">
                                            <small class="text-muted">
                                                <i class="bi bi-stars"></i> Layanan
                                            </small>
                                            <h6 class="mb-0 fw-bold">
                                                <?= esc($order['service']['name'] ?? $order['service_name'] ?? '-') ?>
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card bg-light border-0">
                                        <div class="card-body">
                                            <small class="text-muted">
                                                <i class="bi bi-cash"></i> Total Dibayar
                                            </small>
                                            <h6 class="mb-0 fw-bold text-primary">
                                                <?php
                                                $total = $order['total_price'] ?? $order['total'] ?? $order['grand_total'] ?? 0;
                                                if ($total == 0 && isset($order['service']['price'])) {
                                                    $total = $order['service']['price'];
                                                }
                                                echo format_rupiah($total);
                                                ?>
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Next Steps Timeline -->
                        <div class="card bg-light border-0 mb-4">
                            <div class="card-body">
                                <h5 class="fw-bold mb-4">
                                    <i class="bi bi-list-check text-primary"></i> Langkah Selanjutnya
                                </h5>
                                <div class="timeline">
                                    <div class="timeline-item active">
                                        <strong class="d-block mb-1">1. Pembayaran Dikonfirmasi ✓</strong>
                                        <small class="text-muted">Pembayaran Anda telah berhasil diverifikasi</small>
                                    </div>
                                    <div class="timeline-item">
                                        <strong class="d-block mb-1">2. Pesanan Diproses</strong>
                                        <small class="text-muted">Tim kami akan memproses pesanan Anda</small>
                                    </div>
                                    <div class="timeline-item">
                                        <strong class="d-block mb-1">3. Penjemputan Sepatu</strong>
                                        <small class="text-muted">Kurir akan menjemput sepatu sesuai jadwal</small>
                                    </div>
                                    <div class="timeline-item">
                                        <strong class="d-block mb-1">4. Proses Perawatan</strong>
                                        <small class="text-muted">Sepatu Anda akan dirawat oleh profesional</small>
                                    </div>
                                    <div class="timeline-item">
                                        <strong class="d-block mb-1">5. Pengiriman Kembali</strong>
                                        <small class="text-muted">Sepatu bersih dikirim ke alamat Anda</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Notification Info -->
                        <div class="alert alert-info mb-4">
                            <div class="d-flex align-items-start">
                                <i class="bi bi-bell-fill fs-4 me-3"></i>
                                <div>
                                    <h6 class="fw-bold mb-2">Notifikasi WhatsApp</h6>
                                    <p class="mb-0 small">
                                        Anda akan menerima update status pesanan melalui WhatsApp di setiap tahap proses. 
                                        Pastikan nomor WhatsApp Anda aktif.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-grid gap-2">
                            <?php if (is_logged_in()): ?>
                                <a href="<?= base_url('user/orders/' . $order_number) ?>" class="btn btn-primary btn-lg">
                                    <i class="bi bi-eye"></i> Lihat Detail Pesanan
                                </a>
                                <a href="<?= base_url('user/orders') ?>" class="btn btn-outline-primary">
                                    <i class="bi bi-box-seam"></i> Lihat Semua Pesanan Saya
                                </a>
                            <?php else: ?>
                                <a href="<?= base_url('guest/track?order=' . $order_number) ?>" class="btn btn-primary btn-lg">
                                    <i class="bi bi-search"></i> Lacak Pesanan
                                </a>
                            <?php endif; ?>
                            <a href="<?= base_url('/') ?>" class="btn btn-outline-secondary">
                                <i class="bi bi-house"></i> Kembali ke Beranda
                            </a>
                        </div>

                    </div>
                </div>

                <!-- Additional Cards -->
                <div class="row g-3">
                    <!-- Invoice -->
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center">
                                <i class="bi bi-receipt fs-1 text-primary mb-3"></i>
                                <h6 class="fw-bold">Invoice</h6>
                                <p class="text-muted small mb-3">Download invoice pembayaran Anda</p>
                                <a href="<?= base_url('invoice/' . $order_number) ?>" 
                                   class="btn btn-outline-primary btn-sm" 
                                   target="_blank">
                                    <i class="bi bi-download"></i> Download Invoice
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Service -->
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center">
                                <i class="bi bi-whatsapp fs-1 text-success mb-3"></i>
                                <h6 class="fw-bold">Butuh Bantuan?</h6>
                                <p class="text-muted small mb-3">Hubungi customer service kami</p>
                                <a href="https://wa.me/6281234567890?text=Halo%20Tapak%20Bersih,%20saya%20sudah%20melakukan%20pembayaran%20untuk%20pesanan%20<?= urlencode($order_number) ?>" 
                                   target="_blank" 
                                   class="btn btn-success btn-sm">
                                    <i class="bi bi-whatsapp"></i> Chat WhatsApp
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Create confetti effect
function createConfetti() {
    const colors = ['#667eea', '#764ba2', '#f093fb', '#4facfe', '#43e97b', '#38f9d7'];
    
    for (let i = 0; i < 80; i++) {
        setTimeout(() => {
            const confetti = document.createElement('div');
            confetti.className = 'confetti';
            confetti.style.left = Math.random() * 100 + '%';
            confetti.style.background = colors[Math.floor(Math.random() * colors.length)];
            confetti.style.animationDelay = Math.random() * 3 + 's';
            confetti.style.animationDuration = (Math.random() * 2 + 2) + 's';
            document.body.appendChild(confetti);
            
            setTimeout(() => confetti.remove(), 5000);
        }, i * 30);
    }
}

// Play success sound (optional)
function playSuccessSound() {
    const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBSuBzvLZiTYIGWi77eifTRALUKfj8LZjHAY4kdbyy3ksBS=');
    audio.volume = 0.3;
    audio.play().catch(() => {}); // Ignore if blocked
}

// Trigger effects on page load
document.addEventListener('DOMContentLoaded', function() {
    createConfetti();
    
    // Optional: Play sound after a short delay
    setTimeout(() => {
        playSuccessSound();
    }, 300);
});
</script>
<?= $this->endSection() ?>
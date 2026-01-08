<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
<style>
    .success-animation {
        animation: scaleIn 0.5s ease-out;
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
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-5 text-center">
                        
                        <!-- Success Icon -->
                        <div class="success-animation mb-4">
                            <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex p-4 mb-3">
                                <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
                            </div>
                        </div>

                        <!-- Success Message -->
                        <h2 class="fw-bold mb-3">Pembayaran Berhasil Dikonfirmasi!</h2>
                        <p class="lead text-muted mb-4">
                            Terima kasih! Bukti pembayaran Anda telah kami terima dan sedang dalam proses verifikasi.
                        </p>

                        <!-- Order Number -->
                        <div class="alert alert-success mb-4">
                            <strong>Nomor Pesanan:</strong><br>
                            <h4 class="text-success mb-0 mt-2"><?= esc($order_number) ?></h4>
                        </div>

                        <!-- Next Steps -->
                        <div class="card bg-light border-0 mb-4">
                            <div class="card-body">
                                <h5 class="fw-bold mb-3">
                                    <i class="bi bi-list-check text-primary"></i> Langkah Selanjutnya
                                </h5>
                                <ol class="text-start text-muted mb-0">
                                    <li class="mb-2">Pembayaran akan diverifikasi dalam 1x24 jam</li>
                                    <li class="mb-2">Anda akan mendapat notifikasi via WhatsApp</li>
                                    <li class="mb-2">Pesanan akan diproses setelah verifikasi</li>
                                    <li class="mb-2">Kurir akan menjemput sepatu sesuai jadwal</li>
                                </ol>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-grid gap-2">
                            <a href="<?= base_url('user/orders/' . $order_number) ?>" class="btn btn-primary btn-lg">
                                <i class="bi bi-eye"></i> Lihat Detail Pesanan
                            </a>
                            <a href="<?= base_url('user/orders') ?>" class="btn btn-outline-secondary">
                                <i class="bi bi-box-seam"></i> Lihat Semua Pesanan
                            </a>
                            <a href="<?= base_url('/') ?>" class="btn btn-outline-secondary">
                                <i class="bi bi-house"></i> Kembali ke Beranda
                            </a>
                        </div>

                        <!-- Help -->
                        <div class="mt-4 pt-4 border-top">
                            <p class="text-muted small mb-2">Ada pertanyaan?</p>
                            <a href="https://wa.me/6281234567890?text=Halo,%20saya%20sudah%20melakukan%20pembayaran%20untuk%20<?= urlencode($order_number) ?>" 
                               target="_blank" 
                               class="btn btn-success btn-sm">
                                <i class="bi bi-whatsapp"></i> Hubungi Customer Service
                            </a>
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
    const colors = ['#667eea', '#764ba2', '#f093fb', '#4facfe'];
    for (let i = 0; i < 50; i++) {
        setTimeout(() => {
            const confetti = document.createElement('div');
            confetti.className = 'confetti';
            confetti.style.left = Math.random() * 100 + '%';
            confetti.style.background = colors[Math.floor(Math.random() * colors.length)];
            confetti.style.animationDelay = Math.random() * 3 + 's';
            document.body.appendChild(confetti);
            
            setTimeout(() => confetti.remove(), 3000);
        }, i * 30);
    }
}

// Trigger confetti on page load
createConfetti();
</script>
<?= $this->endSection() ?>
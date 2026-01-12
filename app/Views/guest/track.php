<?= $this->extend('layouts/main') ?>

<?php
helper('format');

// Status configuration sesuai Laravel database enum
function getStatusBadgeClass($status) {
    $classes = [
        'waiting_pickup' => 'warning text-dark',
        'dalam_penjemputan' => 'info',
        'in_progress' => 'primary',
        'ready_for_delivery' => 'success',
        'on_delivery' => 'info',
        'completed' => 'success',
        'cancelled' => 'danger'
    ];
    return $classes[$status] ?? 'secondary';
}

function getStatusLabel($status) {
    $labels = [
        'waiting_pickup' => 'Menunggu Pickup',
        'dalam_penjemputan' => 'Dalam Penjemputan',
        'in_progress' => 'Diproses',
        'ready_for_delivery' => 'Siap Antar',
        'on_delivery' => 'Dikirim',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan'
    ];
    return $labels[$status] ?? ucfirst(str_replace('_', ' ', $status));
}

function getStatusIcon($status) {
    $icons = [
        'waiting_pickup' => 'hourglass-split',
        'dalam_penjemputan' => 'truck',
        'in_progress' => 'gear-fill',
        'ready_for_delivery' => 'box-seam',
        'on_delivery' => 'bicycle',
        'completed' => 'check-circle-fill',
        'cancelled' => 'x-circle-fill'
    ];
    return $icons[$status] ?? 'circle';
}

$order = $order ?? null;
$searched = $searched ?? false;
?>

<?= $this->section('styles') ?>
<style>
    .track-hero {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 60px 0;
        color: white;
    }
    .search-box {
        background: white;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 15px 40px rgba(0,0,0,0.2);
        margin-top: -50px;
        position: relative;
        z-index: 10;
    }
    .status-timeline {
        display: flex;
        justify-content: space-between;
        position: relative;
        margin: 30px 0;
        padding: 0 10px;
    }
    .status-timeline::before {
        content: '';
        position: absolute;
        top: 20px;
        left: 30px;
        right: 30px;
        height: 4px;
        background: #e9ecef;
        border-radius: 2px;
    }
    .timeline-step {
        text-align: center;
        position: relative;
        z-index: 2;
        flex: 1;
    }
    .timeline-step .step-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #e9ecef;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 10px;
        font-size: 16px;
        color: #6c757d;
        border: 4px solid #fff;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }
    .timeline-step.completed .step-icon { 
        background: #198754; 
        color: white; 
    }
    .timeline-step.active .step-icon { 
        background: #0d6efd; 
        color: white;
        animation: pulse 2s infinite;
        transform: scale(1.1);
    }
    @keyframes pulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(13, 110, 253, 0.4); }
        50% { box-shadow: 0 0 0 15px rgba(13, 110, 253, 0); }
    }
    .timeline-step .step-label { 
        font-size: 12px; 
        color: #6c757d;
        font-weight: 500;
    }
    .timeline-step.completed .step-label,
    .timeline-step.active .step-label { 
        color: #212529; 
        font-weight: 600; 
    }
    .order-card {
        border: none;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        overflow: hidden;
    }
    .order-card .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px 25px;
    }
    .info-item {
        padding: 15px;
        border-radius: 12px;
        background: #f8f9fa;
        margin-bottom: 10px;
    }
    .info-label {
        font-size: 0.8rem;
        color: #6c757d;
        margin-bottom: 3px;
    }
    .info-value {
        font-weight: 600;
        color: #212529;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Hero Section -->
<section class="track-hero text-center">
    <div class="container">
        <h1 class="display-5 fw-bold mb-3">
            <i class="bi bi-search"></i> Lacak Pesanan
        </h1>
        <p class="lead opacity-75 mb-0">
            Masukkan nomor pesanan untuk melacak status laundry sepatu Anda
        </p>
    </div>
</section>

<!-- Search Box -->
<section class="pb-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="search-box">
                    <form action="<?= base_url('guest/track') ?>" method="GET" id="trackForm">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-9">
                                <label class="form-label fw-bold">
                                    <i class="bi bi-hash"></i> Nomor Pesanan
                                </label>
                                <input type="text" 
                                       class="form-control form-control-lg" 
                                       name="order_number" 
                                       id="orderNumber"
                                       placeholder="Contoh: ORD-20260109-XXXXX"
                                       value="<?= esc($order_number ?? '') ?>"
                                       required
                                       style="border-radius: 12px; border: 2px solid #e9ecef;">
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary btn-lg w-100" style="border-radius: 12px;">
                                    <i class="bi bi-search"></i> Lacak
                                </button>
                            </div>
                        </div>
                    </form>
                    
                    <div class="text-center mt-3">
                        <small class="text-muted">
                            <i class="bi bi-info-circle"></i> 
                            Nomor pesanan dapat ditemukan di email konfirmasi atau WhatsApp Anda
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Result Section -->
<?php if ($searched): ?>
<section class="py-4">
    <div class="container">
        <?php if (!empty($error)): ?>
            <!-- Error State -->
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="alert alert-danger text-center py-4">
                        <i class="bi bi-exclamation-circle fs-1 d-block mb-3"></i>
                        <h5>Pesanan Tidak Ditemukan</h5>
                        <p class="mb-0"><?= esc($error) ?></p>
                    </div>
                    <div class="text-center">
                        <p class="text-muted">Pastikan nomor pesanan yang Anda masukkan benar.</p>
                        <a href="https://wa.me/6281234567890?text=Halo,%20saya%20ingin%20melacak%20pesanan%20saya" 
                           class="btn btn-success" target="_blank">
                            <i class="bi bi-whatsapp"></i> Hubungi CS via WhatsApp
                        </a>
                    </div>
                </div>
            </div>
        <?php elseif ($order): ?>
            <!-- Order Found -->
            <?php
            $status = $order['order_status'] ?? 'waiting_pickup';
            $paymentStatus = $order['payment_status'] ?? 'pending';
            
            // Status flow
            $statusFlow = ['waiting_pickup', 'dalam_penjemputan', 'in_progress', 'ready_for_delivery', 'on_delivery', 'completed'];
            $currentIndex = array_search($status, $statusFlow);
            if ($currentIndex === false) $currentIndex = -1;
            
            $isCancelled = ($status === 'cancelled');
            ?>
            
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <!-- Order Card -->
                    <div class="card order-card">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h4 class="mb-1 fw-bold"><?= esc($order['order_number']) ?></h4>
                                    <p class="mb-0 opacity-75">
                                        <i class="bi bi-calendar"></i> 
                                        <?= !empty($order['created_at']) ? date('d F Y, H:i', strtotime($order['created_at'])) : '-' ?> WIB
                                    </p>
                                </div>
                                <div class="col-md-4 text-md-end mt-2 mt-md-0">
                                    <span class="badge bg-light text-dark fs-6">
                                        <i class="bi bi-<?= getStatusIcon($status) ?>"></i>
                                        <?= getStatusLabel($status) ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <!-- Status Timeline -->
                            <?php if (!$isCancelled): ?>
                            <div class="mb-4">
                                <h6 class="fw-bold mb-3"><i class="bi bi-diagram-3"></i> Progress Pesanan</h6>
                                <div class="status-timeline">
                                    <?php
                                    $steps = [
                                        ['label' => 'Menunggu', 'icon' => 'bi-hourglass', 'step' => 0],
                                        ['label' => 'Dijemput', 'icon' => 'bi-truck', 'step' => 1],
                                        ['label' => 'Diproses', 'icon' => 'bi-gear', 'step' => 2],
                                        ['label' => 'Siap', 'icon' => 'bi-box-seam', 'step' => 3],
                                        ['label' => 'Dikirim', 'icon' => 'bi-bicycle', 'step' => 4],
                                        ['label' => 'Selesai', 'icon' => 'bi-trophy', 'step' => 5],
                                    ];
                                    foreach ($steps as $step):
                                        $stepClass = '';
                                        if ($step['step'] < $currentIndex) {
                                            $stepClass = 'completed';
                                        } elseif ($step['step'] == $currentIndex) {
                                            $stepClass = 'active';
                                        }
                                    ?>
                                        <div class="timeline-step <?= $stepClass ?>">
                                            <div class="step-icon"><i class="bi <?= $step['icon'] ?>"></i></div>
                                            <div class="step-label"><?= $step['label'] ?></div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php else: ?>
                            <div class="alert alert-danger mb-4">
                                <i class="bi bi-x-circle fs-4 me-2"></i>
                                <strong>Pesanan Dibatalkan</strong>
                                <?php if (!empty($order['cancellation_reason'])): ?>
                                    <br><small>Alasan: <?= esc($order['cancellation_reason']) ?></small>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                            
                            <div class="row g-4">
                                <!-- Order Info -->
                                <div class="col-md-6">
                                    <h6 class="fw-bold mb-3"><i class="bi bi-info-circle"></i> Informasi Pesanan</h6>
                                    
                                    <div class="info-item">
                                        <div class="info-label">Status Pembayaran</div>
                                        <div class="info-value">
                                            <span class="badge bg-<?= $paymentStatus === 'paid' ? 'success' : 'warning text-dark' ?>">
                                                <?= $paymentStatus === 'paid' ? 'Lunas' : 'Belum Bayar' ?>
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="info-item">
                                        <div class="info-label">Total Pembayaran</div>
                                        <div class="info-value text-primary fs-5">
                                            <?= format_rupiah($order['total_amount'] ?? 0) ?>
                                        </div>
                                    </div>
                                    
                                    <?php if (!empty($order['branch'])): ?>
                                    <div class="info-item">
                                        <div class="info-label">Cabang</div>
                                        <div class="info-value"><?= esc($order['branch']['name'] ?? '-') ?></div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Pickup/Delivery Info -->
                                <div class="col-md-6">
                                    <h6 class="fw-bold mb-3"><i class="bi bi-truck"></i> Info Pengiriman</h6>
                                    
                                    <div class="info-item">
                                        <div class="info-label">Pickup</div>
                                        <div class="info-value">
                                            <?= !empty($order['pickup_date']) ? date('d M Y', strtotime($order['pickup_date'])) : '-' ?>
                                            <?= !empty($order['pickup_time']) ? ', ' . date('H:i', strtotime($order['pickup_time'])) : '' ?>
                                        </div>
                                        <small class="text-muted"><?= esc($order['pickup_address'] ?? '-') ?></small>
                                    </div>
                                    
                                    <div class="info-item">
                                        <div class="info-label">Delivery</div>
                                        <div class="info-value">
                                            <?= !empty($order['delivery_date']) ? date('d M Y', strtotime($order['delivery_date'])) : '-' ?>
                                            <?= !empty($order['delivery_time']) ? ', ' . date('H:i', strtotime($order['delivery_time'])) : '' ?>
                                        </div>
                                        <small class="text-muted"><?= esc($order['delivery_address'] ?? '-') ?></small>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Items -->
                            <?php if (!empty($order['items'])): ?>
                            <hr class="my-4">
                            <h6 class="fw-bold mb-3"><i class="bi bi-bag"></i> Item Pesanan</h6>
                            <div class="table-responsive">
                                <table class="table table-borderless">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Layanan</th>
                                            <th class="text-center">Qty</th>
                                            <th class="text-end">Harga</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($order['items'] as $item): ?>
                                        <tr>
                                            <td><?= esc($item['service']['name'] ?? $item['service_name'] ?? 'Layanan') ?></td>
                                            <td class="text-center"><?= $item['quantity'] ?? 1 ?></td>
                                            <td class="text-end"><?= format_rupiah($item['price'] ?? 0) ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Payment Button -->
                            <?php if ($paymentStatus !== 'paid' && !in_array($status, ['completed', 'cancelled'])): ?>
                            <div class="text-center mt-4 pt-3 border-top">
                                <a href="<?= base_url('payment/' . $order['order_number']) ?>" class="btn btn-success btn-lg">
                                    <i class="bi bi-credit-card"></i> Bayar Sekarang
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php else: ?>
<!-- Info Section (when not searched) -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="row g-4 text-center">
                    <div class="col-md-4">
                        <div class="p-4">
                            <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex p-4 mb-3">
                                <i class="bi bi-1-circle-fill text-primary fs-2"></i>
                            </div>
                            <h6 class="fw-bold">Masukkan Nomor</h6>
                            <p class="text-muted small mb-0">Ketik nomor pesanan yang Anda terima via email/WhatsApp</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-4">
                            <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex p-4 mb-3">
                                <i class="bi bi-2-circle-fill text-primary fs-2"></i>
                            </div>
                            <h6 class="fw-bold">Klik Lacak</h6>
                            <p class="text-muted small mb-0">Tekan tombol lacak untuk mencari pesanan Anda</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-4">
                            <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex p-4 mb-3">
                                <i class="bi bi-3-circle-fill text-primary fs-2"></i>
                            </div>
                            <h6 class="fw-bold">Lihat Status</h6>
                            <p class="text-muted small mb-0">Pantau progress laundry sepatu Anda secara real-time</p>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-5">
                    <p class="text-muted mb-3">Belum punya akun? Daftar untuk kemudahan melacak pesanan</p>
                    <a href="<?= base_url('auth/register') ?>" class="btn btn-outline-primary">
                        <i class="bi bi-person-plus"></i> Daftar Sekarang
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Auto uppercase order number
document.getElementById('orderNumber').addEventListener('input', function() {
    this.value = this.value.toUpperCase();
});
</script>
<?= $this->endSection() ?>
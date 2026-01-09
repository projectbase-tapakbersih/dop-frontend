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

$order = $order ?? null;
?>

<?= $this->section('styles') ?>
<style>
    .status-timeline {
        display: flex;
        justify-content: space-between;
        position: relative;
        margin: 20px 0;
        padding: 0 10px;
    }
    .status-timeline::before {
        content: '';
        position: absolute;
        top: 15px;
        left: 25px;
        right: 25px;
        height: 3px;
        background: #e9ecef;
    }
    .timeline-step {
        text-align: center;
        position: relative;
        z-index: 2;
        flex: 1;
    }
    .timeline-step .step-icon {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #e9ecef;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 8px;
        font-size: 14px;
        color: #6c757d;
        border: 3px solid #fff;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .timeline-step.completed .step-icon { 
        background: #198754; 
        color: white; 
    }
    .timeline-step.active .step-icon { 
        background: #0d6efd; 
        color: white;
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(13, 110, 253, 0.4); }
        50% { box-shadow: 0 0 0 10px rgba(13, 110, 253, 0); }
    }
    .timeline-step .step-label { 
        font-size: 11px; 
        color: #6c757d; 
    }
    .timeline-step.completed .step-label,
    .timeline-step.active .step-label { 
        color: #212529; 
        font-weight: 600; 
    }
    .detail-card {
        border-radius: 12px;
        border: none;
        box-shadow: 0 2px 15px rgba(0,0,0,0.08);
    }
    .info-label {
        color: #6c757d;
        font-size: 0.8rem;
        margin-bottom: 2px;
    }
    .info-value {
        font-weight: 600;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Header -->
<section class="py-4 bg-primary text-white">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item">
                    <a href="<?= base_url('user/dashboard') ?>" class="text-white-50">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="<?= base_url('user/orders') ?>" class="text-white-50">Pesanan Saya</a>
                </li>
                <li class="breadcrumb-item active text-white"><?= esc($order['order_number'] ?? 'Detail') ?></li>
            </ol>
        </nav>
        <h2 class="mb-0 fw-bold"><i class="bi bi-receipt"></i> Detail Pesanan</h2>
    </div>
</section>

<?php if (!empty($error)): ?>
    <div class="container mt-4">
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle"></i> <?= esc($error) ?>
        </div>
        <a href="<?= base_url('user/orders') ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
<?php elseif (empty($order)): ?>
    <div class="container mt-4">
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-circle"></i> Pesanan tidak ditemukan
        </div>
        <a href="<?= base_url('user/orders') ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
<?php else: ?>

<?php
$status = $order['order_status'] ?? 'waiting_pickup';
$paymentStatus = $order['payment_status'] ?? 'pending';

// Status flow sesuai Laravel enum
$statusFlow = ['waiting_pickup', 'dalam_penjemputan', 'in_progress', 'ready_for_delivery', 'on_delivery', 'completed'];
$currentIndex = array_search($status, $statusFlow);
if ($currentIndex === false) $currentIndex = -1;

$isCancelled = ($status === 'cancelled');
$isCompleted = ($status === 'completed');
?>

<section class="py-4">
    <div class="container">
        
        <!-- Order Info Card -->
        <div class="card detail-card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="fw-bold text-primary mb-1"><?= esc($order['order_number']) ?></h4>
                        <p class="text-muted mb-2">
                            <i class="bi bi-calendar"></i> 
                            <?= !empty($order['created_at']) ? date('d F Y, H:i', strtotime($order['created_at'])) : '-' ?> WIB
                        </p>
                        <div class="d-flex gap-2">
                            <span class="badge bg-<?= getStatusBadgeClass($status) ?> fs-6">
                                <?= getStatusLabel($status) ?>
                            </span>
                            <span class="badge bg-<?= $paymentStatus === 'paid' ? 'success' : 'warning text-dark' ?> fs-6">
                                <?= $paymentStatus === 'paid' ? 'Lunas' : 'Belum Bayar' ?>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <?php if ($paymentStatus !== 'paid' && !in_array($status, ['completed', 'cancelled'])): ?>
                            <a href="<?= base_url('payment/' . $order['order_number']) ?>" class="btn btn-success btn-lg">
                                <i class="bi bi-credit-card"></i> Bayar Sekarang
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Timeline -->
        <?php if (!$isCancelled): ?>
        <div class="card detail-card mb-4">
            <div class="card-body">
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
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Items -->
                <div class="card detail-card mb-4">
                    <div class="card-header bg-white">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-bag"></i> Item Pesanan</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Layanan</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-end">Harga</th>
                                        <th class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($order['items'])): ?>
                                        <?php foreach ($order['items'] as $item): ?>
                                            <?php 
                                            $itemPrice = $item['price'] ?? 0;
                                            $itemQty = $item['quantity'] ?? 1;
                                            $itemSubtotal = $item['subtotal'] ?? ($itemPrice * $itemQty);
                                            ?>
                                            <tr>
                                                <td>
                                                    <strong><?= esc($item['service']['name'] ?? $item['service_name'] ?? 'Layanan') ?></strong>
                                                    <?php if (!empty($item['notes'])): ?>
                                                        <br><small class="text-muted"><?= esc($item['notes']) ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center"><?= $itemQty ?></td>
                                                <td class="text-end"><?= format_rupiah($itemPrice) ?></td>
                                                <td class="text-end"><?= format_rupiah($itemSubtotal) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">Tidak ada item</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                                <tfoot class="table-light">
                                    <?php if (!empty($order['discount_amount']) && $order['discount_amount'] > 0): ?>
                                    <tr>
                                        <td colspan="3" class="text-end">Subtotal</td>
                                        <td class="text-end"><?= format_rupiah($order['subtotal'] ?? $order['total_amount']) ?></td>
                                    </tr>
                                    <tr class="text-success">
                                        <td colspan="3" class="text-end">Diskon</td>
                                        <td class="text-end">- <?= format_rupiah($order['discount_amount']) ?></td>
                                    </tr>
                                    <?php endif; ?>
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold">Total</td>
                                        <td class="text-end fw-bold text-primary fs-5"><?= format_rupiah($order['total_amount'] ?? 0) ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Pickup & Delivery Info -->
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card detail-card h-100">
                            <div class="card-header bg-white">
                                <h6 class="mb-0 fw-bold"><i class="bi bi-box-arrow-up text-primary"></i> Pickup</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <div class="info-label">Tanggal & Waktu</div>
                                    <div class="info-value">
                                        <?= !empty($order['pickup_date']) ? date('d M Y', strtotime($order['pickup_date'])) : '-' ?>
                                        <?= !empty($order['pickup_time']) ? date('H:i', strtotime($order['pickup_time'])) : '' ?>
                                    </div>
                                </div>
                                <div>
                                    <div class="info-label">Alamat</div>
                                    <div class="info-value"><?= esc($order['pickup_address'] ?? '-') ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card detail-card h-100">
                            <div class="card-header bg-white">
                                <h6 class="mb-0 fw-bold"><i class="bi bi-box-arrow-down text-success"></i> Delivery</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <div class="info-label">Tanggal & Waktu</div>
                                    <div class="info-value">
                                        <?= !empty($order['delivery_date']) ? date('d M Y', strtotime($order['delivery_date'])) : '-' ?>
                                        <?= !empty($order['delivery_time']) ? date('H:i', strtotime($order['delivery_time'])) : '' ?>
                                    </div>
                                </div>
                                <div>
                                    <div class="info-label">Alamat</div>
                                    <div class="info-value"><?= esc($order['delivery_address'] ?? '-') ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Payment Info -->
                <div class="card detail-card mb-4">
                    <div class="card-header bg-white">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-credit-card"></i> Pembayaran</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="info-label">Metode</div>
                            <div class="info-value text-uppercase"><?= esc($order['payment_method'] ?? 'Midtrans') ?></div>
                        </div>
                        <div class="mb-3">
                            <div class="info-label">Status</div>
                            <span class="badge bg-<?= $paymentStatus === 'paid' ? 'success' : 'warning text-dark' ?> fs-6">
                                <?= $paymentStatus === 'paid' ? 'Lunas' : 'Belum Bayar' ?>
                            </span>
                        </div>
                        <hr>
                        <div>
                            <div class="info-label">Total Pembayaran</div>
                            <div class="fw-bold text-primary fs-4"><?= format_rupiah($order['total_amount'] ?? 0) ?></div>
                        </div>
                        
                        <?php if ($paymentStatus !== 'paid' && !in_array($status, ['completed', 'cancelled'])): ?>
                            <a href="<?= base_url('payment/' . $order['order_number']) ?>" class="btn btn-success w-100 mt-3">
                                <i class="bi bi-credit-card"></i> Bayar Sekarang
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Branch Info -->
                <?php if (!empty($order['branch'])): ?>
                <div class="card detail-card mb-4">
                    <div class="card-header bg-white">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-shop"></i> Cabang</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <div class="info-label">Nama</div>
                            <div class="info-value"><?= esc($order['branch']['name'] ?? '-') ?></div>
                        </div>
                        <?php if (!empty($order['branch']['address'])): ?>
                        <div>
                            <div class="info-label">Alamat</div>
                            <div class="info-value small"><?= esc($order['branch']['address']) ?></div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Cancel Order -->
                <?php if (!$isCompleted && !$isCancelled && $status === 'waiting_pickup'): ?>
                <div class="card detail-card border-danger">
                    <div class="card-body">
                        <h6 class="fw-bold text-danger mb-2"><i class="bi bi-exclamation-triangle"></i> Batalkan Pesanan?</h6>
                        <p class="text-muted small mb-3">Pesanan dapat dibatalkan selama belum dijemput</p>
                        <button class="btn btn-outline-danger w-100" onclick="cancelOrder()">
                            <i class="bi bi-x-circle"></i> Batalkan Pesanan
                        </button>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Back Button -->
        <div class="mt-4">
            <a href="<?= base_url('user/orders') ?>" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali ke Daftar Pesanan
            </a>
        </div>
    </div>
</section>

<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
async function cancelOrder() {
    const result = await Swal.fire({
        title: 'Batalkan Pesanan?',
        text: 'Apakah Anda yakin ingin membatalkan pesanan ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        confirmButtonText: 'Ya, Batalkan',
        cancelButtonText: 'Tidak',
        input: 'textarea',
        inputPlaceholder: 'Alasan pembatalan (opsional)...',
        inputAttributes: {
            'aria-label': 'Alasan pembatalan'
        }
    });
    
    if (!result.isConfirmed) return;
    
    try {
        Swal.fire({ 
            title: 'Memproses...', 
            allowOutsideClick: false, 
            didOpen: () => Swal.showLoading() 
        });
        
        const response = await fetch('<?= base_url('user/orders/' . ($order['order_number'] ?? '') . '/cancel') ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'reason=' + encodeURIComponent(result.value || 'Dibatalkan oleh customer')
        });
        
        const data = await response.json();
        
        Swal.fire({
            icon: data.success ? 'success' : 'error',
            title: data.success ? 'Berhasil!' : 'Gagal!',
            text: data.message
        }).then(() => {
            if (data.success) window.location.reload();
        });
    } catch (error) {
        console.error('Error:', error);
        Swal.fire({ 
            icon: 'error', 
            title: 'Error!', 
            text: 'Terjadi kesalahan saat menghubungi server' 
        });
    }
}
</script>
<?= $this->endSection() ?>
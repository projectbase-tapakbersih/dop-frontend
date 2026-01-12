<?= $this->extend('layouts/main') ?>

<?php
helper('format');

// Status configuration
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
    }
    .timeline-step.completed .step-icon { background: #198754; color: white; }
    .timeline-step.active .step-icon { background: #0d6efd; color: white; }
    .timeline-step .step-label { font-size: 11px; color: #6c757d; }
    .timeline-step.completed .step-label,
    .timeline-step.active .step-label { color: #212529; font-weight: 600; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Header -->
<section class="py-4 bg-primary text-white">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item">
                    <a href="<?= base_url('guest/track') ?>" class="text-white-50">Lacak Pesanan</a>
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
        <a href="<?= base_url('guest/track') ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
<?php elseif (empty($order)): ?>
    <div class="container mt-4">
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-circle"></i> Pesanan tidak ditemukan
        </div>
        <a href="<?= base_url('guest/track') ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
<?php else: ?>

<?php
$status = $order['order_status'] ?? 'waiting_pickup';
$paymentStatus = $order['payment_status'] ?? 'pending';

$statusFlow = ['waiting_pickup', 'dalam_penjemputan', 'in_progress', 'ready_for_delivery', 'on_delivery', 'completed'];
$currentIndex = array_search($status, $statusFlow);
if ($currentIndex === false) $currentIndex = -1;

$isCancelled = ($status === 'cancelled');
?>

<section class="py-4">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <!-- Order Info -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h4 class="fw-bold text-primary mb-1"><?= esc($order['order_number']) ?></h4>
                                <p class="text-muted mb-0">
                                    <i class="bi bi-calendar"></i> 
                                    <?= !empty($order['created_at']) ? date('d F Y, H:i', strtotime($order['created_at'])) : '-' ?>
                                </p>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-<?= getStatusBadgeClass($status) ?> fs-6 mb-1">
                                    <?= getStatusLabel($status) ?>
                                </span>
                                <br>
                                <span class="badge bg-<?= $paymentStatus === 'paid' ? 'success' : 'warning text-dark' ?>">
                                    <?= $paymentStatus === 'paid' ? 'Lunas' : 'Belum Bayar' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status Timeline -->
                <?php if (!$isCancelled): ?>
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">Progress Pesanan</h6>
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
                                if ($step['step'] < $currentIndex) $stepClass = 'completed';
                                elseif ($step['step'] == $currentIndex) $stepClass = 'active';
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
                    <i class="bi bi-x-circle"></i> <strong>Pesanan Dibatalkan</strong>
                </div>
                <?php endif; ?>

                <!-- Items -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h6 class="mb-0 fw-bold">Item Pesanan</h6>
                    </div>
                    <div class="card-body p-0">
                        <table class="table mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Layanan</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Harga</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($order['items'])): ?>
                                    <?php foreach ($order['items'] as $item): ?>
                                    <tr>
                                        <td><?= esc($item['service']['name'] ?? $item['service_name'] ?? 'Layanan') ?></td>
                                        <td class="text-center"><?= $item['quantity'] ?? 1 ?></td>
                                        <td class="text-end"><?= format_rupiah($item['price'] ?? 0) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="2" class="text-end fw-bold">Total</td>
                                    <td class="text-end fw-bold text-primary"><?= format_rupiah($order['total_amount'] ?? 0) ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Payment -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h6 class="mb-0 fw-bold">Pembayaran</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <small class="text-muted">Total</small>
                            <h4 class="text-primary mb-0"><?= format_rupiah($order['total_amount'] ?? 0) ?></h4>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted">Status</small>
                            <p class="mb-0">
                                <span class="badge bg-<?= $paymentStatus === 'paid' ? 'success' : 'warning text-dark' ?>">
                                    <?= $paymentStatus === 'paid' ? 'Lunas' : 'Belum Bayar' ?>
                                </span>
                            </p>
                        </div>
                        
                        <?php if ($paymentStatus !== 'paid' && !in_array($status, ['completed', 'cancelled'])): ?>
                        <a href="<?= base_url('payment/' . $order['order_number']) ?>" class="btn btn-success w-100">
                            <i class="bi bi-credit-card"></i> Bayar Sekarang
                        </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Pickup Info -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h6 class="mb-0 fw-bold">Info Pengiriman</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <small class="text-muted">Pickup</small>
                            <p class="mb-0 fw-bold">
                                <?= !empty($order['pickup_date']) ? date('d M Y', strtotime($order['pickup_date'])) : '-' ?>
                                <?= !empty($order['pickup_time']) ? ', ' . date('H:i', strtotime($order['pickup_time'])) : '' ?>
                            </p>
                            <small class="text-muted"><?= esc($order['pickup_address'] ?? '-') ?></small>
                        </div>
                        <div>
                            <small class="text-muted">Delivery</small>
                            <p class="mb-0 fw-bold">
                                <?= !empty($order['delivery_date']) ? date('d M Y', strtotime($order['delivery_date'])) : '-' ?>
                                <?= !empty($order['delivery_time']) ? ', ' . date('H:i', strtotime($order['delivery_time'])) : '' ?>
                            </p>
                            <small class="text-muted"><?= esc($order['delivery_address'] ?? '-') ?></small>
                        </div>
                    </div>
                </div>

                <!-- Help -->
                <div class="card shadow-sm border-success">
                    <div class="card-body text-center">
                        <p class="mb-2"><i class="bi bi-headset fs-4 text-success"></i></p>
                        <p class="mb-2">Ada pertanyaan?</p>
                        <a href="https://wa.me/6281234567890?text=Halo,%20saya%20ingin%20bertanya%20tentang%20pesanan%20<?= esc($order['order_number']) ?>" 
                           class="btn btn-success btn-sm" target="_blank">
                            <i class="bi bi-whatsapp"></i> Hubungi CS
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <a href="<?= base_url('guest/track') ?>" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
</section>

<?php endif; ?>

<?= $this->endSection() ?>
<?= $this->extend('layouts/main') ?>

<?php
if (!function_exists('format_rupiah')) {
    function format_rupiah($number) {
        return 'Rp ' . number_format((float)$number, 0, ',', '.');
    }
}
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
        left: 0;
        right: 0;
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
        margin: 0 auto 5px;
        font-size: 12px;
        color: #6c757d;
    }
    .timeline-step.completed .step-icon { background: #198754; color: white; }
    .timeline-step.active .step-icon { background: #0d6efd; color: white; }
    .timeline-step .step-label { font-size: 10px; color: #6c757d; }
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
                <li class="breadcrumb-item"><a href="<?= base_url('user/orders') ?>" class="text-white-50">Pesanan Saya</a></li>
                <li class="breadcrumb-item active text-white"><?= esc($order['order_number'] ?? 'Detail') ?></li>
            </ol>
        </nav>
        <h2 class="mb-0 fw-bold">Detail Pesanan</h2>
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
        <div class="alert alert-warning">Pesanan tidak ditemukan</div>
    </div>
<?php else: ?>

<?php
$status = $order['order_status'] ?? 'pending';
$paymentStatus = $order['payment_status'] ?? 'pending';

$statusConfig = [
    'waiting_pickup' => ['badge' => 'warning', 'label' => 'Menunggu Pickup', 'step' => 1],
    'picked_up' => ['badge' => 'info', 'label' => 'Dijemput', 'step' => 2],
    'in_process' => ['badge' => 'primary', 'label' => 'Diproses', 'step' => 3],
    'washing' => ['badge' => 'primary', 'label' => 'Dicuci', 'step' => 3],
    'drying' => ['badge' => 'primary', 'label' => 'Dikeringkan', 'step' => 3],
    'quality_check' => ['badge' => 'info', 'label' => 'QC', 'step' => 4],
    'ready' => ['badge' => 'success', 'label' => 'Siap', 'step' => 4],
    'on_delivery' => ['badge' => 'info', 'label' => 'Diantar', 'step' => 5],
    'completed' => ['badge' => 'success', 'label' => 'Selesai', 'step' => 6],
    'cancelled' => ['badge' => 'danger', 'label' => 'Dibatalkan', 'step' => 0]
];
$currentStatus = $statusConfig[$status] ?? ['badge' => 'secondary', 'label' => ucfirst($status), 'step' => 0];
$currentStep = $currentStatus['step'];
$isCancelled = ($status === 'cancelled');
$isCompleted = in_array($status, ['completed', 'delivered']);
?>

<section class="py-4">
    <div class="container">
        
        <!-- Order Info Card -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="fw-bold text-primary mb-1"><?= esc($order['order_number']) ?></h4>
                        <p class="text-muted mb-2">
                            <i class="bi bi-calendar"></i> <?= date('d F Y, H:i', strtotime($order['created_at'])) ?>
                        </p>
                        <span class="badge bg-<?= $currentStatus['badge'] ?> me-1"><?= $currentStatus['label'] ?></span>
                        <span class="badge bg-<?= $paymentStatus === 'paid' ? 'success' : 'warning' ?>">
                            <?= $paymentStatus === 'paid' ? 'Lunas' : 'Belum Bayar' ?>
                        </span>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <?php if ($paymentStatus !== 'paid' && !in_array($status, ['completed', 'cancelled'])): ?>
                            <a href="<?= base_url('payment/' . $order['order_number']) ?>" class="btn btn-success">
                                <i class="bi bi-credit-card"></i> Bayar Sekarang
                            </a>
                        <?php endif; ?>
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
                        ['label' => 'Pickup', 'icon' => 'bi-box-arrow-up', 'step' => 1],
                        ['label' => 'Dijemput', 'icon' => 'bi-truck', 'step' => 2],
                        ['label' => 'Proses', 'icon' => 'bi-gear', 'step' => 3],
                        ['label' => 'Siap', 'icon' => 'bi-check-circle', 'step' => 4],
                        ['label' => 'Kirim', 'icon' => 'bi-bicycle', 'step' => 5],
                        ['label' => 'Selesai', 'icon' => 'bi-trophy', 'step' => 6],
                    ];
                    foreach ($steps as $step):
                        $stepClass = $step['step'] < $currentStep ? 'completed' : ($step['step'] == $currentStep ? 'active' : '');
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
            <?php if (!empty($order['cancellation_reason'])): ?>
                <br>Alasan: <?= esc($order['cancellation_reason']) ?>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="row g-4">
            <div class="col-lg-8">
                <!-- Items -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header"><h6 class="mb-0 fw-bold">Item Pesanan</h6></div>
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
                                            <td><?= esc($item['service']['name'] ?? 'Service') ?></td>
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

                <!-- Pickup Info -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header"><h6 class="mb-0 fw-bold">Info Pickup</h6></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <small class="text-muted">Alamat</small>
                                <p class="mb-0 fw-bold"><?= esc($order['pickup_address'] ?? '-') ?></p>
                            </div>
                            <div class="col-md-3 mb-3">
                                <small class="text-muted">Tanggal</small>
                                <p class="mb-0 fw-bold"><?= !empty($order['pickup_date']) ? date('d M Y', strtotime($order['pickup_date'])) : '-' ?></p>
                            </div>
                            <div class="col-md-3 mb-3">
                                <small class="text-muted">Waktu</small>
                                <p class="mb-0 fw-bold"><?= !empty($order['pickup_time']) ? date('H:i', strtotime($order['pickup_time'])) : '-' ?></p>
                            </div>
                        </div>
                        <?php if (!empty($order['special_notes'])): ?>
                        <div>
                            <small class="text-muted">Catatan</small>
                            <p class="mb-0"><?= esc($order['special_notes']) ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Payment Info -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header"><h6 class="mb-0 fw-bold">Pembayaran</h6></div>
                    <div class="card-body">
                        <div class="mb-3">
                            <small class="text-muted">Metode</small>
                            <p class="mb-0 fw-bold text-uppercase"><?= esc($order['payment_method'] ?? '-') ?></p>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted">Status</small>
                            <p class="mb-0">
                                <span class="badge bg-<?= $paymentStatus === 'paid' ? 'success' : 'warning' ?>">
                                    <?= $paymentStatus === 'paid' ? 'Lunas' : 'Belum Bayar' ?>
                                </span>
                            </p>
                        </div>
                        <div>
                            <small class="text-muted">Total</small>
                            <p class="mb-0 fw-bold text-primary fs-5"><?= format_rupiah($order['total_amount'] ?? 0) ?></p>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <?php if (!$isCompleted && !$isCancelled && $status === 'waiting_pickup'): ?>
                <div class="card shadow-sm border-danger">
                    <div class="card-body">
                        <h6 class="fw-bold text-danger">Batalkan Pesanan?</h6>
                        <p class="text-muted small mb-3">Pesanan dapat dibatalkan selama belum dijemput</p>
                        <button class="btn btn-outline-danger w-100" onclick="cancelOrder()">
                            <i class="bi bi-x-circle"></i> Batalkan
                        </button>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

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
        inputPlaceholder: 'Alasan pembatalan...'
    });
    
    if (!result.isConfirmed) return;
    
    try {
        Swal.fire({ title: 'Memproses...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        
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
        Swal.fire({ icon: 'error', title: 'Error!', text: 'Terjadi kesalahan' });
    }
}
</script>
<?= $this->endSection() ?>
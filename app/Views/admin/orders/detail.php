<?= $this->extend('layouts/main') ?>

<?php
// Helper function for formatting currency if not exists
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
        margin: 30px 0;
    }
    .status-timeline::before {
        content: '';
        position: absolute;
        top: 15px;
        left: 0;
        right: 0;
        height: 4px;
        background: #e9ecef;
        z-index: 1;
    }
    .timeline-step {
        text-align: center;
        position: relative;
        z-index: 2;
        flex: 1;
    }
    .timeline-step .step-icon {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        background: #e9ecef;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 8px;
        font-size: 14px;
        color: #6c757d;
    }
    .timeline-step.completed .step-icon {
        background: #198754;
        color: white;
    }
    .timeline-step.active .step-icon {
        background: #0d6efd;
        color: white;
        animation: pulse 1.5s infinite;
    }
    .timeline-step.cancelled .step-icon {
        background: #dc3545;
        color: white;
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
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.1); }
    }
    .info-card {
        border-radius: 12px;
        border: none;
    }
    .info-card .card-header {
        background: transparent;
        border-bottom: 1px solid #eee;
        font-weight: 600;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<section class="py-4 bg-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('admin/orders') ?>">Pesanan</a></li>
                        <li class="breadcrumb-item active"><?= esc($order['order_number'] ?? 'Detail') ?></li>
                    </ol>
                </nav>
                <h2 class="mb-0 fw-bold">
                    <i class="bi bi-box-seam"></i> Detail Pesanan
                </h2>
            </div>
            <a href="<?= base_url('admin/orders') ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
</section>

<?php if (!empty($error)): ?>
    <div class="container mt-4">
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle"></i> <?= esc($error) ?>
        </div>
    </div>
<?php elseif (empty($order)): ?>
    <div class="container mt-4">
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle"></i> Pesanan tidak ditemukan
        </div>
    </div>
<?php else: ?>

<?php
// Status configuration
$status = $order['order_status'] ?? 'pending';
$paymentStatus = $order['payment_status'] ?? 'pending';

$statusConfig = [
    'waiting_pickup' => ['badge' => 'warning', 'label' => 'Menunggu Pickup', 'step' => 1],
    'picked_up' => ['badge' => 'info', 'label' => 'Sudah Dijemput', 'step' => 2],
    'in_process' => ['badge' => 'primary', 'label' => 'Sedang Diproses', 'step' => 3],
    'processing' => ['badge' => 'primary', 'label' => 'Sedang Diproses', 'step' => 3],
    'washing' => ['badge' => 'primary', 'label' => 'Sedang Dicuci', 'step' => 3],
    'drying' => ['badge' => 'primary', 'label' => 'Sedang Dikeringkan', 'step' => 3],
    'quality_check' => ['badge' => 'info', 'label' => 'Quality Check', 'step' => 4],
    'ready' => ['badge' => 'success', 'label' => 'Siap Diantar', 'step' => 4],
    'on_delivery' => ['badge' => 'info', 'label' => 'Dalam Pengiriman', 'step' => 5],
    'completed' => ['badge' => 'success', 'label' => 'Selesai', 'step' => 6],
    'delivered' => ['badge' => 'success', 'label' => 'Selesai', 'step' => 6],
    'cancelled' => ['badge' => 'danger', 'label' => 'Dibatalkan', 'step' => 0]
];
$currentStatus = $statusConfig[$status] ?? ['badge' => 'secondary', 'label' => ucfirst($status), 'step' => 0];
$currentStep = $currentStatus['step'];
$isCancelled = ($status === 'cancelled');
$isCompleted = in_array($status, ['completed', 'delivered']);
?>

<section class="py-4">
    <div class="container">
        
        <!-- Order Header -->
        <div class="card info-card shadow-sm mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h4 class="mb-1 fw-bold text-primary"><?= esc($order['order_number']) ?></h4>
                        <p class="text-muted mb-0">
                            <i class="bi bi-calendar"></i> 
                            <?= !empty($order['created_at']) ? date('d F Y, H:i', strtotime($order['created_at'])) : '-' ?>
                        </p>
                    </div>
                    <div class="col-md-6 text-md-end mt-3 mt-md-0">
                        <span class="badge bg-<?= $currentStatus['badge'] ?> fs-6 px-3 py-2 me-2">
                            <?= $currentStatus['label'] ?>
                        </span>
                        <span class="badge bg-<?= $paymentStatus === 'paid' ? 'success' : ($paymentStatus === 'failed' ? 'danger' : 'warning') ?> fs-6 px-3 py-2">
                            <?= $paymentStatus === 'paid' ? 'Lunas' : ($paymentStatus === 'failed' ? 'Gagal' : 'Belum Bayar') ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Timeline -->
        <?php if (!$isCancelled): ?>
        <div class="card info-card shadow-sm mb-4">
            <div class="card-body">
                <h6 class="fw-bold mb-3"><i class="bi bi-diagram-3"></i> Progress Pesanan</h6>
                <div class="status-timeline">
                    <?php
                    $steps = [
                        ['label' => 'Pickup', 'icon' => 'bi-box-arrow-up', 'step' => 1],
                        ['label' => 'Dijemput', 'icon' => 'bi-truck', 'step' => 2],
                        ['label' => 'Proses', 'icon' => 'bi-gear', 'step' => 3],
                        ['label' => 'Siap', 'icon' => 'bi-check2-circle', 'step' => 4],
                        ['label' => 'Kirim', 'icon' => 'bi-bicycle', 'step' => 5],
                        ['label' => 'Selesai', 'icon' => 'bi-trophy', 'step' => 6],
                    ];
                    foreach ($steps as $step):
                        $stepClass = '';
                        if ($step['step'] < $currentStep) $stepClass = 'completed';
                        elseif ($step['step'] == $currentStep) $stepClass = 'active';
                    ?>
                        <div class="timeline-step <?= $stepClass ?>">
                            <div class="step-icon">
                                <i class="bi <?= $step['icon'] ?>"></i>
                            </div>
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
            <!-- Left Column -->
            <div class="col-lg-8">
                
                <!-- Order Items -->
                <div class="card info-card shadow-sm mb-4">
                    <div class="card-header">
                        <i class="bi bi-cart3"></i> Item Pesanan
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
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
                                            <tr>
                                                <td>
                                                    <strong><?= esc($item['service']['name'] ?? 'Service #' . ($item['service_id'] ?? '')) ?></strong>
                                                    <?php if (!empty($item['service']['description'])): ?>
                                                        <br><small class="text-muted"><?= esc($item['service']['description']) ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center"><?= $item['quantity'] ?? 1 ?></td>
                                                <td class="text-end"><?= format_rupiah($item['price'] ?? 0) ?></td>
                                                <td class="text-end">
                                                    <strong><?= format_rupiah(($item['price'] ?? 0) * ($item['quantity'] ?? 1)) ?></strong>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-3">Tidak ada item</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="3" class="text-end">Subtotal</td>
                                        <td class="text-end"><?= format_rupiah($order['subtotal'] ?? 0) ?></td>
                                    </tr>
                                    <?php if (($order['discount_amount'] ?? 0) > 0): ?>
                                    <tr>
                                        <td colspan="3" class="text-end text-success">Diskon</td>
                                        <td class="text-end text-success">-<?= format_rupiah($order['discount_amount']) ?></td>
                                    </tr>
                                    <?php endif; ?>
                                    <?php if (($order['delivery_fee'] ?? 0) > 0): ?>
                                    <tr>
                                        <td colspan="3" class="text-end">Ongkos Kirim</td>
                                        <td class="text-end"><?= format_rupiah($order['delivery_fee']) ?></td>
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

                <!-- Shoe Details -->
                <div class="card info-card shadow-sm mb-4">
                    <div class="card-header">
                        <i class="bi bi-shoe-heel"></i> Detail Sepatu
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="text-muted small">Tipe Sepatu</label>
                                <p class="mb-0 fw-bold"><?= esc($order['shoe_type'] ?? '-') ?></p>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="text-muted small">Ukuran</label>
                                <p class="mb-0 fw-bold"><?= esc($order['shoe_size'] ?? '-') ?></p>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="text-muted small">Kantong Plastik</label>
                                <p class="mb-0">
                                    <?php if ($order['plastic_bag_confirmed'] ?? false): ?>
                                        <span class="badge bg-success">Dikonfirmasi</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Tidak</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pickup Information -->
                <div class="card info-card shadow-sm mb-4">
                    <div class="card-header">
                        <i class="bi bi-geo-alt"></i> Informasi Pickup
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">Alamat Pickup</label>
                                <p class="mb-0 fw-bold"><?= esc($order['pickup_address'] ?? '-') ?></p>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="text-muted small">Tanggal Pickup</label>
                                <p class="mb-0 fw-bold">
                                    <?= !empty($order['pickup_date']) ? date('d M Y', strtotime($order['pickup_date'])) : '-' ?>
                                </p>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="text-muted small">Waktu Pickup</label>
                                <p class="mb-0 fw-bold">
                                    <?= !empty($order['pickup_time']) ? date('H:i', strtotime($order['pickup_time'])) : '-' ?>
                                </p>
                            </div>
                            <?php if (!empty($order['special_notes'])): ?>
                            <div class="col-12">
                                <label class="text-muted small">Catatan Khusus</label>
                                <div class="alert alert-light border mb-0">
                                    <?= esc($order['special_notes']) ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Order History -->
                <?php if (!empty($order['histories']) && is_array($order['histories']) && count($order['histories']) > 0): ?>
                <div class="card info-card shadow-sm mb-4">
                    <div class="card-header">
                        <i class="bi bi-clock-history"></i> Riwayat Pesanan
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <?php foreach ($order['histories'] as $history): ?>
                                <div class="d-flex mb-3">
                                    <div class="me-3">
                                        <div class="bg-primary rounded-circle p-2 text-white" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                                            <i class="bi bi-check"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <strong><?= esc($history['status'] ?? $history['action'] ?? '-') ?></strong>
                                        <br>
                                        <small class="text-muted">
                                            <?= !empty($history['created_at']) ? date('d M Y H:i', strtotime($history['created_at'])) : '-' ?>
                                        </small>
                                        <?php if (!empty($history['notes'])): ?>
                                            <br><small><?= esc($history['notes']) ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Right Column -->
            <div class="col-lg-4">
                
                <!-- Customer Info -->
                <div class="card info-card shadow-sm mb-4">
                    <div class="card-header">
                        <i class="bi bi-person"></i> Informasi Customer
                    </div>
                    <div class="card-body">
                        <?php if (!empty($order['user_id'])): ?>
                        <div class="mb-3">
                            <label class="text-muted small">User ID</label>
                            <p class="mb-0 fw-bold">#<?= $order['user_id'] ?></p>
                        </div>
                        <?php endif; ?>
                        <div class="mb-3">
                            <label class="text-muted small">Email</label>
                            <p class="mb-0 fw-bold"><?= esc($order['guest_email'] ?? '-') ?></p>
                        </div>
                        <div>
                            <label class="text-muted small">No. Telepon</label>
                            <p class="mb-0 fw-bold"><?= esc($order['guest_phone'] ?? '-') ?></p>
                        </div>
                    </div>
                </div>

                <!-- Payment Info -->
                <div class="card info-card shadow-sm mb-4">
                    <div class="card-header">
                        <i class="bi bi-credit-card"></i> Informasi Pembayaran
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="text-muted small">Metode Pembayaran</label>
                            <p class="mb-0 fw-bold text-uppercase"><?= esc($order['payment_method'] ?? '-') ?></p>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small">Status Pembayaran</label>
                            <p class="mb-0">
                                <span class="badge bg-<?= $paymentStatus === 'paid' ? 'success' : 'warning' ?>">
                                    <?= $paymentStatus === 'paid' ? 'Lunas' : 'Belum Bayar' ?>
                                </span>
                            </p>
                        </div>
                        <?php if (!empty($order['payment_confirmed_at'])): ?>
                        <div>
                            <label class="text-muted small">Dikonfirmasi</label>
                            <p class="mb-0"><?= date('d M Y H:i', strtotime($order['payment_confirmed_at'])) ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Branch Info -->
                <?php if (!empty($order['branch'])): ?>
                <div class="card info-card shadow-sm mb-4">
                    <div class="card-header">
                        <i class="bi bi-shop"></i> Cabang
                    </div>
                    <div class="card-body">
                        <p class="mb-1 fw-bold"><?= esc($order['branch']['name'] ?? '-') ?></p>
                        <small class="text-muted"><?= esc($order['branch']['address'] ?? '') ?></small>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Admin Actions -->
                <?php if (!$isCompleted && !$isCancelled): ?>
                <div class="card info-card shadow-sm border-primary">
                    <div class="card-header bg-primary text-white">
                        <i class="bi bi-gear"></i> Admin Actions
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Update Status</label>
                            <select class="form-select" id="newStatus">
                                <option value="">-- Pilih Status --</option>
                                <option value="waiting_pickup" <?= $status === 'waiting_pickup' ? 'disabled' : '' ?>>Waiting Pickup</option>
                                <option value="picked_up" <?= $status === 'picked_up' ? 'disabled' : '' ?>>Picked Up</option>
                                <option value="in_process" <?= $status === 'in_process' ? 'disabled' : '' ?>>In Process</option>
                                <option value="washing" <?= $status === 'washing' ? 'disabled' : '' ?>>Washing</option>
                                <option value="drying" <?= $status === 'drying' ? 'disabled' : '' ?>>Drying</option>
                                <option value="quality_check" <?= $status === 'quality_check' ? 'disabled' : '' ?>>Quality Check</option>
                                <option value="ready" <?= $status === 'ready' ? 'disabled' : '' ?>>Ready</option>
                                <option value="on_delivery" <?= $status === 'on_delivery' ? 'disabled' : '' ?>>On Delivery</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Catatan (Opsional)</label>
                            <textarea class="form-control" id="statusNotes" rows="2" placeholder="Tambah catatan..."></textarea>
                        </div>
                        <button class="btn btn-primary w-100 mb-2" onclick="updateStatus()">
                            <i class="bi bi-check-circle"></i> Update Status
                        </button>
                        <button class="btn btn-outline-danger w-100" onclick="cancelOrder()">
                            <i class="bi bi-x-circle"></i> Batalkan Pesanan
                        </button>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</section>

<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const BASE_URL = '<?= base_url() ?>';
const ORDER_NUMBER = '<?= esc($order['order_number'] ?? '') ?>';

async function updateStatus() {
    const newStatus = document.getElementById('newStatus').value;
    const notes = document.getElementById('statusNotes').value;
    
    if (!newStatus) {
        Swal.fire({
            icon: 'warning',
            title: 'Oops!',
            text: 'Pilih status baru terlebih dahulu'
        });
        return;
    }
    
    const result = await Swal.fire({
        title: 'Update Status?',
        text: `Ubah status pesanan menjadi "${newStatus}"?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Update',
        cancelButtonText: 'Batal'
    });
    
    if (!result.isConfirmed) return;
    
    try {
        Swal.fire({
            title: 'Memproses...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });
        
        const response = await fetch(`${BASE_URL}/admin/orders/${ORDER_NUMBER}/status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ 
                status: newStatus, 
                notes: notes 
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message || 'Status berhasil diupdate'
            }).then(() => {
                window.location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: data.message || 'Gagal update status'
            });
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan saat update status'
        });
    }
}

async function cancelOrder() {
    const result = await Swal.fire({
        title: 'Batalkan Pesanan?',
        text: 'Pesanan yang dibatalkan tidak dapat dikembalikan!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
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
        
        const response = await fetch(`${BASE_URL}/admin/orders/${ORDER_NUMBER}/cancel`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ 
                reason: result.value || 'Dibatalkan oleh admin'
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Pesanan berhasil dibatalkan'
            }).then(() => {
                window.location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: data.message || 'Gagal membatalkan pesanan'
            });
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan'
        });
    }
}
</script>
<?= $this->endSection() ?>
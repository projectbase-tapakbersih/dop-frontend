<?= $this->extend('layouts/main') ?>

<?php
if (!function_exists('format_rupiah')) {
    function format_rupiah($number) {
        return 'Rp ' . number_format((float)$number, 0, ',', '.');
    }
}
?>

<?= $this->section('content') ?>

<!-- Header -->
<section class="py-4 bg-success text-white">
    <div class="container">
        <h2 class="mb-0 fw-bold"><i class="bi bi-box-seam"></i> Detail Pesanan</h2>
        <p class="mb-0 mt-1 opacity-75">Pesanan sebagai tamu</p>
    </div>
</section>

<?php if (!empty($error)): ?>
    <div class="container mt-4">
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle"></i> <?= esc($error) ?>
        </div>
        <div class="text-center mt-4">
            <a href="<?= base_url('guest/track') ?>" class="btn btn-primary">
                <i class="bi bi-search"></i> Lacak Pesanan Lain
            </a>
        </div>
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
    'waiting_pickup' => ['badge' => 'warning', 'label' => 'Menunggu Pickup'],
    'picked_up' => ['badge' => 'info', 'label' => 'Sudah Dijemput'],
    'in_process' => ['badge' => 'primary', 'label' => 'Sedang Diproses'],
    'washing' => ['badge' => 'primary', 'label' => 'Dicuci'],
    'drying' => ['badge' => 'primary', 'label' => 'Dikeringkan'],
    'quality_check' => ['badge' => 'info', 'label' => 'Quality Check'],
    'ready' => ['badge' => 'success', 'label' => 'Siap Diantar'],
    'on_delivery' => ['badge' => 'info', 'label' => 'Dalam Pengiriman'],
    'completed' => ['badge' => 'success', 'label' => 'Selesai'],
    'cancelled' => ['badge' => 'danger', 'label' => 'Dibatalkan']
];
$currentStatus = $statusConfig[$status] ?? ['badge' => 'secondary', 'label' => ucfirst($status)];
?>

<section class="py-4">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                
                <!-- Order Card -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body text-center py-4">
                        <span class="badge bg-<?= $currentStatus['badge'] ?> fs-6 mb-3"><?= $currentStatus['label'] ?></span>
                        <h3 class="fw-bold text-primary"><?= esc($order['order_number']) ?></h3>
                        <p class="text-muted mb-0">
                            <i class="bi bi-calendar"></i> <?= date('d F Y, H:i', strtotime($order['created_at'])) ?>
                        </p>
                    </div>
                </div>

                <?php if ($status === 'cancelled'): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-x-circle"></i> <strong>Pesanan Dibatalkan</strong>
                    <?php if (!empty($order['cancellation_reason'])): ?>
                        <br>Alasan: <?= esc($order['cancellation_reason']) ?>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <!-- Items -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header"><h6 class="mb-0 fw-bold">Item Pesanan</h6></div>
                    <div class="card-body p-0">
                        <table class="table mb-0">
                            <tbody>
                                <?php if (!empty($order['items'])): ?>
                                    <?php foreach ($order['items'] as $item): ?>
                                        <tr>
                                            <td>
                                                <strong><?= esc($item['service']['name'] ?? 'Service') ?></strong>
                                                <br><small class="text-muted">x<?= $item['quantity'] ?? 1 ?></small>
                                            </td>
                                            <td class="text-end"><?= format_rupiah($item['price'] ?? 0) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td class="fw-bold">Total</td>
                                    <td class="text-end fw-bold text-primary fs-5"><?= format_rupiah($order['total_amount'] ?? 0) ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Details -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header"><h6 class="mb-0 fw-bold">Detail</h6></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6 mb-3">
                                <small class="text-muted">Tipe Sepatu</small>
                                <p class="mb-0 fw-bold"><?= esc($order['shoe_type'] ?? '-') ?></p>
                            </div>
                            <div class="col-6 mb-3">
                                <small class="text-muted">Ukuran</small>
                                <p class="mb-0 fw-bold"><?= esc($order['shoe_size'] ?? '-') ?></p>
                            </div>
                            <div class="col-12 mb-3">
                                <small class="text-muted">Alamat Pickup</small>
                                <p class="mb-0"><?= esc($order['pickup_address'] ?? '-') ?></p>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Tanggal Pickup</small>
                                <p class="mb-0"><?= !empty($order['pickup_date']) ? date('d M Y', strtotime($order['pickup_date'])) : '-' ?></p>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Waktu</small>
                                <p class="mb-0"><?= !empty($order['pickup_time']) ? date('H:i', strtotime($order['pickup_time'])) : '-' ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header"><h6 class="mb-0 fw-bold">Pembayaran</h6></div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="mb-1 text-uppercase"><?= esc($order['payment_method'] ?? '-') ?></p>
                                <span class="badge bg-<?= $paymentStatus === 'paid' ? 'success' : 'warning' ?>">
                                    <?= $paymentStatus === 'paid' ? 'Lunas' : 'Belum Bayar' ?>
                                </span>
                            </div>
                            <?php if ($paymentStatus !== 'paid' && !in_array($status, ['completed', 'cancelled'])): ?>
                                <a href="<?= base_url('payment/' . $order['order_number']) ?>" class="btn btn-success">
                                    <i class="bi bi-credit-card"></i> Bayar Sekarang
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="d-flex gap-2 justify-content-center">
                    <a href="<?= base_url() ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-house"></i> Beranda
                    </a>
                    <?php if ($status === 'waiting_pickup' && $paymentStatus !== 'paid'): ?>
                        <button class="btn btn-outline-danger" onclick="cancelOrder()">
                            <i class="bi bi-x-circle"></i> Batalkan
                        </button>
                    <?php endif; ?>
                </div>

            </div>
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
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        confirmButtonText: 'Ya, Batalkan',
        cancelButtonText: 'Tidak'
    });
    
    if (!result.isConfirmed) return;
    
    try {
        Swal.fire({ title: 'Memproses...', didOpen: () => Swal.showLoading() });
        
        const response = await fetch('<?= base_url('guest/order/' . ($order['order_number'] ?? '') . '/cancel') ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        });
        
        const data = await response.json();
        
        Swal.fire({
            icon: data.success ? 'success' : 'error',
            title: data.success ? 'Berhasil!' : 'Gagal!',
            text: data.message
        }).then(() => { if (data.success) window.location.reload(); });
    } catch (error) {
        Swal.fire({ icon: 'error', title: 'Error!' });
    }
}
</script>
<?= $this->endSection() ?>
<?= $this->extend('layouts/main') ?>

<?php
// Helper function
if (!function_exists('format_rupiah')) {
    function format_rupiah($number) {
        return 'Rp ' . number_format((float)$number, 0, ',', '.');
    }
}
?>

<?= $this->section('content') ?>

<!-- Page Header -->
<section class="py-4 bg-primary text-white">
    <div class="container">
        <h2 class="mb-0 fw-bold">
            <i class="bi bi-box-seam"></i> Pesanan Saya
        </h2>
        <p class="mb-0 mt-1 opacity-75">Lihat dan kelola semua pesanan Anda</p>
    </div>
</section>

<section class="py-4">
    <div class="container">
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle"></i> <?= esc($error) ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle"></i> <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (empty($orders)): ?>
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="bi bi-inbox fs-1 text-muted mb-3 d-block"></i>
                    <h4 class="text-muted">Belum Ada Pesanan</h4>
                    <p class="text-muted mb-4">Anda belum memiliki pesanan. Mulai pesan layanan sekarang!</p>
                    <a href="<?= base_url('order/create') ?>" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> Buat Pesanan Baru
                    </a>
                </div>
            </div>
        <?php else: ?>
            
            <!-- Filter -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control" id="searchInput" placeholder="Cari nomor pesanan...">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select" id="statusFilter">
                                <option value="">Semua Status</option>
                                <option value="waiting_pickup">Waiting Pickup</option>
                                <option value="in_process">In Process</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Orders List -->
            <div class="row g-4" id="ordersList">
                <?php foreach ($orders as $order): ?>
                    <?php
                    $status = $order['order_status'] ?? 'pending';
                    $paymentStatus = $order['payment_status'] ?? 'pending';
                    
                    $statusConfig = [
                        'waiting_pickup' => ['badge' => 'warning', 'label' => 'Menunggu Pickup', 'icon' => 'hourglass-split'],
                        'picked_up' => ['badge' => 'info', 'label' => 'Sudah Dijemput', 'icon' => 'truck'],
                        'in_process' => ['badge' => 'primary', 'label' => 'Diproses', 'icon' => 'gear'],
                        'processing' => ['badge' => 'primary', 'label' => 'Diproses', 'icon' => 'gear'],
                        'washing' => ['badge' => 'primary', 'label' => 'Dicuci', 'icon' => 'droplet'],
                        'drying' => ['badge' => 'primary', 'label' => 'Dikeringkan', 'icon' => 'wind'],
                        'quality_check' => ['badge' => 'info', 'label' => 'Quality Check', 'icon' => 'check2-square'],
                        'ready' => ['badge' => 'success', 'label' => 'Siap Diantar', 'icon' => 'check-circle'],
                        'on_delivery' => ['badge' => 'info', 'label' => 'Diantar', 'icon' => 'bicycle'],
                        'completed' => ['badge' => 'success', 'label' => 'Selesai', 'icon' => 'trophy'],
                        'cancelled' => ['badge' => 'danger', 'label' => 'Dibatalkan', 'icon' => 'x-circle']
                    ];
                    $statusInfo = $statusConfig[$status] ?? ['badge' => 'secondary', 'label' => ucfirst($status), 'icon' => 'circle'];
                    
                    // Get service names
                    $services = [];
                    if (!empty($order['items'])) {
                        foreach ($order['items'] as $item) {
                            $services[] = $item['service']['name'] ?? 'Service';
                        }
                    }
                    ?>
                    <div class="col-12 order-item" 
                         data-status="<?= $status ?>" 
                         data-search="<?= strtolower($order['order_number'] ?? '') ?>">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="badge bg-<?= $statusInfo['badge'] ?> me-2">
                                                <i class="bi bi-<?= $statusInfo['icon'] ?>"></i> <?= $statusInfo['label'] ?>
                                            </span>
                                            <span class="badge bg-<?= $paymentStatus === 'paid' ? 'success' : 'warning' ?>">
                                                <?= $paymentStatus === 'paid' ? 'Lunas' : 'Belum Bayar' ?>
                                            </span>
                                        </div>
                                        <h5 class="fw-bold text-primary mb-1"><?= esc($order['order_number']) ?></h5>
                                        <p class="text-muted mb-0">
                                            <small>
                                                <i class="bi bi-calendar"></i> <?= date('d M Y, H:i', strtotime($order['created_at'])) ?>
                                            </small>
                                        </p>
                                    </div>
                                    <div class="col-md-3">
                                        <small class="text-muted">Layanan:</small>
                                        <p class="mb-0 fw-bold"><?= esc(implode(', ', array_slice($services, 0, 2))) ?></p>
                                        <small class="text-muted">Total:</small>
                                        <p class="mb-0 fw-bold text-primary"><?= format_rupiah($order['total_amount'] ?? 0) ?></p>
                                    </div>
                                    <div class="col-md-3 text-md-end mt-3 mt-md-0">
                                        <a href="<?= base_url('user/orders/' . $order['order_number']) ?>" class="btn btn-outline-primary">
                                            <i class="bi bi-eye"></i> Detail
                                        </a>
                                        <?php if ($paymentStatus !== 'paid' && !in_array($status, ['completed', 'cancelled'])): ?>
                                            <a href="<?= base_url('payment/' . $order['order_number']) ?>" class="btn btn-success">
                                                <i class="bi bi-credit-card"></i> Bayar
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
        <?php endif; ?>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.getElementById('searchInput')?.addEventListener('input', filterOrders);
document.getElementById('statusFilter')?.addEventListener('change', filterOrders);

function filterOrders() {
    const search = document.getElementById('searchInput').value.toLowerCase();
    const status = document.getElementById('statusFilter').value;
    
    document.querySelectorAll('.order-item').forEach(item => {
        const itemSearch = item.dataset.search;
        const itemStatus = item.dataset.status;
        
        let show = true;
        if (search && !itemSearch.includes(search)) show = false;
        if (status) {
            if (status === 'in_process') {
                const processStatuses = ['in_process', 'picked_up', 'washing', 'drying', 'quality_check', 'ready', 'on_delivery'];
                if (!processStatuses.includes(itemStatus)) show = false;
            } else if (itemStatus !== status) {
                show = false;
            }
        }
        
        item.style.display = show ? '' : 'none';
    });
}
</script>
<?= $this->endSection() ?>
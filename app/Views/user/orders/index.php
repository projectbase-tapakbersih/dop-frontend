<?= $this->extend('layouts/main') ?>

<?php
helper('format');

// Status configuration sesuai Laravel database enum
$statusOptions = [
    'waiting_pickup' => 'Menunggu Penjemputan',
    'dalam_penjemputan' => 'Dalam Penjemputan',
    'in_progress' => 'Sedang Diproses',
    'ready_for_delivery' => 'Siap Diantar',
    'on_delivery' => 'Dalam Pengiriman',
    'completed' => 'Selesai',
    'cancelled' => 'Dibatalkan'
];

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

$orders = $orders ?? [];
?>

<?= $this->section('content') ?>

<!-- Page Header -->
<section class="py-4 bg-primary text-white">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h2 class="mb-0 fw-bold">
                    <i class="bi bi-box-seam"></i> Pesanan Saya
                </h2>
                <p class="mb-0 mt-1 opacity-75">Lihat dan kelola semua pesanan Anda</p>
            </div>
            <a href="<?= base_url('order/checkout') ?>" class="btn btn-light">
                <i class="bi bi-plus-lg"></i> Pesanan Baru
            </a>
        </div>
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
                    <a href="<?= base_url('order/checkout') ?>" class="btn btn-primary">
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
                                <?php foreach ($statusOptions as $value => $label): ?>
                                    <option value="<?= $value ?>"><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Orders List -->
            <div class="row g-4" id="ordersList">
                <?php foreach ($orders as $order): ?>
                    <?php
                    $status = $order['order_status'] ?? 'waiting_pickup';
                    $paymentStatus = $order['payment_status'] ?? 'pending';
                    
                    // Get service names
                    $services = [];
                    if (!empty($order['items'])) {
                        foreach ($order['items'] as $item) {
                            $serviceName = $item['service']['name'] ?? $item['service_name'] ?? 'Layanan';
                            $services[] = $serviceName;
                        }
                    }
                    ?>
                    <div class="col-12 order-item" 
                         data-status="<?= esc($status) ?>" 
                         data-search="<?= strtolower($order['order_number'] ?? '') ?>">
                        <div class="card shadow-sm hover-shadow">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-5">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <span class="badge bg-<?= getStatusBadgeClass($status) ?>">
                                                <i class="bi bi-<?= getStatusIcon($status) ?>"></i> <?= getStatusLabel($status) ?>
                                            </span>
                                            <span class="badge bg-<?= $paymentStatus === 'paid' ? 'success' : 'warning text-dark' ?>">
                                                <?= $paymentStatus === 'paid' ? 'Lunas' : 'Belum Bayar' ?>
                                            </span>
                                        </div>
                                        <h5 class="fw-bold text-primary mb-1"><?= esc($order['order_number'] ?? '-') ?></h5>
                                        <p class="text-muted mb-0 small">
                                            <i class="bi bi-calendar"></i> 
                                            <?= !empty($order['created_at']) ? date('d M Y, H:i', strtotime($order['created_at'])) : '-' ?>
                                        </p>
                                    </div>
                                    <div class="col-md-4 my-2 my-md-0">
                                        <small class="text-muted d-block">Layanan:</small>
                                        <p class="mb-1 fw-semibold">
                                            <?= esc(implode(', ', array_slice($services, 0, 2))) ?>
                                            <?php if (count($services) > 2): ?>
                                                <span class="text-muted">+<?= count($services) - 2 ?> lainnya</span>
                                            <?php endif; ?>
                                        </p>
                                        <small class="text-muted d-block">Total:</small>
                                        <p class="mb-0 fw-bold text-primary fs-5"><?= format_rupiah($order['total_amount'] ?? 0) ?></p>
                                    </div>
                                    <div class="col-md-3 text-md-end mt-3 mt-md-0">
                                        <div class="d-flex gap-2 justify-content-md-end">
                                            <a href="<?= base_url('user/orders/' . ($order['order_number'] ?? '')) ?>" 
                                               class="btn btn-outline-primary">
                                                <i class="bi bi-eye"></i> Detail
                                            </a>
                                            <?php if ($paymentStatus !== 'paid' && !in_array($status, ['completed', 'cancelled'])): ?>
                                                <a href="<?= base_url('payment/' . ($order['order_number'] ?? '')) ?>" 
                                                   class="btn btn-success">
                                                    <i class="bi bi-credit-card"></i> Bayar
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- No Results Message -->
            <div id="noResults" class="text-center py-5 d-none">
                <i class="bi bi-search fs-1 text-muted d-block mb-2"></i>
                <p class="text-muted">Tidak ada pesanan yang ditemukan</p>
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
    
    let visibleCount = 0;
    
    document.querySelectorAll('.order-item').forEach(item => {
        const itemSearch = item.dataset.search;
        const itemStatus = item.dataset.status;
        
        let show = true;
        if (search && !itemSearch.includes(search)) show = false;
        if (status && itemStatus !== status) show = false;
        
        item.style.display = show ? '' : 'none';
        if (show) visibleCount++;
    });
    
    // Show/hide no results message
    const noResults = document.getElementById('noResults');
    if (noResults) {
        noResults.classList.toggle('d-none', visibleCount > 0);
    }
}
</script>
<?= $this->endSection() ?>
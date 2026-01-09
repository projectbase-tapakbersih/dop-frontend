<?= $this->extend('layouts/main') ?>

<?php
helper('format');

$user = $user ?? [];
$stats = $stats ?? [];
$orders = $orders ?? [];

// Status configuration sesuai Laravel database enum
function getStatusColor($status) {
    $colors = [
        'waiting_pickup' => 'warning',
        'dalam_penjemputan' => 'info',
        'in_progress' => 'primary',
        'ready_for_delivery' => 'success',
        'on_delivery' => 'info',
        'completed' => 'success',
        'cancelled' => 'danger'
    ];
    return $colors[$status] ?? 'secondary';
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
?>

<?= $this->section('content') ?>

<!-- Welcome Section -->
<section class="py-4 bg-primary text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="mb-1"><i class="bi bi-person-circle"></i> Selamat Datang, <?= esc($user['name'] ?? 'User') ?>!</h2>
                <p class="mb-0 opacity-75">Kelola pesanan dan akun Anda di sini</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="<?= base_url('order/create') ?>" class="btn btn-light">
                    <i class="bi bi-plus-lg"></i> Buat Pesanan Baru
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Stats Cards -->
<section class="py-4">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-3 col-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="display-6 text-primary mb-2">
                            <i class="bi bi-bag"></i>
                        </div>
                        <h3 class="mb-1"><?= $stats['total_orders'] ?? 0 ?></h3>
                        <p class="text-muted mb-0">Total Pesanan</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="display-6 text-warning mb-2">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                        <h3 class="mb-1"><?= $stats['pending_orders'] ?? 0 ?></h3>
                        <p class="text-muted mb-0">Dalam Proses</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="display-6 text-success mb-2">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <h3 class="mb-1"><?= $stats['completed_orders'] ?? 0 ?></h3>
                        <p class="text-muted mb-0">Selesai</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="display-6 text-info mb-2">
                            <i class="bi bi-wallet2"></i>
                        </div>
                        <h3 class="mb-1"><?= format_rupiah($stats['total_spent'] ?? 0) ?></h3>
                        <p class="text-muted mb-0">Total Transaksi</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Quick Actions & Recent Orders -->
<section class="py-4">
    <div class="container">
        <div class="row g-4">
            <!-- Quick Actions -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0">
                        <h5 class="mb-0"><i class="bi bi-lightning"></i> Aksi Cepat</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="<?= base_url('order/create') ?>" class="btn btn-primary">
                                <i class="bi bi-plus-lg"></i> Buat Pesanan Baru
                            </a>
                            <a href="<?= base_url('user/orders') ?>" class="btn btn-outline-primary">
                                <i class="bi bi-list"></i> Lihat Semua Pesanan
                            </a>
                            <a href="<?= base_url('user/profile') ?>" class="btn btn-outline-secondary">
                                <i class="bi bi-person"></i> Edit Profil
                            </a>
                            <a href="<?= base_url('services') ?>" class="btn btn-outline-info">
                                <i class="bi bi-grid"></i> Lihat Layanan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Orders -->
            <div class="col-md-8">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-clock-history"></i> Pesanan Terakhir</h5>
                        <a href="<?= base_url('user/orders') ?>" class="btn btn-sm btn-link">Lihat Semua</a>
                    </div>
                    <div class="card-body p-0">
                        <?php if (!empty($orders)): ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($orders as $order): ?>
                                    <?php
                                    $orderStatus = $order['order_status'] ?? '';
                                    $paymentStatus = $order['payment_status'] ?? 'pending';
                                    ?>
                                    <a href="<?= base_url('user/orders/' . ($order['order_number'] ?? '')) ?>" 
                                       class="list-group-item list-group-item-action py-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong class="text-primary"><?= esc($order['order_number'] ?? '-') ?></strong>
                                                <br>
                                                <small class="text-muted">
                                                    <i class="bi bi-calendar"></i> 
                                                    <?= !empty($order['created_at']) ? date('d M Y, H:i', strtotime($order['created_at'])) : '-' ?>
                                                </small>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge bg-<?= getStatusColor($orderStatus) ?>">
                                                    <i class="bi bi-<?= getStatusIcon($orderStatus) ?>"></i>
                                                    <?= getStatusLabel($orderStatus) ?>
                                                </span>
                                                <br>
                                                <small class="fw-bold"><?= format_rupiah($order['total_amount'] ?? 0) ?></small>
                                                <?php if ($paymentStatus !== 'paid'): ?>
                                                    <span class="badge bg-warning text-dark ms-1">Belum Bayar</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center text-muted py-5">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                <p class="mb-3">Belum ada pesanan</p>
                                <a href="<?= base_url('order/checkout') ?>" class="btn btn-primary btn-sm">
                                    <i class="bi bi-plus-lg"></i> Buat Pesanan Pertama
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Info Section -->
<section class="py-4">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm bg-light">
                    <div class="card-body">
                        <h6 class="fw-bold"><i class="bi bi-info-circle text-primary"></i> Alur Pesanan</h6>
                        <div class="d-flex flex-wrap gap-1 mt-3">
                            <span class="badge bg-warning text-dark">Menunggu Pickup</span>
                            <i class="bi bi-arrow-right text-muted"></i>
                            <span class="badge bg-info">Dijemput</span>
                            <i class="bi bi-arrow-right text-muted"></i>
                            <span class="badge bg-primary">Diproses</span>
                            <i class="bi bi-arrow-right text-muted"></i>
                            <span class="badge bg-success">Siap</span>
                            <i class="bi bi-arrow-right text-muted"></i>
                            <span class="badge bg-info">Dikirim</span>
                            <i class="bi bi-arrow-right text-muted"></i>
                            <span class="badge bg-success">Selesai</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm bg-light">
                    <div class="card-body">
                        <h6 class="fw-bold"><i class="bi bi-headset text-primary"></i> Butuh Bantuan?</h6>
                        <p class="text-muted mb-2">Hubungi customer service kami:</p>
                        <div class="d-flex gap-2">
                            <a href="https://wa.me/6281234567890" class="btn btn-success btn-sm" target="_blank">
                                <i class="bi bi-whatsapp"></i> WhatsApp
                            </a>
                            <a href="mailto:support@tapakbersih.com" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-envelope"></i> Email
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
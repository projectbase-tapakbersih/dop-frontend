<?= $this->extend('layouts/main') ?>

<?php
if (!function_exists('format_rupiah')) {
    function format_rupiah($number) {
        return 'Rp ' . number_format((float)$number, 0, ',', '.');
    }
}

$user = $user ?? [];
$stats = $stats ?? [];
$orders = $orders ?? [];
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
            <div class="col-md-3">
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
            <div class="col-md-3">
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
            <div class="col-md-3">
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
            <div class="col-md-3">
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

<!-- Quick Actions -->
<section class="py-4">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-6">
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
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-clock-history"></i> Pesanan Terakhir</h5>
                        <a href="<?= base_url('user/orders') ?>" class="btn btn-sm btn-link">Lihat Semua</a>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($orders)): ?>
                            <?php $recentOrders = array_slice($orders, 0, 3); ?>
                            <?php foreach ($recentOrders as $order): ?>
                                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                    <div>
                                        <strong><?= esc($order['order_number'] ?? '-') ?></strong>
                                        <br>
                                        <small class="text-muted"><?= esc($order['created_at'] ?? '-') ?></small>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-<?= getStatusColor($order['order_status'] ?? '') ?>">
                                            <?= getStatusLabel($order['order_status'] ?? '') ?>
                                        </span>
                                        <br>
                                        <small><?= format_rupiah($order['total_amount'] ?? 0) ?></small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1"></i>
                                <p class="mb-0 mt-2">Belum ada pesanan</p>
                                <a href="<?= base_url('order/create') ?>" class="btn btn-primary btn-sm mt-2">
                                    Buat Pesanan Pertama
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
function getStatusColor($status) {
    $colors = [
        'pending' => 'warning',
        'waiting_pickup' => 'info',
        'on_the_way_to_workshop' => 'info',
        'in_process' => 'primary',
        'cleaning_done' => 'success',
        'on_the_way_to_customer' => 'info',
        'delivered' => 'success',
        'completed' => 'success',
        'cancelled' => 'danger'
    ];
    return $colors[$status] ?? 'secondary';
}

function getStatusLabel($status) {
    $labels = [
        'pending' => 'Menunggu',
        'waiting_pickup' => 'Menunggu Pickup',
        'on_the_way_to_workshop' => 'Dalam Perjalanan',
        'in_process' => 'Sedang Diproses',
        'cleaning_done' => 'Selesai Dicuci',
        'on_the_way_to_customer' => 'Dalam Pengiriman',
        'delivered' => 'Terkirim',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan'
    ];
    return $labels[$status] ?? ucfirst($status);
}
?>

<?= $this->endSection() ?>
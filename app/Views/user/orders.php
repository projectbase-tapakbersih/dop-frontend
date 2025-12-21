<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<section class="py-4 bg-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-1 fw-bold">Pesanan Saya</h2>
                <p class="text-muted mb-0">Kelola dan lacak pesanan Anda</p>
            </div>
            <div>
                <a href="<?= base_url('user/profile') ?>" class="btn btn-outline-secondary me-2">
                    <i class="bi bi-person"></i> Profile
                </a>
                <a href="<?= base_url('order/create') ?>" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Buat Pesanan Baru
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Orders Content -->
<section class="py-5">
    <div class="container">
        
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle"></i> <?= esc($error) ?>
            </div>
        <?php endif; ?>

        <?php if (empty($orders)): ?>
            <!-- Empty State -->
            <div class="text-center py-5">
                <i class="bi bi-inbox display-1 text-muted"></i>
                <h3 class="mt-3">Belum Ada Pesanan</h3>
                <p class="text-muted mb-4">Anda belum memiliki pesanan. Mulai pesan layanan kami sekarang!</p>
                <a href="<?= base_url('order/create') ?>" class="btn btn-primary btn-lg">
                    <i class="bi bi-bag-plus"></i> Buat Pesanan Pertama
                </a>
            </div>

        <?php else: ?>
            
            <!-- Filter Tabs -->
            <ul class="nav nav-tabs mb-4" id="orderTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button">
                        Semua Pesanan
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button">
                        Menunggu
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="processing-tab" data-bs-toggle="tab" data-bs-target="#processing" type="button">
                        Diproses
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed" type="button">
                        Selesai
                    </button>
                </li>
            </ul>

            <!-- Orders List -->
            <div class="tab-content" id="orderTabContent">
                <div class="tab-pane fade show active" id="all" role="tabpanel">
                    <div class="row g-3">
                        <?php foreach ($orders as $order): ?>
                            <div class="col-12">
                                <div class="card shadow-sm order-card" data-status="<?= esc($order['status'] ?? 'pending') ?>">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <!-- Order Number & Date -->
                                            <div class="col-md-3">
                                                <h6 class="fw-bold mb-1">
                                                    <?= esc($order['order_number'] ?? 'N/A') ?>
                                                </h6>
                                                <small class="text-muted">
                                                    <i class="bi bi-calendar"></i>
                                                    <?= date('d M Y', strtotime($order['created_at'] ?? 'now')) ?>
                                                </small>
                                            </div>

                                            <!-- Service Info -->
                                            <div class="col-md-4">
                                                <p class="mb-1">
                                                    <strong><?= esc($order['service']['name'] ?? 'Layanan') ?></strong>
                                                </p>
                                                <small class="text-muted">
                                                    <i class="bi bi-geo-alt"></i>
                                                    <?= esc($order['branch']['name'] ?? 'Cabang') ?>
                                                </small>
                                            </div>

                                            <!-- Status -->
                                            <div class="col-md-2">
                                                <?php
                                                $status = $order['status'] ?? 'pending';
                                                $badgeClass = 'secondary';
                                                $statusText = ucfirst($status);

                                                switch($status) {
                                                    case 'pending':
                                                        $badgeClass = 'warning';
                                                        $statusText = 'Menunggu';
                                                        break;
                                                    case 'confirmed':
                                                        $badgeClass = 'info';
                                                        $statusText = 'Dikonfirmasi';
                                                        break;
                                                    case 'picked_up':
                                                        $badgeClass = 'primary';
                                                        $statusText = 'Dijemput';
                                                        break;
                                                    case 'processing':
                                                        $badgeClass = 'primary';
                                                        $statusText = 'Diproses';
                                                        break;
                                                    case 'completed':
                                                        $badgeClass = 'success';
                                                        $statusText = 'Selesai';
                                                        break;
                                                    case 'cancelled':
                                                        $badgeClass = 'danger';
                                                        $statusText = 'Dibatalkan';
                                                        break;
                                                }
                                                ?>
                                                <span class="badge bg-<?= $badgeClass ?> w-100">
                                                    <?= $statusText ?>
                                                </span>
                                            </div>

                                            <!-- Price -->
                                            <div class="col-md-2">
                                                <h6 class="text-primary mb-0">
                                                    <?= format_rupiah($order['total_price'] ?? 0) ?>
                                                </h6>
                                            </div>

                                            <!-- Actions -->
                                            <div class="col-md-1 text-end">
                                                <a href="<?= base_url('user/orders/' . ($order['order_number'] ?? '')) ?>" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

        <?php endif; ?>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Filter orders by status
document.querySelectorAll('#orderTabs button').forEach(button => {
    button.addEventListener('click', function() {
        const target = this.getAttribute('data-bs-target').replace('#', '');
        const cards = document.querySelectorAll('.order-card');
        
        cards.forEach(card => {
            if (target === 'all') {
                card.closest('.col-12').style.display = 'block';
            } else {
                const status = card.getAttribute('data-status');
                if (status === target || (target === 'processing' && ['confirmed', 'picked_up', 'processing'].includes(status))) {
                    card.closest('.col-12').style.display = 'block';
                } else {
                    card.closest('.col-12').style.display = 'none';
                }
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
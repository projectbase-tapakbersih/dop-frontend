<?= $this->extend('layouts/main') ?>

<?php
helper('format');

// Status options sesuai dengan Laravel database enum
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
        'waiting_pickup' => 'bg-warning text-dark',
        'dalam_penjemputan' => 'bg-info',
        'in_progress' => 'bg-primary',
        'ready_for_delivery' => 'bg-success',
        'on_delivery' => 'bg-info',
        'completed' => 'bg-success',
        'cancelled' => 'bg-danger'
    ];
    return $classes[$status] ?? 'bg-secondary';
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

function getPaymentBadgeClass($status) {
    $classes = [
        'pending' => 'bg-warning text-dark',
        'paid' => 'bg-success',
        'failed' => 'bg-danger',
        'expired' => 'bg-secondary',
        'refunded' => 'bg-info'
    ];
    return $classes[$status] ?? 'bg-secondary';
}

$order = $order ?? null;
?>

<?= $this->section('styles') ?>
<style>
    .detail-card {
        border-radius: 12px;
        border: none;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }
    .info-label {
        color: #6c757d;
        font-size: 0.875rem;
        margin-bottom: 4px;
    }
    .info-value {
        font-weight: 600;
        color: #212529;
    }
    .timeline-item {
        position: relative;
        padding-left: 30px;
        padding-bottom: 20px;
        border-left: 2px solid #e9ecef;
    }
    .timeline-item:last-child {
        border-left: 2px solid transparent;
        padding-bottom: 0;
    }
    .timeline-item::before {
        content: '';
        position: absolute;
        left: -8px;
        top: 0;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: #0d6efd;
        border: 2px solid #fff;
    }
    .timeline-item.completed::before {
        background: #198754;
    }
    .timeline-item.pending::before {
        background: #ffc107;
    }
    .status-step {
        padding: 10px;
        border-radius: 8px;
        text-align: center;
        flex: 1;
    }
    .status-step.active {
        background: #0d6efd;
        color: white;
    }
    .status-step.completed {
        background: #198754;
        color: white;
    }
    .status-step.pending {
        background: #f8f9fa;
        color: #6c757d;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<section class="py-4 bg-light">
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('admin/orders') ?>">Orders</a></li>
                        <li class="breadcrumb-item active">Detail</li>
                    </ol>
                </nav>
                <h2 class="mb-0 fw-bold">
                    <i class="bi bi-receipt"></i> Order #<?= esc($order['order_number'] ?? '-') ?>
                </h2>
            </div>
            <div>
                <a href="<?= base_url('admin/orders') ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</section>

<section class="py-4">
    <div class="container-fluid px-4">
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><i class="bi bi-exclamation-triangle"></i> <?= esc($error) ?></div>
        <?php endif; ?>

        <?php if ($order): ?>
            <div class="row g-4">
                <!-- Main Info -->
                <div class="col-lg-8">
                    <!-- Order Status Progress -->
                    <div class="card detail-card mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0 fw-bold"><i class="bi bi-diagram-3"></i> Progress Status</h5>
                        </div>
                        <div class="card-body">
                            <?php
                            $currentStatus = $order['order_status'] ?? 'waiting_pickup';
                            $statusFlow = ['waiting_pickup', 'dalam_penjemputan', 'in_progress', 'ready_for_delivery', 'on_delivery', 'completed'];
                            $currentIndex = array_search($currentStatus, $statusFlow);
                            if ($currentIndex === false) $currentIndex = -1;
                            ?>
                            
                            <div class="d-flex gap-2 flex-wrap mb-3">
                                <?php foreach ($statusFlow as $index => $status): ?>
                                    <?php
                                    $stepClass = 'pending';
                                    if ($currentStatus === 'cancelled') {
                                        $stepClass = 'pending';
                                    } elseif ($index < $currentIndex) {
                                        $stepClass = 'completed';
                                    } elseif ($index === $currentIndex) {
                                        $stepClass = 'active';
                                    }
                                    ?>
                                    <div class="status-step <?= $stepClass ?>">
                                        <small><?= getStatusLabel($status) ?></small>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <?php if ($currentStatus === 'cancelled'): ?>
                                <div class="alert alert-danger mb-0">
                                    <i class="bi bi-x-circle"></i> Order ini telah dibatalkan
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="card detail-card mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0 fw-bold"><i class="bi bi-bag"></i> Item Pesanan</h5>
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
                                                        <strong><?= esc($item['service']['name'] ?? $item['service_name'] ?? 'Layanan') ?></strong>
                                                        <?php if (!empty($item['notes'])): ?>
                                                            <br><small class="text-muted"><?= esc($item['notes']) ?></small>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-center"><?= $item['quantity'] ?? 1 ?></td>
                                                    <td class="text-end"><?= format_rupiah($item['price'] ?? 0) ?></td>
                                                    <td class="text-end"><?= format_rupiah($item['subtotal'] ?? ($item['price'] * $item['quantity'])) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-4">Tidak ada item</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                            <td class="text-end"><strong class="fs-5 text-primary"><?= format_rupiah($order['total_amount'] ?? 0) ?></strong></td>
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
                                    <h6 class="mb-0 fw-bold"><i class="bi bi-box-arrow-up"></i> Pickup</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <div class="info-label">Tanggal & Waktu</div>
                                        <div class="info-value">
                                            <?= format_tanggal($order['pickup_date'] ?? '') ?> 
                                            <?= esc($order['pickup_time'] ?? '') ?>
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
                                    <h6 class="mb-0 fw-bold"><i class="bi bi-box-arrow-down"></i> Delivery</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <div class="info-label">Tanggal & Waktu</div>
                                        <div class="info-value">
                                            <?= format_tanggal($order['delivery_date'] ?? '') ?> 
                                            <?= esc($order['delivery_time'] ?? '') ?>
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
                    <!-- Status & Actions -->
                    <div class="card detail-card mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0 fw-bold"><i class="bi bi-gear"></i> Status & Aksi</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="info-label">Status Order</div>
                                <span class="badge <?= getStatusBadgeClass($order['order_status'] ?? '') ?> fs-6">
                                    <?= getStatusLabel($order['order_status'] ?? '') ?>
                                </span>
                            </div>
                            <div class="mb-3">
                                <div class="info-label">Status Pembayaran</div>
                                <span class="badge <?= getPaymentBadgeClass($order['payment_status'] ?? '') ?> fs-6">
                                    <?= ucfirst($order['payment_status'] ?? 'pending') ?>
                                </span>
                            </div>
                            
                            <hr>
                            
                            <?php if (!in_array($order['order_status'] ?? '', ['completed', 'cancelled'])): ?>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Update Status</label>
                                    <select class="form-select mb-2" id="newStatus">
                                        <?php foreach ($statusOptions as $value => $label): ?>
                                            <option value="<?= $value ?>" <?= ($order['order_status'] ?? '') === $value ? 'selected' : '' ?>>
                                                <?= $label ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <textarea class="form-control mb-2" id="statusNotes" rows="2" placeholder="Catatan (opsional)..."></textarea>
                                    <button type="button" class="btn btn-primary w-100" onclick="updateStatus()">
                                        <i class="bi bi-check-circle"></i> Update Status
                                    </button>
                                </div>
                                
                                <hr>
                                
                                <button type="button" class="btn btn-outline-danger w-100" onclick="cancelOrder()">
                                    <i class="bi bi-x-circle"></i> Batalkan Order
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Customer Info -->
                    <div class="card detail-card mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0 fw-bold"><i class="bi bi-person"></i> Informasi Customer</h5>
                        </div>
                        <div class="card-body">
                            <?php if (isset($order['user'])): ?>
                                <div class="mb-3">
                                    <div class="info-label">Nama</div>
                                    <div class="info-value"><?= esc($order['user']['name'] ?? '-') ?></div>
                                </div>
                                <div class="mb-3">
                                    <div class="info-label">Email</div>
                                    <div class="info-value"><?= esc($order['user']['email'] ?? '-') ?></div>
                                </div>
                                <div class="mb-3">
                                    <div class="info-label">Telepon</div>
                                    <div class="info-value"><?= esc($order['user']['phone'] ?? '-') ?></div>
                                </div>
                            <?php elseif (isset($order['guest_name'])): ?>
                                <span class="badge bg-secondary mb-2">Guest Order</span>
                                <div class="mb-3">
                                    <div class="info-label">Nama</div>
                                    <div class="info-value"><?= esc($order['guest_name']) ?></div>
                                </div>
                                <div class="mb-3">
                                    <div class="info-label">Email</div>
                                    <div class="info-value"><?= esc($order['guest_email'] ?? '-') ?></div>
                                </div>
                                <div class="mb-3">
                                    <div class="info-label">Telepon</div>
                                    <div class="info-value"><?= esc($order['guest_phone'] ?? '-') ?></div>
                                </div>
                            <?php else: ?>
                                <p class="text-muted">Tidak ada informasi customer</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Branch Info -->
                    <div class="card detail-card">
                        <div class="card-header bg-white">
                            <h5 class="mb-0 fw-bold"><i class="bi bi-shop"></i> Cabang</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="info-label">Nama Cabang</div>
                                <div class="info-value"><?= esc($order['branch']['name'] ?? $order['branch_name'] ?? '-') ?></div>
                            </div>
                            <?php if (isset($order['branch']['address'])): ?>
                                <div>
                                    <div class="info-label">Alamat</div>
                                    <div class="info-value"><?= esc($order['branch']['address']) ?></div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="bi bi-exclamation-circle fs-1 text-muted mb-3 d-block"></i>
                <h4 class="text-muted">Order Tidak Ditemukan</h4>
                <a href="<?= base_url('admin/orders') ?>" class="btn btn-primary mt-3">
                    <i class="bi bi-arrow-left"></i> Kembali ke Daftar Order
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const BASE_URL = '<?= base_url() ?>';
const ORDER_NUMBER = '<?= esc($order['order_number'] ?? '') ?>';

// Update Status
async function updateStatus() {
    const newStatus = document.getElementById('newStatus').value;
    const notes = document.getElementById('statusNotes').value;
    
    if (!newStatus) {
        Swal.fire({ icon: 'warning', title: 'Oops!', text: 'Pilih status baru' });
        return;
    }
    
    try {
        Swal.fire({
            title: 'Memproses...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });
        
        const formData = new FormData();
        formData.append('order_status', newStatus);
        if (notes) formData.append('notes', notes);
        
        const response = await fetch(`${BASE_URL}/admin/orders/${ORDER_NUMBER}/status`, {
            method: 'POST',
            body: new URLSearchParams(formData)
        });
        
        const result = await response.json();
        
        if (result.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: result.message,
                timer: 1500
            }).then(() => window.location.reload());
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: result.message
            });
        }
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan'
        });
    }
}

// Cancel Order
async function cancelOrder() {
    const result = await Swal.fire({
        title: 'Batalkan Order?',
        html: `Anda yakin ingin membatalkan order <strong>${ORDER_NUMBER}</strong>?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        confirmButtonText: 'Ya, Batalkan',
        cancelButtonText: 'Tidak'
    });
    
    if (!result.isConfirmed) return;
    
    try {
        Swal.fire({
            title: 'Memproses...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });
        
        const response = await fetch(`${BASE_URL}/admin/orders/${ORDER_NUMBER}/cancel`, {
            method: 'POST'
        });
        
        const data = await response.json();
        
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message,
                timer: 1500
            }).then(() => window.location.href = `${BASE_URL}/admin/orders`);
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: data.message
            });
        }
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan'
        });
    }
}
</script>
<?= $this->endSection() ?>
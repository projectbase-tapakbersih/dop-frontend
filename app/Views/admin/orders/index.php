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

function getStatusIcon($status) {
    $icons = [
        'waiting_pickup' => 'bi-clock',
        'dalam_penjemputan' => 'bi-truck',
        'in_progress' => 'bi-gear-fill',
        'ready_for_delivery' => 'bi-box-seam',
        'on_delivery' => 'bi-bicycle',
        'completed' => 'bi-check-circle-fill',
        'cancelled' => 'bi-x-circle-fill'
    ];
    return $icons[$status] ?? 'bi-circle';
}
?>

<?= $this->section('content') ?>

<!-- Page Header -->
<section class="py-4 bg-light">
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active">Orders</li>
                    </ol>
                </nav>
                <h2 class="mb-0 fw-bold"><i class="bi bi-bag-check"></i> Manajemen Order</h2>
            </div>
            <!-- Filter -->
            <div class="d-flex gap-2">
                <select class="form-select" id="filterStatus" onchange="filterOrders()">
                    <option value="">Semua Status</option>
                    <?php foreach ($statusOptions as $value => $label): ?>
                        <option value="<?= $value ?>"><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>
</section>

<section class="py-4">
    <div class="container-fluid px-4">
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><i class="bi bi-exclamation-triangle"></i> <?= esc($error) ?></div>
        <?php endif; ?>

        <!-- Stats Cards -->
        <?php
        $stats = [
            'waiting_pickup' => 0,
            'dalam_penjemputan' => 0,
            'in_progress' => 0,
            'ready_for_delivery' => 0,
            'on_delivery' => 0,
            'completed' => 0,
            'cancelled' => 0
        ];
        foreach ($orders ?? [] as $o) {
            $s = $o['order_status'] ?? '';
            if (isset($stats[$s])) {
                $stats[$s]++;
            }
        }
        ?>
        <div class="row g-3 mb-4">
            <div class="col-lg-2 col-md-4 col-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-clock fs-3 text-warning"></i>
                        <h4 class="mb-0 mt-2"><?= $stats['waiting_pickup'] ?></h4>
                        <small class="text-muted">Menunggu Pickup</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-truck fs-3 text-info"></i>
                        <h4 class="mb-0 mt-2"><?= $stats['dalam_penjemputan'] ?></h4>
                        <small class="text-muted">Dijemput</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-gear-fill fs-3 text-primary"></i>
                        <h4 class="mb-0 mt-2"><?= $stats['in_progress'] ?></h4>
                        <small class="text-muted">Diproses</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-box-seam fs-3 text-success"></i>
                        <h4 class="mb-0 mt-2"><?= $stats['ready_for_delivery'] + $stats['on_delivery'] ?></h4>
                        <small class="text-muted">Delivery</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-check-circle-fill fs-3 text-success"></i>
                        <h4 class="mb-0 mt-2"><?= $stats['completed'] ?></h4>
                        <small class="text-muted">Selesai</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-x-circle-fill fs-3 text-danger"></i>
                        <h4 class="mb-0 mt-2"><?= $stats['cancelled'] ?></h4>
                        <small class="text-muted">Dibatalkan</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-list"></i> Daftar Order</h5>
                <span class="badge bg-primary"><?= count($orders ?? []) ?> Order</span>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($orders)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="ordersTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Cabang</th>
                                    <th>Total</th>
                                    <th>Status Order</th>
                                    <th>Status Bayar</th>
                                    <th>Tanggal</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <tr data-status="<?= esc($order['order_status'] ?? '') ?>">
                                        <td>
                                            <strong class="text-primary"><?= esc($order['order_number'] ?? '-') ?></strong>
                                        </td>
                                        <td>
                                            <?php if (isset($order['user'])): ?>
                                                <div><?= esc($order['user']['name'] ?? '-') ?></div>
                                                <small class="text-muted"><?= esc($order['user']['phone'] ?? '') ?></small>
                                            <?php elseif (isset($order['guest_name'])): ?>
                                                <div><?= esc($order['guest_name']) ?> <span class="badge bg-secondary">Guest</span></div>
                                                <small class="text-muted"><?= esc($order['guest_phone'] ?? '') ?></small>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?= esc($order['branch']['name'] ?? $order['branch_name'] ?? '-') ?>
                                        </td>
                                        <td>
                                            <strong><?= format_rupiah($order['total_amount'] ?? 0) ?></strong>
                                        </td>
                                        <td>
                                            <span class="badge <?= getStatusBadgeClass($order['order_status'] ?? '') ?>">
                                                <i class="<?= getStatusIcon($order['order_status'] ?? '') ?>"></i>
                                                <?= getStatusLabel($order['order_status'] ?? '') ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge <?= getPaymentBadgeClass($order['payment_status'] ?? '') ?>">
                                                <?= ucfirst($order['payment_status'] ?? 'pending') ?>
                                            </span>
                                        </td>
                                        <td>
                                            <small><?= format_tanggal($order['created_at'] ?? '') ?></small>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?= base_url('admin/orders/' . ($order['order_number'] ?? $order['id'])) ?>" 
                                                   class="btn btn-outline-primary" title="Detail">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <button type="button" class="btn btn-outline-success" 
                                                        onclick="openStatusModal('<?= esc($order['order_number'] ?? '') ?>', '<?= esc($order['order_status'] ?? '') ?>')"
                                                        title="Update Status">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>
                                                <?php if (!in_array($order['order_status'] ?? '', ['completed', 'cancelled'])): ?>
                                                    <button type="button" class="btn btn-outline-danger" 
                                                            onclick="cancelOrder('<?= esc($order['order_number'] ?? '') ?>')"
                                                            title="Batalkan">
                                                        <i class="bi bi-x-circle"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-inbox fs-1 text-muted mb-3 d-block"></i>
                        <h5 class="text-muted">Belum Ada Order</h5>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Update Status Modal -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil-square"></i> Update Status Pesanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="updateStatusForm">
                <input type="hidden" id="statusOrderNumber" name="order_number">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Status Baru *</label>
                        <select class="form-select" name="order_status" id="newStatus" required>
                            <option value="">-- Pilih Status --</option>
                            <?php foreach ($statusOptions as $value => $label): ?>
                                <option value="<?= $value ?>"><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- Status Flow Guide -->
                    <div class="alert alert-light small mb-3">
                        <strong><i class="bi bi-info-circle"></i> Alur Status:</strong><br>
                        <span class="badge bg-warning text-dark">Menunggu</span> →
                        <span class="badge bg-info">Dijemput</span> →
                        <span class="badge bg-primary">Diproses</span> →
                        <span class="badge bg-success">Siap</span> →
                        <span class="badge bg-info">Dikirim</span> →
                        <span class="badge bg-success">Selesai</span>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Catatan (Opsional)</label>
                        <textarea class="form-control" name="notes" id="statusNotes" rows="3" 
                                  placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Update Status
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const BASE_URL = '<?= base_url() ?>';
let statusModal;

document.addEventListener('DOMContentLoaded', function() {
    statusModal = new bootstrap.Modal(document.getElementById('statusModal'));
});

// Filter orders by status
function filterOrders() {
    const filterValue = document.getElementById('filterStatus').value;
    const rows = document.querySelectorAll('#ordersTable tbody tr');
    
    rows.forEach(row => {
        if (!filterValue || row.dataset.status === filterValue) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Open Status Modal
function openStatusModal(orderNumber, currentStatus) {
    document.getElementById('statusOrderNumber').value = orderNumber;
    document.getElementById('newStatus').value = currentStatus;
    document.getElementById('statusNotes').value = '';
    statusModal.show();
}

// Update Status Form Submit
document.getElementById('updateStatusForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const orderNumber = document.getElementById('statusOrderNumber').value;
    const formData = new FormData(this);
    
    try {
        Swal.fire({
            title: 'Memproses...',
            text: 'Sedang mengupdate status order',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });
        
        const response = await fetch(`${BASE_URL}/admin/orders/${orderNumber}/status`, {
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
            }).then(() => {
                statusModal.hide();
                window.location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: result.message
            });
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan saat menghubungi server'
        });
    }
});

// Cancel Order
async function cancelOrder(orderNumber) {
    const result = await Swal.fire({
        title: 'Batalkan Order?',
        html: `Anda yakin ingin membatalkan order <strong>${orderNumber}</strong>?<br><br>Tindakan ini tidak dapat dibatalkan.`,
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
        
        const response = await fetch(`${BASE_URL}/admin/orders/${orderNumber}/cancel`, {
            method: 'POST'
        });
        
        const data = await response.json();
        
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message,
                timer: 1500
            }).then(() => window.location.reload());
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: data.message
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
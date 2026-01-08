<?= $this->extend('layouts/main') ?>

<?php
helper('format');

// Status options sesuai API
$statusOptions = [
    'waiting_pickup' => 'Menunggu Pickup',
    'on_the_way_to_workshop' => 'Dalam Perjalanan ke Workshop',
    'arrived_at_workshop' => 'Tiba di Workshop',
    'in_process' => 'Sedang Diproses',
    'cleaning_done' => 'Selesai Dicuci',
    'on_the_way_to_customer' => 'Dalam Pengiriman',
    'delivered' => 'Terkirim',
    'completed' => 'Selesai',
    'cancelled' => 'Dibatalkan'
];

function getStatusBadgeClass($status) {
    $classes = [
        'pending' => 'bg-warning text-dark',
        'waiting_pickup' => 'bg-info',
        'on_the_way_to_workshop' => 'bg-info',
        'arrived_at_workshop' => 'bg-primary',
        'in_process' => 'bg-primary',
        'cleaning_done' => 'bg-success',
        'on_the_way_to_customer' => 'bg-info',
        'delivered' => 'bg-success',
        'completed' => 'bg-success',
        'cancelled' => 'bg-danger'
    ];
    return $classes[$status] ?? 'bg-secondary';
}

function getStatusLabel($status) {
    $labels = [
        'pending' => 'Menunggu',
        'waiting_pickup' => 'Menunggu Pickup',
        'on_the_way_to_workshop' => 'Ke Workshop',
        'arrived_at_workshop' => 'Di Workshop',
        'in_process' => 'Diproses',
        'cleaning_done' => 'Selesai Cuci',
        'on_the_way_to_customer' => 'Dikirim',
        'delivered' => 'Terkirim',
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
        </div>
    </div>
</section>

<section class="py-4">
    <div class="container-fluid px-4">
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><i class="bi bi-exclamation-triangle"></i> <?= esc($error) ?></div>
        <?php endif; ?>

        <!-- Orders Table -->
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-list"></i> Daftar Order</h5>
                <span class="badge bg-primary"><?= count($orders ?? []) ?> Order</span>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($orders)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
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
                                    <tr>
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
                        <!-- PENTING: name harus "order_status" bukan "status" -->
                        <select class="form-select" name="order_status" id="newStatus" required>
                            <option value="">-- Pilih Status --</option>
                            <?php foreach ($statusOptions as $value => $label): ?>
                                <option value="<?= $value ?>"><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
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
    
    // Debug: lihat apa yang dikirim
    console.log('Sending data:');
    for (let [key, value] of formData.entries()) {
        console.log(key + ': ' + value);
    }
    
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
        console.log('Response:', result);
        
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
<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
<style>
    .status-badge {
        font-size: 0.85rem;
        padding: 6px 12px;
    }
    .order-row:hover {
        background-color: #f8f9fa;
    }
    .filter-card {
        border-radius: 10px;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<section class="py-4 bg-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-1 fw-bold">
                    <i class="bi bi-box-seam"></i> Manajemen Pesanan
                </h2>
                <p class="text-muted mb-0">Kelola semua pesanan customer</p>
            </div>
            <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Dashboard
            </a>
        </div>
    </div>
</section>

<!-- Filter & Search -->
<section class="py-4 bg-white border-bottom">
    <div class="container">
        <form method="get" action="<?= base_url('admin/orders') ?>">
            <div class="row g-3">
                <div class="col-md-4">
                    <input type="text" 
                           class="form-control" 
                           name="search" 
                           id="searchOrder" 
                           placeholder="Cari order number, email, atau HP..."
                           value="<?= esc($_GET['search'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="status" id="filterStatus">
                        <option value="">Semua Status</option>
                        <option value="waiting_pickup" <?= ($_GET['status'] ?? '') === 'waiting_pickup' ? 'selected' : '' ?>>Waiting Pickup</option>
                        <option value="picked_up" <?= ($_GET['status'] ?? '') === 'picked_up' ? 'selected' : '' ?>>Picked Up</option>
                        <option value="in_process" <?= ($_GET['status'] ?? '') === 'in_process' ? 'selected' : '' ?>>In Process</option>
                        <option value="ready" <?= ($_GET['status'] ?? '') === 'ready' ? 'selected' : '' ?>>Ready</option>
                        <option value="on_delivery" <?= ($_GET['status'] ?? '') === 'on_delivery' ? 'selected' : '' ?>>On Delivery</option>
                        <option value="completed" <?= ($_GET['status'] ?? '') === 'completed' ? 'selected' : '' ?>>Completed</option>
                        <option value="cancelled" <?= ($_GET['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="branch" id="filterBranch">
                        <option value="">Semua Cabang</option>
                        <?php if (!empty($branches)): ?>
                            <?php foreach ($branches as $branch): ?>
                                <option value="<?= $branch['id'] ?>" <?= ($_GET['branch'] ?? '') == $branch['id'] ? 'selected' : '' ?>>
                                    <?= esc($branch['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-filter"></i> Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
</section>

<!-- Orders Table -->
<section class="py-4">
    <div class="container">
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle"></i> <?= esc($error) ?>
            </div>
        <?php endif; ?>

        <?php if (empty($orders)): ?>
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="bi bi-inbox display-1 text-muted"></i>
                    <h4 class="mt-3">Tidak Ada Pesanan</h4>
                    <p class="text-muted">Belum ada pesanan yang masuk</p>
                </div>
            </div>
        <?php else: ?>
            
            <!-- Summary -->
            <div class="mb-3">
                <span class="badge bg-secondary px-3 py-2">
                    Total: <?= $pagination['total'] ?? count($orders) ?> pesanan
                </span>
            </div>

            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="ordersTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Order Number</th>
                                    <th>Customer</th>
                                    <th>Layanan</th>
                                    <th>Cabang</th>
                                    <th>Status Order</th>
                                    <th>Pembayaran</th>
                                    <th>Total</th>
                                    <th>Tanggal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <?php
                                    // Status config
                                    $status = $order['order_status'] ?? 'pending';
                                    $statusConfig = [
                                        'waiting_pickup' => ['badge' => 'warning', 'label' => 'Waiting Pickup'],
                                        'picked_up' => ['badge' => 'info', 'label' => 'Picked Up'],
                                        'in_process' => ['badge' => 'primary', 'label' => 'In Process'],
                                        'processing' => ['badge' => 'primary', 'label' => 'Processing'],
                                        'washing' => ['badge' => 'primary', 'label' => 'Washing'],
                                        'drying' => ['badge' => 'primary', 'label' => 'Drying'],
                                        'quality_check' => ['badge' => 'info', 'label' => 'Quality Check'],
                                        'ready' => ['badge' => 'success', 'label' => 'Ready'],
                                        'on_delivery' => ['badge' => 'info', 'label' => 'On Delivery'],
                                        'completed' => ['badge' => 'success', 'label' => 'Completed'],
                                        'delivered' => ['badge' => 'success', 'label' => 'Delivered'],
                                        'cancelled' => ['badge' => 'danger', 'label' => 'Cancelled'],
                                        'pending' => ['badge' => 'secondary', 'label' => 'Pending']
                                    ];
                                    $currentStatus = $statusConfig[$status] ?? ['badge' => 'secondary', 'label' => ucfirst($status)];
                                    
                                    // Payment status
                                    $paymentStatus = $order['payment_status'] ?? 'pending';
                                    $paymentBadge = $paymentStatus === 'paid' ? 'success' : 'warning';
                                    $paymentLabel = $paymentStatus === 'paid' ? 'Lunas' : 'Belum Bayar';
                                    
                                    // Get service names
                                    $serviceNames = '-';
                                    if (!empty($order['items']) && is_array($order['items'])) {
                                        $names = [];
                                        foreach ($order['items'] as $item) {
                                            if (!empty($item['service']['name'])) {
                                                $names[] = $item['service']['name'];
                                            }
                                        }
                                        $serviceNames = !empty($names) ? implode(', ', $names) : '-';
                                    }
                                    
                                    // Branch name
                                    $branchName = $order['branch']['name'] ?? '-';
                                    
                                    // Customer info
                                    $customerEmail = $order['guest_email'] ?? '-';
                                    $customerPhone = $order['guest_phone'] ?? '';
                                    
                                    // Total
                                    $totalAmount = floatval($order['total_amount'] ?? 0);
                                    ?>
                                    <tr class="order-row">
                                        <td>
                                            <strong class="text-primary"><?= esc($order['order_number'] ?? 'N/A') ?></strong>
                                        </td>
                                        <td>
                                            <div><?= esc($customerEmail) ?></div>
                                            <?php if ($customerPhone): ?>
                                                <small class="text-muted"><?= esc($customerPhone) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <small><?= esc($serviceNames) ?></small>
                                        </td>
                                        <td>
                                            <small><?= esc($branchName) ?></small>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $currentStatus['badge'] ?> status-badge">
                                                <?= $currentStatus['label'] ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $paymentBadge ?>">
                                                <?= $paymentLabel ?>
                                            </span>
                                        </td>
                                        <td>
                                            <strong><?= format_rupiah($totalAmount) ?></strong>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <?= date('d M Y', strtotime($order['created_at'] ?? 'now')) ?>
                                                <br>
                                                <?= date('H:i', strtotime($order['created_at'] ?? 'now')) ?>
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?= base_url('admin/orders/' . ($order['order_number'] ?? '')) ?>" 
                                                   class="btn btn-outline-primary" title="Detail">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <?php if (!in_array($status, ['completed', 'cancelled'])): ?>
                                                    <button type="button" 
                                                            class="btn btn-outline-warning" 
                                                            onclick="openStatusModal('<?= esc($order['order_number']) ?>', '<?= $status ?>')"
                                                            title="Update Status">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <?php if (!empty($pagination) && $pagination['last_page'] > 1): ?>
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $pagination['last_page']; $i++): ?>
                            <li class="page-item <?= $i == $pagination['current_page'] ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>

        <?php endif; ?>
    </div>
</section>

<!-- Update Status Modal -->
<div class="modal fade" id="updateStatusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-pencil-square"></i> Update Status Pesanan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="updateStatusForm">
                    <input type="hidden" id="orderNumberInput">
                    
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        Order: <strong id="orderNumberDisplay"></strong>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Status Baru *</label>
                        <select class="form-select" id="newStatus" required>
                            <option value="">-- Pilih Status --</option>
                            <option value="waiting_pickup">Waiting Pickup</option>
                            <option value="picked_up">Picked Up</option>
                            <option value="in_process">In Process</option>
                            <option value="washing">Washing</option>
                            <option value="drying">Drying</option>
                            <option value="quality_check">Quality Check</option>
                            <option value="ready">Ready</option>
                            <option value="on_delivery">On Delivery</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Catatan (Opsional)</label>
                        <textarea class="form-control" id="statusNotes" rows="3" placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="submitStatusUpdate()" id="btnSubmitStatus">
                    <i class="bi bi-check-circle"></i> Update Status
                </button>
            </div>
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
    statusModal = new bootstrap.Modal(document.getElementById('updateStatusModal'));
});

function openStatusModal(orderNumber, currentStatus) {
    document.getElementById('orderNumberInput').value = orderNumber;
    document.getElementById('orderNumberDisplay').textContent = orderNumber;
    document.getElementById('newStatus').value = '';
    document.getElementById('statusNotes').value = '';
    
    // Disable current status option
    const statusSelect = document.getElementById('newStatus');
    Array.from(statusSelect.options).forEach(option => {
        option.disabled = (option.value === currentStatus);
    });
    
    statusModal.show();
}

async function submitStatusUpdate() {
    const orderNumber = document.getElementById('orderNumberInput').value;
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
    
    // Confirm if cancelling
    if (newStatus === 'cancelled') {
        const confirm = await Swal.fire({
            title: 'Batalkan Pesanan?',
            text: 'Pesanan yang dibatalkan tidak dapat dikembalikan!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Batalkan',
            cancelButtonText: 'Tidak'
        });
        
        if (!confirm.isConfirmed) return;
    }
    
    // Disable button
    const btn = document.getElementById('btnSubmitStatus');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Updating...';
    
    try {
        const response = await fetch(`${BASE_URL}/admin/orders/${orderNumber}/status`, {
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
        
        const result = await response.json();
        
        if (result.success) {
            statusModal.hide();
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: result.message || 'Status berhasil diupdate'
            }).then(() => {
                window.location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: result.message || 'Gagal update status'
            });
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan saat update status'
        });
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-check-circle"></i> Update Status';
    }
}

// Client-side search filter
document.getElementById('searchOrder')?.addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('#ordersTable tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});
</script>
<?= $this->endSection() ?>
<?= $this->extend('layouts/main') ?>

<?php
helper('format');
$promoCodes = $promo_codes ?? [];

// Filter valid promo codes
$validPromoCodes = [];
if (is_array($promoCodes)) {
    foreach ($promoCodes as $item) {
        if (is_array($item) && isset($item['id'])) {
            $validPromoCodes[] = $item;
        }
    }
}
$promoCodes = $validPromoCodes;
?>

<?= $this->section('styles') ?>
<style>
    .promo-card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .promo-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    .promo-code-badge {
        font-family: monospace;
        font-size: 1.1rem;
        letter-spacing: 2px;
    }
    .discount-badge {
        font-size: 1.5rem;
        font-weight: bold;
    }
    .status-active {
        background: linear-gradient(135deg, #28a745, #20c997);
    }
    .status-inactive {
        background: linear-gradient(135deg, #6c757d, #495057);
    }
    .status-expired {
        background: linear-gradient(135deg, #dc3545, #c82333);
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
                        <li class="breadcrumb-item active">Promo Codes</li>
                    </ol>
                </nav>
                <h2 class="mb-0 fw-bold"><i class="bi bi-percent"></i> Kelola Promo Code</h2>
            </div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPromoModal">
                <i class="bi bi-plus-lg"></i> Tambah Promo
            </button>
        </div>
    </div>
</section>

<section class="py-4">
    <div class="container-fluid px-4">
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><i class="bi bi-exclamation-triangle"></i> <?= esc($error) ?></div>
        <?php endif; ?>

        <!-- Stats -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                                <i class="bi bi-percent fs-4 text-primary"></i>
                            </div>
                            <div>
                                <h4 class="mb-0 fw-bold"><?= count($promoCodes) ?></h4>
                                <small class="text-muted">Total Promo</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="bg-success bg-opacity-10 rounded-circle p-3 me-3">
                                <i class="bi bi-check-circle fs-4 text-success"></i>
                            </div>
                            <div>
                                <?php 
                                $activeCount = 0;
                                foreach ($promoCodes as $p) {
                                    if (($p['is_active'] ?? false) == true) {
                                        $activeCount++;
                                    }
                                }
                                ?>
                                <h4 class="mb-0 fw-bold"><?= $activeCount ?></h4>
                                <small class="text-muted">Aktif</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="bg-secondary bg-opacity-10 rounded-circle p-3 me-3">
                                <i class="bi bi-pause-circle fs-4 text-secondary"></i>
                            </div>
                            <div>
                                <h4 class="mb-0 fw-bold"><?= count($promoCodes) - $activeCount ?></h4>
                                <small class="text-muted">Nonaktif</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="bg-info bg-opacity-10 rounded-circle p-3 me-3">
                                <i class="bi bi-graph-up fs-4 text-info"></i>
                            </div>
                            <div>
                                <?php 
                                $totalUsed = 0;
                                foreach ($promoCodes as $p) {
                                    $totalUsed += intval($p['used_count'] ?? $p['times_used'] ?? 0);
                                }
                                ?>
                                <h4 class="mb-0 fw-bold"><?= $totalUsed ?></h4>
                                <small class="text-muted">Total Digunakan</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Promo Codes Grid/Table -->
        <?php if (!empty($promoCodes)): ?>
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-list"></i> Daftar Promo Code</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Kode</th>
                                    <th>Deskripsi</th>
                                    <th>Diskon</th>
                                    <th>Min. Pembelian</th>
                                    <th>Kuota</th>
                                    <th>Periode</th>
                                    <th>Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($promoCodes as $promo): ?>
                                    <?php 
                                    if (!isset($promo['id'])) continue;
                                    
                                    $isActive = $promo['is_active'] ?? false;
                                    $discountType = $promo['discount_type'] ?? 'percentage';
                                    $discountValue = $promo['discount_value'] ?? 0;
                                    $usedCount = $promo['used_count'] ?? $promo['times_used'] ?? 0;
                                    $quota = $promo['quota'] ?? null;
                                    $startDate = $promo['start_date'] ?? null;
                                    $endDate = $promo['end_date'] ?? null;
                                    $minPurchase = $promo['min_purchase'] ?? 0;
                                    $maxDiscount = $promo['max_discount'] ?? null;
                                    
                                    // Check if expired
                                    $isExpired = false;
                                    if ($endDate && strtotime($endDate) < time()) {
                                        $isExpired = true;
                                    }
                                    ?>
                                    <tr class="<?= !$isActive ? 'table-secondary' : '' ?>">
                                        <td>
                                            <span class="badge bg-dark promo-code-badge"><?= esc($promo['code'] ?? '') ?></span>
                                        </td>
                                        <td>
                                            <small><?= esc($promo['description'] ?? '-') ?></small>
                                        </td>
                                        <td>
                                            <?php if ($discountType === 'percentage'): ?>
                                                <span class="badge bg-success fs-6"><?= $discountValue ?>%</span>
                                            <?php else: ?>
                                                <span class="badge bg-primary fs-6"><?= format_rupiah($discountValue) ?></span>
                                            <?php endif; ?>
                                            <?php if (!empty($maxDiscount)): ?>
                                                <br><small class="text-muted">Max: <?= format_rupiah($maxDiscount) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?= format_rupiah($minPurchase) ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-info"><?= $usedCount ?><?= $quota ? "/{$quota}" : '' ?></span>
                                        </td>
                                        <td>
                                            <small>
                                                <?php if ($startDate): ?>
                                                    <?= date('d/m/Y', strtotime($startDate)) ?>
                                                <?php endif; ?>
                                                <?php if ($startDate && $endDate): ?>
                                                    -
                                                <?php endif; ?>
                                                <?php if ($endDate): ?>
                                                    <?= date('d/m/Y', strtotime($endDate)) ?>
                                                <?php endif; ?>
                                                <?php if (!$startDate && !$endDate): ?>
                                                    <span class="text-muted">Tidak terbatas</span>
                                                <?php endif; ?>
                                            </small>
                                        </td>
                                        <td>
                                            <?php if ($isExpired): ?>
                                                <span class="badge status-expired text-white">Expired</span>
                                            <?php elseif ($isActive): ?>
                                                <span class="badge status-active text-white">Aktif</span>
                                            <?php else: ?>
                                                <span class="badge status-inactive text-white">Nonaktif</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-outline-primary" 
                                                        onclick='editPromo(<?= htmlspecialchars(json_encode($promo), ENT_QUOTES, 'UTF-8') ?>)'
                                                        title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-<?= $isActive ? 'secondary' : 'success' ?>" 
                                                        onclick="togglePromo(<?= $promo['id'] ?>)"
                                                        title="<?= $isActive ? 'Nonaktifkan' : 'Aktifkan' ?>">
                                                    <i class="bi bi-<?= $isActive ? 'pause' : 'play' ?>"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-danger" 
                                                        onclick="deletePromo(<?= $promo['id'] ?>, '<?= esc($promo['code'] ?? '') ?>')"
                                                        title="Hapus">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="bi bi-percent fs-1 text-muted mb-3 d-block"></i>
                    <h5 class="text-muted">Belum Ada Promo Code</h5>
                    <p class="text-muted mb-3">Buat promo code untuk menarik pelanggan</p>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPromoModal">
                        <i class="bi bi-plus-lg"></i> Buat Promo Pertama
                    </button>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Add Promo Modal -->
<div class="modal fade" id="addPromoModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-plus-lg"></i> Tambah Promo Code</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addPromoForm">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Kode Promo *</label>
                            <input type="text" class="form-control text-uppercase" name="code" 
                                   placeholder="HEMAT20" required maxlength="20" 
                                   style="letter-spacing: 2px; font-family: monospace;">
                            <small class="text-muted">Maksimal 20 karakter, otomatis uppercase</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Status</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="is_active" id="addIsActive" checked>
                                <label class="form-check-label" for="addIsActive">Aktif</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Deskripsi</label>
                            <input type="text" class="form-control" name="description" 
                                   placeholder="Diskon 20% untuk pelanggan baru">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tipe Diskon *</label>
                            <select class="form-select" name="discount_type" required>
                                <option value="percentage">Persentase (%)</option>
                                <option value="fixed">Nominal Tetap (Rp)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nilai Diskon *</label>
                            <input type="number" class="form-control" name="discount_value" 
                                   placeholder="20" required min="0" step="0.01">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Min. Pembelian</label>
                            <input type="number" class="form-control" name="min_purchase" 
                                   placeholder="100000" min="0">
                            <small class="text-muted">Minimum pembelian untuk pakai promo</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Max. Diskon</label>
                            <input type="number" class="form-control" name="max_discount" 
                                   placeholder="50000" min="0">
                            <small class="text-muted">Untuk tipe persentase, batas maksimal diskon</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Kuota</label>
                            <input type="number" class="form-control" name="quota" 
                                   placeholder="100" min="0">
                            <small class="text-muted">Kosongkan untuk unlimited</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Tanggal Mulai</label>
                            <input type="date" class="form-control" name="start_date">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Tanggal Berakhir</label>
                            <input type="date" class="form-control" name="end_date">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Promo Modal -->
<div class="modal fade" id="editPromoModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil"></i> Edit Promo Code</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editPromoForm">
                <input type="hidden" name="id" id="editPromoId">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Kode Promo *</label>
                            <input type="text" class="form-control text-uppercase" name="code" id="editCode"
                                   placeholder="HEMAT20" required maxlength="20" 
                                   style="letter-spacing: 2px; font-family: monospace;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Status</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="is_active" id="editIsActive">
                                <label class="form-check-label" for="editIsActive">Aktif</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Deskripsi</label>
                            <input type="text" class="form-control" name="description" id="editDescription"
                                   placeholder="Diskon 20% untuk pelanggan baru">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tipe Diskon *</label>
                            <select class="form-select" name="discount_type" id="editDiscountType" required>
                                <option value="percentage">Persentase (%)</option>
                                <option value="fixed">Nominal Tetap (Rp)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nilai Diskon *</label>
                            <input type="number" class="form-control" name="discount_value" id="editDiscountValue"
                                   placeholder="20" required min="0" step="0.01">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Min. Pembelian</label>
                            <input type="number" class="form-control" name="min_purchase" id="editMinPurchase"
                                   placeholder="100000" min="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Max. Diskon</label>
                            <input type="number" class="form-control" name="max_discount" id="editMaxDiscount"
                                   placeholder="50000" min="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Kuota</label>
                            <input type="number" class="form-control" name="quota" id="editQuota"
                                   placeholder="100" min="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Tanggal Mulai</label>
                            <input type="date" class="form-control" name="start_date" id="editStartDate">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Tanggal Berakhir</label>
                            <input type="date" class="form-control" name="end_date" id="editEndDate">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg"></i> Update
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
let addModal, editModal;

document.addEventListener('DOMContentLoaded', function() {
    addModal = new bootstrap.Modal(document.getElementById('addPromoModal'));
    editModal = new bootstrap.Modal(document.getElementById('editPromoModal'));
});

// Add Promo Form Submit
document.getElementById('addPromoForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    // Handle checkbox
    formData.set('is_active', document.getElementById('addIsActive').checked ? '1' : '0');
    
    try {
        Swal.fire({
            title: 'Menyimpan...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });
        
        const response = await fetch(`${BASE_URL}/admin/promo-codes/store`, {
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
                addModal.hide();
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
            text: 'Terjadi kesalahan'
        });
    }
});

// Open Edit Modal
function editPromo(promo) {
    if (!promo || !promo.id) {
        Swal.fire({ icon: 'error', title: 'Error!', text: 'Data promo tidak valid' });
        return;
    }
    
    document.getElementById('editPromoId').value = promo.id;
    document.getElementById('editCode').value = promo.code || '';
    document.getElementById('editDescription').value = promo.description || '';
    document.getElementById('editDiscountType').value = promo.discount_type || 'percentage';
    document.getElementById('editDiscountValue').value = promo.discount_value || '';
    document.getElementById('editMinPurchase').value = promo.min_purchase || '';
    document.getElementById('editMaxDiscount').value = promo.max_discount || '';
    document.getElementById('editQuota').value = promo.quota || '';
    document.getElementById('editStartDate').value = promo.start_date ? promo.start_date.split(' ')[0] : '';
    document.getElementById('editEndDate').value = promo.end_date ? promo.end_date.split(' ')[0] : '';
    document.getElementById('editIsActive').checked = promo.is_active == true;
    
    editModal.show();
}

// Edit Promo Form Submit
document.getElementById('editPromoForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const id = document.getElementById('editPromoId').value;
    const formData = new FormData(this);
    
    // Handle checkbox
    formData.set('is_active', document.getElementById('editIsActive').checked ? '1' : '0');
    
    try {
        Swal.fire({
            title: 'Mengupdate...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });
        
        const response = await fetch(`${BASE_URL}/admin/promo-codes/${id}/update`, {
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
                editModal.hide();
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
            text: 'Terjadi kesalahan'
        });
    }
});

// Toggle Promo Status
async function togglePromo(id) {
    try {
        Swal.fire({
            title: 'Memproses...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });
        
        const response = await fetch(`${BASE_URL}/admin/promo-codes/${id}/toggle`, {
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
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan'
        });
    }
}

// Delete Promo
async function deletePromo(id, code) {
    const result = await Swal.fire({
        title: 'Hapus Promo Code?',
        html: `Anda yakin ingin menghapus promo <strong>${code}</strong>?<br>Tindakan ini tidak dapat dibatalkan!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    });
    
    if (!result.isConfirmed) return;
    
    try {
        Swal.fire({
            title: 'Menghapus...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });
        
        const response = await fetch(`${BASE_URL}/admin/promo-codes/${id}/delete`, {
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
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan'
        });
    }
}
</script>
<?= $this->endSection() ?>
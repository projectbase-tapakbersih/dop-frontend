<?= $this->extend('layouts/main') ?>

<?php
if (!function_exists('format_rupiah')) {
    function format_rupiah($number) {
        return 'Rp ' . number_format((float)$number, 0, ',', '.');
    }
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
                        <li class="breadcrumb-item active">Layanan</li>
                    </ol>
                </nav>
                <h2 class="mb-0 fw-bold"><i class="bi bi-tags"></i> Manajemen Layanan</h2>
            </div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addServiceModal">
                <i class="bi bi-plus-lg"></i> Tambah Layanan
            </button>
        </div>
    </div>
</section>

<section class="py-4">
    <div class="container-fluid px-4">
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><i class="bi bi-exclamation-triangle"></i> <?= esc($error) ?></div>
        <?php endif; ?>

        <!-- Services Grid -->
        <div class="row g-4">
            <?php if (!empty($services)): ?>
                <?php foreach ($services as $service): ?>
                    <?php if (!is_array($service) || !isset($service['id'])) continue; ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h5 class="card-title fw-bold mb-1"><?= esc($service['name'] ?? 'Unnamed') ?></h5>
                                        <div class="d-flex gap-2 flex-wrap">
                                            <?php 
                                            $isActive = $service['is_active'] ?? true;
                                            ?>
                                            <span class="badge bg-<?= $isActive ? 'success' : 'secondary' ?>">
                                                <?= $isActive ? 'Aktif' : 'Nonaktif' ?>
                                            </span>
                                            <?php if (!empty($service['duration_hours'])): ?>
                                                <span class="badge bg-info">
                                                    <i class="bi bi-clock"></i> <?= esc($service['duration_hours']) ?> jam
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item" href="#" onclick="editService(<?= htmlspecialchars(json_encode($service), ENT_QUOTES, 'UTF-8') ?>)">
                                                <i class="bi bi-pencil"></i> Edit
                                            </a></li>
                                            <?php if ($isActive): ?>
                                                <li><a class="dropdown-item text-warning" href="#" onclick="toggleService(<?= (int)$service['id'] ?>, 'deactivate')">
                                                    <i class="bi bi-pause-circle"></i> Nonaktifkan
                                                </a></li>
                                            <?php else: ?>
                                                <li><a class="dropdown-item text-success" href="#" onclick="toggleService(<?= (int)$service['id'] ?>, 'activate')">
                                                    <i class="bi bi-play-circle"></i> Aktifkan
                                                </a></li>
                                            <?php endif; ?>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-danger" href="#" onclick="deleteService(<?= (int)$service['id'] ?>, '<?= esc($service['name'] ?? '') ?>')">
                                                <i class="bi bi-trash"></i> Hapus
                                            </a></li>
                                        </ul>
                                    </div>
                                </div>
                                <p class="text-muted small mb-3"><?= esc($service['description'] ?? '-') ?></p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="h5 text-primary mb-0"><?= format_rupiah($service['price'] ?? 0) ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-body text-center py-5">
                            <i class="bi bi-tags fs-1 text-muted mb-3 d-block"></i>
                            <h5 class="text-muted">Belum Ada Layanan</h5>
                            <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#addServiceModal">
                                <i class="bi bi-plus-lg"></i> Tambah Layanan
                            </button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Add Service Modal -->
<div class="modal fade" id="addServiceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-plus-lg"></i> Tambah Layanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addServiceForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Layanan *</label>
                        <input type="text" class="form-control" name="name" required placeholder="e.g. Deep Cleaning">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Deskripsi *</label>
                        <textarea class="form-control" name="description" rows="3" required placeholder="e.g. Perlindungan sepatu dari air"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Harga (Rp) *</label>
                            <input type="number" class="form-control" name="price" required min="0" placeholder="e.g. 50000">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Durasi (jam) *</label>
                            <input type="number" class="form-control" name="duration_hours" required min="1" placeholder="e.g. 24">
                            <small class="text-muted">Estimasi waktu pengerjaan</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Service Modal -->
<div class="modal fade" id="editServiceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil"></i> Edit Layanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editServiceForm">
                <input type="hidden" name="id" id="editServiceId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Layanan *</label>
                        <input type="text" class="form-control" name="name" id="editName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Deskripsi *</label>
                        <textarea class="form-control" name="description" id="editDescription" rows="3" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Harga (Rp) *</label>
                            <input type="number" class="form-control" name="price" id="editPrice" required min="0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Durasi (jam) *</label>
                            <input type="number" class="form-control" name="duration_hours" id="editDurationHours" required min="1">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Simpan</button>
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

// Add Service
document.getElementById('addServiceForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    try {
        Swal.fire({ title: 'Menyimpan...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        
        const response = await fetch(`${BASE_URL}/admin/services/store`, {
            method: 'POST',
            body: new URLSearchParams(formData)
        });
        const result = await response.json();
        
        if (result.success) {
            Swal.fire({ icon: 'success', title: 'Berhasil!', text: result.message, timer: 1500 })
                .then(() => window.location.reload());
        } else {
            Swal.fire({ icon: 'error', title: 'Gagal!', text: result.message });
        }
    } catch (error) {
        console.error(error);
        Swal.fire({ icon: 'error', title: 'Error!', text: 'Terjadi kesalahan' });
    }
});

// Edit Service
function editService(service) {
    document.getElementById('editServiceId').value = service.id;
    document.getElementById('editName').value = service.name || '';
    document.getElementById('editDescription').value = service.description || '';
    document.getElementById('editPrice').value = service.price || 0;
    document.getElementById('editDurationHours').value = service.duration_hours || 24;
    new bootstrap.Modal(document.getElementById('editServiceModal')).show();
}

document.getElementById('editServiceForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const serviceId = document.getElementById('editServiceId').value;
    const formData = new FormData(this);
    
    try {
        Swal.fire({ title: 'Menyimpan...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        
        const response = await fetch(`${BASE_URL}/admin/services/${serviceId}/update`, {
            method: 'POST',
            body: new URLSearchParams(formData)
        });
        const result = await response.json();
        
        if (result.success) {
            Swal.fire({ icon: 'success', title: 'Berhasil!', text: result.message, timer: 1500 })
                .then(() => window.location.reload());
        } else {
            Swal.fire({ icon: 'error', title: 'Gagal!', text: result.message });
        }
    } catch (error) {
        console.error(error);
        Swal.fire({ icon: 'error', title: 'Error!', text: 'Terjadi kesalahan' });
    }
});

// Toggle Service (Activate/Deactivate)
async function toggleService(id, action) {
    const actionText = action === 'activate' ? 'mengaktifkan' : 'menonaktifkan';
    
    try {
        Swal.fire({ title: `Sedang ${actionText}...`, allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        
        const response = await fetch(`${BASE_URL}/admin/services/${id}/${action}`, { method: 'POST' });
        const result = await response.json();
        
        if (result.success) {
            Swal.fire({ icon: 'success', title: 'Berhasil!', text: result.message, timer: 1500 })
                .then(() => window.location.reload());
        } else {
            Swal.fire({ icon: 'error', title: 'Gagal!', text: result.message });
        }
    } catch (error) {
        console.error(error);
        Swal.fire({ icon: 'error', title: 'Error!', text: 'Terjadi kesalahan' });
    }
}

// Delete Service
async function deleteService(id, name) {
    const result = await Swal.fire({
        title: 'Hapus Layanan?',
        html: `Anda yakin ingin menghapus <strong>${name}</strong>?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    });
    
    if (!result.isConfirmed) return;
    
    try {
        Swal.fire({ title: 'Menghapus...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        
        const response = await fetch(`${BASE_URL}/admin/services/${id}/delete`, { method: 'POST' });
        const data = await response.json();
        
        if (data.success) {
            Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message, timer: 1500 })
                .then(() => window.location.reload());
        } else {
            Swal.fire({ icon: 'error', title: 'Gagal!', text: data.message });
        }
    } catch (error) {
        console.error(error);
        Swal.fire({ icon: 'error', title: 'Error!', text: 'Terjadi kesalahan' });
    }
}
</script>
<?= $this->endSection() ?>
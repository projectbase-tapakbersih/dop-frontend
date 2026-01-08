<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<section class="py-4 bg-light">
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active">Cabang</li>
                    </ol>
                </nav>
                <h2 class="mb-0 fw-bold"><i class="bi bi-shop"></i> Manajemen Cabang</h2>
            </div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBranchModal">
                <i class="bi bi-plus-lg"></i> Tambah Cabang
            </button>
        </div>
    </div>
</section>

<section class="py-4">
    <div class="container-fluid px-4">
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><i class="bi bi-exclamation-triangle"></i> <?= esc($error) ?></div>
        <?php endif; ?>

        <!-- Branches Grid -->
        <div class="row g-4">
            <?php if (!empty($branches)): ?>
                <?php foreach ($branches as $branch): ?>
                    <?php if (!is_array($branch) || !isset($branch['id'])) continue; ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h5 class="card-title fw-bold mb-1"><?= esc($branch['name'] ?? 'Unnamed') ?></h5>
                                        <div class="d-flex gap-2 flex-wrap">
                                            <?php $isActive = $branch['is_active'] ?? true; ?>
                                            <span class="badge bg-<?= $isActive ? 'success' : 'secondary' ?>">
                                                <?= $isActive ? 'Aktif' : 'Nonaktif' ?>
                                            </span>
                                            <?php if (!empty($branch['coverage_radius_km'])): ?>
                                                <span class="badge bg-info">
                                                    <i class="bi bi-broadcast"></i> <?= esc($branch['coverage_radius_km']) ?> km
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item" href="#" onclick="editBranch(<?= htmlspecialchars(json_encode($branch), ENT_QUOTES, 'UTF-8') ?>)">
                                                <i class="bi bi-pencil"></i> Edit
                                            </a></li>
                                            <?php if ($isActive): ?>
                                                <li><a class="dropdown-item text-warning" href="#" onclick="toggleBranch(<?= (int)$branch['id'] ?>, 'deactivate')">
                                                    <i class="bi bi-pause-circle"></i> Nonaktifkan
                                                </a></li>
                                            <?php else: ?>
                                                <li><a class="dropdown-item text-success" href="#" onclick="toggleBranch(<?= (int)$branch['id'] ?>, 'activate')">
                                                    <i class="bi bi-play-circle"></i> Aktifkan
                                                </a></li>
                                            <?php endif; ?>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-danger" href="#" onclick="deleteBranch(<?= (int)$branch['id'] ?>, '<?= esc($branch['name'] ?? '') ?>')">
                                                <i class="bi bi-trash"></i> Hapus
                                            </a></li>
                                        </ul>
                                    </div>
                                </div>
                                
                                <?php if (!empty($branch['address'])): ?>
                                    <p class="mb-2"><i class="bi bi-geo-alt text-danger"></i> <small><?= esc($branch['address']) ?></small></p>
                                <?php endif; ?>
                                
                                <?php if (!empty($branch['latitude']) && !empty($branch['longitude'])): ?>
                                    <p class="mb-2"><i class="bi bi-pin-map text-primary"></i> <small class="text-muted"><?= esc($branch['latitude']) ?>, <?= esc($branch['longitude']) ?></small></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-body text-center py-5">
                            <i class="bi bi-shop fs-1 text-muted mb-3 d-block"></i>
                            <h5 class="text-muted">Belum Ada Cabang</h5>
                            <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#addBranchModal">
                                <i class="bi bi-plus-lg"></i> Tambah Cabang
                            </button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Add Branch Modal -->
<div class="modal fade" id="addBranchModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-plus-lg"></i> Tambah Cabang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addBranchForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label fw-bold">Nama Cabang *</label>
                            <input type="text" class="form-control" name="name" required placeholder="e.g. Tapak Bersih Sidoarjo">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Coverage Radius (km) *</label>
                            <input type="number" class="form-control" name="coverage_radius_km" required min="1" step="0.1" value="15">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Alamat *</label>
                        <textarea class="form-control" name="address" rows="2" required placeholder="e.g. Jl. Soekarno Hatta No. 123"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Latitude *</label>
                            <input type="number" class="form-control" name="latitude" required step="0.000001" placeholder="-7.9666">
                            <small class="text-muted">Contoh: -7.9666</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Longitude *</label>
                            <input type="number" class="form-control" name="longitude" required step="0.000001" placeholder="112.6326">
                            <small class="text-muted">Contoh: 112.6326</small>
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

<!-- Edit Branch Modal -->
<div class="modal fade" id="editBranchModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil"></i> Edit Cabang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editBranchForm">
                <input type="hidden" name="id" id="editBranchId">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label fw-bold">Nama Cabang *</label>
                            <input type="text" class="form-control" name="name" id="editName" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Coverage Radius (km) *</label>
                            <input type="number" class="form-control" name="coverage_radius_km" id="editCoverageRadius" required min="1" step="0.1">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Alamat *</label>
                        <textarea class="form-control" name="address" id="editAddress" rows="2" required></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Latitude *</label>
                            <input type="number" class="form-control" name="latitude" id="editLatitude" required step="0.000001">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Longitude *</label>
                            <input type="number" class="form-control" name="longitude" id="editLongitude" required step="0.000001">
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

// Add Branch
document.getElementById('addBranchForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    try {
        Swal.fire({ title: 'Menyimpan...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        
        const response = await fetch(`${BASE_URL}/admin/branches/store`, {
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

// Edit Branch
function editBranch(branch) {
    document.getElementById('editBranchId').value = branch.id;
    document.getElementById('editName').value = branch.name || '';
    document.getElementById('editAddress').value = branch.address || '';
    document.getElementById('editLatitude').value = branch.latitude || '';
    document.getElementById('editLongitude').value = branch.longitude || '';
    document.getElementById('editCoverageRadius').value = branch.coverage_radius_km || 15;
    new bootstrap.Modal(document.getElementById('editBranchModal')).show();
}

document.getElementById('editBranchForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const branchId = document.getElementById('editBranchId').value;
    const formData = new FormData(this);
    
    try {
        Swal.fire({ title: 'Menyimpan...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        
        const response = await fetch(`${BASE_URL}/admin/branches/${branchId}/update`, {
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

// Toggle Branch (Activate/Deactivate)
async function toggleBranch(id, action) {
    const actionText = action === 'activate' ? 'mengaktifkan' : 'menonaktifkan';
    
    try {
        Swal.fire({ title: `Sedang ${actionText}...`, allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        
        const response = await fetch(`${BASE_URL}/admin/branches/${id}/${action}`, { method: 'POST' });
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

// Delete Branch
async function deleteBranch(id, name) {
    const result = await Swal.fire({
        title: 'Hapus Cabang?',
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
        
        const response = await fetch(`${BASE_URL}/admin/branches/${id}/delete`, { method: 'POST' });
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
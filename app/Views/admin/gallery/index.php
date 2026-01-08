<?= $this->extend('layouts/main') ?>

<?php
helper('format');
$galleries = $galleries ?? [];
$services = $services ?? [];

// Filter hanya gallery yang valid (punya id)
$validGalleries = [];
if (is_array($galleries)) {
    foreach ($galleries as $item) {
        if (is_array($item) && isset($item['id'])) {
            $validGalleries[] = $item;
        }
    }
}
$galleries = $validGalleries;
?>

<?= $this->section('styles') ?>
<style>
    .gallery-card {
        transition: transform 0.2s, box-shadow 0.2s;
        overflow: hidden;
    }
    .gallery-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    }
    .gallery-image-container {
        position: relative;
        height: 200px;
        overflow: hidden;
    }
    .gallery-image-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s;
    }
    .gallery-card:hover .gallery-image-container img {
        transform: scale(1.05);
    }
    .before-after-container {
        display: flex;
        gap: 2px;
    }
    .before-after-container .image-half {
        flex: 1;
        position: relative;
    }
    .before-after-container .image-half img {
        width: 100%;
        height: 180px;
        object-fit: cover;
    }
    .before-after-container .image-label {
        position: absolute;
        bottom: 5px;
        left: 5px;
        font-size: 10px;
        padding: 2px 8px;
    }
    .image-preview {
        max-height: 150px;
        object-fit: cover;
        border-radius: 8px;
    }
    .upload-area {
        border: 2px dashed #dee2e6;
        border-radius: 10px;
        padding: 20px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
    }
    .upload-area:hover {
        border-color: #0d6efd;
        background: rgba(13, 110, 253, 0.05);
    }
    .upload-area.dragover {
        border-color: #0d6efd;
        background: rgba(13, 110, 253, 0.1);
    }
    .status-badge-active {
        background: linear-gradient(135deg, #28a745, #20c997);
    }
    .status-badge-inactive {
        background: linear-gradient(135deg, #6c757d, #495057);
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
                        <li class="breadcrumb-item active">Gallery</li>
                    </ol>
                </nav>
                <h2 class="mb-0 fw-bold"><i class="bi bi-images"></i> Kelola Gallery</h2>
            </div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addGalleryModal">
                <i class="bi bi-plus-lg"></i> Tambah Gallery
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
                                <i class="bi bi-images fs-4 text-primary"></i>
                            </div>
                            <div>
                                <h4 class="mb-0 fw-bold"><?= count($galleries) ?></h4>
                                <small class="text-muted">Total Gallery</small>
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
                                foreach ($galleries as $g) {
                                    if (($g['is_active'] ?? true) == true) {
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
                                <h4 class="mb-0 fw-bold"><?= count($galleries) - $activeCount ?></h4>
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
                                <i class="bi bi-tags fs-4 text-info"></i>
                            </div>
                            <div>
                                <h4 class="mb-0 fw-bold"><?= count($services) ?></h4>
                                <small class="text-muted">Layanan</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gallery Grid -->
        <?php if (!empty($galleries)): ?>
            <div class="row g-4">
                <?php foreach ($galleries as $item): ?>
                    <?php 
                    // Double check - skip jika tidak ada id
                    if (!isset($item['id'])) continue;
                    
                    $isActive = $item['is_active'] ?? true;
                    $serviceName = $item['service']['name'] ?? 'Layanan';
                    $galleryId = $item['id'];
                    ?>
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="card gallery-card h-100 shadow-sm <?= !$isActive ? 'opacity-75' : '' ?>">
                            <!-- Before/After Images -->
                            <div class="before-after-container">
                                <div class="image-half">
                                    <img src="<?= esc($item['before_image'] ?? 'https://via.placeholder.com/300x200?text=Before') ?>" 
                                         alt="Before" class="img-fluid">
                                    <span class="badge bg-dark image-label">Before</span>
                                </div>
                                <div class="image-half">
                                    <img src="<?= esc($item['after_image'] ?? 'https://via.placeholder.com/300x200?text=After') ?>" 
                                         alt="After" class="img-fluid">
                                    <span class="badge bg-success image-label">After</span>
                                </div>
                            </div>
                            
                            <div class="card-body">
                                <!-- Service Badge -->
                                <div class="mb-2">
                                    <span class="badge bg-primary"><?= esc($serviceName) ?></span>
                                    <span class="badge <?= $isActive ? 'status-badge-active' : 'status-badge-inactive' ?> text-white">
                                        <?= $isActive ? 'Aktif' : 'Nonaktif' ?>
                                    </span>
                                </div>
                                
                                <!-- Description -->
                                <p class="card-text text-muted small mb-3">
                                    <?= esc($item['description'] ?? 'Tidak ada deskripsi') ?>
                                </p>
                                
                                <!-- Created Date -->
                                <small class="text-muted">
                                    <i class="bi bi-calendar"></i> <?= format_tanggal($item['created_at'] ?? '') ?>
                                </small>
                            </div>
                            
                            <!-- Actions -->
                            <div class="card-footer bg-white border-0">
                                <div class="btn-group btn-group-sm w-100">
                                    <button type="button" class="btn btn-outline-primary" 
                                            onclick='editGallery(<?= htmlspecialchars(json_encode($item), ENT_QUOTES, 'UTF-8') ?>)'>
                                        <i class="bi bi-pencil"></i> Edit
                                    </button>
                                    <?php if ($isActive): ?>
                                        <button type="button" class="btn btn-outline-secondary" 
                                                onclick="toggleStatus(<?= $galleryId ?>, 'deactivate')">
                                            <i class="bi bi-pause"></i> Nonaktif
                                        </button>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-outline-success" 
                                                onclick="toggleStatus(<?= $galleryId ?>, 'activate')">
                                            <i class="bi bi-play"></i> Aktifkan
                                        </button>
                                    <?php endif; ?>
                                    <button type="button" class="btn btn-outline-danger" 
                                            onclick="deleteGallery(<?= $galleryId ?>)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="bi bi-images fs-1 text-muted mb-3 d-block"></i>
                    <h5 class="text-muted">Belum Ada Gallery</h5>
                    <p class="text-muted mb-3">Tambahkan foto before/after untuk menampilkan hasil kerja Anda</p>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addGalleryModal">
                        <i class="bi bi-plus-lg"></i> Tambah Gallery Pertama
                    </button>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Add Gallery Modal -->
<div class="modal fade" id="addGalleryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-plus-lg"></i> Tambah Gallery</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addGalleryForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row g-3">
                        <!-- Service Selection -->
                        <div class="col-12">
                            <label class="form-label fw-bold">Layanan *</label>
                            <select class="form-select" name="service_id" required>
                                <option value="">-- Pilih Layanan --</option>
                                <?php if (!empty($services)): ?>
                                    <?php foreach ($services as $service): ?>
                                        <?php if (is_array($service) && isset($service['id'])): ?>
                                            <option value="<?= $service['id'] ?>"><?= esc($service['name'] ?? 'Layanan') ?></option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <?php if (empty($services)): ?>
                                <small class="text-muted">Belum ada layanan. <a href="<?= base_url('admin/services') ?>">Tambah layanan</a> terlebih dahulu.</small>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Before Image -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Foto Before *</label>
                            <div class="upload-area" onclick="document.getElementById('beforeImageAdd').click()">
                                <i class="bi bi-cloud-upload fs-1 text-muted"></i>
                                <p class="mb-0 text-muted">Klik untuk upload gambar sebelum</p>
                                <input type="file" class="d-none" id="beforeImageAdd" name="before_image" 
                                       accept="image/*" required onchange="previewImage(this, 'beforePreviewAdd')">
                            </div>
                            <img id="beforePreviewAdd" class="img-fluid image-preview mt-2 d-none">
                        </div>
                        
                        <!-- After Image -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Foto After *</label>
                            <div class="upload-area" onclick="document.getElementById('afterImageAdd').click()">
                                <i class="bi bi-cloud-upload fs-1 text-muted"></i>
                                <p class="mb-0 text-muted">Klik untuk upload gambar sesudah</p>
                                <input type="file" class="d-none" id="afterImageAdd" name="after_image" 
                                       accept="image/*" required onchange="previewImage(this, 'afterPreviewAdd')">
                            </div>
                            <img id="afterPreviewAdd" class="img-fluid image-preview mt-2 d-none">
                        </div>
                        
                        <!-- Description -->
                        <div class="col-12">
                            <label class="form-label fw-bold">Deskripsi</label>
                            <textarea class="form-control" name="description" rows="3" 
                                      placeholder="Deskripsi singkat tentang hasil kerja..."></textarea>
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

<!-- Edit Gallery Modal -->
<div class="modal fade" id="editGalleryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil"></i> Edit Gallery</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editGalleryForm" enctype="multipart/form-data">
                <input type="hidden" name="id" id="editGalleryId">
                <div class="modal-body">
                    <div class="row g-3">
                        <!-- Service Selection -->
                        <div class="col-12">
                            <label class="form-label fw-bold">Layanan *</label>
                            <select class="form-select" name="service_id" id="editServiceId" required>
                                <option value="">-- Pilih Layanan --</option>
                                <?php if (!empty($services)): ?>
                                    <?php foreach ($services as $service): ?>
                                        <?php if (is_array($service) && isset($service['id'])): ?>
                                            <option value="<?= $service['id'] ?>"><?= esc($service['name'] ?? 'Layanan') ?></option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        
                        <!-- Current Before Image -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Foto Before</label>
                            <div class="mb-2">
                                <img id="currentBeforeImage" class="img-fluid rounded" style="max-height: 120px;">
                            </div>
                            <div class="upload-area" onclick="document.getElementById('beforeImageEdit').click()">
                                <i class="bi bi-cloud-upload fs-4 text-muted"></i>
                                <p class="mb-0 text-muted small">Klik untuk ganti gambar</p>
                                <input type="file" class="d-none" id="beforeImageEdit" name="before_image" 
                                       accept="image/*" onchange="previewImage(this, 'beforePreviewEdit')">
                            </div>
                            <img id="beforePreviewEdit" class="img-fluid image-preview mt-2 d-none">
                        </div>
                        
                        <!-- Current After Image -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Foto After</label>
                            <div class="mb-2">
                                <img id="currentAfterImage" class="img-fluid rounded" style="max-height: 120px;">
                            </div>
                            <div class="upload-area" onclick="document.getElementById('afterImageEdit').click()">
                                <i class="bi bi-cloud-upload fs-4 text-muted"></i>
                                <p class="mb-0 text-muted small">Klik untuk ganti gambar</p>
                                <input type="file" class="d-none" id="afterImageEdit" name="after_image" 
                                       accept="image/*" onchange="previewImage(this, 'afterPreviewEdit')">
                            </div>
                            <img id="afterPreviewEdit" class="img-fluid image-preview mt-2 d-none">
                        </div>
                        
                        <!-- Description -->
                        <div class="col-12">
                            <label class="form-label fw-bold">Deskripsi</label>
                            <textarea class="form-control" name="description" id="editDescription" rows="3" 
                                      placeholder="Deskripsi singkat tentang hasil kerja..."></textarea>
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
    addModal = new bootstrap.Modal(document.getElementById('addGalleryModal'));
    editModal = new bootstrap.Modal(document.getElementById('editGalleryModal'));
});

// Preview uploaded image
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.classList.remove('d-none');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Add Gallery Form Submit
document.getElementById('addGalleryForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    try {
        Swal.fire({
            title: 'Menyimpan...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });
        
        const response = await fetch(`${BASE_URL}/admin/gallery/store`, {
            method: 'POST',
            body: formData
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
function editGallery(gallery) {
    if (!gallery || !gallery.id) {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Data gallery tidak valid'
        });
        return;
    }
    
    document.getElementById('editGalleryId').value = gallery.id;
    document.getElementById('editServiceId').value = gallery.service_id || '';
    document.getElementById('editDescription').value = gallery.description || '';
    
    // Show current images
    document.getElementById('currentBeforeImage').src = gallery.before_image || 'https://via.placeholder.com/150?text=No+Image';
    document.getElementById('currentAfterImage').src = gallery.after_image || 'https://via.placeholder.com/150?text=No+Image';
    
    // Reset previews
    document.getElementById('beforePreviewEdit').classList.add('d-none');
    document.getElementById('afterPreviewEdit').classList.add('d-none');
    document.getElementById('beforeImageEdit').value = '';
    document.getElementById('afterImageEdit').value = '';
    
    editModal.show();
}

// Edit Gallery Form Submit
document.getElementById('editGalleryForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const id = document.getElementById('editGalleryId').value;
    if (!id) {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'ID gallery tidak ditemukan'
        });
        return;
    }
    
    const formData = new FormData(this);
    
    try {
        Swal.fire({
            title: 'Mengupdate...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });
        
        const response = await fetch(`${BASE_URL}/admin/gallery/${id}/update`, {
            method: 'POST',
            body: formData
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

// Toggle Gallery Status
async function toggleStatus(id, action) {
    if (!id) {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'ID gallery tidak valid'
        });
        return;
    }
    
    const actionText = action === 'activate' ? 'mengaktifkan' : 'menonaktifkan';
    
    const result = await Swal.fire({
        title: `${action === 'activate' ? 'Aktifkan' : 'Nonaktifkan'} Gallery?`,
        text: `Anda yakin ingin ${actionText} gallery ini?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya',
        cancelButtonText: 'Batal'
    });
    
    if (!result.isConfirmed) return;
    
    try {
        Swal.fire({
            title: 'Memproses...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });
        
        const response = await fetch(`${BASE_URL}/admin/gallery/${id}/${action}`, {
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

// Delete Gallery
async function deleteGallery(id) {
    if (!id) {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'ID gallery tidak valid'
        });
        return;
    }
    
    const result = await Swal.fire({
        title: 'Hapus Gallery?',
        text: 'Tindakan ini tidak dapat dibatalkan!',
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
        
        const response = await fetch(`${BASE_URL}/admin/gallery/${id}/delete`, {
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
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<section class="py-4 bg-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-1 fw-bold">Profile Saya</h2>
                <p class="text-muted mb-0">Kelola informasi akun Anda</p>
            </div>
            <a href="<?= base_url('user/orders') ?>" class="btn btn-outline-primary">
                <i class="bi bi-box"></i> Pesanan Saya
            </a>
        </div>
    </div>
</section>

<!-- Profile Content -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 mb-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <!-- Avatar -->
                        <div class="mb-3">
                            <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex p-4">
                                <i class="bi bi-person-fill text-primary" style="font-size: 3rem;"></i>
                            </div>
                        </div>
                        <h5 class="fw-bold mb-1"><?= esc($user['name']) ?></h5>
                        <p class="text-muted small mb-3"><?= esc($user['email']) ?></p>
                        <span class="badge bg-primary"><?= ucfirst($user['role']) ?></span>
                    </div>
                </div>

                <!-- Menu -->
                <div class="card shadow-sm mt-3">
                    <div class="list-group list-group-flush">
                        <a href="<?= base_url('user/profile') ?>" class="list-group-item list-group-item-action active">
                            <i class="bi bi-person me-2"></i> Informasi Akun
                        </a>
                        <a href="<?= base_url('user/orders') ?>" class="list-group-item list-group-item-action">
                            <i class="bi bi-box me-2"></i> Pesanan Saya
                        </a>
                        <a href="<?= base_url('auth/logout') ?>" class="list-group-item list-group-item-action text-danger">
                            <i class="bi bi-box-arrow-right me-2"></i> Logout
                        </a>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9">
                <!-- Alert Container -->
                <div id="alert-container"></div>

                <!-- Personal Information Card -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-person-badge text-primary"></i> Informasi Pribadi
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <form id="profileForm">
                            <?= csrf_field() ?>

                            <div class="row g-3">
                                <!-- Name -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Nama Lengkap *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-person"></i>
                                        </span>
                                        <input type="text" 
                                               class="form-control" 
                                               name="name" 
                                               id="name" 
                                               value="<?= esc($user['name']) ?>" 
                                               required>
                                    </div>
                                    <div class="invalid-feedback" id="error-name"></div>
                                </div>

                                <!-- Email -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Email *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-envelope"></i>
                                        </span>
                                        <input type="email" 
                                               class="form-control" 
                                               name="email" 
                                               id="email" 
                                               value="<?= esc($user['email']) ?>" 
                                               required>
                                    </div>
                                    <div class="invalid-feedback" id="error-email"></div>
                                </div>

                                <!-- Phone -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">No. WhatsApp *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-phone"></i>
                                        </span>
                                        <input type="tel" 
                                               class="form-control" 
                                               name="phone" 
                                               id="phone" 
                                               value="<?= esc($user['phone']) ?>" 
                                               placeholder="08123456789"
                                               required>
                                    </div>
                                    <div class="invalid-feedback" id="error-phone"></div>
                                    <small class="text-muted">Untuk notifikasi pesanan via WhatsApp</small>
                                </div>

                                <!-- Role (Read-only) -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Role</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-shield-check"></i>
                                        </span>
                                        <input type="text" 
                                               class="form-control" 
                                               value="<?= ucfirst($user['role']) ?>" 
                                               readonly 
                                               disabled>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="d-flex justify-content-between align-items-center">
                                <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                                </button>
                                <button type="submit" class="btn btn-primary" id="btnSave">
                                    <i class="bi bi-save"></i> <span id="btnText">Simpan Perubahan</span>
                                    <span id="btnLoading" class="d-none">
                                        <span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Change Password Card -->
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-key text-warning"></i> Ubah Password
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            <strong>Info:</strong> Untuk mengubah password, silakan gunakan fitur 
                            <a href="<?= base_url('auth/forgot-password') ?>" class="alert-link">Lupa Password</a>.
                        </div>
                        <a href="<?= base_url('auth/forgot-password') ?>" class="btn btn-outline-warning">
                            <i class="bi bi-key"></i> Ubah Password
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
const BASE_URL = '<?= base_url() ?>';

// Original values for reset
const originalValues = {
    name: '<?= esc($user['name']) ?>',
    email: '<?= esc($user['email']) ?>',
    phone: '<?= esc($user['phone']) ?>'
};

// Clear validation errors
function clearErrors() {
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
}

// Show validation errors
function showErrors(errors) {
    clearErrors();
    for (const [field, messages] of Object.entries(errors)) {
        const input = document.getElementById(field);
        const errorDiv = document.getElementById(`error-${field}`);
        
        if (input && errorDiv) {
            input.classList.add('is-invalid');
            errorDiv.textContent = Array.isArray(messages) ? messages[0] : messages;
        }
    }
}

// Show alert
function showAlert(message, type = 'success') {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'}"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    document.getElementById('alert-container').innerHTML = alertHtml;
    
    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
    
    // Auto dismiss after 5 seconds
    setTimeout(() => {
        const alert = document.querySelector('.alert');
        if (alert) {
            alert.classList.remove('show');
            setTimeout(() => alert.remove(), 150);
        }
    }, 5000);
}

// Reset form
function resetForm() {
    document.getElementById('name').value = originalValues.name;
    document.getElementById('email').value = originalValues.email;
    document.getElementById('phone').value = originalValues.phone;
    clearErrors();
}

// Profile form submission
document.getElementById('profileForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    clearErrors();
    
    const btnSave = document.getElementById('btnSave');
    const btnText = document.getElementById('btnText');
    const btnLoading = document.getElementById('btnLoading');
    
    // Disable button and show loading
    btnSave.disabled = true;
    btnText.classList.add('d-none');
    btnLoading.classList.remove('d-none');
    
    const formData = new FormData(this);
    
    try {
        const response = await fetch(`${BASE_URL}/user/profile/update`, {
            method: 'POST',
            body: new URLSearchParams(formData)
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert(result.message || 'Profile berhasil diperbarui!', 'success');
            
            // Update original values
            originalValues.name = formData.get('name');
            originalValues.email = formData.get('email');
            originalValues.phone = formData.get('phone');
            
            // Reload page after 2 seconds to update session data
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            if (result.errors) {
                showErrors(result.errors);
            } else {
                showAlert(result.message || 'Gagal memperbarui profile', 'danger');
            }
        }
    } catch (error) {
        console.error('Profile update error:', error);
        showAlert('Terjadi kesalahan. Silakan coba lagi.', 'danger');
    } finally {
        // Re-enable button
        btnSave.disabled = false;
        btnText.classList.remove('d-none');
        btnLoading.classList.add('d-none');
    }
});
</script>
<?= $this->endSection() ?>
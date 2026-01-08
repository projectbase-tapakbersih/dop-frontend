<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
<style>
    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 16px;
    }
    .stat-card {
        border-radius: 12px;
        transition: transform 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-3px);
    }
    .role-badge {
        font-size: 11px;
        padding: 4px 10px;
        border-radius: 20px;
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
                        <li class="breadcrumb-item active">Users</li>
                    </ol>
                </nav>
                <h2 class="mb-0 fw-bold">
                    <i class="bi bi-people"></i> Manajemen User
                </h2>
                <p class="text-muted mb-0">Kelola semua pengguna sistem</p>
            </div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i class="bi bi-plus-lg"></i> Tambah User
            </button>
        </div>
    </div>
</section>

<section class="py-4">
    <div class="container-fluid px-4">
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle"></i> <?= esc($error) ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle"></i> <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (ENVIRONMENT === 'development' && empty($users)): ?>
            <div class="alert alert-warning">
                <i class="bi bi-bug"></i> <strong>Debug Info:</strong> 
                Tidak ada data user. Periksa API endpoint <code>GET /api/users</code>.
                <?php if (!empty($debug_info)): ?>
                    <hr>
                    <small>
                        Response Type: <?= $debug_info['response_type'] ?? 'N/A' ?><br>
                        Response Keys: <?= is_array($debug_info['response_keys'] ?? null) ? implode(', ', $debug_info['response_keys']) : ($debug_info['response_keys'] ?? 'N/A') ?><br>
                        First Item Type: <?= $debug_info['first_item_type'] ?? 'N/A' ?>
                    </small>
                <?php endif; ?>
                <hr>
                <a href="<?= base_url('admin/users/debug') ?>" class="btn btn-sm btn-outline-warning">View Raw API Response</a>
            </div>
        <?php endif; ?>

        <!-- Summary Cards -->
        <div class="row g-3 mb-4">
            <?php
            $users = $users ?? [];
            $totalUsers = is_array($users) ? count($users) : 0;
            $adminCount = 0;
            $customerCount = 0;
            $driverCount = 0;
            
            if (is_array($users)) {
                foreach ($users as $u) {
                    if (!is_array($u)) continue;
                    $r = $u['role'] ?? '';
                    if ($r === 'admin' || $r === 'super_admin') $adminCount++;
                    elseif ($r === 'customer' || $r === 'user') $customerCount++;
                    elseif ($r === 'driver') $driverCount++;
                }
            }
            ?>
            <div class="col-md-3">
                <div class="card stat-card border-0 shadow-sm bg-primary text-white">
                    <div class="card-body">
                        <h3 class="fw-bold"><?= $totalUsers ?></h3>
                        <p class="mb-0">Total User</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card border-0 shadow-sm">
                    <div class="card-body">
                        <h3 class="fw-bold text-danger"><?= $adminCount ?></h3>
                        <p class="mb-0 text-muted">Admin</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card border-0 shadow-sm">
                    <div class="card-body">
                        <h3 class="fw-bold text-success"><?= $customerCount ?></h3>
                        <p class="mb-0 text-muted">Customer</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card border-0 shadow-sm">
                    <div class="card-body">
                        <h3 class="fw-bold text-info"><?= $driverCount ?></h3>
                        <p class="mb-0 text-muted">Driver</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search & Filter -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" id="searchInput" placeholder="Cari nama, email, atau phone...">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select class="form-select" id="roleFilter">
                            <option value="">Semua Role</option>
                            <option value="admin">Admin</option>
                            <option value="customer">Customer</option>
                            <option value="driver">Driver</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-outline-secondary w-100" onclick="resetFilters()">
                            <i class="bi bi-arrow-clockwise"></i> Reset
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users Table -->
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Tanggal Daftar</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="usersTableBody">
                            <?php if (!empty($users) && is_array($users)): ?>
                                <?php foreach ($users as $user): ?>
                                    <?php 
                                    // Skip if user is not an array or doesn't have required fields
                                    if (!is_array($user) || !isset($user['id'])) {
                                        continue;
                                    }
                                    
                                    $role = $user['role'] ?? 'customer';
                                    $roleBadge = [
                                        'admin' => 'danger',
                                        'customer' => 'success',
                                        'user' => 'success',
                                        'driver' => 'info',
                                        'super_admin' => 'dark'
                                    ];
                                    $isVerified = !empty($user['email_verified_at']);
                                    $isGuest = ($user['is_guest'] ?? 0) == 1;
                                    $initials = strtoupper(substr($user['name'] ?? 'U', 0, 1));
                                    ?>
                                    <tr class="user-row" 
                                        data-search="<?= strtolower(($user['name'] ?? '') . ' ' . ($user['email'] ?? '') . ' ' . ($user['phone'] ?? '')) ?>"
                                        data-role="<?= esc($role) ?>">
                                        <td><span class="text-muted">#<?= esc($user['id']) ?></span></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="user-avatar me-2"><?= esc($initials) ?></div>
                                                <div>
                                                    <strong><?= esc($user['name'] ?? '-') ?></strong>
                                                    <?php if ($isGuest): ?>
                                                        <span class="badge bg-secondary ms-1" style="font-size: 10px;">Guest</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?= esc($user['email'] ?? '-') ?></td>
                                        <td><?= esc($user['phone'] ?? '-') ?></td>
                                        <td>
                                            <span class="badge bg-<?= $roleBadge[$role] ?? 'secondary' ?> role-badge">
                                                <?= ucfirst(esc($role)) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($isVerified): ?>
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle"></i> Verified
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark">
                                                    <i class="bi bi-clock"></i> Unverified
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <small><?= !empty($user['created_at']) ? date('d/m/Y', strtotime($user['created_at'])) : '-' ?></small>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary" 
                                                        onclick="editUser(<?= htmlspecialchars(json_encode($user), ENT_QUOTES, 'UTF-8') ?>)"
                                                        title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <?php if (($user['id'] ?? 0) != session()->get('user_id')): ?>
                                                <button class="btn btn-outline-danger" 
                                                        onclick="deleteUser(<?= (int)$user['id'] ?>, '<?= esc($user['name'] ?? 'User') ?>')"
                                                        title="Hapus">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <i class="bi bi-people fs-1 text-muted d-block mb-2"></i>
                                        <h5 class="text-muted">Tidak Ada User</h5>
                                        <p class="text-muted mb-0">Belum ada user yang terdaftar atau terjadi kesalahan saat memuat data</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-person-plus"></i> Tambah User Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addUserForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama *</label>
                        <input type="text" class="form-control" name="name" required minlength="3">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Email *</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">No. Telepon *</label>
                        <input type="tel" class="form-control" name="phone" required minlength="10">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Password *</label>
                        <input type="password" class="form-control" name="password" required minlength="8">
                        <small class="text-muted">Minimal 8 karakter</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Role *</label>
                        <select class="form-select" name="role" required>
                            <option value="">-- Pilih Role --</option>
                            <option value="customer">Customer</option>
                            <option value="admin">Admin</option>
                            <option value="driver">Driver</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> Tambah User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil"></i> Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editUserForm">
                <input type="hidden" name="id" id="editUserId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama *</label>
                        <input type="text" class="form-control" name="name" id="editName" required minlength="3">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Email</label>
                        <input type="email" class="form-control" id="editEmail" disabled>
                        <small class="text-muted">Email tidak dapat diubah</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">No. Telepon *</label>
                        <input type="tel" class="form-control" name="phone" id="editPhone" required minlength="10">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Password Baru</label>
                        <input type="password" class="form-control" name="password" id="editPassword" minlength="8">
                        <small class="text-muted">Kosongkan jika tidak ingin mengubah password</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Role *</label>
                        <select class="form-select" name="role" id="editRole" required>
                            <option value="customer">Customer</option>
                            <option value="admin">Admin</option>
                            <option value="driver">Driver</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg"></i> Simpan Perubahan
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

// Filter functions
document.getElementById('searchInput').addEventListener('input', filterTable);
document.getElementById('roleFilter').addEventListener('change', filterTable);

function filterTable() {
    const search = document.getElementById('searchInput').value.toLowerCase();
    const role = document.getElementById('roleFilter').value;
    
    document.querySelectorAll('.user-row').forEach(row => {
        const rowSearch = row.dataset.search;
        const rowRole = row.dataset.role;
        
        let show = true;
        if (search && !rowSearch.includes(search)) show = false;
        if (role && rowRole !== role && !(role === 'customer' && rowRole === 'user')) show = false;
        
        row.style.display = show ? '' : 'none';
    });
}

function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('roleFilter').value = '';
    filterTable();
}

// Add User
document.getElementById('addUserForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    
    try {
        Swal.fire({
            title: 'Menyimpan...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });
        
        const response = await fetch(`${BASE_URL}/admin/users/store`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: new URLSearchParams(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: result.message || 'User berhasil ditambahkan',
                timer: 1500
            }).then(() => {
                window.location.reload();
            });
        } else {
            let errorMsg = result.message || 'Gagal menambahkan user';
            if (result.errors) {
                errorMsg += '\n' + Object.values(result.errors).join('\n');
            }
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: errorMsg
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

// Edit User
function editUser(user) {
    document.getElementById('editUserId').value = user.id;
    document.getElementById('editName').value = user.name || '';
    document.getElementById('editEmail').value = user.email || '';
    document.getElementById('editPhone').value = user.phone || '';
    document.getElementById('editPassword').value = '';
    document.getElementById('editRole').value = user.role || 'customer';
    
    new bootstrap.Modal(document.getElementById('editUserModal')).show();
}

document.getElementById('editUserForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const userId = document.getElementById('editUserId').value;
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    
    // Remove empty password
    if (!data.password) delete data.password;
    
    try {
        Swal.fire({
            title: 'Menyimpan...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });
        
        const response = await fetch(`${BASE_URL}/admin/users/${userId}/update`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: new URLSearchParams(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: result.message || 'User berhasil diupdate',
                timer: 1500
            }).then(() => {
                window.location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: result.message || 'Gagal mengupdate user'
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

// Delete User
async function deleteUser(userId, userName) {
    const result = await Swal.fire({
        title: 'Hapus User?',
        html: `Anda yakin ingin menghapus user <strong>${userName}</strong>?<br>Tindakan ini tidak dapat dibatalkan.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    });
    
    if (!result.isConfirmed) return;
    
    try {
        Swal.fire({
            title: 'Menghapus...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });
        
        const response = await fetch(`${BASE_URL}/admin/users/${userId}/delete`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message || 'User berhasil dihapus',
                timer: 1500
            }).then(() => {
                window.location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: data.message || 'Gagal menghapus user'
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
// Order Form Logic
const BASE_URL = window.location.origin;
let currentStep = 1;
let selectedService = null;
let selectedBranch = null;

// Check if user is logged in (will be set from PHP)
const isUserLoggedIn = typeof USER_LOGGED_IN !== 'undefined' ? USER_LOGGED_IN : false;

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    // Set minimum date to today
    const today = new Date().toISOString().split('T')[0];
    const pickupDateEl = document.getElementById('pickup_date');
    if (pickupDateEl) {
        pickupDateEl.setAttribute('min', today);
    }
    
    // Initialize service selection
    initServiceSelection();
    initBranchSelection();
    initFormValidation();
    
    // Update summary on form changes
    document.querySelectorAll('#orderForm input, #orderForm select, #orderForm textarea').forEach(input => {
        input.addEventListener('change', updateOrderSummary);
        input.addEventListener('input', updateOrderSummary);
    });

    // Initialize pre-selected service
    const preSelectedService = document.querySelector('.service-option.selected');
    if (preSelectedService) {
        selectedService = {
            id: preSelectedService.dataset.serviceId,
            name: preSelectedService.dataset.serviceName,
            price: parseFloat(preSelectedService.dataset.servicePrice),
            duration: preSelectedService.dataset.serviceDuration
        };
        updateOrderSummary();
    }
});

// =====================
// STEP NAVIGATION
// =====================
function nextStep(step) {
    if (validateCurrentStep()) {
        // Hide current step
        document.getElementById(`formStep${currentStep}`).classList.add('d-none');
        document.getElementById(`step${currentStep}`).classList.remove('active');
        document.getElementById(`step${currentStep}`).classList.add('completed');
        
        // Show next step
        currentStep = step;
        document.getElementById(`formStep${step}`).classList.remove('d-none');
        document.getElementById(`step${step}`).classList.add('active');
        
        // Update summary
        updateOrderSummary();
        
        // Scroll to top
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}

function prevStep(step) {
    // Hide current step
    document.getElementById(`formStep${currentStep}`).classList.add('d-none');
    document.getElementById(`step${currentStep}`).classList.remove('active');
    
    // Show previous step
    currentStep = step;
    document.getElementById(`formStep${step}`).classList.remove('d-none');
    document.getElementById(`step${step}`).classList.add('active');
    document.getElementById(`step${step}`).classList.remove('completed');
    
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// =====================
// VALIDATION
// =====================
function validateCurrentStep() {
    clearErrors();
    
    if (currentStep === 1) {
        const serviceRadio = document.querySelector('input[name="service_id"]:checked');
        if (!serviceRadio) {
            showSwalError('Silakan pilih layanan terlebih dahulu');
            return false;
        }
        return true;
    }
    
    if (currentStep === 2) {
        const shoeType = document.getElementById('shoe_type').value;
        if (!shoeType) {
            showError('shoe_type', 'Jenis sepatu wajib dipilih');
            showSwalError('Jenis sepatu wajib dipilih');
            return false;
        }
        return true;
    }
    
    if (currentStep === 3) {
        const branchId = document.querySelector('input[name="branch_id"]:checked');
        const pickupAddress = document.getElementById('pickup_address').value;
        const pickupDate = document.getElementById('pickup_date').value;
        const pickupTime = document.getElementById('pickup_time').value;
        
        let errors = [];
        
        if (!branchId) {
            errors.push('Silakan pilih cabang terlebih dahulu');
        }
        if (!pickupAddress) {
            showError('pickup_address', 'Alamat penjemputan wajib diisi');
            errors.push('Alamat penjemputan wajib diisi');
        } else if (pickupAddress.length < 10) {
            showError('pickup_address', 'Alamat penjemputan minimal 10 karakter');
            errors.push('Alamat penjemputan minimal 10 karakter');
        }
        if (!pickupDate) {
            showError('pickup_date', 'Tanggal penjemputan wajib dipilih');
            errors.push('Tanggal penjemputan wajib dipilih');
        }
        if (!pickupTime) {
            showError('pickup_time', 'Waktu penjemputan wajib dipilih');
            errors.push('Waktu penjemputan wajib dipilih');
        }
        
        if (errors.length > 0) {
            showSwalError(errors.join('<br>'));
            return false;
        }
        return true;
    }
    
    return true;
}

// =====================
// SERVICE SELECTION
// =====================
function initServiceSelection() {
    document.querySelectorAll('.service-option').forEach(option => {
        option.addEventListener('click', function() {
            document.querySelectorAll('.service-option').forEach(opt => {
                opt.classList.remove('selected');
            });
            
            this.classList.add('selected');
            
            const radio = this.querySelector('input[type="radio"]');
            radio.checked = true;
            
            selectedService = {
                id: this.dataset.serviceId,
                name: this.dataset.serviceName,
                price: parseFloat(this.dataset.servicePrice),
                duration: this.dataset.serviceDuration
            };
            
            updateOrderSummary();
        });
    });
}

// =====================
// BRANCH SELECTION
// =====================
function initBranchSelection() {
    document.querySelectorAll('.branch-option').forEach(option => {
        option.addEventListener('click', function() {
            document.querySelectorAll('.branch-option').forEach(opt => {
                opt.classList.remove('selected');
            });
            
            this.classList.add('selected');
            
            const radio = this.querySelector('input[type="radio"]');
            radio.checked = true;
            
            selectedBranch = {
                id: this.dataset.branchId
            };
        });
    });
}

// =====================
// ORDER SUMMARY
// =====================
function updateOrderSummary() {
    // Update service info
    if (selectedService) {
        document.getElementById('selectedServiceName').textContent = selectedService.name;
        document.getElementById('selectedServiceDuration').textContent = selectedService.duration + ' Jam';
        document.getElementById('totalPrice').textContent = formatRupiah(selectedService.price);
    }
    
    // Update shoe details
    const shoeType = document.getElementById('shoe_type')?.value;
    const shoeSize = document.getElementById('shoe_size')?.value;
    const specialNotes = document.getElementById('special_notes')?.value;
    
    if (shoeType) {
        let shoeInfo = `Jenis: ${shoeType}`;
        if (shoeSize) shoeInfo += `<br>Ukuran: ${shoeSize}`;
        if (specialNotes) shoeInfo += `<br>Catatan: ${specialNotes.substring(0, 50)}${specialNotes.length > 50 ? '...' : ''}`;
        document.getElementById('shoeDetails').innerHTML = shoeInfo;
    }
    
    // Update pickup info
    const pickupDate = document.getElementById('pickup_date')?.value;
    const pickupTime = document.getElementById('pickup_time')?.value;
    const pickupAddress = document.getElementById('pickup_address')?.value;
    
    if (pickupDate || pickupTime || pickupAddress) {
        let pickupInfo = '';
        if (pickupDate) pickupInfo += `Tanggal: ${formatDate(pickupDate)}<br>`;
        if (pickupTime) pickupInfo += `Waktu: ${pickupTime}<br>`;
        if (pickupAddress) pickupInfo += `Alamat: ${pickupAddress.substring(0, 40)}${pickupAddress.length > 40 ? '...' : ''}`;
        document.getElementById('pickupDetails').innerHTML = pickupInfo;
    }
}

// =====================
// FORM SUBMISSION
// =====================
function initFormValidation() {
    document.getElementById('orderForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        clearErrors();
        
        // Validate name and phone (required for all users)
        const guestName = document.getElementById('guest_name')?.value?.trim();
        const guestPhone = document.getElementById('guest_phone')?.value?.trim();
        
        if (!guestName || guestName.length < 3) {
            showError('name', 'Nama lengkap wajib diisi (minimal 3 karakter)');
            showSwalError('Nama lengkap wajib diisi (minimal 3 karakter)');
            return;
        }
        
        if (!guestPhone || guestPhone.length < 10) {
            showError('phone', 'Nomor HP wajib diisi (minimal 10 digit)');
            showSwalError('Nomor HP wajib diisi (minimal 10 digit)');
            return;
        }
        
        // Validate plastic bag confirmation
        const plasticBagConfirmed = document.getElementById('plastic_bag_confirmed').checked;
        if (!plasticBagConfirmed) {
            showError('plastic_bag_confirmed', 'Anda harus menyetujui penggunaan kantong plastik');
            showSwalError('Anda harus menyetujui penggunaan kantong plastik');
            return;
        }
        
        // Validate payment method
        const paymentMethod = document.querySelector('input[name="payment_method"]:checked');
        if (!paymentMethod) {
            showError('payment_method', 'Silakan pilih metode pembayaran');
            showSwalError('Silakan pilih metode pembayaran');
            return;
        }
        
        // Show loading
        setLoadingState(true);
        
        // Prepare form data
        const formData = new FormData(this);
        
        // Use appropriate endpoint based on login status
        const endpoint = isUserLoggedIn ? '/order/checkout' : '/order/guest-checkout';
        
        try {
            const response = await fetch(`${BASE_URL}${endpoint}`, {
                method: 'POST',
                body: new URLSearchParams(formData),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            const result = await response.json();
            
            console.log('Checkout Response:', result); // Debug
            
            if (result.success) {
                // Show success with SweetAlert
                Swal.fire({
                    icon: 'success',
                    title: 'Pesanan Berhasil!',
                    html: `
                        <p>${result.message || 'Pesanan Anda telah berhasil dibuat.'}</p>
                        <div class="alert alert-info mt-3">
                            <strong>Nomor Pesanan:</strong><br>
                            <h4 class="text-primary mb-0">${result.data?.order_number || 'N/A'}</h4>
                        </div>
                        <small class="text-muted">Simpan nomor pesanan untuk tracking</small>
                    `,
                    showCancelButton: true,
                    confirmButtonText: '<i class="bi bi-eye"></i> Lihat Detail',
                    cancelButtonText: '<i class="bi bi-house"></i> Ke Beranda',
                    confirmButtonColor: '#667eea',
                    cancelButtonColor: '#6c757d',
                    allowOutsideClick: false
                }).then((swalResult) => {
                    if (swalResult.isConfirmed && result.redirect) {
                        window.location.href = result.redirect;
                    } else {
                        window.location.href = BASE_URL;
                    }
                });
                
            } else {
                // Handle require login
                if (result.require_login) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Login Diperlukan',
                        text: result.message || 'Silakan login terlebih dahulu',
                        showCancelButton: true,
                        confirmButtonText: 'Login Sekarang',
                        cancelButtonText: 'Lanjut sebagai Tamu',
                        confirmButtonColor: '#667eea'
                    }).then((swalResult) => {
                        if (swalResult.isConfirmed) {
                            window.location.href = result.redirect || `${BASE_URL}/auth/login`;
                        }
                        // If cancel, user can continue as guest
                    });
                    return;
                }
                
                // Handle validation errors
                if (result.errors) {
                    showErrors(result.errors);
                    
                    // Format error messages for SweetAlert
                    let errorMessages = [];
                    for (const [field, messages] of Object.entries(result.errors)) {
                        const message = Array.isArray(messages) ? messages[0] : messages;
                        errorMessages.push(`• ${message}`);
                    }
                    
                    showSwalError(errorMessages.join('<br>'), 'Validasi Gagal');
                } else {
                    showSwalError(result.message || 'Gagal membuat pesanan');
                }
            }
        } catch (error) {
            console.error('Order error:', error);
            showSwalError('Terjadi kesalahan koneksi. Silakan coba lagi.');
        } finally {
            setLoadingState(false);
        }
    });
}

// =====================
// UI HELPERS
// =====================
function setLoadingState(loading) {
    const btnSubmit = document.getElementById('btnSubmit');
    const btnText = document.getElementById('btnText');
    const btnLoading = document.getElementById('btnLoading');
    
    if (loading) {
        btnSubmit.disabled = true;
        btnText.classList.add('d-none');
        btnLoading.classList.remove('d-none');
    } else {
        btnSubmit.disabled = false;
        btnText.classList.remove('d-none');
        btnLoading.classList.add('d-none');
    }
}

// =====================
// SWEETALERT HELPERS
// =====================
function showSwalError(message, title = 'Oops!') {
    Swal.fire({
        icon: 'error',
        title: title,
        html: message,
        confirmButtonColor: '#667eea'
    });
}

function showSwalSuccess(message, title = 'Berhasil!') {
    Swal.fire({
        icon: 'success',
        title: title,
        html: message,
        confirmButtonColor: '#667eea'
    });
}

function showSwalWarning(message, title = 'Perhatian') {
    Swal.fire({
        icon: 'warning',
        title: title,
        html: message,
        confirmButtonColor: '#667eea'
    });
}

// =====================
// UTILITY FUNCTIONS
// =====================
function formatRupiah(amount) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(amount);
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

function clearErrors() {
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
}

function showError(fieldName, message) {
    const field = document.getElementById(fieldName);
    const errorDiv = document.getElementById(`error-${fieldName}`);
    
    if (field) field.classList.add('is-invalid');
    if (errorDiv) errorDiv.textContent = message;
}

function showErrors(errors) {
    for (const [field, messages] of Object.entries(errors)) {
        const message = Array.isArray(messages) ? messages[0] : messages;
        showError(field, message);
    }
}
// === CONFIGURATION ===
const BASE_URL = window.location.origin;

// === UTILITY FUNCTIONS ===

// Clear all validation errors
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

// Show alert message
function showAlert(message, type = 'danger') {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    document.getElementById('alert-container').innerHTML = alertHtml;

    // Auto dismiss after 5 seconds
    setTimeout(() => {
        const alert = document.querySelector('.alert');
        if (alert) {
            alert.classList.remove('show');
            setTimeout(() => alert.remove(), 150);
        }
    }, 5000);
}

// === EVENT LISTENERS ===

// Toggle password visibility
document.querySelectorAll('[id^="togglePassword"]').forEach(btn => {
    btn.addEventListener('click', function() {
        const passwordInput = this.closest('.input-group').querySelector('input');
        const icon = this.querySelector('i');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    });
});

// === LOGIN FORM HANDLING ===
const loginForm = document.getElementById('loginForm');
if (loginForm) {
    loginForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        clearErrors();

        const btnLogin = document.getElementById('btnLogin');
        const btnText = document.getElementById('btnText');
        const btnLoading = document.getElementById('btnLoading');

        // Disable button and show loading
        btnLogin.disabled = true;
        btnText.classList.add('d-none');
        btnLoading.classList.remove('d-none');

        const formData = new FormData(this);
        const data = Object.fromEntries(formData);

        try {
            const response = await fetch(`${BASE_URL}/auth/process-login`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams(data)
            });

            const result = await response.json();

            if (result.success) {
                showAlert(result.message, 'success');
                setTimeout(() => {
                    window.location.href = result.redirect;
                }, 1000);
            } else {
                if (result.errors) {
                    showErrors(result.errors);
                } else {
                    showAlert(result.message, 'danger');
                }

                // Re-enable button
                btnLogin.disabled = false;
                btnText.classList.remove('d-none');
                btnLoading.classList.add('d-none');
            }
        } catch (error) {
            console.error('Login error:', error);
            showAlert('Terjadi kesalahan. Silakan coba lagi.', 'danger');

            // Re-enable button
            btnLogin.disabled = false;
            btnText.classList.remove('d-none');
            btnLoading.classList.add('d-none');
        }
    });
}

// === REGISTER FORM HANDLING ===
const registerForm = document.getElementById('registerForm');
if (registerForm) {
    registerForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        clearErrors();

        const btnRegister = document.getElementById('btnRegister');
        const btnText = btnRegister.querySelector('#btnText');
        const btnLoading = btnRegister.querySelector('#btnLoading');

        // Disable button and show loading
        btnRegister.disabled = true;
        btnText.classList.add('d-none');
        btnLoading.classList.remove('d-none');

        const formData = new FormData(this);
        const data = Object.fromEntries(formData);

        try {
            const response = await fetch(`${BASE_URL}/auth/process-register`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams(data)
            });

            const result = await response.json();

            if (result.success) {
                showAlert(result.message, 'success');
                setTimeout(() => {
                    window.location.href = result.redirect;
                }, 1000);
            } else {
                if (result.errors) {
                    showErrors(result.errors);
                } else {
                    showAlert(result.message, 'danger');
                }

                // Re-enable button
                btnRegister.disabled = false;
                btnText.classList.remove('d-none');
                btnLoading.classList.add('d-none');
            }
        } catch (error) {
            console.error('Register error:', error);
            showAlert('Terjadi kesalahan. Silakan coba lagi.', 'danger');

            // Re-enable button
            btnRegister.disabled = false;
            btnText.classList.remove('d-none');
            btnLoading.classList.add('d-none');
        }
    });
}

// === GUEST CHECKOUT HANDLING ===
const btnGuestCheckout = document.getElementById('btnGuestCheckout');
if (btnGuestCheckout) {
    btnGuestCheckout.addEventListener('click', async function() {
        clearErrors();

        const guestForm = document.getElementById('guestForm');
        const formData = new FormData(guestForm);
        const data = Object.fromEntries(formData);

        // Basic validation
        if (!data.guest_name || !data.guest_phone) {
            showAlert('Nama dan nomor telepon wajib diisi', 'danger');
            return;
        }

        // Disable button
        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Loading...';

        try {
            const response = await fetch(`${BASE_URL}/auth/guest-checkout`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams(data)
            });

            const result = await response.json();

            if (result.success) {
                window.location.href = result.redirect;
            } else {
                if (result.errors) {
                    showErrors(result.errors);
                } else {
                    showAlert(result.message, 'danger');
                }

                this.disabled = false;
                this.innerHTML = 'Lanjutkan';
            }
        } catch (error) {
            console.error('Guest checkout error:', error);
            showAlert('Terjadi kesalahan. Silakan coba lagi.', 'danger');
            
            // Re-enable button on error
            this.disabled = false;
            this.innerHTML = 'Lanjutkan';
        }
    });
}
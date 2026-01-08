// Order Form Handler
let currentStep = 1;
let selectedServiceData = null;
let selectedBranchData = null;

document.addEventListener('DOMContentLoaded', function() {
  initializeForm();
  attachEventListeners();
});

function initializeForm() {
  const selectedService = document.querySelector('input[name="service_id"]:checked');
  if (selectedService) {
    const serviceOption = selectedService.closest('.service-option');
    if (serviceOption) {
      selectedServiceData = {
        id: serviceOption.dataset.serviceId,
        name: serviceOption.dataset.serviceName,
        price: serviceOption.dataset.servicePrice,
        duration: serviceOption.dataset.serviceDuration
      };
      updateSummary();
    }
  }
}

function attachEventListeners() {
  document.querySelectorAll('.service-option').forEach(option => {
    option.addEventListener('click', function() {
      document.querySelectorAll('.service-option').forEach(o => o.classList.remove('selected'));
      this.classList.add('selected');

      const radio = this.querySelector('input[type="radio"]');
      if (radio) radio.checked = true;

      selectedServiceData = {
        id: this.dataset.serviceId,
        name: this.dataset.serviceName,
        price: this.dataset.servicePrice,
        duration: this.dataset.serviceDuration
      };
      updateSummary();
    });
  });

  document.querySelectorAll('.branch-option').forEach(option => {
    option.addEventListener('click', function() {
      document.querySelectorAll('.branch-option').forEach(o => o.classList.remove('selected'));
      this.classList.add('selected');

      const radio = this.querySelector('input[type="radio"]');
      if (radio) radio.checked = true;

      selectedBranchData = { id: this.dataset.branchId };
    });
  });

  const form = document.getElementById('orderForm');
  if (form) {
    form.addEventListener('submit', handleFormSubmit);
  }

  ['shoe_type', 'shoe_size', 'special_notes', 'pickup_address', 'pickup_date', 'pickup_time'].forEach(id => {
    const el = document.getElementById(id);
    if (!el) return;
    el.addEventListener('input', updateSummary);
    el.addEventListener('change', updateSummary);
  });
}

// Step Navigation
function nextStep(step) {
  if (!validateStep(currentStep)) return;

  document.getElementById(`formStep${currentStep}`).classList.add('d-none');
  document.getElementById(`step${currentStep}`).classList.remove('active');
  document.getElementById(`step${currentStep}`).classList.add('completed');

  currentStep = step;
  document.getElementById(`formStep${step}`).classList.remove('d-none');
  document.getElementById(`step${step}`).classList.add('active');

  updateSummary();
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

function prevStep(step) {
  document.getElementById(`formStep${currentStep}`).classList.add('d-none');
  document.getElementById(`step${currentStep}`).classList.remove('active');

  currentStep = step;
  document.getElementById(`formStep${step}`).classList.remove('d-none');
  document.getElementById(`step${step}`).classList.add('active');
  document.getElementById(`step${step}`).classList.remove('completed');

  window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Validation
function validateStep(step) {
  clearErrors();

  switch(step) {
    case 1: {
      const serviceId = document.querySelector('input[name="service_id"]:checked');
      if (!serviceId) return showError('service_id', 'Pilih salah satu layanan');
      break;
    }
    case 2: {
      const shoeType = document.getElementById('shoe_type');
      if (!shoeType || !shoeType.value) return showError('shoe_type', 'Jenis sepatu harus dipilih');
      break;
    }
    case 3: {
      const branchId = document.querySelector('input[name="branch_id"]:checked');
      const pickupAddress = document.getElementById('pickup_address');
      const pickupDate = document.getElementById('pickup_date');
      const pickupTime = document.getElementById('pickup_time');

      if (!branchId) return showError('branch_id', 'Pilih cabang terdekat');
      if (!pickupAddress || pickupAddress.value.length < 10) return showError('pickup_address', 'Alamat penjemputan minimal 10 karakter');
      if (!pickupDate || !pickupDate.value) return showError('pickup_date', 'Tanggal penjemputan harus diisi');
      if (!pickupTime || !pickupTime.value) return showError('pickup_time', 'Waktu penjemputan harus diisi');

      const hour = parseInt(pickupTime.value.split(':')[0], 10);
      if (hour < 8 || hour >= 20) return showError('pickup_time', 'Waktu operasional: 08:00 - 20:00');
      break;
    }
    case 4: {
      const guestName = document.getElementById('guest_name');
      const guestPhone = document.getElementById('guest_phone');
      const plasticBag = document.getElementById('plastic_bag_confirmed');
      const paymentMethod = document.querySelector('input[name="payment_method"]:checked');

      if (!guestName || !guestName.value.trim()) return showError('name', 'Nama lengkap harus diisi');
      if (!guestPhone || !guestPhone.value.trim()) return showError('phone', 'Nomor HP harus diisi');

      const phoneRegex = /^(\+62|62|0)[0-9]{9,12}$/;
      if (!phoneRegex.test(guestPhone.value.replace(/\s/g, ''))) {
        return showError('phone', 'Format nomor HP tidak valid (contoh: 081234567890)');
      }

      if (!plasticBag || !plasticBag.checked) return showError('plastic_bag_confirmed', 'Anda harus menyetujui penggunaan kantong plastik');
      if (!paymentMethod) return showError('payment_method', 'Pilih metode pembayaran');
      break;
    }
  }

  return true;
}

function showError(fieldName, message) {
  const errorElement = document.getElementById(`error-${fieldName}`);
  const inputElement = document.getElementById(fieldName) || document.querySelector(`input[name="${fieldName}"]`);

  if (errorElement) {
    errorElement.textContent = message;
    errorElement.style.display = 'block';
  }
  if (inputElement) inputElement.classList.add('is-invalid');

  Swal.fire({ icon: 'error', title: 'Oops!', text: message, confirmButtonColor: '#667eea' });
  return false;
}

function clearErrors() {
  document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
  document.querySelectorAll('.invalid-feedback').forEach(el => el.style.display = 'none');
}

// Update Summary
function updateSummary() {
  if (selectedServiceData) {
    document.getElementById('selectedServiceName').textContent = selectedServiceData.name;
    document.getElementById('selectedServiceDuration').textContent = `${selectedServiceData.duration} Jam`;
    document.getElementById('totalPrice').textContent = formatRupiah(selectedServiceData.price);
  }

  const shoeType = document.getElementById('shoe_type')?.value;
  const shoeSize = document.getElementById('shoe_size')?.value;
  const specialNotes = document.getElementById('special_notes')?.value;

  let shoeDetails = 'Belum diisi';
  if (shoeType) {
    shoeDetails = `<strong>${shoeType}</strong>`;
    if (shoeSize) shoeDetails += ` - Ukuran: ${shoeSize}`;
    if (specialNotes) shoeDetails += `<br><small class="text-muted">${specialNotes.substring(0, 50)}${specialNotes.length > 50 ? '...' : ''}</small>`;
  }
  const shoeDetailsEl = document.getElementById('shoeDetails');
  if (shoeDetailsEl) shoeDetailsEl.innerHTML = shoeDetails;

  const pickupAddress = document.getElementById('pickup_address')?.value;
  const pickupDate = document.getElementById('pickup_date')?.value;
  const pickupTime = document.getElementById('pickup_time')?.value;

  let pickupDetails = 'Belum diisi';
  if (pickupDate && pickupTime) {
    const dateObj = new Date(pickupDate);
    const dateStr = dateObj.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
    pickupDetails = `<strong>${dateStr}</strong><br>Pukul ${pickupTime}`;
    if (pickupAddress) pickupDetails += `<br><small class="text-muted">${pickupAddress.substring(0, 50)}${pickupAddress.length > 50 ? '...' : ''}</small>`;
  }
  const pickupDetailsEl = document.getElementById('pickupDetails');
  if (pickupDetailsEl) pickupDetailsEl.innerHTML = pickupDetails;
}

// Form Submission
async function handleFormSubmit(e) {
  e.preventDefault();
  if (!validateStep(4)) return;

  const form = e.target;
  const formData = new FormData(form);

  const btnSubmit = document.getElementById('btnSubmit');
  const btnText = document.getElementById('btnText');
  const btnLoading = document.getElementById('btnLoading');

  btnSubmit.disabled = true;
  btnText.classList.add('d-none');
  btnLoading.classList.remove('d-none');

  try {
    const endpoint = (typeof USER_LOGGED_IN !== 'undefined' && USER_LOGGED_IN)
      ? `${BASE_URL}/order/checkout`
      : `${BASE_URL}/order/guest-checkout`;

    const response = await fetch(endpoint, {
      method: 'POST',
      body: formData
    });

    const result = await response.json();

    if (result.success) {
      await Swal.fire({
        icon: 'success',
        title: 'Pesanan Berhasil Dibuat!',
        text: 'Anda akan diarahkan ke halaman pembayaran...',
        timer: 1800,
        showConfirmButton: false
      });

      const orderNumber = result.data?.order_number || result.order?.order_number || result.order_number;
      if (orderNumber) {
        window.location.href = `${BASE_URL}/payment/${encodeURIComponent(orderNumber)}`;
      } else {
        window.location.href = result.redirect || `${BASE_URL}/user/orders`;
      }
      return;
    }

    let errorMessage = result.message || 'Gagal membuat pesanan';
    if (result.errors) {
      const errorMessages = [];
      for (const [_, messages] of Object.entries(result.errors)) {
        const msg = Array.isArray(messages) ? messages[0] : messages;
        errorMessages.push(`• ${msg}`);
      }
      errorMessage = errorMessages.join('\n');
    }

    Swal.fire({ icon: 'error', title: 'Gagal!', text: errorMessage, confirmButtonColor: '#667eea' });

  } catch (error) {
    console.error('Error:', error);
    Swal.fire({ icon: 'error', title: 'Oops!', text: 'Terjadi kesalahan. Silakan coba lagi.', confirmButtonColor: '#667eea' });
  } finally {
    btnSubmit.disabled = false;
    btnText.classList.remove('d-none');
    btnLoading.classList.add('d-none');
  }
}

function formatRupiah(amount) {
  const number = parseInt(amount, 10);
  return 'Rp ' + (isNaN(number) ? 0 : number).toLocaleString('id-ID');
}

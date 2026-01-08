<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
  .payment-method{border:2px solid #e9ecef;border-radius:16px;padding:18px;cursor:pointer;transition:.2s ease;background:#fff;height:100%}
  .payment-method:hover{border-color:#667eea;box-shadow:0 10px 20px rgba(102,126,234,.12);transform:translateY(-1px)}
  .payment-method.selected{border-color:#667eea;background:#f6f7ff}
  .payment-details-card{border-radius:16px;overflow:hidden}
  .qr-box{display:flex;align-items:center;justify-content:center;padding:18px;background:#fff;border:1px solid #e9ecef;border-radius:12px;min-height:320px}
  .qr-img{max-width:280px;width:100%;height:auto}
  .va-number-card{background:linear-gradient(135deg, rgba(102,126,234,.12) 0%, rgba(118,75,162,.12) 100%);border:1px solid rgba(102,126,234,.25);border-radius:14px;padding:18px}
  .va-number{font-size:1.6rem;font-weight:800;letter-spacing:1px}
  #loadingOverlay{position:fixed;inset:0;background:rgba(0,0,0,.35);display:none;align-items:center;justify-content:center;z-index:9999}
  #loadingOverlay.active{display:flex}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php
$order = $order ?? null;
$payment = $payment ?? null;
$error = $error ?? null;
$orderNumber = $orderNumber ?? ($order['order_number'] ?? null);

$hasOrder = !empty($orderNumber);
$hasPayment = is_array($payment) && !empty($payment);
$paymentChannel = $hasPayment ? ($payment['channel'] ?? null) : null;

$totalPrice = (float)($order['total_amount'] ?? $order['total_price'] ?? 0);

$qrisPayload = ($hasPayment && $paymentChannel === 'qris') ? ($payment['qris_payload'] ?? null) : null;
$qrisQrString = ($hasPayment && $paymentChannel === 'qris') ? ($payment['qr_string'] ?? null) : null;

$vaNumber = ($hasPayment && in_array($paymentChannel, ['va_bni','va_mandiri'], true)) ? ($payment['va_number'] ?? null) : null;
?>

<div id="loadingOverlay">
  <div class="bg-white rounded-4 p-4 text-center shadow">
    <div class="spinner-border" role="status"></div>
    <div class="mt-3 fw-bold">Memproses...</div>
    <div class="text-muted small">Mohon tunggu sebentar</div>
  </div>
</div>

<?php if ($hasOrder): ?>
<section class="py-4 bg-light">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <h2 class="mb-1 fw-bold">Pembayaran</h2>
        <p class="text-muted mb-0">Nomor Pesanan: <strong><?= esc($orderNumber) ?></strong></p>
      </div>
      <a href="<?= base_url('/') ?>" class="btn btn-outline-secondary"><i class="bi bi-house"></i> Beranda</a>
    </div>
  </div>
</section>

<section class="py-5">
  <div class="container">
    <?php if ($error): ?>
      <div class="alert alert-danger"><i class="bi bi-exclamation-triangle"></i> <?= esc($error) ?></div>
    <?php endif; ?>

    <div class="row g-4">
      <div class="col-lg-8">

        <div class="card shadow-sm mb-4">
          <div class="card-header bg-white border-bottom">
            <h5 class="mb-0 fw-bold"><i class="bi bi-wallet2"></i> Pilih Metode Pembayaran</h5>
          </div>
          <div class="card-body p-4">
            <div class="row g-3">
              <div class="col-md-4">
                <div class="payment-method <?= $paymentChannel==='qris' ? 'selected':'' ?>" data-channel="qris" onclick="selectPaymentMethod('qris')">
                  <div class="text-center mb-2"><i class="bi bi-qr-code display-5 text-primary"></i></div>
                  <h6 class="fw-bold text-center mb-1">QRIS</h6>
                  <div class="text-muted small text-center">Scan & Pay</div>
                  <div class="text-muted small text-center">Semua e-wallet</div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="payment-method <?= $paymentChannel==='va_bni' ? 'selected':'' ?>" data-channel="va_bni" onclick="selectPaymentMethod('va_bni')">
                  <div class="text-center mb-2"><i class="bi bi-bank display-5 text-success"></i></div>
                  <h6 class="fw-bold text-center mb-1">BNI VA</h6>
                  <div class="text-muted small text-center">Virtual Account</div>
                  <div class="text-muted small text-center">Transfer via BNI</div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="payment-method <?= $paymentChannel==='va_mandiri' ? 'selected':'' ?>" data-channel="va_mandiri" onclick="selectPaymentMethod('va_mandiri')">
                  <div class="text-center mb-2"><i class="bi bi-bank display-5" style="color:#003d79;"></i></div>
                  <h6 class="fw-bold text-center mb-1">Mandiri VA</h6>
                  <div class="text-muted small text-center">Virtual Account</div>
                  <div class="text-muted small text-center">Transfer via Mandiri</div>
                </div>
              </div>
            </div>

            <div class="d-flex gap-2 mt-4">
              <button class="btn btn-primary" type="button" onclick="createPayment()"><i class="bi bi-lock"></i> Buat Pembayaran</button>
              <button class="btn btn-outline-secondary" type="button" onclick="manualRefreshStatus()"><i class="bi bi-arrow-clockwise"></i> Refresh Status</button>
            </div>

            <div class="alert alert-info mt-4 mb-0">
              <i class="bi bi-info-circle"></i>
              <small>Pilih metode lalu klik <strong>Buat Pembayaran</strong>. QR / VA akan muncul di bawah.</small>
            </div>
          </div>
        </div>

        <!-- QRIS -->
        <div class="card shadow-sm payment-details-card mb-4 <?= (!$hasPayment || $paymentChannel!=='qris') ? 'd-none':'' ?>" id="qrisDetails">
          <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-qr-code"></i> Pembayaran QRIS</h5>
          </div>
          <div class="card-body p-4 text-center">
            <div class="fw-bold mb-2">Scan QRIS untuk Membayar</div>

            <div class="qr-box">
              <div id="qrisQrWrap" class="w-100 d-flex justify-content-center"></div>
            </div>

            <div id="qrisError" class="text-danger mt-2 small d-none">
              Gagal memuat QR dari URL. (Fallback ke QR string jika tersedia) — klik Refresh Status.
            </div>

            <div class="alert alert-info text-start mt-3">
              <div class="fw-bold">Total Pembayaran</div>
              <div class="fs-4 fw-bold text-primary"><?= format_rupiah($totalPrice) ?></div>
            </div>
          </div>
        </div>

        <!-- VA -->
        <div class="card shadow-sm payment-details-card mb-4 <?= (!$hasPayment || !in_array($paymentChannel,['va_bni','va_mandiri'],true)) ? 'd-none':'' ?>" id="vaDetails">
          <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="bi bi-bank"></i> Virtual Account <span id="vaBankName"></span></h5>
          </div>
          <div class="card-body p-4">
            <div class="va-number-card mb-3">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="fw-bold">Nomor Virtual Account</div>
                <button class="btn btn-light btn-sm" type="button" onclick="copyVANumber()"><i class="bi bi-clipboard"></i> Copy</button>
              </div>
              <div class="va-number" id="vaNumberDisplay"><?= $vaNumber ? esc($vaNumber) : 'Loading...' ?></div>
              <div class="small text-muted">a.n. <strong>Tapak Bersih</strong></div>
            </div>

            <div class="alert alert-info">
              <div class="fw-bold">Total Transfer</div>
              <div class="fs-4 fw-bold text-primary"><?= format_rupiah($totalPrice) ?></div>
            </div>
          </div>
        </div>

      </div>

      <div class="col-lg-4">
        <div class="card shadow-sm sticky-top" style="top:20px;">
          <div class="card-header bg-white border-bottom">
            <h5 class="mb-0 fw-bold"><i class="bi bi-receipt"></i> Ringkasan Pesanan</h5>
          </div>
          <div class="card-body">
            <div class="mb-2"><small class="text-muted">Order Number</small><div class="fw-bold"><?= esc($orderNumber) ?></div></div>
            <hr>
            <div class="d-flex justify-content-between"><span>Total</span><strong><?= format_rupiah($totalPrice) ?></strong></div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<?php else: ?>
<section class="py-5"><div class="container text-center py-5"><h2>Pesanan Tidak Ditemukan</h2></div></section>
<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>

<script>
const BASE_URL     = '<?= rtrim(base_url(), '/') ?>';
const ORDER_NUMBER = '<?= esc($orderNumber ?? '') ?>';
let selectedChannel = '<?= esc($paymentChannel ?? 'qris') ?>';
let lastKnownStatus = null;
let statusTimer = null;

function getUnifiedStatus(payment) {
  if (!payment) return null;

  // status lokal dari DB payment
  const s1 = payment.status ? String(payment.status).toLowerCase() : null;

  // status dari midtrans (raw_response.transaction_status)
  const s2 = payment.transaction_status ? String(payment.transaction_status).toLowerCase() : null;

  // Prioritaskan midtrans transaction_status jika ada
  return s2 || s1;
}

function isPaidStatus(status) {
  if (!status) return false;
  const s = String(status).toLowerCase();
  // Midtrans paid-like: settlement/capture (credit card)/success/paid
  return ['settlement', 'capture', 'paid', 'success'].includes(s);
}

function isFailedStatus(status) {
  if (!status) return false;
  const s = String(status).toLowerCase();
  return ['expire', 'expired', 'cancel', 'canceled', 'deny', 'failure', 'failed'].includes(s);
}

async function handleStatusChange(payment) {
  const currentStatus = getUnifiedStatus(payment);

  // pertama kali set baseline
  if (lastKnownStatus === null) {
    lastKnownStatus = currentStatus;
    return;
  }

  // kalau status tidak berubah, skip
  if (currentStatus === lastKnownStatus) return;

  // update status terakhir
  lastKnownStatus = currentStatus;

  // Paid -> popup + redirect
  if (isPaidStatus(currentStatus)) {
    await Swal.fire({
      icon: 'success',
      title: 'Pembayaran Berhasil',
      text: 'Pembayaran kamu sudah terkonfirmasi. Terima kasih!',
      confirmButtonText: 'Lihat Detail'
    });
    window.location.href = `${BASE_URL}/payment/${encodeURIComponent(ORDER_NUMBER)}/success`;
    return;
  }

  // Failed -> popup
  if (isFailedStatus(currentStatus)) {
    Swal.fire({
      icon: 'error',
      title: 'Pembayaran Gagal',
      text: `Status pembayaran: ${currentStatus}`,
    });
  } else {
    // status lain (pending, etc)
    Swal.fire({
      icon: 'info',
      title: 'Status Diperbarui',
      text: `Status sekarang: ${currentStatus}`,
      timer: 1200,
      showConfirmButton: false
    });
  }
}

function selectPaymentMethod(channel) {
  selectedChannel = channel;
  document.querySelectorAll('.payment-method').forEach(m => m.classList.remove('selected'));
  document.querySelector(`.payment-method[data-channel="${channel}"]`)?.classList.add('selected');
}

async function createPayment() {
  const overlay = document.getElementById('loadingOverlay');
  overlay.classList.add('active');

  try {
    const resp = await fetch(`${BASE_URL}/payment/${encodeURIComponent(ORDER_NUMBER)}/pay`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams({ channel: selectedChannel })
    });

    const result = await resp.json();
    if (!result.success || !result.payment) throw new Error(result.message || 'Gagal membuat pembayaran');

    displayPaymentDetails(result.payment);

    Swal.fire({ icon:'success', title:'Berhasil!', text:'Silakan lanjutkan pembayaran.', timer:1200, showConfirmButton:false });
  } catch (e) {
    console.error(e);
    Swal.fire({ icon:'error', title:'Gagal', text: e.message || 'Terjadi kesalahan' });
  } finally {
    overlay.classList.remove('active');
  }
}

function extractQrString(payment) {
  if (payment.qr_string) return payment.qr_string;
  if (payment.raw_response && typeof payment.raw_response === 'string') {
    try { const obj = JSON.parse(payment.raw_response); if (obj?.qr_string) return obj.qr_string; } catch(e){}
  }
  return null;
}

function extractQrisPayload(payment) {
  if (payment.qris_payload) return payment.qris_payload;
  if (payment.raw_response && typeof payment.raw_response === 'string') {
    try {
      const obj = JSON.parse(payment.raw_response);
      if (obj?.actions?.length) {
        const gen = obj.actions.find(a => a.name === 'generate-qr-code' && a.url);
        if (gen?.url) return gen.url;
        const any = obj.actions.find(a => a.url && String(a.url).includes('/qr-code'));
        if (any?.url) return any.url;
      }
    } catch(e){}
  }
  return null;
}

function renderQrFromUrl(url, fallbackQrString) {
  const wrap = document.getElementById('qrisQrWrap');
  const err  = document.getElementById('qrisError');
  wrap.innerHTML = '';
  err.classList.add('d-none');

  if (!url) {
    // langsung fallback ke string
    return renderQrFromString(fallbackQrString);
  }

  // cache-busting biar selalu load fresh
  const bust = (url.includes('?') ? '&' : '?') + 't=' + Date.now();
  const finalUrl = url + bust;

  const img = document.createElement('img');
  img.className = 'qr-img';
  img.alt = 'QRIS QR Code';
  img.referrerPolicy = 'no-referrer';
  img.src = finalUrl;

  img.onload = () => { /* ok */ };
  img.onerror = () => {
    // kalau URL gagal → fallback ke qr_string
    err.classList.remove('d-none');
    renderQrFromString(fallbackQrString);
  };

  wrap.appendChild(img);
}

function renderQrFromString(qrString) {
  const wrap = document.getElementById('qrisQrWrap');
  wrap.innerHTML = '';
  if (!qrString) return;

  // QRCodeJS render
  new QRCode(wrap, {
    text: qrString,
    width: 260,
    height: 260,
    correctLevel: QRCode.CorrectLevel.M
  });
}

function extractVaNumber(payment) {
  if (payment.va_number) return payment.va_number;
  if (payment.raw_response && typeof payment.raw_response === 'string') {
    try {
      const obj = JSON.parse(payment.raw_response);
      if (obj?.permata_va_number) return obj.permata_va_number;
      if (obj?.va_numbers?.[0]?.va_number) return obj.va_numbers[0].va_number;
    } catch(e){}
  }
  return null;
}

function displayPaymentDetails(payment) {
  const channel = payment.channel;

  document.getElementById('qrisDetails')?.classList.add('d-none');
  document.getElementById('vaDetails')?.classList.add('d-none');

  if (channel === 'qris') {
    document.getElementById('qrisDetails')?.classList.remove('d-none');

    const payload = extractQrisPayload(payment);
    const qrStr   = extractQrString(payment);

    // PRIORITAS: render dari qris_payload URL
    renderQrFromUrl(payload, qrStr);

    document.getElementById('qrisDetails')?.scrollIntoView({behavior:'smooth', block:'start'});
    return;
  }

  if (channel === 'va_bni' || channel === 'va_mandiri') {
    document.getElementById('vaDetails')?.classList.remove('d-none');
    document.getElementById('vaBankName').textContent = channel === 'va_bni' ? 'BNI' : 'Mandiri';

    const va = extractVaNumber(payment);
    if (va) document.getElementById('vaNumberDisplay').textContent = va;

    document.getElementById('vaDetails')?.scrollIntoView({behavior:'smooth', block:'start'});
    return;
  }

  Swal.fire({ icon:'warning', title:'Metode tidak didukung', text:'Channel pembayaran tidak dikenali.' });
}

function copyVANumber() {
  const va = document.getElementById('vaNumberDisplay')?.textContent?.trim().replace(/\s/g,'') || '';
  if (!va) return;
  navigator.clipboard.writeText(va).then(() => {
    Swal.fire({ icon:'success', title:'Tersalin', text:'Nomor VA berhasil disalin', timer:1000, showConfirmButton:false });
  });
}

async function manualRefreshStatus() {
  try {
    const url = `${BASE_URL}/payment/${encodeURIComponent(ORDER_NUMBER)}/status?t=${Date.now()}`; // cache busting
    const resp = await fetch(url, { cache: 'no-store' });
    const result = await resp.json();

    if (!result.success || !result.payment) {
      Swal.fire({ icon:'warning', title:'Gagal', text: result.message || 'Gagal mengambil status' });
      return;
    }

    // Update tampilan detail pembayaran (QR/VA)
    displayPaymentDetails(result.payment);

    // Cek perubahan status & munculkan popup kalau paid/fail
    await handleStatusChange(result.payment);

  } catch (e) {
    console.error(e);
    Swal.fire({ icon:'error', title:'Error', text:'Gagal mengambil status (cek endpoint /status).' });
  }
}


function startAutoPolling() {
  // polling tiap 6 detik
  if (statusTimer) clearInterval(statusTimer);

  statusTimer = setInterval(async () => {
    try {
      const url = `${BASE_URL}/payment/${encodeURIComponent(ORDER_NUMBER)}/status?t=${Date.now()}`;
      const resp = await fetch(url, { cache: 'no-store' });
      const result = await resp.json();
      if (result.success && result.payment) {
        // update detail (optional)
        displayPaymentDetails(result.payment);

        // cek paid/failed
        await handleStatusChange(result.payment);

        // stop polling kalau sudah paid/failed
        const st = getUnifiedStatus(result.payment);
        if (isPaidStatus(st) || isFailedStatus(st)) {
          clearInterval(statusTimer);
          statusTimer = null;
        }
      }
    } catch (e) {
      // kalau error, biarin aja polling lanjut (tidak spam popup)
      console.warn('polling error', e);
    }
  }, 6000);
}

document.addEventListener('DOMContentLoaded', () => {
  // baseline status awal dari PHP (kalau payment sudah ada)
  <?php if (!empty($payment)): ?>
    try {
      const initialPayment = <?= json_encode($payment) ?>;
      lastKnownStatus = getUnifiedStatus(initialPayment);
    } catch (e) {}
  <?php endif; ?>

  startAutoPolling();
});

</script>
<?= $this->endSection() ?>

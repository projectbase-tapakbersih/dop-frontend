<?= $this->extend('layouts/main') ?>

<?php
// Helper function for formatting currency if not exists
if (!function_exists('format_rupiah')) {
    function format_rupiah($number) {
        return 'Rp ' . number_format((float)$number, 0, ',', '.');
    }
}
?>

<?= $this->section('styles') ?>
<style>
    .stat-card {
        border-radius: 12px;
        transition: transform 0.2s, box-shadow 0.2s;
        border: none;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }
    .chart-container {
        position: relative;
        height: 300px;
    }
    .status-badge {
        font-size: 12px;
        padding: 5px 10px;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<section class="py-4 bg-light">
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-1 fw-bold">
                    <i class="bi bi-speedometer2"></i> Dashboard Admin
                </h2>
                <p class="text-muted mb-0">Selamat datang, <?= esc(session()->get('user_name') ?? 'Admin') ?>!</p>
            </div>
            <div>
                <span class="badge bg-primary fs-6">
                    <i class="bi bi-calendar"></i> <?= date('d F Y') ?>
                </span>
            </div>
        </div>
    </div>
</section>

<section class="py-4">
    <div class="container-fluid px-4">
        
        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
            <!-- Total Orders -->
            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="card stat-card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3">
                                <i class="bi bi-box-seam"></i>
                            </div>
                            <div>
                                <h3 class="mb-0 fw-bold"><?= $stats['total_orders'] ?? 0 ?></h3>
                                <small class="text-muted">Total Pesanan</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Waiting Pickup -->
            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="card stat-card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon bg-warning bg-opacity-10 text-warning me-3">
                                <i class="bi bi-hourglass-split"></i>
                            </div>
                            <div>
                                <h3 class="mb-0 fw-bold"><?= $stats['waiting_pickup'] ?? 0 ?></h3>
                                <small class="text-muted">Menunggu Pickup</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- In Process -->
            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="card stat-card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon bg-info bg-opacity-10 text-info me-3">
                                <i class="bi bi-gear"></i>
                            </div>
                            <div>
                                <h3 class="mb-0 fw-bold"><?= $stats['in_process'] ?? 0 ?></h3>
                                <small class="text-muted">Diproses</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Completed -->
            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="card stat-card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon bg-success bg-opacity-10 text-success me-3">
                                <i class="bi bi-check-circle"></i>
                            </div>
                            <div>
                                <h3 class="mb-0 fw-bold"><?= $stats['completed'] ?? 0 ?></h3>
                                <small class="text-muted">Selesai</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Cancelled -->
            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="card stat-card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon bg-danger bg-opacity-10 text-danger me-3">
                                <i class="bi bi-x-circle"></i>
                            </div>
                            <div>
                                <h3 class="mb-0 fw-bold"><?= $stats['cancelled'] ?? 0 ?></h3>
                                <small class="text-muted">Dibatalkan</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Total Revenue -->
            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="card stat-card shadow-sm h-100 bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon bg-white bg-opacity-25 text-white me-3">
                                <i class="bi bi-currency-dollar"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold"><?= format_rupiah($stats['total_revenue'] ?? 0) ?></h5>
                                <small class="text-white-50">Total Revenue</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Charts -->
            <div class="col-lg-8">
                <!-- Revenue Chart -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-graph-up"></i> Grafik Pendapatan</h5>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-primary active" onclick="showDailyChart()">Harian</button>
                            <button class="btn btn-outline-primary" onclick="showMonthlyChart()">Bulanan</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Recent Orders -->
                <div class="card shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-clock-history"></i> Pesanan Terbaru</h5>
                        <a href="<?= base_url('admin/orders') ?>" class="btn btn-sm btn-primary">
                            Lihat Semua <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Order #</th>
                                        <th>Customer</th>
                                        <th>Layanan</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Pembayaran</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($recent_orders)): ?>
                                        <?php foreach (array_slice($recent_orders, 0, 5) as $order): ?>
                                            <?php
                                            $status = $order['order_status'] ?? 'pending';
                                            $paymentStatus = $order['payment_status'] ?? 'pending';
                                            $statusConfig = [
                                                'waiting_pickup' => ['badge' => 'warning', 'label' => 'Waiting Pickup'],
                                                'picked_up' => ['badge' => 'info', 'label' => 'Picked Up'],
                                                'in_process' => ['badge' => 'primary', 'label' => 'In Process'],
                                                'processing' => ['badge' => 'primary', 'label' => 'Processing'],
                                                'washing' => ['badge' => 'primary', 'label' => 'Washing'],
                                                'drying' => ['badge' => 'primary', 'label' => 'Drying'],
                                                'quality_check' => ['badge' => 'info', 'label' => 'QC'],
                                                'ready' => ['badge' => 'success', 'label' => 'Ready'],
                                                'on_delivery' => ['badge' => 'info', 'label' => 'Delivery'],
                                                'completed' => ['badge' => 'success', 'label' => 'Completed'],
                                                'cancelled' => ['badge' => 'danger', 'label' => 'Cancelled']
                                            ];
                                            $statusInfo = $statusConfig[$status] ?? ['badge' => 'secondary', 'label' => ucfirst($status)];
                                            
                                            // Get service names
                                            $services = [];
                                            if (!empty($order['items'])) {
                                                foreach ($order['items'] as $item) {
                                                    $services[] = $item['service']['name'] ?? 'Service';
                                                }
                                            }
                                            ?>
                                            <tr>
                                                <td>
                                                    <strong class="text-primary"><?= esc($order['order_number']) ?></strong>
                                                    <br><small class="text-muted"><?= date('d/m/Y', strtotime($order['created_at'])) ?></small>
                                                </td>
                                                <td>
                                                    <?= esc($order['guest_email'] ?? '-') ?>
                                                    <br><small class="text-muted"><?= esc($order['guest_phone'] ?? '') ?></small>
                                                </td>
                                                <td>
                                                    <small><?= esc(implode(', ', array_slice($services, 0, 2))) ?><?= count($services) > 2 ? '...' : '' ?></small>
                                                </td>
                                                <td><strong><?= format_rupiah($order['total_amount'] ?? 0) ?></strong></td>
                                                <td>
                                                    <span class="badge bg-<?= $statusInfo['badge'] ?> status-badge">
                                                        <?= $statusInfo['label'] ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?= $paymentStatus === 'paid' ? 'success' : ($paymentStatus === 'failed' ? 'danger' : 'warning') ?> status-badge">
                                                        <?= ucfirst($paymentStatus) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="<?= base_url('admin/orders/' . $order['order_number']) ?>" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center py-4 text-muted">
                                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                                Belum ada pesanan
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Sidebar -->
            <div class="col-lg-4">
                <!-- Status Distribution -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-pie-chart"></i> Distribusi Status</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container" style="height: 250px;">
                            <canvas id="statusChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-lightning"></i> Menu Cepat</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="<?= base_url('admin/orders') ?>" class="btn btn-outline-primary">
                                <i class="bi bi-box-seam me-2"></i> Kelola Pesanan
                            </a>
                            <a href="<?= base_url('admin/users') ?>" class="btn btn-outline-success">
                                <i class="bi bi-people me-2"></i> Kelola User
                            </a>
                            <a href="<?= base_url('admin/services') ?>" class="btn btn-outline-info">
                                <i class="bi bi-tags me-2"></i> Kelola Layanan
                            </a>
                            <a href="<?= base_url('admin/branches') ?>" class="btn btn-outline-warning">
                                <i class="bi bi-shop me-2"></i> Kelola Cabang
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Payment Status Summary -->
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-credit-card"></i> Status Pembayaran</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span><i class="bi bi-check-circle text-success me-2"></i> Lunas</span>
                            <strong><?= $stats['paid'] ?? 0 ?></strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span><i class="bi bi-clock text-warning me-2"></i> Pending</span>
                            <strong><?= $stats['payment_pending'] ?? 0 ?></strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-x-circle text-danger me-2"></i> Gagal</span>
                            <strong><?= $stats['payment_failed'] ?? 0 ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Chart Data from PHP
const chartData = <?= json_encode($chart_data ?? ['daily' => [], 'monthly' => [], 'status' => []]) ?>;

// Revenue Chart
let revenueChart;
const revenueCtx = document.getElementById('revenueChart').getContext('2d');

function initRevenueChart(data, labels, label) {
    if (revenueChart) revenueChart.destroy();
    
    revenueChart = new Chart(revenueCtx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: label,
                data: data,
                backgroundColor: 'rgba(13, 110, 253, 0.7)',
                borderColor: 'rgba(13, 110, 253, 1)',
                borderWidth: 1,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                }
            }
        }
    });
}

function showDailyChart() {
    const daily = chartData.daily || [];
    const labels = daily.map(d => d.date || d.label);
    const data = daily.map(d => d.revenue || d.value || 0);
    initRevenueChart(data, labels, 'Pendapatan Harian');
    
    document.querySelectorAll('.btn-group .btn').forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
}

function showMonthlyChart() {
    const monthly = chartData.monthly || [];
    const labels = monthly.map(d => d.month || d.label);
    const data = monthly.map(d => d.revenue || d.value || 0);
    initRevenueChart(data, labels, 'Pendapatan Bulanan');
    
    document.querySelectorAll('.btn-group .btn').forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
}

// Status Distribution Chart
const statusCtx = document.getElementById('statusChart').getContext('2d');
const statusData = chartData.status || {};

new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: ['Waiting Pickup', 'In Process', 'Completed', 'Cancelled'],
        datasets: [{
            data: [
                statusData.waiting_pickup || <?= $stats['waiting_pickup'] ?? 0 ?>,
                statusData.in_process || <?= $stats['in_process'] ?? 0 ?>,
                statusData.completed || <?= $stats['completed'] ?? 0 ?>,
                statusData.cancelled || <?= $stats['cancelled'] ?? 0 ?>
            ],
            backgroundColor: [
                '#ffc107',
                '#0d6efd',
                '#198754',
                '#dc3545'
            ],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '60%',
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    usePointStyle: true,
                    padding: 15
                }
            }
        }
    }
});

// Initialize with daily chart
document.addEventListener('DOMContentLoaded', function() {
    showDailyChart();
});
</script>
<?= $this->endSection() ?>
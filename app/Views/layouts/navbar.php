<?php
/**
 * Main Navbar Component
 * Include in layouts/main.php
 * 
 * Usage: <?= $this->include('layouts/navbar') ?>
 */

$currentUrl = current_url();
$user = session()->get('user');
$isLoggedIn = is_logged_in();
$isAdmin = is_admin();
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top shadow-sm">
    <div class="container">
        <!-- Brand -->
        <a class="navbar-brand fw-bold" href="<?= base_url() ?>">
            <i class="bi bi-stars"></i> Tapak Bersih
        </a>
        
        <!-- Mobile Toggle -->
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <!-- Nav Items -->
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?= $currentUrl == base_url() ? 'active' : '' ?>" href="<?= base_url() ?>">
                        <i class="bi bi-house-door"></i> Beranda
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= strpos($currentUrl, '/services') !== false || strpos($currentUrl, '/layanan') !== false ? 'active' : '' ?>" 
                       href="<?= base_url('services') ?>">
                        <i class="bi bi-grid"></i> Layanan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= strpos($currentUrl, '/gallery') !== false ? 'active' : '' ?>" 
                       href="<?= base_url('gallery') ?>">
                        <i class="bi bi-images"></i> Gallery
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= strpos($currentUrl, '/branches') !== false || strpos($currentUrl, '/cabang') !== false ? 'active' : '' ?>" 
                       href="<?= base_url('branches') ?>">
                        <i class="bi bi-geo-alt"></i> Cabang
                    </a>
                </li>
            </ul>
            
            <!-- Right Side -->
            <ul class="navbar-nav">
                <?php if ($isLoggedIn): ?>
                    <!-- Order Button -->
                    <li class="nav-item me-2">
                        <a class="nav-link <?= strpos($currentUrl, '/order') !== false ? 'active' : '' ?>" 
                           href="<?= base_url('order/checkout') ?>">
                            <i class="bi bi-bag-plus"></i> Pesanan
                        </a>
                    </li>
                    
                    <!-- User Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" 
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle"></i> <?= esc($user['name'] ?? 'User') ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown">
                            <?php if ($isAdmin): ?>
                                <!-- Admin Menu -->
                                <li>
                                    <a class="dropdown-item" href="<?= base_url('admin/dashboard') ?>">
                                        <i class="bi bi-speedometer2 text-primary"></i> Admin Dashboard
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                            <?php endif; ?>
                            
                            <!-- User Menu -->
                            <li>
                                <a class="dropdown-item" href="<?= base_url('user/dashboard') ?>">
                                    <i class="bi bi-house-door text-primary"></i> Dashboard
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= base_url('user/orders') ?>">
                                    <i class="bi bi-box-seam text-info"></i> Pesanan Saya
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= base_url('user/profile') ?>">
                                    <i class="bi bi-person text-success"></i> Profile
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="<?= base_url('auth/logout') ?>">
                                    <i class="bi bi-box-arrow-right"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php else: ?>
                    <!-- Guest Menu -->
                    <li class="nav-item me-2">
                        <a class="nav-link" href="<?= base_url('guest/track') ?>">
                            <i class="bi bi-search"></i> Lacak Pesanan
                        </a>
                    </li>
                    <li class="nav-item me-2">
                        <a class="btn btn-outline-light btn-sm" href="<?= base_url('auth/login') ?>">
                            <i class="bi bi-box-arrow-in-right"></i> Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-light btn-sm" href="<?= base_url('auth/register') ?>">
                            <i class="bi bi-person-plus"></i> Daftar
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
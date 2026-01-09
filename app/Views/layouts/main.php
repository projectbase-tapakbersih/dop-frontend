<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Tapak Bersih - Layanan perawatan sepatu profesional dengan gratis jemput & antar">
    <meta name="keywords" content="cuci sepatu, perawatan sepatu, shoe care, cleaning service">
    <title><?= $title ?? 'Tapak Bersih - Layanan Perawatan Sepatu Profesional' ?></title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --accent-color: #f093fb;
            --dark-color: #2d3748;
            --light-gray: #f8f9fa;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-top: 76px; /* Height of fixed navbar */
        }
        
        /* Navbar Styles */
        .navbar {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 12px 0;
            transition: all 0.3s ease;
        }
        
        .navbar.scrolled {
            padding: 8px 0;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }
        
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
            color: white !important;
            transition: transform 0.3s ease;
        }
        
        .navbar-brand:hover {
            transform: scale(1.05);
        }
        
        .navbar-nav .nav-link {
            color: rgba(255,255,255,0.9) !important;
            font-weight: 500;
            padding: 8px 16px !important;
            margin: 0 4px;
            border-radius: 8px;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .navbar-nav .nav-link:hover {
            background: rgba(255,255,255,0.15);
            color: white !important;
            transform: translateY(-2px);
        }
        
        .navbar-nav .nav-link.active {
            background: rgba(255,255,255,0.2);
            color: white !important;
        }
        
        .navbar-nav .nav-link i {
            margin-right: 6px;
        }
        
        .btn-navbar {
            background: white;
            color: var(--primary-color) !important;
            font-weight: 600;
            padding: 8px 24px;
            border-radius: 25px;
            border: 2px solid white;
            transition: all 0.3s ease;
        }
        
        .btn-navbar:hover {
            background: transparent;
            color: white !important;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255,255,255,0.3);
        }
        
        .dropdown-menu {
            border: none;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            padding: 8px;
            margin-top: 8px;
        }
        
        .dropdown-item {
            border-radius: 8px;
            padding: 10px 16px;
            transition: all 0.2s ease;
        }
        
        .dropdown-item:hover {
            background: var(--light-gray);
            transform: translateX(5px);
        }
        
        .dropdown-item i {
            width: 20px;
            margin-right: 8px;
        }
        
        /* Mobile Menu */
        @media (max-width: 991px) {
            .navbar-collapse {
                background: rgba(102, 126, 234, 0.98);
                padding: 20px;
                border-radius: 15px;
                margin-top: 15px;
            }
            
            .navbar-nav {
                gap: 8px;
            }
            
            .btn-navbar {
                width: 100%;
                margin-top: 12px;
            }
        }
        
        /* Buttons */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border: none;
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .btn-outline-primary {
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-outline-primary:hover {
            background: var(--primary-color);
            border-color: var(--primary-color);
            transform: translateY(-2px);
        }
        
        /* Cards */
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        
        /* Footer */
        .footer {
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--primary-color) 100%);
            color: white;
            padding: 60px 0 20px;
            margin-top: 80px;
        }
        
        .footer a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .footer a:hover {
            color: white;
            transform: translateX(5px);
            display: inline-block;
        }
        
        .footer-social a {
            width: 40px;
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            margin-right: 10px;
            transition: all 0.3s ease;
        }
        
        .footer-social a:hover {
            background: white;
            color: var(--primary-color) !important;
            transform: translateY(-5px);
        }
        
        /* Section Titles */
        .section-title {
            font-weight: 700;
            margin-bottom: 40px;
            position: relative;
            padding-bottom: 15px;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border-radius: 2px;
        }
        
        /* Utilities */
        .price-tag {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
        }
        
        /* Scroll to top button */
        #scrollTop {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            border: none;
            display: none;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 1000;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
        }
        
        #scrollTop:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        
        #scrollTop.show {
            display: flex;
        }
    </style>
    
    <?= $this->renderSection('styles') ?>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top" id="mainNavbar">
        <div class="container">
            <!-- Brand -->
            <a class="navbar-brand" href="<?= base_url('/') ?>">
                <i class="bi bi-stars"></i> Tapak Bersih
            </a>
            
            <!-- Mobile Toggle -->
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <!-- Navigation Menu -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link <?= uri_string() == '' ? 'active' : '' ?>" href="<?= base_url('/') ?>">
                            <i class="bi bi-house-door"></i> Beranda
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos(uri_string(), 'services') !== false ? 'active' : '' ?>" href="<?= base_url('services') ?>">
                            <i class="bi bi-grid"></i> Layanan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos(uri_string(), 'gallery') !== false ? 'active' : '' ?>" href="<?= base_url('gallery') ?>">
                            <i class="bi bi-images"></i> Gallery
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos(uri_string(), 'branches') !== false ? 'active' : '' ?>" href="<?= base_url('branches') ?>">
                            <i class="bi bi-geo-alt"></i> Cabang
                        </a>
                    </li>
                </ul>
                
                <!-- Auth Section -->
                <ul class="navbar-nav">
                    <?php if (is_logged_in()): ?>
                        <?php $user = get_user_data(); ?>
                        
                        <!-- Orders Link -->
                        <li class="nav-item">
                            <a class="nav-link <?= strpos(uri_string(), 'order') !== false ? 'active' : '' ?>" href="<?= base_url('order/checkout') ?>">
                                <i class="bi bi-bag-plus"></i> Pesanan
                            </a>
                        </li>
                        
                        <!-- User Dropdown -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i> <?= esc($user['name'] ?? 'User') ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <?php if (is_admin()): ?>
                                    <li>
                                        <a class="dropdown-item" href="<?= base_url('admin/dashboard') ?>">
                                            <i class="bi bi-speedometer2 text-danger"></i> Admin Dashboard
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                <?php endif; ?>
                                
                                <!-- User Menu - Always show for all users -->
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
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('auth/login') ?>">
                                <i class="bi bi-box-arrow-in-right"></i> Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-navbar" href="<?= base_url('order/checkout') ?>">
                                <i class="bi bi-bag-plus"></i> Pesan Sekarang
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="container mt-3">
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle"></i>
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="container mt-3">
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="bi bi-exclamation-triangle"></i>
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('info')): ?>
        <div class="container mt-3">
            <div class="alert alert-info alert-dismissible fade show">
                <i class="bi bi-info-circle"></i>
                <?= session()->getFlashdata('info') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    <?php endif; ?>

    <!-- Main Content -->
    <main>
        <?= $this->renderSection('content') ?>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row g-4">
                <!-- About -->
                <div class="col-lg-4 mb-4">
                    <h5 class="fw-bold mb-3">
                        <i class="bi bi-stars"></i> Tapak Bersih
                    </h5>
                    <p class="mb-3">
                        Layanan perawatan sepatu profesional dengan hasil terbaik. 
                        Gratis jemput & antar, tracking real-time, dan pembayaran digital.
                    </p>
                    <div class="footer-social">
                        <a href="#" target="_blank" title="Facebook">
                            <i class="bi bi-facebook"></i>
                        </a>
                        <a href="#" target="_blank" title="Instagram">
                            <i class="bi bi-instagram"></i>
                        </a>
                        <a href="#" target="_blank" title="Twitter">
                            <i class="bi bi-twitter"></i>
                        </a>
                        <a href="https://wa.me/6281234567890" target="_blank" title="WhatsApp">
                            <i class="bi bi-whatsapp"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div class="col-lg-2 col-md-4 mb-4">
                    <h6 class="fw-bold mb-3">Navigasi</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <a href="<?= base_url('/') ?>">
                                <i class="bi bi-chevron-right"></i> Beranda
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="<?= base_url('services') ?>">
                                <i class="bi bi-chevron-right"></i> Layanan
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="<?= base_url('gallery') ?>">
                                <i class="bi bi-chevron-right"></i> Gallery
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="<?= base_url('branches') ?>">
                                <i class="bi bi-chevron-right"></i> Cabang
                            </a>
                        </li>
                    </ul>
                </div>
                
                <!-- Services -->
                <div class="col-lg-3 col-md-4 mb-4">
                    <h6 class="fw-bold mb-3">Layanan</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <a href="<?= base_url('services') ?>">
                                <i class="bi bi-chevron-right"></i> Basic Cleaning
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="<?= base_url('services') ?>">
                                <i class="bi bi-chevron-right"></i> Deep Cleaning
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="<?= base_url('services') ?>">
                                <i class="bi bi-chevron-right"></i> Premium Care
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="<?= base_url('order/checkout') ?>">
                                <i class="bi bi-chevron-right"></i> Cara Pemesanan
                            </a>
                        </li>
                    </ul>
                </div>
                
                <!-- Contact -->
                <div class="col-lg-3 col-md-4 mb-4">
                    <h6 class="fw-bold mb-3">Kontak</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="bi bi-telephone-fill me-2"></i>
                            <a href="tel:+6281234567890">+62 812-3456-7890</a>
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-envelope-fill me-2"></i>
                            <a href="mailto:info@tapakbersih.com">info@tapakbersih.com</a>
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-clock-fill me-2"></i>
                            Senin - Minggu: 08:00 - 20:00
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-geo-alt-fill me-2"></i>
                            Surabaya, Indonesia
                        </li>
                    </ul>
                </div>
            </div>
            
            <hr class="bg-white my-4">
            
            <!-- Copyright -->
            <div class="row">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    <p class="mb-0">
                        &copy; <?= date('Y') ?> <strong>Tapak Bersih</strong>. All rights reserved.
                    </p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <a href="#" class="me-3">Privacy Policy</a>
                    <a href="#" class="me-3">Terms of Service</a>
                    <a href="#">Sitemap</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scroll to Top Button -->
    <button id="scrollTop" title="Scroll to top">
        <i class="bi bi-arrow-up fs-5"></i>
    </button>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Main JS -->
    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('mainNavbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
        
        // Scroll to top button
        const scrollTop = document.getElementById('scrollTop');
        
        window.addEventListener('scroll', function() {
            if (window.scrollY > 300) {
                scrollTop.classList.add('show');
            } else {
                scrollTop.classList.remove('show');
            }
        });
        
        scrollTop.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
        
        // Auto-hide flash messages
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
        
        // Close mobile menu on link click
        document.querySelectorAll('.navbar-nav .nav-link').forEach(link => {
            link.addEventListener('click', function() {
                const navbarCollapse = document.getElementById('navbarNav');
                const bsCollapse = bootstrap.Collapse.getInstance(navbarCollapse);
                if (bsCollapse) {
                    bsCollapse.hide();
                }
            });
        });
    </script>
    
    <?= $this->renderSection('scripts') ?>
</body>
</html>
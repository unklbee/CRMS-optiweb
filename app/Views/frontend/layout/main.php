<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Computer Repair Shop' ?></title>
    <meta name="description" content="<?= $meta_description ?? 'Professional computer repair services' ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3B82F6',
                        secondary: '#64748B',
                        accent: '#10B981',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 font-sans">
<!-- Header -->
<header class="bg-white shadow-lg sticky top-0 z-50">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between py-4">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="/" class="flex items-center space-x-2">
                    <i class="fas fa-tools text-2xl text-primary"></i>
                    <span class="text-2xl font-bold text-gray-800"><?= get_site_setting('site_name', 'Repair Shop') ?></span>
                </a>
            </div>

            <!-- Navigation -->
            <nav class="hidden md:flex items-center space-x-8">
                <a href="/" class="text-gray-700 hover:text-primary transition-colors">Home</a>
                <a href="/services" class="text-gray-700 hover:text-primary transition-colors">Services</a>
                <a href="/track-order" class="text-gray-700 hover:text-primary transition-colors">Track Order</a>
                <a href="/contact" class="text-gray-700 hover:text-primary transition-colors">Contact</a>
                <a href="/book-service" class="bg-primary text-white px-6 py-2 rounded-full hover:bg-blue-700 transition-colors">
                    Book Service
                </a>
            </nav>

            <!-- Mobile menu button -->
            <button id="mobile-menu-btn" class="md:hidden text-gray-700 hover:text-primary">
                <i class="fas fa-bars text-xl"></i>
            </button>
        </div>

        <!-- Mobile Navigation -->
        <div id="mobile-menu" class="hidden md:hidden pb-4">
            <div class="flex flex-col space-y-2">
                <a href="/" class="text-gray-700 hover:text-primary py-2 transition-colors">Home</a>
                <a href="/services" class="text-gray-700 hover:text-primary py-2 transition-colors">Services</a>
                <a href="/track-order" class="text-gray-700 hover:text-primary py-2 transition-colors">Track Order</a>
                <a href="/contact" class="text-gray-700 hover:text-primary py-2 transition-colors">Contact</a>
                <a href="/book-service" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-center mt-2">
                    Book Service
                </a>
            </div>
        </div>
    </div>
</header>

<!-- Flash Messages -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 mx-4 mt-4 rounded-lg">
        <div class="flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            <span><?= session()->getFlashdata('success') ?></span>
        </div>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mx-4 mt-4 rounded-lg">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <span><?= session()->getFlashdata('error') ?></span>
        </div>
    </div>
<?php endif; ?>

<!-- Main Content -->
<main>
    <?= $this->renderSection('content') ?>
</main>

<!-- Footer -->
<footer class="bg-gray-800 text-white">
    <div class="container mx-auto px-4 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- Company Info -->
            <div class="space-y-4">
                <h3 class="text-xl font-bold"><?= get_site_setting('site_name', 'Repair Shop') ?></h3>
                <p class="text-gray-400"><?= get_site_setting('site_description', 'Professional computer repair services') ?></p>
                <div class="flex space-x-4">
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">
                        <i class="fab fa-facebook text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">
                        <i class="fab fa-twitter text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">
                        <i class="fab fa-instagram text-xl"></i>
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="space-y-4">
                <h4 class="text-lg font-semibold">Quick Links</h4>
                <ul class="space-y-2">
                    <li><a href="/" class="text-gray-400 hover:text-white transition-colors">Home</a></li>
                    <li><a href="/services" class="text-gray-400 hover:text-white transition-colors">Services</a></li>
                    <li><a href="/track-order" class="text-gray-400 hover:text-white transition-colors">Track Order</a></li>
                    <li><a href="/contact" class="text-gray-400 hover:text-white transition-colors">Contact</a></li>
                </ul>
            </div>

            <!-- Services -->
            <div class="space-y-4">
                <h4 class="text-lg font-semibold">Our Services</h4>
                <ul class="space-y-2">
                    <li><a href="/services" class="text-gray-400 hover:text-white transition-colors">Hardware Repair</a></li>
                    <li><a href="/services" class="text-gray-400 hover:text-white transition-colors">Software Installation</a></li>
                    <li><a href="/services" class="text-gray-400 hover:text-white transition-colors">Data Recovery</a></li>
                    <li><a href="/services" class="text-gray-400 hover:text-white transition-colors">Virus Removal</a></li>
                </ul>
            </div>

            <!-- Contact Info -->
            <div class="space-y-4">
                <h4 class="text-lg font-semibold">Contact Info</h4>
                <div class="space-y-2">
                    <p class="text-gray-400 flex items-center">
                        <i class="fas fa-map-marker-alt mr-2"></i>
                        <?= get_site_setting('address', 'Jl. Teknologi No. 123, Jakarta') ?>
                    </p>
                    <p class="text-gray-400 flex items-center">
                        <i class="fas fa-phone mr-2"></i>
                        <?= get_site_setting('contact_phone', '+62-21-1234567') ?>
                    </p>
                    <p class="text-gray-400 flex items-center">
                        <i class="fas fa-envelope mr-2"></i>
                        <?= get_site_setting('contact_email', 'info@repairshop.com') ?>
                    </p>
                </div>
            </div>
        </div>

        <div class="border-t border-gray-700 mt-8 pt-8 text-center">
            <p class="text-gray-400">
                &copy; <?= date('Y') ?> <?= get_site_setting('site_name', 'Repair Shop') ?>. All rights reserved.
            </p>
        </div>
    </div>
</footer>

<script>
    // Mobile menu toggle
    document.getElementById('mobile-menu-btn').addEventListener('click', function() {
        const menu = document.getElementById('mobile-menu');
        menu.classList.toggle('hidden');
    });

    // Auto hide flash messages
    setTimeout(function() {
        const alerts = document.querySelectorAll('[class*="bg-green-100"], [class*="bg-red-100"]');
        alerts.forEach(alert => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        });
    }, 5000);
</script>
</body>
</html>
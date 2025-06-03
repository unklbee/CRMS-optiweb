<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Admin Panel' ?> - Computer Repair Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3B82F6',
                        secondary: '#64748B',
                        success: '#10B981',
                        warning: '#F59E0B',
                        danger: '#EF4444',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 font-sans">
<div class="flex min-h-screen">
    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-lg">
        <div class="p-6 border-b border-gray-200">
            <h1 class="text-xl font-bold text-gray-800">
                <i class="fas fa-tools text-primary mr-2"></i>
                Repair Shop
            </h1>
        </div>

        <nav class="mt-6">
            <div class="px-4">
                <a href="/admin/dashboard" class="flex items-center px-4 py-3 text-gray-700 hover:bg-primary hover:text-white rounded-lg transition-colors mb-1 <?= (current_url() == base_url('admin/dashboard')) ? 'bg-primary text-white' : '' ?>">
                    <i class="fas fa-tachometer-alt mr-3"></i>
                    Dashboard
                </a>

                <a href="/admin/orders" class="flex items-center px-4 py-3 text-gray-700 hover:bg-primary hover:text-white rounded-lg transition-colors mb-1 <?= (strpos(current_url(), 'admin/orders') !== false) ? 'bg-primary text-white' : '' ?>">
                    <i class="fas fa-clipboard-list mr-3"></i>
                    Repair Orders
                </a>

                <a href="/admin/customers" class="flex items-center px-4 py-3 text-gray-700 hover:bg-primary hover:text-white rounded-lg transition-colors mb-1 <?= (strpos(current_url(), 'admin/customers') !== false) ? 'bg-primary text-white' : '' ?>">
                    <i class="fas fa-users mr-3"></i>
                    Customers
                </a>

                <a href="/admin/services" class="flex items-center px-4 py-3 text-gray-700 hover:bg-primary hover:text-white rounded-lg transition-colors mb-1 <?= (strpos(current_url(), 'admin/services') !== false) ? 'bg-primary text-white' : '' ?>">
                    <i class="fas fa-cog mr-3"></i>
                    Services
                </a>

                <a href="/admin/parts" class="flex items-center px-4 py-3 text-gray-700 hover:bg-primary hover:text-white rounded-lg transition-colors mb-1 <?= (strpos(current_url(), 'admin/parts') !== false) ? 'bg-primary text-white' : '' ?>">
                    <i class="fas fa-boxes mr-3"></i>
                    Parts & Inventory
                </a>

                <a href="/admin/users" class="flex items-center px-4 py-3 text-gray-700 hover:bg-primary hover:text-white rounded-lg transition-colors mb-1 <?= (strpos(current_url(), 'admin/users') !== false) ? 'bg-primary text-white' : '' ?>">
                    <i class="fas fa-user-cog mr-3"></i>
                    Users
                </a>

                <a href="/admin/pages" class="flex items-center px-4 py-3 text-gray-700 hover:bg-primary hover:text-white rounded-lg transition-colors mb-1 <?= (strpos(current_url(), 'admin/pages') !== false) ? 'bg-primary text-white' : '' ?>">
                    <i class="fas fa-file-alt mr-3"></i>
                    CMS Pages
                </a>

                <a href="/admin/settings" class="flex items-center px-4 py-3 text-gray-700 hover:bg-primary hover:text-white rounded-lg transition-colors mb-1 <?= (strpos(current_url(), 'admin/settings') !== false) ? 'bg-primary text-white' : '' ?>">
                    <i class="fas fa-sliders-h mr-3"></i>
                    Settings
                </a>
            </div>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="flex items-center justify-between px-6 py-4">
                <h2 class="text-2xl font-semibold text-gray-800"><?= $title ?? 'Dashboard' ?></h2>

                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <button class="flex items-center text-gray-700 hover:text-primary transition-colors">
                            <i class="fas fa-user-circle text-2xl mr-2"></i>
                            <span><?= session()->get('full_name') ?></span>
                            <i class="fas fa-chevron-down ml-2"></i>
                        </button>
                    </div>

                    <a href="/admin/logout" class="bg-danger text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-colors">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        Logout
                    </a>
                </div>
            </div>
        </header>

        <!-- Flash Messages -->
        <?php if (session()->getFlashData('success')): ?>
            <div class="mx-6 mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative">
                <span class="block sm:inline"><?= session()->getFlashData('success') ?></span>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashData('error')): ?>
            <div class="mx-6 mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative">
                <span class="block sm:inline"><?= session()->getFlashData('error') ?></span>
            </div>
        <?php endif; ?>

        <!-- Page Content -->
        <main class="flex-1 p-6">
            <?= $this->renderSection('content') ?>
        </main>
    </div>
</div>

<script>
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
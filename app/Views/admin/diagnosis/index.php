<?php $this->extend('admin/layout/main'); ?>

<?php $this->section('content'); ?>

    <div class="container mx-auto px-4 py-6">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Diagnosis Queue</h1>
                    <p class="text-gray-600 mt-1">Manage device diagnosis and technical assessments</p>
                </div>
                <div class="flex space-x-3">
                    <a href="/admin/diagnosis/templates"
                       class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                        <i class="fas fa-file-alt mr-2"></i>Templates
                    </a>
                    <a href="/admin/orders"
                       class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-clipboard-list mr-2"></i>All Orders
                    </a>
                </div>
            </div>
        </div>

        <!-- Alert Messages -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-100 text-red-600">
                        <i class="fas fa-clock text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-800"><?= $stats['pending'] ?? 0 ?></h3>
                        <p class="text-gray-600 text-sm">Pending Diagnosis</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <i class="fas fa-tools text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-800"><?= $stats['in_progress'] ?? 0 ?></h3>
                        <p class="text-gray-600 text-sm">In Progress</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <i class="fas fa-check-circle text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-800"><?= $stats['completed'] ?? 0 ?></h3>
                        <p class="text-gray-600 text-sm">Completed</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <i class="fas fa-chart-line text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-800"><?= $stats['today'] ?? 0 ?></h3>
                        <p class="text-gray-600 text-sm">Today</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Status</option>
                        <option value="pending" <?= (request()->getGet('status') === 'pending') ? 'selected' : '' ?>>Pending</option>
                        <option value="in_progress" <?= (request()->getGet('status') === 'in_progress') ? 'selected' : '' ?>>In Progress</option>
                        <option value="completed" <?= (request()->getGet('status') === 'completed') ? 'selected' : '' ?>>Completed</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Device Type</label>
                    <select name="device_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Types</option>
                        <?php if (isset($device_types)): ?>
                            <?php foreach ($device_types as $type): ?>
                                <option value="<?= $type['id'] ?>" <?= (request()->getGet('device_type') == $type['id']) ? 'selected' : '' ?>>
                                    <?= $type['name'] ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Technician</label>
                    <select name="technician" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Technicians</option>
                        <?php if (isset($technicians)): ?>
                            <?php foreach ($technicians as $tech): ?>
                                <option value="<?= $tech['id'] ?>" <?= (request()->getGet('technician') == $tech['id']) ? 'selected' : '' ?>>
                                    <?= $tech['full_name'] ?? $tech['name'] ?? $tech['username'] ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" name="search" value="<?= request()->getGet('search') ?>"
                           placeholder="Order number, customer name..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div class="flex space-x-2">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-search mr-2"></i>Filter
                    </button>
                    <a href="/admin/diagnosis" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                        <i class="fas fa-refresh mr-2"></i>Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Orders Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Diagnosis Queue</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Device</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Technician</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (!empty($orders)): ?>
                        <?php foreach ($orders as $order): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">#<?= $order['order_number'] ?></div>
                                    <div class="text-sm text-gray-500"><?= date('M d, Y', strtotime($order['created_at'])) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?= $order['customer_name'] ?></div>
                                    <div class="text-sm text-gray-500"><?= $order['customer_phone'] ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?= $order['device_brand'] ?> <?= $order['device_model'] ?></div>
                                    <div class="text-sm text-gray-500"><?= $order['device_type_name'] ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php
                                    $statusClass = '';
                                    $statusText = ucfirst(str_replace('_', ' ', $order['diagnosis_status'] ?: 'pending'));

                                    switch ($order['diagnosis_status']) {
                                        case 'pending':
                                            $statusClass = 'bg-red-100 text-red-800';
                                            break;
                                        case 'in_progress':
                                            $statusClass = 'bg-yellow-100 text-yellow-800';
                                            break;
                                        case 'completed':
                                            $statusClass = 'bg-green-100 text-green-800';
                                            break;
                                        default:
                                            $statusClass = 'bg-gray-100 text-gray-800';
                                    }
                                    ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $statusClass ?>">
                                        <?= $statusText ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($order['diagnosed_by_name']): ?>
                                        <div class="text-sm font-medium text-gray-900"><?= $order['diagnosed_by_name'] ?></div>
                                        <?php if ($order['diagnosis_date']): ?>
                                            <div class="text-sm text-gray-500"><?= date('M d, H:i', strtotime($order['diagnosis_date'])) ?></div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-sm text-gray-400">Not assigned</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= date('M d, Y', strtotime($order['created_at'])) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <?php if ($order['diagnosis_status'] === 'pending'): ?>
                                            <a href="/admin/diagnosis/<?= $order['id'] ?>/start"
                                               class="bg-blue-600 text-white px-3 py-1 rounded text-xs hover:bg-blue-700 transition-colors">
                                                <i class="fas fa-play mr-1"></i>Start
                                            </a>
                                        <?php elseif ($order['diagnosis_status'] === 'in_progress'): ?>
                                            <a href="/admin/diagnosis/<?= $order['id'] ?>/create"
                                               class="bg-yellow-600 text-white px-3 py-1 rounded text-xs hover:bg-yellow-700 transition-colors">
                                                <i class="fas fa-edit mr-1"></i>Continue
                                            </a>
                                        <?php elseif ($order['diagnosis_status'] === 'completed'): ?>
                                            <a href="/admin/diagnosis/<?= $order['id'] ?>"
                                               class="bg-green-600 text-white px-3 py-1 rounded text-xs hover:bg-green-700 transition-colors">
                                                <i class="fas fa-eye mr-1"></i>View
                                            </a>
                                            <a href="/admin/diagnosis/<?= $order['id'] ?>/edit"
                                               class="bg-gray-600 text-white px-3 py-1 rounded text-xs hover:bg-gray-700 transition-colors">
                                                <i class="fas fa-edit mr-1"></i>Edit
                                            </a>
                                        <?php endif; ?>

                                        <a href="/admin/orders/<?= $order['id'] ?>"
                                           class="bg-blue-600 text-white px-3 py-1 rounded text-xs hover:bg-blue-700 transition-colors">
                                            <i class="fas fa-file-alt mr-1"></i>Order
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="text-gray-400">
                                    <i class="fas fa-inbox text-4xl mb-4"></i>
                                    <p class="text-lg font-medium">No orders found</p>
                                    <p class="text-sm">No orders match your current filters.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if (isset($pager)): ?>
                <div class="px-6 py-4 border-t border-gray-200">
                    <?= $pager->links() ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Quick Actions -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">

            <!-- Priority Queue -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-exclamation-triangle text-red-600 mr-2"></i>Priority Queue
                </h3>
                <?php if (!empty($priority_orders)): ?>
                    <div class="space-y-3">
                        <?php foreach (array_slice($priority_orders, 0, 5) as $order): ?>
                            <div class="flex justify-between items-center p-3 bg-red-50 rounded-lg">
                                <div>
                                    <div class="font-medium text-sm">#<?= $order['order_number'] ?></div>
                                    <div class="text-xs text-gray-600"><?= $order['customer_name'] ?></div>
                                </div>
                                <a href="/admin/diagnosis/<?= $order['id'] ?>/start"
                                   class="bg-red-600 text-white px-2 py-1 rounded text-xs hover:bg-red-700">
                                    Start
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-gray-600 text-sm">No priority orders</p>
                <?php endif; ?>
            </div>

            <!-- Today's Completed -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-check-circle text-green-600 mr-2"></i>Today's Completed
                </h3>
                <?php if (!empty($today_completed)): ?>
                    <div class="space-y-3">
                        <?php foreach (array_slice($today_completed, 0, 5) as $order): ?>
                            <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                                <div>
                                    <div class="font-medium text-sm">#<?= $order['order_number'] ?></div>
                                    <div class="text-xs text-gray-600"><?= $order['diagnosed_by_name'] ?></div>
                                </div>
                                <a href="/admin/diagnosis/<?= $order['id'] ?>"
                                   class="bg-green-600 text-white px-2 py-1 rounded text-xs hover:bg-green-700">
                                    View
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-gray-600 text-sm">No completed diagnosis today</p>
                <?php endif; ?>
            </div>

            <!-- Templates -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-file-alt text-blue-600 mr-2"></i>Quick Templates
                </h3>
                <?php if (!empty($popular_templates)): ?>
                    <div class="space-y-3">
                        <?php foreach (array_slice($popular_templates, 0, 5) as $template): ?>
                            <div class="p-3 bg-blue-50 rounded-lg">
                                <div class="font-medium text-sm"><?= $template['title'] ?></div>
                                <div class="text-xs text-gray-600"><?= $template['device_type_name'] ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="mt-4">
                        <a href="/admin/diagnosis/templates"
                           class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            Manage Templates →
                        </a>
                    </div>
                <?php else: ?>
                    <p class="text-gray-600 text-sm mb-4">No templates available</p>
                    <a href="/admin/diagnosis/templates"
                       class="bg-blue-600 text-white px-3 py-2 rounded text-sm hover:bg-blue-700">
                        Create Template
                    </a>
                <?php endif; ?>
            </div>

        </div>

    </div>

    <script>
        // Auto-refresh every 30 seconds for real-time updates
        let autoRefresh = true;

        function toggleAutoRefresh() {
            autoRefresh = !autoRefresh;
            const button = document.getElementById('autoRefreshBtn');
            if (autoRefresh) {
                button.innerHTML = '<i class="fas fa-pause mr-2"></i>Pause Refresh';
                button.className = 'bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors';
            } else {
                button.innerHTML = '<i class="fas fa-play mr-2"></i>Resume Refresh';
                button.className = 'bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors';
            }
        }

        // Auto refresh functionality
        setInterval(() => {
            if (autoRefresh && !document.hidden) {
                // Only refresh if no forms are being filled out
                const hasActiveInput = document.querySelector('input:focus, select:focus, textarea:focus');
                if (!hasActiveInput) {
                    location.reload();
                }
            }
        }, 30000);

        // Show notification when page becomes visible
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden && autoRefresh) {
                // Page became visible, check for updates
                setTimeout(() => {
                    location.reload();
                }, 1000);
            }
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            // Ctrl + R or F5 to refresh
            if ((e.ctrlKey && e.key === 'r') || e.key === 'F5') {
                e.preventDefault();
                location.reload();
            }

            // Ctrl + F to focus search
            if (e.ctrlKey && e.key === 'f') {
                e.preventDefault();
                const searchInput = document.querySelector('input[name="search"]');
                if (searchInput) {
                    searchInput.focus();
                    searchInput.select();
                }
            }
        });

        // Add loading states to action buttons
        document.querySelectorAll('a[href*="/start"], a[href*="/create"]').forEach(button => {
            button.addEventListener('click', function() {
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Loading...';
                this.style.pointerEvents = 'none';

                // Restore after 5 seconds in case of navigation failure
                setTimeout(() => {
                    this.innerHTML = originalText;
                    this.style.pointerEvents = 'auto';
                }, 5000);
            });
        });
    </script>

<?php $this->endSection(); ?>
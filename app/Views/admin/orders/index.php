<?= $this->extend('admin/layout/main') ?>

<?= $this->section('content') ?>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Repair Orders</h1>
                <p class="text-gray-600">Manage all repair orders and track their progress</p>
            </div>
            <a href="/admin/orders/new" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-plus mr-2"></i>New Order
            </a>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <?php
            $totalOrders = count($orders ?? []);
            $pendingCount = count(array_filter($orders ?? [], function($o) { return in_array($o['status'], ['received', 'diagnosed', 'waiting_approval']); }));
            $inProgressCount = count(array_filter($orders ?? [], function($o) { return in_array($o['status'], ['in_progress', 'waiting_parts']); }));
            $completedCount = count(array_filter($orders ?? [], function($o) { return in_array($o['status'], ['completed', 'delivered']); }));
            ?>

            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Total Orders</p>
                        <p class="text-3xl font-bold text-gray-800"><?= $totalOrders ?></p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-clipboard-list text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Pending</p>
                        <p class="text-3xl font-bold text-yellow-600"><?= $pendingCount ?></p>
                    </div>
                    <div class="bg-yellow-100 p-3 rounded-full">
                        <i class="fas fa-clock text-yellow-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">In Progress</p>
                        <p class="text-3xl font-bold text-blue-600"><?= $inProgressCount ?></p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-wrench text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Completed</p>
                        <p class="text-3xl font-bold text-green-600"><?= $completedCount ?></p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <form method="GET" class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <input type="text" name="search" placeholder="Search by order number, customer name, or phone"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           value="<?= $search ?>">
                </div>
                <div>
                    <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Status</option>
                        <?php foreach ($statuses as $statusValue => $statusLabel): ?>
                            <option value="<?= $statusValue ?>" <?= $current_status === $statusValue ? 'selected' : '' ?>>
                                <?= $statusLabel ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="bg-gray-800 text-white px-6 py-2 rounded-lg hover:bg-gray-900 transition-colors">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
                <a href="/admin/orders" class="bg-gray-100 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-200 transition-colors">
                    <i class="fas fa-times mr-2"></i>Clear
                </a>
            </form>
        </div>

        <!-- Orders Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Device</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Technician</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (!empty($orders)): ?>
                        <?php foreach ($orders as $order): ?>
                            <tr class="hover:bg-gray-50 cursor-pointer" onclick="window.location='/admin/orders/<?= $order['id'] ?>'">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                            <i class="fas fa-clipboard-list text-blue-600"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900"><?= $order['order_number'] ?></div>
                                            <div class="text-sm text-gray-500">
                                                <?= format_order_priority($order['priority']) ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900"><?= $order['customer_name'] ?></div>
                                        <div class="text-sm text-gray-500"><?= $order['customer_phone'] ?></div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900"><?= $order['device_type_name'] ?></div>
                                        <div class="text-sm text-gray-500"><?= $order['device_brand'] ?> <?= $order['device_model'] ?></div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?= format_order_status($order['status']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($order['technician_name']): ?>
                                        <div class="flex items-center">
                                            <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center mr-2">
                                                <i class="fas fa-user text-green-600 text-xs"></i>
                                            </div>
                                            <span class="text-sm text-gray-900"><?= $order['technician_name'] ?></span>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-sm text-gray-500">Not assigned</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?= date('M d, Y', strtotime($order['created_at'])) ?></div>
                                    <div class="text-sm text-gray-500"><?= time_ago($order['created_at']) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2" onclick="event.stopPropagation()">
                                    <a href="/admin/orders/<?= $order['id'] ?>" class="text-blue-600 hover:text-blue-900">View</a>
                                    <a href="/admin/orders/<?= $order['id'] ?>/status" class="text-green-600 hover:text-green-900">Status</a>
                                    <a href="/admin/orders/<?= $order['id'] ?>/edit" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-clipboard-list text-4xl mb-4 text-gray-300"></i>
                                <h3 class="text-xl font-semibold text-gray-800 mb-2">No Orders Found</h3>
                                <p class="text-gray-600 mb-6">
                                    <?php if ($search || $status): ?>
                                        No orders match your current filters.
                                    <?php else: ?>
                                        Get started by creating your first repair order.
                                    <?php endif; ?>
                                </p>
                                <?php if (!$search && !$status): ?>
                                    <a href="/admin/orders/new" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                                        <i class="fas fa-plus mr-2"></i>Create First Order
                                    </a>
                                <?php else: ?>
                                    <a href="/admin/orders" class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition-colors">
                                        <i class="fas fa-times mr-2"></i>Clear Filters
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <?php if (isset($pager) && $pager->getPageCount() > 1): ?>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex flex-col sm:flex-row justify-between items-center">
                    <div class="text-sm text-gray-700 mb-4 sm:mb-0">
                        Showing <?= $pager->getFirstItem() ?> to <?= $pager->getLastItem() ?> of <?= $pager->getTotal() ?> results
                    </div>
                    <div class="flex space-x-1">
                        <?= $pager->links() ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Status Distribution -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Status Distribution</h3>
                <div class="space-y-3">
                    <?php
                    $statusCounts = [];
                    foreach ($orders ?? [] as $order) {
                        $status = $order['status'];
                        $statusCounts[$status] = ($statusCounts[$status] ?? 0) + 1;
                    }
                    ?>
                    <?php foreach ($statuses as $statusValue => $statusLabel): ?>
                        <?php $count = $statusCounts[$statusValue] ?? 0; ?>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <i class="fas fa-<?= get_status_icon($statusValue) ?> text-gray-400 mr-2"></i>
                                <span class="text-sm text-gray-600"><?= $statusLabel ?></span>
                            </div>
                            <span class="text-sm font-medium text-gray-900"><?= $count ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Priority Distribution -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Priority Distribution</h3>
                <div class="space-y-3">
                    <?php
                    $priorityCounts = ['low' => 0, 'normal' => 0, 'high' => 0, 'urgent' => 0];
                    foreach ($orders ?? [] as $order) {
                        $priority = $order['priority'] ?? 'normal';
                        $priorityCounts[$priority]++;
                    }
                    ?>
                    <?php foreach ($priorityCounts as $priority => $count): ?>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-3 h-3 rounded-full mr-2 <?=
                                $priority === 'urgent' ? 'bg-red-500' :
                                    ($priority === 'high' ? 'bg-orange-500' :
                                        ($priority === 'normal' ? 'bg-blue-500' : 'bg-gray-500'))
                                ?>"></div>
                                <span class="text-sm text-gray-600"><?= ucfirst($priority) ?></span>
                            </div>
                            <span class="text-sm font-medium text-gray-900"><?= $count ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Activity</h3>
                <div class="space-y-3">
                    <?php $recentOrders = array_slice($orders ?? [], 0, 5); ?>
                    <?php if (!empty($recentOrders)): ?>
                        <?php foreach ($recentOrders as $recent): ?>
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-<?= get_status_icon($recent['status']) ?> text-blue-600 text-xs"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-gray-900 truncate"><?= $recent['order_number'] ?></p>
                                    <p class="text-xs text-gray-500"><?= time_ago($recent['updated_at']) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-sm text-gray-500">No recent activity</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Action Modal (if needed) -->
    <div id="quickActionModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-xl shadow-2xl max-w-md w-full p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
                    <button onclick="closeQuickActionModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="space-y-3">
                    <button onclick="window.location='/admin/orders/new'" class="w-full text-left p-3 rounded-lg hover:bg-gray-50 border border-gray-200">
                        <i class="fas fa-plus text-green-600 mr-3"></i>
                        <span>Create New Order</span>
                    </button>

                    <button onclick="window.location='/admin/orders?status=received'" class="w-full text-left p-3 rounded-lg hover:bg-gray-50 border border-gray-200">
                        <i class="fas fa-inbox text-yellow-600 mr-3"></i>
                        <span>View New Orders</span>
                    </button>

                    <button onclick="window.location='/admin/orders?status=in_progress'" class="w-full text-left p-3 rounded-lg hover:bg-gray-50 border border-gray-200">
                        <i class="fas fa-wrench text-blue-600 mr-3"></i>
                        <span>View In Progress</span>
                    </button>

                    <button onclick="window.location='/admin/orders?status=completed'" class="w-full text-left p-3 rounded-lg hover:bg-gray-50 border border-gray-200">
                        <i class="fas fa-check-circle text-green-600 mr-3"></i>
                        <span>View Completed</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-refresh every 30 seconds for real-time updates
        let autoRefreshTimer;

        function startAutoRefresh() {
            autoRefreshTimer = setInterval(() => {
                // Only refresh if no modals are open and user is on the page
                if (!document.hidden && !document.querySelector('.modal:not(.hidden)')) {
                    window.location.reload();
                }
            }, 30000);
        }

        function stopAutoRefresh() {
            if (autoRefreshTimer) {
                clearInterval(autoRefreshTimer);
            }
        }

        // Start auto-refresh when page loads
        document.addEventListener('DOMContentLoaded', startAutoRefresh);

        // Stop auto-refresh when page is hidden
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                stopAutoRefresh();
            } else {
                startAutoRefresh();
            }
        });

        // Quick action modal functions
        function openQuickActionModal() {
            document.getElementById('quickActionModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeQuickActionModal() {
            document.getElementById('quickActionModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        // Close modal when clicking outside
        document.getElementById('quickActionModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeQuickActionModal();
            }
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // N for new order
            if (e.key === 'n' && !e.ctrlKey && !e.metaKey) {
                const activeElement = document.activeElement;
                if (activeElement.tagName !== 'INPUT' && activeElement.tagName !== 'TEXTAREA') {
                    window.location.href = '/admin/orders/new';
                }
            }

            // F for focus search
            if (e.key === 'f' && !e.ctrlKey && !e.metaKey) {
                const activeElement = document.activeElement;
                if (activeElement.tagName !== 'INPUT' && activeElement.tagName !== 'TEXTAREA') {
                    e.preventDefault();
                    document.querySelector('input[name="search"]').focus();
                }
            }

            // Q for quick actions
            if (e.key === 'q' && !e.ctrlKey && !e.metaKey) {
                const activeElement = document.activeElement;
                if (activeElement.tagName !== 'INPUT' && activeElement.tagName !== 'TEXTAREA') {
                    e.preventDefault();
                    openQuickActionModal();
                }
            }

            // Escape to close modal
            if (e.key === 'Escape') {
                closeQuickActionModal();
            }

            // Number keys for status filters
            if (e.key >= '1' && e.key <= '8') {
                const activeElement = document.activeElement;
                if (activeElement.tagName !== 'INPUT' && activeElement.tagName !== 'TEXTAREA') {
                    const statuses = ['', 'received', 'diagnosed', 'waiting_approval', 'in_progress', 'waiting_parts', 'completed', 'delivered'];
                    const statusIndex = parseInt(e.key);
                    if (statuses[statusIndex]) {
                        window.location.href = `/admin/orders?status=${statuses[statusIndex]}`;
                    }
                }
            }
        });

        // Enhanced row click handling
        document.querySelectorAll('tbody tr').forEach(row => {
            row.addEventListener('click', function(e) {
                // Don't navigate if clicking on action links
                if (e.target.tagName === 'A' || e.target.closest('a')) {
                    return;
                }

                const orderLink = this.getAttribute('onclick');
                if (orderLink) {
                    eval(orderLink);
                }
            });
        });

        // Add loading states to action buttons
        document.querySelectorAll('a[href*="/status"], a[href*="/edit"]').forEach(link => {
            link.addEventListener('click', function() {
                const icon = this.querySelector('i');
                if (icon) {
                    icon.className = 'fas fa-spinner fa-spin';
                }
            });
        });

        // Add tooltips for truncated text
        document.querySelectorAll('.truncate').forEach(element => {
            element.addEventListener('mouseenter', function() {
                if (this.scrollWidth > this.clientWidth) {
                    this.title = this.textContent;
                }
            });
        });
    </script>
<?= $this->endSection() ?>
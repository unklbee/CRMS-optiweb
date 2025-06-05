<?= $this->extend('admin/layout/main') ?>

<?= $this->section('content') ?>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Diagnosis Queue</h1>
                <p class="text-gray-600">Orders waiting for or undergoing diagnosis</p>
            </div>
            <a href="/admin/orders" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left mr-2"></i>Back to Orders
            </a>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Pending Diagnosis</p>
                        <p class="text-3xl font-bold text-yellow-600"><?= $summary['pending_diagnosis'] ?></p>
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
                        <p class="text-3xl font-bold text-blue-600"><?= $summary['in_progress_diagnosis'] ?></p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-stethoscope text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Completed</p>
                        <p class="text-3xl font-bold text-green-600"><?= $summary['completed_diagnosis'] ?></p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Awaiting Approval</p>
                        <p class="text-3xl font-bold text-purple-600"><?= $summary['awaiting_approval'] ?></p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-full">
                        <i class="fas fa-user-check text-purple-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Orders Requiring Diagnosis</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Device</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diagnosis Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Age</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (!empty($orders)): ?>
                        <?php foreach ($orders as $order): ?>
                            <tr class="hover:bg-gray-50 <?= $order['priority'] === 'urgent' ? 'bg-red-50' : ($order['priority'] === 'high' ? 'bg-orange-50' : '') ?>">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                            <i class="fas fa-stethoscope text-blue-600"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900"><?= $order['order_number'] ?></div>
                                            <div class="text-sm text-gray-500"><?= format_order_status($order['status']) ?></div>
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
                                    <?= format_order_priority($order['priority']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php
                                    $diagnosisStatus = $order['diagnosis_status'] ?? 'pending';
                                    $statusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'in_progress' => 'bg-blue-100 text-blue-800',
                                        'completed' => 'bg-green-100 text-green-800'
                                    ];
                                    $color = $statusColors[$diagnosisStatus] ?? 'bg-gray-100 text-gray-800';
                                    ?>
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?= $color ?>">
                                        <?= ucfirst(str_replace('_', ' ', $diagnosisStatus)) ?>
                                    </span>
                                    <?php if ($order['technician_name']): ?>
                                        <div class="text-xs text-gray-500 mt-1">by <?= $order['technician_name'] ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?= time_ago($order['created_at']) ?></div>
                                    <div class="text-xs text-gray-500"><?= date('M d, Y', strtotime($order['created_at'])) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                    <?php if (($order['diagnosis_status'] ?? 'pending') === 'pending'): ?>
                                        <a href="/admin/orders/<?= $order['id'] ?>/start-diagnosis"
                                           class="bg-blue-600 text-white px-3 py-1 rounded text-xs hover:bg-blue-700">
                                            <i class="fas fa-play mr-1"></i>Start
                                        </a>
                                    <?php elseif ($order['diagnosis_status'] === 'in_progress'): ?>
                                        <a href="/admin/orders/<?= $order['id'] ?>/diagnosis"
                                           class="bg-green-600 text-white px-3 py-1 rounded text-xs hover:bg-green-700">
                                            <i class="fas fa-edit mr-1"></i>Continue
                                        </a>
                                    <?php else: ?>
                                        <a href="/admin/orders/<?= $order['id'] ?>/diagnosis"
                                           class="bg-purple-600 text-white px-3 py-1 rounded text-xs hover:bg-purple-700">
                                            <i class="fas fa-eye mr-1"></i>Review
                                        </a>
                                    <?php endif; ?>

                                    <a href="/admin/orders/<?= $order['id'] ?>"
                                       class="text-gray-600 hover:text-gray-900 px-3 py-1">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-stethoscope text-4xl mb-4 text-gray-300"></i>
                                <h3 class="text-xl font-semibold text-gray-800 mb-2">No Orders Awaiting Diagnosis</h3>
                                <p class="text-gray-600">All orders have been diagnosed or don't require diagnosis yet.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="/admin/orders?status=received"
                   class="p-4 border border-gray-200 rounded-lg hover:border-blue-300 hover:bg-blue-50 transition-colors">
                    <div class="flex items-center">
                        <i class="fas fa-inbox text-blue-600 text-xl mr-3"></i>
                        <div>
                            <h4 class="font-medium text-gray-900">New Orders</h4>
                            <p class="text-sm text-gray-600">View recently received orders</p>
                        </div>
                    </div>
                </a>

                <a href="/admin/orders?status=diagnosed"
                   class="p-4 border border-gray-200 rounded-lg hover:border-green-300 hover:bg-green-50 transition-colors">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-600 text-xl mr-3"></i>
                        <div>
                            <h4 class="font-medium text-gray-900">Diagnosed Orders</h4>
                            <p class="text-sm text-gray-600">Orders with completed diagnosis</p>
                        </div>
                    </div>
                </a>

                <a href="/admin/orders?status=waiting_approval"
                   class="p-4 border border-gray-200 rounded-lg hover:border-purple-300 hover:bg-purple-50 transition-colors">
                    <div class="flex items-center">
                        <i class="fas fa-user-check text-purple-600 text-xl mr-3"></i>
                        <div>
                            <h4 class="font-medium text-gray-900">Awaiting Approval</h4>
                            <p class="text-sm text-gray-600">Customer approval pending</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <script>
        // Auto-refresh every 30 seconds
        let autoRefreshTimer = setInterval(() => {
            if (!document.hidden) {
                window.location.reload();
            }
        }, 30000);

        // Stop auto-refresh when page is hidden
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                clearInterval(autoRefreshTimer);
            } else {
                autoRefreshTimer = setInterval(() => {
                    window.location.reload();
                }, 30000);
            }
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // R for refresh
            if (e.key === 'r' && !e.ctrlKey && !e.metaKey) {
                const activeElement = document.activeElement;
                if (activeElement.tagName !== 'INPUT' && activeElement.tagName !== 'TEXTAREA') {
                    e.preventDefault();
                    window.location.reload();
                }
            }
        });
    </script>
<?= $this->endSection() ?>
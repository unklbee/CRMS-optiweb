<?= $this->extend('admin/layout/main') ?>

<?= $this->section('content') ?>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Order Details</h1>
                <p class="text-gray-600">Order #<?= $order['order_number'] ?></p>
            </div>
            <div class="flex space-x-2">
                <a href="/admin/orders/<?= $order['id'] ?>/status" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-edit mr-2"></i>Update Status
                </a>
                <a href="/admin/orders/<?= $order['id'] ?>/edit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-edit mr-2"></i>Edit Order
                </a>
                <a href="/admin/orders" class="text-gray-600 hover:text-gray-900 px-4 py-2">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Orders
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Order Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Order Information</h3>

                    <div class="space-y-4">
                        <div class="flex items-center space-x-4">
                            <div class="w-16 h-16 bg-blue-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-clipboard-list text-blue-600 text-2xl"></i>
                            </div>
                            <div>
                                <h4 class="text-xl font-semibold text-gray-900">Order #<?= $order['order_number'] ?></h4>
                                <p class="text-gray-600"><?= $order['customer_name'] ?></p>
                                <div class="flex items-center space-x-4 mt-1">
                                    <?= format_order_status($order['status']) ?>
                                    <?= format_order_priority($order['priority']) ?>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <h4 class="font-medium text-gray-900 mb-2">Device Information</h4>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Type:</span>
                                        <span class="font-medium"><?= $order['device_type_name'] ?></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Brand:</span>
                                        <span class="font-medium"><?= $order['device_brand'] ?></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Model:</span>
                                        <span class="font-medium"><?= $order['device_model'] ?></span>
                                    </div>
                                    <?php if ($order['device_serial']): ?>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Serial:</span>
                                            <span class="font-medium font-mono"><?= $order['device_serial'] ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div>
                                <h4 class="font-medium text-gray-900 mb-2">Order Details</h4>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Created:</span>
                                        <span class="font-medium"><?= date('M d, Y H:i', strtotime($order['created_at'])) ?></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Updated:</span>
                                        <span class="font-medium"><?= date('M d, Y H:i', strtotime($order['updated_at'])) ?></span>
                                    </div>
                                    <?php if ($order['completed_at']): ?>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Completed:</span>
                                            <span class="font-medium text-green-600"><?= date('M d, Y H:i', strtotime($order['completed_at'])) ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($order['technician_name']): ?>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Technician:</span>
                                            <span class="font-medium"><?= $order['technician_name'] ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <?php if ($order['problem_description']): ?>
                            <div>
                                <h4 class="font-medium text-gray-900 mb-2">Problem Description</h4>
                                <p class="text-gray-600 bg-gray-50 p-3 rounded-lg"><?= nl2br($order['problem_description']) ?></p>
                            </div>
                        <?php endif; ?>

                        <?php if ($order['accessories']): ?>
                            <div>
                                <h4 class="font-medium text-gray-900 mb-2">Accessories</h4>
                                <p class="text-gray-600"><?= nl2br($order['accessories']) ?></p>
                            </div>
                        <?php endif; ?>

                        <?php if ($order['notes']): ?>
                            <div>
                                <h4 class="font-medium text-gray-900 mb-2">Notes</h4>
                                <p class="text-gray-600 bg-blue-50 p-3 rounded-lg border border-blue-200"><?= nl2br($order['notes']) ?></p>
                            </div>
                        <?php endif; ?>

                        <!-- Cost Information -->
                        <?php if ($order['estimated_cost'] || $order['final_cost']): ?>
                            <div class="border-t border-gray-200 pt-4">
                                <h4 class="font-medium text-gray-900 mb-3">Cost Information</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <?php if ($order['estimated_cost']): ?>
                                        <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                                            <div class="text-center">
                                                <p class="text-2xl font-bold text-yellow-600"><?= format_currency($order['estimated_cost']) ?></p>
                                                <p class="text-sm text-yellow-700">Estimated Cost</p>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($order['final_cost']): ?>
                                        <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                                            <div class="text-center">
                                                <p class="text-2xl font-bold text-green-600"><?= format_currency($order['final_cost']) ?></p>
                                                <p class="text-sm text-green-700">Final Cost</p>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Customer Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Customer Information</h3>

                    <div class="space-y-4">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-green-600 text-xl"></i>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900"><?= $order['customer_name'] ?></h4>
                                <p class="text-gray-600"><?= $order['customer_phone'] ?></p>
                                <?php if ($order['customer_email']): ?>
                                    <p class="text-gray-600"><?= $order['customer_email'] ?></p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="flex space-x-3">
                            <a href="tel:<?= $order['customer_phone'] ?>" class="flex-1 bg-blue-50 text-blue-700 py-2 px-4 rounded-lg hover:bg-blue-100 transition-colors text-center">
                                <i class="fas fa-phone mr-2"></i>Call Customer
                            </a>
                            <?php if ($order['customer_email']): ?>
                                <a href="mailto:<?= $order['customer_email'] ?>" class="flex-1 bg-green-50 text-green-700 py-2 px-4 rounded-lg hover:bg-green-100 transition-colors text-center">
                                    <i class="fas fa-envelope mr-2"></i>Send Email
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Status History -->
                <?php if (!empty($status_history)): ?>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800">Status History</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <?php foreach ($status_history as $history): ?>
                                    <div class="flex items-start space-x-4">
                                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                            <i class="fas fa-<?= get_status_icon($history['new_status']) ?> text-blue-600 text-sm"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between">
                                                <p class="text-sm font-medium text-gray-900">
                                                    Status changed from
                                                    <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs"><?= ucfirst(str_replace('_', ' ', $history['old_status'])) ?></span>
                                                    to
                                                    <?= format_order_status($history['new_status']) ?>
                                                </p>
                                                <p class="text-sm text-gray-500"><?= time_ago($history['created_at']) ?></p>
                                            </div>
                                            <?php if ($history['notes']): ?>
                                                <p class="text-sm text-gray-600 mt-1"><?= $history['notes'] ?></p>
                                            <?php endif; ?>
                                            <?php if ($history['changed_by_name']): ?>
                                                <p class="text-xs text-gray-500 mt-1">by <?= $history['changed_by_name'] ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Parts Used -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-800">Parts Used</h3>
                            <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm">
                                <i class="fas fa-plus mr-2"></i>Add Part
                            </button>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-box text-4xl text-gray-300 mb-4"></i>
                            <h4 class="text-lg font-medium text-gray-900 mb-2">No Parts Used</h4>
                            <p class="text-gray-600">Parts used for this repair will appear here</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Actions -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <a href="/admin/orders/<?= $order['id'] ?>/status"
                           class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors text-center block">
                            <i class="fas fa-edit mr-2"></i>Update Status
                        </a>

                        <a href="/admin/orders/<?= $order['id'] ?>/edit"
                           class="w-full bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition-colors text-center block">
                            <i class="fas fa-edit mr-2"></i>Edit Order
                        </a>

                        <button onclick="printOrder()"
                                class="w-full bg-purple-600 text-white py-2 px-4 rounded-lg hover:bg-purple-700 transition-colors">
                            <i class="fas fa-print mr-2"></i>Print Order
                        </button>

                        <button onclick="duplicateOrder()"
                                class="w-full bg-yellow-600 text-white py-2 px-4 rounded-lg hover:bg-yellow-700 transition-colors">
                            <i class="fas fa-copy mr-2"></i>Duplicate Order
                        </button>

                        <button onclick="deleteOrder()"
                                class="w-full bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition-colors">
                            <i class="fas fa-trash mr-2"></i>Delete Order
                        </button>
                    </div>
                </div>

                <!-- Order Progress -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Order Progress</h3>

                    <?php
                    $currentProgress = get_status_progress($order['status']);
                    if ($currentProgress !== null):
                        ?>
                        <div class="mb-4">
                            <div class="flex justify-between text-sm mb-2">
                                <span class="text-gray-600">Progress</span>
                                <span class="font-medium"><?= $currentProgress ?>%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="bg-blue-600 h-3 rounded-full transition-all duration-300" style="width: <?= $currentProgress ?>%"></div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="space-y-3">
                        <?php
                        $allStatuses = [
                            'received' => 'Received',
                            'diagnosed' => 'Diagnosed',
                            'waiting_approval' => 'Waiting Approval',
                            'in_progress' => 'In Progress',
                            'completed' => 'Completed',
                            'delivered' => 'Delivered'
                        ];

                        $statusOrder = array_keys($allStatuses);
                        $currentIndex = array_search($order['status'], $statusOrder);
                        ?>

                        <?php foreach ($allStatuses as $statusKey => $statusLabel): ?>
                            <?php
                            $statusIndex = array_search($statusKey, $statusOrder);
                            $isCompleted = $currentIndex !== false && $statusIndex <= $currentIndex;
                            $isCurrent = $statusKey === $order['status'];
                            ?>
                            <div class="flex items-center space-x-3">
                                <div class="w-6 h-6 rounded-full flex items-center justify-center
                                    <?= $isCompleted ? 'bg-green-500 text-white' : ($isCurrent ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-500') ?>">
                                    <?php if ($isCompleted && !$isCurrent): ?>
                                        <i class="fas fa-check text-xs"></i>
                                    <?php elseif ($isCurrent): ?>
                                        <i class="fas fa-circle text-xs"></i>
                                    <?php else: ?>
                                        <span class="text-xs"><?= $statusIndex + 1 ?></span>
                                    <?php endif; ?>
                                </div>
                                <span class="text-sm <?= $isCurrent ? 'font-medium text-blue-600' : ($isCompleted ? 'text-green-600' : 'text-gray-500') ?>">
                                    <?= $statusLabel ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Order Statistics -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Order Statistics</h3>
                    <div class="space-y-4">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-blue-600">
                                <?php
                                if ($order['created_at']) {
                                    $days = floor((time() - strtotime($order['created_at'])) / (60 * 60 * 24));
                                    echo $days;
                                } else {
                                    echo '0';
                                }
                                ?>
                            </p>
                            <p class="text-sm text-gray-600">Days in System</p>
                        </div>

                        <?php if ($order['completed_at']): ?>
                            <div class="text-center">
                                <p class="text-lg font-medium text-green-600">
                                    <?php
                                    $turnaroundDays = floor((strtotime($order['completed_at']) - strtotime($order['created_at'])) / (60 * 60 * 24));
                                    echo $turnaroundDays . ' days';
                                    ?>
                                </p>
                                <p class="text-sm text-gray-600">Turnaround Time</p>
                            </div>
                        <?php endif; ?>

                        <div class="text-center">
                            <p class="text-lg font-medium text-purple-600"><?= count($status_history) ?></p>
                            <p class="text-sm text-gray-600">Status Changes</p>
                        </div>
                    </div>
                </div>

                <!-- Customer Notes -->
                <div class="bg-gradient-to-br from-green-50 to-emerald-100 rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Customer Contact</h3>
                    <div class="space-y-2 text-sm">
                        <p class="text-green-800 font-medium"><?= $order['customer_name'] ?></p>
                        <p class="text-green-700"><?= $order['customer_phone'] ?></p>
                        <?php if ($order['customer_email']): ?>
                            <p class="text-green-700"><?= $order['customer_email'] ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="mt-4 flex space-x-2">
                        <a href="tel:<?= $order['customer_phone'] ?>" class="flex-1 bg-green-600 text-white py-2 px-3 rounded-lg hover:bg-green-700 transition-colors text-center text-sm">
                            <i class="fas fa-phone mr-1"></i>Call
                        </a>
                        <?php if ($order['customer_email']): ?>
                            <a href="mailto:<?= $order['customer_email'] ?>" class="flex-1 bg-blue-600 text-white py-2 px-3 rounded-lg hover:bg-blue-700 transition-colors text-center text-sm">
                                <i class="fas fa-envelope mr-1"></i>Email
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function printOrder() {
            const printWindow = window.open('', '_blank');
            const orderData = `
                <html>
                <head>
                    <title>Order #<?= $order['order_number'] ?></title>
                    <style>
                        body { font-family: Arial, sans-serif; padding: 20px; }
                        .header { border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
                        .section { margin-bottom: 20px; }
                        .label { font-weight: bold; }
                        .status { padding: 5px 10px; border-radius: 5px; background: #e5e7eb; }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <h1>Repair Order #<?= $order['order_number'] ?></h1>
                        <p>Date: <?= date('M d, Y H:i', strtotime($order['created_at'])) ?></p>
                    </div>

                    <div class="section">
                        <h3>Customer Information</h3>
                        <p><span class="label">Name:</span> <?= $order['customer_name'] ?></p>
                        <p><span class="label">Phone:</span> <?= $order['customer_phone'] ?></p>
                        <?php if ($order['customer_email']): ?>
                        <p><span class="label">Email:</span> <?= $order['customer_email'] ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="section">
                        <h3>Device Information</h3>
                        <p><span class="label">Type:</span> <?= $order['device_type_name'] ?></p>
                        <p><span class="label">Brand:</span> <?= $order['device_brand'] ?></p>
                        <p><span class="label">Model:</span> <?= $order['device_model'] ?></p>
                        <?php if ($order['device_serial']): ?>
                        <p><span class="label">Serial:</span> <?= $order['device_serial'] ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="section">
                        <h3>Problem Description</h3>
                        <p><?= nl2br($order['problem_description']) ?></p>
                    </div>

                    <div class="section">
                        <h3>Order Status</h3>
                        <p><span class="status"><?= $statuses[$order['status']] ?></span></p>
                        <p><span class="label">Priority:</span> <?= ucfirst($order['priority']) ?></p>
                        <?php if ($order['technician_name']): ?>
                        <p><span class="label">Technician:</span> <?= $order['technician_name'] ?></p>
                        <?php endif; ?>
                    </div>

                    <?php if ($order['estimated_cost'] || $order['final_cost']): ?>
                    <div class="section">
                        <h3>Cost Information</h3>
                        <?php if ($order['estimated_cost']): ?>
                        <p><span class="label">Estimated Cost:</span> <?= format_currency($order['estimated_cost']) ?></p>
                        <?php endif; ?>
                        <?php if ($order['final_cost']): ?>
                        <p><span class="label">Final Cost:</span> <?= format_currency($order['final_cost']) ?></p>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </body>
                </html>
            `;

            printWindow.document.write(orderData);
            printWindow.document.close();
            printWindow.print();
        }

        function duplicateOrder() {
            if (confirm('Create a duplicate of this order?')) {
                window.location.href = '/admin/orders/new?duplicate=<?= $order['id'] ?>';
            }
        }

        function deleteOrder() {
            if (confirm('Are you sure you want to delete this order? This action cannot be undone.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/admin/orders/<?= $order['id'] ?>';
                form.innerHTML = `
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="DELETE">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // U for update status
            if (e.key === 'u' && !e.ctrlKey && !e.metaKey) {
                const activeElement = document.activeElement;
                if (activeElement.tagName !== 'INPUT' && activeElement.tagName !== 'TEXTAREA') {
                    window.location.href = '/admin/orders/<?= $order['id'] ?>/status';
                }
            }

            // E for edit
            if (e.key === 'e' && !e.ctrlKey && !e.metaKey) {
                const activeElement = document.activeElement;
                if (activeElement.tagName !== 'INPUT' && activeElement.tagName !== 'TEXTAREA') {
                    window.location.href = '/admin/orders/<?= $order['id'] ?>/edit';
                }
            }

            // P for print
            if (e.key === 'p' && !e.ctrlKey && !e.metaKey) {
                const activeElement = document.activeElement;
                if (activeElement.tagName !== 'INPUT' && activeElement.tagName !== 'TEXTAREA') {
                    e.preventDefault();
                    printOrder();
                }
            }
        });
    </script>
<?= $this->endSection() ?>
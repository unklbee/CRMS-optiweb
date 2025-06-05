<?= $this->extend('admin/layout/main') ?>

<?= $this->section('content') ?>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Order Details</h1>
                <p class="text-gray-600">Order #<?= $order['order_number'] ?></p>
            </div>
            <div class="flex space-x-2">
                <a href="/admin/orders/<?= $order['id'] ?>/edit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-edit mr-2"></i>Edit Order
                </a>
                <a href="/admin/orders/<?= $order['id'] ?>/status" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-sync mr-2"></i>Update Status
                </a>
                <a href="/admin/orders/<?= $order['id'] ?>/manage-parts" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                    <i class="fas fa-cogs mr-2"></i>Manage Parts
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Order Information -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Device Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Device Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Device Type</label>
                            <p class="text-gray-900"><?= $order['device_type_name'] ?></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Brand</label>
                            <p class="text-gray-900"><?= $order['device_brand'] ?></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Model</label>
                            <p class="text-gray-900"><?= $order['device_model'] ?></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Serial Number</label>
                            <p class="text-gray-900"><?= $order['device_serial'] ?: 'N/A' ?></p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-600">Problem Description</label>
                        <p class="text-gray-900 mt-1"><?= $order['problem_description'] ?></p>
                    </div>
                    <?php if ($order['accessories']): ?>
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-600">Accessories</label>
                            <p class="text-gray-900 mt-1"><?= $order['accessories'] ?></p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Customer Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Customer Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Name</label>
                            <p class="text-gray-900"><?= $order['customer_name'] ?></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Phone</label>
                            <p class="text-gray-900"><?= $order['customer_phone'] ?></p>
                        </div>
                        <?php if ($order['customer_email']): ?>
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Email</label>
                                <p class="text-gray-900"><?= $order['customer_email'] ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Parts Used -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-800">Parts Used</h3>
                            <a href="/admin/orders/<?= $order['id'] ?>/manage-parts" class="text-blue-600 hover:text-blue-800 text-sm">
                                <i class="fas fa-plus mr-1"></i>Add Parts
                            </a>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <?php if (!empty($order_parts)): ?>
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Part</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Price</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                <?php
                                $totalPartsPrice = 0;
                                foreach ($order_parts as $part):
                                    $totalPartsPrice += $part['total_price'];
                                    ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900"><?= $part['part_name'] ?></div>
                                                <div class="text-sm text-gray-500"><?= $part['part_number'] ?></div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?= $part['quantity'] ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?= format_currency($part['unit_price']) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <?= format_currency($part['total_price']) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <button onclick="removePart(<?= $part['id'] ?>)" class="text-red-600 hover:text-red-900">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php if ($part['notes']): ?>
                                    <tr class="bg-gray-50">
                                        <td colspan="5" class="px-6 py-2 text-sm text-gray-600 italic">
                                            <i class="fas fa-comment-alt mr-2"></i><?= $part['notes'] ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                <?php endforeach; ?>
                                </tbody>
                                <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-sm font-medium text-gray-900 text-right">
                                        Total Parts Cost:
                                    </td>
                                    <td class="px-6 py-4 text-sm font-bold text-gray-900">
                                        <?= format_currency($totalPartsPrice) ?>
                                    </td>
                                    <td></td>
                                </tr>
                                </tfoot>
                            </table>
                        <?php else: ?>
                            <div class="text-center py-8">
                                <i class="fas fa-cogs text-4xl text-gray-300 mb-4"></i>
                                <h4 class="text-lg font-medium text-gray-900 mb-2">No Parts Used</h4>
                                <p class="text-gray-600 mb-4">No parts have been added to this order yet</p>
                                <a href="/admin/orders/<?= $order['id'] ?>/manage-parts" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                    <i class="fas fa-plus mr-2"></i>Add Parts
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Stock Movements Related to This Order -->
                <?php if (!empty($stock_movements)): ?>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800">Stock Movements</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Part</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Movement</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cost</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($stock_movements as $movement): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?= date('M d, Y H:i', strtotime($movement['created_at'])) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900"><?= $movement['part_name'] ?></div>
                                                <div class="text-sm text-gray-500"><?= $movement['part_number'] ?></div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?php
                                            $typeColors = [
                                                'add' => 'bg-green-100 text-green-800',
                                                'subtract' => 'bg-red-100 text-red-800',
                                                'use' => 'bg-orange-100 text-orange-800',
                                                'return' => 'bg-purple-100 text-purple-800'
                                            ];
                                            $color = $typeColors[$movement['movement_type']] ?? 'bg-gray-100 text-gray-800';
                                            ?>
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?= $color ?>">
                                                <?= ucfirst($movement['movement_type']) ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php if (in_array($movement['movement_type'], ['add', 'return'])): ?>
                                                <span class="text-green-600">+<?= $movement['quantity_change'] ?></span>
                                            <?php else: ?>
                                                <span class="text-red-600">-<?= $movement['quantity_change'] ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?= format_currency($movement['total_cost']) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Order Status & Actions -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Order Status</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Current Status</label>
                            <span class="inline-flex px-3 py-1 rounded-full text-sm font-medium
                            <?= $order['status'] === 'completed' ? 'bg-green-100 text-green-800' :
                                ($order['status'] === 'in_progress' ? 'bg-blue-100 text-blue-800' :
                                    ($order['status'] === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800')) ?>">
                            <?= ucfirst(str_replace('_', ' ', $order['status'])) ?>
                        </span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600">Priority</label>
                            <span class="inline-flex px-3 py-1 rounded-full text-sm font-medium
                            <?= $order['priority'] === 'urgent' ? 'bg-red-100 text-red-800' :
                                ($order['priority'] === 'high' ? 'bg-orange-100 text-orange-800' : 'bg-gray-100 text-gray-800') ?>">
                            <?= ucfirst($order['priority']) ?>
                        </span>
                        </div>

                        <?php if ($order['technician_name']): ?>
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Assigned Technician</label>
                                <p class="text-gray-900"><?= $order['technician_name'] ?></p>
                            </div>
                        <?php endif; ?>

                        <div>
                            <label class="block text-sm font-medium text-gray-600">Created Date</label>
                            <p class="text-gray-900"><?= date('M d, Y H:i', strtotime($order['created_at'])) ?></p>
                        </div>

                        <?php if ($order['estimated_completion']): ?>
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Estimated Completion</label>
                                <p class="text-gray-900"><?= date('M d, Y H:i', strtotime($order['estimated_completion'])) ?></p>
                            </div>
                        <?php endif; ?>

                        <?php if ($order['completed_at']): ?>
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Completed Date</label>
                                <p class="text-gray-900"><?= date('M d, Y H:i', strtotime($order['completed_at'])) ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Cost Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Cost Information</h3>
                    <div class="space-y-3">
                        <?php if ($order['estimated_cost'] > 0): ?>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Estimated Cost</span>
                                <span class="font-medium"><?= format_currency($order['estimated_cost']) ?></span>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($order_parts)): ?>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Parts Cost</span>
                                <span class="font-medium"><?= format_currency($totalPartsPrice ?? 0) ?></span>
                            </div>
                        <?php endif; ?>

                        <?php if ($order['final_cost'] > 0): ?>
                            <div class="flex justify-between border-t pt-3">
                                <span class="text-gray-600 font-medium">Final Cost</span>
                                <span class="font-bold text-lg"><?= format_currency($order['final_cost']) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <a href="/admin/orders/<?= $order['id'] ?>/edit"
                           class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors text-center block">
                            <i class="fas fa-edit mr-2"></i>Edit Order
                        </a>

                        <a href="/admin/orders/<?= $order['id'] ?>/status"
                           class="w-full bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition-colors text-center block">
                            <i class="fas fa-sync mr-2"></i>Update Status
                        </a>

                        <a href="/admin/orders/<?= $order['id'] ?>/manage-parts"
                           class="w-full bg-purple-600 text-white py-2 px-4 rounded-lg hover:bg-purple-700 transition-colors text-center block">
                            <i class="fas fa-cogs mr-2"></i>Manage Parts
                        </a>

                        <button onclick="printOrder()"
                                class="w-full bg-gray-600 text-white py-2 px-4 rounded-lg hover:bg-gray-700 transition-colors">
                            <i class="fas fa-print mr-2"></i>Print Order
                        </button>

                        <button onclick="deleteOrder()"
                                class="w-full bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition-colors">
                            <i class="fas fa-trash mr-2"></i>Delete Order
                        </button>
                    </div>
                </div>

                <!-- Notes -->
                <?php if ($order['notes']): ?>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">
                            <i class="fas fa-sticky-note text-yellow-600 mr-2"></i>Notes
                        </h3>
                        <p class="text-gray-700"><?= nl2br($order['notes']) ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        function removePart(orderPartId) {
            if (confirm('Are you sure you want to remove this part from the order? Stock will be restored.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/orders/<?= $order['id'] ?>/parts/${orderPartId}/remove`;
                form.innerHTML = `
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="DELETE">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        function printOrder() {
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <html>
                <head>
                    <title>Order #<?= $order['order_number'] ?></title>
                    <style>
                        body { font-family: Arial, sans-serif; padding: 20px; }
                        .header { border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
                        .section { margin: 20px 0; }
                        .label { font-weight: bold; }
                        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
                        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                        th { background-color: #f2f2f2; }
                        .total { font-weight: bold; background-color: #f9f9f9; }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <h1>Repair Order #<?= $order['order_number'] ?></h1>
                        <p>Date: <?= date('M d, Y H:i', strtotime($order['created_at'])) ?></p>
                        <p>Status: <?= ucfirst(str_replace('_', ' ', $order['status'])) ?></p>
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
                        <p><span class="label">Problem:</span> <?= $order['problem_description'] ?></p>
                    </div>

                    <?php if (!empty($order_parts)): ?>
                    <div class="section">
                        <h3>Parts Used</h3>
                        <table>
                            <thead>
                                <tr>
                                    <th>Part</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($order_parts as $part): ?>
                                <tr>
                                    <td><?= $part['part_name'] ?> (<?= $part['part_number'] ?>)</td>
                                    <td><?= $part['quantity'] ?></td>
                                    <td><?= format_currency($part['unit_price']) ?></td>
                                    <td><?= format_currency($part['total_price']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <tr class="total">
                                    <td colspan="3">Total Parts Cost</td>
                                    <td><?= format_currency($totalPartsPrice ?? 0) ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>

                    <?php if ($order['final_cost'] > 0): ?>
                    <div class="section">
                        <h3>Final Cost: <?= format_currency($order['final_cost']) ?></h3>
                    </div>
                    <?php endif; ?>

                    <?php if ($order['notes']): ?>
                    <div class="section">
                        <h3>Notes</h3>
                        <p><?= nl2br($order['notes']) ?></p>
                    </div>
                    <?php endif; ?>
                </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.print();
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
            // E for edit
            if (e.key === 'e' && !e.ctrlKey && !e.metaKey) {
                const activeElement = document.activeElement;
                if (activeElement.tagName !== 'INPUT' && activeElement.tagName !== 'TEXTAREA') {
                    window.location.href = '/admin/orders/<?= $order['id'] ?>/edit';
                }
            }

            // S for status update
            if (e.key === 's' && !e.ctrlKey && !e.metaKey) {
                const activeElement = document.activeElement;
                if (activeElement.tagName !== 'INPUT' && activeElement.tagName !== 'TEXTAREA') {
                    window.location.href = '/admin/orders/<?= $order['id'] ?>/status';
                }
            }

            // P for parts management
            if (e.key === 'p' && !e.ctrlKey && !e.metaKey) {
                const activeElement = document.activeElement;
                if (activeElement.tagName !== 'INPUT' && activeElement.tagName !== 'TEXTAREA') {
                    window.location.href = '/admin/orders/<?= $order['id'] ?>/manage-parts';
                }
            }

            // Ctrl/Cmd + P for print
            if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
                e.preventDefault();
                printOrder();
            }
        });

        // Auto-refresh status if order is in progress
        <?php if (in_array($order['status'], ['received', 'diagnosed', 'in_progress', 'waiting_parts'])): ?>
        setInterval(function() {
            // You can implement real-time status checking here
            // For example, fetch current status via AJAX
        }, 60000); // Check every minute
        <?php endif; ?>
    </script>
<?= $this->endSection() ?>
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

        <!-- WORKFLOW PROGRESS BAR -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Order Progress</h3>

            <?php
            $currentStatus = $order['status'];
            $steps = [
                'received' => ['label' => 'Received', 'icon' => 'inbox', 'description' => 'Device received'],
                'diagnosed' => ['label' => 'Diagnosed', 'icon' => 'search', 'description' => 'Diagnosis completed'],
                'waiting_approval' => ['label' => 'Quotation Sent', 'icon' => 'file-invoice-dollar', 'description' => 'Awaiting customer approval'],
                'in_progress' => ['label' => 'In Progress', 'icon' => 'wrench', 'description' => 'Repair work ongoing'],
                'completed' => ['label' => 'Completed', 'icon' => 'check-circle', 'description' => 'Ready for pickup'],
                'delivered' => ['label' => 'Delivered', 'icon' => 'truck', 'description' => 'Delivered to customer']
            ];

            $statusOrder = array_keys($steps);
            $currentIndex = array_search($currentStatus, $statusOrder);
            ?>

            <div class="flex items-center justify-between">
                <?php foreach ($steps as $status => $step):
                    $stepIndex = array_search($status, $statusOrder);
                    $isCompleted = $stepIndex <= $currentIndex;
                    $isCurrent = $status === $currentStatus;
                    ?>
                    <div class="flex flex-col items-center flex-1">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center mb-2 <?= $isCompleted ? 'bg-green-500 text-white' : ($isCurrent ? 'bg-blue-500 text-white' : 'bg-gray-300 text-gray-600') ?>">
                            <i class="fas fa-<?= $step['icon'] ?> text-sm"></i>
                        </div>
                        <div class="text-center">
                            <div class="text-sm font-medium <?= $isCurrent ? 'text-blue-600' : ($isCompleted ? 'text-green-600' : 'text-gray-500') ?>">
                                <?= $step['label'] ?>
                            </div>
                            <div class="text-xs text-gray-500 mt-1"><?= $step['description'] ?></div>
                        </div>
                    </div>

                    <?php if ($stepIndex < count($steps) - 1): ?>
                    <div class="w-12 h-0.5 <?= $stepIndex < $currentIndex ? 'bg-green-500' : 'bg-gray-300' ?> mx-2"></div>
                <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- WORKFLOW ACTION CARDS -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- DIAGNOSIS CARD -->
            <?php if ($order['status'] === 'received'): ?>
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 border-2 border-blue-200 rounded-xl p-6">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-stethoscope text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-blue-900">Next: Device Diagnosis</h3>
                            <p class="text-blue-700 text-sm">Examine device and identify issues</p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <a href="/admin/orders/<?= $order['id'] ?>/diagnosis"
                           class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 transition-colors text-center block font-medium">
                            <i class="fas fa-play mr-2"></i>Start Diagnosis
                        </a>
                        <p class="text-xs text-blue-600">
                            <i class="fas fa-info-circle mr-1"></i>
                            Complete diagnosis to proceed with quotation
                        </p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- QUOTATION CARD -->
            <?php if ($order['status'] === 'diagnosed'): ?>
                <div class="bg-gradient-to-br from-green-50 to-green-100 border-2 border-green-200 rounded-xl p-6">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-file-invoice-dollar text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-green-900">Ready: Create Quotation</h3>
                            <p class="text-green-700 text-sm">Device diagnosed, ready for cost estimate</p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <?php
                        // Check if quotation already exists
                        $quotationModel = new \App\Models\QuotationModel();
                        $existingQuotation = $quotationModel->getQuotationByOrder($order['id']);
                        ?>

                        <?php if ($existingQuotation): ?>
                            <a href="/admin/orders/<?= $order['id'] ?>/quotation"
                               class="w-full bg-green-600 text-white py-3 px-4 rounded-lg hover:bg-green-700 transition-colors text-center block font-medium">
                                <i class="fas fa-eye mr-2"></i>View Quotation
                            </a>
                            <a href="/admin/orders/<?= $order['id'] ?>/quotation/edit"
                               class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors text-center block text-sm">
                                <i class="fas fa-edit mr-2"></i>Edit Quotation
                            </a>
                            <?php if ($existingQuotation['status'] === 'draft'): ?>
                                <button onclick="sendQuotation(<?= $existingQuotation['id'] ?>)"
                                        class="w-full bg-purple-600 text-white py-2 px-4 rounded-lg hover:bg-purple-700 transition-colors text-center block text-sm">
                                    <i class="fas fa-paper-plane mr-2"></i>Send to Customer
                                </button>
                            <?php endif; ?>
                        <?php else: ?>
                            <a href="/admin/orders/<?= $order['id'] ?>/create-quotation"
                               class="w-full bg-green-600 text-white py-3 px-4 rounded-lg hover:bg-green-700 transition-colors text-center block font-medium">
                                <i class="fas fa-plus mr-2"></i>Create Quotation
                            </a>
                        <?php endif; ?>

                        <p class="text-xs text-green-600">
                            <i class="fas fa-lightbulb mr-1"></i>
                            Diagnosis completed ✓ Ready for quotation
                        </p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- WAITING APPROVAL CARD -->
            <?php if ($order['status'] === 'waiting_approval'): ?>
                <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 border-2 border-yellow-200 rounded-xl p-6">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-yellow-500 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-clock text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-yellow-900">Waiting: Customer Approval</h3>
                            <p class="text-yellow-700 text-sm">Quotation sent, awaiting response</p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <?php
                        $quotationModel = new \App\Models\QuotationModel();
                        $quotation = $quotationModel->getQuotationByOrder($order['id']);
                        ?>

                        <a href="/admin/orders/<?= $order['id'] ?>/quotation"
                           class="w-full bg-yellow-600 text-white py-3 px-4 rounded-lg hover:bg-yellow-700 transition-colors text-center block font-medium">
                            <i class="fas fa-eye mr-2"></i>View Sent Quotation
                        </a>

                        <?php if ($quotation): ?>
                            <button onclick="sendReminder(<?= $quotation['id'] ?>)"
                                    class="w-full bg-orange-600 text-white py-2 px-4 rounded-lg hover:bg-orange-700 transition-colors text-center block text-sm">
                                <i class="fas fa-bell mr-2"></i>Send Reminder
                            </button>

                            <div class="text-xs text-yellow-700 bg-yellow-50 p-2 rounded">
                                <p><strong>Sent:</strong> <?= date('M d, Y H:i', strtotime($quotation['sent_at'])) ?></p>
                                <p><strong>Valid until:</strong> <?= date('M d, Y', strtotime($quotation['valid_until'])) ?></p>
                                <?php if (strtotime($quotation['valid_until']) < time()): ?>
                                    <p class="text-red-600 font-medium">⚠️ EXPIRED</p>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- IN PROGRESS CARD -->
            <?php if ($order['status'] === 'in_progress'): ?>
                <div class="bg-gradient-to-br from-purple-50 to-purple-100 border-2 border-purple-200 rounded-xl p-6">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-wrench text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-purple-900">Active: Repair in Progress</h3>
                            <p class="text-purple-700 text-sm">Customer approved, work ongoing</p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <a href="/admin/orders/<?= $order['id'] ?>/status"
                           class="w-full bg-purple-600 text-white py-3 px-4 rounded-lg hover:bg-purple-700 transition-colors text-center block font-medium">
                            <i class="fas fa-sync mr-2"></i>Update Progress
                        </a>

                        <a href="/admin/orders/<?= $order['id'] ?>/manage-parts"
                           class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors text-center block text-sm">
                            <i class="fas fa-cogs mr-2"></i>Manage Parts
                        </a>

                        <?php if ($order['final_cost']): ?>
                            <div class="text-xs text-purple-700 bg-purple-50 p-2 rounded">
                                <p><strong>Approved Amount:</strong> <?= format_currency($order['final_cost']) ?></p>
                                <?php if ($order['technician_name']): ?>
                                    <p><strong>Technician:</strong> <?= $order['technician_name'] ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- REST OF ORIGINAL CONTENT (Device Info, Customer Info, etc.) -->
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

                <!-- DIAGNOSIS INFORMATION (if available) -->
                <?php if (!empty($order['diagnosis_notes']) || !empty($order['recommended_actions'])): ?>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Diagnosis Information</h3>

                        <?php if (!empty($order['diagnosis_notes'])): ?>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-600">Diagnosis Notes</label>
                                <div class="bg-gray-50 p-3 rounded-lg mt-1">
                                    <p class="text-gray-900"><?= nl2br($order['diagnosis_notes']) ?></p>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($order['recommended_actions'])): ?>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-600">Recommended Actions</label>
                                <div class="bg-blue-50 p-3 rounded-lg mt-1">
                                    <p class="text-gray-900"><?= nl2br($order['recommended_actions']) ?></p>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <?php if (!empty($order['estimated_hours'])): ?>
                                <div>
                                    <label class="block text-sm font-medium text-gray-600">Estimated Hours</label>
                                    <p class="text-gray-900"><?= $order['estimated_hours'] ?> hours</p>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($order['diagnosis_date'])): ?>
                                <div>
                                    <label class="block text-sm font-medium text-gray-600">Diagnosis Date</label>
                                    <p class="text-gray-900"><?= date('M d, Y', strtotime($order['diagnosis_date'])) ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

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
                                    </tr>
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

                        <a href="/admin/orders/<?= $order['id'] ?>/receipt"
                           class="w-full bg-purple-600 text-white py-2 px-4 rounded-lg hover:bg-purple-700 transition-colors text-center block">
                            <i class="fas fa-receipt mr-2"></i>Service Receipt
                        </a>

                        <?php if (in_array($order['status'], ['completed', 'delivered'])): ?>
                            <a href="/admin/orders/<?= $order['id'] ?>/delivery-receipt"
                               class="w-full bg-indigo-600 text-white py-2 px-4 rounded-lg hover:bg-indigo-700 transition-colors text-center block">
                                <i class="fas fa-file-invoice mr-2"></i>Delivery Receipt
                            </a>
                        <?php endif; ?>

                        <?php if (!empty($order['customer_email'])): ?>
                            <button onclick="emailReceipt(<?= $order['id'] ?>)"
                                    class="w-full bg-orange-600 text-white py-2 px-4 rounded-lg hover:bg-orange-700 transition-colors">
                                <i class="fas fa-envelope mr-2"></i>Email Receipt
                            </button>
                        <?php endif; ?>
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
        function printOrder() {
            window.open('/admin/orders/<?= $order['id'] ?>/receipt?print=1', '_blank');
        }

        function emailReceipt(orderId) {
            if (confirm('Send service receipt to customer email?')) {
                window.location.href = `/admin/orders/${orderId}/email-receipt`;
            }
        }

        function sendQuotation(quotationId) {
            if (confirm('Send quotation to customer via email?')) {
                window.location.href = `/admin/orders/<?= $order['id'] ?>/quotation/${quotationId}/send`;
            }
        }

        function sendReminder(quotationId) {
            if (confirm('Send reminder email to customer?')) {
                fetch(`/admin/quotations/${quotationId}/send-reminder`)
                    .then(response => {
                        if (response.ok) {
                            alert('Reminder sent successfully!');
                            location.reload();
                        } else {
                            alert('Failed to send reminder');
                        }
                    })
                    .catch(error => {
                        alert('Error sending reminder');
                    });
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

            // Q for quotation (if applicable)
            if (e.key === 'q' && !e.ctrlKey && !e.metaKey) {
                const activeElement = document.activeElement;
                if (activeElement.tagName !== 'INPUT' && activeElement.tagName !== 'TEXTAREA') {
                    const status = '<?= $order['status'] ?>';
                    if (status === 'diagnosed') {
                        window.location.href = '/admin/orders/<?= $order['id'] ?>/create-quotation';
                    } else if (status === 'waiting_approval') {
                        window.location.href = '/admin/orders/<?= $order['id'] ?>/quotation';
                    }
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

        // Auto-refresh for orders in progress (every 5 minutes)
        <?php if (in_array($order['status'], ['waiting_approval', 'in_progress'])): ?>
        setInterval(function() {
            if (!document.hidden) {
                // Check for status updates without full page reload
                fetch(`/admin/api/orders/<?= $order['id'] ?>/status`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.status !== '<?= $order['status'] ?>') {
                            // Status changed, show notification and offer to reload
                            if (confirm('Order status has been updated. Reload page to see changes?')) {
                                location.reload();
                            }
                        }
                    })
                    .catch(error => {
                        // Silently fail - don't disturb user
                    });
            }
        }, 300000); // 5 minutes
        <?php endif; ?>

        // Progress bar animation on load
        document.addEventListener('DOMContentLoaded', function() {
            const progressSteps = document.querySelectorAll('.w-10.h-10.rounded-full');
            progressSteps.forEach((step, index) => {
                step.style.opacity = '0';
                step.style.transform = 'scale(0.8)';

                setTimeout(() => {
                    step.style.transition = 'all 0.3s ease';
                    step.style.opacity = '1';
                    step.style.transform = 'scale(1)';
                }, index * 100);
            });
        });
    </script>
<?= $this->endSection() ?>
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
            </div>

            <!-- Order Status & Actions -->
            <div class="space-y-6">
                <!-- Status Card -->
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
                                <span class="font-medium">Rp <?= number_format($order['estimated_cost'], 0, ',', '.') ?></span>
                            </div>
                        <?php endif; ?>

                        <?php if ($order['final_cost'] > 0): ?>
                            <div class="flex justify-between border-t pt-3">
                                <span class="text-gray-600 font-medium">Final Cost</span>
                                <span class="font-bold text-lg">Rp <?= number_format($order['final_cost'], 0, ',', '.') ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
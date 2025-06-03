<?= $this->extend('admin/layout/main') ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Order</h1>
            <p class="text-gray-600">Order #<?= $order['order_number'] ?></p>
        </div>
        <a href="/admin/orders" class="text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left mr-2"></i>Back to Orders
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <?php if (session()->getFlashdata('errors')): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                <ul class="list-disc list-inside space-y-1">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="/admin/orders/<?= $order['id'] ?>" method="POST" class="space-y-6">
            <?= csrf_field() ?>
            <input type="hidden" name="_method" value="PUT">

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Customer Information -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-800">Customer Information</h3>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Customer *</label>
                        <select name="customer_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Select Customer</option>
                            <?php foreach ($customers as $customer): ?>
                                <option value="<?= $customer['id'] ?>" <?= $order['customer_id'] == $customer['id'] ? 'selected' : '' ?>>
                                    <?= $customer['full_name'] ?> - <?= $customer['phone'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Device Information -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-800">Device Information</h3>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Device Type *</label>
                        <select name="device_type_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Select Device Type</option>
                            <?php foreach ($device_types as $type): ?>
                                <option value="<?= $type['id'] ?>" <?= $order['device_type_id'] == $type['id'] ? 'selected' : '' ?>>
                                    <?= $type['name'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Brand *</label>
                            <input type="text" name="device_brand" value="<?= $order['device_brand'] ?>" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Model *</label>
                            <input type="text" name="device_model" value="<?= $order['device_model'] ?>" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Serial Number</label>
                        <input type="text" name="device_serial" value="<?= $order['device_serial'] ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
            </div>

            <!-- Problem Description -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Problem Description *</label>
                <textarea name="problem_description" rows="4" required
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                          placeholder="Describe the problem with the device..."><?= $order['problem_description'] ?></textarea>
            </div>

            <!-- Accessories -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Accessories</label>
                <textarea name="accessories" rows="2"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                          placeholder="List any accessories (charger, mouse, keyboard, etc.)"><?= $order['accessories'] ?></textarea>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Priority -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Priority *</label>
                    <select name="priority" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="low" <?= $order['priority'] == 'low' ? 'selected' : '' ?>>Low</option>
                        <option value="normal" <?= $order['priority'] == 'normal' ? 'selected' : '' ?>>Normal</option>
                        <option value="high" <?= $order['priority'] == 'high' ? 'selected' : '' ?>>High</option>
                        <option value="urgent" <?= $order['priority'] == 'urgent' ? 'selected' : '' ?>>Urgent</option>
                    </select>
                </div>

                <!-- Technician -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Assign Technician</label>
                    <select name="technician_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Not Assigned</option>
                        <?php foreach ($technicians as $tech): ?>
                            <option value="<?= $tech['id'] ?>" <?= $order['technician_id'] == $tech['id'] ? 'selected' : '' ?>>
                                <?= $tech['full_name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="received" <?= $order['status'] == 'received' ? 'selected' : '' ?>>Received</option>
                        <option value="diagnosed" <?= $order['status'] == 'diagnosed' ? 'selected' : '' ?>>Diagnosed</option>
                        <option value="waiting_approval" <?= $order['status'] == 'waiting_approval' ? 'selected' : '' ?>>Waiting Approval</option>
                        <option value="in_progress" <?= $order['status'] == 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                        <option value="waiting_parts" <?= $order['status'] == 'waiting_parts' ? 'selected' : '' ?>>Waiting Parts</option>
                        <option value="completed" <?= $order['status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
                        <option value="delivered" <?= $order['status'] == 'delivered' ? 'selected' : '' ?>>Delivered</option>
                        <option value="cancelled" <?= $order['status'] == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                    </select>
                </div>
            </div>

            <!-- Cost Information -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Estimated Cost</label>
                    <input type="number" name="estimated_cost" value="<?= $order['estimated_cost'] ?>" step="0.01"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Final Cost</label>
                    <input type="number" name="final_cost" value="<?= $order['final_cost'] ?>" step="0.01"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Additional Notes</label>
                <textarea name="notes" rows="3"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                          placeholder="Any additional notes or instructions..."><?= $order['notes'] ?></textarea>
            </div>

            <!-- Actions -->
            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="/admin/orders" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>Update Order
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
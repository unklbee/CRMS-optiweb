<?= $this->extend('admin/layout/main') ?>

<?= $this->section('content') ?>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Create New Order</h1>
                <p class="text-gray-600">Add a new repair order</p>
            </div>
            <a href="/admin/orders" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left mr-2"></i>Back to Orders
            </a>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <form action="/admin/orders" method="POST" class="space-y-6">
                <?= csrf_field() ?>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Customer Information -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-800">Customer Information</h3>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Customer *</label>
                            <select name="customer_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select Customer</option>
                                <?php foreach ($customers as $customer): ?>
                                    <option value="<?= $customer['id'] ?>" <?= old('customer_id') == $customer['id'] ? 'selected' : '' ?>>
                                        <?= $customer['full_name'] ?> - <?= $customer['phone'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($errors['customer_id'])): ?>
                                <p class="text-red-500 text-sm mt-1"><?= $errors['customer_id'] ?></p>
                            <?php endif; ?>
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
                                    <option value="<?= $type['id'] ?>" <?= old('device_type_id') == $type['id'] ? 'selected' : '' ?>>
                                        <?= $type['name'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($errors['device_type_id'])): ?>
                                <p class="text-red-500 text-sm mt-1"><?= $errors['device_type_id'] ?></p>
                            <?php endif; ?>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Brand *</label>
                                <input type="text" name="device_brand" value="<?= old('device_brand') ?>" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <?php if (isset($errors['device_brand'])): ?>
                                    <p class="text-red-500 text-sm mt-1"><?= $errors['device_brand'] ?></p>
                                <?php endif; ?>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Model *</label>
                                <input type="text" name="device_model" value="<?= old('device_model') ?>" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <?php if (isset($errors['device_model'])): ?>
                                    <p class="text-red-500 text-sm mt-1"><?= $errors['device_model'] ?></p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Serial Number</label>
                            <input type="text" name="device_serial" value="<?= old('device_serial') ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                </div>

                <!-- Problem Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Problem Description *</label>
                    <textarea name="problem_description" rows="4" required
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Describe the problem with the device..."><?= old('problem_description') ?></textarea>
                    <?php if (isset($errors['problem_description'])): ?>
                        <p class="text-red-500 text-sm mt-1"><?= $errors['problem_description'] ?></p>
                    <?php endif; ?>
                </div>

                <!-- Accessories -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Accessories</label>
                    <textarea name="accessories" rows="2"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="List any accessories (charger, mouse, keyboard, etc.)"><?= old('accessories') ?></textarea>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Priority -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Priority *</label>
                        <select name="priority" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="normal" <?= old('priority') == 'medium' ? 'selected' : '' ?>>Medium</option>
                            <option value="high" <?= old('priority') == 'high' ? 'selected' : '' ?>>High</option>
                            <option value="urgent" <?= old('priority') == 'urgent' ? 'selected' : '' ?>>Urgent</option>
                            <option value="low" <?= old('priority') == 'low' ? 'selected' : '' ?>>Low</option>
                        </select>
                    </div>

                    <!-- Technician -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Assign Technician</label>
                        <select name="technician_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Assign later</option>
                            <?php foreach ($technicians as $tech): ?>
                                <option value="<?= $tech['id'] ?>" <?= old('technician_id') == $tech['id'] ? 'selected' : '' ?>>
                                    <?= $tech['full_name'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Notes -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Additional Notes</label>
                    <textarea name="notes" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Any additional notes or instructions..."><?= old('notes') ?></textarea>
                </div>

                <!-- Receipt Options - tambahkan sebelum Actions -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="font-medium text-blue-800 mb-3">
                        <i class="fas fa-receipt mr-2"></i>Receipt Options
                    </h4>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="checkbox" name="view_receipt" value="1" checked
                                   class="mr-2 text-blue-600 rounded focus:ring-blue-500">
                            <span class="text-sm text-blue-700">Generate service receipt after creating order</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="print_receipt" value="1"
                                   class="mr-2 text-blue-600 rounded focus:ring-blue-500">
                            <span class="text-sm text-blue-700">Print receipt immediately (for customer copy)</span>
                        </label>
                    </div>
                    <p class="text-xs text-blue-600 mt-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        Service receipt serves as proof that device has been received for repair
                    </p>
                </div>

                <!-- Actions -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="/admin/orders" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>Create Order
                    </button>
                </div>
            </form>
        </div>
    </div>
<?= $this->endSection() ?>
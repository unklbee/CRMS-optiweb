<?= $this->extend('frontend/layout/main') ?>

<?= $this->section('content') ?>
    <div class="container mx-auto px-4 py-12">
        <?= breadcrumb($breadcrumb ?? []) ?>

        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-4">Book Repair Service</h1>
                <p class="text-gray-600">Fill out the form below to schedule your device repair</p>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-8">
                <?php if (session()->getFlashdata('errors')): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                        <ul class="list-disc list-inside space-y-1">
                            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                <li><?= $error ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="/book-service" method="POST" class="space-y-6">
                    <?= csrf_field() ?>

                    <!-- Customer Information -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Customer Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                                <input type="text" name="customer_name" value="<?= old('customer_name') ?>" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number *</label>
                                <input type="tel" name="customer_phone" value="<?= old('customer_phone') ?>" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                                <input type="email" name="customer_email" value="<?= old('customer_email') ?>"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                                <input type="text" name="customer_address" value="<?= old('customer_address') ?>"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>
                    </div>

                    <!-- Device Information -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Device Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Device Type *</label>
                                <select name="device_type_id" required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Select Device Type</option>
                                    <?php foreach ($device_types as $type): ?>
                                        <option value="<?= $type['id'] ?>" <?= old('device_type_id') == $type['id'] ? 'selected' : '' ?>>
                                            <?= $type['name'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Service Category *</label>
                                <select name="service_category_id" required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Select Service Type</option>
                                    <?php foreach ($service_categories as $category): ?>
                                        <option value="<?= $category['id'] ?>" <?= old('service_category_id') == $category['id'] ? 'selected' : '' ?>>
                                            <?= $category['name'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Brand *</label>
                                <input type="text" name="device_brand" value="<?= old('device_brand') ?>" required
                                       placeholder="e.g., Apple, HP, Dell, Samsung"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Model *</label>
                                <input type="text" name="device_model" value="<?= old('device_model') ?>" required
                                       placeholder="e.g., iPhone 13, MacBook Pro, ThinkPad"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Serial Number
                                    (Optional)</label>
                                <input type="text" name="device_serial" value="<?= old('device_serial') ?>"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>
                    </div>

                    <!-- Problem Description -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Problem Description *</label>
                        <textarea name="problem_description" rows="4" required
                                  placeholder="Please describe the issue with your device in detail..."
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"><?= old('problem_description') ?></textarea>
                    </div>

                    <!-- Accessories -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Accessories (Optional)</label>
                        <textarea name="accessories" rows="2"
                                  placeholder="List any accessories you're bringing (charger, mouse, keyboard, etc.)"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"><?= old('accessories') ?></textarea>
                    </div>

                    <!-- Priority -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                        <select name="priority"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="normal" <?= old('priority') == 'normal' ? 'selected' : '' ?>>Normal (3-5
                                business days)
                            </option>
                            <option value="high" <?= old('priority') == 'high' ? 'selected' : '' ?>>High (1-2 business
                                days)
                            </option>
                            <option value="urgent" <?= old('priority') == 'urgent' ? 'selected' : '' ?>>Urgent (Same
                                day)
                            </option>
                        </select>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                        <a href="/"
                           class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                            Cancel
                        </a>
                        <button type="submit"
                                class="px-8 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                            <i class="fas fa-paper-plane mr-2"></i>Submit Order
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
<?= $this->extend('admin/layout/main') ?>

<?= $this->section('content') ?>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit Service</h1>
                <p class="text-gray-600"><?= $service['name'] ?></p>
            </div>
            <div class="flex space-x-2">
                <a href="/admin/services/<?= $service['id'] ?>" class="text-gray-600 hover:text-gray-900 px-4 py-2">
                    <i class="fas fa-eye mr-2"></i>View Details
                </a>
                <a href="/admin/services" class="text-gray-600 hover:text-gray-900 px-4 py-2">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Services
                </a>
            </div>
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

            <form action="/admin/services/<?= $service['id'] ?>" method="POST" class="space-y-6">
                <?= csrf_field() ?>
                <input type="hidden" name="_method" value="PUT">

                <!-- Basic Information -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Service Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Service Name *</label>
                            <input type="text" name="name" value="<?= old('name', $service['name']) ?>" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                            <select name="category_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>" <?= $service['category_id'] == $category['id'] ? 'selected' : '' ?>>
                                        <?= $category['name'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                            <select name="status" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="active" <?= $service['status'] == 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= $service['status'] == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="4"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"><?= old('description', $service['description']) ?></textarea>
                </div>

                <!-- Pricing & Duration -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Pricing & Duration</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Base Price (Rp) *</label>
                            <input type="number" name="base_price" value="<?= old('base_price', $service['base_price']) ?>" required min="0" step="1000"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <p class="text-xs text-gray-500 mt-1">Current: <?= format_currency($service['base_price']) ?></p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Estimated Duration (minutes) *</label>
                            <input type="number" name="estimated_duration" value="<?= old('estimated_duration', $service['estimated_duration']) ?>" required min="15" step="15"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <p class="text-xs text-gray-500 mt-1">Current: <?= $service['estimated_duration'] ?> minutes (<?= number_format($service['estimated_duration']/60, 1) ?> hours)</p>
                        </div>
                    </div>
                </div>

                <!-- Service Statistics -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Service Statistics</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-blue-600"><?= $service['orders_count'] ?? 0 ?></p>
                            <p class="text-sm text-gray-600">Total Orders</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-green-600"><?= format_currency($service['total_revenue'] ?? 0) ?></p>
                            <p class="text-sm text-gray-600">Total Revenue</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-purple-600"><?= number_format($service['avg_rating'] ?? 0, 1) ?>/5.0</p>
                            <p class="text-sm text-gray-600">Average Rating</p>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="/admin/services" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>Update Service
                    </button>
                </div>
            </form>
        </div>
    </div>
<?= $this->endSection() ?>
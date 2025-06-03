<?= $this->extend('admin/layout/main') ?>

<?= $this->section('content') ?>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Service Details</h1>
                <p class="text-gray-600"><?= $service['name'] ?></p>
            </div>
            <div class="flex space-x-2">
                <a href="/admin/services/<?= $service['id'] ?>/edit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-edit mr-2"></i>Edit Service
                </a>
                <a href="/admin/services" class="text-gray-600 hover:text-gray-900 px-4 py-2">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Services
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Service Information -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Info -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Service Information</h3>

                    <div class="space-y-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-tools text-blue-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900"><?= $service['name'] ?></p>
                                <p class="text-sm text-gray-600"><?= $service['category_name'] ?></p>
                            </div>
                        </div>

                        <?php if ($service['description']): ?>
                            <div>
                                <h4 class="font-medium text-gray-900 mb-2">Description</h4>
                                <p class="text-gray-600"><?= nl2br($service['description']) ?></p>
                            </div>
                        <?php endif; ?>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <h4 class="font-medium text-gray-900 mb-1">Base Price</h4>
                                <p class="text-2xl font-bold text-blue-600"><?= format_currency($service['base_price']) ?></p>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900 mb-1">Duration</h4>
                                <p class="text-lg font-medium text-gray-900"><?= $service['estimated_duration'] ?> minutes</p>
                                <p class="text-sm text-gray-500"><?= number_format($service['estimated_duration']/60, 1) ?> hours</p>
                            </div>
                        </div>

                        <div>
                            <h4 class="font-medium text-gray-900 mb-2">Status</h4>
                            <span class="px-3 py-1 rounded-full text-sm font-medium
                            <?= $service['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' ?>">
                            <?= ucfirst($service['status']) ?>
                        </span>
                        </div>

                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-600">Created:</span>
                                <span class="font-medium"><?= date('M d, Y', strtotime($service['created_at'])) ?></span>
                            </div>
                            <div>
                                <span class="text-gray-600">Updated:</span>
                                <span class="font-medium"><?= date('M d, Y', strtotime($service['updated_at'])) ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Orders -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">Recent Orders</h3>
                    </div>
                    <div class="p-6">
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-clipboard-list text-4xl mb-4 text-gray-300"></i>
                            <p>No recent orders for this service</p>
                            <p class="text-sm">Orders will appear here once customers book this service</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Stats -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Stats</h3>
                    <div class="space-y-4">
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
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Actions</h3>
                    <div class="space-y-3">
                        <a href="/admin/services/<?= $service['id'] ?>/edit"
                           class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors text-center block">
                            <i class="fas fa-edit mr-2"></i>Edit Service
                        </a>

                        <?php if ($service['status'] === 'active'): ?>
                            <button onclick="toggleStatus('inactive')"
                                    class="w-full bg-yellow-600 text-white py-2 px-4 rounded-lg hover:bg-yellow-700 transition-colors">
                                <i class="fas fa-pause mr-2"></i>Deactivate
                            </button>
                        <?php else: ?>
                            <button onclick="toggleStatus('active')"
                                    class="w-full bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition-colors">
                                <i class="fas fa-play mr-2"></i>Activate
                            </button>
                        <?php endif; ?>

                        <button onclick="duplicateService()"
                                class="w-full bg-purple-600 text-white py-2 px-4 rounded-lg hover:bg-purple-700 transition-colors">
                            <i class="fas fa-copy mr-2"></i>Duplicate
                        </button>

                        <button onclick="deleteService()"
                                class="w-full bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition-colors">
                            <i class="fas fa-trash mr-2"></i>Delete Service
                        </button>
                    </div>
                </div>

                <!-- Category Info -->
                <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Category</h3>
                    <p class="text-blue-800 font-medium"><?= $service['category_name'] ?></p>
                    <a href="/admin/service-categories" class="text-blue-600 hover:text-blue-800 text-sm mt-2 inline-block">
                        Manage Categories <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleStatus(newStatus) {
            const action = newStatus === 'active' ? 'activate' : 'deactivate';
            if (confirm(`Are you sure you want to ${action} this service?`)) {
                // Create form and submit
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/admin/services/<?= $service['id'] ?>';

                form.innerHTML = `
            <?= csrf_field() ?>
            <input type="hidden" name="_method" value="PUT">
            <input type="hidden" name="status" value="${newStatus}">
            <input type="hidden" name="category_id" value="<?= $service['category_id'] ?>">
            <input type="hidden" name="name" value="<?= addslashes($service['name']) ?>">
            <input type="hidden" name="base_price" value="<?= $service['base_price'] ?>">
            <input type="hidden" name="estimated_duration" value="<?= $service['estimated_duration'] ?>">
            <input type="hidden" name="description" value="<?= addslashes($service['description']) ?>">
        `;

                document.body.appendChild(form);
                form.submit();
            }
        }

        function duplicateService() {
            if (confirm('Create a copy of this service?')) {
                window.location.href = '/admin/services/new?duplicate=<?= $service['id'] ?>';
            }
        }

        function deleteService() {
            if (confirm('Are you sure you want to delete this service? This action cannot be undone.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/admin/services/<?= $service['id'] ?>';
                form.innerHTML = `
            <?= csrf_field() ?>
            <input type="hidden" name="_method" value="DELETE">
        `;
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
<?= $this->endSection() ?>
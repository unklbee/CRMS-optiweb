<?= $this->extend('admin/layout/main') ?>

<?= $this->section('content') ?>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Category Details</h1>
                <p class="text-gray-600"><?= $category['name'] ?></p>
            </div>
            <div class="flex space-x-2">
                <a href="/admin/service-categories/<?= $category['id'] ?>/edit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-edit mr-2"></i>Edit Category
                </a>
                <a href="/admin/service-categories" class="text-gray-600 hover:text-gray-900 px-4 py-2">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Categories
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Category Information -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Info -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Category Information</h3>

                    <div class="space-y-4">
                        <div class="flex items-center space-x-4">
                            <?php if ($category['icon']): ?>
                                <div class="w-16 h-16 bg-blue-100 rounded-xl flex items-center justify-center">
                                    <i class="<?= $category['icon'] ?> text-blue-600 text-2xl"></i>
                                </div>
                            <?php else: ?>
                                <div class="w-16 h-16 bg-gray-100 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-folder text-gray-600 text-2xl"></i>
                                </div>
                            <?php endif; ?>
                            <div>
                                <h4 class="text-xl font-semibold text-gray-900"><?= $category['name'] ?></h4>
                                <p class="text-gray-600">Service Category</p>
                            </div>
                        </div>

                        <?php if ($category['description']): ?>
                            <div>
                                <h4 class="font-medium text-gray-900 mb-2">Description</h4>
                                <p class="text-gray-600 leading-relaxed"><?= nl2br($category['description']) ?></p>
                            </div>
                        <?php endif; ?>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <h4 class="font-medium text-gray-900 mb-1">Status</h4>
                                <span class="px-3 py-1 rounded-full text-sm font-medium
                                <?= $category['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' ?>">
                                <?= ucfirst($category['status']) ?>
                            </span>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900 mb-1">Services Count</h4>
                                <p class="text-2xl font-bold text-blue-600"><?= count($services) ?></p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-600">Created:</span>
                                <span class="font-medium"><?= date('M d, Y H:i', strtotime($category['created_at'])) ?></span>
                            </div>
                            <div>
                                <span class="text-gray-600">Updated:</span>
                                <span class="font-medium"><?= date('M d, Y H:i', strtotime($category['updated_at'])) ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Services in Category -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-800">Services in this Category</h3>
                            <a href="/admin/services/new?category=<?= $category['id'] ?>" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                <i class="fas fa-plus mr-1"></i>Add Service
                            </a>
                        </div>
                    </div>
                    <div class="p-6">
                        <?php if (!empty($services)): ?>
                            <div class="space-y-4">
                                <?php foreach ($services as $service): ?>
                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-3">
                                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                                    <i class="fas fa-cog text-blue-600"></i>
                                                </div>
                                                <div>
                                                    <h4 class="font-medium text-gray-900"><?= $service['name'] ?></h4>
                                                    <p class="text-sm text-gray-600"><?= truncate_text($service['description'] ?? '', 60) ?></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-4">
                                            <div class="text-right">
                                                <p class="font-medium text-gray-900"><?= format_currency($service['base_price']) ?></p>
                                                <p class="text-sm text-gray-500"><?= $service['estimated_duration'] ?> min</p>
                                            </div>
                                            <span class="px-2 py-1 text-xs rounded-full <?= $service['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' ?>">
                                                <?= ucfirst($service['status']) ?>
                                            </span>
                                            <a href="/admin/services/<?= $service['id'] ?>" class="text-blue-600 hover:text-blue-800 text-sm">
                                                View <i class="fas fa-arrow-right ml-1"></i>
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-8">
                                <i class="fas fa-cog text-4xl text-gray-300 mb-4"></i>
                                <h4 class="text-lg font-medium text-gray-900 mb-2">No Services Yet</h4>
                                <p class="text-gray-600 mb-4">This category doesn't have any services yet.</p>
                                <a href="/admin/services/new?category=<?= $category['id'] ?>" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                    <i class="fas fa-plus mr-2"></i>Add First Service
                                </a>
                            </div>
                        <?php endif; ?>
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
                            <p class="text-2xl font-bold text-blue-600"><?= count($services) ?></p>
                            <p class="text-sm text-gray-600">Total Services</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-green-600"><?= count(array_filter($services, function($s) { return $s['status'] === 'active'; })) ?></p>
                            <p class="text-sm text-gray-600">Active Services</p>
                        </div>
                        <div class="text-center">
                            <?php
                            $avgPrice = count($services) > 0 ? array_sum(array_column($services, 'base_price')) / count($services) : 0;
                            ?>
                            <p class="text-2xl font-bold text-purple-600"><?= format_currency($avgPrice) ?></p>
                            <p class="text-sm text-gray-600">Avg Service Price</p>
                        </div>
                        <div class="text-center">
                            <?php
                            $avgDuration = count($services) > 0 ? array_sum(array_column($services, 'estimated_duration')) / count($services) : 0;
                            ?>
                            <p class="text-2xl font-bold text-orange-600"><?= round($avgDuration) ?> min</p>
                            <p class="text-sm text-gray-600">Avg Duration</p>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Actions</h3>
                    <div class="space-y-3">
                        <a href="/admin/service-categories/<?= $category['id'] ?>/edit"
                           class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors text-center block">
                            <i class="fas fa-edit mr-2"></i>Edit Category
                        </a>

                        <a href="/admin/services/new?category=<?= $category['id'] ?>"
                           class="w-full bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition-colors text-center block">
                            <i class="fas fa-plus mr-2"></i>Add Service
                        </a>

                        <?php if ($category['status'] === 'active'): ?>
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

                        <button onclick="duplicateCategory()"
                                class="w-full bg-purple-600 text-white py-2 px-4 rounded-lg hover:bg-purple-700 transition-colors">
                            <i class="fas fa-copy mr-2"></i>Duplicate
                        </button>

                        <button onclick="deleteCategory()"
                                class="w-full bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition-colors">
                            <i class="fas fa-trash mr-2"></i>Delete Category
                        </button>
                    </div>
                </div>

                <!-- Category Usage -->
                <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Category Usage</h3>
                    <p class="text-blue-800 text-sm mb-3">This category is used across your repair services.</p>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Services:</span>
                            <span class="font-medium"><?= count($services) ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Status:</span>
                            <span class="font-medium <?= $category['status'] === 'active' ? 'text-green-600' : 'text-gray-600' ?>">
                                <?= ucfirst($category['status']) ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleStatus(newStatus) {
            const action = newStatus === 'active' ? 'activate' : 'deactivate';
            if (confirm(`Are you sure you want to ${action} this category?`)) {
                // Create form and submit
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/admin/service-categories/<?= $category['id'] ?>';

                form.innerHTML = `
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" name="status" value="${newStatus}">
                    <input type="hidden" name="name" value="<?= addslashes($category['name']) ?>">
                    <input type="hidden" name="description" value="<?= addslashes($category['description']) ?>">
                    <input type="hidden" name="icon" value="<?= $category['icon'] ?>">
                `;

                document.body.appendChild(form);
                form.submit();
            }
        }

        function duplicateCategory() {
            if (confirm('Create a copy of this category?')) {
                window.location.href = '/admin/service-categories/new?duplicate=<?= $category['id'] ?>';
            }
        }

        function deleteCategory() {
            const servicesCount = <?= count($services) ?>;
            if (servicesCount > 0) {
                alert('Cannot delete category that has services. Please move or delete services first.');
                return;
            }

            if (confirm('Are you sure you want to delete this category? This action cannot be undone.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/admin/service-categories/<?= $category['id'] ?>';
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
                    window.location.href = '/admin/service-categories/<?= $category['id'] ?>/edit';
                }
            }
        });
    </script>
<?= $this->endSection() ?>
<?= $this->extend('admin/layout/main') ?>

<?= $this->section('content') ?>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Services</h1>
                <p class="text-gray-600">Manage repair services and pricing</p>
            </div>
            <div class="flex space-x-2">
                <a href="/admin/service-categories" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-folder mr-2"></i>Manage Categories
                </a>
                <a href="/admin/services/new" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Add Service
                </a>
            </div>
        </div>

        <!-- Services Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if (!empty($services)): ?>
                <?php foreach ($services as $service): ?>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="bg-blue-100 w-12 h-12 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-tools text-blue-600 text-xl"></i>
                                </div>
                                <span class="px-2 py-1 text-xs rounded-full <?= $service['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' ?>">
                                <?= ucfirst($service['status']) ?>
                            </span>
                            </div>

                            <h3 class="text-lg font-semibold text-gray-800 mb-2"><?= $service['name'] ?></h3>
                            <p class="text-sm text-gray-600 mb-3"><?= $service['category_name'] ?? 'No Category' ?></p>
                            <p class="text-gray-600 mb-4 text-sm"><?= truncate_text($service['description'], 80) ?></p>

                            <div class="flex justify-between items-center mb-4">
                                <div>
                                    <span class="text-sm text-gray-500">Base Price</span>
                                    <p class="text-xl font-bold text-blue-600"><?= format_currency($service['base_price']) ?></p>
                                </div>
                                <div class="text-right">
                                    <span class="text-sm text-gray-500">Duration</span>
                                    <p class="text-sm font-medium"><?= $service['estimated_duration'] ?> min</p>
                                </div>
                            </div>

                            <div class="flex space-x-2">
                                <a href="/admin/services/<?= $service['id'] ?>/edit"
                                   class="flex-1 bg-blue-50 text-blue-700 py-2 px-3 rounded-lg hover:bg-blue-100 transition-colors text-center text-sm">
                                    <i class="fas fa-edit mr-1"></i>Edit
                                </a>
                                <a href="/admin/services/<?= $service['id'] ?>"
                                   class="flex-1 bg-gray-50 text-gray-700 py-2 px-3 rounded-lg hover:bg-gray-100 transition-colors text-center text-sm">
                                    <i class="fas fa-eye mr-1"></i>View
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-full text-center py-12">
                    <i class="fas fa-cog text-4xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">No Services Found</h3>
                    <p class="text-gray-600 mb-6">Get started by adding your first repair service.</p>
                    <a href="/admin/services/new" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Add First Service
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?= $this->endSection() ?>
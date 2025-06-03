<?= $this->extend('admin/layout/main') ?>

<?= $this->section('content') ?>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Parts & Inventory</h1>
                <p class="text-gray-600">Manage spare parts and stock levels</p>
            </div>
            <a href="/admin/parts/new" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-plus mr-2"></i>Add Part
            </a>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Total Parts</p>
                        <p class="text-2xl font-bold text-gray-800"><?= count($parts ?? []) ?></p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-boxes text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Low Stock</p>
                        <p class="text-2xl font-bold text-red-600"><?= count(array_filter($parts ?? [], function($part) { return $part['stock_quantity'] <= $part['min_stock']; })) ?></p>
                    </div>
                    <div class="bg-red-100 p-3 rounded-full">
                        <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Total Value</p>
                        <p class="text-2xl font-bold text-green-600"><?= format_currency(array_sum(array_map(function($part) { return $part['stock_quantity'] * $part['cost_price']; }, $parts ?? []))) ?></p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-dollar-sign text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Categories</p>
                        <p class="text-2xl font-bold text-purple-600"><?= count(array_unique(array_column($parts ?? [], 'category'))) ?></p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-full">
                        <i class="fas fa-tags text-purple-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Parts Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Part</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pricing</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (!empty($parts)): ?>
                        <?php foreach ($parts as $part): ?>
                            <tr class="hover:bg-gray-50 <?= $part['stock_quantity'] <= $part['min_stock'] ? 'bg-red-50' : '' ?>">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900"><?= $part['name'] ?></div>
                                        <div class="text-sm text-gray-500"><?= $part['part_number'] ?></div>
                                        <?php if ($part['brand']): ?>
                                            <div class="text-xs text-gray-400"><?= $part['brand'] ?></div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        <?= $part['category'] ?: 'Uncategorized' ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <span class="<?= $part['stock_quantity'] <= $part['min_stock'] ? 'text-red-600 font-bold' : '' ?>">
                                            <?= $part['stock_quantity'] ?>
                                        </span>
                                        / <?= $part['min_stock'] ?> min
                                    </div>
                                    <?php if ($part['stock_quantity'] <= $part['min_stock']): ?>
                                        <div class="text-xs text-red-600 font-medium">Low Stock!</div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <div>Cost: <?= format_currency($part['cost_price']) ?></div>
                                        <div>Sell: <?= format_currency($part['selling_price']) ?></div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= $part['location'] ?: '-' ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                    <a href="/admin/parts/<?= $part['id'] ?>" class="text-blue-600 hover:text-blue-900">View</a>
                                    <a href="/admin/parts/<?= $part['id'] ?>/edit" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-boxes text-4xl mb-4 text-gray-300"></i>
                                <p>No parts in inventory</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
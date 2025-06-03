<?= $this->extend('admin/layout/main') ?>

<?= $this->section('content') ?>
    <div class="space-y-6">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Total Orders</p>
                        <p class="text-3xl font-bold text-gray-800"><?= $stats['total_orders'] ?></p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-clipboard-list text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Pending Orders</p>
                        <p class="text-3xl font-bold text-yellow-600"><?= $stats['pending_orders'] ?></p>
                    </div>
                    <div class="bg-yellow-100 p-3 rounded-full">
                        <i class="fas fa-clock text-yellow-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">In Progress</p>
                        <p class="text-3xl font-bold text-blue-600"><?= $stats['in_progress_orders'] ?></p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-wrench text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Completed</p>
                        <p class="text-3xl font-bold text-green-600"><?= $stats['completed_orders'] ?></p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Orders -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-800">Recent Orders</h3>
                        <a href="/admin/orders" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            View All <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
                <div class="p-6">
                    <?php if (!empty($recent_orders)): ?>
                        <div class="space-y-4">
                            <?php foreach ($recent_orders as $order): ?>
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3">
                                            <span class="font-medium text-gray-800"><?= $order['order_number'] ?></span>
                                            <span class="px-2 py-1 text-xs rounded-full
                                            <?= $order['status'] === 'completed' ? 'bg-green-100 text-green-800' :
                                                ($order['status'] === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') ?>">
                                            <?= ucfirst(str_replace('_', ' ', $order['status'])) ?>
                                        </span>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1"><?= $order['customer_name'] ?></p>
                                        <p class="text-xs text-gray-500"><?= $order['device_type_name'] ?> - <?= $order['device_brand'] ?></p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm text-gray-600"><?= date('M d, Y', strtotime($order['created_at'])) ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-gray-500 text-center py-8">No recent orders</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Low Stock Parts -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-800">Low Stock Alert</h3>
                        <a href="/admin/parts" class="text-red-600 hover:text-red-800 text-sm font-medium">
                            View All <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
                <div class="p-6">
                    <?php if (!empty($low_stock_parts)): ?>
                        <div class="space-y-4">
                            <?php foreach ($low_stock_parts as $part): ?>
                                <div class="flex items-center justify-between p-4 bg-red-50 rounded-lg border border-red-200">
                                    <div>
                                        <p class="font-medium text-gray-800"><?= $part['name'] ?></p>
                                        <p class="text-sm text-gray-600">
                                            Stock: <?= $part['stock_quantity'] ?> / Min: <?= $part['min_stock'] ?>
                                        </p>
                                    </div>
                                    <div class="text-red-600">
                                        <i class="fas fa-exclamation-triangle"></i>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-8">
                            <i class="fas fa-check-circle text-green-500 text-3xl mb-2"></i>
                            <p class="text-gray-500">All parts are well stocked</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
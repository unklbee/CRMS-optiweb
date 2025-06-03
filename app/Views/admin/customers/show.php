<?= $this->extend('admin/layout/main') ?>

<?= $this->section('content') ?>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Customer Details</h1>
                <p class="text-gray-600"><?= $customer['full_name'] ?></p>
            </div>
            <div class="flex space-x-2">
                <a href="/admin/customers/<?= $customer['id'] ?>/edit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-edit mr-2"></i>Edit Customer
                </a>
                <a href="/admin/customers" class="text-gray-600 hover:text-gray-900 px-4 py-2">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Customers
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Customer Information -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Customer Information</h3>

                    <div class="space-y-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-blue-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900"><?= $customer['full_name'] ?></p>
                                <?php if ($customer['username']): ?>
                                    <p class="text-sm text-gray-600">@<?= $customer['username'] ?></p>
                                <?php else: ?>
                                    <p class="text-sm text-gray-500">Guest Customer</p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-phone text-gray-400 w-5"></i>
                                <span class="text-gray-900"><?= $customer['phone'] ?></span>
                            </div>

                            <?php if ($customer['email']): ?>
                                <div class="flex items-center space-x-3">
                                    <i class="fas fa-envelope text-gray-400 w-5"></i>
                                    <span class="text-gray-900"><?= $customer['email'] ?></span>
                                </div>
                            <?php endif; ?>

                            <?php if ($customer['address']): ?>
                                <div class="flex items-start space-x-3">
                                    <i class="fas fa-map-marker-alt text-gray-400 w-5 mt-1"></i>
                                    <span class="text-gray-900"><?= nl2br($customer['address']) ?></span>
                                </div>
                            <?php endif; ?>

                            <div class="flex items-center space-x-3">
                                <i class="fas fa-calendar text-gray-400 w-5"></i>
                                <span class="text-gray-600">Customer since <?= date('M d, Y', strtotime($customer['created_at'])) ?></span>
                            </div>
                        </div>

                        <?php if ($customer['notes']): ?>
                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <h4 class="font-medium text-gray-900 mb-2">Notes</h4>
                                <p class="text-gray-600 text-sm"><?= nl2br($customer['notes']) ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Orders History -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-800">Repair History</h3>
                            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                            <?= count($orders) ?> Orders
                        </span>
                        </div>
                    </div>

                    <div class="p-6">
                        <?php if (!empty($orders)): ?>
                            <div class="space-y-4">
                                <?php foreach ($orders as $order): ?>
                                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-sm transition-shadow">
                                        <div class="flex items-center justify-between mb-3">
                                            <div class="flex items-center space-x-3">
                                                <span class="font-medium text-gray-900"><?= $order['order_number'] ?></span>
                                                <?= status_badge($order['status'], 'order') ?>
                                                <?= status_badge($order['priority'], 'priority') ?>
                                            </div>
                                            <span class="text-sm text-gray-500"><?= date('M d, Y', strtotime($order['created_at'])) ?></span>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                            <div>
                                                <p class="text-gray-600">Device: <span class="text-gray-900"><?= $order['device_type_name'] ?></span></p>
                                                <p class="text-gray-600">Brand: <span class="text-gray-900"><?= $order['device_brand'] ?> <?= $order['device_model'] ?></span></p>
                                            </div>
                                            <div>
                                                <?php if ($order['technician_name']): ?>
                                                    <p class="text-gray-600">Technician: <span class="text-gray-900"><?= $order['technician_name'] ?></span></p>
                                                <?php endif; ?>
                                                <?php if ($order['final_cost'] > 0): ?>
                                                    <p class="text-gray-600">Cost: <span class="text-gray-900 font-medium"><?= format_currency($order['final_cost']) ?></span></p>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <p class="text-gray-600 mt-2 text-sm"><?= truncate_text($order['problem_description'], 100) ?></p>

                                        <div class="mt-3 flex justify-end">
                                            <a href="/admin/orders/<?= $order['id'] ?>" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                View Details <i class="fas fa-arrow-right ml-1"></i>
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-8">
                                <i class="fas fa-clipboard-list text-4xl text-gray-300 mb-4"></i>
                                <h4 class="text-lg font-medium text-gray-900 mb-2">No Repair History</h4>
                                <p class="text-gray-600">This customer hasn't placed any repair orders yet.</p>
                                <div class="mt-4">
                                    <a href="/admin/orders/new?customer=<?= $customer['id'] ?>" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                        <i class="fas fa-plus mr-2"></i>Create First Order
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
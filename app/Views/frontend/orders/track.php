<?= $this->extend('frontend/layout/main') ?>

<?= $this->section('content') ?>
    <div class="container mx-auto px-4 py-12">
        <?= breadcrumb($breadcrumb ?? []) ?>

        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-4">Track Your Order</h1>
                <p class="text-gray-600">Enter your order number to check repair status</p>
            </div>

            <!-- Search Form -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                <form action="/track-order" method="POST" class="flex flex-col sm:flex-row gap-4">
                    <?= csrf_field() ?>
                    <div class="flex-1">
                        <input type="text" name="order_number" placeholder="Enter your order number (e.g., ORD20241201001)"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               value="<?= $order_number ?>" required>
                    </div>
                    <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                        <i class="fas fa-search mr-2"></i>Track Order
                    </button>
                </form>
            </div>

            <?php if ($order): ?>
                <!-- Order Details -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white p-6">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
                            <div>
                                <h2 class="text-2xl font-bold mb-2">Order #<?= $order['order_number'] ?></h2>
                                <p class="text-blue-100">Submitted on <?= date('M d, Y', strtotime($order['created_at'])) ?></p>
                            </div>
                            <div class="mt-4 sm:mt-0">
                                <?= status_badge($order['status'], 'order') ?>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <!-- Progress Tracker -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold mb-4">Repair Progress</h3>
                            <div class="flex items-center justify-between">
                                <?php
                                $statuses = ['received', 'diagnosed', 'in_progress', 'completed', 'delivered'];
                                $currentIndex = array_search($order['status'], $statuses);
                                ?>
                                <?php foreach ($statuses as $index => $status): ?>
                                    <div class="flex flex-col items-center">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold
                                        <?= $index <= $currentIndex ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-500' ?>">
                                            <?= $index + 1 ?>
                                        </div>
                                        <p class="text-xs mt-2 text-center <?= $index <= $currentIndex ? 'text-green-600 font-semibold' : 'text-gray-500' ?>">
                                            <?= ucfirst(str_replace('_', ' ', $status)) ?>
                                        </p>
                                    </div>
                                    <?php if ($index < count($statuses) - 1): ?>
                                        <div class="flex-1 h-1 mx-2 <?= $index < $currentIndex ? 'bg-green-500' : 'bg-gray-200' ?>"></div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Device Information -->
                            <div>
                                <h3 class="text-lg font-semibold mb-4">Device Information</h3>
                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Device Type:</span>
                                        <span class="font-medium"><?= $order['device_type_name'] ?></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Brand:</span>
                                        <span class="font-medium"><?= $order['device_brand'] ?></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Model:</span>
                                        <span class="font-medium"><?= $order['device_model'] ?></span>
                                    </div>
                                    <?php if ($order['device_serial']): ?>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Serial:</span>
                                            <span class="font-medium"><?= $order['device_serial'] ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Order Information -->
                            <div>
                                <h3 class="text-lg font-semibold mb-4">Order Information</h3>
                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Priority:</span>
                                        <?= status_badge($order['priority'], 'priority') ?>
                                    </div>
                                    <?php if ($order['technician_name']): ?>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Technician:</span>
                                            <span class="font-medium"><?= $order['technician_name'] ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($order['estimated_completion']): ?>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Est. Completion:</span>
                                            <span class="font-medium"><?= date('M d, Y', strtotime($order['estimated_completion'])) ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($order['final_cost'] > 0): ?>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Total Cost:</span>
                                            <span class="font-bold text-lg"><?= format_currency($order['final_cost']) ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Problem Description -->
                        <div class="mt-8">
                            <h3 class="text-lg font-semibold mb-4">Problem Description</h3>
                            <p class="text-gray-700 bg-gray-50 p-4 rounded-lg"><?= $order['problem_description'] ?></p>
                        </div>

                        <!-- Contact Section -->
                        <div class="mt-8 bg-blue-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold mb-4">Need Help?</h3>
                            <p class="text-gray-700 mb-4">If you have any questions about your repair, feel free to contact us:</p>
                            <div class="flex flex-col sm:flex-row gap-4">
                                <a href="/contact" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors text-center">
                                    <i class="fas fa-envelope mr-2"></i>Contact Us
                                </a>
                                <a href="tel:<?= get_site_setting('contact_phone') ?>" class="border border-blue-600 text-blue-600 px-6 py-2 rounded-lg hover:bg-blue-600 hover:text-white transition-colors text-center">
                                    <i class="fas fa-phone mr-2"></i>Call Us
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            <?php elseif ($order_number): ?>
                <!-- Order Not Found -->
                <div class="bg-white rounded-xl shadow-lg p-8 text-center">
                    <i class="fas fa-search text-4xl text-gray-400 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Order Not Found</h3>
                    <p class="text-gray-600 mb-6">We couldn't find an order with number "<?= esc($order_number) ?>"</p>
                    <div class="space-y-2 text-left max-w-md mx-auto">
                        <p class="text-sm text-gray-600">Please check:</p>
                        <ul class="text-sm text-gray-600 space-y-1 ml-4">
                            <li>• Order number is correct</li>
                            <li>• No extra spaces or characters</li>
                            <li>• Order format: ORD + date + number</li>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?= $this->endSection() ?>
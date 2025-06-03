<?= $this->extend('admin/layout/main') ?>

<?= $this->section('content') ?>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Update Order Status</h1>
                <p class="text-gray-600">Order #<?= $order['order_number'] ?></p>
            </div>
            <a href="/admin/orders/<?= $order['id'] ?>" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left mr-2"></i>Back to Order
            </a>
        </div>

        <!-- Current Status -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Current Status</h3>
            <div class="flex items-center space-x-4">
                <span class="text-gray-600">Status:</span>
                <?= status_badge($order['status'], 'order') ?>
                <span class="text-gray-400">•</span>
                <span class="text-gray-600">Last updated: <?= date('M d, Y H:i', strtotime($order['updated_at'])) ?></span>
            </div>
        </div>

        <!-- Update Form -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Update Status</h3>

            <form action="/admin/orders/<?= $order['id'] ?>/status" method="POST" class="space-y-6">
                <?= csrf_field() ?>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">New Status *</label>
                    <select name="status" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <?php foreach ($statuses as $key => $label): ?>
                            <option value="<?= $key ?>" <?= $order['status'] == $key ? 'selected' : '' ?>>
                                <?= $label ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" rows="4"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Add notes about this status change..."></textarea>
                </div>

                <!-- Actions -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="/admin/orders/<?= $order['id'] ?>" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-sync mr-2"></i>Update Status
                    </button>
                </div>
            </form>
        </div>
    </div>
<?= $this->endSection() ?>
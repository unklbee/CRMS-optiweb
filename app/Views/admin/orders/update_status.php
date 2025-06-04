<?= $this->extend('admin/layout/main') ?>

<?= $this->section('content') ?>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Update Order Status</h1>
                <p class="text-gray-600">Order #<?= $order['order_number'] ?> - <?= $order['customer_name'] ?></p>
            </div>
            <div class="flex space-x-2">
                <a href="/admin/orders/<?= $order['id'] ?>" class="text-gray-600 hover:text-gray-900 px-4 py-2">
                    <i class="fas fa-eye mr-2"></i>View Order
                </a>
                <a href="/admin/orders" class="text-gray-600 hover:text-gray-900 px-4 py-2">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Orders
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Current Order Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Order Information</h3>

                <div class="space-y-4">
                    <div class="flex items-center space-x-4">
                        <div class="w-16 h-16 bg-blue-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-clipboard-list text-blue-600 text-2xl"></i>
                        </div>
                        <div>
                            <h4 class="text-xl font-semibold text-gray-900">Order #<?= $order['order_number'] ?></h4>
                            <p class="text-gray-600"><?= $order['customer_name'] ?></p>
                            <p class="text-sm text-gray-500"><?= $order['customer_phone'] ?></p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Device:</span>
                            <span class="font-medium"><?= $order['device_type_name'] ?> - <?= $order['device_brand'] ?> <?= $order['device_model'] ?></span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-600">Current Status:</span>
                            <?= format_order_status($order['status']) ?>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-600">Priority:</span>
                            <?= format_order_priority($order['priority']) ?>
                        </div>

                        <?php if ($order['technician_name']): ?>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Technician:</span>
                                <span class="font-medium"><?= $order['technician_name'] ?></span>
                            </div>
                        <?php endif; ?>

                        <div class="flex justify-between">
                            <span class="text-gray-600">Created:</span>
                            <span class="font-medium"><?= date('M d, Y H:i', strtotime($order['created_at'])) ?></span>
                        </div>
                    </div>

                    <?php if ($order['problem_description']): ?>
                        <div class="pt-4 border-t border-gray-200">
                            <h4 class="font-medium text-gray-900 mb-2">Problem Description</h4>
                            <p class="text-gray-600 text-sm"><?= nl2br($order['problem_description']) ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Status Update Form -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Update Status</h3>

                <?php if (session()->getFlashdata('errors')): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                        <ul class="list-disc list-inside space-y-1">
                            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                <li><?= $error ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="/admin/orders/<?= $order['id'] ?>/status" method="POST" class="space-y-6" id="statusForm">
                    <?= csrf_field() ?>

                    <!-- Status Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">New Status *</label>
                        <div class="space-y-3">
                            <?php foreach ($statuses as $statusValue => $statusLabel): ?>
                                <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer
                                    <?= $order['status'] === $statusValue ? 'bg-blue-50 border-blue-300' : '' ?>">
                                    <input type="radio" name="status" value="<?= $statusValue ?>"
                                        <?= $order['status'] === $statusValue ? 'checked' : '' ?>
                                           class="mr-3 text-blue-600" required>
                                    <div class="flex-1 flex items-center justify-between">
                                        <div class="flex items-center">
                                            <i class="fas fa-<?= get_status_icon($statusValue) ?> text-gray-600 mr-3"></i>
                                            <div>
                                                <span class="font-medium text-gray-900"><?= $statusLabel ?></span>
                                                <?php if ($order['status'] === $statusValue): ?>
                                                    <span class="text-xs text-blue-600 ml-2">(Current)</span>
                                                <?php endif; ?>
                                                <p class="text-sm text-gray-600"><?= get_status_description($statusValue) ?></p>
                                            </div>
                                        </div>
                                        <?php if (get_status_progress($statusValue) !== null): ?>
                                            <div class="text-right">
                                                <div class="text-xs text-gray-500"><?= get_status_progress($statusValue) ?>%</div>
                                                <div class="w-12 bg-gray-200 rounded-full h-2 mt-1">
                                                    <div class="bg-blue-600 h-2 rounded-full" style="width: <?= get_status_progress($statusValue) ?>%"></div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status Update Notes</label>
                        <textarea name="notes" rows="4"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                  placeholder="Add any notes about this status change..."><?= old('notes') ?></textarea>
                        <p class="text-xs text-gray-500 mt-1">These notes will be visible to the customer and added to the order history</p>
                    </div>

                    <!-- Quick Notes -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Quick Notes</label>
                        <div class="grid grid-cols-1 gap-2">
                            <button type="button" onclick="setNote('Diagnosis completed - awaiting customer approval')"
                                    class="text-left p-2 border border-gray-200 rounded hover:bg-gray-50 text-sm">
                                <i class="fas fa-search text-blue-600 mr-2"></i>Diagnosis completed - awaiting customer approval
                            </button>
                            <button type="button" onclick="setNote('Repair work started')"
                                    class="text-left p-2 border border-gray-200 rounded hover:bg-gray-50 text-sm">
                                <i class="fas fa-wrench text-green-600 mr-2"></i>Repair work started
                            </button>
                            <button type="button" onclick="setNote('Waiting for replacement parts to arrive')"
                                    class="text-left p-2 border border-gray-200 rounded hover:bg-gray-50 text-sm">
                                <i class="fas fa-box text-purple-600 mr-2"></i>Waiting for replacement parts to arrive
                            </button>
                            <button type="button" onclick="setNote('Repair completed successfully - ready for pickup')"
                                    class="text-left p-2 border border-gray-200 rounded hover:bg-gray-50 text-sm">
                                <i class="fas fa-check-circle text-green-600 mr-2"></i>Repair completed successfully - ready for pickup
                            </button>
                            <button type="button" onclick="setNote('Device delivered to customer')"
                                    class="text-left p-2 border border-gray-200 rounded hover:bg-gray-50 text-sm">
                                <i class="fas fa-truck text-blue-600 mr-2"></i>Device delivered to customer
                            </button>
                        </div>
                    </div>

                    <!-- Customer Notification -->
                    <?php if (!empty($order['customer_email'])): ?>
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <i class="fas fa-envelope text-blue-600 mr-2"></i>
                                <span class="text-blue-800 font-medium text-sm">Email Notification</span>
                            </div>
                            <p class="text-blue-700 text-sm mt-1">
                                Customer will be automatically notified about this status change at: <?= $order['customer_email'] ?>
                            </p>
                        </div>
                    <?php else: ?>
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-triangle text-yellow-600 mr-2"></i>
                                <span class="text-yellow-800 font-medium text-sm">No Email Available</span>
                            </div>
                            <p class="text-yellow-700 text-sm mt-1">
                                Customer will need to be notified manually via phone: <?= $order['customer_phone'] ?>
                            </p>
                        </div>
                    <?php endif; ?>

                    <!-- Submit Buttons -->
                    <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                        <a href="/admin/orders/<?= $order['id'] ?>"
                           class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </a>
                        <button type="submit"
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium"
                                id="submitBtn">
                            <i class="fas fa-save mr-2"></i>Update Status
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Set quick note
        function setNote(note) {
            document.querySelector('textarea[name="notes"]').value = note;
        }

        // Form validation and confirmation
        document.getElementById('statusForm').addEventListener('submit', function(e) {
            const selectedStatus = document.querySelector('input[name="status"]:checked');
            const currentStatus = '<?= $order['status'] ?>';

            if (!selectedStatus) {
                alert('Please select a status.');
                e.preventDefault();
                return;
            }

            if (selectedStatus.value === currentStatus) {
                if (!confirm('The selected status is the same as the current status. Do you want to continue?')) {
                    e.preventDefault();
                    return;
                }
            }

            // Special confirmations for certain status changes
            if (selectedStatus.value === 'completed') {
                if (!confirm('Are you sure the repair is completed and ready for pickup/delivery?')) {
                    e.preventDefault();
                    return;
                }
            }

            if (selectedStatus.value === 'cancelled') {
                if (!confirm('Are you sure you want to cancel this order? This action should be used carefully.')) {
                    e.preventDefault();
                    return;
                }
            }

            // Show loading state
            const submitBtn = document.getElementById('submitBtn');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Updating...';
            submitBtn.disabled = true;
        });

        // Auto-select appropriate notes based on status
        document.querySelectorAll('input[name="status"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const status = this.value;
                const notesField = document.querySelector('textarea[name="notes"]');

                // Don't overwrite if user has already typed something
                if (notesField.value.trim()) return;

                const autoNotes = {
                    'diagnosed': 'Diagnosis completed - awaiting customer approval',
                    'in_progress': 'Repair work has started',
                    'waiting_parts': 'Waiting for replacement parts to arrive',
                    'completed': 'Repair completed successfully - ready for pickup',
                    'delivered': 'Device delivered to customer',
                    'cancelled': 'Order cancelled'
                };

                if (autoNotes[status]) {
                    notesField.value = autoNotes[status];
                }
            });
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Number keys to select status (1-8)
            if (e.key >= '1' && e.key <= '8') {
                const statusInputs = document.querySelectorAll('input[name="status"]');
                const index = parseInt(e.key) - 1;
                if (statusInputs[index]) {
                    statusInputs[index].checked = true;
                    statusInputs[index].dispatchEvent(new Event('change'));
                }
            }

            // Ctrl/Cmd + Enter to submit
            if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('statusForm').submit();
            }
        });
    </script>
<?= $this->endSection() ?>
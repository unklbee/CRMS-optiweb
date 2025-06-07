<?php
/**
 * File: app/Views/admin/quotations/create.php
 * Create this file to fix the missing view error
 */
?>

<?= $this->extend('admin/layout/main') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800"><?= $title ?></h1>
                    <p class="text-gray-600 mt-1">Create quotation for order #<?= $order['order_number'] ?></p>
                </div>
                <div class="flex space-x-3 mt-4 sm:mt-0">
                    <a href="/admin/orders/<?= $order['id'] ?>"
                       class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Order
                    </a>
                    <a href="/admin/diagnosis/<?= $order['id'] ?>"
                       class="bg-blue-100 text-blue-700 px-4 py-2 rounded-lg hover:bg-blue-200 transition-colors">
                        <i class="fas fa-search mr-2"></i>View Diagnosis
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Form (Left 2 columns) -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Order Information Summary -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Order Summary</h3>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="font-medium text-gray-600">Customer:</span>
                            <p class="text-gray-800"><?= $order['customer_name'] ?></p>
                        </div>
                        <div>
                            <span class="font-medium text-gray-600">Device:</span>
                            <p class="text-gray-800"><?= $order['device_type_name'] ?></p>
                        </div>
                        <div>
                            <span class="font-medium text-gray-600">Brand/Model:</span>
                            <p class="text-gray-800"><?= $order['device_brand'] ?> <?= $order['device_model'] ?></p>
                        </div>
                        <div>
                            <span class="font-medium text-gray-600">Status:</span>
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">
                            <?= ucwords($order['status']) ?>
                        </span>
                        </div>
                    </div>
                </div>

                <!-- Diagnosis Summary (if available) -->
                <?php if (!empty($order['diagnosis_notes'])): ?>
                    <div class="bg-blue-50 rounded-xl border border-blue-200 p-6">
                        <h3 class="text-lg font-semibold text-blue-800 mb-4">
                            <i class="fas fa-search mr-2"></i>Diagnosis Summary
                        </h3>
                        <div class="space-y-3 text-sm">
                            <?php if (!empty($order['diagnosis_notes'])): ?>
                                <div>
                                    <span class="font-medium text-blue-800">Findings:</span>
                                    <p class="text-blue-700 mt-1"><?= nl2br($order['diagnosis_notes']) ?></p>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($order['recommended_actions'])): ?>
                                <div>
                                    <span class="font-medium text-blue-800">Recommended Actions:</span>
                                    <p class="text-blue-700 mt-1"><?= nl2br($order['recommended_actions']) ?></p>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($order['estimated_hours'])): ?>
                                <div>
                                    <span class="font-medium text-blue-800">Estimated Hours:</span>
                                    <span class="text-blue-700 ml-2"><?= $order['estimated_hours'] ?> hours</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Parts Cost Summary -->
                <?php if (!empty($order_parts)): ?>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">
                            <i class="fas fa-cogs mr-2"></i>Parts Required
                        </h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50">
                                <tr>
                                    <th class="text-left p-3">Part Name</th>
                                    <th class="text-center p-3">Qty</th>
                                    <th class="text-right p-3">Unit Price</th>
                                    <th class="text-right p-3">Total</th>
                                </tr>
                                </thead>
                                <tbody class="divide-y">
                                <?php foreach ($order_parts as $part): ?>
                                    <tr>
                                        <td class="p-3"><?= $part['part_name'] ?></td>
                                        <td class="text-center p-3"><?= $part['quantity'] ?></td>
                                        <td class="text-right p-3"><?= format_currency($part['unit_price']) ?></td>
                                        <td class="text-right p-3 font-medium"><?= format_currency($part['total_price']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                                <tfoot class="bg-gray-50 font-medium">
                                <tr>
                                    <td colspan="3" class="p-3 text-right">Parts Total:</td>
                                    <td class="p-3 text-right"><?= format_currency($parts_total) ?></td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Quotation Form -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-file-invoice-dollar mr-2"></i>Quotation Details
                    </h3>

                    <?php if (session()->getFlashdata('errors')): ?>
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                            <ul class="list-disc list-inside space-y-1">
                                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                    <li><?= $error ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="/admin/orders/<?= $order['id'] ?>/quotation" method="POST" class="space-y-6" id="quotationForm">
                        <?= csrf_field() ?>

                        <!-- Parts Cost (Read-only) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Parts Cost</label>
                            <div class="relative">
                                <input type="text"
                                       value="<?= format_currency($parts_total) ?>"
                                       class="w-full p-3 border border-gray-300 rounded-lg bg-gray-50 text-gray-600"
                                       readonly>
                                <span class="absolute right-3 top-3 text-gray-400">
                                <i class="fas fa-cogs"></i>
                            </span>
                            </div>
                        </div>

                        <!-- Labor Cost -->
                        <div>
                            <label for="labor_cost" class="block text-sm font-medium text-gray-700 mb-2">
                                Labor Cost <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="number"
                                       step="0.01"
                                       id="labor_cost"
                                       name="labor_cost"
                                       value="<?= old('labor_cost') ?>"
                                       class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="0.00"
                                       required
                                       oninput="calculateTotal()">
                                <span class="absolute right-3 top-3 text-gray-400">
                                <i class="fas fa-wrench"></i>
                            </span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Enter the labor cost for repair work</p>
                        </div>

                        <!-- Total Cost Display -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Total Cost</label>
                            <div class="relative">
                                <input type="text"
                                       id="total_display"
                                       value="<?= format_currency($parts_total) ?>"
                                       class="w-full p-3 border border-gray-300 rounded-lg bg-blue-50 text-blue-800 font-semibold text-lg"
                                       readonly>
                                <span class="absolute right-3 top-3 text-blue-600">
                                <i class="fas fa-calculator"></i>
                            </span>
                            </div>
                        </div>

                        <!-- Validity Period -->
                        <div>
                            <label for="valid_days" class="block text-sm font-medium text-gray-700 mb-2">
                                Quotation Valid For (Days)
                            </label>
                            <select id="valid_days"
                                    name="valid_days"
                                    class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="7" <?= old('valid_days', '7') == '7' ? 'selected' : '' ?>>7 days</option>
                                <option value="14" <?= old('valid_days') == '14' ? 'selected' : '' ?>>14 days</option>
                                <option value="30" <?= old('valid_days') == '30' ? 'selected' : '' ?>>30 days</option>
                                <option value="60" <?= old('valid_days') == '60' ? 'selected' : '' ?>>60 days</option>
                            </select>
                        </div>

                        <!-- Notes -->
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                Additional Notes
                            </label>
                            <textarea id="notes"
                                      name="notes"
                                      rows="4"
                                      class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="Any additional terms, conditions, or notes for the customer..."><?= old('notes') ?></textarea>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col sm:flex-row gap-3">
                            <button type="submit"
                                    name="action"
                                    value="save_draft"
                                    class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                                <i class="fas fa-save mr-2"></i>Save as Draft
                            </button>
                            <button type="submit"
                                    name="action"
                                    value="save_and_send"
                                    class="flex-1 bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors font-medium">
                                <i class="fas fa-paper-plane mr-2"></i>Save & Send to Customer
                            </button>
                            <a href="/admin/orders/<?= $order['id'] ?>"
                               class="flex-1 text-center bg-gray-100 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-200 transition-colors font-medium">
                                <i class="fas fa-times mr-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Sidebar (Right column) -->
            <div class="space-y-6">
                <!-- Quick Actions -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <a href="/admin/diagnosis/<?= $order['id'] ?>"
                           class="block w-full text-center bg-blue-100 text-blue-700 px-4 py-2 rounded-lg hover:bg-blue-200 transition-colors">
                            <i class="fas fa-search mr-2"></i>View Diagnosis
                        </a>
                        <a href="/admin/orders/<?= $order['id'] ?>/manage-parts"
                           class="block w-full text-center bg-purple-100 text-purple-700 px-4 py-2 rounded-lg hover:bg-purple-200 transition-colors">
                            <i class="fas fa-cogs mr-2"></i>Manage Parts
                        </a>
                        <a href="/admin/orders/<?= $order['id'] ?>"
                           class="block w-full text-center bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i>Back to Order
                        </a>
                    </div>
                </div>

                <!-- Cost Breakdown -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Cost Breakdown</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Parts Cost:</span>
                            <span class="font-medium" id="parts_cost_display"><?= format_currency($parts_total) ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Labor Cost:</span>
                            <span class="font-medium" id="labor_cost_display">Rp 0</span>
                        </div>
                        <hr>
                        <div class="flex justify-between text-lg font-semibold">
                            <span>Total:</span>
                            <span class="text-blue-600" id="total_cost_display"><?= format_currency($parts_total) ?></span>
                        </div>
                    </div>
                </div>

                <!-- Estimated Timeline -->
                <?php if (!empty($order['estimated_hours'])): ?>
                    <div class="bg-green-50 rounded-xl border border-green-200 p-6">
                        <h3 class="text-lg font-semibold text-green-800 mb-2">
                            <i class="fas fa-clock mr-2"></i>Estimated Timeline
                        </h3>
                        <p class="text-green-700">
                            <span class="font-medium"><?= $order['estimated_hours'] ?> hours</span> of work required
                        </p>
                        <p class="text-green-600 text-sm mt-1">
                            Approximately <?= ceil($order['estimated_hours'] / 8) ?> working day(s)
                        </p>
                    </div>
                <?php endif; ?>

                <!-- Help -->
                <div class="bg-yellow-50 rounded-xl border border-yellow-200 p-6">
                    <h3 class="text-lg font-semibold text-yellow-800 mb-2">
                        <i class="fas fa-info-circle mr-2"></i>Tips
                    </h3>
                    <ul class="text-yellow-700 text-sm space-y-1">
                        <li>• Labor cost should include all repair work</li>
                        <li>• Parts cost is auto-calculated from order parts</li>
                        <li>• Draft quotations can be edited later</li>
                        <li>• Sent quotations will be emailed to customer</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Global variables
        const partsTotal = <?= $parts_total ?>;

        function calculateTotal() {
            const laborCost = parseFloat(document.getElementById('labor_cost').value) || 0;
            const total = partsTotal + laborCost;

            // Update displays
            document.getElementById('total_display').value = formatCurrency(total);
            document.getElementById('labor_cost_display').textContent = formatCurrency(laborCost);
            document.getElementById('total_cost_display').textContent = formatCurrency(total);
        }

        function formatCurrency(amount) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
        }

        // Initialize calculation on page load
        document.addEventListener('DOMContentLoaded', function() {
            calculateTotal();
        });

        // Form validation
        document.getElementById('quotationForm').addEventListener('submit', function(e) {
            const laborCost = document.getElementById('labor_cost').value;

            if (!laborCost || parseFloat(laborCost) < 0) {
                e.preventDefault();
                alert('Please enter a valid labor cost');
                return false;
            }

            // Confirm send action
            if (e.submitter.value === 'save_and_send') {
                if (!confirm('Are you sure you want to send this quotation to the customer immediately?')) {
                    e.preventDefault();
                    return false;
                }
            }
        });
    </script>

<?= $this->endSection() ?>
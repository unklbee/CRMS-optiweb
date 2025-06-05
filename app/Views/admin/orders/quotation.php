<?= $this->extend('admin/layout/main') ?>

<?= $this->section('content') ?>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    <?= isset($existing_quotation) && $existing_quotation ? 'Edit' : 'Create' ?> Quotation
                </h1>
                <p class="text-gray-600">Order #<?= $order['order_number'] ?> - <?= $order['customer_name'] ?></p>
            </div>
            <div class="flex space-x-2">
                <?php if (isset($existing_quotation) && $existing_quotation): ?>
                    <a href="/admin/orders/<?= $order['id'] ?>/quotation" class="text-gray-600 hover:text-gray-900 px-4 py-2">
                        <i class="fas fa-eye mr-2"></i>View Current
                    </a>
                <?php endif; ?>
                <a href="/admin/orders/<?= $order['id'] ?>" class="text-gray-600 hover:text-gray-900 px-4 py-2">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Order
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Form -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Diagnosis Summary -->
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-blue-800 mb-4">
                        <i class="fas fa-stethoscope mr-2"></i>Diagnosis Summary
                    </h3>
                    <div class="space-y-3 text-sm">
                        <?php if ($order['diagnosis_notes']): ?>
                            <div>
                                <span class="font-medium text-blue-800">Findings:</span>
                                <p class="text-blue-700 mt-1"><?= nl2br($order['diagnosis_notes']) ?></p>
                            </div>
                        <?php endif; ?>

                        <?php if ($order['recommended_actions']): ?>
                            <div>
                                <span class="font-medium text-blue-800">Recommended Actions:</span>
                                <p class="text-blue-700 mt-1"><?= nl2br($order['recommended_actions']) ?></p>
                            </div>
                        <?php endif; ?>

                        <?php if ($order['estimated_hours']): ?>
                            <div>
                                <span class="font-medium text-blue-800">Estimated Hours:</span>
                                <span class="text-blue-700 ml-2"><?= $order['estimated_hours'] ?> hours</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Quotation Form -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <form action="/admin/orders/<?= $order['id'] ?>/quotation" method="POST" class="space-y-6" id="quotationForm">
                        <?= csrf_field() ?>

                        <!-- Service Costs -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Service Charges</h3>

                            <!-- Quick Service Rates -->
                            <div class="mb-4">
                                <h4 class="font-medium text-gray-700 mb-2">Quick Rates (Click to apply)</h4>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                                    <?php foreach ($service_rates as $service => $rate): ?>
                                        <button type="button" onclick="setServiceCost(<?= $rate ?>)"
                                                class="text-left p-2 border border-gray-200 rounded hover:bg-gray-50 text-sm">
                                            <div class="font-medium"><?= ucfirst(str_replace('_', ' ', $service)) ?></div>
                                            <div class="text-gray-500"><?= format_currency($rate) ?></div>
                                        </button>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Service Cost (Rp) *</label>
                                <input type="number" name="service_cost" required step="1000" min="0"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="0" id="serviceCostInput"
                                       value="<?= old('service_cost', $existing_quotation['service_cost'] ?? '') ?>"
                                       onchange="calculateTotal()">
                                <p class="text-xs text-gray-500 mt-1">Labor charges for repair work</p>
                            </div>
                        </div>

                        <!-- Parts Costs -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Parts & Materials</h3>

                            <?php if (!empty($order_parts)): ?>
                                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                                    <h4 class="font-medium text-gray-700 mb-2">Parts Already Added to Order</h4>
                                    <div class="space-y-2 text-sm">
                                        <?php
                                        $totalPartsFromOrder = 0;
                                        foreach ($order_parts as $part):
                                            $totalPartsFromOrder += $part['total_price'];
                                            ?>
                                            <div class="flex justify-between">
                                                <span><?= $part['part_name'] ?> (×<?= $part['quantity'] ?>)</span>
                                                <span class="font-medium"><?= format_currency($part['total_price']) ?></span>
                                            </div>
                                        <?php endforeach; ?>
                                        <div class="border-t pt-2 flex justify-between font-medium">
                                            <span>Total:</span>
                                            <span><?= format_currency($totalPartsFromOrder) ?></span>
                                        </div>
                                    </div>
                                    <button type="button" onclick="setPartsCost(<?= $totalPartsFromOrder ?>)"
                                            class="mt-2 text-blue-600 hover:text-blue-800 text-sm">
                                        <i class="fas fa-copy mr-1"></i>Use this amount
                                    </button>
                                </div>
                            <?php endif; ?>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Parts Cost (Rp)</label>
                                <input type="number" name="parts_cost" step="1000" min="0"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="0" id="partsCostInput"
                                       value="<?= old('parts_cost', $existing_quotation['parts_cost'] ?? '') ?>"
                                       onchange="calculateTotal()">
                                <p class="text-xs text-gray-500 mt-1">Cost of replacement parts and materials</p>
                            </div>
                        </div>

                        <!-- Additional Costs -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Additional Charges</h3>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Additional Cost (Rp)</label>
                                <input type="number" name="additional_cost" step="1000" min="0"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="0" id="additionalCostInput"
                                       value="<?= old('additional_cost', $existing_quotation['additional_cost'] ?? '') ?>"
                                       onchange="calculateTotal()">
                                <p class="text-xs text-gray-500 mt-1">Any additional charges (shipping, express service, etc.)</p>
                            </div>
                        </div>

                        <!-- Discounts & Tax -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h4 class="font-medium text-gray-800 mb-3">Discount</h4>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Discount Percentage (%)</label>
                                        <input type="number" name="discount_percentage" step="0.1" min="0" max="100"
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                               placeholder="0" id="discountPercentageInput"
                                               value="<?= old('discount_percentage', $existing_quotation['discount_percentage'] ?? '') ?>"
                                               onchange="calculateTotal()">
                                    </div>
                                    <div class="text-center text-sm text-gray-500">OR</div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Discount Amount (Rp)</label>
                                        <input type="number" name="discount_amount" step="1000" min="0"
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                               placeholder="0" id="discountAmountInput"
                                               value="<?= old('discount_amount', $existing_quotation['discount_amount'] ?? '') ?>"
                                               onchange="calculateTotal()">
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h4 class="font-medium text-gray-800 mb-3">Tax</h4>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Tax Percentage (%)</label>
                                    <input type="number" name="tax_percentage" step="0.1" min="0" max="100"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           placeholder="0" id="taxPercentageInput"
                                           value="<?= old('tax_percentage', $existing_quotation['tax_percentage'] ?? $default_tax_rate) ?>"
                                           onchange="calculateTotal()">
                                    <p class="text-xs text-gray-500 mt-1">PPN or other applicable taxes</p>
                                </div>
                            </div>
                        </div>

                        <!-- Cost Breakdown -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-medium text-gray-800 mb-3">Cost Breakdown</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span>Service Cost:</span>
                                    <span id="displayServiceCost">Rp 0</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Parts Cost:</span>
                                    <span id="displayPartsCost">Rp 0</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Additional Cost:</span>
                                    <span id="displayAdditionalCost">Rp 0</span>
                                </div>
                                <div class="flex justify-between border-t pt-2">
                                    <span>Subtotal:</span>
                                    <span id="displaySubtotal">Rp 0</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Discount:</span>
                                    <span id="displayDiscount" class="text-red-600">- Rp 0</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Tax:</span>
                                    <span id="displayTax">Rp 0</span>
                                </div>
                                <div class="flex justify-between border-t pt-2 font-bold text-lg">
                                    <span>Total:</span>
                                    <span id="displayTotal" class="text-green-600">Rp 0</span>
                                </div>
                            </div>
                        </div>

                        <!-- Timeline & Terms -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Estimated Duration *</label>
                                <input type="text" name="estimated_duration" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="e.g., 2-3 working days"
                                       value="<?= old('estimated_duration', $existing_quotation['estimated_duration'] ?? '') ?>">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Warranty Period</label>
                                <input type="text" name="warranty_period"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="e.g., 30 days"
                                       value="<?= old('warranty_period', $existing_quotation['warranty_period'] ?? $default_warranty) ?>">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Valid Until *</label>
                            <input type="date" name="valid_until" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   value="<?= old('valid_until', $existing_quotation['valid_until'] ?? date('Y-m-d', strtotime('+7 days'))) ?>"
                                   min="<?= date('Y-m-d') ?>">
                        </div>

                        <!-- Terms & Conditions -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Terms & Conditions</label>
                            <textarea name="terms_conditions" rows="4"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="Enter terms and conditions..."><?= old('terms_conditions', $existing_quotation['terms_conditions'] ?? get_site_setting('default_terms', '1. Payment required before work begins\n2. Warranty void if device is opened by customer\n3. Data backup is customer\'s responsibility')) ?></textarea>
                        </div>

                        <!-- Internal Notes -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Internal Notes</label>
                            <textarea name="internal_notes" rows="3"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="Internal notes (not visible to customer)..."><?= old('internal_notes', $existing_quotation['internal_notes'] ?? '') ?></textarea>
                        </div>

                        <!-- Hidden total field -->
                        <input type="hidden" name="total_cost" id="totalCostInput">

                        <!-- Send Options -->
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <h4 class="font-medium text-green-800 mb-3">
                                <i class="fas fa-paper-plane mr-2"></i>Send to Customer
                            </h4>
                            <label class="flex items-center">
                                <input type="checkbox" name="send_immediately" value="1"
                                       class="mr-2 text-green-600 rounded focus:ring-green-500">
                                <span class="text-sm text-green-700">Send quotation to customer immediately via email</span>
                            </label>
                            <p class="text-xs text-green-600 mt-2">
                                Customer email: <?= $order['customer_email'] ?: 'Not available' ?>
                            </p>
                        </div>

                        <!-- Actions -->
                        <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                            <a href="/admin/orders/<?= $order['id'] ?>" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                                Cancel
                            </a>
                            <button type="button" onclick="saveDraft()" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                                <i class="fas fa-save mr-2"></i>Save Draft
                            </button>
                            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-file-invoice-dollar mr-2"></i>
                                <?= isset($existing_quotation) && $existing_quotation ? 'Update' : 'Create' ?> Quotation
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Order Summary -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Order Summary</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Order:</span>
                            <span class="font-medium"><?= $order['order_number'] ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Customer:</span>
                            <span class="font-medium"><?= $order['customer_name'] ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Device:</span>
                            <span class="font-medium"><?= $order['device_type_name'] ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Priority:</span>
                            <?= format_order_priority($order['priority']) ?>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Status:</span>
                            <?= format_order_status($order['status']) ?>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <button type="button" onclick="addStandardMarkup()"
                                class="w-full bg-blue-100 text-blue-700 py-2 px-4 rounded-lg hover:bg-blue-200 transition-colors text-sm">
                            <i class="fas fa-percentage mr-2"></i>Add 30% Markup
                        </button>
                        <button type="button" onclick="roundToNearest()"
                                class="w-full bg-purple-100 text-purple-700 py-2 px-4 rounded-lg hover:bg-purple-200 transition-colors text-sm">
                            <i class="fas fa-calculator mr-2"></i>Round to Nearest 10k
                        </button>
                        <button type="button" onclick="applyDiscount(10)"
                                class="w-full bg-orange-100 text-orange-700 py-2 px-4 rounded-lg hover:bg-orange-200 transition-colors text-sm">
                            <i class="fas fa-tag mr-2"></i>Apply 10% Discount
                        </button>
                    </div>
                </div>

                <!-- Tips -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-yellow-800 mb-4">
                        <i class="fas fa-lightbulb mr-2"></i>Tips
                    </h3>
                    <div class="space-y-2 text-sm text-yellow-700">
                        <p>• Include all potential costs to avoid surprises</p>
                        <p>• Be realistic with estimated duration</p>
                        <p>• Clear terms help prevent disputes</p>
                        <p>• Consider part availability in timeline</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function calculateTotal() {
            const serviceCost = parseFloat(document.getElementById('serviceCostInput').value) || 0;
            const partsCost = parseFloat(document.getElementById('partsCostInput').value) || 0;
            const additionalCost = parseFloat(document.getElementById('additionalCostInput').value) || 0;

            const discountPercentage = parseFloat(document.getElementById('discountPercentageInput').value) || 0;
            const discountAmount = parseFloat(document.getElementById('discountAmountInput').value) || 0;

            const taxPercentage = parseFloat(document.getElementById('taxPercentageInput').value) || 0;

            // Calculate subtotal
            const subtotal = serviceCost + partsCost + additionalCost;

            // Calculate discount (percentage takes priority)
            let finalDiscount = discountAmount;
            if (discountPercentage > 0) {
                finalDiscount = (subtotal * discountPercentage) / 100;
                document.getElementById('discountAmountInput').value = finalDiscount;
            }

            // Subtotal after discount
            const afterDiscount = subtotal - finalDiscount;

            // Calculate tax
            const taxAmount = (afterDiscount * taxPercentage) / 100;

            // Final total
            const total = afterDiscount + taxAmount;

            // Update display
            document.getElementById('displayServiceCost').textContent = formatCurrency(serviceCost);
            document.getElementById('displayPartsCost').textContent = formatCurrency(partsCost);
            document.getElementById('displayAdditionalCost').textContent = formatCurrency(additionalCost);
            document.getElementById('displaySubtotal').textContent = formatCurrency(subtotal);
            document.getElementById('displayDiscount').textContent = '- ' + formatCurrency(finalDiscount);
            document.getElementById('displayTax').textContent = formatCurrency(taxAmount);
            document.getElementById('displayTotal').textContent = formatCurrency(total);

            // Update hidden input
            document.getElementById('totalCostInput').value = total;
        }

        function formatCurrency(amount) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
        }

        function setServiceCost(amount) {
            document.getElementById('serviceCostInput').value = amount;
            calculateTotal();
        }

        function setPartsCost(amount) {
            document.getElementById('partsCostInput').value = amount;
            calculateTotal();
        }

        function addStandardMarkup() {
            const serviceCost = parseFloat(document.getElementById('serviceCostInput').value) || 0;
            const newCost = serviceCost * 1.3; // 30% markup
            document.getElementById('serviceCostInput').value = Math.round(newCost);
            calculateTotal();
        }

        function roundToNearest() {
            const total = parseFloat(document.getElementById('totalCostInput').value) || 0;
            const rounded = Math.round(total / 10000) * 10000;

            // Adjust service cost to match rounded total
            const serviceCost = parseFloat(document.getElementById('serviceCostInput').value) || 0;
            const adjustment = rounded - total;
            document.getElementById('serviceCostInput').value = serviceCost + adjustment;

            calculateTotal();
        }

        function applyDiscount(percentage) {
            document.getElementById('discountPercentageInput').value = percentage;
            document.getElementById('discountAmountInput').value = '';
            calculateTotal();
        }

        function saveDraft() {
            const formData = new FormData(document.getElementById('quotationForm'));
            const draftData = {
                timestamp: new Date().toISOString(),
                data: Object.fromEntries(formData)
            };

            localStorage.setItem('quotation_draft_<?= $order['id'] ?>', JSON.stringify(draftData));

            // Show success message
            const btn = event.target;
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check mr-2"></i>Saved!';
            btn.classList.remove('bg-gray-600', 'hover:bg-gray-700');
            btn.classList.add('bg-green-600');

            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.classList.remove('bg-green-600');
                btn.classList.add('bg-gray-600', 'hover:bg-gray-700');
            }, 2000);
        }

        // Initialize calculations
        document.addEventListener('DOMContentLoaded', function() {
            calculateTotal();

            // Load draft if available
            const draft = localStorage.getItem('quotation_draft_<?= $order['id'] ?>');
            if (draft) {
                const draftData = JSON.parse(draft);
                const draftAge = new Date() - new Date(draftData.timestamp);

                if (draftAge < 24 * 60 * 60 * 1000) {
                    if (confirm('Found a saved draft. Would you like to restore it?')) {
                        Object.keys(draftData.data).forEach(key => {
                            const input = document.querySelector(`[name="${key}"]`);
                            if (input) {
                                if (input.type === 'checkbox') {
                                    input.checked = draftData.data[key] === '1';
                                } else {
                                    input.value = draftData.data[key];
                                }
                            }
                        });
                        calculateTotal();
                    } else {
                        localStorage.removeItem('quotation_draft_<?= $order['id'] ?>');
                    }
                }
            }
        });

        // Clear draft on form submission
        document.getElementById('quotationForm').addEventListener('submit', function() {
            localStorage.removeItem('quotation_draft_<?= $order['id'] ?>');
        });

        // Auto-calculate when discount percentage changes
        document.getElementById('discountPercentageInput').addEventListener('input', function() {
            if (this.value) {
                document.getElementById('discountAmountInput').value = '';
            }
            calculateTotal();
        });

        // Auto-calculate when discount amount changes
        document.getElementById('discountAmountInput').addEventListener('input', function() {
            if (this.value) {
                document.getElementById('discountPercentageInput').value = '';
            }
            calculateTotal();
        });
    </script>
<?= $this->endSection() ?>
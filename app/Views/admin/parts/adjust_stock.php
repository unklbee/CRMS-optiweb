<?= $this->extend('admin/layout/main') ?>

<?= $this->section('content') ?>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Adjust Stock</h1>
                <p class="text-gray-600"><?= $part['part_number'] ?> - <?= $part['name'] ?></p>
            </div>
            <div class="flex space-x-2">
                <a href="/admin/parts/<?= $part['id'] ?>" class="text-gray-600 hover:text-gray-900 px-4 py-2">
                    <i class="fas fa-eye mr-2"></i>View Part
                </a>
                <a href="/admin/parts" class="text-gray-600 hover:text-gray-900 px-4 py-2">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Parts
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Current Stock Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Current Stock Information</h3>

                <div class="space-y-4">
                    <div class="flex items-center space-x-4">
                        <div class="w-16 h-16 bg-blue-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-box text-blue-600 text-2xl"></i>
                        </div>
                        <div>
                            <h4 class="text-xl font-semibold text-gray-900"><?= $part['name'] ?></h4>
                            <p class="text-gray-600">Part #<?= $part['part_number'] ?></p>
                            <?php if ($part['brand']): ?>
                                <p class="text-sm text-gray-500">Brand: <?= $part['brand'] ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <p class="text-3xl font-bold <?= $part['stock_quantity'] <= $part['min_stock'] ? 'text-red-600' : 'text-blue-600' ?>" id="currentStock">
                                <?= $part['stock_quantity'] ?>
                            </p>
                            <p class="text-sm text-gray-600">Current Stock</p>
                        </div>
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <p class="text-xl font-medium text-gray-800"><?= $part['min_stock'] ?></p>
                            <p class="text-sm text-gray-600">Minimum Stock</p>
                        </div>
                    </div>

                    <?php if ($part['stock_quantity'] <= $part['min_stock']): ?>
                        <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-triangle text-red-600 mr-2"></i>
                                <span class="text-red-800 font-medium text-sm">Low Stock Alert</span>
                            </div>
                            <p class="text-red-700 text-sm mt-1">Current stock is below minimum level</p>
                        </div>
                    <?php endif; ?>

                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Cost per Unit:</span>
                            <span class="font-medium"><?= format_currency($part['cost_price']) ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Selling Price:</span>
                            <span class="font-medium"><?= format_currency($part['selling_price']) ?></span>
                        </div>
                        <div class="flex justify-between border-t pt-2">
                            <span class="text-gray-600">Current Stock Value:</span>
                            <span class="font-bold"><?= format_currency($part['stock_quantity'] * $part['cost_price']) ?></span>
                        </div>
                    </div>

                    <?php if ($part['location']): ?>
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                            <div class="flex items-center">
                                <i class="fas fa-map-marker-alt text-blue-600 mr-2"></i>
                                <span class="text-blue-800 font-medium text-sm">Storage Location</span>
                            </div>
                            <p class="text-blue-700 text-sm mt-1"><?= $part['location'] ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Stock Adjustment Form -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Stock Adjustment</h3>

                <?php if (session()->getFlashdata('errors')): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                        <ul class="list-disc list-inside space-y-1">
                            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                <li><?= $error ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="/admin/parts/<?= $part['id'] ?>/adjust-stock" method="POST" class="space-y-6" id="adjustStockForm">
                    <?= csrf_field() ?>

                    <!-- Adjustment Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Adjustment Type *</label>
                        <div class="space-y-3">
                            <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="radio" name="adjustment_type" value="add" required class="mr-3 text-blue-600">
                                <div class="flex-1">
                                    <div class="flex items-center">
                                        <i class="fas fa-plus text-green-600 mr-2"></i>
                                        <span class="font-medium text-gray-900">Add Stock</span>
                                    </div>
                                    <p class="text-sm text-gray-600 ml-6">Increase inventory (receiving new stock)</p>
                                </div>
                            </label>

                            <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="radio" name="adjustment_type" value="subtract" required class="mr-3 text-blue-600">
                                <div class="flex-1">
                                    <div class="flex items-center">
                                        <i class="fas fa-minus text-red-600 mr-2"></i>
                                        <span class="font-medium text-gray-900">Remove Stock</span>
                                    </div>
                                    <p class="text-sm text-gray-600 ml-6">Decrease inventory (damaged, used, or lost items)</p>
                                </div>
                            </label>

                            <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="radio" name="adjustment_type" value="set" required class="mr-3 text-blue-600">
                                <div class="flex-1">
                                    <div class="flex items-center">
                                        <i class="fas fa-equals text-blue-600 mr-2"></i>
                                        <span class="font-medium text-gray-900">Set Stock</span>
                                    </div>
                                    <p class="text-sm text-gray-600 ml-6">Set exact quantity (physical count correction)</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Quantity -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <span id="quantityLabel">Quantity *</span>
                        </label>
                        <input type="number" name="quantity" required min="1"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Enter quantity" id="quantityInput">
                        <p class="text-xs text-gray-500 mt-1" id="quantityHelp">Enter the quantity to adjust</p>
                    </div>

                    <!-- Preview -->
                    <div class="bg-gray-50 rounded-lg p-4" id="previewSection" style="display: none;">
                        <h4 class="font-medium text-gray-900 mb-3">Preview</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Current Stock:</span>
                                <span class="font-medium" id="previewCurrent"><?= $part['stock_quantity'] ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600" id="previewOperationLabel">Adjustment:</span>
                                <span class="font-medium" id="previewOperation">-</span>
                            </div>
                            <div class="flex justify-between border-t pt-2">
                                <span class="text-gray-600">New Stock:</span>
                                <span class="font-bold text-lg" id="previewNew">-</span>
                            </div>
                        </div>

                        <!-- Stock alert preview -->
                        <div class="mt-3" id="previewAlert" style="display: none;">
                            <div class="bg-red-100 border border-red-300 text-red-700 px-3 py-2 rounded text-sm">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                <span id="previewAlertText">Warning message</span>
                            </div>
                        </div>
                    </div>

                    <!-- Reason/Notes -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Reason/Notes</label>
                        <textarea name="notes" rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                  placeholder="Enter reason for stock adjustment (optional but recommended)"></textarea>
                        <p class="text-xs text-gray-500 mt-1">Good practice: Always document the reason for stock changes</p>
                    </div>

                    <!-- Quick Reason Buttons -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Quick Reasons</label>
                        <div class="grid grid-cols-2 gap-2">
                            <button type="button" onclick="setReason('New stock received')" class="text-left p-2 border border-gray-200 rounded hover:bg-gray-50 text-sm">
                                <i class="fas fa-truck text-green-600 mr-2"></i>New stock received
                            </button>
                            <button type="button" onclick="setReason('Damaged item removed')" class="text-left p-2 border border-gray-200 rounded hover:bg-gray-50 text-sm">
                                <i class="fas fa-exclamation-triangle text-orange-600 mr-2"></i>Damaged item
                            </button>
                            <button type="button" onclick="setReason('Used for repair')" class="text-left p-2 border border-gray-200 rounded hover:bg-gray-50 text-sm">
                                <i class="fas fa-wrench text-blue-600 mr-2"></i>Used for repair
                            </button>
                            <button type="button" onclick="setReason('Physical count correction')" class="text-left p-2 border border-gray-200 rounded hover:bg-gray-50 text-sm">
                                <i class="fas fa-clipboard-check text-purple-600 mr-2"></i>Count correction
                            </button>
                            <button type="button" onclick="setReason('Lost or stolen')" class="text-left p-2 border border-gray-200 rounded hover:bg-gray-50 text-sm">
                                <i class="fas fa-question text-red-600 mr-2"></i>Lost/stolen
                            </button>
                            <button type="button" onclick="setReason('Returned by customer')" class="text-left p-2 border border-gray-200 rounded hover:bg-gray-50 text-sm">
                                <i class="fas fa-undo text-indigo-600 mr-2"></i>Customer return
                            </button>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                        <a href="/admin/parts/<?= $part['id'] ?>"
                           class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </a>
                        <button type="submit"
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium"
                                id="submitBtn">
                            <i class="fas fa-check mr-2"></i>Adjust Stock
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Recent Stock Movements (if implemented) -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Recent Stock Movements</h3>
            </div>
            <div class="p-6">
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-history text-4xl text-gray-300 mb-4"></i>
                    <h4 class="text-lg font-medium text-gray-900 mb-2">No Movement History</h4>
                    <p class="text-gray-600">Stock movements will appear here when inventory is adjusted</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        const currentStock = <?= $part['stock_quantity'] ?>;
        const minStock = <?= $part['min_stock'] ?>;

        // Update preview when inputs change
        function updatePreview() {
            const adjustmentType = document.querySelector('input[name="adjustment_type"]:checked');
            const quantity = parseInt(document.getElementById('quantityInput').value) || 0;

            if (!adjustmentType || quantity <= 0) {
                document.getElementById('previewSection').style.display = 'none';
                return;
            }

            document.getElementById('previewSection').style.display = 'block';

            let newStock = currentStock;
            let operationText = '';
            let operationLabel = '';

            switch (adjustmentType.value) {
                case 'add':
                    newStock = currentStock + quantity;
                    operationText = `+${quantity}`;
                    operationLabel = 'Adding:';
                    break;
                case 'subtract':
                    newStock = Math.max(0, currentStock - quantity);
                    operationText = `-${quantity}`;
                    operationLabel = 'Removing:';
                    break;
                case 'set':
                    newStock = quantity;
                    operationText = `Set to ${quantity}`;
                    operationLabel = 'Setting to:';
                    break;
            }

            document.getElementById('previewOperationLabel').textContent = operationLabel;
            document.getElementById('previewOperation').textContent = operationText;
            document.getElementById('previewNew').textContent = newStock;

            // Update color based on stock level
            const previewNew = document.getElementById('previewNew');
            if (newStock <= minStock) {
                previewNew.className = 'font-bold text-lg text-red-600';
            } else {
                previewNew.className = 'font-bold text-lg text-green-600';
            }

            // Show alerts
            const alertDiv = document.getElementById('previewAlert');
            const alertText = document.getElementById('previewAlertText');

            if (newStock <= minStock && newStock > 0) {
                alertDiv.style.display = 'block';
                alertText.textContent = 'New stock level will be below minimum stock!';
            } else if (newStock === 0) {
                alertDiv.style.display = 'block';
                alertText.textContent = 'This will result in zero stock!';
            } else if (adjustmentType.value === 'subtract' && quantity > currentStock) {
                alertDiv.style.display = 'block';
                alertText.textContent = 'Cannot remove more than current stock. Stock will be set to 0.';
            } else {
                alertDiv.style.display = 'none';
            }
        }

        // Update labels based on adjustment type
        function updateLabels() {
            const adjustmentType = document.querySelector('input[name="adjustment_type"]:checked');
            const quantityLabel = document.getElementById('quantityLabel');
            const quantityHelp = document.getElementById('quantityHelp');
            const quantityInput = document.getElementById('quantityInput');

            if (!adjustmentType) return;

            switch (adjustmentType.value) {
                case 'add':
                    quantityLabel.textContent = 'Quantity to Add *';
                    quantityHelp.textContent = 'Enter how many units to add to current stock';
                    quantityInput.placeholder = 'Enter quantity to add';
                    break;
                case 'subtract':
                    quantityLabel.textContent = 'Quantity to Remove *';
                    quantityHelp.textContent = 'Enter how many units to remove from current stock';
                    quantityInput.placeholder = 'Enter quantity to remove';
                    quantityInput.max = currentStock;
                    break;
                case 'set':
                    quantityLabel.textContent = 'New Stock Quantity *';
                    quantityHelp.textContent = 'Enter the exact stock quantity after physical count';
                    quantityInput.placeholder = 'Enter exact quantity';
                    quantityInput.removeAttribute('max');
                    break;
            }
        }

        // Set quick reason
        function setReason(reason) {
            document.querySelector('textarea[name="notes"]').value = reason;
        }

        // Event listeners
        document.querySelectorAll('input[name="adjustment_type"]').forEach(radio => {
            radio.addEventListener('change', function() {
                updateLabels();
                updatePreview();
            });
        });

        document.getElementById('quantityInput').addEventListener('input', updatePreview);

        // Form validation
        document.getElementById('adjustStockForm').addEventListener('submit', function(e) {
            const adjustmentType = document.querySelector('input[name="adjustment_type"]:checked');
            const quantity = parseInt(document.getElementById('quantityInput').value) || 0;

            if (!adjustmentType) {
                alert('Please select an adjustment type.');
                e.preventDefault();
                return;
            }

            if (quantity <= 0) {
                alert('Please enter a valid quantity greater than 0.');
                e.preventDefault();
                return;
            }

            if (adjustmentType.value === 'subtract' && quantity > currentStock) {
                if (!confirm(`You are trying to remove ${quantity} units but only ${currentStock} units are available. This will set stock to 0. Continue?`)) {
                    e.preventDefault();
                    return;
                }
            }

            let newStock = currentStock;
            switch (adjustmentType.value) {
                case 'add':
                    newStock = currentStock + quantity;
                    break;
                case 'subtract':
                    newStock = Math.max(0, currentStock - quantity);
                    break;
                case 'set':
                    newStock = quantity;
                    break;
            }

            if (newStock <= minStock) {
                if (!confirm(`Warning: New stock level (${newStock}) will be at or below minimum stock level (${minStock}). Continue?`)) {
                    e.preventDefault();
                    return;
                }
            }

            // Show loading state
            const submitBtn = document.getElementById('submitBtn');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
            submitBtn.disabled = true;
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Number keys to select adjustment type
            if (e.key === '1') {
                document.querySelector('input[value="add"]').checked = true;
                updateLabels();
                updatePreview();
            } else if (e.key === '2') {
                document.querySelector('input[value="subtract"]').checked = true;
                updateLabels();
                updatePreview();
            } else if (e.key === '3') {
                document.querySelector('input[value="set"]').checked = true;
                updateLabels();
                updatePreview();
            }
        });

        // Auto-focus quantity input when adjustment type is selected
        document.querySelectorAll('input[name="adjustment_type"]').forEach(radio => {
            radio.addEventListener('change', function() {
                setTimeout(() => {
                    document.getElementById('quantityInput').focus();
                }, 100);
            });
        });

        // Quick quantity buttons for common adjustments
        function addQuickQuantityButtons() {
            const quantityDiv = document.getElementById('quantityInput').parentElement;
            const quickButtons = document.createElement('div');
            quickButtons.className = 'mt-2 flex flex-wrap gap-2';
            quickButtons.innerHTML = `
                <button type="button" onclick="setQuantity(1)" class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-sm hover:bg-gray-200">1</button>
                <button type="button" onclick="setQuantity(5)" class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-sm hover:bg-gray-200">5</button>
                <button type="button" onclick="setQuantity(10)" class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-sm hover:bg-gray-200">10</button>
                <button type="button" onclick="setQuantity(25)" class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-sm hover:bg-gray-200">25</button>
                <button type="button" onclick="setQuantity(50)" class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-sm hover:bg-gray-200">50</button>
                <button type="button" onclick="setQuantity(100)" class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-sm hover:bg-gray-200">100</button>
            `;
            quantityDiv.appendChild(quickButtons);
        }

        function setQuantity(value) {
            document.getElementById('quantityInput').value = value;
            updatePreview();
        }

        // Initialize quick quantity buttons
        document.addEventListener('DOMContentLoaded', function() {
            addQuickQuantityButtons();
        });
    </script>
<?= $this->endSection() ?>
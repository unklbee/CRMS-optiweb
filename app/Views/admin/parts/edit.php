<?= $this->extend('admin/layout/main') ?>

<?= $this->section('content') ?>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit Part</h1>
                <p class="text-gray-600"><?= $part['part_number'] ?> - <?= $part['name'] ?></p>
            </div>
            <div class="flex space-x-2">
                <a href="/admin/parts/<?= $part['id'] ?>" class="text-gray-600 hover:text-gray-900 px-4 py-2">
                    <i class="fas fa-eye mr-2"></i>View Details
                </a>
                <a href="/admin/parts" class="text-gray-600 hover:text-gray-900 px-4 py-2">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Parts
                </a>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <?php if (session()->getFlashdata('errors')): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                    <ul class="list-disc list-inside space-y-1">
                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="/admin/parts/<?= $part['id'] ?>" method="POST" class="space-y-6" id="editPartForm">
                <?= csrf_field() ?>
                <input type="hidden" name="_method" value="PUT">

                <!-- Basic Information -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Basic Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Part Number *</label>
                            <input type="text" name="part_number" value="<?= old('part_number', $part['part_number']) ?>" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="e.g., LCD001, KB001, BAT001">
                            <p class="text-xs text-gray-500 mt-1">Unique identifier for this part</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Part Name *</label>
                            <input type="text" name="name" value="<?= old('name', $part['name']) ?>" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="e.g., Laptop LCD Screen 15.6 inch">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                            <div class="flex space-x-2">
                                <select name="category" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Select Category</option>
                                    <?php if (!empty($categories)): ?>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?= $category['category'] ?>" <?= $part['category'] == $category['category'] ? 'selected' : '' ?>>
                                                <?= $category['category'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                    <option value="Display" <?= $part['category'] == 'Display' ? 'selected' : '' ?>>Display</option>
                                    <option value="Input" <?= $part['category'] == 'Input' ? 'selected' : '' ?>>Input</option>
                                    <option value="Power" <?= $part['category'] == 'Power' ? 'selected' : '' ?>>Power</option>
                                    <option value="Memory" <?= $part['category'] == 'Memory' ? 'selected' : '' ?>>Memory</option>
                                    <option value="Storage" <?= $part['category'] == 'Storage' ? 'selected' : '' ?>>Storage</option>
                                    <option value="Cooling" <?= $part['category'] == 'Cooling' ? 'selected' : '' ?>>Cooling</option>
                                    <option value="Motherboard" <?= $part['category'] == 'Motherboard' ? 'selected' : '' ?>>Motherboard</option>
                                </select>
                                <input type="text" id="newCategory" placeholder="New category"
                                       class="w-32 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                                <button type="button" onclick="addNewCategory()" class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Brand</label>
                            <input type="text" name="brand" value="<?= old('brand', $part['brand']) ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="e.g., Samsung, Kingston, Generic">
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Detailed description of the part, compatibility, specifications..."><?= old('description', $part['description']) ?></textarea>
                </div>

                <!-- Pricing Information -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Pricing Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Cost Price (Rp) *</label>
                            <input type="number" name="cost_price" value="<?= old('cost_price', $part['cost_price']) ?>" required min="0" step="1000"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="0">
                            <p class="text-xs text-gray-500 mt-1">Your purchase/cost price</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Selling Price (Rp) *</label>
                            <input type="number" name="selling_price" value="<?= old('selling_price', $part['selling_price']) ?>" required min="0" step="1000"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="0">
                            <p class="text-xs text-gray-500 mt-1">Price charged to customers</p>
                        </div>
                    </div>

                    <!-- Current Profit Calculation -->
                    <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div>
                                <span class="text-gray-600">Current Profit:</span>
                                <span class="font-medium text-green-600"><?= format_currency($part['selling_price'] - $part['cost_price']) ?></span>
                            </div>
                            <div>
                                <span class="text-gray-600">Current Margin:</span>
                                <span class="font-medium text-green-600">
                                    <?= $part['selling_price'] > 0 ? number_format(($part['selling_price'] - $part['cost_price']) / $part['selling_price'] * 100, 1) : 0 ?>%
                                </span>
                            </div>
                            <div>
                                <span class="text-gray-600">Current Markup:</span>
                                <span class="font-medium text-blue-600">
                                    <?= $part['cost_price'] > 0 ? number_format(($part['selling_price'] - $part['cost_price']) / $part['cost_price'] * 100, 1) : 0 ?>%
                                </span>
                            </div>
                        </div>

                        <!-- New Profit Calculation -->
                        <div class="mt-3 pt-3 border-t border-gray-200">
                            <h5 class="text-sm font-medium text-gray-700 mb-2">New Calculation:</h5>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-600">Profit Amount:</span>
                                    <span class="font-medium text-green-600" id="profitAmount">Rp 0</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Profit Margin:</span>
                                    <span class="font-medium text-green-600" id="profitMargin">0%</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Markup:</span>
                                    <span class="font-medium text-blue-600" id="markup">0%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Inventory Information -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Inventory Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Current Stock *</label>
                            <div class="relative">
                                <input type="number" name="stock_quantity" value="<?= old('stock_quantity', $part['stock_quantity']) ?>" required min="0"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <a href="/admin/parts/<?= $part['id'] ?>/adjust-stock" class="text-blue-600 hover:text-blue-800 text-sm">
                                        <i class="fas fa-plus-minus"></i>
                                    </a>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Current quantity in stock</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Minimum Stock *</label>
                            <input type="number" name="min_stock" value="<?= old('min_stock', $part['min_stock']) ?>" required min="1"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <p class="text-xs text-gray-500 mt-1">Alert when stock falls below this</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Storage Location</label>
                            <input type="text" name="location" value="<?= old('location', $part['location']) ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="e.g., A1-01, Shelf B2, Storage Room">
                            <p class="text-xs text-gray-500 mt-1">Where this part is stored</p>
                        </div>
                    </div>

                    <!-- Stock Alert -->
                    <div class="mt-4 p-4 border border-orange-200 bg-orange-50 rounded-lg" id="stockAlert" style="<?= $part['stock_quantity'] <= $part['min_stock'] ? '' : 'display: none;' ?>">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle text-orange-600 mr-2"></i>
                            <span class="text-orange-800 text-sm font-medium">Low Stock Warning</span>
                        </div>
                        <p class="text-orange-700 text-sm mt-1">Current stock is below minimum stock level.</p>
                    </div>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                    <select name="status" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent md:w-48">
                        <option value="active" <?= $part['status'] == 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= $part['status'] == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Only active parts are available for use in orders</p>
                </div>

                <!-- Part Usage Statistics -->
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Part Statistics</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-blue-600"><?= $part['times_used'] ?? 0 ?></p>
                            <p class="text-sm text-gray-600">Times Used</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-green-600"><?= format_currency($part['stock_quantity'] * $part['cost_price']) ?></p>
                            <p class="text-sm text-gray-600">Stock Value (Cost)</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-purple-600"><?= format_currency($part['stock_quantity'] * $part['selling_price']) ?></p>
                            <p class="text-sm text-gray-600">Stock Value (Selling)</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-orange-600"><?= format_currency($part['revenue_generated'] ?? 0) ?></p>
                            <p class="text-sm text-gray-600">Revenue Generated</p>
                        </div>
                    </div>
                </div>

                <!-- Changelog Info -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="font-medium text-blue-800 mb-2">
                        <i class="fas fa-info-circle mr-2"></i>Part Information
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-blue-700">
                        <div>
                            <span class="font-medium">Created:</span> <?= date('M d, Y H:i', strtotime($part['created_at'])) ?>
                        </div>
                        <div>
                            <span class="font-medium">Last Updated:</span> <?= date('M d, Y H:i', strtotime($part['updated_at'])) ?>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                    <!-- Left side - Additional actions -->
                    <div class="flex space-x-2">
                        <a href="/admin/parts/<?= $part['id'] ?>/adjust-stock"
                           class="px-4 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors text-sm">
                            <i class="fas fa-plus-minus mr-2"></i>Adjust Stock
                        </a>
                        <button type="button" onclick="duplicatePart()"
                                class="px-4 py-2 bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200 transition-colors text-sm">
                            <i class="fas fa-copy mr-2"></i>Duplicate
                        </button>
                    </div>

                    <!-- Right side - Main actions -->
                    <div class="flex space-x-4">
                        <a href="/admin/parts"
                           class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </a>
                        <button type="button" onclick="saveDraft()"
                                class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                            <i class="fas fa-save mr-2"></i>Save Draft
                        </button>
                        <button type="submit"
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                            <i class="fas fa-check mr-2"></i>Update Part
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Add new category functionality
        function addNewCategory() {
            const newCategory = document.getElementById('newCategory').value.trim();
            if (newCategory) {
                const select = document.querySelector('select[name="category"]');
                const option = new Option(newCategory, newCategory, true, true);
                select.add(option);
                document.getElementById('newCategory').value = '';
            }
        }

        // Calculate profit margins
        function calculateProfit() {
            const costPrice = parseFloat(document.querySelector('input[name="cost_price"]').value) || 0;
            const sellingPrice = parseFloat(document.querySelector('input[name="selling_price"]').value) || 0;

            const profit = sellingPrice - costPrice;
            const profitMargin = sellingPrice > 0 ? (profit / sellingPrice * 100) : 0;
            const markup = costPrice > 0 ? (profit / costPrice * 100) : 0;

            document.getElementById('profitAmount').textContent = 'Rp ' + profit.toLocaleString('id-ID');
            document.getElementById('profitMargin').textContent = profitMargin.toFixed(1) + '%';
            document.getElementById('markup').textContent = markup.toFixed(1) + '%';

            // Color coding
            const profitColor = profit >= 0 ? 'text-green-600' : 'text-red-600';
            document.getElementById('profitAmount').className = 'font-medium ' + profitColor;
            document.getElementById('profitMargin').className = 'font-medium ' + profitColor;
        }

        // Check stock levels
        function checkStockLevels() {
            const currentStock = parseInt(document.querySelector('input[name="stock_quantity"]').value) || 0;
            const minStock = parseInt(document.querySelector('input[name="min_stock"]').value) || 0;
            const alertDiv = document.getElementById('stockAlert');

            if (currentStock < minStock) {
                alertDiv.style.display = 'block';
            } else {
                alertDiv.style.display = 'none';
            }
        }

        // Event listeners
        document.querySelector('input[name="cost_price"]').addEventListener('input', calculateProfit);
        document.querySelector('input[name="selling_price"]').addEventListener('input', calculateProfit);
        document.querySelector('input[name="stock_quantity"]').addEventListener('input', checkStockLevels);
        document.querySelector('input[name="min_stock"]').addEventListener('input', checkStockLevels);

        // Initialize calculations
        document.addEventListener('DOMContentLoaded', function() {
            calculateProfit();
            checkStockLevels();
        });

        // Form validation
        document.getElementById('editPartForm').addEventListener('submit', function(e) {
            const partNumber = document.querySelector('input[name="part_number"]').value.trim();
            const name = document.querySelector('input[name="name"]').value.trim();
            const costPrice = parseFloat(document.querySelector('input[name="cost_price"]').value);
            const sellingPrice = parseFloat(document.querySelector('input[name="selling_price"]').value);

            if (partNumber.length < 2) {
                alert('Part number must be at least 2 characters long.');
                e.preventDefault();
                return;
            }

            if (name.length < 2) {
                alert('Part name must be at least 2 characters long.');
                e.preventDefault();
                return;
            }

            if (costPrice <= 0) {
                alert('Cost price must be greater than 0.');
                e.preventDefault();
                return;
            }

            if (sellingPrice <= 0) {
                alert('Selling price must be greater than 0.');
                e.preventDefault();
                return;
            }

            if (sellingPrice < costPrice) {
                if (!confirm('Selling price is lower than cost price. This will result in a loss. Continue?')) {
                    e.preventDefault();
                    return;
                }
            }

            // Show loading state
            const submitBtn = e.target.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Updating Part...';
            submitBtn.disabled = true;
        });

        // Draft functionality
        function saveDraft() {
            const formData = {
                part_number: document.querySelector('input[name="part_number"]').value,
                name: document.querySelector('input[name="name"]').value,
                category: document.querySelector('select[name="category"]').value,
                brand: document.querySelector('input[name="brand"]').value,
                description: document.querySelector('textarea[name="description"]').value,
                cost_price: document.querySelector('input[name="cost_price"]').value,
                selling_price: document.querySelector('input[name="selling_price"]').value,
                stock_quantity: document.querySelector('input[name="stock_quantity"]').value,
                min_stock: document.querySelector('input[name="min_stock"]').value,
                location: document.querySelector('input[name="location"]').value,
                status: document.querySelector('select[name="status"]').value,
                timestamp: new Date().toISOString()
            };

            localStorage.setItem('part_edit_draft_<?= $part['id'] ?>', JSON.stringify(formData));

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

        // Duplicate part functionality
        function duplicatePart() {
            if (confirm('Create a copy of this part?')) {
                window.location.href = '/admin/parts/new?duplicate=<?= $part['id'] ?>';
            }
        }

        // Load draft on page load
        document.addEventListener('DOMContentLoaded', function() {
            const draft = localStorage.getItem('part_edit_draft_<?= $part['id'] ?>');
            if (draft) {
                const draftData = JSON.parse(draft);
                const draftAge = new Date() - new Date(draftData.timestamp);

                // Only restore if draft is less than 24 hours old
                if (draftAge < 24 * 60 * 60 * 1000) {
                    if (confirm('Found a saved draft. Would you like to restore it?')) {
                        Object.keys(draftData).forEach(key => {
                            if (key !== 'timestamp') {
                                const field = document.querySelector(`[name="${key}"]`);
                                if (field) {
                                    field.value = draftData[key];
                                }
                            }
                        });

                        calculateProfit();
                        checkStockLevels();
                    } else {
                        localStorage.removeItem('part_edit_draft_<?= $part['id'] ?>');
                    }
                } else {
                    localStorage.removeItem('part_edit_draft_<?= $part['id'] ?>');
                }
            }
        });

        // Clear draft on successful form submission
        document.getElementById('editPartForm').addEventListener('submit', function() {
            localStorage.removeItem('part_edit_draft_<?= $part['id'] ?>');
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl/Cmd + S to save draft
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
                saveDraft();
            }

            // Ctrl/Cmd + Enter to submit
            if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('editPartForm').submit();
            }
        });

        // Enter key on new category input
        document.getElementById('newCategory').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                addNewCategory();
            }
        });

        // Track changes for unsaved changes warning
        let formChanged = false;
        const form = document.getElementById('editPartForm');
        const inputs = form.querySelectorAll('input, select, textarea');

        inputs.forEach(input => {
            input.addEventListener('change', () => {
                formChanged = true;
            });
        });

        // Warn user about unsaved changes
        window.addEventListener('beforeunload', function(e) {
            if (formChanged) {
                e.preventDefault();
                e.returnValue = '';
                return 'You have unsaved changes. Are you sure you want to leave?';
            }
        });

        // Reset flag when form is submitted
        form.addEventListener('submit', () => {
            formChanged = false;
        });
    </script>
<?= $this->endSection() ?>
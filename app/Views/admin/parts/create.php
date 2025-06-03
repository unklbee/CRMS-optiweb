<?= $this->extend('admin/layout/main') ?>

<?= $this->section('content') ?>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Add New Part</h1>
                <p class="text-gray-600">Add a new spare part to your inventory</p>
            </div>
            <a href="/admin/parts" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left mr-2"></i>Back to Parts
            </a>
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

            <form action="/admin/parts" method="POST" class="space-y-6" id="partForm">
                <?= csrf_field() ?>

                <!-- Basic Information -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Basic Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Part Number *</label>
                            <input type="text" name="part_number" value="<?= old('part_number') ?>" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="e.g., LCD001, KB001, BAT001">
                            <p class="text-xs text-gray-500 mt-1">Unique identifier for this part</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Part Name *</label>
                            <input type="text" name="name" value="<?= old('name') ?>" required
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
                                            <option value="<?= $category['category'] ?>" <?= old('category') == $category['category'] ? 'selected' : '' ?>>
                                                <?= $category['category'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                    <option value="Display" <?= old('category') == 'Display' ? 'selected' : '' ?>>Display</option>
                                    <option value="Input" <?= old('category') == 'Input' ? 'selected' : '' ?>>Input</option>
                                    <option value="Power" <?= old('category') == 'Power' ? 'selected' : '' ?>>Power</option>
                                    <option value="Memory" <?= old('category') == 'Memory' ? 'selected' : '' ?>>Memory</option>
                                    <option value="Storage" <?= old('category') == 'Storage' ? 'selected' : '' ?>>Storage</option>
                                    <option value="Cooling" <?= old('category') == 'Cooling' ? 'selected' : '' ?>>Cooling</option>
                                    <option value="Motherboard" <?= old('category') == 'Motherboard' ? 'selected' : '' ?>>Motherboard</option>
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
                            <input type="text" name="brand" value="<?= old('brand') ?>"
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
                              placeholder="Detailed description of the part, compatibility, specifications..."><?= old('description') ?></textarea>
                </div>

                <!-- Pricing Information -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Pricing Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Cost Price (Rp) *</label>
                            <input type="number" name="cost_price" value="<?= old('cost_price') ?>" required min="0" step="1000"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="0">
                            <p class="text-xs text-gray-500 mt-1">Your purchase/cost price</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Selling Price (Rp) *</label>
                            <input type="number" name="selling_price" value="<?= old('selling_price') ?>" required min="0" step="1000"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="0">
                            <p class="text-xs text-gray-500 mt-1">Price charged to customers</p>
                        </div>
                    </div>

                    <!-- Profit Calculation -->
                    <div class="mt-4 p-4 bg-gray-50 rounded-lg">
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

                <!-- Inventory Information -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Inventory Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Current Stock *</label>
                            <input type="number" name="stock_quantity" value="<?= old('stock_quantity', 0) ?>" required min="0"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <p class="text-xs text-gray-500 mt-1">Current quantity in stock</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Minimum Stock *</label>
                            <input type="number" name="min_stock" value="<?= old('min_stock', 5) ?>" required min="1"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <p class="text-xs text-gray-500 mt-1">Alert when stock falls below this</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Storage Location</label>
                            <input type="text" name="location" value="<?= old('location') ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="e.g., A1-01, Shelf B2, Storage Room">
                            <p class="text-xs text-gray-500 mt-1">Where this part is stored</p>
                        </div>
                    </div>

                    <!-- Stock Alert -->
                    <div class="mt-4 p-4 border border-orange-200 bg-orange-50 rounded-lg" id="stockAlert" style="display: none;">
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
                        <option value="active" <?= old('status', 'active') == 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= old('status') == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Only active parts are available for use in orders</p>
                </div>

                <!-- Quick Templates -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Templates</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        <?php
                        $templates = [
                            ['name' => 'Laptop LCD Screen', 'part_number' => 'LCD###', 'category' => 'Display', 'cost' => 400000, 'selling' => 600000],
                            ['name' => 'Laptop Keyboard', 'part_number' => 'KB###', 'category' => 'Input', 'cost' => 80000, 'selling' => 120000],
                            ['name' => 'Laptop Battery', 'part_number' => 'BAT###', 'category' => 'Power', 'cost' => 200000, 'selling' => 300000],
                            ['name' => 'DDR4 RAM 8GB', 'part_number' => 'RAM###', 'category' => 'Memory', 'cost' => 450000, 'selling' => 600000],
                            ['name' => 'SSD 256GB', 'part_number' => 'SSD###', 'category' => 'Storage', 'cost' => 550000, 'selling' => 750000],
                            ['name' => 'Cooling Fan', 'part_number' => 'FAN###', 'category' => 'Cooling', 'cost' => 100000, 'selling' => 180000]
                        ];
                        foreach ($templates as $index => $template): ?>
                            <button type="button" onclick="useTemplate(<?= $index ?>)"
                                    class="text-left p-3 border border-gray-200 rounded-lg hover:border-blue-300 hover:bg-blue-50 transition-colors template-btn">
                                <div class="font-medium text-gray-900 text-sm"><?= $template['name'] ?></div>
                                <div class="text-xs text-gray-500 mt-1">
                                    <?= $template['category'] ?> • <?= format_currency($template['selling']) ?>
                                </div>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
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
                        <i class="fas fa-plus mr-2"></i>Add Part
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Templates data
        const templates = [
            {name: 'Laptop LCD Screen', part_number: 'LCD###', category: 'Display', cost: 400000, selling: 600000, description: 'Compatible with various laptop models', brand: 'Generic'},
            {name: 'Laptop Keyboard', part_number: 'KB###', category: 'Input', cost: 80000, selling: 120000, description: 'Standard US QWERTY keyboard', brand: 'Generic'},
            {name: 'Laptop Battery', part_number: 'BAT###', category: 'Power', cost: 200000, selling: 300000, description: 'High capacity lithium-ion battery', brand: 'Generic'},
            {name: 'DDR4 RAM 8GB', part_number: 'RAM###', category: 'Memory', cost: 450000, selling: 600000, description: '8GB DDR4 2666MHz memory module', brand: 'Kingston'},
            {name: 'SSD 256GB', part_number: 'SSD###', category: 'Storage', cost: 550000, selling: 750000, description: '256GB SATA SSD drive', brand: 'Samsung'},
            {name: 'Cooling Fan', part_number: 'FAN###', category: 'Cooling', cost: 100000, selling: 180000, description: 'CPU cooling fan assembly', brand: 'Generic'}
        ];

        // Use template functionality
        function useTemplate(index) {
            const template = templates[index];
            document.querySelector('input[name="name"]').value = template.name;
            document.querySelector('input[name="part_number"]').value = template.part_number;
            document.querySelector('select[name="category"]').value = template.category;
            document.querySelector('input[name="brand"]').value = template.brand;
            document.querySelector('input[name="cost_price"]').value = template.cost;
            document.querySelector('input[name="selling_price"]').value = template.selling;
            document.querySelector('textarea[name="description"]').value = template.description;

            calculateProfit();

            // Highlight selected template
            document.querySelectorAll('.template-btn').forEach(btn => {
                btn.classList.remove('border-blue-500', 'bg-blue-50');
                btn.classList.add('border-gray-200');
            });

            event.target.closest('.template-btn').classList.add('border-blue-500', 'bg-blue-50');
            event.target.closest('.template-btn').classList.remove('border-gray-200');
        }

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

        // Auto-suggest part numbers
        document.querySelector('select[name="category"]').addEventListener('change', function() {
            const category = this.value;
            const partNumberField = document.querySelector('input[name="part_number"]');

            if (category && !partNumberField.value) {
                const prefix = category.substring(0, 3).toUpperCase();
                const randomNum = Math.floor(Math.random() * 999) + 1;
                partNumberField.value = prefix + String(randomNum).padStart(3, '0');
            }
        });

        // Form validation
        document.getElementById('partForm').addEventListener('submit', function(e) {
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
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Adding Part...';
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

            localStorage.setItem('part_create_draft', JSON.stringify(formData));

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

        // Load draft on page load
        document.addEventListener('DOMContentLoaded', function() {
            const draft = localStorage.getItem('part_create_draft');
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
                        localStorage.removeItem('part_create_draft');
                    }
                } else {
                    localStorage.removeItem('part_create_draft');
                }
            }
        });

        // Clear draft on successful form submission
        document.getElementById('partForm').addEventListener('submit', function() {
            localStorage.removeItem('part_create_draft');
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
                document.getElementById('partForm').submit();
            }
        });

        // Enter key on new category input
        document.getElementById('newCategory').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                addNewCategory();
            }
        });
    </script>
<?= $this->endSection() ?>
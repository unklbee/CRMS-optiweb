<?= $this->extend('admin/layout/main') ?>

<?= $this->section('content') ?>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Manage Order Parts</h1>
                <p class="text-gray-600">Order #<?= $order['order_number'] ?> - Add or remove parts</p>
            </div>
            <a href="/admin/orders/<?= $order['id'] ?>" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left mr-2"></i>Back to Order
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Add New Part -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Add Part to Order</h3>

                <?php if (session()->getFlashdata('errors')): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                        <ul class="list-disc list-inside space-y-1">
                            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                <li><?= $error ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="/admin/orders/<?= $order['id'] ?>/parts" method="POST" class="space-y-4" id="addPartForm">
                    <?= csrf_field() ?>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Part *</label>
                        <select name="part_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="partSelect">
                            <option value="">Choose a part...</option>
                            <?php foreach ($available_parts as $part): ?>
                                <option value="<?= $part['id'] ?>"
                                        data-stock="<?= $part['stock_quantity'] ?>"
                                        data-price="<?= $part['selling_price'] ?>"
                                        data-cost="<?= $part['cost_price'] ?>"
                                        data-name="<?= $part['name'] ?>"
                                        data-number="<?= $part['part_number'] ?>">
                                    <?= $part['name'] ?> (<?= $part['part_number'] ?>) - Stock: <?= $part['stock_quantity'] ?> - <?= format_currency($part['selling_price']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Part Info Display -->
                    <div id="partInfo" class="hidden bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h4 class="font-medium text-blue-900 mb-2">Part Information</h4>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-blue-700">Available Stock:</span>
                                <span class="font-medium" id="availableStock">-</span>
                            </div>
                            <div>
                                <span class="text-blue-700">Suggested Price:</span>
                                <span class="font-medium" id="suggestedPrice">-</span>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Quantity *</label>
                            <input type="number" name="quantity" required min="1"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   id="quantityInput">
                            <p class="text-xs text-gray-500 mt-1" id="stockWarning"></p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Unit Price *</label>
                            <input type="number" name="unit_price" required step="0.01"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   id="unitPriceInput">
                        </div>
                    </div>

                    <!-- Total Calculation -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-700">Total Price:</span>
                            <span class="font-bold text-lg" id="totalPrice">Rp 0</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                        <textarea name="notes" rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                  placeholder="Optional notes about this part usage..."></textarea>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Add Part to Order
                    </button>
                </form>
            </div>

            <!-- Current Parts in Order -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Current Parts in Order</h3>
                </div>
                <div class="p-6">
                    <?php if (!empty($order_parts)): ?>
                        <div class="space-y-4">
                            <?php
                            $totalOrderPartsPrice = 0;
                            foreach ($order_parts as $part):
                                $totalOrderPartsPrice += $part['total_price'];
                                ?>
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-box text-blue-600"></i>
                                            </div>
                                            <div>
                                                <h4 class="font-medium text-gray-900"><?= $part['part_name'] ?></h4>
                                                <p class="text-sm text-gray-600"><?= $part['part_number'] ?></p>
                                                <p class="text-xs text-gray-500">
                                                    Qty: <?= $part['quantity'] ?> × <?= format_currency($part['unit_price']) ?> = <?= format_currency($part['total_price']) ?>
                                                </p>
                                            </div>
                                        </div>
                                        <?php if ($part['notes']): ?>
                                            <div class="mt-2 text-sm text-gray-600 italic">
                                                <i class="fas fa-comment-alt mr-1"></i><?= $part['notes'] ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <button onclick="removePart(<?= $part['id'] ?>)"
                                            class="ml-4 p-2 text-red-600 hover:text-red-800 hover:bg-red-100 rounded-lg transition-colors">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            <?php endforeach; ?>

                            <!-- Total -->
                            <div class="border-t pt-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-medium text-gray-900">Total Parts Cost:</span>
                                    <span class="text-lg font-bold text-green-600"><?= format_currency($totalOrderPartsPrice) ?></span>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-8">
                            <i class="fas fa-box-open text-4xl text-gray-300 mb-4"></i>
                            <h4 class="text-lg font-medium text-gray-900 mb-2">No Parts Added</h4>
                            <p class="text-gray-600">Add parts to this order using the form on the left</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Available Parts Browser -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800">Available Parts</h3>
                    <div class="flex items-center space-x-4">
                        <input type="text" id="searchParts" placeholder="Search parts..."
                               class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <select id="filterCategory" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">All Categories</option>
                            <?php
                            $categories = array_unique(array_column($available_parts, 'category'));
                            foreach ($categories as $category):
                                if ($category):
                                    ?>
                                    <option value="<?= $category ?>"><?= $category ?></option>
                                <?php
                                endif;
                            endforeach;
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" id="partsTable">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Part</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($available_parts as $part): ?>
                        <tr class="hover:bg-gray-50 part-row"
                            data-name="<?= strtolower($part['name']) ?>"
                            data-number="<?= strtolower($part['part_number']) ?>"
                            data-category="<?= $part['category'] ?>">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-box text-blue-600"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900"><?= $part['name'] ?></div>
                                        <div class="text-sm text-gray-500"><?= $part['part_number'] ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if ($part['category']): ?>
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            <?= $part['category'] ?>
                                        </span>
                                <?php else: ?>
                                    <span class="text-gray-400">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                        <span class="<?= $part['stock_quantity'] <= $part['min_stock'] ? 'text-red-600 font-bold' : '' ?>">
                                            <?= $part['stock_quantity'] ?>
                                        </span>
                                </div>
                                <?php if ($part['stock_quantity'] <= $part['min_stock']): ?>
                                    <div class="text-xs text-red-600">Low Stock</div>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?= format_currency($part['selling_price']) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button onclick="quickAddPart(<?= $part['id'] ?>, '<?= addslashes($part['name']) ?>', <?= $part['selling_price'] ?>, <?= $part['stock_quantity'] ?>)"
                                        class="text-blue-600 hover:text-blue-900 bg-blue-100 hover:bg-blue-200 px-3 py-1 rounded transition-colors">
                                    <i class="fas fa-plus mr-1"></i>Quick Add
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Part selection handling
        document.getElementById('partSelect').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const partInfo = document.getElementById('partInfo');
            const quantityInput = document.getElementById('quantityInput');
            const unitPriceInput = document.getElementById('unitPriceInput');

            if (selectedOption.value) {
                const stock = parseInt(selectedOption.dataset.stock);
                const price = parseFloat(selectedOption.dataset.price);

                document.getElementById('availableStock').textContent = stock;
                document.getElementById('suggestedPrice').textContent = 'Rp ' + price.toLocaleString('id-ID');

                unitPriceInput.value = price;
                quantityInput.max = stock;
                quantityInput.value = 1;

                partInfo.classList.remove('hidden');
                updateTotal();
                checkStock();
            } else {
                partInfo.classList.add('hidden');
                quantityInput.value = '';
                unitPriceInput.value = '';
                updateTotal();
            }
        });

        // Quantity and price change handling
        document.getElementById('quantityInput').addEventListener('input', function() {
            checkStock();
            updateTotal();
        });

        document.getElementById('unitPriceInput').addEventListener('input', updateTotal);

        function checkStock() {
            const partSelect = document.getElementById('partSelect');
            const selectedOption = partSelect.options[partSelect.selectedIndex];
            const quantityInput = document.getElementById('quantityInput');
            const stockWarning = document.getElementById('stockWarning');

            if (selectedOption.value) {
                const stock = parseInt(selectedOption.dataset.stock);
                const quantity = parseInt(quantityInput.value) || 0;

                if (quantity > stock) {
                    stockWarning.textContent = `Warning: Only ${stock} units available in stock`;
                    stockWarning.className = 'text-xs text-red-500 mt-1';
                    quantityInput.classList.add('border-red-500');
                } else if (quantity > 0) {
                    stockWarning.textContent = `${stock - quantity} units will remain in stock`;
                    stockWarning.className = 'text-xs text-gray-500 mt-1';
                    quantityInput.classList.remove('border-red-500');
                } else {
                    stockWarning.textContent = '';
                    quantityInput.classList.remove('border-red-500');
                }
            }
        }

        function updateTotal() {
            const quantity = parseInt(document.getElementById('quantityInput').value) || 0;
            const unitPrice = parseFloat(document.getElementById('unitPriceInput').value) || 0;
            const total = quantity * unitPrice;

            document.getElementById('totalPrice').textContent = 'Rp ' + total.toLocaleString('id-ID');
        }

        function removePart(orderPartId) {
            if (confirm('Are you sure you want to remove this part from the order? Stock will be restored.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/orders/<?= $order['id'] ?>/parts/${orderPartId}/remove`;
                form.innerHTML = `
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="DELETE">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        function quickAddPart(partId, partName, price, stock) {
            const partSelect = document.getElementById('partSelect');
            const quantityInput = document.getElementById('quantityInput');
            const unitPriceInput = document.getElementById('unitPriceInput');

            // Set the form values
            partSelect.value = partId;
            quantityInput.value = 1;
            quantityInput.max = stock;
            unitPriceInput.value = price;

            // Trigger change event to update UI
            partSelect.dispatchEvent(new Event('change'));

            // Scroll to form
            document.getElementById('addPartForm').scrollIntoView({ behavior: 'smooth' });
        }

        // Search and filter functionality
        document.getElementById('searchParts').addEventListener('input', filterParts);
        document.getElementById('filterCategory').addEventListener('change', filterParts);

        function filterParts() {
            const search = document.getElementById('searchParts').value.toLowerCase();
            const category = document.getElementById('filterCategory').value;
            const rows = document.querySelectorAll('.part-row');

            rows.forEach(row => {
                const name = row.dataset.name;
                const number = row.dataset.number;
                const rowCategory = row.dataset.category;

                const matchesSearch = !search || name.includes(search) || number.includes(search);
                const matchesCategory = !category || rowCategory === category;

                if (matchesSearch && matchesCategory) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // Form validation
        document.getElementById('addPartForm').addEventListener('submit', function(e) {
            const partSelect = document.getElementById('partSelect');
            const quantityInput = document.getElementById('quantityInput');
            const selectedOption = partSelect.options[partSelect.selectedIndex];

            if (selectedOption.value) {
                const stock = parseInt(selectedOption.dataset.stock);
                const quantity = parseInt(quantityInput.value) || 0;

                if (quantity > stock) {
                    e.preventDefault();
                    alert(`Cannot add ${quantity} units. Only ${stock} units available in stock.`);
                    return false;
                }

                if (quantity <= 0) {
                    e.preventDefault();
                    alert('Please enter a valid quantity greater than 0.');
                    return false;
                }
            }
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl/Cmd + Enter to submit form
            if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('addPartForm').submit();
            }
        });
    </script>
<?= $this->endSection() ?>
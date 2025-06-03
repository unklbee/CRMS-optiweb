<?= $this->extend('admin/layout/main') ?>

<?= $this->section('content') ?>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Part Details</h1>
                <p class="text-gray-600"><?= $part['part_number'] ?> - <?= $part['name'] ?></p>
            </div>
            <div class="flex space-x-2">
                <a href="/admin/parts/<?= $part['id'] ?>/edit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-edit mr-2"></i>Edit Part
                </a>
                <a href="/admin/parts/<?= $part['id'] ?>/adjust-stock" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-plus-minus mr-2"></i>Adjust Stock
                </a>
                <a href="/admin/parts" class="text-gray-600 hover:text-gray-900 px-4 py-2">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Parts
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Part Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Part Information</h3>

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

                        <?php if ($part['description']): ?>
                            <div>
                                <h4 class="font-medium text-gray-900 mb-2">Description</h4>
                                <p class="text-gray-600"><?= nl2br($part['description']) ?></p>
                            </div>
                        <?php endif; ?>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <h4 class="font-medium text-gray-900 mb-1">Category</h4>
                                <?php if ($part['category']): ?>
                                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                                        <?= $part['category'] ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-gray-500">Uncategorized</span>
                                <?php endif; ?>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900 mb-1">Status</h4>
                                <span class="px-3 py-1 rounded-full text-sm font-medium
                                    <?= $part['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' ?>">
                                    <?= ucfirst($part['status']) ?>
                                </span>
                            </div>
                        </div>

                        <?php if ($part['location']): ?>
                            <div>
                                <h4 class="font-medium text-gray-900 mb-1">Storage Location</h4>
                                <p class="text-gray-600"><?= $part['location'] ?></p>
                            </div>
                        <?php endif; ?>

                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-600">Added:</span>
                                <span class="font-medium"><?= date('M d, Y', strtotime($part['created_at'])) ?></span>
                            </div>
                            <div>
                                <span class="text-gray-600">Updated:</span>
                                <span class="font-medium"><?= date('M d, Y', strtotime($part['updated_at'])) ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pricing Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Pricing Information</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-medium text-gray-900 mb-2">Cost Price</h4>
                            <p class="text-2xl font-bold text-gray-800"><?= format_currency($part['cost_price']) ?></p>
                            <p class="text-sm text-gray-500">Your purchase price</p>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900 mb-2">Selling Price</h4>
                            <p class="text-2xl font-bold text-green-600"><?= format_currency($part['selling_price']) ?></p>
                            <p class="text-sm text-gray-500">Customer price</p>
                        </div>
                    </div>

                    <!-- Profit Analysis -->
                    <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                        <h4 class="font-medium text-gray-900 mb-3">Profit Analysis</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <?php
                            $profit = $part['selling_price'] - $part['cost_price'];
                            $profitMargin = $part['selling_price'] > 0 ? ($profit / $part['selling_price'] * 100) : 0;
                            $markup = $part['cost_price'] > 0 ? ($profit / $part['cost_price'] * 100) : 0;
                            ?>
                            <div class="text-center">
                                <p class="text-xl font-bold <?= $profit >= 0 ? 'text-green-600' : 'text-red-600' ?>">
                                    <?= format_currency($profit) ?>
                                </p>
                                <p class="text-sm text-gray-600">Profit per Unit</p>
                            </div>
                            <div class="text-center">
                                <p class="text-xl font-bold <?= $profitMargin >= 0 ? 'text-green-600' : 'text-red-600' ?>">
                                    <?= number_format($profitMargin, 1) ?>%
                                </p>
                                <p class="text-sm text-gray-600">Profit Margin</p>
                            </div>
                            <div class="text-center">
                                <p class="text-xl font-bold text-blue-600">
                                    <?= number_format($markup, 1) ?>%
                                </p>
                                <p class="text-sm text-gray-600">Markup</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stock Movement History -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">Stock Movement History</h3>
                    </div>
                    <div class="p-6">
                        <div class="text-center py-8">
                            <i class="fas fa-history text-4xl text-gray-300 mb-4"></i>
                            <h4 class="text-lg font-medium text-gray-900 mb-2">No Movement History</h4>
                            <p class="text-gray-600">Stock movements will appear here when inventory is adjusted</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Stock Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Stock Information</h3>

                    <div class="space-y-4">
                        <div class="text-center">
                            <p class="text-3xl font-bold <?= $part['stock_quantity'] <= $part['min_stock'] ? 'text-red-600' : 'text-blue-600' ?>">
                                <?= $part['stock_quantity'] ?>
                            </p>
                            <p class="text-sm text-gray-600">Current Stock</p>
                        </div>

                        <div class="text-center">
                            <p class="text-lg font-medium text-gray-800"><?= $part['min_stock'] ?></p>
                            <p class="text-sm text-gray-600">Minimum Stock</p>
                        </div>

                        <?php if ($part['stock_quantity'] <= $part['min_stock']): ?>
                            <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                                <div class="flex items-center">
                                    <i class="fas fa-exclamation-triangle text-red-600 mr-2"></i>
                                    <span class="text-red-800 font-medium text-sm">Low Stock Alert</span>
                                </div>
                                <p class="text-red-700 text-sm mt-1">Stock is below minimum level</p>
                            </div>
                        <?php else: ?>
                            <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                                <div class="flex items-center">
                                    <i class="fas fa-check-circle text-green-600 mr-2"></i>
                                    <span class="text-green-800 font-medium text-sm">Stock OK</span>
                                </div>
                                <p class="text-green-700 text-sm mt-1">Stock level is sufficient</p>
                            </div>
                        <?php endif; ?>

                        <!-- Stock Value -->
                        <div class="pt-4 border-t border-gray-200">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Stock Value (Cost):</span>
                                <span class="font-medium"><?= format_currency($part['stock_quantity'] * $part['cost_price']) ?></span>
                            </div>
                            <div class="flex justify-between text-sm mt-1">
                                <span class="text-gray-600">Stock Value (Selling):</span>
                                <span class="font-medium"><?= format_currency($part['stock_quantity'] * $part['selling_price']) ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <a href="/admin/parts/<?= $part['id'] ?>/edit"
                           class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors text-center block">
                            <i class="fas fa-edit mr-2"></i>Edit Part
                        </a>

                        <a href="/admin/parts/<?= $part['id'] ?>/adjust-stock"
                           class="w-full bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition-colors text-center block">
                            <i class="fas fa-plus-minus mr-2"></i>Adjust Stock
                        </a>

                        <?php if ($part['status'] === 'active'): ?>
                            <button onclick="toggleStatus('inactive')"
                                    class="w-full bg-yellow-600 text-white py-2 px-4 rounded-lg hover:bg-yellow-700 transition-colors">
                                <i class="fas fa-pause mr-2"></i>Deactivate
                            </button>
                        <?php else: ?>
                            <button onclick="toggleStatus('active')"
                                    class="w-full bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition-colors">
                                <i class="fas fa-play mr-2"></i>Activate
                            </button>
                        <?php endif; ?>

                        <button onclick="duplicatePart()"
                                class="w-full bg-purple-600 text-white py-2 px-4 rounded-lg hover:bg-purple-700 transition-colors">
                            <i class="fas fa-copy mr-2"></i>Duplicate
                        </button>

                        <button onclick="deletePart()"
                                class="w-full bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition-colors">
                            <i class="fas fa-trash mr-2"></i>Delete Part
                        </button>
                    </div>
                </div>

                <!-- QR Code Generator -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">QR Code</h3>
                    <div class="text-center">
                        <div class="w-32 h-32 bg-gray-100 rounded-lg mx-auto mb-4 flex items-center justify-center">
                            <i class="fas fa-qrcode text-gray-400 text-4xl"></i>
                        </div>
                        <button onclick="generateQR()" class="text-blue-600 hover:text-blue-800 text-sm">
                            <i class="fas fa-download mr-1"></i>Generate QR Code
                        </button>
                        <p class="text-xs text-gray-500 mt-2">For inventory tracking</p>
                    </div>
                </div>

                <!-- Usage Statistics -->
                <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Usage Statistics</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Times Used:</span>
                            <span class="font-medium">0</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Revenue Generated:</span>
                            <span class="font-medium"><?= format_currency(0) ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Last Used:</span>
                            <span class="font-medium">Never</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleStatus(newStatus) {
            const action = newStatus === 'active' ? 'activate' : 'deactivate';
            if (confirm(`Are you sure you want to ${action} this part?`)) {
                // Create form and submit
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/admin/parts/<?= $part['id'] ?>';

                form.innerHTML = `
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" name="status" value="${newStatus}">
                    <input type="hidden" name="part_number" value="<?= addslashes($part['part_number']) ?>">
                    <input type="hidden" name="name" value="<?= addslashes($part['name']) ?>">
                    <input type="hidden" name="cost_price" value="<?= $part['cost_price'] ?>">
                    <input type="hidden" name="selling_price" value="<?= $part['selling_price'] ?>">
                    <input type="hidden" name="stock_quantity" value="<?= $part['stock_quantity'] ?>">
                    <input type="hidden" name="min_stock" value="<?= $part['min_stock'] ?>">
                `;

                document.body.appendChild(form);
                form.submit();
            }
        }

        function duplicatePart() {
            if (confirm('Create a copy of this part?')) {
                window.location.href = '/admin/parts/new?duplicate=<?= $part['id'] ?>';
            }
        }

        function deletePart() {
            if (confirm('Are you sure you want to delete this part? This action cannot be undone.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/admin/parts/<?= $part['id'] ?>';
                form.innerHTML = `
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="DELETE">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        function generateQR() {
            // Implementation for QR code generation
            const partInfo = {
                part_number: '<?= $part['part_number'] ?>',
                name: '<?= addslashes($part['name']) ?>',
                location: '<?= $part['location'] ?>',
                url: window.location.href
            };

            // For now, just show an alert. You can integrate with a QR code library
            alert('QR Code generation feature will be implemented with a QR code library');

            // Example using qrcode.js library (if included):
            // const qrData = JSON.stringify(partInfo);
            // QRCode.toDataURL(qrData, function (err, url) {
            //     if (!err) {
            //         const link = document.createElement('a');
            //         link.download = 'part_<?= $part['part_number'] ?>_qr.png';
            //         link.href = url;
            //         link.click();
            //     }
            // });
        }

        // Auto-refresh stock status if it's a low stock item
        <?php if ($part['stock_quantity'] <= $part['min_stock']): ?>
        setInterval(function() {
            // You can implement real-time stock checking here
            // For example, fetch current stock via AJAX
        }, 30000); // Check every 30 seconds
        <?php endif; ?>

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // E for edit
            if (e.key === 'e' && !e.ctrlKey && !e.metaKey) {
                const activeElement = document.activeElement;
                if (activeElement.tagName !== 'INPUT' && activeElement.tagName !== 'TEXTAREA') {
                    window.location.href = '/admin/parts/<?= $part['id'] ?>/edit';
                }
            }

            // S for stock adjustment
            if (e.key === 's' && !e.ctrlKey && !e.metaKey) {
                const activeElement = document.activeElement;
                if (activeElement.tagName !== 'INPUT' && activeElement.tagName !== 'TEXTAREA') {
                    window.location.href = '/admin/parts/<?= $part['id'] ?>/adjust-stock';
                }
            }
        });

        // Print functionality
        function printPartLabel() {
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <html>
                <head>
                    <title>Part Label - <?= $part['part_number'] ?></title>
                    <style>
                        body { font-family: Arial, sans-serif; padding: 20px; }
                        .label { border: 2px solid #000; padding: 15px; width: 300px; }
                        .part-number { font-size: 18px; font-weight: bold; }
                        .part-name { font-size: 14px; margin: 5px 0; }
                        .details { font-size: 12px; color: #666; }
                    </style>
                </head>
                <body>
                    <div class="label">
                        <div class="part-number"><?= $part['part_number'] ?></div>
                        <div class="part-name"><?= $part['name'] ?></div>
                        <div class="details">Location: <?= $part['location'] ?: 'Not specified' ?></div>
                        <div class="details">Price: <?= format_currency($part['selling_price']) ?></div>
                    </div>
                </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.print();
        }

        // Add print button if needed
        document.addEventListener('DOMContentLoaded', function() {
            // You can add a print button to the quick actions if needed
        });
    </script>
<?= $this->endSection() ?>
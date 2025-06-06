<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quotation - <?= $quotation['quotation_number'] ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            .print-break { page-break-after: always; }
            body { font-size: 12px; }
            .container { max-width: none; margin: 0; padding: 20px; }
        }
        .quotation-header { background: linear-gradient(135deg, #3B82F6 0%, #1E40AF 100%); }
    </style>
</head>
<body class="bg-gray-50">

<!-- Print Actions -->
<div class="no-print bg-white border-b border-gray-200 p-4">
    <div class="max-w-4xl mx-auto flex justify-between items-center">
        <div>
            <h1 class="text-lg font-semibold text-gray-900">Quotation Preview</h1>
            <p class="text-sm text-gray-600"><?= $quotation['quotation_number'] ?></p>
        </div>
        <div class="flex space-x-2">
            <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                <i class="fas fa-print mr-2"></i>Print PDF
            </button>
            <a href="/admin/orders/<?= $quotation['order_id'] ?>/quotation/<?= $quotation['id'] ?>/send"
               class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                <i class="fas fa-envelope mr-2"></i>Send to Customer
            </a>
            <a href="/admin/orders/<?= $quotation['order_id'] ?>/quotation/edit"
               class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
        </div>
    </div>
</div>

<!-- Quotation Content -->
<div class="container max-w-4xl mx-auto p-6">
    <div class="bg-white shadow-2xl rounded-lg overflow-hidden">

        <!-- Header -->
        <div class="quotation-header text-white p-8">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-3xl font-bold"><?= $shop_info['name'] ?></h1>
                    <?php if ($shop_info['address']): ?>
                        <p class="mt-2 opacity-90"><?= $shop_info['address'] ?></p>
                    <?php endif; ?>
                    <div class="flex space-x-4 text-sm mt-3 opacity-80">
                        <?php if ($shop_info['phone']): ?>
                            <span><i class="fas fa-phone mr-1"></i><?= $shop_info['phone'] ?></span>
                        <?php endif; ?>
                        <?php if ($shop_info['email']): ?>
                            <span><i class="fas fa-envelope mr-1"></i><?= $shop_info['email'] ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="text-right">
                    <h2 class="text-2xl font-bold bg-white/20 px-4 py-2 rounded">QUOTATION</h2>
                    <p class="text-xl font-semibold mt-2"><?= $quotation['quotation_number'] ?></p>
                    <p class="text-sm opacity-80">Date: <?= date('M d, Y', strtotime($quotation['created_at'])) ?></p>
                </div>
            </div>
        </div>

        <!-- Customer & Order Info -->
        <div class="p-8 border-b border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Bill To:</h3>
                    <div class="space-y-2">
                        <p class="font-semibold text-gray-900"><?= $quotation['customer_name'] ?></p>
                        <p class="text-gray-600"><?= $quotation['customer_phone'] ?></p>
                        <?php if ($quotation['customer_email']): ?>
                            <p class="text-gray-600"><?= $quotation['customer_email'] ?></p>
                        <?php endif; ?>
                        <?php if ($quotation['customer_address']): ?>
                            <p class="text-gray-600"><?= nl2br($quotation['customer_address']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Order Details:</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Order Number:</span>
                            <span class="font-medium"><?= $quotation['order_number'] ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Device:</span>
                            <span class="font-medium"><?= $quotation['device_type_name'] ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Brand/Model:</span>
                            <span class="font-medium"><?= $quotation['device_brand'] ?> <?= $quotation['device_model'] ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Quote Valid Until:</span>
                            <span class="font-medium text-red-600"><?= date('M d, Y', strtotime($quotation['valid_until'])) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Problem Description -->
        <div class="p-8 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Problem Description</h3>
            <div class="bg-white p-4 rounded-lg border-l-4 border-blue-500">
                <p class="text-gray-900"><?= nl2br($quotation['problem_description']) ?></p>
            </div>
        </div>

        <!-- Services & Parts Breakdown -->
        <div class="p-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-6">Cost Breakdown</h3>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Description</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700">Amount</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                    <?php if ($quotation['service_cost'] > 0): ?>
                        <tr>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">Labor & Service Charges</div>
                                <div class="text-sm text-gray-600">Professional repair services</div>
                            </td>
                            <td class="px-6 py-4 text-right font-medium text-gray-900">
                                <?= format_currency($quotation['service_cost']) ?>
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php if ($quotation['parts_cost'] > 0): ?>
                        <tr>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">Parts & Materials</div>
                                <div class="text-sm text-gray-600">Replacement parts and components</div>
                            </td>
                            <td class="px-6 py-4 text-right font-medium text-gray-900">
                                <?= format_currency($quotation['parts_cost']) ?>
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php if ($quotation['additional_cost'] > 0): ?>
                        <tr>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">Additional Charges</div>
                                <div class="text-sm text-gray-600">Express service, shipping, etc.</div>
                            </td>
                            <td class="px-6 py-4 text-right font-medium text-gray-900">
                                <?= format_currency($quotation['additional_cost']) ?>
                            </td>
                        </tr>
                    <?php endif; ?>

                    <!-- Subtotal -->
                    <tr class="bg-gray-50">
                        <td class="px-6 py-4 font-semibold text-gray-900">Subtotal</td>
                        <td class="px-6 py-4 text-right font-semibold text-gray-900">
                            <?= format_currency($quotation['service_cost'] + $quotation['parts_cost'] + $quotation['additional_cost']) ?>
                        </td>
                    </tr>

                    <!-- Discount -->
                    <?php if ($quotation['discount_amount'] > 0): ?>
                        <tr>
                            <td class="px-6 py-4">
                                <div class="font-medium text-green-700">Discount
                                    <?php if ($quotation['discount_percentage'] > 0): ?>
                                        (<?= $quotation['discount_percentage'] ?>%)
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right font-medium text-green-700">
                                -<?= format_currency($quotation['discount_amount']) ?>
                            </td>
                        </tr>
                    <?php endif; ?>

                    <!-- Tax -->
                    <?php if ($quotation['tax_amount'] > 0): ?>
                        <tr>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">Tax (<?= $quotation['tax_percentage'] ?>%)</div>
                            </td>
                            <td class="px-6 py-4 text-right font-medium text-gray-900">
                                <?= format_currency($quotation['tax_amount']) ?>
                            </td>
                        </tr>
                    <?php endif; ?>

                    <!-- Total -->
                    <tr class="bg-blue-50 border-t-2 border-blue-200">
                        <td class="px-6 py-6">
                            <div class="text-xl font-bold text-blue-900">TOTAL AMOUNT</div>
                            <div class="text-sm text-blue-700">All inclusive</div>
                        </td>
                        <td class="px-6 py-6 text-right">
                            <div class="text-2xl font-bold text-blue-900">
                                <?= format_currency($quotation['total_cost']) ?>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Timeline & Warranty -->
        <div class="p-8 bg-gradient-to-r from-blue-50 to-purple-50 border-t border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <h4 class="font-semibold text-gray-800 mb-3">
                        <i class="fas fa-clock text-blue-600 mr-2"></i>Timeline
                    </h4>
                    <div class="bg-white p-4 rounded-lg shadow-sm">
                        <p class="text-sm text-gray-600">Estimated Duration:</p>
                        <p class="font-semibold text-gray-900"><?= $quotation['estimated_duration'] ?></p>
                    </div>
                </div>
                <?php if ($quotation['warranty_period']): ?>
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-3">
                            <i class="fas fa-shield-alt text-green-600 mr-2"></i>Warranty
                        </h4>
                        <div class="bg-white p-4 rounded-lg shadow-sm">
                            <p class="text-sm text-gray-600">Warranty Period:</p>
                            <p class="font-semibold text-gray-900"><?= $quotation['warranty_period'] ?></p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Terms & Conditions -->
        <?php if ($quotation['terms_conditions']): ?>
            <div class="p-8 border-t border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Terms & Conditions</h3>
                <div class="bg-gray-50 p-6 rounded-lg">
                    <div class="text-sm text-gray-700 space-y-2">
                        <?= nl2br($quotation['terms_conditions']) ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Customer Action Buttons -->
        <div class="p-8 bg-gradient-to-r from-green-600 to-blue-600 text-white">
            <div class="text-center">
                <h3 class="text-xl font-bold mb-4">Ready to Proceed?</h3>
                <div class="flex flex-col sm:flex-row justify-center space-y-3 sm:space-y-0 sm:space-x-4">
                    <a href="/quotation/<?= $quotation['id'] ?>/approve"
                       class="bg-white text-green-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                        <i class="fas fa-check mr-2"></i>APPROVE & START REPAIR
                    </a>
                    <a href="/quotation/<?= $quotation['id'] ?>/reject"
                       class="bg-red-500 text-white px-8 py-3 rounded-lg font-semibold hover:bg-red-600 transition-colors">
                        <i class="fas fa-times mr-2"></i>DECLINE QUOTATION
                    </a>
                </div>
                <p class="text-sm mt-4 opacity-80">
                    Questions? Contact us at <?= $shop_info['phone'] ?: $shop_info['email'] ?>
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="bg-gray-800 text-white p-6 text-center">
            <p class="text-sm">Thank you for choosing <?= $shop_info['name'] ?>!</p>
            <p class="text-xs mt-1 opacity-75">This quotation is valid until <?= date('F d, Y', strtotime($quotation['valid_until'])) ?></p>
        </div>
    </div>
</div>

<script>
    // Auto-print if requested
    <?php if (isset($print_mode) && $print_mode): ?>
    window.onload = function() { window.print(); }
    <?php endif; ?>

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
            e.preventDefault();
            window.print();
        }
    });
</script>

</body>
</html>
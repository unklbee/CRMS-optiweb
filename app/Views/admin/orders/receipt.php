<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($receipt_type) && $receipt_type === 'delivery' ? 'Delivery' : 'Service' ?> Receipt - <?= $order['order_number'] ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @media print {
            .no-print { display: none !important; }
            .print-break { page-break-after: always; }
            body { font-size: 12px; }
            .container { max-width: none; margin: 0; padding: 20px; }
        }
    </style>
</head>
<body class="bg-gray-50">
<!-- Print Actions (hidden when printing) -->
<div class="no-print bg-white border-b border-gray-200 p-4">
    <div class="max-w-4xl mx-auto flex justify-between items-center">
        <div>
            <h1 class="text-lg font-semibold text-gray-900">
                <?= isset($receipt_type) && $receipt_type === 'delivery' ? 'Delivery' : 'Service' ?> Receipt
            </h1>
            <p class="text-sm text-gray-600">Order #<?= $order['order_number'] ?></p>
        </div>
        <div class="flex space-x-2">
            <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-print mr-2"></i>Print
            </button>
            <a href="/admin/orders/<?= $order['id'] ?>/email-receipt"
               class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                <i class="fas fa-envelope mr-2"></i>Email to Customer
            </a>
            <a href="/admin/orders/<?= $order['id'] ?>"
               class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Back to Order
            </a>
        </div>
    </div>
</div>

<!-- Receipt Content -->
<div class="container max-w-4xl mx-auto p-6">
    <div class="bg-white shadow-lg">
        <!-- Header -->
        <div class="border-b-2 border-gray-800 p-6">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900"><?= $shop_info['name'] ?></h1>
                    <?php if ($shop_info['address']): ?>
                        <p class="text-gray-600 mt-1"><?= $shop_info['address'] ?></p>
                    <?php endif; ?>
                    <div class="flex space-x-4 text-sm text-gray-600 mt-2">
                        <?php if ($shop_info['phone']): ?>
                            <span><i class="fas fa-phone mr-1"></i><?= $shop_info['phone'] ?></span>
                        <?php endif; ?>
                        <?php if ($shop_info['email']): ?>
                            <span><i class="fas fa-envelope mr-1"></i><?= $shop_info['email'] ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="text-right">
                    <h2 class="text-xl font-bold text-blue-600">
                        <?= isset($receipt_type) && $receipt_type === 'delivery' ? 'DELIVERY RECEIPT' : 'SERVICE RECEIPT' ?>
                    </h2>
                    <p class="text-lg font-semibold text-gray-900 mt-1">#<?= $order['order_number'] ?></p>
                    <p class="text-sm text-gray-600">
                        <?= isset($receipt_type) && $receipt_type === 'delivery' ? 'Delivered' : 'Received' ?>:
                        <?= date('M d, Y H:i', strtotime($order['created_at'])) ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Customer Information -->
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-3">Customer Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Name</p>
                    <p class="font-semibold text-gray-900"><?= $order['customer_name'] ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Phone</p>
                    <p class="font-semibold text-gray-900"><?= $order['customer_phone'] ?></p>
                </div>
                <?php if ($order['customer_email']): ?>
                    <div>
                        <p class="text-sm text-gray-600">Email</p>
                        <p class="font-semibold text-gray-900"><?= $order['customer_email'] ?></p>
                    </div>
                <?php endif; ?>
                <?php if (isset($order['customer_address']) && $order['customer_address']): ?>
                    <div>
                        <p class="text-sm text-gray-600">Address</p>
                        <p class="font-semibold text-gray-900"><?= $order['customer_address'] ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Device Information -->
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-3">Device Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Device Type</p>
                    <p class="font-semibold text-gray-900"><?= $order['device_type_name'] ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Brand & Model</p>
                    <p class="font-semibold text-gray-900"><?= $order['device_brand'] ?> <?= $order['device_model'] ?></p>
                </div>
                <?php if ($order['device_serial']): ?>
                    <div>
                        <p class="text-sm text-gray-600">Serial Number</p>
                        <p class="font-semibold text-gray-900"><?= $order['device_serial'] ?></p>
                    </div>
                <?php endif; ?>
                <div>
                    <p class="text-sm text-gray-600">Priority</p>
                    <span class="px-2 py-1 rounded-full text-xs font-medium
                            <?= $order['priority'] === 'urgent' ? 'bg-red-100 text-red-800' :
                        ($order['priority'] === 'high' ? 'bg-orange-100 text-orange-800' : 'bg-gray-100 text-gray-800') ?>">
                            <?= ucfirst($order['priority']) ?>
                        </span>
                </div>
            </div>
        </div>

        <!-- Problem Description -->
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-3">
                <?= isset($receipt_type) && $receipt_type === 'delivery' ? 'Work Performed' : 'Problem Description' ?>
            </h3>
            <p class="text-gray-900 bg-gray-50 p-3 rounded"><?= nl2br($order['problem_description']) ?></p>

            <?php if ($order['accessories']): ?>
                <div class="mt-4">
                    <h4 class="font-medium text-gray-800 mb-2">Accessories Included</h4>
                    <p class="text-gray-900 bg-gray-50 p-3 rounded"><?= nl2br($order['accessories']) ?></p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Status & Dates -->
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-3">Service Status</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Current Status</p>
                    <span class="inline-flex px-3 py-1 rounded-full text-sm font-medium
                            <?= $order['status'] === 'completed' ? 'bg-green-100 text-green-800' :
                        ($order['status'] === 'in_progress' ? 'bg-blue-100 text-blue-800' :
                            ($order['status'] === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800')) ?>">
                            <?= ucfirst(str_replace('_', ' ', $order['status'])) ?>
                        </span>
                </div>
                <div>
                    <p class="text-sm text-gray-600"><?= isset($receipt_type) && $receipt_type === 'delivery' ? 'Delivered Date' : 'Received Date' ?></p>
                    <p class="font-semibold text-gray-900"><?= date('M d, Y', strtotime($order['created_at'])) ?></p>
                </div>
                <?php if ($order['estimated_completion']): ?>
                    <div>
                        <p class="text-sm text-gray-600">Expected Completion</p>
                        <p class="font-semibold text-gray-900"><?= date('M d, Y', strtotime($order['estimated_completion'])) ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Pricing (if available) -->
        <?php if ($order['estimated_cost'] > 0 || $order['final_cost'] > 0): ?>
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Pricing Information</h3>
                <div class="space-y-2">
                    <?php if ($order['estimated_cost'] > 0): ?>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Estimated Cost:</span>
                            <span class="font-semibold"><?= format_currency($order['estimated_cost']) ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if ($order['final_cost'] > 0): ?>
                        <div class="flex justify-between text-lg border-t pt-2">
                            <span class="font-semibold">Final Cost:</span>
                            <span class="font-bold text-green-600"><?= format_currency($order['final_cost']) ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Tracking Information -->
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-3">Order Tracking</h3>
            <div class="flex items-center justify-between bg-blue-50 p-4 rounded-lg">
                <div>
                    <p class="text-sm text-blue-600 font-medium">Track your order online:</p>
                    <p class="text-blue-800 font-mono text-sm"><?= base_url("track-order?order={$order['order_number']}") ?></p>
                </div>
                <div class="no-print">
                    <a href="/admin/orders/<?= $order['id'] ?>/qr-code" target="_blank"
                       class="bg-blue-600 text-white px-3 py-2 rounded text-sm hover:bg-blue-700">
                        <i class="fas fa-qrcode mr-1"></i>QR Code
                    </a>
                </div>
            </div>
        </div>

        <!-- Terms & Conditions -->
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-3">Terms & Conditions</h3>
            <div class="text-sm text-gray-600 space-y-2">
                <p>• Please keep this receipt for your records and device pickup.</p>
                <p>• <?= isset($receipt_type) && $receipt_type === 'delivery' ?
                        'Warranty period starts from delivery date.' :
                        'We are not responsible for data loss. Please backup your data before service.' ?>
                </p>
                <p>• <?= isset($receipt_type) && $receipt_type === 'delivery' ?
                        'Please test your device thoroughly before leaving our premises.' :
                        'Devices not collected within 30 days may incur storage charges.' ?>
                </p>
                <p>• For support, contact us using the information above.</p>
            </div>
        </div>

        <!-- Signatures -->
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <p class="text-sm text-gray-600 mb-8">Customer Signature</p>
                    <div class="border-b border-gray-400 h-8"></div>
                    <p class="text-xs text-gray-500 mt-1">Date: ________________</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 mb-8">
                        <?= isset($receipt_type) && $receipt_type === 'delivery' ? 'Technician' : 'Received by' ?>
                    </p>
                    <div class="border-b border-gray-400 h-8"></div>
                    <p class="text-xs text-gray-500 mt-1">
                        <?= isset($order['received_by']) ? $order['received_by'] : (isset($order['technician_name']) ? $order['technician_name'] : 'Staff') ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="bg-gray-800 text-white p-4 text-center">
            <p class="text-sm">Thank you for choosing <?= $shop_info['name'] ?>!</p>
            <p class="text-xs mt-1">Professional Computer Repair Services</p>
        </div>
    </div>
</div>

<!-- Auto-print if requested -->
<?php if (isset($print_mode) && $print_mode): ?>
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
<?php endif; ?>

<script>
    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + P to print
        if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
            e.preventDefault();
            window.print();
        }
    });
</script>
</body>
</html>
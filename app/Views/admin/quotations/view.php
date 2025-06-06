<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $quotation['quotation_number'] ?> - Repair Quotation</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .gradient-bg { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .card-hover { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(0,0,0,0.15); }
        .pulse-animation { animation: pulse 2s infinite; }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
        }
    </style>
</head>
<body class="bg-gray-100">

<!-- Header -->
<div class="gradient-bg text-white py-12">
    <div class="max-w-4xl mx-auto px-6">
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-white/20 rounded-full mb-4">
                <i class="fas fa-file-invoice-dollar text-2xl"></i>
            </div>
            <h1 class="text-3xl font-bold mb-2">Repair Quotation</h1>
            <p class="text-xl opacity-90"><?= $quotation['quotation_number'] ?></p>
            <p class="text-sm opacity-75 mt-2">for Order #<?= $quotation['order_number'] ?></p>
        </div>
    </div>
</div>

<!-- Status Alert -->
<div class="max-w-4xl mx-auto px-6 -mt-6 relative z-10">
    <?php if (session()->getFlashdata('success')): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-6 py-4 rounded-lg shadow-lg mb-6">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-2xl mr-4"></i>
                <div>
                    <h3 class="font-bold">Success!</h3>
                    <p class="text-sm"><?= session()->getFlashdata('success') ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-lg shadow-lg mb-6">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle text-2xl mr-4"></i>
                <div>
                    <h3 class="font-bold">Error</h3>
                    <p class="text-sm"><?= session()->getFlashdata('error') ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($quotation['status'] === 'expired'): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-lg shadow-lg">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle text-2xl mr-4"></i>
                <div>
                    <h3 class="font-bold">Quotation Expired</h3>
                    <p class="text-sm">This quotation expired on <?= date('M d, Y', strtotime($quotation['valid_until'])) ?>. Please contact us for an updated quote.</p>
                </div>
            </div>
        </div>
    <?php elseif ($quotation['status'] === 'approved'): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-6 py-4 rounded-lg shadow-lg">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-2xl mr-4"></i>
                <div>
                    <h3 class="font-bold">Quotation Approved</h3>
                    <p class="text-sm">Thank you! We have received your approval and will begin the repair work shortly.</p>
                </div>
            </div>
        </div>
    <?php elseif ($quotation['status'] === 'rejected'): ?>
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-6 py-4 rounded-lg shadow-lg">
            <div class="flex items-center">
                <i class="fas fa-times-circle text-2xl mr-4"></i>
                <div>
                    <h3 class="font-bold">Quotation Declined</h3>
                    <p class="text-sm">You have declined this quotation. We will contact you to discuss alternatives.</p>
                </div>
            </div>
        </div>
    <?php elseif ($quotation['status'] === 'sent'): ?>
        <div class="bg-blue-100 border border-blue-400 text-blue-700 px-6 py-4 rounded-lg shadow-lg">
            <div class="flex items-center">
                <i class="fas fa-clock text-2xl mr-4 pulse-animation"></i>
                <div>
                    <h3 class="font-bold">Awaiting Your Decision</h3>
                    <p class="text-sm">Please review the quotation below and let us know if you'd like to proceed with the repair.</p>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Main Content -->
<div class="max-w-4xl mx-auto px-6 py-8">
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">

        <!-- Customer & Device Info -->
        <div class="p-8 border-b border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Customer Information</h3>
                    <div class="space-y-2 text-sm">
                        <p><span class="text-gray-600">Name:</span> <span class="font-medium"><?= $quotation['customer_name'] ?></span></p>
                        <p><span class="text-gray-600">Phone:</span> <span class="font-medium"><?= $quotation['customer_phone'] ?></span></p>
                        <?php if ($quotation['customer_email']): ?>
                            <p><span class="text-gray-600">Email:</span> <span class="font-medium"><?= $quotation['customer_email'] ?></span></p>
                        <?php endif; ?>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Device Information</h3>
                    <div class="space-y-2 text-sm">
                        <p><span class="text-gray-600">Type:</span> <span class="font-medium"><?= $quotation['device_type_name'] ?></span></p>
                        <p><span class="text-gray-600">Brand/Model:</span> <span class="font-medium"><?= $quotation['device_brand'] ?> <?= $quotation['device_model'] ?></span></p>
                        <p><span class="text-gray-600">Problem:</span> <span class="font-medium"><?= truncate_text($quotation['problem_description'], 100) ?></span></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cost Breakdown -->
        <div class="p-8">
            <h3 class="text-xl font-semibold text-gray-800 mb-6">Cost Breakdown</h3>

            <div class="space-y-4">
                <?php if ($quotation['service_cost'] > 0): ?>
                    <div class="flex justify-between items-center py-3 border-b border-gray-100">
                        <div>
                            <span class="font-medium text-gray-900">Labor & Service</span>
                            <p class="text-sm text-gray-600">Professional repair services</p>
                        </div>
                        <span class="text-lg font-semibold text-gray-900"><?= format_currency($quotation['service_cost']) ?></span>
                    </div>
                <?php endif; ?>

                <?php if ($quotation['parts_cost'] > 0): ?>
                    <div class="flex justify-between items-center py-3 border-b border-gray-100">
                        <div>
                            <span class="font-medium text-gray-900">Parts & Materials</span>
                            <p class="text-sm text-gray-600">Replacement components</p>
                        </div>
                        <span class="text-lg font-semibold text-gray-900"><?= format_currency($quotation['parts_cost']) ?></span>
                    </div>
                <?php endif; ?>

                <?php if ($quotation['additional_cost'] > 0): ?>
                    <div class="flex justify-between items-center py-3 border-b border-gray-100">
                        <div>
                            <span class="font-medium text-gray-900">Additional Charges</span>
                            <p class="text-sm text-gray-600">Express service, shipping, etc.</p>
                        </div>
                        <span class="text-lg font-semibold text-gray-900"><?= format_currency($quotation['additional_cost']) ?></span>
                    </div>
                <?php endif; ?>

                <!-- Subtotal -->
                <div class="flex justify-between items-center py-3 border-b border-gray-200">
                    <span class="font-semibold text-gray-900">Subtotal</span>
                    <span class="text-lg font-semibold text-gray-900">
                        <?= format_currency($quotation['service_cost'] + $quotation['parts_cost'] + $quotation['additional_cost']) ?>
                    </span>
                </div>

                <!-- Discount -->
                <?php if ($quotation['discount_amount'] > 0): ?>
                    <div class="flex justify-between items-center py-3 border-b border-gray-100">
                        <div>
                        <span class="font-medium text-green-700">Discount
                            <?php if ($quotation['discount_percentage'] > 0): ?>
                                (<?= $quotation['discount_percentage'] ?>%)
                            <?php endif; ?>
                        </span>
                        </div>
                        <span class="text-lg font-semibold text-green-700">-<?= format_currency($quotation['discount_amount']) ?></span>
                    </div>
                <?php endif; ?>

                <!-- Tax -->
                <?php if ($quotation['tax_amount'] > 0): ?>
                    <div class="flex justify-between items-center py-3 border-b border-gray-100">
                        <span class="font-medium text-gray-900">Tax (<?= $quotation['tax_percentage'] ?>%)</span>
                        <span class="text-lg font-semibold text-gray-900"><?= format_currency($quotation['tax_amount']) ?></span>
                    </div>
                <?php endif; ?>

                <!-- Total -->
                <div class="flex justify-between items-center py-6 bg-gradient-to-r from-blue-50 to-purple-50 px-6 rounded-lg border-2 border-blue-200">
                    <div>
                        <span class="text-2xl font-bold text-blue-900">TOTAL</span>
                        <p class="text-sm text-blue-700">Final amount to pay</p>
                    </div>
                    <span class="text-3xl font-bold text-blue-900"><?= format_currency($quotation['total_cost']) ?></span>
                </div>
            </div>
        </div>

        <!-- Timeline & Warranty -->
        <div class="p-8 bg-gray-50 border-t border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="card-hover bg-white p-6 rounded-lg shadow-sm">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-clock text-blue-600 text-2xl mr-3"></i>
                        <h4 class="font-semibold text-gray-800">Estimated Timeline</h4>
                    </div>
                    <p class="text-lg font-medium text-gray-900"><?= $quotation['estimated_duration'] ?></p>
                    <p class="text-sm text-gray-600 mt-1">From approval to completion</p>
                </div>

                <?php if ($quotation['warranty_period']): ?>
                    <div class="card-hover bg-white p-6 rounded-lg shadow-sm">
                        <div class="flex items-center mb-4">
                            <i class="fas fa-shield-alt text-green-600 text-2xl mr-3"></i>
                            <h4 class="font-semibold text-gray-800">Warranty Coverage</h4>
                        </div>
                        <p class="text-lg font-medium text-gray-900"><?= $quotation['warranty_period'] ?></p>
                        <p class="text-sm text-gray-600 mt-1">Starting from completion date</p>
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

        <!-- Action Buttons -->
        <div class="no-print">
            <?php if ($quotation['status'] === 'sent' && strtotime($quotation['valid_until']) > time()): ?>
                <div class="p-8 bg-gradient-to-r from-green-600 via-blue-600 to-purple-600">
                    <div class="text-center text-white">
                        <h3 class="text-2xl font-bold mb-2">Ready to Start Your Repair?</h3>
                        <p class="mb-6 opacity-90">Choose your action below. Valid until <?= date('F d, Y', strtotime($quotation['valid_until'])) ?></p>

                        <div class="flex flex-col sm:flex-row justify-center space-y-4 sm:space-y-0 sm:space-x-6">
                            <!-- Approve Button -->
                            <a href="/quotation/<?= $quotation['id'] ?>/approve"
                               class="bg-white text-green-600 px-8 py-4 rounded-xl font-bold text-lg hover:bg-gray-100 transition-all transform hover:scale-105 shadow-lg inline-block">
                                <i class="fas fa-check-circle mr-3"></i>APPROVE & START REPAIR
                            </a>

                            <!-- Decline Button -->
                            <a href="/quotation/<?= $quotation['id'] ?>/reject"
                               class="bg-red-500 text-white px-8 py-4 rounded-xl font-bold text-lg hover:bg-red-600 transition-all transform hover:scale-105 shadow-lg inline-block">
                                <i class="fas fa-times-circle mr-3"></i>DECLINE QUOTATION
                            </a>
                        </div>

                        <p class="text-sm mt-6 opacity-75">
                            <i class="fas fa-phone mr-2"></i>Questions? Call us at <?= $shop_info['phone'] ?: 'contact number' ?>
                        </p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Download & Print Options -->
            <div class="p-6 bg-gray-100 border-t">
                <div class="flex flex-col sm:flex-row justify-center space-y-2 sm:space-y-0 sm:space-x-4">
                    <a href="/quotation/<?= $quotation['id'] ?>/pdf" target="_blank"
                       class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700 transition-colors text-center">
                        <i class="fas fa-download mr-2"></i>Download PDF
                    </a>
                    <button onclick="window.print()"
                            class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-print mr-2"></i>Print Quotation
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<div class="bg-gray-800 text-white py-8 mt-12">
    <div class="max-w-4xl mx-auto px-6 text-center">
        <h3 class="text-lg font-semibold mb-2"><?= $shop_info['name'] ?></h3>
        <div class="flex justify-center space-x-6 text-sm">
            <?php if ($shop_info['phone']): ?>
                <span><i class="fas fa-phone mr-2"></i><?= $shop_info['phone'] ?></span>
            <?php endif; ?>
            <?php if ($shop_info['email']): ?>
                <span><i class="fas fa-envelope mr-2"></i><?= $shop_info['email'] ?></span>
            <?php endif; ?>
        </div>
        <p class="text-xs mt-4 opacity-75">Professional Computer Repair Services</p>
    </div>
</div>

<script>
    // Auto-refresh status every 30 seconds for pending quotations
    <?php if ($quotation['status'] === 'sent'): ?>
    setInterval(function() {
        if (!document.hidden) {
            fetch('/quotation/<?= $quotation['id'] ?>/status')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.status !== 'sent') {
                        location.reload();
                    }
                });
        }
    }, 30000);
    <?php endif; ?>

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + P for print
        if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
            e.preventDefault();
            window.print();
        }
    });
</script>

</body>
</html>
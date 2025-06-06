<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $action === 'approve' ? 'Approve' : 'Decline' ?> Quotation - <?= $quotation['quotation_number'] ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .gradient-bg { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    </style>
</head>
<body class="bg-gray-100">

<!-- Header -->
<div class="gradient-bg text-white py-8">
    <div class="max-w-2xl mx-auto px-6 text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-white/20 rounded-full mb-4">
            <i class="fas fa-<?= $action === 'approve' ? 'check-circle' : 'times-circle' ?> text-2xl"></i>
        </div>
        <h1 class="text-2xl font-bold mb-2"><?= $action === 'approve' ? 'Approve' : 'Decline' ?> Quotation</h1>
        <p class="opacity-90"><?= $quotation['quotation_number'] ?></p>
    </div>
</div>

<!-- Main Content -->
<div class="max-w-2xl mx-auto px-6 py-8">
    <!-- Quotation Summary -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Quotation Summary</h3>

        <div class="space-y-3 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-600">Device:</span>
                <span class="font-medium"><?= $quotation['device_type_name'] ?> - <?= $quotation['device_brand'] ?> <?= $quotation['device_model'] ?></span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Customer:</span>
                <span class="font-medium"><?= $quotation['customer_name'] ?></span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Estimated Duration:</span>
                <span class="font-medium"><?= $quotation['estimated_duration'] ?></span>
            </div>
            <div class="flex justify-between border-t pt-3">
                <span class="text-lg font-semibold text-gray-900">Total Amount:</span>
                <span class="text-lg font-bold text-<?= $action === 'approve' ? 'green' : 'red' ?>-600"><?= format_currency($quotation['total_cost']) ?></span>
            </div>
        </div>
    </div>

    <!-- Action Form -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <?php if ($action === 'approve'): ?>
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                    <i class="fas fa-check text-green-600 text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900">Confirm Approval</h3>
                <p class="text-gray-600 mt-2">By approving this quotation, you authorize us to begin the repair work.</p>
            </div>
        <?php else: ?>
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-red-100 rounded-full mb-4">
                    <i class="fas fa-times text-red-600 text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900">Decline Quotation</h3>
                <p class="text-gray-600 mt-2">Please let us know why you're declining this quotation so we can assist you better.</p>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                <span class="block sm:inline"><?= session()->getFlashdata('error') ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-6">
            <?= csrf_field() ?>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <?php if ($action === 'approve'): ?>
                        Additional Notes (Optional)
                    <?php else: ?>
                        Reason for Declining *
                    <?php endif; ?>
                </label>
                <textarea name="customer_notes" rows="4"
                          <?= $action === 'reject' ? 'required' : '' ?>
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-<?= $action === 'approve' ? 'green' : 'red' ?>-500 focus:border-transparent"
                          placeholder="<?php if ($action === 'approve'): ?>Any special instructions or comments...<?php else: ?>Please tell us why you're declining (too expensive, want different approach, etc.)<?php endif; ?>"><?= old('customer_notes') ?></textarea>
            </div>

            <?php if ($action === 'reject'): ?>
                <!-- Quick Decline Reasons -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Quick Reasons (Click to select)</label>
                    <div class="grid grid-cols-1 gap-2">
                        <button type="button" onclick="setReason('Price is too high for my budget')"
                                class="text-left p-3 border border-gray-200 rounded hover:bg-gray-50 text-sm">
                            💰 Price is too high for my budget
                        </button>
                        <button type="button" onclick="setReason('Timeline is too long, I need it fixed sooner')"
                                class="text-left p-3 border border-gray-200 rounded hover:bg-gray-50 text-sm">
                            ⏰ Timeline is too long, I need it fixed sooner
                        </button>
                        <button type="button" onclick="setReason('I want to get a second opinion before proceeding')"
                                class="text-left p-3 border border-gray-200 rounded hover:bg-gray-50 text-sm">
                            🤔 I want to get a second opinion before proceeding
                        </button>
                        <button type="button" onclick="setReason('I decided not to repair the device')"
                                class="text-left p-3 border border-gray-200 rounded hover:bg-gray-50 text-sm">
                            ❌ I decided not to repair the device
                        </button>
                        <button type="button" onclick="setReason('I need more details about the repair process')"
                                class="text-left p-3 border border-gray-200 rounded hover:bg-gray-50 text-sm">
                            ❓ I need more details about the repair process
                        </button>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($action === 'approve'): ?>
                <!-- Approval Confirmations -->
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <h4 class="font-medium text-green-800 mb-3">By approving this quotation, you agree to:</h4>
                    <div class="space-y-2 text-sm text-green-700">
                        <label class="flex items-start">
                            <input type="checkbox" required class="mt-1 mr-2 text-green-600 rounded focus:ring-green-500">
                            <span>The total cost of <strong><?= format_currency($quotation['total_cost']) ?></strong> for the repair work</span>
                        </label>
                        <label class="flex items-start">
                            <input type="checkbox" required class="mt-1 mr-2 text-green-600 rounded focus:ring-green-500">
                            <span>The estimated timeline of <strong><?= $quotation['estimated_duration'] ?></strong></span>
                        </label>
                        <label class="flex items-start">
                            <input type="checkbox" required class="mt-1 mr-2 text-green-600 rounded focus:ring-green-500">
                            <span>The terms and conditions outlined in the quotation</span>
                        </label>
                        <?php if ($quotation['warranty_period']): ?>
                            <label class="flex items-start">
                                <input type="checkbox" required class="mt-1 mr-2 text-green-600 rounded focus:ring-green-500">
                                <span>The warranty period of <strong><?= $quotation['warranty_period'] ?></strong></span>
                            </label>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Contact Info -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h4 class="font-medium text-blue-800 mb-2">
                    <i class="fas fa-info-circle mr-2"></i>Need to discuss before deciding?
                </h4>
                <p class="text-sm text-blue-700">
                    Contact us at <?= $shop_info['phone'] ?: $shop_info['email'] ?> if you have any questions about this quotation.
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4 pt-6 border-t">
                <a href="/quotation/<?= $quotation['id'] ?>"
                   class="flex-1 bg-gray-300 text-gray-700 py-3 rounded-lg font-medium hover:bg-gray-400 transition-colors text-center">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Quotation
                </a>
                <button type="submit"
                        class="flex-1 bg-<?= $action === 'approve' ? 'green' : 'red' ?>-600 text-white py-3 rounded-lg font-medium hover:bg-<?= $action === 'approve' ? 'green' : 'red' ?>-700 transition-colors">
                    <i class="fas fa-<?= $action === 'approve' ? 'check' : 'times' ?> mr-2"></i>
                    <?= $action === 'approve' ? 'Confirm Approval' : 'Submit Decline' ?>
                </button>
            </div>
        </form>
    </div>

    <!-- What Happens Next -->
    <div class="bg-white rounded-xl shadow-lg p-6 mt-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-info-circle text-blue-600 mr-2"></i>What happens next?
        </h3>

        <?php if ($action === 'approve'): ?>
            <div class="space-y-3 text-sm text-gray-700">
                <div class="flex items-start">
                    <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center mr-3 mt-0.5">
                        <span class="text-green-600 font-bold text-xs">1</span>
                    </div>
                    <p>We'll receive your approval immediately and begin preparing for the repair</p>
                </div>
                <div class="flex items-start">
                    <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center mr-3 mt-0.5">
                        <span class="text-green-600 font-bold text-xs">2</span>
                    </div>
                    <p>Our technician will start working on your device within 1 business day</p>
                </div>
                <div class="flex items-start">
                    <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center mr-3 mt-0.5">
                        <span class="text-green-600 font-bold text-xs">3</span>
                    </div>
                    <p>We'll keep you updated on progress and notify you when repair is complete</p>
                </div>
                <div class="flex items-start">
                    <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center mr-3 mt-0.5">
                        <span class="text-green-600 font-bold text-xs">4</span>
                    </div>
                    <p>You can pick up your repaired device or we'll arrange delivery</p>
                </div>
            </div>
        <?php else: ?>
            <div class="space-y-3 text-sm text-gray-700">
                <div class="flex items-start">
                    <div class="w-6 h-6 bg-orange-100 rounded-full flex items-center justify-center mr-3 mt-0.5">
                        <span class="text-orange-600 font-bold text-xs">1</span>
                    </div>
                    <p>We'll receive your feedback and review your concerns</p>
                </div>
                <div class="flex items-start">
                    <div class="w-6 h-6 bg-orange-100 rounded-full flex items-center justify-center mr-3 mt-0.5">
                        <span class="text-orange-600 font-bold text-xs">2</span>
                    </div>
                    <p>Our team will contact you within 1 business day to discuss alternatives</p>
                </div>
                <div class="flex items-start">
                    <div class="w-6 h-6 bg-orange-100 rounded-full flex items-center justify-center mr-3 mt-0.5">
                        <span class="text-orange-600 font-bold text-xs">3</span>
                    </div>
                    <p>We may provide a revised quotation or different repair options</p>
                </div>
                <div class="flex items-start">
                    <div class="w-6 h-6 bg-orange-100 rounded-full flex items-center justify-center mr-3 mt-0.5">
                        <span class="text-orange-600 font-bold text-xs">4</span>
                    </div>
                    <p>Your device remains safe with us while we work out the best solution</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Footer -->
<div class="bg-gray-800 text-white py-8 mt-12">
    <div class="max-w-2xl mx-auto px-6 text-center">
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
    // Set quick reason
    function setReason(reason) {
        document.querySelector('textarea[name="customer_notes"]').value = reason;
    }

    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        <?php if ($action === 'approve'): ?>
        const checkboxes = document.querySelectorAll('input[type="checkbox"][required]');
        let allChecked = true;

        checkboxes.forEach(checkbox => {
            if (!checkbox.checked) {
                allChecked = false;
            }
        });

        if (!allChecked) {
            e.preventDefault();
            alert('Please confirm all agreements before proceeding.');
            return;
        }

        if (!confirm('Are you sure you want to approve this quotation? This will authorize us to begin the repair work.')) {
            e.preventDefault();
            return;
        }
        <?php else: ?>
        const notes = document.querySelector('textarea[name="customer_notes"]').value.trim();
        if (!notes) {
            e.preventDefault();
            alert('Please provide a reason for declining this quotation.');
            return;
        }

        if (!confirm('Are you sure you want to decline this quotation?')) {
            e.preventDefault();
            return;
        }
        <?php endif; ?>

        // Show loading state
        const submitBtn = document.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
        submitBtn.disabled = true;
    });
</script>

</body>
</html>
<?= $this->extend('admin/layout/main') ?>

<?= $this->section('content') ?>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Device Diagnosis</h1>
                <p class="text-gray-600">Order #<?= $order['order_number'] ?> - <?= $order['customer_name'] ?></p>
            </div>
            <div class="flex space-x-2">
                <a href="/admin/orders/<?= $order['id'] ?>" class="text-gray-600 hover:text-gray-900 px-4 py-2">
                    <i class="fas fa-eye mr-2"></i>View Order
                </a>
                <a href="/admin/orders/diagnosis-queue" class="text-gray-600 hover:text-gray-900 px-4 py-2">
                    <i class="fas fa-list mr-2"></i>Diagnosis Queue
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Diagnosis Form -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Device Info -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Device Information</h3>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-600">Type:</span>
                            <span class="font-medium ml-2"><?= $order['device_type_name'] ?></span>
                        </div>
                        <div>
                            <span class="text-gray-600">Brand:</span>
                            <span class="font-medium ml-2"><?= $order['device_brand'] ?> <?= $order['device_model'] ?></span>
                        </div>
                        <div>
                            <span class="text-gray-600">Serial:</span>
                            <span class="font-medium ml-2"><?= $order['device_serial'] ?: 'N/A' ?></span>
                        </div>
                        <div>
                            <span class="text-gray-600">Customer Problem:</span>
                            <span class="font-medium ml-2"><?= truncate_text($order['problem_description'], 50) ?></span>
                        </div>
                    </div>
                </div>

                <!-- Diagnosis Form -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <form action="/admin/orders/<?= $order['id'] ?>/diagnosis" method="POST" class="space-y-6" id="diagnosisForm">
                        <?= csrf_field() ?>

                        <!-- Issues Found -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Issues Found</h3>
                            <div id="issuesContainer">
                                <div class="issue-item bg-gray-50 p-4 rounded-lg mb-4">
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Issue Description</label>
                                            <input type="text" name="issues[0][description]"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                   placeholder="Describe the issue found...">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Severity</label>
                                            <select name="issues[0][severity]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                                <option value="low">Low</option>
                                                <option value="medium" selected>Medium</option>
                                                <option value="high">High</option>
                                                <option value="critical">Critical</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Estimated Cost (Rp)</label>
                                            <input type="number" name="issues[0][estimated_cost]"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                   placeholder="0" min="0" step="1000">
                                        </div>
                                        <div class="flex items-end">
                                            <label class="flex items-center">
                                                <input type="checkbox" name="issues[0][repair_needed]" value="1" checked
                                                       class="mr-2 text-blue-600 rounded focus:ring-blue-500">
                                                <span class="text-sm text-gray-700">Repair Required</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="button" onclick="addIssue()" class="mt-2 px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors">
                                <i class="fas fa-plus mr-2"></i>Add Another Issue
                            </button>
                        </div>

                        <!-- Quick Issues -->
                        <div>
                            <h4 class="font-medium text-gray-800 mb-3">Common Issues (Click to add)</h4>
                            <div class="flex flex-wrap gap-2">
                                <?php foreach ($common_issues as $issue): ?>
                                    <button type="button" onclick="addCommonIssue('<?= addslashes($issue) ?>')"
                                            class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-gray-200 transition-colors">
                                        <?= $issue ?>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Detailed Diagnosis -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Detailed Diagnosis Notes *</label>
                            <textarea name="diagnosis_notes" rows="5" required
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="Provide detailed diagnosis findings, test results, and technical observations..."><?= old('diagnosis_notes', $order['diagnosis_notes'] ?? '') ?></textarea>
                        </div>

                        <!-- Recommended Actions -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Recommended Actions *</label>
                            <textarea name="recommended_actions" rows="4" required
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="Recommend repair actions, parts needed, and next steps..."><?= old('recommended_actions', $order['recommended_actions'] ?? '') ?></textarea>
                        </div>

                        <!-- Estimates -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Estimated Repair Hours</label>
                                <input type="number" name="estimated_hours" step="0.5" min="0"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="0.0" value="<?= old('estimated_hours', $order['estimated_hours'] ?? '') ?>">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Total Estimated Cost (Rp)</label>
                                <input type="number" name="estimated_cost" min="0" step="1000"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="0" value="<?= old('estimated_cost', $order['estimated_cost'] ?? '') ?>" id="totalCostInput">
                            </div>
                        </div>

                        <!-- Customer Contact -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h4 class="font-medium text-blue-800 mb-3">
                                <i class="fas fa-phone mr-2"></i>Customer Communication
                            </h4>
                            <label class="flex items-center">
                                <input type="checkbox" name="contact_customer" value="1"
                                       class="mr-2 text-blue-600 rounded focus:ring-blue-500">
                                <span class="text-sm text-blue-700">Contact customer immediately after saving diagnosis</span>
                            </label>
                            <p class="text-xs text-blue-600 mt-2">
                                Customer will be contacted with diagnosis results and cost estimate for approval
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
                            <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                <i class="fas fa-check mr-2"></i>Complete Diagnosis
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Status -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Diagnosis Status</h3>
                    <div class="space-y-3">
                        <div>
                            <span class="text-gray-600">Current Status:</span>
                            <span class="ml-2 px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                                <?= ucfirst(str_replace('_', ' ', $order['diagnosis_status'] ?? 'pending')) ?>
                            </span>
                        </div>
                        <?php if ($order['diagnosed_by_name']): ?>
                            <div>
                                <span class="text-gray-600">Diagnosed by:</span>
                                <span class="ml-2 font-medium"><?= $order['diagnosed_by_name'] ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ($order['diagnosis_date']): ?>
                            <div>
                                <span class="text-gray-600">Diagnosis Date:</span>
                                <span class="ml-2 font-medium"><?= date('M d, Y H:i', strtotime($order['diagnosis_date'])) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Customer Problem -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Customer's Problem</h3>
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <p class="text-gray-900 text-sm"><?= nl2br($order['problem_description']) ?></p>
                    </div>
                    <?php if ($order['accessories']): ?>
                        <div class="mt-4">
                            <h4 class="font-medium text-gray-800 mb-2">Accessories</h4>
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <p class="text-gray-900 text-sm"><?= nl2br($order['accessories']) ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Previous Diagnosis (if exists) -->
                <?php if (!empty($order['diagnosis_notes'])): ?>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-yellow-800 mb-4">
                            <i class="fas fa-history mr-2"></i>Previous Diagnosis
                        </h3>
                        <div class="space-y-3 text-sm">
                            <div>
                                <span class="font-medium text-yellow-800">Notes:</span>
                                <p class="text-yellow-700 mt-1"><?= nl2br($order['diagnosis_notes']) ?></p>
                            </div>
                            <?php if ($order['recommended_actions']): ?>
                                <div>
                                    <span class="font-medium text-yellow-800">Recommended Actions:</span>
                                    <p class="text-yellow-700 mt-1"><?= nl2br($order['recommended_actions']) ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        let issueCounter = 1;

        function addIssue() {
            const container = document.getElementById('issuesContainer');
            const newIssue = document.createElement('div');
            newIssue.className = 'issue-item bg-gray-50 p-4 rounded-lg mb-4';
            newIssue.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Issue Description</label>
                        <input type="text" name="issues[${issueCounter}][description]"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Describe the issue found...">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Severity</label>
                        <select name="issues[${issueCounter}][severity]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                            <option value="critical">Critical</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Estimated Cost (Rp)</label>
                        <input type="number" name="issues[${issueCounter}][estimated_cost]"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent issue-cost"
                               placeholder="0" min="0" step="1000" onchange="calculateTotalCost()">
                    </div>
                    <div class="flex items-end">
                        <label class="flex items-center">
                            <input type="checkbox" name="issues[${issueCounter}][repair_needed]" value="1" checked
                                   class="mr-2 text-blue-600 rounded focus:ring-blue-500">
                            <span class="text-sm text-gray-700">Repair Required</span>
                        </label>
                    </div>
                    <div class="flex items-end">
                        <button type="button" onclick="removeIssue(this)" class="px-3 py-1 bg-red-100 text-red-700 rounded hover:bg-red-200 transition-colors text-sm">
                            <i class="fas fa-times mr-1"></i>Remove
                        </button>
                    </div>
                </div>
            `;
            container.appendChild(newIssue);
            issueCounter++;
        }

        function removeIssue(button) {
            const issueItem = button.closest('.issue-item');
            issueItem.remove();
            calculateTotalCost();
        }

        function addCommonIssue(issueText) {
            // Find the first empty issue field
            const issueInputs = document.querySelectorAll('input[name*="[description]"]');
            let emptyInput = null;

            for (let input of issueInputs) {
                if (!input.value.trim()) {
                    emptyInput = input;
                    break;
                }
            }

            if (!emptyInput) {
                addIssue();
                emptyInput = document.querySelector('input[name*="[description]"]:last-of-type');
            }

            if (emptyInput) {
                emptyInput.value = issueText;
                emptyInput.focus();
            }
        }

        function calculateTotalCost() {
            const costInputs = document.querySelectorAll('.issue-cost');
            let total = 0;

            costInputs.forEach(input => {
                const value = parseFloat(input.value) || 0;
                total += value;
            });

            document.getElementById('totalCostInput').value = total;
        }

        function saveDraft() {
            const formData = new FormData(document.getElementById('diagnosisForm'));
            const draftData = {
                timestamp: new Date().toISOString(),
                data: Object.fromEntries(formData)
            };

            localStorage.setItem('diagnosis_draft_<?= $order['id'] ?>', JSON.stringify(draftData));

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
            const draft = localStorage.getItem('diagnosis_draft_<?= $order['id'] ?>');
            if (draft) {
                const draftData = JSON.parse(draft);
                const draftAge = new Date() - new Date(draftData.timestamp);

                // Only restore if draft is less than 24 hours old
                if (draftAge < 24 * 60 * 60 * 1000) {
                    if (confirm('Found a saved draft. Would you like to restore it?')) {
                        // Restore form data
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
                        calculateTotalCost();
                    } else {
                        localStorage.removeItem('diagnosis_draft_<?= $order['id'] ?>');
                    }
                } else {
                    localStorage.removeItem('diagnosis_draft_<?= $order['id'] ?>');
                }
            }
        });

        // Clear draft on form submission
        document.getElementById('diagnosisForm').addEventListener('submit', function() {
            localStorage.removeItem('diagnosis_draft_<?= $order['id'] ?>');
        });

        // Auto-calculate total cost when individual costs change
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('issue-cost')) {
                calculateTotalCost();
            }
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
                document.getElementById('diagnosisForm').submit();
            }
        });

        // Form validation
        document.getElementById('diagnosisForm').addEventListener('submit', function(e) {
            const diagnosisNotes = document.querySelector('textarea[name="diagnosis_notes"]').value.trim();
            const recommendedActions = document.querySelector('textarea[name="recommended_actions"]').value.trim();

            if (diagnosisNotes.length < 10) {
                alert('Please provide more detailed diagnosis notes (minimum 10 characters).');
                e.preventDefault();
                return;
            }

            if (recommendedActions.length < 10) {
                alert('Please provide more detailed recommended actions (minimum 10 characters).');
                e.preventDefault();
                return;
            }

            // Check if at least one issue is described
            const issueInputs = document.querySelectorAll('input[name*="[description]"]');
            let hasIssue = false;
            issueInputs.forEach(input => {
                if (input.value.trim()) {
                    hasIssue = true;
                }
            });

            if (!hasIssue) {
                if (!confirm('No specific issues were listed. Continue with general diagnosis only?')) {
                    e.preventDefault();
                    return;
                }
            }
        });
    </script>
<?= $this->endSection() ?>
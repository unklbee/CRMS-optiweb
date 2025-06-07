<?php $this->extend('admin/layout/main'); ?>

<?php $this->section('content'); ?>

    <div class="container mx-auto px-4 py-6">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800"><?= $title ?></h1>
                    <p class="text-gray-600 mt-1">Complete diagnosis for this device</p>
                </div>
                <div class="flex space-x-3">
                    <a href="/admin/diagnosis/<?= $order['id'] ?>"
                       class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Diagnosis
                    </a>
                    <a href="/admin/orders/<?= $order['id'] ?>"
                       class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors">
                        <i class="fas fa-file-alt mr-2"></i>View Order
                    </a>
                </div>
            </div>
        </div>

        <!-- Alert Messages -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('errors')): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <ul class="list-disc list-inside">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

            <!-- Left Sidebar - Order Info & Templates -->
            <div class="lg:col-span-1 space-y-6">

                <!-- Order Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Order Information</h3>
                    <div class="space-y-3 text-sm">
                        <div>
                            <span class="text-gray-600">Order:</span>
                            <span class="font-medium ml-2">#<?= $order['order_number'] ?></span>
                        </div>
                        <div>
                            <span class="text-gray-600">Customer:</span>
                            <span class="font-medium ml-2"><?= $order['customer_name'] ?></span>
                        </div>
                        <div>
                            <span class="text-gray-600">Device:</span>
                            <span class="font-medium ml-2"><?= $order['device_brand'] ?> <?= $order['device_model'] ?></span>
                        </div>
                        <div>
                            <span class="text-gray-600">Type:</span>
                            <span class="font-medium ml-2"><?= $order['device_type_name'] ?></span>
                        </div>
                    </div>
                </div>

                <!-- Customer Problem -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Customer's Problem</h3>
                    <div class="bg-gray-50 p-3 rounded-lg text-sm">
                        <p class="text-gray-900"><?= nl2br($order['problem_description']) ?></p>
                    </div>
                    <?php if ($order['accessories']): ?>
                        <div class="mt-4">
                            <h4 class="font-medium text-gray-800 mb-2 text-sm">Accessories</h4>
                            <div class="bg-gray-50 p-3 rounded-lg text-sm">
                                <p class="text-gray-900"><?= nl2br($order['accessories']) ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Diagnosis Templates -->
                <?php if (!empty($templates)): ?>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Templates</h3>
                        <div class="space-y-2">
                            <?php foreach ($templates as $template): ?>
                                <button type="button" onclick="useTemplate(<?= htmlspecialchars(json_encode($template)) ?>)"
                                        class="w-full text-left p-3 border border-gray-200 rounded-lg hover:border-blue-300 hover:bg-blue-50 transition-colors text-sm">
                                    <div class="font-medium text-gray-900"><?= $template['title'] ?></div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        <?= isset($template['estimated_hours']) ? $template['estimated_hours'] . ' hours' : 'Quick template' ?>
                                    </div>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Common Issues -->
                <?php if (!empty($common_issues)): ?>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Common Issues</h3>
                        <div class="space-y-2">
                            <?php foreach ($common_issues as $issue): ?>
                                <button type="button" onclick="addCommonIssue('<?= htmlspecialchars($issue) ?>')"
                                        class="w-full text-left p-2 border border-gray-200 rounded hover:border-blue-300 hover:bg-blue-50 transition-colors text-sm">
                                    <?= $issue ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

            </div>

            <!-- Main Content - Diagnosis Form -->
            <div class="lg:col-span-3">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">

                    <form action="/admin/diagnosis/<?= $order['id'] ?>/store" method="POST" class="space-y-8" id="diagnosisForm">
                        <?= csrf_field() ?>

                        <!-- Issues Found Section -->
                        <div>
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold text-gray-800">Issues Found</h3>
                                <button type="button" onclick="addIssueField()"
                                        class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors text-sm">
                                    <i class="fas fa-plus mr-2"></i>Add Issue
                                </button>
                            </div>

                            <div id="issuesContainer">
                                <!-- Initial issue field -->
                                <div class="issue-item bg-gray-50 p-4 rounded-lg mb-4" data-issue-index="0">
                                    <div class="flex justify-between items-center mb-3">
                                        <h4 class="font-medium text-gray-800">Issue #1</h4>
                                        <button type="button" onclick="removeIssueField(this)"
                                                class="text-red-600 hover:text-red-800 text-sm hidden">
                                            <i class="fas fa-trash"></i> Remove
                                        </button>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Issue Description *</label>
                                            <input type="text" name="issues[0][description]" required
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                   placeholder="Describe the issue found...">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Severity</label>
                                            <select name="issues[0][severity]"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                                <option value="low">Low</option>
                                                <option value="medium" selected>Medium</option>
                                                <option value="high">High</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        <label class="flex items-center">
                                            <input type="hidden" name="issues[0][repair_needed]" value="0">
                                            <input type="checkbox" name="issues[0][repair_needed]" value="1" checked
                                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                            <span class="ml-2 text-sm text-gray-700">Repair needed for this issue</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Diagnosis Notes -->
                        <div>
                            <label for="diagnosis_notes" class="block text-sm font-medium text-gray-700 mb-2">
                                Diagnosis Notes *
                            </label>
                            <textarea name="diagnosis_notes" id="diagnosis_notes" rows="6" required
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="Provide detailed diagnosis notes including your findings, inspection results, and technical assessment..."><?= old('diagnosis_notes') ?></textarea>
                            <p class="text-xs text-gray-500 mt-1">Minimum 10 characters required</p>
                        </div>

                        <!-- Recommended Actions -->
                        <div>
                            <label for="recommended_actions" class="block text-sm font-medium text-gray-700 mb-2">
                                Recommended Actions *
                            </label>
                            <textarea name="recommended_actions" id="recommended_actions" rows="5" required
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="Describe the recommended repair actions, parts needed, and any precautions..."><?= old('recommended_actions') ?></textarea>
                            <p class="text-xs text-gray-500 mt-1">Minimum 10 characters required</p>
                        </div>

                        <!-- Estimated Hours -->
                        <div>
                            <label for="estimated_hours" class="block text-sm font-medium text-gray-700 mb-2">
                                Estimated Work Hours
                            </label>
                            <div class="flex items-center space-x-4">
                                <input type="number" name="estimated_hours" id="estimated_hours" step="0.5" min="0" max="100"
                                       class="w-32 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="0.0" value="<?= old('estimated_hours') ?>">
                                <span class="text-sm text-gray-600">hours (optional)</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Estimated time to complete the repair work</p>
                        </div>

                        <!-- Additional Options -->
                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Additional Options</h3>

                            <div class="space-y-3">
                                <label class="flex items-center">
                                    <input type="checkbox" name="create_quotation" value="1"
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700">Automatically create quotation after diagnosis</span>
                                </label>

                                <label class="flex items-center">
                                    <input type="checkbox" name="notify_customer" value="1" checked
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700">Send diagnosis notification to customer</span>
                                </label>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                            <a href="/admin/diagnosis/<?= $order['id'] ?>"
                               class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                                <i class="fas fa-times mr-2"></i>Cancel
                            </a>
                            <button type="button" onclick="saveDraft()"
                                    class="px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                                <i class="fas fa-save mr-2"></i>Save Draft
                            </button>
                            <button type="submit"
                                    class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                                <i class="fas fa-check mr-2"></i>Complete Diagnosis
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <script>
        let issueCounter = 1;

        // Add new issue field
        function addIssueField() {
            const container = document.getElementById('issuesContainer');
            const newIssueHtml = `
            <div class="issue-item bg-gray-50 p-4 rounded-lg mb-4" data-issue-index="${issueCounter}">
                <div class="flex justify-between items-center mb-3">
                    <h4 class="font-medium text-gray-800">Issue #${issueCounter + 1}</h4>
                    <button type="button" onclick="removeIssueField(this)"
                            class="text-red-600 hover:text-red-800 text-sm">
                        <i class="fas fa-trash"></i> Remove
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Issue Description *</label>
                        <input type="text" name="issues[${issueCounter}][description]" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Describe the issue found...">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Severity</label>
                        <select name="issues[${issueCounter}][severity]"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>
                </div>

                <div class="mt-3">
                    <label class="flex items-center">
                        <input type="hidden" name="issues[${issueCounter}][repair_needed]" value="0">
                        <input type="checkbox" name="issues[${issueCounter}][repair_needed]" value="1" checked
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">Repair needed for this issue</span>
                    </label>
                </div>
            </div>
        `;

            container.insertAdjacentHTML('beforeend', newIssueHtml);
            issueCounter++;

            // Show remove buttons if more than one issue
            updateRemoveButtons();
        }

        // Remove issue field
        function removeIssueField(button) {
            const issueItem = button.closest('.issue-item');
            issueItem.remove();
            updateRemoveButtons();
            renumberIssues();
        }

        // Update remove button visibility
        function updateRemoveButtons() {
            const issues = document.querySelectorAll('.issue-item');
            const removeButtons = document.querySelectorAll('.issue-item button[onclick*="removeIssueField"]');

            removeButtons.forEach(button => {
                if (issues.length > 1) {
                    button.classList.remove('hidden');
                } else {
                    button.classList.add('hidden');
                }
            });
        }

        // Renumber issues after removal
        function renumberIssues() {
            const issues = document.querySelectorAll('.issue-item');
            issues.forEach((issue, index) => {
                const title = issue.querySelector('h4');
                title.textContent = `Issue #${index + 1}`;
                issue.setAttribute('data-issue-index', index);
            });
        }

        // Use template
        function useTemplate(template) {
            if (confirm('This will replace current diagnosis notes and recommended actions. Continue?')) {
                document.getElementById('diagnosis_notes').value = template.common_issues || '';
                document.getElementById('recommended_actions').value = template.recommended_actions || '';

                if (template.estimated_hours) {
                    document.getElementById('estimated_hours').value = template.estimated_hours;
                }
            }
        }

        // Add common issue as new issue field
        function addCommonIssue(issueDescription) {
            // Add new issue field first
            addIssueField();

            // Get the last added issue field and populate it
            const lastIssue = document.querySelector('.issue-item:last-child');
            const descriptionInput = lastIssue.querySelector('input[name*="[description]"]');
            descriptionInput.value = issueDescription;
        }

        // Save draft functionality
        function saveDraft() {
            // You can implement auto-save to local storage or server
            const formData = new FormData(document.getElementById('diagnosisForm'));
            console.log('Saving draft...', Object.fromEntries(formData));

            // Show notification
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
            notification.textContent = 'Draft saved locally';
            document.body.appendChild(notification);

            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        // Form validation
        document.getElementById('diagnosisForm').addEventListener('submit', function(e) {
            const diagnosisNotes = document.getElementById('diagnosis_notes').value.trim();
            const recommendedActions = document.getElementById('recommended_actions').value.trim();

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

            // Show loading state
            const submitButton = document.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
            submitButton.disabled = true;

            // Re-enable button after a delay in case of errors
            setTimeout(() => {
                submitButton.innerHTML = originalText;
                submitButton.disabled = false;
            }, 10000);
        });

        // Auto-save form data to localStorage every 30 seconds
        setInterval(() => {
            const formData = new FormData(document.getElementById('diagnosisForm'));
            const data = Object.fromEntries(formData);
            localStorage.setItem(`diagnosis_draft_${<?= $order['id'] ?>}`, JSON.stringify(data));
        }, 30000);

        // Load draft on page load
        document.addEventListener('DOMContentLoaded', function() {
            const savedDraft = localStorage.getItem(`diagnosis_draft_${<?= $order['id'] ?>}`);
            if (savedDraft && confirm('Found a saved draft. Would you like to restore it?')) {
                const data = JSON.parse(savedDraft);

                // Restore form fields
                Object.keys(data).forEach(key => {
                    const element = document.querySelector(`[name="${key}"]`);
                    if (element) {
                        if (element.type === 'checkbox') {
                            element.checked = data[key] === '1';
                        } else {
                            element.value = data[key];
                        }
                    }
                });
            }
        });
    </script>

<?php $this->endSection(); ?>
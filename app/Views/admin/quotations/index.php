<?= $this->extend('admin/layout/main') ?>

<?= $this->section('content') ?>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Quotation Management</h1>
                <p class="text-gray-600">Manage repair quotations and customer approvals</p>
            </div>
            <div class="flex space-x-3">
                <a href="/admin/quotations/analytics" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                    <i class="fas fa-chart-bar mr-2"></i>Analytics
                </a>
                <a href="/admin/quotations/export" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-download mr-2"></i>Export
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Total</p>
                        <p class="text-3xl font-bold text-gray-800"><?= $stats['total'] ?></p>
                    </div>
                    <div class="bg-gray-100 p-3 rounded-full">
                        <i class="fas fa-file-invoice-dollar text-gray-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Draft</p>
                        <p class="text-3xl font-bold text-yellow-600"><?= $stats['draft'] ?></p>
                    </div>
                    <div class="bg-yellow-100 p-3 rounded-full">
                        <i class="fas fa-edit text-yellow-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Sent</p>
                        <p class="text-3xl font-bold text-blue-600"><?= $stats['sent'] ?></p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-paper-plane text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Approved</p>
                        <p class="text-3xl font-bold text-green-600"><?= $stats['approved'] ?></p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Rejected</p>
                        <p class="text-3xl font-bold text-red-600"><?= $stats['rejected'] ?></p>
                    </div>
                    <div class="bg-red-100 p-3 rounded-full">
                        <i class="fas fa-times-circle text-red-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <form method="GET" class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <input type="text" name="search" placeholder="Search by quotation number, order number, or customer name"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           value="<?= $search ?>">
                </div>
                <div>
                    <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Status</option>
                        <?php foreach ($statuses as $statusValue => $statusLabel): ?>
                            <option value="<?= $statusValue ?>" <?= $status === $statusValue ? 'selected' : '' ?>>
                                <?= $statusLabel ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="bg-gray-800 text-white px-6 py-2 rounded-lg hover:bg-gray-900 transition-colors">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
                <a href="/admin/quotations" class="bg-gray-100 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-200 transition-colors">
                    <i class="fas fa-times mr-2"></i>Clear
                </a>
            </form>
        </div>

        <!-- Bulk Actions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <form method="POST" action="/admin/quotations/bulk-action" id="bulkForm">
                <?= csrf_field() ?>
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <select name="bulk_action" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            <option value="">Bulk Actions</option>
                            <option value="send_reminders">Send Reminders</option>
                            <option value="mark_expired">Mark as Expired</option>
                            <option value="delete">Delete Selected</option>
                        </select>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm">
                            Apply
                        </button>
                    </div>
                    <div class="text-sm text-gray-600">
                        <span id="selectedCount">0</span> selected
                    </div>
                </div>
            </form>
        </div>

        <!-- Quotations Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" id="selectAll" class="rounded text-blue-600">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quotation</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Device</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valid Until</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (!empty($quotations)): ?>
                        <?php foreach ($quotations as $quotation): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="checkbox" name="quotation_ids[]" value="<?= $quotation['id'] ?>"
                                           class="quotation-checkbox rounded text-blue-600">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                                            <i class="fas fa-file-invoice-dollar text-purple-600"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900"><?= $quotation['quotation_number'] ?></div>
                                            <div class="text-sm text-gray-500">Order: <?= $quotation['order_number'] ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900"><?= $quotation['customer_name'] ?></div>
                                        <div class="text-sm text-gray-500"><?= $quotation['customer_phone'] ?></div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?= $quotation['device_type_name'] ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?= format_currency($quotation['total_cost']) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php
                                    $statusColors = [
                                        'draft' => 'bg-yellow-100 text-yellow-800',
                                        'sent' => 'bg-blue-100 text-blue-800',
                                        'approved' => 'bg-green-100 text-green-800',
                                        'rejected' => 'bg-red-100 text-red-800',
                                        'expired' => 'bg-gray-100 text-gray-800',
                                        'superseded' => 'bg-purple-100 text-purple-800'
                                    ];
                                    $color = $statusColors[$quotation['status']] ?? 'bg-gray-100 text-gray-800';
                                    ?>
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?= $color ?>">
                                        <?= ucfirst($quotation['status']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($quotation['valid_until']): ?>
                                        <div class="text-sm text-gray-900">
                                            <?= date('M d, Y', strtotime($quotation['valid_until'])) ?>
                                        </div>
                                        <?php if (strtotime($quotation['valid_until']) < time() && $quotation['status'] === 'sent'): ?>
                                            <div class="text-xs text-red-600">EXPIRED</div>
                                        <?php elseif (strtotime($quotation['valid_until']) < strtotime('+3 days') && $quotation['status'] === 'sent'): ?>
                                            <div class="text-xs text-orange-600">EXPIRING SOON</div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-gray-400">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                    <div class="flex items-center space-x-2">
                                        <!-- View -->
                                        <a href="/admin/quotations/<?= $quotation['id'] ?>"
                                           class="text-blue-600 hover:text-blue-900" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        <!-- Print -->
                                        <a href="/admin/quotations/<?= $quotation['id'] ?>?print=1" target="_blank"
                                           class="text-purple-600 hover:text-purple-900" title="Print">
                                            <i class="fas fa-print"></i>
                                        </a>

                                        <!-- Actions based on status -->
                                        <?php if ($quotation['status'] === 'draft'): ?>
                                            <a href="/admin/orders/<?= $quotation['order_id'] ?>/quotation/edit"
                                               class="text-green-600 hover:text-green-900" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        <?php elseif ($quotation['status'] === 'sent'): ?>
                                            <button onclick="sendReminder(<?= $quotation['id'] ?>)"
                                                    class="text-orange-600 hover:text-orange-900" title="Send Reminder">
                                                <i class="fas fa-bell"></i>
                                            </button>
                                        <?php elseif ($quotation['status'] === 'approved'): ?>
                                            <a href="/admin/orders/<?= $quotation['order_id'] ?>"
                                               class="text-green-600 hover:text-green-900" title="View Order">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                        <?php endif; ?>

                                        <!-- Duplicate -->
                                        <button onclick="duplicateQuotation(<?= $quotation['id'] ?>)"
                                                class="text-gray-600 hover:text-gray-900" title="Duplicate">
                                            <i class="fas fa-copy"></i>
                                        </button>

                                        <!-- More actions dropdown -->
                                        <div class="relative inline-block text-left">
                                            <button onclick="toggleDropdown(<?= $quotation['id'] ?>)"
                                                    class="text-gray-600 hover:text-gray-900">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <div id="dropdown-<?= $quotation['id'] ?>"
                                                 class="hidden origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                                                <div class="py-1">
                                                    <?php if ($quotation['status'] === 'sent'): ?>
                                                        <a href="/admin/quotations/<?= $quotation['id'] ?>/mark-expired"
                                                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Mark Expired</a>
                                                    <?php endif; ?>
                                                    <a href="/admin/quotations/<?= $quotation['id'] ?>/duplicate"
                                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Duplicate</a>
                                                    <a href="/admin/orders/<?= $quotation['order_id'] ?>/quotation/revise"
                                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Create Revision</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-file-invoice-dollar text-4xl mb-4 text-gray-300"></i>
                                <h3 class="text-xl font-semibold text-gray-800 mb-2">No Quotations Found</h3>
                                <p class="text-gray-600">
                                    <?php if ($search || $status): ?>
                                        No quotations match your current filters.
                                    <?php else: ?>
                                        No quotations have been created yet.
                                    <?php endif; ?>
                                </p>
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <?php if (isset($pager) && $pager->getPageCount() > 1): ?>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex flex-col sm:flex-row justify-between items-center">
                    <div class="text-sm text-gray-700 mb-4 sm:mb-0">
                        Showing <?= $pager->getFirstItem() ?> to <?= $pager->getLastItem() ?> of <?= $pager->getTotal() ?> results
                    </div>
                    <div class="flex space-x-1">
                        <?= $pager->links() ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Quick Links -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <a href="/admin/quotations/pending" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-clock text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">Pending Quotations</h3>
                        <p class="text-sm text-gray-600">Sent and awaiting customer response</p>
                    </div>
                </div>
            </a>

            <a href="/admin/quotations/expired" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">Expired Quotations</h3>
                        <p class="text-sm text-gray-600">Quotations that have passed validity date</p>
                    </div>
                </div>
            </a>

            <a href="/admin/quotations/analytics" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-chart-bar text-purple-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">Analytics & Reports</h3>
                        <p class="text-sm text-gray-600">Conversion rates and performance metrics</p>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <script>
        // Select all functionality
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.quotation-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateSelectedCount();
        });

        // Update selected count
        function updateSelectedCount() {
            const checkedBoxes = document.querySelectorAll('.quotation-checkbox:checked');
            document.getElementById('selectedCount').textContent = checkedBoxes.length;
        }

        // Listen for individual checkbox changes
        document.querySelectorAll('.quotation-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectedCount);
        });

        // Bulk form validation
        document.getElementById('bulkForm').addEventListener('submit', function(e) {
            const checkedBoxes = document.querySelectorAll('.quotation-checkbox:checked');
            const action = document.querySelector('select[name="bulk_action"]').value;

            if (checkedBoxes.length === 0) {
                e.preventDefault();
                alert('Please select at least one quotation.');
                return;
            }

            if (!action) {
                e.preventDefault();
                alert('Please select an action.');
                return;
            }

            if (action === 'delete') {
                if (!confirm(`Are you sure you want to delete ${checkedBoxes.length} quotation(s)? This action cannot be undone.`)) {
                    e.preventDefault();
                    return;
                }
            } else if (action === 'mark_expired') {
                if (!confirm(`Are you sure you want to mark ${checkedBoxes.length} quotation(s) as expired?`)) {
                    e.preventDefault();
                    return;
                }
            }

            // Add selected IDs to form
            checkedBoxes.forEach(checkbox => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'quotation_ids[]';
                input.value = checkbox.value;
                this.appendChild(input);
            });
        });

        // Dropdown toggle
        function toggleDropdown(id) {
            const dropdown = document.getElementById(`dropdown-${id}`);
            // Close other dropdowns
            document.querySelectorAll('[id^="dropdown-"]').forEach(el => {
                if (el.id !== `dropdown-${id}`) {
                    el.classList.add('hidden');
                }
            });
            dropdown.classList.toggle('hidden');
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('[id^="dropdown-"]') && !e.target.closest('button')) {
                document.querySelectorAll('[id^="dropdown-"]').forEach(el => {
                    el.classList.add('hidden');
                });
            }
        });

        // Send reminder
        function sendReminder(quotationId) {
            if (confirm('Send reminder email to customer?')) {
                window.location.href = `/admin/quotations/${quotationId}/send-reminder`;
            }
        }

        // Duplicate quotation
        function duplicateQuotation(quotationId) {
            if (confirm('Create a duplicate of this quotation?')) {
                window.location.href = `/admin/quotations/${quotationId}/duplicate`;
            }
        }

        // Auto-refresh for pending quotations
        if (window.location.search.includes('status=sent')) {
            setInterval(() => {
                if (!document.hidden) {
                    window.location.reload();
                }
            }, 60000); // Refresh every minute
        }
    </script>
<?= $this->endSection() ?>
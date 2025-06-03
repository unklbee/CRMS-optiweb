<?php
// app/Views/admin/customers/edit.php - Complete version
?>
<?= $this->extend('admin/layout/main') ?>

<?= $this->section('content') ?>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit Customer</h1>
                <p class="text-gray-600"><?= $customer['full_name'] ?></p>
            </div>
            <div class="flex space-x-2">
                <a href="/admin/customers/<?= $customer['id'] ?>" class="text-gray-600 hover:text-gray-900 px-4 py-2">
                    <i class="fas fa-eye mr-2"></i>View Details
                </a>
                <a href="/admin/customers" class="text-gray-600 hover:text-gray-900 px-4 py-2">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Customers
                </a>
            </div>
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

            <form action="/admin/customers/<?= $customer['id'] ?>" method="POST" class="space-y-6">
                <?= csrf_field() ?>
                <input type="hidden" name="_method" value="PUT">

                <!-- Personal Information Section -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Personal Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Full Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="full_name" value="<?= old('full_name', $customer['full_name']) ?>" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                                   placeholder="Enter customer's full name">
                            <?php if (isset($errors['full_name'])): ?>
                                <p class="text-red-500 text-sm mt-1"><?= $errors['full_name'] ?></p>
                            <?php endif; ?>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Phone Number <span class="text-red-500">*</span>
                            </label>
                            <input type="tel" name="phone" value="<?= old('phone', $customer['phone']) ?>" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                                   placeholder="e.g., +62-21-1234567 or 081234567890">
                            <?php if (isset($errors['phone'])): ?>
                                <p class="text-red-500 text-sm mt-1"><?= $errors['phone'] ?></p>
                            <?php endif; ?>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Email Address
                            </label>
                            <input type="email" name="email" value="<?= old('email', $customer['email']) ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                                   placeholder="customer@example.com">
                            <p class="text-xs text-gray-500 mt-1">Optional - for sending repair notifications</p>
                            <?php if (isset($errors['email'])): ?>
                                <p class="text-red-500 text-sm mt-1"><?= $errors['email'] ?></p>
                            <?php endif; ?>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Customer Since
                            </label>
                            <input type="text" value="<?= date('M d, Y', strtotime($customer['created_at'])) ?>" readonly
                                   class="w-full px-4 py-2 border border-gray-200 rounded-lg bg-gray-50 text-gray-600">
                        </div>
                    </div>
                </div>

                <!-- Address Information Section -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Address Information</h3>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Full Address
                        </label>
                        <textarea name="address" rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                                  placeholder="Enter customer's complete address including street, city, postal code..."><?= old('address', $customer['address']) ?></textarea>
                        <p class="text-xs text-gray-500 mt-1">Used for delivery and pickup services</p>
                    </div>
                </div>

                <!-- Additional Information Section -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Additional Information</h3>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Notes & Special Instructions
                        </label>
                        <textarea name="notes" rows="4"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                                  placeholder="Any special notes about this customer (preferences, special requirements, etc.)"><?= old('notes', $customer['notes']) ?></textarea>
                        <p class="text-xs text-gray-500 mt-1">Internal notes - visible only to staff members</p>
                    </div>
                </div>

                <!-- Account Information (if linked) -->
                <?php if ($customer['user_id']): ?>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-blue-800 mb-2">
                            <i class="fas fa-user-check mr-2"></i>Linked Account
                        </h3>
                        <p class="text-blue-700 text-sm">
                            This customer has a registered user account and can login to track their orders online.
                        </p>
                        <div class="mt-2">
                            <span class="text-sm text-blue-600">Username: <strong><?= $customer['username'] ?? 'N/A' ?></strong></span>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-gray-700 mb-2">
                            <i class="fas fa-user-plus mr-2"></i>Guest Customer
                        </h3>
                        <p class="text-gray-600 text-sm">
                            This customer doesn't have a registered account. They can create one to track orders online.
                        </p>
                    </div>
                <?php endif; ?>

                <!-- Customer Statistics (Read-only) -->
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Customer Statistics</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-blue-600"><?= $customer['total_orders'] ?? 0 ?></p>
                            <p class="text-sm text-gray-600">Total Orders</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-green-600"><?= $customer['completed_orders'] ?? 0 ?></p>
                            <p class="text-sm text-gray-600">Completed</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-orange-600"><?= $customer['pending_orders'] ?? 0 ?></p>
                            <p class="text-sm text-gray-600">In Progress</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-purple-600"><?= format_currency($customer['total_spent'] ?? 0) ?></p>
                            <p class="text-sm text-gray-600">Total Spent</p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                    <!-- Left side - Danger actions -->
<!--                    <div>-->
<!--                        --><?php //if ($customer['status'] === 'active'): ?>
<!--                            <button type="button" onclick="confirmDeactivate()"-->
<!--                                    class="px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors">-->
<!--                                <i class="fas fa-user-slash mr-2"></i>Deactivate Customer-->
<!--                            </button>-->
<!--                        --><?php //else: ?>
<!--                            <button type="button" onclick="confirmActivate()"-->
<!--                                    class="px-4 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors">-->
<!--                                <i class="fas fa-user-check mr-2"></i>Activate Customer-->
<!--                            </button>-->
<!--                        --><?php //endif; ?>
<!--                    </div>-->

                    <!-- Right side - Main actions -->
                    <div class="flex space-x-4">
                        <a href="/admin/customers"
                           class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </a>
                        <button type="submit"
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                            <i class="fas fa-save mr-2"></i>Update Customer
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Confirmation Modals -->
    <div id="deactivateModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mt-4">Deactivate Customer</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">
                        Are you sure you want to deactivate this customer? They won't be able to place new orders.
                    </p>
                </div>
                <div class="items-center px-4 py-3">
                    <button onclick="closeModal('deactivateModal')"
                            class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md mr-2">Cancel</button>
                    <button onclick="deactivateCustomer()"
                            class="px-4 py-2 bg-red-600 text-white rounded-md">Deactivate</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmDeactivate() {
            document.getElementById('deactivateModal').classList.remove('hidden');
        }

        function confirmActivate() {
            if (confirm('Are you sure you want to activate this customer?')) {
                // Add activate customer logic here
                window.location.href = '/admin/customers/<?= $customer['id'] ?>/activate';
            }
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        function deactivateCustomer() {
            // Add deactivate customer logic here
            window.location.href = '/admin/customers/<?= $customer['id'] ?>/deactivate';
        }

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const fullName = document.querySelector('input[name="full_name"]').value.trim();
            const phone = document.querySelector('input[name="phone"]').value.trim();

            if (fullName.length < 2) {
                alert('Full name must be at least 2 characters long.');
                e.preventDefault();
                return;
            }

            if (phone.length < 10) {
                alert('Phone number must be at least 10 characters long.');
                e.preventDefault();
                return;
            }
        });

        // Phone number formatting
        document.querySelector('input[name="phone"]').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 0) {
                // Format Indonesian phone numbers
                if (value.startsWith('62')) {
                    value = '+' + value;
                } else if (value.startsWith('0')) {
                    value = '+62' + value.substring(1);
                } else if (!value.startsWith('+')) {
                    value = '+62' + value;
                }
            }
            e.target.value = value;
        });
    </script>
<?= $this->endSection() ?>
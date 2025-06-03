<?= $this->extend('admin/layout/main') ?>

<?= $this->section('content') ?>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">User Details</h1>
                <p class="text-gray-600"><?= $user['full_name'] ?></p>
            </div>
            <div class="flex space-x-2">
                <a href="/admin/users/<?= $user['id'] ?>/edit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-edit mr-2"></i>Edit User
                </a>
                <a href="/admin/users" class="text-gray-600 hover:text-gray-900 px-4 py-2">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Users
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- User Information -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">User Information</h3>

                    <div class="space-y-6">
                        <!-- Profile Section -->
                        <div class="flex items-center space-x-4">
                            <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center">
                                <img class="w-20 h-20 rounded-full" src="<?= get_avatar($user['full_name'], 80) ?>" alt="<?= $user['full_name'] ?>">
                            </div>
                            <div>
                                <h4 class="text-xl font-semibold text-gray-900"><?= $user['full_name'] ?></h4>
                                <p class="text-gray-600">@<?= $user['username'] ?></p>
                                <span class="inline-flex px-3 py-1 rounded-full text-sm font-medium mt-1
                                    <?= $user['role'] === 'admin' ? 'bg-purple-100 text-purple-800' :
                                    ($user['role'] === 'technician' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') ?>">
                                    <?= ucfirst($user['role']) ?>
                                </span>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-3">Contact Information</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="flex items-center space-x-3">
                                    <i class="fas fa-envelope text-gray-400 w-5"></i>
                                    <div>
                                        <p class="text-sm text-gray-600">Email</p>
                                        <p class="font-medium text-gray-900"><?= $user['email'] ?></p>
                                    </div>
                                </div>
                                <?php if ($user['phone']): ?>
                                    <div class="flex items-center space-x-3">
                                        <i class="fas fa-phone text-gray-400 w-5"></i>
                                        <div>
                                            <p class="text-sm text-gray-600">Phone</p>
                                            <p class="font-medium text-gray-900"><?= $user['phone'] ?></p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Account Information -->
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-3">Account Information</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-600">Status</p>
                                    <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium
                                        <?= $user['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                        <?= ucfirst($user['status']) ?>
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Role</p>
                                    <p class="font-medium text-gray-900"><?= ucfirst($user['role']) ?></p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Member Since</p>
                                    <p class="font-medium text-gray-900"><?= date('M d, Y', strtotime($user['created_at'])) ?></p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Last Updated</p>
                                    <p class="font-medium text-gray-900"><?= date('M d, Y', strtotime($user['updated_at'])) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Activity/Performance Section -->
                <?php if ($user['role'] === 'technician'): ?>
                    <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Technician Performance</h3>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="text-center p-4 bg-blue-50 rounded-lg">
                                <p class="text-2xl font-bold text-blue-600"><?= $user['total_orders'] ?? 0 ?></p>
                                <p class="text-sm text-blue-800">Total Orders</p>
                            </div>
                            <div class="text-center p-4 bg-green-50 rounded-lg">
                                <p class="text-2xl font-bold text-green-600"><?= $user['completed_orders'] ?? 0 ?></p>
                                <p class="text-sm text-green-800">Completed</p>
                            </div>
                            <div class="text-center p-4 bg-purple-50 rounded-lg">
                                <p class="text-2xl font-bold text-purple-600"><?= number_format($user['avg_rating'] ?? 0, 1) ?></p>
                                <p class="text-sm text-purple-800">Avg Rating</p>
                            </div>
                        </div>

                        <!-- Recent Orders -->
                        <div class="mt-6">
                            <h4 class="font-semibold text-gray-900 mb-3">Recent Orders</h4>
                            <div class="text-center py-6 text-gray-500">
                                <i class="fas fa-clipboard-list text-3xl text-gray-300 mb-2"></i>
                                <p>Recent order history will be shown here</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Actions -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <a href="/admin/users/<?= $user['id'] ?>/edit"
                           class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors text-center block">
                            <i class="fas fa-edit mr-2"></i>Edit User
                        </a>

                        <?php if ($user['status'] === 'active'): ?>
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

                        <button onclick="resetPassword()"
                                class="w-full bg-orange-600 text-white py-2 px-4 rounded-lg hover:bg-orange-700 transition-colors">
                            <i class="fas fa-key mr-2"></i>Reset Password
                        </button>

                        <?php if ($user['id'] != session()->get('user_id')): ?>
                            <button onclick="deleteUser()"
                                    class="w-full bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition-colors">
                                <i class="fas fa-trash mr-2"></i>Delete User
                            </button>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Account Security -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Account Security</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Password:</span>
                            <span class="text-green-600">
                                <i class="fas fa-check-circle mr-1"></i>Set
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Last Login:</span>
<!--                            <span class="text-gray-900">--><?php //= $user['last_login'] ? date('M d, Y', strtotime($user['last_login'])) : 'Never' ?><!--</span>-->
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Login Count:</span>
                            <span class="text-gray-900"><?= $user['login_count'] ?? 0 ?></span>
                        </div>
                    </div>
                </div>

                <!-- Role Permissions -->
                <div class="bg-gradient-to-br from-purple-50 to-indigo-100 rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Role Permissions</h3>
                    <div class="space-y-2 text-sm">
                        <?php
                        $permissions = [];
                        switch($user['role']) {
                            case 'admin':
                                $permissions = [
                                    'Full system access',
                                    'Manage users',
                                    'Manage orders',
                                    'Manage parts & inventory',
                                    'View reports',
                                    'System settings'
                                ];
                                break;
                            case 'technician':
                                $permissions = [
                                    'View assigned orders',
                                    'Update order status',
                                    'Manage parts usage',
                                    'View customer info',
                                    'Create work reports'
                                ];
                                break;
                            case 'customer':
                                $permissions = [
                                    'View own orders',
                                    'Track order status',
                                    'Update profile',
                                    'Submit new orders'
                                ];
                                break;
                        }
                        ?>
                        <?php foreach($permissions as $permission): ?>
                            <div class="flex items-center">
                                <i class="fas fa-check text-green-600 mr-2"></i>
                                <span class="text-gray-700"><?= $permission ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleStatus(newStatus) {
            const action = newStatus === 'active' ? 'activate' : 'deactivate';
            if (confirm(`Are you sure you want to ${action} this user?`)) {
                // Create form and submit
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/admin/users/<?= $user['id'] ?>';

                form.innerHTML = `
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" name="status" value="${newStatus}">
                    <input type="hidden" name="username" value="<?= addslashes($user['username']) ?>">
                    <input type="hidden" name="email" value="<?= addslashes($user['email']) ?>">
                    <input type="hidden" name="full_name" value="<?= addslashes($user['full_name']) ?>">
                    <input type="hidden" name="phone" value="<?= addslashes($user['phone']) ?>">
                    <input type="hidden" name="role" value="<?= $user['role'] ?>">
                `;

                document.body.appendChild(form);
                form.submit();
            }
        }

        function resetPassword() {
            if (confirm('Generate a new password for this user? They will need to be notified of the new password.')) {
                // You can implement password reset functionality here
                alert('Password reset functionality would be implemented here.');
            }
        }

        function deleteUser() {
            if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
                if (confirm('This will permanently delete the user and all associated data. Are you absolutely sure?')) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '/admin/users/<?= $user['id'] ?>';
                    form.innerHTML = `
                        <?= csrf_field() ?>
                        <input type="hidden" name="_method" value="DELETE">
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            }
        }

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // E for edit
            if (e.key === 'e' && !e.ctrlKey && !e.metaKey) {
                const activeElement = document.activeElement;
                if (activeElement.tagName !== 'INPUT' && activeElement.tagName !== 'TEXTAREA') {
                    window.location.href = '/admin/users/<?= $user['id'] ?>/edit';
                }
            }
        });
    </script>
<?= $this->endSection() ?>
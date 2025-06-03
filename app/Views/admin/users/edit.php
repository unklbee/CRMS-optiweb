<?= $this->extend('admin/layout/main') ?>

<?= $this->section('content') ?>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit User</h1>
                <p class="text-gray-600"><?= $user['full_name'] ?> (@<?= $user['username'] ?>)</p>
            </div>
            <div class="flex space-x-2">
                <a href="/admin/users/<?= $user['id'] ?>" class="text-gray-600 hover:text-gray-900 px-4 py-2">
                    <i class="fas fa-eye mr-2"></i>View Details
                </a>
                <a href="/admin/users" class="text-gray-600 hover:text-gray-900 px-4 py-2">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Users
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

            <form action="/admin/users/<?= $user['id'] ?>" method="POST" class="space-y-6" id="editUserForm">
                <?= csrf_field() ?>
                <input type="hidden" name="_method" value="PUT">

                <!-- Account Information -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Account Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Username *</label>
                            <input type="text" name="username" value="<?= old('username', $user['username']) ?>" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Enter username">
                            <p class="text-xs text-gray-500 mt-1">Must be unique, 3-50 characters, no spaces</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                            <input type="email" name="email" value="<?= old('email', $user['email']) ?>" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="user@example.com">
                            <p class="text-xs text-gray-500 mt-1">Must be a valid and unique email address</p>
                        </div>
                    </div>
                </div>

                <!-- Password Change -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Password Change</h3>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                        <div class="flex">
                            <i class="fas fa-info-circle text-yellow-600 mt-0.5 mr-2"></i>
                            <div>
                                <p class="text-yellow-800 text-sm font-medium">Leave password fields empty to keep current password</p>
                                <p class="text-yellow-700 text-xs mt-1">Only fill these fields if you want to change the password</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                            <div class="relative">
                                <input type="password" name="password"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent pr-10"
                                       placeholder="Enter new password" id="passwordInput">
                                <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <i class="fas fa-eye text-gray-400" id="passwordToggleIcon"></i>
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Minimum 6 characters</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                            <input type="password" name="confirm_password"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Confirm new password" id="confirmPasswordInput">
                            <p class="text-xs text-red-500 mt-1" id="passwordMismatch" style="display: none;">Passwords do not match</p>
                        </div>
                    </div>

                    <div class="mt-4 flex space-x-2">
                        <button type="button" onclick="generatePassword()" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors text-sm">
                            <i class="fas fa-key mr-2"></i>Generate Password
                        </button>
                        <button type="button" onclick="clearPasswords()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm">
                            <i class="fas fa-times mr-2"></i>Clear Fields
                        </button>
                    </div>
                </div>

                <!-- Personal Information -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Personal Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                            <input type="text" name="full_name" value="<?= old('full_name', $user['full_name']) ?>" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Enter full name">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                            <input type="tel" name="phone" value="<?= old('phone', $user['phone']) ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="+62-21-1234567">
                            <p class="text-xs text-gray-500 mt-1">Optional, used for notifications</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Role *</label>
                            <select name="role" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="roleSelect">
                                <option value="">Select Role</option>
                                <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Administrator</option>
                                <option value="technician" <?= $user['role'] == 'technician' ? 'selected' : '' ?>>Technician</option>
                                <option value="customer" <?= $user['role'] == 'customer' ? 'selected' : '' ?>>Customer</option>
                            </select>
                            <?php if ($user['id'] == session()->get('user_id')): ?>
                                <p class="text-xs text-orange-600 mt-1">
                                    <i class="fas fa-warning mr-1"></i>
                                    You are editing your own account. Be careful when changing your role.
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Role Description -->
                <div class="bg-gray-50 rounded-lg p-4" id="roleDescription">
                    <h4 class="font-medium text-gray-900 mb-2">Role Permissions</h4>
                    <div id="rolePermissions" class="text-sm text-gray-600"></div>
                </div>

                <!-- Account Settings -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Account Settings</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Account Status *</label>
                            <select name="status" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="active" <?= $user['status'] == 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= $user['status'] == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Inactive users cannot login</p>
                            <?php if ($user['id'] == session()->get('user_id')): ?>
                                <p class="text-xs text-red-600 mt-1">
                                    <i class="fas fa-warning mr-1"></i>
                                    Deactivating your own account will log you out!
                                </p>
                            <?php endif; ?>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Notify User of Changes</label>
                            <label class="flex items-center">
                                <input type="checkbox" name="notify_user" value="1"
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-600">Send email notification about account changes</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Account Statistics -->
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Account Statistics</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-blue-600"><?= $user['login_count'] ?? 0 ?></p>
                            <p class="text-sm text-gray-600">Total Logins</p>
                        </div>
                        <div class="text-center">
                            <p class="text-lg font-medium text-gray-800">
<!--                                --><?php //= $user['last_login'] ? date('M d, Y', strtotime($user['last_login'])) : 'Never' ?>
                            </p>
                            <p class="text-sm text-gray-600">Last Login</p>
                        </div>
                        <div class="text-center">
                            <p class="text-lg font-medium text-gray-800"><?= date('M d, Y', strtotime($user['created_at'])) ?></p>
                            <p class="text-sm text-gray-600">Member Since</p>
                        </div>
                        <div class="text-center">
                            <p class="text-lg font-medium text-gray-800"><?= date('M d, Y', strtotime($user['updated_at'])) ?></p>
                            <p class="text-sm text-gray-600">Last Updated</p>
                        </div>
                    </div>
                </div>

                <!-- Password Strength (shown when changing password) -->
                <div class="bg-gray-50 rounded-lg p-4" id="passwordStrengthSection" style="display: none;">
                    <h4 class="font-medium text-gray-800 mb-2">Password Strength</h4>
                    <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                        <div class="bg-red-500 h-2 rounded-full transition-all duration-300" id="passwordStrengthBar" style="width: 0%"></div>
                    </div>
                    <p class="text-sm text-gray-600" id="passwordStrengthText">Enter a password to see strength</p>
                </div>

                <!-- Actions -->
                <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                    <!-- Left side - Additional actions -->
                    <div class="flex space-x-2">
                        <?php if ($user['id'] != session()->get('user_id')): ?>
                            <button type="button" onclick="resetPassword()"
                                    class="px-4 py-2 bg-orange-100 text-orange-700 rounded-lg hover:bg-orange-200 transition-colors text-sm">
                                <i class="fas fa-key mr-2"></i>Reset Password
                            </button>
                            <button type="button" onclick="sendLoginInfo()"
                                    class="px-4 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors text-sm">
                                <i class="fas fa-envelope mr-2"></i>Send Login Info
                            </button>
                        <?php endif; ?>
                    </div>

                    <!-- Right side - Main actions -->
                    <div class="flex space-x-4">
                        <a href="/admin/users"
                           class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </a>
                        <button type="submit"
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                            <i class="fas fa-save mr-2"></i>Update User
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Role descriptions
        const roleDescriptions = {
            admin: [
                'Full system access and control',
                'Manage all users and permissions',
                'Access all orders and customer data',
                'Manage parts and inventory',
                'View reports and analytics',
                'Configure system settings'
            ],
            technician: [
                'View and manage assigned repair orders',
                'Update order status and progress',
                'Access customer information for repairs',
                'Manage parts usage and inventory',
                'Create work reports and documentation'
            ],
            customer: [
                'Submit new repair orders',
                'Track own order status',
                'View repair history',
                'Update personal profile',
                'Receive order notifications'
            ]
        };

        // Update role description
        function updateRoleDescription() {
            const role = document.getElementById('roleSelect').value;
            const permissionsDiv = document.getElementById('rolePermissions');

            if (role && roleDescriptions[role]) {
                permissionsDiv.innerHTML = roleDescriptions[role].map(perm =>
                    `<div class="flex items-center mb-1">
                        <i class="fas fa-check text-green-600 mr-2"></i>
                        <span>${perm}</span>
                    </div>`
                ).join('');
            }
        }

        // Toggle password visibility
        function togglePassword() {
            const passwordInput = document.getElementById('passwordInput');
            const toggleIcon = document.getElementById('passwordToggleIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.className = 'fas fa-eye-slash text-gray-400';
            } else {
                passwordInput.type = 'password';
                toggleIcon.className = 'fas fa-eye text-gray-400';
            }
        }

        // Generate random password
        function generatePassword() {
            const length = 12;
            const charset = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+-=[]{}|;:,.<>?';
            let password = '';

            // Ensure we have at least one of each required character type
            password += 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'[Math.floor(Math.random() * 26)]; // Uppercase
            password += 'abcdefghijklmnopqrstuvwxyz'[Math.floor(Math.random() * 26)]; // Lowercase
            password += '0123456789'[Math.floor(Math.random() * 10)]; // Number
            password += '!@#$%^&*()_+-='[Math.floor(Math.random() * 13)]; // Special

            // Fill the rest randomly
            for (let i = password.length; i < length; i++) {
                password += charset[Math.floor(Math.random() * charset.length)];
            }

            // Shuffle the password
            password = password.split('').sort(() => Math.random() - 0.5).join('');

            document.getElementById('passwordInput').value = password;
            document.getElementById('confirmPasswordInput').value = password;
            showPasswordStrength();
            checkPasswordStrength(password);
        }

        // Clear password fields
        function clearPasswords() {
            document.getElementById('passwordInput').value = '';
            document.getElementById('confirmPasswordInput').value = '';
            hidePasswordStrength();
        }

        // Show password strength section
        function showPasswordStrength() {
            document.getElementById('passwordStrengthSection').style.display = 'block';
        }

        // Hide password strength section
        function hidePasswordStrength() {
            document.getElementById('passwordStrengthSection').style.display = 'none';
        }

        // Check password strength
        function checkPasswordStrength(password) {
            if (!password) {
                hidePasswordStrength();
                return;
            }

            showPasswordStrength();

            let strength = 0;
            if (password.length >= 6) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/\d/.test(password)) strength++;
            if (/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password)) strength++;

            const strengthBar = document.getElementById('passwordStrengthBar');
            const strengthText = document.getElementById('passwordStrengthText');

            switch (strength) {
                case 0:
                case 1:
                    strengthBar.style.width = '25%';
                    strengthBar.className = 'bg-red-500 h-2 rounded-full transition-all duration-300';
                    strengthText.textContent = 'Weak password';
                    strengthText.className = 'text-sm text-red-600';
                    break;
                case 2:
                    strengthBar.style.width = '50%';
                    strengthBar.className = 'bg-yellow-500 h-2 rounded-full transition-all duration-300';
                    strengthText.textContent = 'Fair password';
                    strengthText.className = 'text-sm text-yellow-600';
                    break;
                case 3:
                    strengthBar.style.width = '75%';
                    strengthBar.className = 'bg-blue-500 h-2 rounded-full transition-all duration-300';
                    strengthText.textContent = 'Good password';
                    strengthText.className = 'text-sm text-blue-600';
                    break;
                case 4:
                    strengthBar.style.width = '100%';
                    strengthBar.className = 'bg-green-500 h-2 rounded-full transition-all duration-300';
                    strengthText.textContent = 'Strong password';
                    strengthText.className = 'text-sm text-green-600';
                    break;
            }
        }

        // Check if passwords match
        function checkPasswordMatch() {
            const password = document.getElementById('passwordInput').value;
            const confirmPassword = document.getElementById('confirmPasswordInput').value;
            const mismatchDiv = document.getElementById('passwordMismatch');

            if (confirmPassword && password !== confirmPassword) {
                mismatchDiv.style.display = 'block';
                return false;
            } else {
                mismatchDiv.style.display = 'none';
                return true;
            }
        }

        // Reset password (admin function)
        function resetPassword() {
            if (confirm('Generate a new random password for this user? They will need to be notified of the new password.')) {
                generatePassword();
                alert('New password generated. Make sure to inform the user about their new password.');
            }
        }

        // Send login info
        function sendLoginInfo() {
            if (confirm('Send current login information to the user via email?')) {
                // This would typically make an AJAX call to send email
                alert('Login information would be sent to the user\'s email address.');
            }
        }

        // Event listeners
        document.getElementById('roleSelect').addEventListener('change', updateRoleDescription);

        document.getElementById('passwordInput').addEventListener('input', function() {
            checkPasswordStrength(this.value);
            checkPasswordMatch();
        });

        document.getElementById('confirmPasswordInput').addEventListener('input', checkPasswordMatch);

        // Form validation
        document.getElementById('editUserForm').addEventListener('submit', function(e) {
            const username = document.querySelector('input[name="username"]').value.trim();
            const email = document.querySelector('input[name="email"]').value.trim();
            const password = document.getElementById('passwordInput').value;
            const confirmPassword = document.getElementById('confirmPasswordInput').value;
            const fullName = document.querySelector('input[name="full_name"]').value.trim();
            const role = document.getElementById('roleSelect').value;
            const status = document.querySelector('select[name="status"]').value;

            // Username validation
            if (username.length < 3) {
                alert('Username must be at least 3 characters long.');
                e.preventDefault();
                return;
            }

            if (!/^[a-zA-Z0-9._]+$/.test(username)) {
                alert('Username can only contain letters, numbers, dots, and underscores.');
                e.preventDefault();
                return;
            }

            // Email validation
            if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                alert('Please enter a valid email address.');
                e.preventDefault();
                return;
            }

            // Password validation (only if password is being changed)
            if (password || confirmPassword) {
                if (password.length < 6) {
                    alert('Password must be at least 6 characters long.');
                    e.preventDefault();
                    return;
                }

                if (password !== confirmPassword) {
                    alert('Passwords do not match.');
                    e.preventDefault();
                    return;
                }
            }

            // Full name validation
            if (fullName.length < 2) {
                alert('Full name must be at least 2 characters long.');
                e.preventDefault();
                return;
            }

            // Role validation
            if (!role) {
                alert('Please select a role.');
                e.preventDefault();
                return;
            }

            // Self-deactivation warning
            if ('<?= $user['id'] ?>' === '<?= session()->get('user_id') ?>' && status === 'inactive') {
                if (!confirm('Warning: You are about to deactivate your own account. This will log you out immediately. Are you sure?')) {
                    e.preventDefault();
                    return;
                }
            }

            // Role change warning for self
            if ('<?= $user['id'] ?>' === '<?= session()->get('user_id') ?>' && role !== '<?= $user['role'] ?>') {
                if (!confirm('Warning: You are changing your own role. This may affect your access permissions. Continue?')) {
                    e.preventDefault();
                    return;
                }
            }

            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Updating User...';
            submitBtn.disabled = true;
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl/Cmd + G to generate password
            if ((e.ctrlKey || e.metaKey) && e.key === 'g') {
                e.preventDefault();
                generatePassword();
            }

            // Escape to clear password fields
            if (e.key === 'Escape') {
                clearPasswords();
            }
        });

        // Track changes for unsaved changes warning
        let formChanged = false;
        const form = document.getElementById('editUserForm');
        const inputs = form.querySelectorAll('input, select, textarea');

        inputs.forEach(input => {
            const originalValue = input.value;
            input.addEventListener('change', () => {
                formChanged = (input.value !== originalValue);
            });
        });

        // Warn user about unsaved changes
        window.addEventListener('beforeunload', function(e) {
            if (formChanged) {
                e.preventDefault();
                e.returnValue = '';
                return 'You have unsaved changes. Are you sure you want to leave?';
            }
        });

        // Reset flag when form is submitted
        form.addEventListener('submit', () => {
            formChanged = false;
        });

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            updateRoleDescription();

            // Show current user warning if editing own account
            <?php if ($user['id'] == session()->get('user_id')): ?>
            const warningDiv = document.createElement('div');
            warningDiv.className = 'bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6';
            warningDiv.innerHTML = `
                    <div class="flex">
                        <i class="fas fa-exclamation-triangle text-amber-600 mt-0.5 mr-2"></i>
                        <div>
                            <p class="text-amber-800 text-sm font-medium">You are editing your own account</p>
                            <p class="text-amber-700 text-xs mt-1">Be careful when changing your role or status as it may affect your access to the system.</p>
                        </div>
                    </div>
                `;
            form.parentElement.insertBefore(warningDiv, form);
            <?php endif; ?>
        });

        // Auto-save draft functionality
        function saveDraft() {
            const formData = new FormData(form);
            const draftData = {};

            for (let [key, value] of formData.entries()) {
                if (key !== 'password' && key !== 'confirm_password') { // Don't save passwords
                    draftData[key] = value;
                }
            }

            localStorage.setItem('user_edit_draft_<?= $user['id'] ?>', JSON.stringify({
                data: draftData,
                timestamp: new Date().toISOString()
            }));
        }

        // Auto-save every 30 seconds
        setInterval(saveDraft, 30000);

        // Load draft on page load
        document.addEventListener('DOMContentLoaded', function() {
            const draft = localStorage.getItem('user_edit_draft_<?= $user['id'] ?>');
            if (draft) {
                const draftData = JSON.parse(draft);
                const draftAge = new Date() - new Date(draftData.timestamp);

                // Only restore if draft is less than 1 hour old
                if (draftAge < 60 * 60 * 1000) {
                    if (confirm('Found a saved draft from ' + new Date(draftData.timestamp).toLocaleString() + '. Would you like to restore it?')) {
                        Object.keys(draftData.data).forEach(key => {
                            const field = document.querySelector(`[name="${key}"]`);
                            if (field && field.value !== draftData.data[key]) {
                                field.value = draftData.data[key];
                                formChanged = true;
                            }
                        });
                        updateRoleDescription();
                    } else {
                        localStorage.removeItem('user_edit_draft_<?= $user['id'] ?>');
                    }
                } else {
                    localStorage.removeItem('user_edit_draft_<?= $user['id'] ?>');
                }
            }
        });

        // Clear draft on successful form submission
        form.addEventListener('submit', function() {
            localStorage.removeItem('user_edit_draft_<?= $user['id'] ?>');
        });
    </script>
<?= $this->endSection() ?>
<?= $this->extend('admin/layout/main') ?>

<?= $this->section('content') ?>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Add New User</h1>
                <p class="text-gray-600">Create a new user account</p>
            </div>
            <a href="/admin/users" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left mr-2"></i>Back to Users
            </a>
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

            <form action="/admin/users" method="POST" class="space-y-6" id="createUserForm">
                <?= csrf_field() ?>

                <!-- Account Information -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Account Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Username *</label>
                            <input type="text" name="username" value="<?= old('username') ?>" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Enter username" id="usernameInput">
                            <p class="text-xs text-gray-500 mt-1">Must be unique, 3-50 characters, no spaces</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                            <input type="email" name="email" value="<?= old('email') ?>" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="user@example.com">
                            <p class="text-xs text-gray-500 mt-1">Must be a valid and unique email address</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Password *</label>
                            <div class="relative">
                                <input type="password" name="password" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent pr-10"
                                       placeholder="Enter password" id="passwordInput">
                                <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <i class="fas fa-eye text-gray-400" id="passwordToggleIcon"></i>
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Minimum 6 characters</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password *</label>
                            <input type="password" name="confirm_password" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Confirm password" id="confirmPasswordInput">
                            <p class="text-xs text-red-500 mt-1" id="passwordMismatch" style="display: none;">Passwords do not match</p>
                        </div>
                    </div>
                </div>

                <!-- Personal Information -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Personal Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                            <input type="text" name="full_name" value="<?= old('full_name') ?>" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Enter full name" id="fullNameInput">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                            <input type="tel" name="phone" value="<?= old('phone') ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="+62-21-1234567">
                            <p class="text-xs text-gray-500 mt-1">Optional, used for notifications</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Role *</label>
                            <select name="role" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="roleSelect">
                                <option value="">Select Role</option>
                                <option value="admin" <?= old('role') == 'admin' ? 'selected' : '' ?>>Administrator</option>
                                <option value="technician" <?= old('role') == 'technician' ? 'selected' : '' ?>>Technician</option>
                                <option value="customer" <?= old('role') == 'customer' ? 'selected' : '' ?>>Customer</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Role Description -->
                <div class="bg-gray-50 rounded-lg p-4" id="roleDescription" style="display: none;">
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
                                <option value="active" <?= old('status', 'active') == 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= old('status') == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Inactive users cannot login</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Send Welcome Email</label>
                            <label class="flex items-center">
                                <input type="checkbox" name="send_welcome_email" value="1" checked
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-600">Send login credentials via email</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Username Generation -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="font-medium text-blue-800 mb-2">
                        <i class="fas fa-lightbulb mr-2"></i>Username Suggestions
                    </h4>
                    <p class="text-sm text-blue-700 mb-3">Click to use suggested usernames based on the full name:</p>
                    <div class="flex flex-wrap gap-2" id="usernameSuggestions">
                        <!-- Username suggestions will be populated here -->
                    </div>
                </div>

                <!-- Password Strength -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="font-medium text-gray-800 mb-2">Password Strength</h4>
                    <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                        <div class="bg-red-500 h-2 rounded-full transition-all duration-300" id="passwordStrengthBar" style="width: 0%"></div>
                    </div>
                    <p class="text-sm text-gray-600" id="passwordStrengthText">Enter a password to see strength</p>

                    <div class="mt-3 space-y-1 text-xs">
                        <div class="flex items-center" id="lengthCheck">
                            <i class="fas fa-times text-red-500 mr-2"></i>
                            <span>At least 6 characters</span>
                        </div>
                        <div class="flex items-center" id="uppercaseCheck">
                            <i class="fas fa-times text-red-500 mr-2"></i>
                            <span>At least one uppercase letter</span>
                        </div>
                        <div class="flex items-center" id="numberCheck">
                            <i class="fas fa-times text-red-500 mr-2"></i>
                            <span>At least one number</span>
                        </div>
                        <div class="flex items-center" id="specialCheck">
                            <i class="fas fa-times text-red-500 mr-2"></i>
                            <span>At least one special character</span>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="/admin/users" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                    <button type="button" onclick="generatePassword()" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        <i class="fas fa-key mr-2"></i>Generate Password
                    </button>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                        <i class="fas fa-user-plus mr-2"></i>Create User
                    </button>
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
            const descriptionDiv = document.getElementById('roleDescription');
            const permissionsDiv = document.getElementById('rolePermissions');

            if (role && roleDescriptions[role]) {
                descriptionDiv.style.display = 'block';
                permissionsDiv.innerHTML = roleDescriptions[role].map(perm =>
                    `<div class="flex items-center mb-1">
                        <i class="fas fa-check text-green-600 mr-2"></i>
                        <span>${perm}</span>
                    </div>`
                ).join('');
            } else {
                descriptionDiv.style.display = 'none';
            }
        }

        // Generate username suggestions
        function generateUsernameSuggestions() {
            const fullName = document.getElementById('fullNameInput').value.trim();
            const suggestionsDiv = document.getElementById('usernameSuggestions');

            if (fullName.length < 2) {
                suggestionsDiv.innerHTML = '<p class="text-sm text-gray-500">Enter a full name to see suggestions</p>';
                return;
            }

            const names = fullName.toLowerCase().split(' ');
            const suggestions = [];

            if (names.length >= 2) {
                // First name + last name
                suggestions.push(names[0] + '.' + names[names.length - 1]);
                suggestions.push(names[0] + names[names.length - 1]);
                // First initial + last name
                suggestions.push(names[0].charAt(0) + names[names.length - 1]);
                // Full name without spaces
                suggestions.push(names.join(''));
            } else {
                // Single name
                suggestions.push(names[0]);
                suggestions.push(names[0] + '123');
            }

            // Add random numbers to some suggestions
            const numberedSuggestions = suggestions.slice(0, 2).map(s => s + Math.floor(Math.random() * 99 + 1));
            suggestions.push(...numberedSuggestions);

            suggestionsDiv.innerHTML = suggestions.map(suggestion =>
                `<button type="button" onclick="setUsername('${suggestion}')"
                         class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm hover:bg-blue-200 transition-colors">
                    ${suggestion}
                </button>`
            ).join('');
        }

        // Set username from suggestion
        function setUsername(username) {
            document.getElementById('usernameInput').value = username;
            checkUsernameAvailability(username);
        }

        // Check username availability (mock function)
        function checkUsernameAvailability(username) {
            // In real implementation, this would make an AJAX call
            // For now, just show visual feedback
            const input = document.getElementById('usernameInput');
            input.style.borderColor = '#10B981'; // Green
            setTimeout(() => {
                input.style.borderColor = '';
            }, 2000);
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

        // Check password strength
        function checkPasswordStrength(password) {
            let strength = 0;
            let checks = {
                length: password.length >= 6,
                uppercase: /[A-Z]/.test(password),
                number: /\d/.test(password),
                special: /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password)
            };

            // Update visual checks
            document.getElementById('lengthCheck').innerHTML = checks.length
                ? '<i class="fas fa-check text-green-500 mr-2"></i><span>At least 6 characters</span>'
                : '<i class="fas fa-times text-red-500 mr-2"></i><span>At least 6 characters</span>';

            document.getElementById('uppercaseCheck').innerHTML = checks.uppercase
                ? '<i class="fas fa-check text-green-500 mr-2"></i><span>At least one uppercase letter</span>'
                : '<i class="fas fa-times text-red-500 mr-2"></i><span>At least one uppercase letter</span>';

            document.getElementById('numberCheck').innerHTML = checks.number
                ? '<i class="fas fa-check text-green-500 mr-2"></i><span>At least one number</span>'
                : '<i class="fas fa-times text-red-500 mr-2"></i><span>At least one number</span>';

            document.getElementById('specialCheck').innerHTML = checks.special
                ? '<i class="fas fa-check text-green-500 mr-2"></i><span>At least one special character</span>'
                : '<i class="fas fa-times text-red-500 mr-2"></i><span>At least one special character</span>';

            // Calculate strength
            strength = Object.values(checks).filter(Boolean).length;

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

            return strength;
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
            checkPasswordStrength(password);
            checkPasswordMatch();
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

        // Event listeners
        document.getElementById('roleSelect').addEventListener('change', updateRoleDescription);
        document.getElementById('fullNameInput').addEventListener('input', generateUsernameSuggestions);
        document.getElementById('passwordInput').addEventListener('input', function() {
            checkPasswordStrength(this.value);
            checkPasswordMatch();
        });
        document.getElementById('confirmPasswordInput').addEventListener('input', checkPasswordMatch);

        // Form validation
        document.getElementById('createUserForm').addEventListener('submit', function(e) {
            const username = document.getElementById('usernameInput').value.trim();
            const email = document.querySelector('input[name="email"]').value.trim();
            const password = document.getElementById('passwordInput').value;
            const confirmPassword = document.getElementById('confirmPasswordInput').value;
            const fullName = document.getElementById('fullNameInput').value.trim();
            const role = document.getElementById('roleSelect').value;

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

            // Password validation
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

            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Creating User...';
            submitBtn.disabled = true;
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl/Cmd + G to generate password
            if ((e.ctrlKey || e.metaKey) && e.key === 'g') {
                e.preventDefault();
                generatePassword();
            }
        });

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            updateRoleDescription();
            generateUsernameSuggestions();
        });
    </script>
<?= $this->endSection() ?>
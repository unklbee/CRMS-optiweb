<?= $this->extend('admin/layout/main') ?>

<?= $this->section('content') ?>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Website Settings</h1>
                <p class="text-gray-600">Configure your website preferences and options</p>
            </div>
            <div class="flex space-x-2">
                <a href="/admin/settings/backup" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-download mr-2"></i>Backup
                </a>
                <a href="/admin/settings/new" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Add Setting
                </a>
            </div>
        </div>

        <form action="/admin/settings" method="POST" class="space-y-6">
            <?= csrf_field() ?>

            <!-- General Settings -->
            <?php if (!empty($grouped_settings['general'])): ?>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-cog text-blue-600 mr-2"></i>General Settings
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <?php foreach ($grouped_settings['general'] as $setting): ?>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <?= ucwords(str_replace('_', ' ', $setting['setting_key'])) ?>
                                </label>
                                <?php if ($setting['setting_type'] === 'textarea'): ?>
                                    <textarea name="<?= $setting['setting_key'] ?>" rows="3"
                                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"><?= $setting['setting_value'] ?></textarea>
                                <?php elseif ($setting['setting_type'] === 'boolean'): ?>
                                    <select name="<?= $setting['setting_key'] ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="1" <?= $setting['setting_value'] ? 'selected' : '' ?>>Yes</option>
                                        <option value="0" <?= !$setting['setting_value'] ? 'selected' : '' ?>>No</option>
                                    </select>
                                <?php else: ?>
                                    <input type="<?= $setting['setting_type'] === 'number' ? 'number' : 'text' ?>"
                                           name="<?= $setting['setting_key'] ?>"
                                           value="<?= $setting['setting_value'] ?>"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <?php endif; ?>
                                <?php if ($setting['description']): ?>
                                    <p class="text-xs text-gray-500 mt-1"><?= $setting['description'] ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Contact Settings -->
            <?php if (!empty($grouped_settings['contact'])): ?>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-address-book text-green-600 mr-2"></i>Contact Information
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <?php foreach ($grouped_settings['contact'] as $setting): ?>
                            <div class="<?= $setting['setting_key'] === 'address' ? 'md:col-span-2' : '' ?>">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <?= ucwords(str_replace('_', ' ', $setting['setting_key'])) ?>
                                </label>
                                <?php if ($setting['setting_type'] === 'textarea' || $setting['setting_key'] === 'address'): ?>
                                    <textarea name="<?= $setting['setting_key'] ?>" rows="3"
                                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"><?= $setting['setting_value'] ?></textarea>
                                <?php else: ?>
                                    <input type="<?= $setting['setting_key'] === 'contact_email' ? 'email' : 'text' ?>"
                                           name="<?= $setting['setting_key'] ?>"
                                           value="<?= $setting['setting_value'] ?>"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <?php endif; ?>
                                <?php if ($setting['description']): ?>
                                    <p class="text-xs text-gray-500 mt-1"><?= $setting['description'] ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Business Settings -->
            <?php if (!empty($grouped_settings['business'])): ?>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-business-time text-purple-600 mr-2"></i>Business Settings
                    </h3>
                    <div class="space-y-6">
                        <?php foreach ($grouped_settings['business'] as $setting): ?>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <?= ucwords(str_replace('_', ' ', $setting['setting_key'])) ?>
                                </label>
                                <?php if ($setting['setting_key'] === 'business_hours'): ?>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <?php
                                        $hours = json_decode($setting['setting_value'], true) ?: [];
                                        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                                        ?>
                                        <?php foreach ($days as $day): ?>
                                            <div class="flex items-center space-x-2">
                                                <label class="w-20 text-sm text-gray-600"><?= ucfirst($day) ?></label>
                                                <input type="text" name="business_hours[<?= $day ?>]"
                                                       value="<?= $hours[$day] ?? '09:00-17:00' ?>"
                                                       placeholder="09:00-17:00 or closed"
                                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <input type="<?= $setting['setting_type'] === 'number' ? 'number' : 'text' ?>"
                                           name="<?= $setting['setting_key'] ?>"
                                           value="<?= $setting['setting_value'] ?>"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <?php endif; ?>
                                <?php if ($setting['description']): ?>
                                    <p class="text-xs text-gray-500 mt-1"><?= $setting['description'] ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Other Settings -->
            <?php if (!empty($grouped_settings['other'])): ?>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-sliders-h text-gray-600 mr-2"></i>Other Settings
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <?php foreach ($grouped_settings['other'] as $setting): ?>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <?= ucwords(str_replace('_', ' ', $setting['setting_key'])) ?>
                                </label>
                                <?php if ($setting['setting_type'] === 'textarea'): ?>
                                    <textarea name="<?= $setting['setting_key'] ?>" rows="3"
                                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"><?= $setting['setting_value'] ?></textarea>
                                <?php elseif ($setting['setting_type'] === 'boolean'): ?>
                                    <select name="<?= $setting['setting_key'] ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="1" <?= $setting['setting_value'] ? 'selected' : '' ?>>Yes</option>
                                        <option value="0" <?= !$setting['setting_value'] ? 'selected' : '' ?>>No</option>
                                    </select>
                                <?php else: ?>
                                    <input type="<?= $setting['setting_type'] === 'number' ? 'number' : 'text' ?>"
                                           name="<?= $setting['setting_key'] ?>"
                                           value="<?= $setting['setting_value'] ?>"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <?php endif; ?>
                                <?php if ($setting['description']): ?>
                                    <p class="text-xs text-gray-500 mt-1"><?= $setting['description'] ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex justify-between items-center">
                    <div class="flex space-x-4">
                        <a href="/admin/settings/restore" class="text-blue-600 hover:text-blue-800">
                            <i class="fas fa-upload mr-2"></i>Restore from Backup
                        </a>
                        <a href="/admin/settings/cache" class="text-gray-600 hover:text-gray-800">
                            <i class="fas fa-broom mr-2"></i>Clear Cache
                        </a>
                    </div>

                    <div class="flex space-x-4">
                        <button type="button" onclick="location.reload()" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                            <i class="fas fa-undo mr-2"></i>Reset
                        </button>
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-save mr-2"></i>Save Settings
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        // Auto-save draft every 30 seconds
        let autoSaveTimer;
        function autoSave() {
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(() => {
                // Could implement auto-save functionality here
                console.log('Auto-save triggered');
            }, 30000);
        }

        // Trigger auto-save on input changes
        document.querySelectorAll('input, textarea, select').forEach(element => {
            element.addEventListener('change', autoSave);
        });

        // Business hours validation
        document.querySelectorAll('input[name^="business_hours"]').forEach(input => {
            input.addEventListener('blur', function() {
                const value = this.value.trim();
                if (value && value !== 'closed') {
                    const timePattern = /^(\d{2}:\d{2})-(\d{2}:\d{2})$/;
                    if (!timePattern.test(value)) {
                        this.style.borderColor = '#ef4444';
                        if (!this.nextElementSibling || !this.nextElementSibling.classList.contains('error-message')) {
                            const error = document.createElement('p');
                            error.className = 'error-message text-red-500 text-xs mt-1';
                            error.textContent = 'Format: HH:MM-HH:MM or "closed"';
                            this.parentNode.appendChild(error);
                        }
                    } else {
                        this.style.borderColor = '';
                        const error = this.parentNode.querySelector('.error-message');
                        if (error) error.remove();
                    }
                }
            });
        });
    </script>
<?= $this->endSection() ?>
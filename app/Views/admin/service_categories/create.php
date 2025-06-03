<?= $this->extend('admin/layout/main') ?>

<?= $this->section('content') ?>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Create New Service Category</h1>
                <p class="text-gray-600">Add a new category to organize your repair services</p>
            </div>
            <a href="/admin/service-categories" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left mr-2"></i>Back to Categories
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

            <form action="/admin/service-categories" method="POST" class="space-y-6" id="categoryForm">
                <?= csrf_field() ?>

                <!-- Basic Information -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Category Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Category Name *</label>
                            <input type="text" name="name" value="<?= old('name') ?>" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="e.g., Hardware Repair, Software Installation">
                            <p class="text-xs text-gray-500 mt-1">This will be displayed to customers</p>
                        </div>

                        <div class="md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                            <select name="status" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="active" <?= old('status', 'active') == 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= old('status') == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Only active categories are visible to customers</p>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="4"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Describe what types of services belong to this category..."><?= old('description') ?></textarea>
                    <p class="text-xs text-gray-500 mt-1">Help customers understand what services are included</p>
                </div>

                <!-- Icon Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category Icon</label>
                    <div class="space-y-4">
                        <!-- Current Icon Display -->
                        <div class="flex items-center space-x-4">
                            <div class="w-16 h-16 bg-blue-100 rounded-xl flex items-center justify-center" id="iconPreview">
                                <i class="fas fa-folder text-blue-600 text-2xl" id="currentIcon"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Selected Icon</p>
                                <p class="text-sm text-gray-600" id="iconName">fas fa-folder</p>
                            </div>
                        </div>

                        <!-- Icon Input -->
                        <div>
                            <input type="text" name="icon" value="<?= old('icon', 'fas fa-folder') ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="e.g., fas fa-wrench, fas fa-laptop, fas fa-shield-alt"
                                   id="iconInput">
                            <p class="text-xs text-gray-500 mt-1">Use FontAwesome icon classes (e.g., fas fa-wrench)</p>
                        </div>

                        <!-- Popular Icons -->
                        <div>
                            <p class="text-sm font-medium text-gray-700 mb-2">Popular Icons</p>
                            <div class="grid grid-cols-6 md:grid-cols-12 gap-2" id="iconGrid">
                                <?php
                                $popularIcons = [
                                    'fas fa-wrench', 'fas fa-laptop', 'fas fa-mobile-alt', 'fas fa-desktop',
                                    'fas fa-shield-alt', 'fas fa-download', 'fas fa-database', 'fas fa-tachometer-alt',
                                    'fas fa-network-wired', 'fas fa-print', 'fas fa-gamepad', 'fas fa-tv',
                                    'fas fa-tablet-alt', 'fas fa-keyboard', 'fas fa-mouse', 'fas fa-memory',
                                    'fas fa-hdd', 'fas fa-microchip', 'fas fa-wifi', 'fas fa-cog',
                                    'fas fa-tools', 'fas fa-screwdriver', 'fas fa-hammer', 'fas fa-bolt'
                                ];
                                foreach ($popularIcons as $icon): ?>
                                    <button type="button" onclick="selectIcon('<?= $icon ?>')"
                                            class="w-10 h-10 bg-gray-100 hover:bg-blue-100 rounded-lg flex items-center justify-center transition-colors icon-btn"
                                            data-icon="<?= $icon ?>"
                                            title="<?= $icon ?>">
                                        <i class="<?= $icon ?> text-gray-600 hover:text-blue-600"></i>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Quick Category Templates -->
                        <div>
                            <p class="text-sm font-medium text-gray-700 mb-2">Quick Templates</p>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                <?php
                                $templates = [
                                    ['name' => 'Hardware Repair', 'icon' => 'fas fa-wrench', 'desc' => 'Physical device repairs and replacements'],
                                    ['name' => 'Software Installation', 'icon' => 'fas fa-download', 'desc' => 'OS and application installations'],
                                    ['name' => 'Data Recovery', 'icon' => 'fas fa-database', 'desc' => 'Recover lost or corrupted data'],
                                    ['name' => 'Virus Removal', 'icon' => 'fas fa-shield-alt', 'desc' => 'Malware and virus cleaning'],
                                    ['name' => 'Performance Optimization', 'icon' => 'fas fa-tachometer-alt', 'desc' => 'Speed up and optimize systems'],
                                    ['name' => 'Network Setup', 'icon' => 'fas fa-network-wired', 'desc' => 'Network configuration and troubleshooting']
                                ];
                                foreach ($templates as $template): ?>
                                    <button type="button" onclick="useTemplate('<?= $template['name'] ?>', '<?= $template['icon'] ?>', '<?= $template['desc'] ?>')"
                                            class="text-left p-3 border border-gray-200 rounded-lg hover:border-blue-300 hover:bg-blue-50 transition-colors template-btn">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                                <i class="<?= $template['icon'] ?> text-blue-600 text-sm"></i>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900 text-sm"><?= $template['name'] ?></p>
                                                <p class="text-xs text-gray-500"><?= $template['desc'] ?></p>
                                            </div>
                                        </div>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Preview Section -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Live Preview</h3>
                    <div class="bg-white rounded-lg p-4 border border-gray-200">
                        <div class="flex items-center space-x-3 mb-3">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center" id="previewIconContainer">
                                <i class="fas fa-folder text-blue-600 text-xl" id="previewIcon"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900" id="previewName">Category Name</h4>
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800" id="previewStatus">Active</span>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600" id="previewDescription">Category description will appear here...</p>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="/admin/service-categories"
                       class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                    <button type="button" onclick="saveDraft()"
                            class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>Save Draft
                    </button>
                    <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                        <i class="fas fa-plus mr-2"></i>Create Category
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Icon selection functionality
        function selectIcon(iconClass) {
            document.getElementById('iconInput').value = iconClass;
            updateIconPreview(iconClass);
            updateLivePreview();

            // Update active state
            document.querySelectorAll('.icon-btn').forEach(btn => {
                btn.classList.remove('bg-blue-500', 'text-white');
                btn.classList.add('bg-gray-100');
                btn.querySelector('i').classList.remove('text-white');
                btn.querySelector('i').classList.add('text-gray-600');
            });

            const selectedBtn = document.querySelector(`[data-icon="${iconClass}"]`);
            selectedBtn.classList.add('bg-blue-500', 'text-white');
            selectedBtn.classList.remove('bg-gray-100');
            selectedBtn.querySelector('i').classList.add('text-white');
            selectedBtn.querySelector('i').classList.remove('text-gray-600');
        }

        function updateIconPreview(iconClass) {
            const preview = document.getElementById('currentIcon');
            const iconName = document.getElementById('iconName');
            const previewIcon = document.getElementById('previewIcon');

            preview.className = iconClass + ' text-blue-600 text-2xl';
            previewIcon.className = iconClass + ' text-blue-600 text-xl';
            iconName.textContent = iconClass;
        }

        // Template functionality
        function useTemplate(name, icon, description) {
            document.querySelector('input[name="name"]').value = name;
            document.querySelector('input[name="icon"]').value = icon;
            document.querySelector('textarea[name="description"]').value = description;

            updateIconPreview(icon);
            selectIcon(icon);
            updateLivePreview();

            // Highlight used template
            document.querySelectorAll('.template-btn').forEach(btn => {
                btn.classList.remove('border-blue-500', 'bg-blue-50');
                btn.classList.add('border-gray-200');
            });

            event.target.closest('.template-btn').classList.add('border-blue-500', 'bg-blue-50');
            event.target.closest('.template-btn').classList.remove('border-gray-200');
        }

        // Live preview functionality
        function updateLivePreview() {
            const name = document.querySelector('input[name="name"]').value || 'Category Name';
            const description = document.querySelector('textarea[name="description"]').value || 'Category description will appear here...';
            const status = document.querySelector('select[name="status"]').value;

            document.getElementById('previewName').textContent = name;
            document.getElementById('previewDescription').textContent = description;
            document.getElementById('previewStatus').textContent = status === 'active' ? 'Active' : 'Inactive';
            document.getElementById('previewStatus').className = `px-2 py-1 text-xs rounded-full ${
                status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'
            }`;
        }

        // Real-time updates
        document.getElementById('iconInput').addEventListener('input', function(e) {
            const iconClass = e.target.value.trim();
            if (iconClass) {
                updateIconPreview(iconClass);
            }
        });

        ['input', 'change', 'keyup'].forEach(event => {
            document.querySelector('input[name="name"]').addEventListener(event, updateLivePreview);
            document.querySelector('textarea[name="description"]').addEventListener(event, updateLivePreview);
            document.querySelector('select[name="status"]').addEventListener(event, updateLivePreview);
        });

        // Form validation
        document.getElementById('categoryForm').addEventListener('submit', function(e) {
            const name = document.querySelector('input[name="name"]').value.trim();

            if (name.length < 2) {
                alert('Category name must be at least 2 characters long.');
                e.preventDefault();
                return;
            }

            // Show loading state
            const submitBtn = e.target.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Creating...';
            submitBtn.disabled = true;

            // Re-enable if form validation fails
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 3000);
        });

        // Draft functionality
        function saveDraft() {
            const formData = {
                name: document.querySelector('input[name="name"]').value,
                description: document.querySelector('textarea[name="description"]').value,
                status: document.querySelector('select[name="status"]').value,
                icon: document.querySelector('input[name="icon"]').value,
                timestamp: new Date().toISOString()
            };

            localStorage.setItem('category_create_draft', JSON.stringify(formData));

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
            const draft = localStorage.getItem('category_create_draft');
            if (draft) {
                const draftData = JSON.parse(draft);
                const draftAge = new Date() - new Date(draftData.timestamp);

                // Only restore if draft is less than 24 hours old
                if (draftAge < 24 * 60 * 60 * 1000) {
                    if (confirm('Found a saved draft. Would you like to restore it?')) {
                        document.querySelector('input[name="name"]').value = draftData.name || '';
                        document.querySelector('textarea[name="description"]').value = draftData.description || '';
                        document.querySelector('select[name="status"]').value = draftData.status || 'active';
                        document.querySelector('input[name="icon"]').value = draftData.icon || 'fas fa-folder';

                        if (draftData.icon) {
                            updateIconPreview(draftData.icon);
                            selectIcon(draftData.icon);
                        }

                        updateLivePreview();
                    } else {
                        localStorage.removeItem('category_create_draft');
                    }
                } else {
                    // Remove old draft
                    localStorage.removeItem('category_create_draft');
                }
            }

            // Initialize default icon selection
            selectIcon('fas fa-folder');
            updateLivePreview();
        });

        // Clear draft on successful form submission
        document.getElementById('categoryForm').addEventListener('submit', function() {
            localStorage.removeItem('category_create_draft');
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
                document.getElementById('categoryForm').submit();
            }
        });

        // Auto-save functionality
        let autoSaveTimer;
        function autoSave() {
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(() => {
                const name = document.querySelector('input[name="name"]').value.trim();
                if (name.length > 0) {
                    saveDraft();
                }
            }, 5000); // Auto-save after 5 seconds of inactivity
        }

        // Set up auto-save listeners
        ['input', 'change'].forEach(event => {
            document.querySelectorAll('input, textarea, select').forEach(element => {
                element.addEventListener(event, autoSave);
            });
        });

        // Form change detection
        let formChanged = false;
        document.querySelectorAll('input, textarea, select').forEach(element => {
            element.addEventListener('change', function() {
                formChanged = true;
            });
        });

        // Warn before leaving if form has changes
        window.addEventListener('beforeunload', function(e) {
            if (formChanged && document.querySelector('input[name="name"]').value.trim()) {
                e.preventDefault();
                e.returnValue = '';
                return 'You have unsaved changes. Are you sure you want to leave?';
            }
        });

        // Don't warn when submitting form
        document.getElementById('categoryForm').addEventListener('submit', function() {
            formChanged = false;
        });
    </script>
<?= $this->endSection() ?>
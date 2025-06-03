<?= $this->extend('admin/layout/main') ?>

<?= $this->section('content') ?>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit Service Category</h1>
                <p class="text-gray-600"><?= $category['name'] ?></p>
            </div>
            <div class="flex space-x-2">
                <a href="/admin/service-categories/<?= $category['id'] ?>" class="text-gray-600 hover:text-gray-900 px-4 py-2">
                    <i class="fas fa-eye mr-2"></i>View Details
                </a>
                <a href="/admin/service-categories" class="text-gray-600 hover:text-gray-900 px-4 py-2">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Categories
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

            <form action="/admin/service-categories/<?= $category['id'] ?>" method="POST" class="space-y-6" id="categoryForm">
                <?= csrf_field() ?>
                <input type="hidden" name="_method" value="PUT">

                <!-- Basic Information -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Category Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Category Name *</label>
                            <input type="text" name="name" value="<?= old('name', $category['name']) ?>" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="e.g., Hardware Repair, Software Installation">
                            <p class="text-xs text-gray-500 mt-1">This will be displayed to customers</p>
                        </div>

                        <div class="md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                            <select name="status" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="active" <?= $category['status'] == 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= $category['status'] == 'inactive' ? 'selected' : '' ?>>Inactive</option>
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
                              placeholder="Describe what types of services belong to this category..."><?= old('description', $category['description']) ?></textarea>
                    <p class="text-xs text-gray-500 mt-1">Help customers understand what services are included</p>
                </div>

                <!-- Icon Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category Icon</label>
                    <div class="space-y-4">
                        <!-- Current Icon Display -->
                        <div class="flex items-center space-x-4">
                            <div class="w-16 h-16 bg-blue-100 rounded-xl flex items-center justify-center" id="iconPreview">
                                <?php if ($category['icon']): ?>
                                    <i class="<?= $category['icon'] ?> text-blue-600 text-2xl" id="currentIcon"></i>
                                <?php else: ?>
                                    <i class="fas fa-folder text-blue-600 text-2xl" id="currentIcon"></i>
                                <?php endif; ?>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Current Icon</p>
                                <p class="text-sm text-gray-600" id="iconName"><?= $category['icon'] ?: 'fas fa-folder' ?></p>
                            </div>
                        </div>

                        <!-- Icon Input -->
                        <div>
                            <input type="text" name="icon" value="<?= old('icon', $category['icon']) ?>"
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
                                            data-icon="<?= $icon ?>">
                                        <i class="<?= $icon ?> text-gray-600 hover:text-blue-600"></i>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Category Statistics (Read-only) -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Category Statistics</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-blue-600"><?= $category['services_count'] ?? 0 ?></p>
                            <p class="text-sm text-gray-600">Total Services</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-green-600"><?= $category['active_services'] ?? 0 ?></p>
                            <p class="text-sm text-gray-600">Active Services</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-purple-600"><?= $category['total_orders'] ?? 0 ?></p>
                            <p class="text-sm text-gray-600">Total Orders</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-orange-600"><?= format_currency($category['total_revenue'] ?? 0) ?></p>
                            <p class="text-sm text-gray-600">Total Revenue</p>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                    <!-- Left side - Additional actions -->
                    <div class="flex space-x-2">
                        <button type="button" onclick="previewCategory()"
                                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                            <i class="fas fa-eye mr-2"></i>Preview
                        </button>
                        <button type="button" onclick="resetForm()"
                                class="px-4 py-2 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 transition-colors">
                            <i class="fas fa-undo mr-2"></i>Reset
                        </button>
                    </div>

                    <!-- Right side - Main actions -->
                    <div class="flex space-x-4">
                        <a href="/admin/service-categories/<?= $category['id'] ?>"
                           class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </a>
                        <button type="submit"
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                            <i class="fas fa-save mr-2"></i>Update Category
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Preview Modal -->
    <div id="previewModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4 text-center">Category Preview</h3>
                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <div class="flex items-center space-x-3 mb-3">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center" id="previewIcon">
                            <i class="fas fa-folder text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900" id="previewName">Category Name</h4>
                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800" id="previewStatus">Active</span>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600" id="previewDescription">Category description will appear here...</p>
                </div>
                <div class="flex justify-center">
                    <button onclick="closePreview()" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Icon selection functionality
        function selectIcon(iconClass) {
            document.getElementById('iconInput').value = iconClass;
            updateIconPreview(iconClass);

            // Update active state
            document.querySelectorAll('.icon-btn').forEach(btn => {
                btn.classList.remove('bg-blue-500', 'text-white');
                btn.classList.add('bg-gray-100');
            });

            document.querySelector(`[data-icon="${iconClass}"]`).classList.add('bg-blue-500', 'text-white');
            document.querySelector(`[data-icon="${iconClass}"]`).classList.remove('bg-gray-100');
        }

        function updateIconPreview(iconClass) {
            const preview = document.getElementById('currentIcon');
            const iconName = document.getElementById('iconName');

            preview.className = iconClass + ' text-blue-600 text-2xl';
            iconName.textContent = iconClass;
        }

        // Real-time icon preview
        document.getElementById('iconInput').addEventListener('input', function(e) {
            const iconClass = e.target.value.trim();
            if (iconClass) {
                updateIconPreview(iconClass);
            }
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
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Updating...';
            submitBtn.disabled = true;

            // Re-enable if form validation fails
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 3000);
        });

        // Preview functionality
        function previewCategory() {
            const name = document.querySelector('input[name="name"]').value || 'Category Name';
            const description = document.querySelector('textarea[name="description"]').value || 'No description provided';
            const status = document.querySelector('select[name="status"]').value;
            const icon = document.querySelector('input[name="icon"]').value || 'fas fa-folder';

            document.getElementById('previewName').textContent = name;
            document.getElementById('previewDescription').textContent = description;
            document.getElementById('previewStatus').textContent = status === 'active' ? 'Active' : 'Inactive';
            document.getElementById('previewStatus').className = `px-2 py-1 text-xs rounded-full ${
                status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'
            }`;

            const previewIconEl = document.getElementById('previewIcon').querySelector('i');
            previewIconEl.className = icon + ' text-blue-600 text-xl';

            document.getElementById('previewModal').classList.remove('hidden');
        }

        function closePreview() {
            document.getElementById('previewModal').classList.add('hidden');
        }

        // Reset form functionality
        function resetForm() {
            if (confirm('Are you sure you want to reset all changes? This will restore the original values.')) {
                // Reset to original values
                document.querySelector('input[name="name"]').value = '<?= addslashes($category['name']) ?>';
                document.querySelector('textarea[name="description"]').value = '<?= addslashes($category['description']) ?>';
                document.querySelector('select[name="status"]').value = '<?= $category['status'] ?>';
                document.querySelector('input[name="icon"]').value = '<?= $category['icon'] ?>';

                // Reset icon preview
                updateIconPreview('<?= $category['icon'] ?: 'fas fa-folder' ?>');

                // Reset icon selection highlighting
                document.querySelectorAll('.icon-btn').forEach(btn => {
                    btn.classList.remove('bg-blue-500', 'text-white');
                    btn.classList.add('bg-gray-100');
                });

                const originalIconBtn = document.querySelector(`[data-icon="<?= $category['icon'] ?>"]`);
                if (originalIconBtn) {
                    originalIconBtn.classList.add('bg-blue-500', 'text-white');
                    originalIconBtn.classList.remove('bg-gray-100');
                }
            }
        }

        // Auto-save draft functionality (optional)
        let autoSaveTimer;
        function autoSaveDraft() {
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(() => {
                const formData = {
                    name: document.querySelector('input[name="name"]').value,
                    description: document.querySelector('textarea[name="description"]').value,
                    status: document.querySelector('select[name="status"]').value,
                    icon: document.querySelector('input[name="icon"]').value
                };

                // Save to localStorage as draft
                localStorage.setItem('category_edit_draft_<?= $category['id'] ?>', JSON.stringify(formData));

                // Show saved indicator
                showSavedIndicator();
            }, 2000);
        }

        function showSavedIndicator() {
            const indicator = document.createElement('div');
            indicator.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
            indicator.innerHTML = '<i class="fas fa-check mr-2"></i>Draft saved';
            document.body.appendChild(indicator);

            setTimeout(() => {
                indicator.remove();
            }, 3000);
        }

        // Load draft on page load
        document.addEventListener('DOMContentLoaded', function() {
            const draft = localStorage.getItem('category_edit_draft_<?= $category['id'] ?>');
            if (draft) {
                const draftData = JSON.parse(draft);

                // Ask user if they want to restore draft
                if (confirm('Found unsaved changes. Would you like to restore them?')) {
                    document.querySelector('input[name="name"]').value = draftData.name || '';
                    document.querySelector('textarea[name="description"]').value = draftData.description || '';
                    document.querySelector('select[name="status"]').value = draftData.status || 'active';
                    document.querySelector('input[name="icon"]').value = draftData.icon || '';

                    if (draftData.icon) {
                        updateIconPreview(draftData.icon);
                    }
                } else {
                    // Clear draft if user doesn't want to restore
                    localStorage.removeItem('category_edit_draft_<?= $category['id'] ?>');
                }
            }

            // Set up auto-save listeners
            ['input', 'change'].forEach(event => {
                document.querySelectorAll('input, textarea, select').forEach(element => {
                    element.addEventListener(event, autoSaveDraft);
                });
            });
        });

        // Clear draft on successful form submission
        document.getElementById('categoryForm').addEventListener('submit', function() {
            localStorage.removeItem('category_edit_draft_<?= $category['id'] ?>');
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl/Cmd + S to save
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
                document.getElementById('categoryForm').submit();
            }

            // Ctrl/Cmd + P to preview
            if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
                e.preventDefault();
                previewCategory();
            }

            // Escape to close preview
            if (e.key === 'Escape') {
                closePreview();
            }

            // Ctrl/Cmd + R to reset (with confirmation)
            if ((e.ctrlKey || e.metaKey) && e.key === 'r') {
                e.preventDefault();
                resetForm();
            }
        });

        // Initialize icon selection highlighting
        document.addEventListener('DOMContentLoaded', function() {
            const currentIcon = '<?= $category['icon'] ?>';
            if (currentIcon) {
                const iconBtn = document.querySelector(`[data-icon="${currentIcon}"]`);
                if (iconBtn) {
                    iconBtn.classList.add('bg-blue-500', 'text-white');
                    iconBtn.classList.remove('bg-gray-100');
                }
            }
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
            if (formChanged) {
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
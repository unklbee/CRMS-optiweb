<?= $this->extend('admin/layout/main') ?>

<?= $this->section('content') ?>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Service Categories</h1>
                <p class="text-gray-600">Manage repair service categories and organization</p>
            </div>
            <div class="flex space-x-2">
                <a href="/admin/services" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-cog mr-2"></i>View Services
                </a>
                <a href="/admin/service-categories/new" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Add Category
                </a>
            </div>
        </div>

        <!-- Search -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <form method="GET" class="flex gap-4">
                <div class="flex-1">
                    <input type="text" name="search" placeholder="Search categories by name or description"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           value="<?= $search ?>">
                </div>
                <button type="submit" class="bg-gray-800 text-white px-6 py-2 rounded-lg hover:bg-gray-900 transition-colors">
                    <i class="fas fa-search mr-2"></i>Search
                </button>
            </form>
        </div>

        <!-- Categories Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if (!empty($categories)): ?>
                <?php foreach ($categories as $category): ?>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-3">
                                    <?php if ($category['icon']): ?>
                                        <div class="bg-blue-100 w-12 h-12 rounded-lg flex items-center justify-center">
                                            <i class="<?= $category['icon'] ?> text-blue-600 text-xl"></i>
                                        </div>
                                    <?php else: ?>
                                        <div class="bg-gray-100 w-12 h-12 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-folder text-gray-600 text-xl"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-800"><?= $category['name'] ?></h3>
                                    </div>
                                </div>
                                <span class="px-2 py-1 text-xs rounded-full <?= $category['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' ?>">
                                    <?= ucfirst($category['status']) ?>
                                </span>
                            </div>

                            <?php if ($category['description']): ?>
                                <p class="text-gray-600 mb-4 text-sm"><?= truncate_text($category['description'], 100) ?></p>
                            <?php endif; ?>

                            <div class="flex justify-between items-center mb-4">
                                <div>
                                    <span class="text-sm text-gray-500">Services Count</span>
                                    <p class="text-2xl font-bold text-blue-600"><?= $category['services_count'] ?? 0 ?></p>
                                </div>
                                <div class="text-right">
                                    <span class="text-sm text-gray-500">Created</span>
                                    <p class="text-sm font-medium"><?= date('M d, Y', strtotime($category['created_at'])) ?></p>
                                </div>
                            </div>

                            <div class="flex space-x-2">
                                <a href="/admin/service-categories/<?= $category['id'] ?>"
                                   class="flex-1 bg-gray-50 text-gray-700 py-2 px-3 rounded-lg hover:bg-gray-100 transition-colors text-center text-sm">
                                    <i class="fas fa-eye mr-1"></i>View
                                </a>
                                <a href="/admin/service-categories/<?= $category['id'] ?>/edit"
                                   class="flex-1 bg-blue-50 text-blue-700 py-2 px-3 rounded-lg hover:bg-blue-100 transition-colors text-center text-sm">
                                    <i class="fas fa-edit mr-1"></i>Edit
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-full text-center py-12">
                    <i class="fas fa-folder-plus text-4xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">No Categories Found</h3>
                    <p class="text-gray-600 mb-6">Get started by creating your first service category to organize your repair services.</p>
                    <a href="/admin/service-categories/new" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Create First Category
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Quick Stats -->
        <?php if (!empty($categories)): ?>
            <div class="bg-gradient-to-r from-blue-50 to-indigo-100 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Category Overview</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-blue-600"><?= count($categories) ?></p>
                        <p class="text-sm text-gray-600">Total Categories</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-green-600"><?= count(array_filter($categories, function($cat) { return $cat['status'] === 'active'; })) ?></p>
                        <p class="text-sm text-gray-600">Active Categories</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-purple-600"><?= array_sum(array_column($categories, 'services_count')) ?></p>
                        <p class="text-sm text-gray-600">Total Services</p>
                    </div>
                    <div class="text-center">
                        <?php
                        $avgServices = count($categories) > 0 ? round(array_sum(array_column($categories, 'services_count')) / count($categories), 1) : 0;
                        ?>
                        <p class="text-2xl font-bold text-orange-600"><?= $avgServices ?></p>
                        <p class="text-sm text-gray-600">Avg Services/Category</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Quick Actions Modal (Optional Enhancement) -->
    <div id="quickActionsModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    <button onclick="bulkActivate()" class="w-full bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-check mr-2"></i>Activate Selected
                    </button>
                    <button onclick="bulkDeactivate()" class="w-full bg-yellow-600 text-white py-2 px-4 rounded-lg hover:bg-yellow-700 transition-colors">
                        <i class="fas fa-pause mr-2"></i>Deactivate Selected
                    </button>
                    <button onclick="exportCategories()" class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-download mr-2"></i>Export Categories
                    </button>
                </div>
                <div class="mt-4">
                    <button onclick="closeQuickActions()" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Search functionality
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.querySelector('input[name="search"]');
            if (searchInput) {
                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        this.closest('form').submit();
                    }
                });
            }
        });

        // Quick actions functions
        function showQuickActions() {
            document.getElementById('quickActionsModal').classList.remove('hidden');
        }

        function closeQuickActions() {
            document.getElementById('quickActionsModal').classList.add('hidden');
        }

        function bulkActivate() {
            // Implementation for bulk activation
            alert('Bulk activation feature coming soon!');
            closeQuickActions();
        }

        function bulkDeactivate() {
            // Implementation for bulk deactivation
            alert('Bulk deactivation feature coming soon!');
            closeQuickActions();
        }

        function exportCategories() {
            // Implementation for exporting categories
            window.location.href = '/admin/service-categories/export';
            closeQuickActions();
        }

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl/Cmd + N for new category
            if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
                e.preventDefault();
                window.location.href = '/admin/service-categories/new';
            }

            // Ctrl/Cmd + F for search
            if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
                e.preventDefault();
                document.querySelector('input[name="search"]').focus();
            }

            // Escape to close modals
            if (e.key === 'Escape') {
                closeQuickActions();
            }
        });

        // Auto-hide success/error messages
        setTimeout(function() {
            const alerts = document.querySelectorAll('[class*="bg-green-100"], [class*="bg-red-100"]');
            alerts.forEach(alert => {
                if (alert.closest('.space-y-6')) return; // Don't hide flash messages in main content
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>
<?= $this->endSection() ?>
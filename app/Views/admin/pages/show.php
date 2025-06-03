<?= $this->extend('admin/layout/main') ?>

<?= $this->section('content') ?>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Page Details</h1>
                <p class="text-gray-600"><?= $page['title'] ?></p>
            </div>
            <div class="flex space-x-2">
                <a href="/<?= $page['slug'] ?>" target="_blank" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-external-link-alt mr-2"></i>View Live
                </a>
                <a href="/admin/pages/<?= $page['id'] ?>/edit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-edit mr-2"></i>Edit Page
                </a>
                <a href="/admin/pages" class="text-gray-600 hover:text-gray-900 px-4 py-2">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Pages
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Page Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Page Information</h3>

                    <div class="space-y-4">
                        <div>
                            <h4 class="text-xl font-semibold text-gray-900"><?= $page['title'] ?></h4>
                            <div class="flex items-center space-x-4 mt-2">
                                <span class="inline-flex px-3 py-1 rounded-full text-sm font-medium
                                    <?= $page['status'] === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' ?>">
                                    <?= ucfirst($page['status']) ?>
                                </span>
                                <span class="text-gray-500">•</span>
                                <span class="text-gray-600">Template: <?= ucfirst($page['template']) ?></span>
                            </div>
                        </div>

                        <div>
                            <h4 class="font-medium text-gray-900 mb-2">URL Slug</h4>
                            <div class="flex items-center space-x-2">
                                <code class="bg-gray-100 px-3 py-1 rounded text-sm">/<?= $page['slug'] ?></code>
                                <a href="/<?= $page['slug'] ?>" target="_blank" class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                                <button onclick="copyToClipboard('<?= base_url($page['slug']) ?>')" class="text-gray-600 hover:text-gray-800">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>

                        <?php if ($page['meta_title']): ?>
                            <div>
                                <h4 class="font-medium text-gray-900 mb-2">Meta Title</h4>
                                <p class="text-gray-600"><?= $page['meta_title'] ?></p>
                            </div>
                        <?php endif; ?>

                        <?php if ($page['meta_description']): ?>
                            <div>
                                <h4 class="font-medium text-gray-900 mb-2">Meta Description</h4>
                                <p class="text-gray-600"><?= $page['meta_description'] ?></p>
                            </div>
                        <?php endif; ?>

                        <?php if ($page['featured_image']): ?>
                            <div>
                                <h4 class="font-medium text-gray-900 mb-2">Featured Image</h4>
                                <div class="flex items-center space-x-4">
                                    <img src="<?= $page['featured_image'] ?>" alt="Featured Image" class="w-20 h-20 object-cover rounded-lg">
                                    <div>
                                        <p class="text-sm text-gray-600"><?= $page['featured_image'] ?></p>
                                        <a href="<?= $page['featured_image'] ?>" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">
                                            <i class="fas fa-external-link-alt mr-1"></i>View Full Size
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-600">Created:</span>
                                <span class="font-medium"><?= date('M d, Y H:i', strtotime($page['created_at'])) ?></span>
                            </div>
                            <div>
                                <span class="text-gray-600">Updated:</span>
                                <span class="font-medium"><?= date('M d, Y H:i', strtotime($page['updated_at'])) ?></span>
                            </div>
                            <?php if ($page['created_by_name']): ?>
                                <div>
                                    <span class="text-gray-600">Author:</span>
                                    <span class="font-medium"><?= $page['created_by_name'] ?></span>
                                </div>
                            <?php endif; ?>
                            <div>
                                <span class="text-gray-600">Content Length:</span>
                                <span class="font-medium"><?= number_format(strlen($page['content'])) ?> characters</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Page Content -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-800">Page Content</h3>
                            <div class="flex space-x-2">
                                <button onclick="toggleContentView()" class="text-sm bg-gray-100 text-gray-700 px-3 py-1 rounded-lg hover:bg-gray-200 transition-colors" id="contentToggle">
                                    <i class="fas fa-eye mr-1"></i>Show Preview
                                </button>
                                <button onclick="toggleFullscreen()" class="text-sm bg-gray-100 text-gray-700 px-3 py-1 rounded-lg hover:bg-gray-200 transition-colors">
                                    <i class="fas fa-expand mr-1"></i>Fullscreen
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <!-- Raw Content View -->
                        <div id="rawContent" class="space-y-4">
                            <?php if ($page['content']): ?>
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 max-h-96 overflow-y-auto">
                                    <pre class="text-sm text-gray-800 whitespace-pre-wrap font-mono"><?= htmlspecialchars($page['content']) ?></pre>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-8 text-gray-500">
                                    <i class="fas fa-file-alt text-4xl text-gray-300 mb-4"></i>
                                    <p>No content available</p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Preview Content View -->
                        <div id="previewContent" class="space-y-4" style="display: none;">
                            <?php if ($page['content']): ?>
                                <div class="bg-white border border-gray-200 rounded-lg p-6 max-h-96 overflow-y-auto prose prose-sm max-w-none">
                                    <?= $page['content'] ?>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-8 text-gray-500">
                                    <i class="fas fa-file-alt text-4xl text-gray-300 mb-4"></i>
                                    <p>No content to preview</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- SEO Analysis -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">SEO Analysis</h3>

                    <div class="space-y-4">
                        <!-- Title Length -->
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-heading text-gray-600 mr-3"></i>
                                <span class="text-sm font-medium">Title Length</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <?php
                                $titleLength = strlen($page['meta_title'] ?: $page['title']);
                                $titleStatus = $titleLength >= 50 && $titleLength <= 60 ? 'good' : ($titleLength < 50 ? 'short' : 'long');
                                ?>
                                <span class="text-sm text-gray-600"><?= $titleLength ?> characters</span>
                                <i class="fas fa-circle text-<?= $titleStatus === 'good' ? 'green' : ($titleStatus === 'short' ? 'yellow' : 'red') ?>-500"></i>
                            </div>
                        </div>

                        <!-- Description Length -->
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-align-left text-gray-600 mr-3"></i>
                                <span class="text-sm font-medium">Description Length</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <?php
                                $descLength = strlen($page['meta_description']);
                                $descStatus = $descLength >= 150 && $descLength <= 160 ? 'good' : ($descLength < 150 ? 'short' : 'long');
                                ?>
                                <span class="text-sm text-gray-600"><?= $descLength ?> characters</span>
                                <i class="fas fa-circle text-<?= $descStatus === 'good' ? 'green' : ($descStatus === 'short' ? 'yellow' : 'red') ?>-500"></i>
                            </div>
                        </div>

                        <!-- Content Length -->
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-file-text text-gray-600 mr-3"></i>
                                <span class="text-sm font-medium">Content Length</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <?php
                                $contentLength = strlen(strip_tags($page['content']));
                                $contentStatus = $contentLength >= 300 ? 'good' : 'short';
                                ?>
                                <span class="text-sm text-gray-600"><?= number_format($contentLength) ?> characters</span>
                                <i class="fas fa-circle text-<?= $contentStatus === 'good' ? 'green' : 'yellow' ?>-500"></i>
                            </div>
                        </div>

                        <!-- URL Structure -->
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-link text-gray-600 mr-3"></i>
                                <span class="text-sm font-medium">URL Structure</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <?php
                                $urlLength = strlen($page['slug']);
                                $urlStatus = $urlLength <= 75 && !str_contains($page['slug'], '_') ? 'good' : 'needs-improvement';
                                ?>
                                <span class="text-sm text-gray-600"><?= $urlLength ?> characters</span>
                                <i class="fas fa-circle text-<?= $urlStatus === 'good' ? 'green' : 'yellow' ?>-500"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Actions -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <a href="/admin/pages/<?= $page['id'] ?>/edit"
                           class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors text-center block">
                            <i class="fas fa-edit mr-2"></i>Edit Page
                        </a>

                        <a href="/<?= $page['slug'] ?>" target="_blank"
                           class="w-full bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition-colors text-center block">
                            <i class="fas fa-external-link-alt mr-2"></i>View Live
                        </a>

                        <?php if ($page['status'] === 'published'): ?>
                            <button onclick="toggleStatus('draft')"
                                    class="w-full bg-yellow-600 text-white py-2 px-4 rounded-lg hover:bg-yellow-700 transition-colors">
                                <i class="fas fa-eye-slash mr-2"></i>Unpublish
                            </button>
                        <?php else: ?>
                            <button onclick="toggleStatus('published')"
                                    class="w-full bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition-colors">
                                <i class="fas fa-globe mr-2"></i>Publish
                            </button>
                        <?php endif; ?>

                        <button onclick="duplicatePage()"
                                class="w-full bg-purple-600 text-white py-2 px-4 rounded-lg hover:bg-purple-700 transition-colors">
                            <i class="fas fa-copy mr-2"></i>Duplicate
                        </button>

                        <button onclick="deletePage()"
                                class="w-full bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition-colors">
                            <i class="fas fa-trash mr-2"></i>Delete Page
                        </button>
                    </div>
                </div>

                <!-- Page Statistics -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Page Statistics</h3>
                    <div class="space-y-4">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-blue-600"><?= $page['views'] ?? 0 ?></p>
                            <p class="text-sm text-gray-600">Total Views</p>
                        </div>
                        <div class="text-center">
                            <p class="text-lg font-medium text-gray-800"><?= date('M d, Y', strtotime($page['updated_at'])) ?></p>
                            <p class="text-sm text-gray-600">Last Updated</p>
                        </div>
                        <div class="text-center">
                            <p class="text-lg font-medium text-gray-800"><?= number_format(strlen($page['content'])) ?></p>
                            <p class="text-sm text-gray-600">Content Length</p>
                        </div>
                    </div>
                </div>

                <!-- Template Info -->
                <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Template</h3>
                    <p class="text-blue-800 font-medium"><?= ucfirst($page['template']) ?></p>
                    <p class="text-blue-700 text-sm mt-1">
                        <?php
                        $templateDescriptions = [
                            'default' => 'Standard page layout with header and footer',
                            'landing' => 'Full-width landing page without navigation',
                            'full-width' => 'Full-width layout for maximum content space'
                        ];
                        echo $templateDescriptions[$page['template']] ?? 'Custom template layout';
                        ?>
                    </p>
                </div>

                <!-- Recent Activity -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Activity</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-edit text-blue-600"></i>
                            <span class="text-gray-600">Last edited <?= time_ago($page['updated_at']) ?></span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-plus text-green-600"></i>
                            <span class="text-gray-600">Created <?= time_ago($page['created_at']) ?></span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-<?= $page['status'] === 'published' ? 'globe' : 'eye-slash' ?> text-<?= $page['status'] === 'published' ? 'green' : 'yellow' ?>-600"></i>
                            <span class="text-gray-600"><?= ucfirst($page['status']) ?> status</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Fullscreen Modal -->
    <div id="fullscreenModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="h-full flex flex-col">
            <div class="flex justify-between items-center p-4 bg-white border-b">
                <h3 class="text-lg font-semibold">Page Content</h3>
                <button onclick="toggleFullscreen()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="flex-1 overflow-auto p-6 bg-white">
                <div id="fullscreenContent" class="prose max-w-none">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <script>
        let isPreviewMode = false;

        // Toggle between raw content and preview
        function toggleContentView() {
            const rawContent = document.getElementById('rawContent');
            const previewContent = document.getElementById('previewContent');
            const toggleBtn = document.getElementById('contentToggle');

            if (isPreviewMode) {
                rawContent.style.display = 'block';
                previewContent.style.display = 'none';
                toggleBtn.innerHTML = '<i class="fas fa-eye mr-1"></i>Show Preview';
                isPreviewMode = false;
            } else {
                rawContent.style.display = 'none';
                previewContent.style.display = 'block';
                toggleBtn.innerHTML = '<i class="fas fa-code mr-1"></i>Show Raw';
                isPreviewMode = true;
            }
        }

        // Toggle fullscreen view
        function toggleFullscreen() {
            const modal = document.getElementById('fullscreenModal');
            const fullscreenContent = document.getElementById('fullscreenContent');

            if (modal.classList.contains('hidden')) {
                fullscreenContent.innerHTML = `<?= $page['content'] ?>`;
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            } else {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            }
        }

        // Copy to clipboard
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                // Show temporary success message
                const btn = event.target;
                const originalClass = btn.className;
                btn.className = 'fas fa-check text-green-600';
                setTimeout(() => {
                    btn.className = originalClass;
                }, 2000);
            });
        }

        // Toggle page status
        function toggleStatus(newStatus) {
            const action = newStatus === 'published' ? 'publish' : 'unpublish';
            if (confirm(`Are you sure you want to ${action} this page?`)) {
                // Create form and submit
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/admin/pages/<?= $page['id'] ?>';

                form.innerHTML = `
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" name="status" value="${newStatus}">
                    <input type="hidden" name="title" value="<?= addslashes($page['title']) ?>">
                    <input type="hidden" name="slug" value="<?= $page['slug'] ?>">
                    <input type="hidden" name="content" value="<?= addslashes($page['content']) ?>">
                    <input type="hidden" name="template" value="<?= $page['template'] ?>">
                `;

                document.body.appendChild(form);
                form.submit();
            }
        }

        // Duplicate page
        function duplicatePage() {
            if (confirm('Create a copy of this page?')) {
                window.location.href = '/admin/pages/<?= $page['id'] ?>/duplicate';
            }
        }

        // Delete page
        function deletePage() {
            if (confirm('Are you sure you want to delete this page? This action cannot be undone.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/admin/pages/<?= $page['id'] ?>';
                form.innerHTML = `
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="DELETE">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // E for edit
            if (e.key === 'e' && !e.ctrlKey && !e.metaKey) {
                const activeElement = document.activeElement;
                if (activeElement.tagName !== 'INPUT' && activeElement.tagName !== 'TEXTAREA') {
                    window.location.href = '/admin/pages/<?= $page['id'] ?>/edit';
                }
            }

            // V for view live
            if (e.key === 'v' && !e.ctrlKey && !e.metaKey) {
                const activeElement = document.activeElement;
                if (activeElement.tagName !== 'INPUT' && activeElement.tagName !== 'TEXTAREA') {
                    window.open('/<?= $page['slug'] ?>', '_blank');
                }
            }

            // P for preview toggle
            if (e.key === 'p' && !e.ctrlKey && !e.metaKey) {
                const activeElement = document.activeElement;
                if (activeElement.tagName !== 'INPUT' && activeElement.tagName !== 'TEXTAREA') {
                    e.preventDefault();
                    toggleContentView();
                }
            }

            // F for fullscreen
            if (e.key === 'f' && !e.ctrlKey && !e.metaKey) {
                const activeElement = document.activeElement;
                if (activeElement.tagName !== 'INPUT' && activeElement.tagName !== 'TEXTAREA') {
                    e.preventDefault();
                    toggleFullscreen();
                }
            }

            // Escape to close fullscreen
            if (e.key === 'Escape') {
                const modal = document.getElementById('fullscreenModal');
                if (!modal.classList.contains('hidden')) {
                    toggleFullscreen();
                }
            }
        });

        // Close fullscreen when clicking outside
        document.getElementById('fullscreenModal').addEventListener('click', function(e) {
            if (e.target === this) {
                toggleFullscreen();
            }
        });
    </script>
<?= $this->endSection() ?>
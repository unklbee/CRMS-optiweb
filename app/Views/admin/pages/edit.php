<?= $this->extend('admin/layout/main') ?>

<?= $this->section('content') ?>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit Page</h1>
                <p class="text-gray-600"><?= $page['title'] ?></p>
            </div>
            <div class="flex space-x-2">
                <a href="/<?= $page['slug'] ?>" target="_blank" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-external-link-alt mr-2"></i>Preview
                </a>
                <a href="/admin/pages/<?= $page['id'] ?>" class="text-gray-600 hover:text-gray-900 px-4 py-2">
                    <i class="fas fa-eye mr-2"></i>View Details
                </a>
                <a href="/admin/pages" class="text-gray-600 hover:text-gray-900 px-4 py-2">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Pages
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

            <form action="/admin/pages/<?= $page['id'] ?>" method="POST" class="space-y-6" id="editPageForm">
                <?= csrf_field() ?>
                <input type="hidden" name="_method" value="PUT">

                <!-- Basic Information -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Basic Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Page Title *</label>
                            <input type="text" name="title" value="<?= old('title', $page['title']) ?>" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   id="pageTitle">
                            <p class="text-xs text-gray-500 mt-1">This will appear in browser tabs and search results</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">URL Slug</label>
                            <div class="flex">
                                <span class="inline-flex items-center px-3 py-2 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                                    <?= base_url() ?>
                                </span>
                                <input type="text" name="slug" value="<?= old('slug', $page['slug']) ?>"
                                       class="flex-1 px-4 py-2 border border-gray-300 rounded-r-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="auto-generated-from-title" id="pageSlug">
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Leave empty to auto-generate from title</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                            <select name="status" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="draft" <?= $page['status'] == 'draft' ? 'selected' : '' ?>>Draft</option>
                                <option value="published" <?= $page['status'] == 'published' ? 'selected' : '' ?>>Published</option>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">
                                <?php if ($page['status'] === 'published'): ?>
                                    <span class="text-green-600">Currently live</span>
                                <?php else: ?>
                                    <span class="text-yellow-600">Not visible to public</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-sm font-medium text-gray-700">Page Content</label>
                        <div class="flex space-x-2">
                            <button type="button" onclick="toggleEditor('visual')" class="text-sm bg-gray-100 text-gray-700 px-3 py-1 rounded-lg hover:bg-gray-200 transition-colors" id="visualBtn">
                                <i class="fas fa-eye mr-1"></i>Visual
                            </button>
                            <button type="button" onclick="toggleEditor('code')" class="text-sm bg-gray-100 text-gray-700 px-3 py-1 rounded-lg hover:bg-gray-200 transition-colors" id="codeBtn">
                                <i class="fas fa-code mr-1"></i>HTML
                            </button>
                            <button type="button" onclick="toggleFullscreen()" class="text-sm bg-gray-100 text-gray-700 px-3 py-1 rounded-lg hover:bg-gray-200 transition-colors">
                                <i class="fas fa-expand mr-1"></i>Fullscreen
                            </button>
                        </div>
                    </div>
                    <textarea name="content" rows="20"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent font-mono text-sm"
                              placeholder="Enter your page content here..." id="contentEditor"><?= old('content', $page['content']) ?></textarea>
                    <div class="mt-2 flex justify-between items-center text-xs text-gray-500">
                        <span>Use HTML for formatting. Basic styling is supported.</span>
                        <span id="characterCount">0 characters</span>
                    </div>
                </div>

                <!-- SEO Settings -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">SEO Settings</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Meta Title</label>
                            <input type="text" name="meta_title" value="<?= old('meta_title', $page['meta_title']) ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="SEO optimized title (leave empty to use page title)" maxlength="60" id="metaTitle">
                            <div class="mt-1 flex justify-between text-xs">
                                <span class="text-gray-500">Recommended: 50-60 characters</span>
                                <span id="metaTitleCount" class="text-gray-500">0/60</span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Meta Description</label>
                            <textarea name="meta_description" rows="3"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="Brief description for search engines" maxlength="160" id="metaDescription"><?= old('meta_description', $page['meta_description']) ?></textarea>
                            <div class="mt-1 flex justify-between text-xs">
                                <span class="text-gray-500">Recommended: 150-160 characters</span>
                                <span id="metaDescCount" class="text-gray-500">0/160</span>
                            </div>
                        </div>

                        <!-- SEO Preview -->
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Search Engine Preview</h4>
                            <div class="bg-white p-3 rounded border">
                                <div class="text-blue-600 text-lg font-medium hover:underline cursor-pointer" id="seoPreviewTitle">
                                    <?= $page['meta_title'] ?: $page['title'] ?>
                                </div>
                                <div class="text-green-700 text-sm" id="seoPreviewUrl">
                                    <?= base_url($page['slug']) ?>
                                </div>
                                <div class="text-gray-600 text-sm mt-1" id="seoPreviewDescription">
                                    <?= $page['meta_description'] ?: truncate_text(strip_tags($page['content']), 160) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Advanced Settings -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Advanced Settings</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Featured Image URL</label>
                            <input type="url" name="featured_image" value="<?= old('featured_image', $page['featured_image']) ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="https://example.com/image.jpg" id="featuredImage">
                            <p class="text-xs text-gray-500 mt-1">Used for social media sharing</p>

                            <!-- Image Preview -->
                            <div class="mt-2" id="imagePreview" style="<?= $page['featured_image'] ? '' : 'display: none;' ?>">
                                <img src="<?= $page['featured_image'] ?>" alt="Featured Image" class="w-20 h-20 object-cover rounded border" id="previewImg">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Template</label>
                            <select name="template" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="default" <?= $page['template'] == 'default' ? 'selected' : '' ?>>Default</option>
                                <option value="landing" <?= $page['template'] == 'landing' ? 'selected' : '' ?>>Landing Page</option>
                                <option value="full-width" <?= $page['template'] == 'full-width' ? 'selected' : '' ?>>Full Width</option>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">
                                <?php
                                $templateDesc = [
                                    'default' => 'Standard layout with header and footer',
                                    'landing' => 'Full-width page without navigation',
                                    'full-width' => 'Maximum content width'
                                ];
                                echo $templateDesc[$page['template']] ?? 'Standard layout';
                                ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Page Statistics -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Page Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div>
                            <span class="text-gray-600">Created:</span>
                            <span class="font-medium"><?= date('M d, Y H:i', strtotime($page['created_at'])) ?></span>
                        </div>
                        <div>
                            <span class="text-gray-600">Last Updated:</span>
                            <span class="font-medium"><?= date('M d, Y H:i', strtotime($page['updated_at'])) ?></span>
                        </div>
                        <div>
                            <span class="text-gray-600">Content Length:</span>
                            <span class="font-medium"><?= number_format(strlen($page['content'])) ?> characters</span>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                    <!-- Left side actions -->
                    <div class="flex space-x-2">
                        <button type="button" onclick="saveDraft()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm">
                            <i class="fas fa-save mr-2"></i>Save Draft
                        </button>
                        <button type="button" onclick="previewPage()" class="px-4 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors text-sm">
                            <i class="fas fa-eye mr-2"></i>Preview
                        </button>
                    </div>

                    <!-- Right side actions -->
                    <div class="flex space-x-4">
                        <a href="/admin/pages" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                            Cancel
                        </a>
                        <?php if ($page['status'] === 'draft'): ?>
                            <button type="submit" name="action" value="publish" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                <i class="fas fa-globe mr-2"></i>Save & Publish
                            </button>
                        <?php endif; ?>
                        <button type="submit" name="action" value="save" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-save mr-2"></i>Update Page
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Fullscreen Editor Modal -->
    <div id="fullscreenModal" class="fixed inset-0 bg-white z-50 hidden">
        <div class="h-full flex flex-col">
            <div class="flex justify-between items-center p-4 bg-gray-50 border-b">
                <h3 class="text-lg font-semibold">Content Editor</h3>
                <div class="flex space-x-2">
                    <button onclick="toggleEditor('visual')" class="text-sm bg-gray-200 text-gray-700 px-3 py-1 rounded hover:bg-gray-300" id="fullscreenVisualBtn">Visual</button>
                    <button onclick="toggleEditor('code')" class="text-sm bg-gray-200 text-gray-700 px-3 py-1 rounded hover:bg-gray-300" id="fullscreenCodeBtn">HTML</button>
                    <button onclick="toggleFullscreen()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            <div class="flex-1 p-4">
                <textarea id="fullscreenEditor" class="w-full h-full border border-gray-300 rounded-lg p-4 font-mono text-sm resize-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
            </div>
            <div class="p-4 bg-gray-50 border-t">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600" id="fullscreenCharCount">0 characters</span>
                    <button onclick="saveFullscreenContent()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                        <i class="fas fa-save mr-2"></i>Save & Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let isFullscreen = false;
        let formChanged = false;

        // Track form changes
        const form = document.getElementById('editPageForm');
        const inputs = form.querySelectorAll('input, select, textarea');

        inputs.forEach(input => {
            input.addEventListener('change', () => {
                formChanged = true;
            });
        });

        // Auto-generate slug from title
        document.getElementById('pageTitle').addEventListener('input', function() {
            const title = this.value;
            const slug = title.toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .trim('-');

            const slugField = document.getElementById('pageSlug');
            if (!slugField.value || slugField.value === slugField.dataset.original) {
                slugField.value = slug;
                slugField.dataset.original = slug;
            }

            updateSEOPreview();
        });

        // Update character counts
        function updateCharacterCounts() {
            const contentEditor = document.getElementById('contentEditor');
            const metaTitle = document.getElementById('metaTitle');
            const metaDescription = document.getElementById('metaDescription');

            // Content count
            document.getElementById('characterCount').textContent = contentEditor.value.length + ' characters';

            // Meta title count
            const titleCount = metaTitle.value.length;
            document.getElementById('metaTitleCount').textContent = titleCount + '/60';
            document.getElementById('metaTitleCount').className = titleCount > 60 ? 'text-red-500' : 'text-gray-500';

            // Meta description count
            const descCount = metaDescription.value.length;
            document.getElementById('metaDescCount').textContent = descCount + '/160';
            document.getElementById('metaDescCount').className = descCount > 160 ? 'text-red-500' : 'text-gray-500';
        }

        // Update SEO preview
        function updateSEOPreview() {
            const title = document.getElementById('metaTitle').value || document.getElementById('pageTitle').value;
            const slug = document.getElementById('pageSlug').value;
            const description = document.getElementById('metaDescription').value;

            document.getElementById('seoPreviewTitle').textContent = title || 'Page Title';
            document.getElementById('seoPreviewUrl').textContent = '<?= base_url() ?>' + (slug || 'page-url');
            document.getElementById('seoPreviewDescription').textContent = description || 'Page description will appear here...';
        }

        // Featured image preview
        document.getElementById('featuredImage').addEventListener('input', function() {
            const imagePreview = document.getElementById('imagePreview');
            const previewImg = document.getElementById('previewImg');

            if (this.value) {
                previewImg.src = this.value;
                imagePreview.style.display = 'block';
            } else {
                imagePreview.style.display = 'none';
            }
        });

        // Toggle editor modes
        function toggleEditor(mode) {
            const visualBtn = document.getElementById('visualBtn');
            const codeBtn = document.getElementById('codeBtn');
            const fullscreenVisualBtn = document.getElementById('fullscreenVisualBtn');
            const fullscreenCodeBtn = document.getElementById('fullscreenCodeBtn');

            if (mode === 'visual') {
                visualBtn.classList.add('bg-blue-600', 'text-white');
                visualBtn.classList.remove('bg-gray-100', 'text-gray-700');
                codeBtn.classList.remove('bg-blue-600', 'text-white');
                codeBtn.classList.add('bg-gray-100', 'text-gray-700');

                if (isFullscreen) {
                    fullscreenVisualBtn.classList.add('bg-blue-600', 'text-white');
                    fullscreenCodeBtn.classList.remove('bg-blue-600', 'text-white');
                }

                // In a real implementation, you'd initialize a WYSIWYG editor here
                alert('Visual editor would be initialized here (TinyMCE, CKEditor, etc.)');
            } else {
                codeBtn.classList.add('bg-blue-600', 'text-white');
                codeBtn.classList.remove('bg-gray-100', 'text-gray-700');
                visualBtn.classList.remove('bg-blue-600', 'text-white');
                visualBtn.classList.add('bg-gray-100', 'text-gray-700');

                if (isFullscreen) {
                    fullscreenCodeBtn.classList.add('bg-blue-600', 'text-white');
                    fullscreenVisualBtn.classList.remove('bg-blue-600', 'text-white');
                }
            }
        }

        // Toggle fullscreen editor
        function toggleFullscreen() {
            const modal = document.getElementById('fullscreenModal');
            const contentEditor = document.getElementById('contentEditor');
            const fullscreenEditor = document.getElementById('fullscreenEditor');

            if (!isFullscreen) {
                fullscreenEditor.value = contentEditor.value;
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                isFullscreen = true;
                updateFullscreenCharCount();
            } else {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
                isFullscreen = false;
            }
        }

        // Save fullscreen content
        function saveFullscreenContent() {
            const contentEditor = document.getElementById('contentEditor');
            const fullscreenEditor = document.getElementById('fullscreenEditor');

            contentEditor.value = fullscreenEditor.value;
            toggleFullscreen();
            updateCharacterCounts();
            formChanged = true;
        }

        // Update fullscreen character count
        function updateFullscreenCharCount() {
            const fullscreenEditor = document.getElementById('fullscreenEditor');
            document.getElementById('fullscreenCharCount').textContent = fullscreenEditor.value.length + ' characters';
        }

        // Save draft
        function saveDraft() {
            const formData = new FormData(form);
            formData.set('status', 'draft');

            // In a real implementation, you'd send this via AJAX
            const btn = event.target;
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';

            setTimeout(() => {
                btn.innerHTML = '<i class="fas fa-check mr-2"></i>Saved!';
                btn.classList.remove('bg-gray-100', 'text-gray-700');
                btn.classList.add('bg-green-100', 'text-green-700');

                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.classList.remove('bg-green-100', 'text-green-700');
                    btn.classList.add('bg-gray-100', 'text-gray-700');
                }, 2000);
            }, 1000);
        }

        // Preview page
        function previewPage() {
            window.open('/<?= $page['slug'] ?>', '_blank');
        }

        // Event listeners
        document.getElementById('contentEditor').addEventListener('input', updateCharacterCounts);
        document.getElementById('metaTitle').addEventListener('input', function() {
            updateCharacterCounts();
            updateSEOPreview();
        });
        document.getElementById('metaDescription').addEventListener('input', function() {
            updateCharacterCounts();
            updateSEOPreview();
        });
        document.getElementById('pageSlug').addEventListener('input', updateSEOPreview);

        document.getElementById('fullscreenEditor').addEventListener('input', updateFullscreenCharCount);

        // Form validation
        form.addEventListener('submit', function(e) {
            const title = document.getElementById('pageTitle').value.trim();
            const content = document.getElementById('contentEditor').value.trim();

            if (title.length < 2) {
                alert('Page title must be at least 2 characters long.');
                e.preventDefault();
                return;
            }

            // Reset form changed flag
            formChanged = false;
        });

        // Warn about unsaved changes
        window.addEventListener('beforeunload', function(e) {
            if (formChanged) {
                e.preventDefault();
                e.returnValue = '';
                return 'You have unsaved changes. Are you sure you want to leave?';
            }
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl/Cmd + S to save
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
                saveDraft();
            }

            // Ctrl/Cmd + Enter to submit
            if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
                e.preventDefault();
                form.submit();
            }

            // F11 for fullscreen
            if (e.key === 'F11') {
                e.preventDefault();
                toggleFullscreen();
            }

            // Escape to close fullscreen
            if (e.key === 'Escape' && isFullscreen) {
                toggleFullscreen();
            }
        });

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            updateCharacterCounts();
            updateSEOPreview();

            // Set initial slug dataset
            const slugField = document.getElementById('pageSlug');
            slugField.dataset.original = slugField.value;
        });

        // Auto-save functionality (optional)
        let autoSaveTimer;
        function autoSave() {
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(() => {
                if (formChanged) {
                    // Implement auto-save logic here
                    console.log('Auto-save triggered');
                }
            }, 30000); // Auto-save every 30 seconds
        }

        // Trigger auto-save on input
        inputs.forEach(input => {
            input.addEventListener('input', autoSave);
        });
    </script>
<?= $this->endSection() ?>
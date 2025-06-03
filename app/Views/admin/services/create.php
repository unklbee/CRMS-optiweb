<?= $this->extend('admin/layout/main') ?>

<?= $this->section('content') ?>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Add New Service</h1>
                <p class="text-gray-600">Create a new repair service</p>
            </div>
            <a href="/admin/services" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left mr-2"></i>Back to Services
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

            <form action="/admin/services" method="POST" class="space-y-6">
                <?= csrf_field() ?>

                <!-- Basic Information -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Service Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Service Name *</label>
                            <input type="text" name="name" value="<?= old('name') ?>" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="e.g., Screen Replacement, Virus Removal">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                            <select name="category_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>" <?= old('category_id') == $category['id'] ? 'selected' : '' ?>>
                                        <?= $category['name'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                            <select name="status" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="active" <?= old('status', 'active') == 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= old('status') == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="4"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Detailed description of the service..."><?= old('description') ?></textarea>
                </div>

                <!-- Pricing & Duration -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Pricing & Duration</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Base Price (Rp) *</label>
                            <input type="number" name="base_price" value="<?= old('base_price') ?>" required min="0" step="1000"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="150000">
                            <p class="text-xs text-gray-500 mt-1">Starting price for this service</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Estimated Duration (minutes) *</label>
                            <input type="number" name="estimated_duration" value="<?= old('estimated_duration', 60) ?>" required min="15" step="15"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="60">
                            <p class="text-xs text-gray-500 mt-1">Estimated time to complete this service</p>
                        </div>
                    </div>
                </div>

                <!-- Quick Duration Buttons -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Quick Duration Selection</label>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" onclick="setDuration(30)" class="px-3 py-1 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm">30 min</button>
                        <button type="button" onclick="setDuration(60)" class="px-3 py-1 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm">1 hour</button>
                        <button type="button" onclick="setDuration(90)" class="px-3 py-1 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm">1.5 hours</button>
                        <button type="button" onclick="setDuration(120)" class="px-3 py-1 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm">2 hours</button>
                        <button type="button" onclick="setDuration(180)" class="px-3 py-1 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm">3 hours</button>
                        <button type="button" onclick="setDuration(240)" class="px-3 py-1 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm">4 hours</button>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="/admin/services" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>Create Service
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function setDuration(minutes) {
            document.querySelector('input[name="estimated_duration"]').value = minutes;
        }

        // Auto-format price input
        document.querySelector('input[name="base_price"]').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            e.target.value = value;
        });

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const name = document.querySelector('input[name="name"]').value.trim();
            const category = document.querySelector('select[name="category_id"]').value;
            const price = document.querySelector('input[name="base_price"]').value;
            const duration = document.querySelector('input[name="estimated_duration"]').value;

            if (name.length < 2) {
                alert('Service name must be at least 2 characters long.');
                e.preventDefault();
                return;
            }

            if (!category) {
                alert('Please select a category.');
                e.preventDefault();
                return;
            }

            if (!price || price < 0) {
                alert('Please enter a valid base price.');
                e.preventDefault();
                return;
            }

            if (!duration || duration < 15) {
                alert('Estimated duration must be at least 15 minutes.');
                e.preventDefault();
                return;
            }
        });
    </script>
<?= $this->endSection() ?>
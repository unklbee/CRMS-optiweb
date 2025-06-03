<?= $this->extend('frontend/layout/main') ?>

<?= $this->section('content') ?>
    <div class="container mx-auto px-4 py-12">
        <?= breadcrumb($breadcrumb ?? []) ?>

        <div class="text-center mb-12">
            <h1 class="text-3xl font-bold text-gray-800 mb-4">Our Services</h1>
            <p class="text-xl text-gray-600">Professional repair services for all your devices</p>
        </div>

        <!-- Service Categories Filter -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <div class="flex flex-wrap gap-4 justify-center">
                <a href="/services" class="px-6 py-2 rounded-full border-2 <?= !$selected_category ? 'border-blue-600 bg-blue-600 text-white' : 'border-gray-300 text-gray-700 hover:border-blue-600' ?> transition-colors">
                    All Services
                </a>
                <?php foreach ($categories as $category): ?>
                    <a href="/services?category=<?= $category['id'] ?>"
                       class="px-6 py-2 rounded-full border-2 <?= $selected_category == $category['id'] ? 'border-blue-600 bg-blue-600 text-white' : 'border-gray-300 text-gray-700 hover:border-blue-600' ?> transition-colors">
                        <i class="<?= $category['icon'] ?> mr-2"></i><?= $category['name'] ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Services Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($services as $service): ?>
                <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <div class="bg-blue-100 w-12 h-12 rounded-lg flex items-center justify-center mr-4">
                                <i class="fas fa-tools text-blue-600 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-semibold text-gray-800"><?= $service['name'] ?></h3>
                                <p class="text-sm text-gray-600"><?= $service['category_name'] ?? '' ?></p>
                            </div>
                        </div>

                        <p class="text-gray-600 mb-4"><?= $service['description'] ?></p>

                        <div class="flex justify-between items-center">
                            <div>
                                <span class="text-sm text-gray-500">Starting from</span>
                                <p class="text-2xl font-bold text-blue-600"><?= format_currency($service['base_price']) ?></p>
                            </div>
                            <div class="text-right">
                                <span class="text-sm text-gray-500">Duration</span>
                                <p class="text-sm font-medium"><?= $service['estimated_duration'] ?> min</p>
                            </div>
                        </div>

                        <div class="mt-6">
                            <a href="/book-service?service=<?= $service['id'] ?>"
                               class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors text-center inline-block">
                                Book This Service
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (empty($services)): ?>
            <div class="text-center py-12">
                <i class="fas fa-search text-4xl text-gray-400 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">No Services Found</h3>
                <p class="text-gray-600">Try selecting a different category</p>
            </div>
        <?php endif; ?>

        <!-- CTA Section -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-xl p-8 mt-12 text-center">
            <h2 class="text-2xl font-bold mb-4">Need a Custom Service?</h2>
            <p class="text-blue-100 mb-6">Can't find what you're looking for? Contact us for custom repair solutions.</p>
            <a href="/contact" class="bg-white text-blue-600 px-6 py-3 rounded-lg hover:bg-gray-100 transition-colors font-semibold">
                Contact Our Experts
            </a>
        </div>
    </div>
<?= $this->endSection() ?>
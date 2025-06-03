<?= $this->extend('frontend/layout/main') ?>

<?= $this->section('content') ?>
    <!-- Hero Section -->
    <section class="bg-gradient-to-br from-blue-600 to-blue-800 text-white">
        <div class="container mx-auto px-4 py-20">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="space-y-6">
                    <h1 class="text-4xl lg:text-5xl font-bold leading-tight">
                        <?= $hero_content['title'] ?>
                    </h1>
                    <p class="text-xl text-blue-100">
                        <?= $hero_content['subtitle'] ?>
                    </p>

                    <!-- Features -->
                    <div class="grid grid-cols-2 gap-4 mt-8">
                        <?php foreach ($hero_content['features'] as $feature): ?>
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-check-circle text-green-400"></i>
                                <span><?= $feature ?></span>
                            </div>
                        <?php endforeach; ?>
                        <div class="space-y-4 mt-8">
                            <div class="bg-white bg-opacity-20 rounded-lg p-4">
                                <i class="fas fa-desktop text-3xl mb-2"></i>
                                <p class="text-sm">PC Repair</p>
                            </div>
                            <div class="bg-white bg-opacity-20 rounded-lg p-4">
                                <i class="fas fa-tablet-alt text-3xl mb-2"></i>
                                <p class="text-sm">Tablet Repair</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Service Categories Section -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-800 mb-4">Our Services</h2>
                <p class="text-xl text-gray-600">Professional repair services for all your devices</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($service_categories as $category): ?>
                    <div class="group bg-white border border-gray-200 rounded-xl p-6 hover:shadow-xl hover:border-blue-300 transition-all duration-300">
                        <div class="text-center">
                            <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-blue-200 transition-colors">
                                <i class="<?= $category['icon'] ?> text-2xl text-blue-600"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-800 mb-3"><?= $category['name'] ?></h3>
                            <p class="text-gray-600 mb-4"><?= $category['description'] ?></p>
                            <a href="/services?category=<?= $category['id'] ?>"
                               class="text-blue-600 hover:text-blue-800 font-medium">
                                Learn More <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Process Section -->
    <section class="py-20 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-800 mb-4">How It Works</h2>
                <p class="text-xl text-gray-600">Simple steps to get your device repaired</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="bg-blue-600 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-white text-xl font-bold">1</span>
                    </div>
                    <h3 class="text-lg font-semibold mb-2">Book Service</h3>
                    <p class="text-gray-600">Schedule your repair online or visit our shop</p>
                </div>
                <div class="text-center">
                    <div class="bg-blue-600 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-white text-xl font-bold">2</span>
                    </div>
                    <h3 class="text-lg font-semibold mb-2">Diagnosis</h3>
                    <p class="text-gray-600">Free diagnosis to identify the problem</p>
                </div>
                <div class="text-center">
                    <div class="bg-blue-600 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-white text-xl font-bold">3</span>
                    </div>
                    <h3 class="text-lg font-semibold mb-2">Repair</h3>
                    <p class="text-gray-600">Expert technicians fix your device</p>
                </div>
                <div class="text-center">
                    <div class="bg-blue-600 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-white text-xl font-bold">4</span>
                    </div>
                    <h3 class="text-lg font-semibold mb-2">Pickup</h3>
                    <p class="text-gray-600">Get your device back in perfect condition</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-800 mb-4">What Our Customers Say</h2>
                <p class="text-xl text-gray-600">Real feedback from satisfied customers</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <?php foreach ($testimonials as $testimonial): ?>
                    <div class="bg-gray-50 rounded-xl p-6">
                        <div class="flex items-center mb-4">
                            <div class="flex text-yellow-400">
                                <?php for ($i = 0; $i < $testimonial['rating']; $i++): ?>
                                    <i class="fas fa-star"></i>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <p class="text-gray-700 mb-4 italic">"<?= $testimonial['comment'] ?>"</p>
                        <div>
                            <p class="font-semibold text-gray-800"><?= $testimonial['name'] ?></p>
                            <p class="text-sm text-gray-600"><?= $testimonial['device'] ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gradient-to-r from-blue-600 to-blue-800 text-white">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl lg:text-4xl font-bold mb-4">Ready to Fix Your Device?</h2>
            <p class="text-xl text-blue-100 mb-8">Get professional repair service with warranty included</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="/book-service"
                   class="bg-green-500 text-white px-8 py-3 rounded-lg hover:bg-green-600 transition-colors font-semibold">
                    <i class="fas fa-tools mr-2"></i>Book Repair Service
                </a>
                <a href="/contact"
                   class="border-2 border-white text-white px-8 py-3 rounded-lg hover:bg-white hover:text-blue-800 transition-colors font-semibold">
                    <i class="fas fa-phone mr-2"></i>Call Us Now
                </a>
            </div>
        </div>
    </section>
<?= $this->endSection() ?>
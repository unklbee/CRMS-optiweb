<?= $this->extend('frontend/layout/main') ?>

<?= $this->section('content') ?>
    <div class="container mx-auto px-4 py-12">
        <?= breadcrumb($breadcrumb ?? []) ?>

        <div class="text-center mb-12">
            <h1 class="text-3xl font-bold text-gray-800 mb-4">Contact Us</h1>
            <p class="text-xl text-gray-600">Get in touch with our repair experts</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- Contact Information -->
            <div class="space-y-8">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Get in Touch</h2>
                    <div class="space-y-6">
                        <div class="flex items-start space-x-4">
                            <div class="bg-blue-100 w-12 h-12 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-map-marker-alt text-blue-600 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800 mb-1">Address</h3>
                                <p class="text-gray-600"><?= $contact_info['address'] ?></p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4">
                            <div class="bg-blue-100 w-12 h-12 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-phone text-blue-600 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800 mb-1">Phone</h3>
                                <p class="text-gray-600"><?= $contact_info['phone'] ?></p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4">
                            <div class="bg-blue-100 w-12 h-12 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-envelope text-blue-600 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800 mb-1">Email</h3>
                                <p class="text-gray-600"><?= $contact_info['email'] ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Business Hours -->
                <?php if (!empty($contact_info['business_hours'])): ?>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800 mb-6">Business Hours</h2>
                        <div class="bg-gray-50 rounded-lg p-6">
                            <?php foreach ($contact_info['business_hours'] as $day => $hours): ?>
                                <div class="flex justify-between py-2 border-b border-gray-200 last:border-b-0">
                                    <span class="font-medium text-gray-800"><?= ucfirst($day) ?></span>
                                    <span class="text-gray-600"><?= $hours === 'closed' ? 'Closed' : $hours ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Contact Form -->
            <div class="bg-white rounded-xl shadow-lg p-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Send us a Message</h2>

                <?php if (session()->getFlashdata('errors')): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                        <ul class="list-disc list-inside space-y-1">
                            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                <li><?= $error ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="/contact" method="POST" class="space-y-6">
                    <?= csrf_field() ?>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Name *</label>
                            <input type="text" name="name" value="<?= old('name') ?>" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                            <input type="email" name="email" value="<?= old('email') ?>" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Subject *</label>
                        <input type="text" name="subject" value="<?= old('subject') ?>" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Message *</label>
                        <textarea name="message" rows="6" required
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"><?= old('message') ?></textarea>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                        <i class="fas fa-paper-plane mr-2"></i>Send Message
                    </button>
                </form>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
<?= $this->extend('frontend/layout/main') ?>

<?= $this->section('content') ?>
    <div class="container mx-auto px-4 py-12">
        <?= breadcrumb($breadcrumb ?? []) ?>

        <div class="max-w-4xl mx-auto">
            <!-- Page Header -->
            <div class="text-center mb-12">
                <h1 class="text-3xl font-bold text-gray-800 mb-4"><?= $page['title'] ?></h1>
                <?php if ($page['meta_description']): ?>
                    <p class="text-xl text-gray-600"><?= $page['meta_description'] ?></p>
                <?php endif; ?>
            </div>

            <!-- Featured Image -->
            <?php if ($page['featured_image']): ?>
                <div class="mb-12">
                    <img src="<?= $page['featured_image'] ?>" alt="<?= $page['title'] ?>"
                         class="w-full h-64 object-cover rounded-xl shadow-lg">
                </div>
            <?php endif; ?>

            <!-- Page Content -->
            <div class="bg-white rounded-xl shadow-lg p-8">
                <div class="prose prose-lg max-w-none">
                    <?= $page['content'] ?>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Computer Repair Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center">
<div class="bg-white p-8 rounded-2xl shadow-2xl w-full max-w-md">
    <div class="text-center mb-8">
        <i class="fas fa-tools text-4xl text-blue-600 mb-4"></i>
        <h1 class="text-3xl font-bold text-gray-800">Admin Login</h1>
        <p class="text-gray-600 mt-2">Computer Repair Management System</p>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <form action="/admin/login" method="POST" class="space-y-6">
        <?= csrf_field() ?>

        <div>
            <label class="block text-gray-700 text-sm font-medium mb-2">
                <i class="fas fa-user mr-2"></i>Username
            </label>
            <input type="text" name="username"
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                   placeholder="Enter your username"
                   value="<?= old('username') ?>" required>
            <?php if (isset($errors['username'])): ?>
                <p class="text-red-500 text-sm mt-1"><?= $errors['username'] ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label class="block text-gray-700 text-sm font-medium mb-2">
                <i class="fas fa-lock mr-2"></i>Password
            </label>
            <input type="password" name="password"
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                   placeholder="Enter your password" required>
            <?php if (isset($errors['password'])): ?>
                <p class="text-red-500 text-sm mt-1"><?= $errors['password'] ?></p>
            <?php endif; ?>
        </div>

        <button type="submit"
                class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition-colors font-medium">
            <i class="fas fa-sign-in-alt mr-2"></i>Login
        </button>
    </form>

    <div class="mt-8 text-center">
        <a href="/" class="text-blue-600 hover:text-blue-800 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Back to Website
        </a>
    </div>
</div>
</body>
</html>
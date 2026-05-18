<?php require_once APP_ROOT . '/views/inc/header.php'; ?>

<div class="max-w-md mx-auto bg-white p-8 rounded shadow-md mt-10">
    <h2 class="text-2xl font-bold mb-6 text-center">Login</h2>
    <?php flash('register_success'); ?><?php flash('login_error'); ?>
    <form action="<?php echo URL_ROOT; ?>/login" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
        <div class="mb-4">
            <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email Address</label>
            <input type="email" name="email" id="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline <?php echo (!empty($data['email_err'])) ? 'border-red-500' : ''; ?>" value="<?php echo $data['email']; ?>" required>
            <span class="text-red-500 text-xs italic"><?php echo $data['email_err']; ?></span>
        </div>
        <div class="mb-6">
            <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password</label>
            <input type="password" name="password" id="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline <?php echo (!empty($data['password_err'])) ? 'border-red-500' : ''; ?>" required>
            <span class="text-red-500 text-xs italic"><?php echo $data['password_err']; ?></span>
        </div>
        <div class="flex items-center justify-between">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Sign In
            </button>
            <a class="inline-block align-baseline font-bold text-sm text-indigo-600 hover:text-indigo-800" href="#">
                Forgot Password?
            </a>
        </div>
    </form>
    <p class="text-center text-gray-600 text-xs mt-4">
        Don't have an account? <a href="<?php echo URL_ROOT; ?>/register" class="text-indigo-600">Register</a>
    </p>
</div>

<?php require_once APP_ROOT . '/views/inc/footer.php'; ?>
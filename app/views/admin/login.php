<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login -
        <?php echo APP_NAME; ?>
    </title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-900 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md bg-white rounded-lg shadow-2xl overflow-hidden">
        <div class="bg-indigo-600 p-8 text-center">
            <h2 class="text-3xl font-extrabold text-white tracking-wider">
                Admin Panel
            </h2>
            <p class="text-indigo-200 mt-2">Restricted Access</p>
        </div>
        <div class="p-8">
            <form action="<?php echo URL_ROOT; ?>/admin/authenticate" method="POST">
                <div class="mb-6">
                    <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email Address</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input type="email" name="email"
                            class="w-full pl-10 pr-3 py-2 border rounded-lg focus:outline-none focus:border-indigo-500 <?php echo (!empty($data['email_err'])) ? 'border-red-500' : 'border-gray-300'; ?>"
                            value="<?php echo $data['email']; ?>" placeholder="admin@example.com">
                    </div>
                    <span class="text-red-500 text-xs mt-1">
                        <?php echo $data['email_err']; ?>
                    </span>
                </div>
                <div class="mb-6">
                    <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" name="password"
                            class="w-full pl-10 pr-3 py-2 border rounded-lg focus:outline-none focus:border-indigo-500 <?php echo (!empty($data['password_err'])) ? 'border-red-500' : 'border-gray-300'; ?>"
                            value="<?php echo $data['password']; ?>" placeholder="********">
                    </div>
                    <span class="text-red-500 text-xs mt-1">
                        <?php echo $data['password_err']; ?>
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full transition duration-300">
                        Login to Dashboard
                    </button>
                </div>
            </form>
            <div class="mt-6 text-center text-xs text-gray-400">
                &copy;
                <?php echo date('Y'); ?>
                <?php echo APP_NAME; ?>. All rights reserved.
            </div>
        </div>
    </div>
</body>

</html>
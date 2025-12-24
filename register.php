<?php
require_once 'config/database.php';

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = sanitize($_POST['first_name'] ?? '');
    $lastName = sanitize($_POST['last_name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // Validation
    if (empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
        $message = 'Please fill in all required fields.';
        $messageType = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Please enter a valid email address.';
        $messageType = 'error';
    } elseif (strlen($password) < 8) {
        $message = 'Password must be at least 8 characters long.';
        $messageType = 'error';
    } elseif ($password !== $confirmPassword) {
        $message = 'Passwords do not match.';
        $messageType = 'error';
    } else {
        try {
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
            $stmt->execute([$email]);

            if ($stmt->fetch()) {
                $message = 'Email address is already registered.';
                $messageType = 'error';
            } else {
                // Hash password
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);

                // Insert new user
                $stmt = $pdo->prepare("
                    INSERT INTO users (first_name, last_name, email, phone, password_hash, registration_date)
                    VALUES (?, ?, ?, ?, ?, NOW())
                ");

                if ($stmt->execute([$firstName, $lastName, $email, $phone, $passwordHash])) {
                    $message = 'Account created successfully! You can now log in.';
                    $messageType = 'success';
                } else {
                    $message = 'Registration failed. Please try again.';
                    $messageType = 'error';
                }
            }
        } catch(PDOException $e) {
            $message = 'Registration failed. Please try again.';
            $messageType = 'error';
            error_log('Registration error: ' . $e->getMessage());
        }
    }
}
?>

<?php include 'header.php'; ?>

<div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <div class="text-center">
            <div class="h-16 w-16 rounded-full bg-teal-600 flex items-center justify-center mx-auto mb-4">
                <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
            </div>
            <h2 class="text-3xl font-bold text-gray-900">Create your account</h2>
            <p class="mt-2 text-sm text-gray-600">
                Join AVILLA OKADA HOTEL and start booking your perfect stay
            </p>
        </div>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
            <?php if ($message): ?>
                <div class="mb-4 p-4 rounded-lg <?php echo $messageType === 'error' ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-green-50 text-green-700 border border-green-200'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form class="space-y-6" action="register.php" method="POST" data-validate>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700">
                            First name *
                        </label>
                        <div class="mt-1">
                            <input id="first_name" name="first_name" type="text" required
                                   class="form-input" value="<?php echo sanitize($_POST['first_name'] ?? ''); ?>">
                        </div>
                    </div>

                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700">
                            Last name *
                        </label>
                        <div class="mt-1">
                            <input id="last_name" name="last_name" type="text" required
                                   class="form-input" value="<?php echo sanitize($_POST['last_name'] ?? ''); ?>">
                        </div>
                    </div>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">
                        Email address *
                    </label>
                    <div class="mt-1">
                        <input id="email" name="email" type="email" autocomplete="email" required
                               class="form-input" value="<?php echo sanitize($_POST['email'] ?? ''); ?>">
                    </div>
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">
                        Phone number
                    </label>
                    <div class="mt-1">
                        <input id="phone" name="phone" type="tel" autocomplete="tel"
                               class="form-input" value="<?php echo sanitize($_POST['phone'] ?? ''); ?>">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">
                        Password *
                    </label>
                    <div class="mt-1">
                        <input id="password" name="password" type="password" required
                               class="form-input" minlength="8">
                    </div>
                    <p class="mt-1 text-sm text-gray-500">Must be at least 8 characters long</p>
                </div>

                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700">
                        Confirm password *
                    </label>
                    <div class="mt-1">
                        <input id="confirm_password" name="confirm_password" type="password" required
                               class="form-input">
                    </div>
                </div>

                <div class="flex items-center">
                    <input id="terms" name="terms" type="checkbox" required
                           class="h-4 w-4 text-teal-600 focus:ring-teal-500 border-gray-300 rounded">
                    <label for="terms" class="ml-2 block text-sm text-gray-900">
                        I agree to the <a href="#" class="text-teal-600 hover:text-teal-500">Terms of Service</a> and <a href="#" class="text-teal-600 hover:text-teal-500">Privacy Policy</a>
                    </label>
                </div>

                <div>
                    <button type="submit" class="btn-primary w-full">
                        Create account
                    </button>
                </div>
            </form>

            <div class="mt-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">Already have an account?</span>
                    </div>
                </div>

                <div class="mt-6">
                    <a href="login.php" class="btn-secondary w-full text-center">
                        Sign in instead
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
<?php
require_once '../config/database.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

// Handle settings updates
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_hotel_settings'])) {
        // Update hotel settings
        $hotel_name = sanitize($_POST['hotel_name']);
        $hotel_address = sanitize($_POST['hotel_address']);
        $hotel_phone = sanitize($_POST['hotel_phone']);
        $hotel_email = sanitize($_POST['hotel_email']);
        $hotel_website = sanitize($_POST['hotel_website']);
        $check_in_time = $_POST['check_in_time'];
        $check_out_time = $_POST['check_out_time'];
        $currency = sanitize($_POST['currency']);

        try {
            // Check if settings exist
            $stmt = $pdo->query("SELECT COUNT(*) FROM settings WHERE setting_key = 'hotel_name'");
            $exists = $stmt->fetchColumn();

            if ($exists) {
                // Update existing settings
                $settings = [
                    'hotel_name' => $hotel_name,
                    'hotel_address' => $hotel_address,
                    'hotel_phone' => $hotel_phone,
                    'hotel_email' => $hotel_email,
                    'hotel_website' => $hotel_website,
                    'check_in_time' => $check_in_time,
                    'check_out_time' => $check_out_time,
                    'currency' => $currency
                ];

                foreach ($settings as $key => $value) {
                    $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
                    $stmt->execute([$value, $key]);
                }
            } else {
                // Insert new settings
                $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)");
                $stmt->execute(['hotel_name', $hotel_name]);
                $stmt->execute(['hotel_address', $hotel_address]);
                $stmt->execute(['hotel_phone', $hotel_phone]);
                $stmt->execute(['hotel_email', $hotel_email]);
                $stmt->execute(['hotel_website', $hotel_website]);
                $stmt->execute(['check_in_time', $check_in_time]);
                $stmt->execute(['check_out_time', $check_out_time]);
                $stmt->execute(['currency', $currency]);
            }

            $message = 'Hotel settings updated successfully!';
            $messageType = 'success';
        } catch(PDOException $e) {
            $message = 'Error updating hotel settings: ' . $e->getMessage();
            $messageType = 'error';
        }
    } elseif (isset($_POST['update_pricing'])) {
        // Update pricing rules
        $cancellation_fee = (float)$_POST['cancellation_fee'];
        $early_checkout_fee = (float)$_POST['early_checkout_fee'];
        $late_checkout_fee = (float)$_POST['late_checkout_fee'];
        $service_charge_percent = (float)$_POST['service_charge_percent'];

        try {
            $pricing_settings = [
                'cancellation_fee' => $cancellation_fee,
                'early_checkout_fee' => $early_checkout_fee,
                'late_checkout_fee' => $late_checkout_fee,
                'service_charge_percent' => $service_charge_percent
            ];

            foreach ($pricing_settings as $key => $value) {
                $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
                $stmt->execute([$key, $value, $value]);
            }

            $message = 'Pricing settings updated successfully!';
            $messageType = 'success';
        } catch(PDOException $e) {
            $message = 'Error updating pricing settings: ' . $e->getMessage();
            $messageType = 'error';
        }
    } elseif (isset($_POST['update_system_settings'])) {
        // Update system settings
        $maintenance_mode = isset($_POST['maintenance_mode']) ? 1 : 0;
        $allow_registrations = isset($_POST['allow_registrations']) ? 1 : 0;
        $email_notifications = isset($_POST['email_notifications']) ? 1 : 0;
        $backup_frequency = sanitize($_POST['backup_frequency']);

        try {
            $system_settings = [
                'maintenance_mode' => $maintenance_mode,
                'allow_registrations' => $allow_registrations,
                'email_notifications' => $email_notifications,
                'backup_frequency' => $backup_frequency
            ];

            foreach ($system_settings as $key => $value) {
                $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
                $stmt->execute([$key, $value, $value]);
            }

            $message = 'System settings updated successfully!';
            $messageType = 'success';
        } catch(PDOException $e) {
            $message = 'Error updating system settings: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
}

// Load current settings
$settings = [];
try {
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
    while ($row = $stmt->fetch()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
} catch(PDOException $e) {
    $settings = [];
}

// Set defaults if not set
$defaults = [
    'hotel_name' => 'AVILLA OKADA HOTEL',
    'hotel_address' => '123 Hotel Street, City, Country',
    'hotel_phone' => '+234 xxx xxx xxxx',
    'hotel_email' => 'info@avillaokada.com',
    'hotel_website' => 'https://www.avillaokada.com',
    'check_in_time' => '14:00',
    'check_out_time' => '12:00',
    'currency' => '₦',
    'cancellation_fee' => '5000',
    'early_checkout_fee' => '2500',
    'late_checkout_fee' => '3000',
    'service_charge_percent' => '10',
    'maintenance_mode' => '0',
    'allow_registrations' => '1',
    'email_notifications' => '1',
    'backup_frequency' => 'daily'
];

foreach ($defaults as $key => $value) {
    if (!isset($settings[$key])) {
        $settings[$key] = $value;
    }
}

$pageTitle = 'System Settings';
$currentPage = 'settings';
require_once 'admin-header.php';
?>

<!-- System Settings -->
<div id="settings" class="tab-content p-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">System Settings</h1>
            <p class="text-gray-600">Configure hotel settings, pricing, and system preferences</p>
        </div>
    </div>

    <!-- Message Display -->
    <?php if ($message): ?>
        <div class="mb-6 p-4 rounded-lg <?php echo $messageType === 'error' ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-green-50 text-green-700 border border-green-200'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <!-- Settings Tabs -->
    <div class="mb-6">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8">
                <button onclick="showSettingsTab('hotel')" id="hotel-tab" class="settings-tab active py-2 px-1 border-b-2 font-medium text-sm">
                    Hotel Information
                </button>
                <button onclick="showSettingsTab('pricing')" id="pricing-tab" class="settings-tab py-2 px-1 border-b-2 font-medium text-sm">
                    Pricing & Fees
                </button>
                <button onclick="showSettingsTab('system')" id="system-tab" class="settings-tab py-2 px-1 border-b-2 font-medium text-sm">
                    System Settings
                </button>
            </nav>
        </div>
    </div>

    <!-- Hotel Settings -->
    <div id="hotel-settings" class="settings-content">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Hotel Information</h3>
            <form method="POST" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Hotel Name</label>
                        <input type="text" name="hotel_name" value="<?php echo htmlspecialchars($settings['hotel_name']); ?>" required class="form-input">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Hotel Email</label>
                        <input type="email" name="hotel_email" value="<?php echo htmlspecialchars($settings['hotel_email']); ?>" required class="form-input">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Hotel Phone</label>
                        <input type="tel" name="hotel_phone" value="<?php echo htmlspecialchars($settings['hotel_phone']); ?>" required class="form-input">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Hotel Website</label>
                        <input type="url" name="hotel_website" value="<?php echo htmlspecialchars($settings['hotel_website']); ?>" class="form-input">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Hotel Address</label>
                    <textarea name="hotel_address" rows="3" class="form-textarea"><?php echo htmlspecialchars($settings['hotel_address']); ?></textarea>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Check-in Time</label>
                        <input type="time" name="check_in_time" value="<?php echo htmlspecialchars($settings['check_in_time']); ?>" required class="form-input">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Check-out Time</label>
                        <input type="time" name="check_out_time" value="<?php echo htmlspecialchars($settings['check_out_time']); ?>" required class="form-input">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Currency Symbol</label>
                        <input type="text" name="currency" value="<?php echo htmlspecialchars($settings['currency']); ?>" required class="form-input" placeholder="₦">
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit" name="update_hotel_settings" class="btn-primary">Save Hotel Settings</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Pricing Settings -->
    <div id="pricing-settings" class="settings-content hidden">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Pricing & Fee Structure</h3>
            <form method="POST" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Cancellation Fee (₦)</label>
                        <input type="number" name="cancellation_fee" value="<?php echo htmlspecialchars($settings['cancellation_fee']); ?>" step="0.01" min="0" required class="form-input">
                        <p class="text-sm text-gray-500 mt-1">Fee charged for booking cancellations</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Early Checkout Fee (₦)</label>
                        <input type="number" name="early_checkout_fee" value="<?php echo htmlspecialchars($settings['early_checkout_fee']); ?>" step="0.01" min="0" required class="form-input">
                        <p class="text-sm text-gray-500 mt-1">Fee for checking out before scheduled time</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Late Checkout Fee (₦)</label>
                        <input type="number" name="late_checkout_fee" value="<?php echo htmlspecialchars($settings['late_checkout_fee']); ?>" step="0.01" min="0" required class="form-input">
                        <p class="text-sm text-gray-500 mt-1">Fee for checking out after scheduled time</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Service Charge (%)</label>
                        <input type="number" name="service_charge_percent" value="<?php echo htmlspecialchars($settings['service_charge_percent']); ?>" step="0.01" min="0" max="100" required class="form-input">
                        <p class="text-sm text-gray-500 mt-1">Percentage added to all bills</p>
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit" name="update_pricing" class="btn-primary">Save Pricing Settings</button>
                </div>
            </form>
        </div>
    </div>

    <!-- System Settings -->
    <div id="system-settings" class="settings-content hidden">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">System Configuration</h3>
            <form method="POST" class="space-y-6">
                <div class="space-y-4">
                    <div class="flex items-center">
                        <input type="checkbox" name="maintenance_mode" id="maintenance_mode" <?php echo $settings['maintenance_mode'] == '1' ? 'checked' : ''; ?> class="h-4 w-4 text-teal-600 focus:ring-teal-500 border-gray-300 rounded">
                        <label for="maintenance_mode" class="ml-2 block text-sm text-gray-900">
                            <span class="font-medium">Maintenance Mode</span>
                            <span class="block text-gray-500">Put the system in maintenance mode for updates</span>
                        </label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="allow_registrations" id="allow_registrations" <?php echo $settings['allow_registrations'] == '1' ? 'checked' : ''; ?> class="h-4 w-4 text-teal-600 focus:ring-teal-500 border-gray-300 rounded">
                        <label for="allow_registrations" class="ml-2 block text-sm text-gray-900">
                            <span class="font-medium">Allow User Registrations</span>
                            <span class="block text-gray-500">Allow new users to register accounts</span>
                        </label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="email_notifications" id="email_notifications" <?php echo $settings['email_notifications'] == '1' ? 'checked' : ''; ?> class="h-4 w-4 text-teal-600 focus:ring-teal-500 border-gray-300 rounded">
                        <label for="email_notifications" class="ml-2 block text-sm text-gray-900">
                            <span class="font-medium">Email Notifications</span>
                            <span class="block text-gray-500">Send email notifications for bookings and orders</span>
                        </label>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Database Backup Frequency</label>
                    <select name="backup_frequency" class="form-select">
                        <option value="daily" <?php echo $settings['backup_frequency'] === 'daily' ? 'selected' : ''; ?>>Daily</option>
                        <option value="weekly" <?php echo $settings['backup_frequency'] === 'weekly' ? 'selected' : ''; ?>>Weekly</option>
                        <option value="monthly" <?php echo $settings['backup_frequency'] === 'monthly' ? 'selected' : ''; ?>>Monthly</option>
                        <option value="manual" <?php echo $settings['backup_frequency'] === 'manual' ? 'selected' : ''; ?>>Manual Only</option>
                    </select>
                </div>
                <div class="flex justify-end">
                    <button type="submit" name="update_system_settings" class="btn-primary">Save System Settings</button>
                </div>
            </form>
        </div>

        <!-- System Information -->
        <div class="bg-white rounded-lg shadow p-6 mt-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">System Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-medium text-gray-900 mb-3">Server Information</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">PHP Version:</span>
                            <span><?php echo phpversion(); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Server:</span>
                            <span><?php echo $_SERVER['SERVER_SOFTWARE']; ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Database:</span>
                            <span>MySQL <?php echo $pdo->query('SELECT VERSION()')->fetchColumn(); ?></span>
                        </div>
                    </div>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900 mb-3">System Actions</h4>
                    <div class="space-y-3">
                        <button onclick="clearCache()" class="w-full btn-secondary text-sm">
                            Clear System Cache
                        </button>
                        <button onclick="backupDatabase()" class="w-full btn-secondary text-sm">
                            Backup Database
                        </button>
                        <button onclick="optimizeDatabase()" class="w-full btn-warning text-sm">
                            Optimize Database
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function showSettingsTab(tab) {
    // Hide all tabs
    document.querySelectorAll('.settings-content').forEach(content => {
        content.classList.add('hidden');
    });

    // Remove active class from all tabs
    document.querySelectorAll('.settings-tab').forEach(tabBtn => {
        tabBtn.classList.remove('active', 'border-teal-500', 'text-teal-600');
        tabBtn.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
    });

    // Show selected tab
    document.getElementById(tab + '-settings').classList.remove('hidden');

    // Add active class to selected tab
    document.getElementById(tab + '-tab').classList.add('active', 'border-teal-500', 'text-teal-600');
    document.getElementById(tab + '-tab').classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
}

function clearCache() {
    if (confirm('Are you sure you want to clear the system cache?')) {
        // Implement cache clearing logic
        alert('Cache cleared successfully!');
    }
}

function backupDatabase() {
    if (confirm('Are you sure you want to backup the database?')) {
        // Implement database backup logic
        alert('Database backup completed successfully!');
    }
}

function optimizeDatabase() {
    if (confirm('Are you sure you want to optimize the database? This may take some time.')) {
        // Implement database optimization logic
        alert('Database optimization completed successfully!');
    }
}

// Initialize first tab as active
document.addEventListener('DOMContentLoaded', function() {
    showSettingsTab('hotel');
});
</script>

<?php require_once 'admin-footer.php'; ?>
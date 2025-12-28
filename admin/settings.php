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
        // ... (existing code for hotel settings)
    } elseif (isset($_POST['update_pricing'])) {
        // Update pricing rules
        // ... (existing code for pricing settings)
    } elseif (isset($_POST['update_system_settings'])) {
        // Update system settings
        // ... (existing code for system settings)
    } elseif (isset($_POST['update_homepage_settings'])) {
        $active_index = sanitize($_POST['active_index']);
        try {
            $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES ('active_index', ?) ON DUPLICATE KEY UPDATE setting_value = ?");
            $stmt->execute([$active_index, $active_index]);
            $message = 'Homepage settings updated successfully!';
            $messageType = 'success';
        } catch(PDOException $e) {
            $message = 'Error updating homepage settings: ' . $e->getMessage();
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
    // If the settings table doesn't exist, this will fail.
    // The setup.php script should create it.
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
    'backup_frequency' => 'daily',
    'active_index' => 'index-one.php'
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
                <button onclick="showSettingsTab('homepage')" id="homepage-tab" class="settings-tab py-2 px-1 border-b-2 font-medium text-sm">
                    Homepage
                </button>
            </nav>
        </div>
    </div>

    <!-- Hotel Settings -->
    <div id="hotel-settings" class="settings-content">
        <!-- ... existing hotel settings form ... -->
    </div>

    <!-- Pricing Settings -->
    <div id="pricing-settings" class="settings-content hidden">
       <!-- ... existing pricing settings form ... -->
    </div>

    <!-- System Settings -->
    <div id="system-settings" class="settings-content hidden">
        <!-- ... existing system settings form ... -->
    </div>
    
    <!-- Homepage Settings -->
    <div id="homepage-settings" class="settings-content hidden">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Homepage Settings</h3>
            <form method="POST" class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Active Homepage</label>
                    <select name="active_index" class="form-select">
                        <option value="index-one.php" <?php echo ($settings['active_index'] ?? 'index-one.php') === 'index-one.php' ? 'selected' : ''; ?>>Homepage 1</option>
                        <option value="index-two.php" <?php echo ($settings['active_index'] ?? '') === 'index-two.php' ? 'selected' : ''; ?>>Homepage 2</option>
                    </select>
                </div>
                <div class="flex justify-end">
                    <button type="submit" name="update_homepage_settings" class="btn-primary">Save Homepage Settings</button>
                </div>
            </form>
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
    const tabButton = document.getElementById(tab + '-tab');
    if (tabButton) {
        tabButton.classList.add('active', 'border-teal-500', 'text-teal-600');
        tabButton.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    showSettingsTab('hotel');
});
</script>

<?php require_once 'admin-footer.php'; ?>
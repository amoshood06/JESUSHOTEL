<?php
require_once '../config/database.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

// Handle user operations
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_user'])) {
        // Update user
        $user_id = (int)$_POST['user_id'];
        $full_name = sanitize($_POST['full_name']);
        $email = sanitize($_POST['email']);
        $phone = sanitize($_POST['phone']);
        $room_number = sanitize($_POST['room_number']);
        $user_type = sanitize($_POST['user_type']);
        $is_active = isset($_POST['is_active']) ? 1 : 0;

        try {
            $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, phone = ?, room_number = ?, user_type = ?, is_active = ? WHERE user_id = ?");
            $stmt->execute([$full_name, $email, $phone, $room_number, $user_type, $is_active, $user_id]);
            $message = 'User updated successfully!';
            $messageType = 'success';
        } catch(PDOException $e) {
            $message = 'Error updating user: ' . $e->getMessage();
            $messageType = 'error';
        }
    } elseif (isset($_POST['delete_user'])) {
        // Delete user
        $user_id = (int)$_POST['user_id'];

        try {
            $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
            $stmt->execute([$user_id]);
            $message = 'User deleted successfully!';
            $messageType = 'success';
        } catch(PDOException $e) {
            $message = 'Error deleting user: ' . $e->getMessage();
            $messageType = 'error';
        }
    } elseif (isset($_POST['reset_password'])) {
        // Reset user password
        $user_id = (int)$_POST['user_id'];
        $new_password = password_hash('password123', PASSWORD_DEFAULT); // Default reset password

        try {
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE user_id = ?");
            $stmt->execute([$new_password, $user_id]);
            $message = 'Password reset successfully! New password: password123';
            $messageType = 'success';
        } catch(PDOException $e) {
            $message = 'Error resetting password: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
}

// Get filter parameters
$user_type_filter = $_GET['user_type'] ?? '';
$status_filter = $_GET['status'] ?? '';
$search_filter = $_GET['search'] ?? '';

// Build query with filters
$query = "SELECT u.*, 
          COUNT(DISTINCT b.booking_id) as total_bookings,
          COUNT(DISTINCT CASE WHEN b.check_out_date >= CURDATE() THEN b.booking_id END) as active_bookings,
          SUM(p.amount) as total_spent
          FROM users u
          LEFT JOIN bookings b ON u.user_id = b.user_id
          LEFT JOIN payments p ON u.user_id = p.user_id AND p.status = 'completed'";

$conditions = [];
$params = [];

if ($user_type_filter && $user_type_filter !== 'all') {
    $conditions[] = "u.user_type = ?";
    $params[] = $user_type_filter;
}

if ($status_filter !== '') {
    $conditions[] = "u.is_active = ?";
    $params[] = (int)$status_filter;
}

if ($search_filter) {
    $conditions[] = "(u.full_name LIKE ? OR u.email LIKE ? OR u.phone LIKE ? OR u.room_number LIKE ?)";
    $params[] = "%$search_filter%";
    $params[] = "%$search_filter%";
    $params[] = "%$search_filter%";
    $params[] = "%$search_filter%";
}

if (!empty($conditions)) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}

$query .= " GROUP BY u.user_id ORDER BY u.created_at DESC";

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $users = $stmt->fetchAll();
} catch(PDOException $e) {
    $users = [];
    error_log('Error fetching users: ' . $e->getMessage());
}

// Get user statistics
try {
    $stmt = $pdo->query("SELECT
        COUNT(*) as total,
        SUM(CASE WHEN user_type = 'customer' THEN 1 ELSE 0 END) as customers,
        SUM(CASE WHEN user_type = 'staff' THEN 1 ELSE 0 END) as staff,
        SUM(CASE WHEN user_type = 'admin' THEN 1 ELSE 0 END) as admins,
        SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active,
        SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) as inactive
        FROM users");
    $stats = $stmt->fetch();
} catch(PDOException $e) {
    $stats = ['total' => 0, 'customers' => 0, 'staff' => 0, 'admins' => 0, 'active' => 0, 'inactive' => 0];
}

$pageTitle = 'Users Management';
$currentPage = 'users';
require_once 'admin-header.php';
?>

<!-- Users Management -->
<div id="users" class="tab-content p-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Users Management</h1>
            <p class="text-gray-600">Manage user accounts, profiles, and customer information</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <div class="text-2xl font-bold text-gray-900"><?php echo $stats['total']; ?></div>
            <div class="text-sm text-gray-600">Total Users</div>
        </div>
        <div class="bg-blue-50 rounded-lg shadow p-4 text-center border-l-4 border-blue-400">
            <div class="text-2xl font-bold text-blue-700"><?php echo $stats['customers']; ?></div>
            <div class="text-sm text-blue-600">Customers</div>
        </div>
        <div class="bg-green-50 rounded-lg shadow p-4 text-center border-l-4 border-green-400">
            <div class="text-2xl font-bold text-green-700"><?php echo $stats['staff']; ?></div>
            <div class="text-sm text-green-600">Staff</div>
        </div>
        <div class="bg-purple-50 rounded-lg shadow p-4 text-center border-l-4 border-purple-400">
            <div class="text-2xl font-bold text-purple-700"><?php echo $stats['admins']; ?></div>
            <div class="text-sm text-purple-600">Admins</div>
        </div>
        <div class="bg-teal-50 rounded-lg shadow p-4 text-center border-l-4 border-teal-400">
            <div class="text-2xl font-bold text-teal-700"><?php echo $stats['active']; ?></div>
            <div class="text-sm text-teal-600">Active</div>
        </div>
        <div class="bg-red-50 rounded-lg shadow p-4 text-center border-l-4 border-red-400">
            <div class="text-2xl font-bold text-red-700"><?php echo $stats['inactive']; ?></div>
            <div class="text-sm text-red-600">Inactive</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex flex-wrap gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">User Type</label>
                <select id="userTypeFilter" class="form-select">
                    <option value="all" <?php echo $user_type_filter === '' || $user_type_filter === 'all' ? 'selected' : ''; ?>>All Users</option>
                    <option value="customer" <?php echo $user_type_filter === 'customer' ? 'selected' : ''; ?>>Customers</option>
                    <option value="staff" <?php echo $user_type_filter === 'staff' ? 'selected' : ''; ?>>Staff</option>
                    <option value="admin" <?php echo $user_type_filter === 'admin' ? 'selected' : ''; ?>>Admins</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="statusFilter" class="form-select">
                    <option value="" <?php echo $status_filter === '' ? 'selected' : ''; ?>>All Users</option>
                    <option value="1" <?php echo $status_filter === '1' ? 'selected' : ''; ?>>Active</option>
                    <option value="0" <?php echo $status_filter === '0' ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" id="searchFilter" class="form-input" placeholder="Name, email, phone, or room" value="<?php echo htmlspecialchars($search_filter); ?>">
            </div>
        </div>
    </div>

    <!-- Message Display -->
    <?php if ($message): ?>
        <div class="mb-6 p-4 rounded-lg <?php echo $messageType === 'error' ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-green-50 text-green-700 border border-green-200'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <!-- Users Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Users (<?php echo count($users); ?>)</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>User Details</th>
                        <th>Contact</th>
                        <th>Room</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Activity</th>
                        <th>Registration</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-8 text-gray-500">
                                <svg class="h-12 w-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                </svg>
                                No users found. Users will appear here when they register.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td>
                                    <div>
                                        <div class="font-medium"><?php echo htmlspecialchars($user['full_name']); ?></div>
                                        <div class="text-sm text-gray-500"><?php echo htmlspecialchars($user['email']); ?></div>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-sm">
                                        <div><?php echo htmlspecialchars($user['phone']); ?></div>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($user['room_number'] ?: 'N/A'); ?></td>
                                <td>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                        <?php echo $user['user_type'] === 'admin' ? 'bg-purple-100 text-purple-800' : 
                                                  ($user['user_type'] === 'staff' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'); ?>">
                                        <?php echo ucfirst($user['user_type']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge status-<?php echo $user['is_active'] ? 'active' : 'inactive'; ?>">
                                        <?php echo $user['is_active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="text-sm">
                                        <div><?php echo $user['total_bookings']; ?> bookings</div>
                                        <div class="text-gray-500"><?php echo $user['active_bookings']; ?> active</div>
                                        <?php if ($user['total_spent'] > 0): ?>
                                            <div class="text-green-600 font-medium"><?php echo formatCurrency($user['total_spent']); ?> spent</div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-sm">
                                        <div><?php echo date('M d, Y', strtotime($user['created_at'])); ?></div>
                                        <div class="text-gray-500"><?php echo date('H:i', strtotime($user['created_at'])); ?></div>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex space-x-2">
                                        <button onclick="editUser(<?php echo htmlspecialchars(json_encode($user)); ?>)" class="btn-secondary text-xs px-3 py-1">
                                            Edit
                                        </button>
                                        <button onclick="resetPassword(<?php echo $user['user_id']; ?>, '<?php echo htmlspecialchars($user['full_name']); ?>')" class="btn-warning text-xs px-3 py-1">
                                            Reset Password
                                        </button>
                                        <?php if ($user['user_id'] !== $_SESSION['user_id']): // Don't allow deleting self ?>
                                            <button onclick="deleteUser(<?php echo $user['user_id']; ?>, '<?php echo htmlspecialchars($user['full_name']); ?>')" class="btn-danger text-xs px-3 py-1">
                                                Delete
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div id="editUserModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Edit User</h3>
                <button onclick="closeEditUserModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form method="POST" class="space-y-4">
                <input type="hidden" name="user_id" id="edit_user_id">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Full Name</label>
                    <input type="text" name="full_name" id="edit_full_name" required class="form-input">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" id="edit_email" required class="form-input">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Phone</label>
                        <input type="tel" name="phone" id="edit_phone" required class="form-input">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Room Number</label>
                        <input type="text" name="room_number" id="edit_room_number" class="form-input" placeholder="101, 202, etc.">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">User Type</label>
                        <select name="user_type" id="edit_user_type" required class="form-select">
                            <option value="customer">Customer</option>
                            <option value="staff">Staff</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="edit_is_active" class="h-4 w-4 text-teal-600 focus:ring-teal-500 border-gray-300 rounded">
                    <label for="edit_is_active" class="ml-2 block text-sm text-gray-900">Active User</label>
                </div>
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeEditUserModal()" class="btn-secondary">Cancel</button>
                    <button type="submit" name="update_user" class="btn-primary">Update User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editUser(user) {
    document.getElementById('edit_user_id').value = user.user_id;
    document.getElementById('edit_full_name').value = user.full_name;
    document.getElementById('edit_email').value = user.email;
    document.getElementById('edit_phone').value = user.phone;
    document.getElementById('edit_room_number').value = user.room_number || '';
    document.getElementById('edit_user_type').value = user.user_type;
    document.getElementById('edit_is_active').checked = user.is_active == 1;
    document.getElementById('editUserModal').classList.remove('hidden');
}

function closeEditUserModal() {
    document.getElementById('editUserModal').classList.add('hidden');
}

function resetPassword(id, name) {
    if (confirm(`Are you sure you want to reset the password for "${name}"? The new password will be "password123".`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="user_id" value="${id}">
            <input type="hidden" name="reset_password" value="1">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

function deleteUser(id, name) {
    if (confirm(`Are you sure you want to delete "${name}"? This action cannot be undone and will remove all associated data.`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="user_id" value="${id}">
            <input type="hidden" name="delete_user" value="1">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Filter functionality
document.getElementById('userTypeFilter').addEventListener('change', function() {
    const userType = this.value;
    const status = document.getElementById('statusFilter').value;
    const search = document.getElementById('searchFilter').value;
    const url = new URL(window.location);
    if (userType === 'all') {
        url.searchParams.delete('user_type');
    } else {
        url.searchParams.set('user_type', userType);
    }
    if (status !== '') {
        url.searchParams.set('status', status);
    } else {
        url.searchParams.delete('status');
    }
    if (search) url.searchParams.set('search', search);
    else url.searchParams.delete('search');
    window.location.href = url.toString();
});

document.getElementById('statusFilter').addEventListener('change', function() {
    const status = this.value;
    const userType = document.getElementById('userTypeFilter').value;
    const search = document.getElementById('searchFilter').value;
    const url = new URL(window.location);
    if (userType !== 'all') {
        url.searchParams.set('user_type', userType);
    } else {
        url.searchParams.delete('user_type');
    }
    if (status !== '') {
        url.searchParams.set('status', status);
    } else {
        url.searchParams.delete('status');
    }
    if (search) url.searchParams.set('search', search);
    else url.searchParams.delete('search');
    window.location.href = url.toString();
});

document.getElementById('searchFilter').addEventListener('input', function() {
    const search = this.value;
    const userType = document.getElementById('userTypeFilter').value;
    const status = document.getElementById('statusFilter').value;
    const url = new URL(window.location);
    if (userType !== 'all') {
        url.searchParams.set('user_type', userType);
    } else {
        url.searchParams.delete('user_type');
    }
    if (status !== '') {
        url.searchParams.set('status', status);
    } else {
        url.searchParams.delete('status');
    }
    if (search) url.searchParams.set('search', search);
    else url.searchParams.delete('search');
    window.location.href = url.toString();
});
</script>

<?php require_once 'admin-footer.php'; ?>
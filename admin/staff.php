<?php
require_once '../config/database.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

// Handle staff operations
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_staff'])) {
        // Add new staff member - first create user account, then staff record
        $first_name = sanitize($_POST['first_name']);
        $last_name = sanitize($_POST['last_name']);
        $email = sanitize($_POST['email']);
        $phone = sanitize($_POST['phone']);
        $national_id = sanitize($_POST['national_id']);
        $position = sanitize($_POST['position']);
        $department = sanitize($_POST['department']);
        $salary = (float)$_POST['salary'];
        $hire_date = $_POST['hire_date'];
        $employment_status = sanitize($_POST['employment_status']);
        $emergency_contact_name = sanitize($_POST['emergency_contact_name']);
        $emergency_contact_phone = sanitize($_POST['emergency_contact_phone']);

        try {
            $pdo->beginTransaction();

            // Create user account first
            $password = password_hash('default123', PASSWORD_DEFAULT); // Default password
            $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, phone, password, role, registration_date) VALUES (?, ?, ?, ?, ?, 'staff', NOW())");
            $stmt->execute([$first_name, $last_name, $email, $phone, $password]);
            $user_id = $pdo->lastInsertId();

            // Create staff record
            $stmt = $pdo->prepare("INSERT INTO staff (user_id, national_id, position, department, salary, hire_date, employment_status, emergency_contact_name, emergency_contact_phone) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$user_id, $national_id, $position, $department, $salary, $hire_date, $employment_status, $emergency_contact_name, $emergency_contact_phone]);

            $pdo->commit();
            $message = 'Staff member added successfully! Default password is "default123"';
            $messageType = 'success';
        } catch(PDOException $e) {
            $pdo->rollBack();
            $message = 'Error adding staff member: ' . $e->getMessage();
            $messageType = 'error';
        }
    } elseif (isset($_POST['update_staff'])) {
        // Update staff member
        $staff_id = (int)$_POST['staff_id'];
        $first_name = sanitize($_POST['first_name']);
        $last_name = sanitize($_POST['last_name']);
        $email = sanitize($_POST['email']);
        $phone = sanitize($_POST['phone']);
        $national_id = sanitize($_POST['national_id']);
        $position = sanitize($_POST['position']);
        $department = sanitize($_POST['department']);
        $salary = (float)$_POST['salary'];
        $hire_date = $_POST['hire_date'];
        $employment_status = sanitize($_POST['employment_status']);
        $emergency_contact_name = sanitize($_POST['emergency_contact_name']);
        $emergency_contact_phone = sanitize($_POST['emergency_contact_phone']);

        try {
            $pdo->beginTransaction();

            // Get user_id from staff record
            $stmt = $pdo->prepare("SELECT user_id FROM staff WHERE staff_id = ?");
            $stmt->execute([$staff_id]);
            $user_id = $stmt->fetchColumn();

            // Update user record
            $stmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ? WHERE user_id = ?");
            $stmt->execute([$first_name, $last_name, $email, $phone, $user_id]);

            // Update staff record
            $stmt = $pdo->prepare("UPDATE staff SET national_id = ?, position = ?, department = ?, salary = ?, hire_date = ?, employment_status = ?, emergency_contact_name = ?, emergency_contact_phone = ? WHERE staff_id = ?");
            $stmt->execute([$national_id, $position, $department, $salary, $hire_date, $employment_status, $emergency_contact_name, $emergency_contact_phone, $staff_id]);

            $pdo->commit();
            $message = 'Staff member updated successfully!';
            $messageType = 'success';
        } catch(PDOException $e) {
            $pdo->rollBack();
            $message = 'Error updating staff member: ' . $e->getMessage();
            $messageType = 'error';
        }
    } elseif (isset($_POST['delete_staff'])) {
        // Delete staff member
        $staff_id = (int)$_POST['staff_id'];

        try {
            $stmt = $pdo->prepare("DELETE FROM staff WHERE staff_id = ?");
            $stmt->execute([$staff_id]);
            $message = 'Staff member deleted successfully!';
            $messageType = 'success';
        } catch(PDOException $e) {
            $message = 'Error deleting staff member: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
}

// Get filter parameters
$department_filter = $_GET['department'] ?? '';
$status_filter = $_GET['status'] ?? '';
$search_filter = $_GET['search'] ?? '';

// Build query with filters
$query = "SELECT s.*, CONCAT(u.first_name, ' ', u.last_name) as full_name, u.email, u.phone
          FROM staff s
          JOIN users u ON s.user_id = u.user_id";
$conditions = [];
$params = [];

if ($department_filter && $department_filter !== 'all') {
    $conditions[] = "s.department = ?";
    $params[] = $department_filter;
}

if ($status_filter !== '') {
    $conditions[] = "s.employment_status = ?";
    $params[] = $status_filter === '1' ? 'active' : ($status_filter === '0' ? 'inactive' : 'terminated');
}

if ($search_filter) {
    $conditions[] = "(CONCAT(u.first_name, ' ', u.last_name) LIKE ? OR u.email LIKE ? OR s.position LIKE ?)";
    $params[] = "%$search_filter%";
    $params[] = "%$search_filter%";
    $params[] = "%$search_filter%";
}

if (!empty($conditions)) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}

$query .= " ORDER BY department ASC, full_name ASC";

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $staff_members = $stmt->fetchAll();
} catch(PDOException $e) {
    $staff_members = [];
    error_log('Error fetching staff: ' . $e->getMessage());
}

// Get staff statistics
try {
    $stmt = $pdo->query("SELECT
        COUNT(*) as total,
        SUM(CASE WHEN employment_status = 'active' THEN 1 ELSE 0 END) as active,
        SUM(CASE WHEN employment_status = 'inactive' THEN 1 ELSE 0 END) as inactive,
        SUM(CASE WHEN employment_status = 'terminated' THEN 1 ELSE 0 END) as terminated,
        COUNT(DISTINCT department) as departments,
        AVG(salary) as avg_salary,
        SUM(salary) as total_salary
        FROM staff");
    $stats = $stmt->fetch();
} catch(PDOException $e) {
    $stats = ['total' => 0, 'active' => 0, 'inactive' => 0, 'terminated' => 0, 'departments' => 0, 'avg_salary' => 0, 'total_salary' => 0];
}

$pageTitle = 'Staff Management';
$currentPage = 'staff';
require_once 'admin-header.php';
?>

<!-- Staff Management -->
<div id="staff" class="tab-content p-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Staff Management</h1>
            <p class="text-gray-600">Manage hotel staff members, roles, and schedules</p>
        </div>
        <button onclick="openAddStaffModal()" class="btn-primary">
            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Add Staff Member
        </button>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <div class="text-2xl font-bold text-gray-900"><?php echo $stats['total']; ?></div>
            <div class="text-sm text-gray-600">Total Staff</div>
        </div>
        <div class="bg-green-50 rounded-lg shadow p-4 text-center border-l-4 border-green-400">
            <div class="text-2xl font-bold text-green-700"><?php echo $stats['active']; ?></div>
            <div class="text-sm text-green-600">Active</div>
        </div>
        <div class="bg-yellow-50 rounded-lg shadow p-4 text-center border-l-4 border-yellow-400">
            <div class="text-2xl font-bold text-yellow-700"><?php echo $stats['inactive']; ?></div>
            <div class="text-sm text-yellow-600">Inactive</div>
        </div>
        <div class="bg-red-50 rounded-lg shadow p-4 text-center border-l-4 border-red-400">
            <div class="text-2xl font-bold text-red-700"><?php echo $stats['terminated']; ?></div>
            <div class="text-sm text-red-600">Terminated</div>
        </div>
        <div class="bg-blue-50 rounded-lg shadow p-4 text-center border-l-4 border-blue-400">
            <div class="text-2xl font-bold text-blue-700"><?php echo $stats['departments']; ?></div>
            <div class="text-sm text-blue-600">Departments</div>
        </div>
        <div class="bg-purple-50 rounded-lg shadow p-4 text-center border-l-4 border-purple-400">
            <div class="text-2xl font-bold text-purple-700"><?php echo formatCurrency($stats['avg_salary']); ?></div>
            <div class="text-sm text-purple-600">Avg Salary</div>
        </div>
        <div class="bg-teal-50 rounded-lg shadow p-4 text-center border-l-4 border-teal-400">
            <div class="text-2xl font-bold text-teal-700"><?php echo formatCurrency($stats['total_salary']); ?></div>
            <div class="text-sm text-teal-600">Total Payroll</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex flex-wrap gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                <select id="departmentFilter" class="form-select">
                    <option value="all" <?php echo $department_filter === '' || $department_filter === 'all' ? 'selected' : ''; ?>>All Departments</option>
                    <option value="Management" <?php echo $department_filter === 'Management' ? 'selected' : ''; ?>>Management</option>
                    <option value="Front Desk" <?php echo $department_filter === 'Front Desk' ? 'selected' : ''; ?>>Front Desk</option>
                    <option value="Housekeeping" <?php echo $department_filter === 'Housekeeping' ? 'selected' : ''; ?>>Housekeeping</option>
                    <option value="Food & Beverage" <?php echo $department_filter === 'Food & Beverage' ? 'selected' : ''; ?>>Food & Beverage</option>
                    <option value="Maintenance" <?php echo $department_filter === 'Maintenance' ? 'selected' : ''; ?>>Maintenance</option>
                    <option value="Security" <?php echo $department_filter === 'Security' ? 'selected' : ''; ?>>Security</option>
                    <option value="IT" <?php echo $department_filter === 'IT' ? 'selected' : ''; ?>>IT</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="statusFilter" class="form-select">
                    <option value="" <?php echo $status_filter === '' ? 'selected' : ''; ?>>All Staff</option>
                    <option value="active" <?php echo $status_filter === 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?php echo $status_filter === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                    <option value="terminated" <?php echo $status_filter === 'terminated' ? 'selected' : ''; ?>>Terminated</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" id="searchFilter" class="form-input" placeholder="Name, email, or role" value="<?php echo htmlspecialchars($search_filter); ?>">
            </div>
        </div>
    </div>

    <!-- Message Display -->
    <?php if ($message): ?>
        <div class="mb-6 p-4 rounded-lg <?php echo $messageType === 'error' ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-green-50 text-green-700 border border-green-200'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <!-- Staff Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Staff Members (<?php echo count($staff_members); ?>)</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Contact</th>
                        <th>Position</th>
                        <th>Department</th>
                        <th>Salary</th>
                        <th>National ID</th>
                        <th>Status</th>
                        <th>Hire Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($staff_members)): ?>
                        <tr>
                            <td colspan="9" class="text-center py-8 text-gray-500">
                                <svg class="h-12 w-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                No staff members found. Add your first staff member to get started.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($staff_members as $staff): ?>
                            <tr>
                                <td>
                                    <div class="font-medium"><?php echo htmlspecialchars($staff['full_name']); ?></div>
                                </td>
                                <td>
                                    <div class="text-sm">
                                        <div><?php echo htmlspecialchars($staff['email']); ?></div>
                                        <div class="text-gray-500"><?php echo htmlspecialchars($staff['phone']); ?></div>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($staff['position']); ?></td>
                                <td><?php echo htmlspecialchars($staff['department']); ?></td>
                                <td class="font-medium"><?php echo formatCurrency($staff['salary']); ?></td>
                                <td><?php echo htmlspecialchars($staff['national_id']); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $staff['employment_status']; ?>">
                                        <?php echo ucfirst($staff['employment_status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($staff['hire_date'])); ?></td>
                                <td>
                                    <div class="flex space-x-2">
                                        <button onclick="editStaff(<?php echo htmlspecialchars(json_encode($staff)); ?>)" class="btn-secondary text-xs px-3 py-1">
                                            Edit
                                        </button>
                                        <button onclick="deleteStaff(<?php echo $staff['staff_id']; ?>, '<?php echo htmlspecialchars($staff['full_name']); ?>')" class="btn-danger text-xs px-3 py-1">
                                            Delete
                                        </button>
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

<!-- Add Staff Modal -->
<div id="addStaffModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white max-h-screen overflow-y-auto">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Add Staff Member</h3>
                <button onclick="closeAddStaffModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form method="POST" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">First Name</label>
                        <input type="text" name="first_name" required class="form-input" placeholder="John">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Last Name</label>
                        <input type="text" name="last_name" required class="form-input" placeholder="Doe">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" required class="form-input" placeholder="john@example.com">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Phone</label>
                        <input type="tel" name="phone" required class="form-input" placeholder="+234 xxx xxx xxxx">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">National ID</label>
                    <input type="text" name="national_id" required class="form-input" placeholder="National ID number">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Position</label>
                        <input type="text" name="position" required class="form-input" placeholder="Manager, Receptionist, etc.">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Department</label>
                        <select name="department" required class="form-select">
                            <option value="Management">Management</option>
                            <option value="Front Desk">Front Desk</option>
                            <option value="Housekeeping">Housekeeping</option>
                            <option value="Food & Beverage">Food & Beverage</option>
                            <option value="Maintenance">Maintenance</option>
                            <option value="Security">Security</option>
                            <option value="IT">IT</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Salary (₦)</label>
                        <input type="number" name="salary" required step="0.01" min="0" class="form-input" placeholder="50000.00">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Employment Status</label>
                        <select name="employment_status" required class="form-select">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="terminated">Terminated</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Hire Date</label>
                    <input type="date" name="hire_date" required class="form-input" value="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Emergency Contact Name</label>
                        <input type="text" name="emergency_contact_name" required class="form-input" placeholder="Emergency contact name">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Emergency Contact Phone</label>
                        <input type="tel" name="emergency_contact_phone" required class="form-input" placeholder="Emergency contact phone">
                    </div>
                </div>
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeAddStaffModal()" class="btn-secondary">Cancel</button>
                    <button type="submit" name="add_staff" class="btn-primary">Add Staff Member</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Staff Modal -->
<div id="editStaffModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white max-h-screen overflow-y-auto">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Edit Staff Member</h3>
                <button onclick="closeEditStaffModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form method="POST" class="space-y-4">
                <input type="hidden" name="staff_id" id="edit_staff_id">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">First Name</label>
                        <input type="text" name="first_name" id="edit_first_name" required class="form-input">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Last Name</label>
                        <input type="text" name="last_name" id="edit_last_name" required class="form-input">
                    </div>
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
                <div>
                    <label class="block text-sm font-medium text-gray-700">National ID</label>
                    <input type="text" name="national_id" id="edit_national_id" required class="form-input">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Position</label>
                        <input type="text" name="position" id="edit_position" required class="form-input">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Department</label>
                        <select name="department" id="edit_department" required class="form-select">
                            <option value="Management">Management</option>
                            <option value="Front Desk">Front Desk</option>
                            <option value="Housekeeping">Housekeeping</option>
                            <option value="Food & Beverage">Food & Beverage</option>
                            <option value="Maintenance">Maintenance</option>
                            <option value="Security">Security</option>
                            <option value="IT">IT</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Salary (₦)</label>
                        <input type="number" name="salary" id="edit_salary" required step="0.01" min="0" class="form-input">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Employment Status</label>
                        <select name="employment_status" id="edit_employment_status" required class="form-select">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="terminated">Terminated</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Hire Date</label>
                    <input type="date" name="hire_date" id="edit_hire_date" required class="form-input">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Emergency Contact Name</label>
                        <input type="text" name="emergency_contact_name" id="edit_emergency_contact_name" required class="form-input">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Emergency Contact Phone</label>
                        <input type="tel" name="emergency_contact_phone" id="edit_emergency_contact_phone" required class="form-input">
                    </div>
                </div>
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeEditStaffModal()" class="btn-secondary">Cancel</button>
                    <button type="submit" name="update_staff" class="btn-primary">Update Staff Member</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openAddStaffModal() {
    document.getElementById('addStaffModal').classList.remove('hidden');
}

function closeAddStaffModal() {
    document.getElementById('addStaffModal').classList.add('hidden');
}

function editStaff(staff) {
    // Split full name into first and last name
    const nameParts = staff.full_name.split(' ');
    const firstName = nameParts[0] || '';
    const lastName = nameParts.slice(1).join(' ') || '';

    document.getElementById('edit_staff_id').value = staff.staff_id;
    document.getElementById('edit_first_name').value = firstName;
    document.getElementById('edit_last_name').value = lastName;
    document.getElementById('edit_email').value = staff.email;
    document.getElementById('edit_phone').value = staff.phone;
    document.getElementById('edit_national_id').value = staff.national_id || '';
    document.getElementById('edit_position').value = staff.position || '';
    document.getElementById('edit_department').value = staff.department;
    document.getElementById('edit_salary').value = staff.salary;
    document.getElementById('edit_employment_status').value = staff.employment_status || 'active';
    document.getElementById('edit_hire_date').value = staff.hire_date;
    document.getElementById('edit_emergency_contact_name').value = staff.emergency_contact_name || '';
    document.getElementById('edit_emergency_contact_phone').value = staff.emergency_contact_phone || '';
    document.getElementById('editStaffModal').classList.remove('hidden');
}

function closeEditStaffModal() {
    document.getElementById('editStaffModal').classList.add('hidden');
}

function deleteStaff(id, name) {
    if (confirm(`Are you sure you want to delete "${name}"? This action cannot be undone.`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="staff_id" value="${id}">
            <input type="hidden" name="delete_staff" value="1">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Filter functionality
document.getElementById('departmentFilter').addEventListener('change', function() {
    const department = this.value;
    const status = document.getElementById('statusFilter').value;
    const search = document.getElementById('searchFilter').value;
    const url = new URL(window.location);
    if (department === 'all') {
        url.searchParams.delete('department');
    } else {
        url.searchParams.set('department', department);
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
    const department = document.getElementById('departmentFilter').value;
    const search = document.getElementById('searchFilter').value;
    const url = new URL(window.location);
    if (department !== 'all') {
        url.searchParams.set('department', department);
    } else {
        url.searchParams.delete('department');
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
    const department = document.getElementById('departmentFilter').value;
    const status = document.getElementById('statusFilter').value;
    const url = new URL(window.location);
    if (department !== 'all') {
        url.searchParams.set('department', department);
    } else {
        url.searchParams.delete('department');
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
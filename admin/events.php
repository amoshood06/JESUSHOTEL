<?php
require_once '../config/database.php';

// Check if user is logged in and is admin/staff
if (!isLoggedIn() || !isStaff()) {
    redirect('../login.php');
}

// Handle event operations
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_event'])) {
        // Add new event
        $event_name = sanitize($_POST['event_name']);
        $description = sanitize($_POST['description']);
        $event_date = $_POST['event_date'];
        $start_time = $_POST['start_time'];
        $end_time = $_POST['end_time'];
        $venue = sanitize($_POST['venue']);
        $capacity = (int)$_POST['capacity'];
        $price_per_person = (float)$_POST['price_per_person'];
        $event_type = sanitize($_POST['event_type']);
        $status = sanitize($_POST['status']);
        $organizer_name = sanitize($_POST['organizer_name']);
        $organizer_phone = sanitize($_POST['organizer_phone']);
        $organizer_email = sanitize($_POST['organizer_email']);
        $special_requirements = sanitize($_POST['special_requirements'] ?? '');

        try {
            $stmt = $pdo->prepare("INSERT INTO events (event_name, description, event_date, start_time, end_time, venue, capacity, price_per_person, event_type, status, organizer_name, organizer_phone, organizer_email, special_requirements) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$event_name, $description, $event_date, $start_time, $end_time, $venue, $capacity, $price_per_person, $event_type, $status, $organizer_name, $organizer_phone, $organizer_email, $special_requirements]);
            $message = 'Event added successfully!';
            $messageType = 'success';
        } catch(PDOException $e) {
            $message = 'Error adding event: ' . $e->getMessage();
            $messageType = 'error';
        }
    } elseif (isset($_POST['update_event'])) {
        // Update event
        $event_id = (int)$_POST['event_id'];
        $event_name = sanitize($_POST['event_name']);
        $description = sanitize($_POST['description']);
        $event_date = $_POST['event_date'];
        $start_time = $_POST['start_time'];
        $end_time = $_POST['end_time'];
        $venue = sanitize($_POST['venue']);
        $capacity = (int)$_POST['capacity'];
        $price_per_person = (float)$_POST['price_per_person'];
        $event_type = sanitize($_POST['event_type']);
        $status = sanitize($_POST['status']);
        $organizer_name = sanitize($_POST['organizer_name']);
        $organizer_phone = sanitize($_POST['organizer_phone']);
        $organizer_email = sanitize($_POST['organizer_email']);
        $special_requirements = sanitize($_POST['special_requirements'] ?? '');

        try {
            $stmt = $pdo->prepare("UPDATE events SET event_name = ?, description = ?, event_date = ?, start_time = ?, end_time = ?, venue = ?, capacity = ?, price_per_person = ?, event_type = ?, status = ?, organizer_name = ?, organizer_phone = ?, organizer_email = ?, special_requirements = ? WHERE event_id = ?");
            $stmt->execute([$event_name, $description, $event_date, $start_time, $end_time, $venue, $capacity, $price_per_person, $event_type, $status, $organizer_name, $organizer_phone, $organizer_email, $special_requirements, $event_id]);
            $message = 'Event updated successfully!';
            $messageType = 'success';
        } catch(PDOException $e) {
            $message = 'Error updating event: ' . $e->getMessage();
            $messageType = 'error';
        }
    } elseif (isset($_POST['delete_event'])) {
        // Delete event
        $event_id = (int)$_POST['event_id'];

        try {
            $stmt = $pdo->prepare("DELETE FROM events WHERE event_id = ?");
            $stmt->execute([$event_id]);
            $message = 'Event deleted successfully!';
            $messageType = 'success';
        } catch(PDOException $e) {
            $message = 'Error deleting event: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
}

// Get filter parameters
$status_filter = $_GET['status'] ?? '';
$type_filter = $_GET['type'] ?? '';
$date_filter = $_GET['date'] ?? '';
$search_filter = $_GET['search'] ?? '';

// Build query with filters
$query = "SELECT * FROM events";
$conditions = [];
$params = [];

if ($status_filter && $status_filter !== 'all') {
    $conditions[] = "status = ?";
    $params[] = $status_filter;
}

if ($type_filter && $type_filter !== 'all') {
    $conditions[] = "event_type = ?";
    $params[] = $type_filter;
}

if ($date_filter) {
    $conditions[] = "DATE(event_date) = ?";
    $params[] = $date_filter;
}

if ($search_filter) {
    $conditions[] = "(event_name LIKE ? OR organizer_name LIKE ? OR venue LIKE ?)";
    $params[] = "%$search_filter%";
    $params[] = "%$search_filter%";
    $params[] = "%$search_filter%";
}

if (!empty($conditions)) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}

$query .= " ORDER BY event_date ASC, start_time ASC";

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $events = $stmt->fetchAll();
} catch(PDOException $e) {
    $events = [];
    error_log('Error fetching events: ' . $e->getMessage());
}

// Get event statistics
try {
    $stmt = $pdo->query("SELECT
        COUNT(*) as total,
        SUM(CASE WHEN status = 'upcoming' THEN 1 ELSE 0 END) as upcoming,
        SUM(CASE WHEN status = 'ongoing' THEN 1 ELSE 0 END) as ongoing,
        SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
        SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled,
        SUM(price_per_person * capacity) as potential_revenue
        FROM events");
    $stats = $stmt->fetch();
} catch(PDOException $e) {
    $stats = ['total' => 0, 'upcoming' => 0, 'ongoing' => 0, 'completed' => 0, 'cancelled' => 0, 'potential_revenue' => 0];
}

$pageTitle = 'Events Management';
$currentPage = 'events';
require_once 'admin-header.php';
?>

<!-- Events Management -->
<div id="events" class="tab-content p-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Events Management</h1>
            <p class="text-gray-600">Manage hotel events, bookings, and scheduling</p>
        </div>
        <button onclick="openAddEventModal()" class="btn-primary">
            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Add Event
        </button>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <div class="text-2xl font-bold text-gray-900"><?php echo $stats['total']; ?></div>
            <div class="text-sm text-gray-600">Total Events</div>
        </div>
        <div class="bg-blue-50 rounded-lg shadow p-4 text-center border-l-4 border-blue-400">
            <div class="text-2xl font-bold text-blue-700"><?php echo $stats['upcoming']; ?></div>
            <div class="text-sm text-blue-600">Upcoming</div>
        </div>
        <div class="bg-green-50 rounded-lg shadow p-4 text-center border-l-4 border-green-400">
            <div class="text-2xl font-bold text-green-700"><?php echo $stats['ongoing']; ?></div>
            <div class="text-sm text-green-600">Ongoing</div>
        </div>
        <div class="bg-purple-50 rounded-lg shadow p-4 text-center border-l-4 border-purple-400">
            <div class="text-2xl font-bold text-purple-700"><?php echo $stats['completed']; ?></div>
            <div class="text-sm text-purple-600">Completed</div>
        </div>
        <div class="bg-red-50 rounded-lg shadow p-4 text-center border-l-4 border-red-400">
            <div class="text-2xl font-bold text-red-700"><?php echo $stats['cancelled']; ?></div>
            <div class="text-sm text-red-600">Cancelled</div>
        </div>
        <div class="bg-teal-50 rounded-lg shadow p-4 text-center border-l-4 border-teal-400">
            <div class="text-2xl font-bold text-teal-700"><?php echo formatCurrency($stats['potential_revenue']); ?></div>
            <div class="text-sm text-teal-600">Potential Revenue</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex flex-wrap gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="statusFilter" class="form-select">
                    <option value="all" <?php echo $status_filter === '' || $status_filter === 'all' ? 'selected' : ''; ?>>All Events</option>
                    <option value="upcoming" <?php echo $status_filter === 'upcoming' ? 'selected' : ''; ?>>Upcoming</option>
                    <option value="ongoing" <?php echo $status_filter === 'ongoing' ? 'selected' : ''; ?>>Ongoing</option>
                    <option value="completed" <?php echo $status_filter === 'completed' ? 'selected' : ''; ?>>Completed</option>
                    <option value="cancelled" <?php echo $status_filter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select id="typeFilter" class="form-select">
                    <option value="all" <?php echo $type_filter === '' || $type_filter === 'all' ? 'selected' : ''; ?>>All Types</option>
                    <option value="Wedding" <?php echo $type_filter === 'Wedding' ? 'selected' : ''; ?>>Wedding</option>
                    <option value="Conference" <?php echo $type_filter === 'Conference' ? 'selected' : ''; ?>>Conference</option>
                    <option value="Birthday Party" <?php echo $type_filter === 'Birthday Party' ? 'selected' : ''; ?>>Birthday Party</option>
                    <option value="Corporate Meeting" <?php echo $type_filter === 'Corporate Meeting' ? 'selected' : ''; ?>>Corporate Meeting</option>
                    <option value="Seminar" <?php echo $type_filter === 'Seminar' ? 'selected' : ''; ?>>Seminar</option>
                    <option value="Other" <?php echo $type_filter === 'Other' ? 'selected' : ''; ?>>Other</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                <input type="date" id="dateFilter" class="form-input" value="<?php echo $date_filter; ?>">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" id="searchFilter" class="form-input" placeholder="Event name, organizer, or venue" value="<?php echo htmlspecialchars($search_filter); ?>">
            </div>
        </div>
    </div>

    <!-- Message Display -->
    <?php if ($message): ?>
        <div class="mb-6 p-4 rounded-lg <?php echo $messageType === 'error' ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-green-50 text-green-700 border border-green-200'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <!-- Events Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Events (<?php echo count($events); ?>)</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Event Details</th>
                        <th>Date & Time</th>
                        <th>Venue</th>
                        <th>Capacity</th>
                        <th>Price/Person</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Organizer</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($events)): ?>
                        <tr>
                            <td colspan="9" class="text-center py-8 text-gray-500">
                                <svg class="h-12 w-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                No events found. Add your first event to get started.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($events as $event): ?>
                            <tr>
                                <td>
                                    <div>
                                        <div class="font-medium"><?php echo htmlspecialchars($event['event_name']); ?></div>
                                        <?php if ($event['description']): ?>
                                            <div class="text-sm text-gray-500"><?php echo htmlspecialchars(substr($event['description'], 0, 50)) . (strlen($event['description']) > 50 ? '...' : ''); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-sm">
                                        <div><?php echo date('M d, Y', strtotime($event['event_date'])); ?></div>
                                        <div class="text-gray-500"><?php echo date('H:i', strtotime($event['start_time'])); ?> - <?php echo date('H:i', strtotime($event['end_time'])); ?></div>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($event['venue']); ?></td>
                                <td><?php echo $event['capacity']; ?> people</td>
                                <td class="font-medium"><?php echo formatCurrency($event['price_per_person']); ?></td>
                                <td><?php echo htmlspecialchars($event['event_type']); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $event['status']; ?>">
                                        <?php echo ucfirst($event['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="text-sm">
                                        <div><?php echo htmlspecialchars($event['organizer_name']); ?></div>
                                        <div class="text-gray-500"><?php echo htmlspecialchars($event['organizer_phone']); ?></div>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex space-x-2">
                                        <button onclick="editEvent(<?php echo htmlspecialchars(json_encode($event)); ?>)" class="btn-secondary text-xs px-3 py-1">
                                            Edit
                                        </button>
                                        <button onclick="deleteEvent(<?php echo $event['event_id']; ?>, '<?php echo htmlspecialchars($event['event_name']); ?>')" class="btn-danger text-xs px-3 py-1">
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

<!-- Add Event Modal -->
<div id="addEventModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white max-h-screen overflow-y-auto">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Add Event</h3>
                <button onclick="closeAddEventModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Event Name</label>
                    <input type="text" name="event_name" required class="form-input" placeholder="Wedding Reception, Conference, etc.">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" rows="3" class="form-textarea" placeholder="Event description..."></textarea>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Date</label>
                        <input type="date" name="event_date" required class="form-input">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Start Time</label>
                        <input type="time" name="start_time" required class="form-input">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">End Time</label>
                        <input type="time" name="end_time" required class="form-input">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Venue</label>
                        <select name="venue" required class="form-select">
                            <option value="Grand Ballroom">Grand Ballroom</option>
                            <option value="Conference Room A">Conference Room A</option>
                            <option value="Conference Room B">Conference Room B</option>
                            <option value="Garden Terrace">Garden Terrace</option>
                            <option value="Poolside">Poolside</option>
                            <option value="Restaurant">Restaurant</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Capacity</label>
                        <input type="number" name="capacity" required min="1" class="form-input" placeholder="100">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Price per Person (₦)</label>
                        <input type="number" name="price_per_person" required step="0.01" min="0" class="form-input" placeholder="5000.00">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Event Type</label>
                        <select name="event_type" required class="form-select">
                            <option value="Wedding">Wedding</option>
                            <option value="Conference">Conference</option>
                            <option value="Birthday Party">Birthday Party</option>
                            <option value="Corporate Meeting">Corporate Meeting</option>
                            <option value="Seminar">Seminar</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" required class="form-select">
                        <option value="upcoming">Upcoming</option>
                        <option value="ongoing">Ongoing</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Organizer Name</label>
                        <input type="text" name="organizer_name" required class="form-input" placeholder="John Doe">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Phone</label>
                        <input type="tel" name="organizer_phone" required class="form-input" placeholder="+234 xxx xxx xxxx">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="organizer_email" required class="form-input" placeholder="john@example.com">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Special Requirements</label>
                    <textarea name="special_requirements" rows="2" class="form-textarea" placeholder="Any special requirements or notes..."></textarea>
                </div>
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeAddEventModal()" class="btn-secondary">Cancel</button>
                    <button type="submit" name="add_event" class="btn-primary">Add Event</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Event Modal -->
<div id="editEventModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white max-h-screen overflow-y-auto">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Edit Event</h3>
                <button onclick="closeEditEventModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form method="POST" class="space-y-4">
                <input type="hidden" name="event_id" id="edit_event_id">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Event Name</label>
                    <input type="text" name="event_name" id="edit_event_name" required class="form-input">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="edit_description" rows="3" class="form-textarea"></textarea>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Date</label>
                        <input type="date" name="event_date" id="edit_event_date" required class="form-input">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Start Time</label>
                        <input type="time" name="start_time" id="edit_start_time" required class="form-input">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">End Time</label>
                        <input type="time" name="end_time" id="edit_end_time" required class="form-input">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Venue</label>
                        <select name="venue" id="edit_venue" required class="form-select">
                            <option value="Grand Ballroom">Grand Ballroom</option>
                            <option value="Conference Room A">Conference Room A</option>
                            <option value="Conference Room B">Conference Room B</option>
                            <option value="Garden Terrace">Garden Terrace</option>
                            <option value="Poolside">Poolside</option>
                            <option value="Restaurant">Restaurant</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Capacity</label>
                        <input type="number" name="capacity" id="edit_capacity" required min="1" class="form-input">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Price per Person (₦)</label>
                        <input type="number" name="price_per_person" id="edit_price_per_person" required step="0.01" min="0" class="form-input">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Event Type</label>
                        <select name="event_type" id="edit_event_type" required class="form-select">
                            <option value="Wedding">Wedding</option>
                            <option value="Conference">Conference</option>
                            <option value="Birthday Party">Birthday Party</option>
                            <option value="Corporate Meeting">Corporate Meeting</option>
                            <option value="Seminar">Seminar</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" id="edit_status" required class="form-select">
                        <option value="upcoming">Upcoming</option>
                        <option value="ongoing">Ongoing</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Organizer Name</label>
                        <input type="text" name="organizer_name" id="edit_organizer_name" required class="form-input">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Phone</label>
                        <input type="tel" name="organizer_phone" id="edit_organizer_phone" required class="form-input">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="organizer_email" id="edit_organizer_email" required class="form-input">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Special Requirements</label>
                    <textarea name="special_requirements" id="edit_special_requirements" rows="2" class="form-textarea"></textarea>
                </div>
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeEditEventModal()" class="btn-secondary">Cancel</button>
                    <button type="submit" name="update_event" class="btn-primary">Update Event</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openAddEventModal() {
    document.getElementById('addEventModal').classList.remove('hidden');
}

function closeAddEventModal() {
    document.getElementById('addEventModal').classList.add('hidden');
}

function editEvent(event) {
    document.getElementById('edit_event_id').value = event.event_id;
    document.getElementById('edit_event_name').value = event.event_name;
    document.getElementById('edit_description').value = event.description || '';
    document.getElementById('edit_event_date').value = event.event_date;
    document.getElementById('edit_start_time').value = event.start_time;
    document.getElementById('edit_end_time').value = event.end_time;
    document.getElementById('edit_venue').value = event.venue;
    document.getElementById('edit_capacity').value = event.capacity;
    document.getElementById('edit_price_per_person').value = event.price_per_person;
    document.getElementById('edit_event_type').value = event.event_type;
    document.getElementById('edit_status').value = event.status;
    document.getElementById('edit_organizer_name').value = event.organizer_name;
    document.getElementById('edit_organizer_phone').value = event.organizer_phone;
    document.getElementById('edit_organizer_email').value = event.organizer_email;
    document.getElementById('edit_special_requirements').value = event.special_requirements || '';
    document.getElementById('editEventModal').classList.remove('hidden');
}

function closeEditEventModal() {
    document.getElementById('editEventModal').classList.add('hidden');
}

function deleteEvent(id, name) {
    if (confirm(`Are you sure you want to delete "${name}"? This action cannot be undone.`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="event_id" value="${id}">
            <input type="hidden" name="delete_event" value="1">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Filter functionality
document.getElementById('statusFilter').addEventListener('change', function() {
    const status = this.value;
    const type = document.getElementById('typeFilter').value;
    const date = document.getElementById('dateFilter').value;
    const search = document.getElementById('searchFilter').value;
    const url = new URL(window.location);
    if (status === 'all') {
        url.searchParams.delete('status');
    } else {
        url.searchParams.set('status', status);
    }
    if (type !== 'all') url.searchParams.set('type', type);
    else url.searchParams.delete('type');
    if (date) url.searchParams.set('date', date);
    else url.searchParams.delete('date');
    if (search) url.searchParams.set('search', search);
    else url.searchParams.delete('search');
    window.location.href = url.toString();
});

document.getElementById('typeFilter').addEventListener('change', function() {
    const type = this.value;
    const status = document.getElementById('statusFilter').value;
    const date = document.getElementById('dateFilter').value;
    const search = document.getElementById('searchFilter').value;
    const url = new URL(window.location);
    if (status !== 'all') url.searchParams.set('status', status);
    else url.searchParams.delete('status');
    if (type === 'all') {
        url.searchParams.delete('type');
    } else {
        url.searchParams.set('type', type);
    }
    if (date) url.searchParams.set('date', date);
    else url.searchParams.delete('date');
    if (search) url.searchParams.set('search', search);
    else url.searchParams.delete('search');
    window.location.href = url.toString();
});

document.getElementById('dateFilter').addEventListener('change', function() {
    const date = this.value;
    const status = document.getElementById('statusFilter').value;
    const type = document.getElementById('typeFilter').value;
    const search = document.getElementById('searchFilter').value;
    const url = new URL(window.location);
    if (status !== 'all') url.searchParams.set('status', status);
    else url.searchParams.delete('status');
    if (type !== 'all') url.searchParams.set('type', type);
    else url.searchParams.delete('type');
    if (date) url.searchParams.set('date', date);
    else url.searchParams.delete('date');
    if (search) url.searchParams.set('search', search);
    else url.searchParams.delete('search');
    window.location.href = url.toString();
});

document.getElementById('searchFilter').addEventListener('input', function() {
    const search = this.value;
    const status = document.getElementById('statusFilter').value;
    const type = document.getElementById('typeFilter').value;
    const date = document.getElementById('dateFilter').value;
    const url = new URL(window.location);
    if (status !== 'all') url.searchParams.set('status', status);
    else url.searchParams.delete('status');
    if (type !== 'all') url.searchParams.set('type', type);
    else url.searchParams.delete('type');
    if (date) url.searchParams.set('date', date);
    else url.searchParams.delete('date');
    if (search) url.searchParams.set('search', search);
    else url.searchParams.delete('search');
    window.location.href = url.toString();
});
</script>

<?php require_once 'admin-footer.php'; ?>
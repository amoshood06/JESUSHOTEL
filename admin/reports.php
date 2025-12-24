<?php
require_once '../config/database.php';

// Check if user is logged in and is admin/staff
if (!isLoggedIn() || !isStaff()) {
    redirect('../login.php');
}

// Get date range parameters
$start_date = $_GET['start_date'] ?? date('Y-m-01'); // First day of current month
$end_date = $_GET['end_date'] ?? date('Y-m-t'); // Last day of current month

// Revenue Report
try {
    $stmt = $pdo->prepare("SELECT
        DATE(created_at) as date,
        SUM(amount) as daily_revenue,
        COUNT(*) as transactions
        FROM payments
        WHERE status = 'completed'
        AND DATE(created_at) BETWEEN ? AND ?
        GROUP BY DATE(created_at)
        ORDER BY date ASC");
    $stmt->execute([$start_date, $end_date]);
    $revenue_data = $stmt->fetchAll();
} catch(PDOException $e) {
    $revenue_data = [];
}

// Booking Statistics
try {
    $stmt = $pdo->prepare("SELECT
        DATE(created_at) as date,
        COUNT(*) as bookings,
        SUM(total_amount) as booking_revenue
        FROM bookings
        WHERE DATE(created_at) BETWEEN ? AND ?
        GROUP BY DATE(created_at)
        ORDER BY date ASC");
    $stmt->execute([$start_date, $end_date]);
    $booking_data = $stmt->fetchAll();
} catch(PDOException $e) {
    $booking_data = [];
}

// Room Occupancy
try {
    $stmt = $pdo->prepare("SELECT
        r.room_type,
        COUNT(*) as total_rooms,
        SUM(CASE WHEN r.status = 'available' THEN 1 ELSE 0 END) as available,
        SUM(CASE WHEN r.status = 'occupied' THEN 1 ELSE 0 END) as occupied,
        SUM(CASE WHEN r.status = 'maintenance' THEN 1 ELSE 0 END) as maintenance
        FROM rooms r
        GROUP BY r.room_type");
    $stmt->execute();
    $room_stats = $stmt->fetchAll();
} catch(PDOException $e) {
    $room_stats = [];
}

// Food Orders Report
try {
    $stmt = $pdo->prepare("SELECT
        DATE(fo.created_at) as date,
        COUNT(*) as orders,
        SUM(fo.total_amount) as food_revenue
        FROM food_orders fo
        WHERE DATE(fo.created_at) BETWEEN ? AND ?
        GROUP BY DATE(fo.created_at)
        ORDER BY date ASC");
    $stmt->execute([$start_date, $end_date]);
    $food_data = $stmt->fetchAll();
} catch(PDOException $e) {
    $food_data = [];
}

// Overall Statistics
try {
    $stmt = $pdo->prepare("SELECT
        (SELECT COUNT(*) FROM bookings WHERE DATE(created_at) BETWEEN ? AND ?) as total_bookings,
        (SELECT SUM(total_amount) FROM bookings WHERE DATE(created_at) BETWEEN ? AND ?) as booking_revenue,
        (SELECT COUNT(*) FROM food_orders WHERE DATE(created_at) BETWEEN ? AND ?) as total_orders,
        (SELECT SUM(total_amount) FROM food_orders WHERE DATE(created_at) BETWEEN ? AND ?) as food_revenue,
        (SELECT COUNT(*) FROM users WHERE DATE(created_at) BETWEEN ? AND ?) as new_users");
    $stmt->execute([$start_date, $end_date, $start_date, $end_date, $start_date, $end_date, $start_date, $end_date, $start_date, $end_date]);
    $overall_stats = $stmt->fetch();
} catch(PDOException $e) {
    $overall_stats = ['total_bookings' => 0, 'booking_revenue' => 0, 'total_orders' => 0, 'food_revenue' => 0, 'new_users' => 0];
}

// Top Performing Items
try {
    $stmt = $pdo->prepare("SELECT
        fm.item_name,
        fm.category,
        COUNT(foi.order_item_id) as orders_count,
        SUM(foi.quantity) as total_quantity,
        SUM(foi.price * foi.quantity) as total_revenue
        FROM food_order_items foi
        JOIN food_orders fo ON foi.order_id = fo.order_id
        JOIN food_menu fm ON foi.menu_item_id = fm.menu_item_id
        WHERE DATE(fo.created_at) BETWEEN ? AND ?
        GROUP BY fm.menu_item_id, fm.item_name, fm.category
        ORDER BY total_revenue DESC
        LIMIT 10");
    $stmt->execute([$start_date, $end_date]);
    $top_items = $stmt->fetchAll();
} catch(PDOException $e) {
    $top_items = [];
}

// Monthly Trends (Last 12 months)
try {
    $stmt = $pdo->query("SELECT
        DATE_FORMAT(created_at, '%Y-%m') as month,
        COUNT(*) as bookings,
        SUM(total_amount) as revenue
        FROM bookings
        WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
        GROUP BY DATE_FORMAT(created_at, '%Y-%m')
        ORDER BY month ASC");
    $monthly_trends = $stmt->fetchAll();
} catch(PDOException $e) {
    $monthly_trends = [];
}

$pageTitle = 'Reports & Analytics';
$currentPage = 'reports';
require_once 'admin-header.php';
?>

<!-- Reports & Analytics -->
<div id="reports" class="tab-content p-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Reports & Analytics</h1>
            <p class="text-gray-600">Comprehensive reporting and business intelligence</p>
        </div>
        <div class="flex space-x-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                <input type="date" id="startDate" class="form-input" value="<?php echo $start_date; ?>">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                <input type="date" id="endDate" class="form-input" value="<?php echo $end_date; ?>">
            </div>
            <div class="flex items-end">
                <button onclick="updateDateRange()" class="btn-primary">Update</button>
            </div>
        </div>
    </div>

    <!-- Overall Statistics -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <div class="text-3xl font-bold text-blue-600"><?php echo $overall_stats['total_bookings']; ?></div>
            <div class="text-sm text-gray-600">Total Bookings</div>
            <div class="text-lg font-semibold text-green-600 mt-2"><?php echo formatCurrency($overall_stats['booking_revenue']); ?></div>
        </div>
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <div class="text-3xl font-bold text-green-600"><?php echo $overall_stats['total_orders']; ?></div>
            <div class="text-sm text-gray-600">Food Orders</div>
            <div class="text-lg font-semibold text-green-600 mt-2"><?php echo formatCurrency($overall_stats['food_revenue']); ?></div>
        </div>
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <div class="text-3xl font-bold text-purple-600"><?php echo $overall_stats['new_users']; ?></div>
            <div class="text-sm text-gray-600">New Users</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <div class="text-3xl font-bold text-teal-600"><?php echo formatCurrency($overall_stats['booking_revenue'] + $overall_stats['food_revenue']); ?></div>
            <div class="text-sm text-gray-600">Total Revenue</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <div class="text-3xl font-bold text-orange-600"><?php echo count($revenue_data); ?></div>
            <div class="text-sm text-gray-600">Active Days</div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Revenue Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Daily Revenue Trend</h3>
            <canvas id="revenueChart" width="400" height="200"></canvas>
        </div>

        <!-- Booking Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Daily Bookings</h3>
            <canvas id="bookingChart" width="400" height="200"></canvas>
        </div>
    </div>

    <!-- Room Occupancy & Food Orders -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Room Occupancy -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Room Occupancy by Type</h3>
            <div class="space-y-4">
                <?php foreach ($room_stats as $stat): ?>
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span><?php echo htmlspecialchars($stat['room_type']); ?></span>
                            <span><?php echo $stat['occupied']; ?>/<?php echo $stat['total_rooms']; ?> occupied</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: <?php echo $stat['total_rooms'] > 0 ? ($stat['occupied'] / $stat['total_rooms'] * 100) : 0; ?>%"></div>
                        </div>
                        <div class="flex justify-between text-xs text-gray-500 mt-1">
                            <span>Available: <?php echo $stat['available']; ?></span>
                            <span>Maintenance: <?php echo $stat['maintenance']; ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Food Orders Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Daily Food Orders</h3>
            <canvas id="foodChart" width="400" height="200"></canvas>
        </div>
    </div>

    <!-- Top Performing Items & Monthly Trends -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Top Items -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Performing Menu Items</h3>
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Orders</th>
                            <th>Quantity</th>
                            <th>Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($top_items)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-4 text-gray-500">No food orders in selected period</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($top_items as $item): ?>
                                <tr>
                                    <td>
                                        <div>
                                            <div class="font-medium"><?php echo htmlspecialchars($item['item_name']); ?></div>
                                            <div class="text-sm text-gray-500"><?php echo htmlspecialchars($item['category']); ?></div>
                                        </div>
                                    </td>
                                    <td><?php echo $item['orders_count']; ?></td>
                                    <td><?php echo $item['total_quantity']; ?></td>
                                    <td class="font-medium"><?php echo formatCurrency($item['total_revenue']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Monthly Trends -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Monthly Booking Trends (Last 12 Months)</h3>
            <canvas id="monthlyChart" width="400" height="200"></canvas>
        </div>
    </div>

    <!-- Detailed Reports -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Revenue Breakdown -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Revenue Breakdown</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span>Room Bookings</span>
                    <span class="font-semibold"><?php echo formatCurrency($overall_stats['booking_revenue']); ?></span>
                </div>
                <div class="flex justify-between items-center">
                    <span>Food Orders</span>
                    <span class="font-semibold"><?php echo formatCurrency($overall_stats['food_revenue']); ?></span>
                </div>
                <hr>
                <div class="flex justify-between items-center font-bold text-lg">
                    <span>Total Revenue</span>
                    <span><?php echo formatCurrency($overall_stats['booking_revenue'] + $overall_stats['food_revenue']); ?></span>
                </div>
            </div>
        </div>

        <!-- Export Options -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Export Reports</h3>
            <div class="space-y-3">
                <button onclick="exportReport('revenue')" class="w-full btn-primary">
                    <svg class="h-4 w-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export Revenue Report
                </button>
                <button onclick="exportReport('bookings')" class="w-full btn-secondary">
                    <svg class="h-4 w-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export Booking Report
                </button>
                <button onclick="exportReport('food')" class="w-full btn-secondary">
                    <svg class="h-4 w-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export Food Orders Report
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Prepare data for charts
const revenueLabels = <?php echo json_encode(array_column($revenue_data, 'date')); ?>;
const revenueData = <?php echo json_encode(array_column($revenue_data, 'daily_revenue')); ?>;

const bookingLabels = <?php echo json_encode(array_column($booking_data, 'date')); ?>;
const bookingData = <?php echo json_encode(array_column($booking_data, 'bookings')); ?>;

const foodLabels = <?php echo json_encode(array_column($food_data, 'date')); ?>;
const foodData = <?php echo json_encode(array_column($food_data, 'orders')); ?>;

const monthlyLabels = <?php echo json_encode(array_column($monthly_trends, 'month')); ?>;
const monthlyBookings = <?php echo json_encode(array_column($monthly_trends, 'bookings')); ?>;
const monthlyRevenue = <?php echo json_encode(array_column($monthly_trends, 'revenue')); ?>;

// Revenue Chart
const revenueChart = new Chart(document.getElementById('revenueChart'), {
    type: 'line',
    data: {
        labels: revenueLabels,
        datasets: [{
            label: 'Daily Revenue (₦)',
            data: revenueData,
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '₦' + value.toLocaleString();
                    }
                }
            }
        }
    }
});

// Booking Chart
const bookingChart = new Chart(document.getElementById('bookingChart'), {
    type: 'bar',
    data: {
        labels: bookingLabels,
        datasets: [{
            label: 'Daily Bookings',
            data: bookingData,
            backgroundColor: 'rgba(16, 185, 129, 0.8)',
            borderColor: 'rgb(16, 185, 129)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Food Orders Chart
const foodChart = new Chart(document.getElementById('foodChart'), {
    type: 'line',
    data: {
        labels: foodLabels,
        datasets: [{
            label: 'Daily Orders',
            data: foodData,
            borderColor: 'rgb(245, 101, 101)',
            backgroundColor: 'rgba(245, 101, 101, 0.1)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Monthly Trends Chart
const monthlyChart = new Chart(document.getElementById('monthlyChart'), {
    type: 'bar',
    data: {
        labels: monthlyLabels,
        datasets: [{
            label: 'Monthly Bookings',
            data: monthlyBookings,
            backgroundColor: 'rgba(139, 92, 246, 0.8)',
            borderColor: 'rgb(139, 92, 246)',
            borderWidth: 1,
            yAxisID: 'y'
        }, {
            label: 'Monthly Revenue (₦)',
            data: monthlyRevenue,
            backgroundColor: 'rgba(245, 158, 11, 0.8)',
            borderColor: 'rgb(245, 158, 11)',
            borderWidth: 1,
            yAxisID: 'y1'
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                type: 'linear',
                display: true,
                position: 'left',
                title: {
                    display: true,
                    text: 'Bookings'
                }
            },
            y1: {
                type: 'linear',
                display: true,
                position: 'right',
                title: {
                    display: true,
                    text: 'Revenue (₦)'
                },
                ticks: {
                    callback: function(value) {
                        return '₦' + value.toLocaleString();
                    }
                }
            }
        }
    }
});

function updateDateRange() {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;

    if (startDate && endDate) {
        const url = new URL(window.location);
        url.searchParams.set('start_date', startDate);
        url.searchParams.set('end_date', endDate);
        window.location.href = url.toString();
    }
}

function exportReport(type) {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;

    // Create a form to submit the export request
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'export-report.php'; // You'll need to create this file
    form.target = '_blank';

    form.innerHTML = `
        <input type="hidden" name="report_type" value="${type}">
        <input type="hidden" name="start_date" value="${startDate}">
        <input type="hidden" name="end_date" value="${endDate}">
    `;

    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}
</script>

<?php require_once 'admin-footer.php'; ?>
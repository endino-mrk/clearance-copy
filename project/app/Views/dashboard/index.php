<?php
// Include helper functions
include_once 'includes/functions.php';

// Set page title
$pageTitle = 'Dashboard';

// Enable charts for this page
$useCharts = true;

// Sample data for dashboard stats
$dashboardStats = [
    'total_residents' => 124,
    'cleared_residents' => 87,
    'pending_clearance' => 32,
    'unpaid_fees' => 8245,
    'resident_growth' => 12,
    'clearance_growth' => 8,
    'pending_growth' => 5,
    'fees_reduction' => 3
];

// Sample data for recent activities
$recentActivities = [
    [
        'type' => 'resident_added',
        'icon' => 'ri-user-add-line',
        'icon_bg' => 'bg-blue-100',
        'icon_text' => 'text-blue-500',
        'title' => 'New resident added',
        'description' => 'Emma Thompson was added to Room 304',
        'time' => '20 minutes ago'
    ],
    [
        'type' => 'clearance_approved',
        'icon' => 'ri-check-line',
        'icon_bg' => 'bg-green-100',
        'icon_text' => 'text-green-500',
        'title' => 'Clearance approved',
        'description' => 'Michael Johnson\'s clearance was approved',
        'time' => '1 hour ago'
    ],
    [
        'type' => 'payment_received',
        'icon' => 'ri-money-dollar-circle-line',
        'icon_bg' => 'bg-yellow-100',
        'icon_text' => 'text-yellow-500',
        'title' => 'Payment received',
        'description' => 'Sarah Wilson paid $450 for Room 215',
        'time' => '3 hours ago'
    ],
    [
        'type' => 'fine_issued',
        'icon' => 'ri-close-line',
        'icon_bg' => 'bg-red-100',
        'icon_text' => 'text-red-500',
        'title' => 'Fine issued',
        'description' => 'Daniel Brown received a $25 fine for late payment',
        'time' => '5 hours ago'
    ]
];

// Sample data for pending clearance
$pendingClearances = [
    [
        'resident_id' => 'R-0004',
        'resident_name' => 'Jessica Davis',
        'resident_email' => 'jessica.davis@example.com',
        'room' => 'Room 201',
        'building' => 'Building A',
        'status' => 'Pending Room Check',
        'due_date' => '2025-05-05'
    ],
    [
        'resident_id' => 'R-0002',
        'resident_name' => 'Robert Harris',
        'resident_email' => 'robert.harris@example.com',
        'room' => 'Room 315',
        'building' => 'Building B',
        'status' => 'Unpaid Fees',
        'due_date' => '2025-05-02'
    ],
    [
        'resident_id' => 'R-0003',
        'resident_name' => 'Amanda Miller',
        'resident_email' => 'amanda.miller@example.com',
        'room' => 'Room 107',
        'building' => 'Building A',
        'status' => 'Missing Documents',
        'due_date' => '2025-05-10'
    ],
    [
        'resident_id' => 'R-0001',
        'resident_name' => 'Kevin Wilson',
        'resident_email' => 'kevin.wilson@example.com',
        'room' => 'Room 422',
        'building' => 'Building C',
        'status' => 'Multiple Issues',
        'due_date' => '2025-05-01'
    ]
];

// Include header component
include BASE_PATH . '/app/Views/components/header.php';
?>

<!-- Page Header -->
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Dormitory Manager Dashboard</h1>
    <p class="mt-1 text-sm text-gray-500">Tuesday, April 30, 2025</p>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded shadow p-4 border-l-4 border-primary">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Total Residents</p>
                <p class="text-2xl font-bold text-gray-900"><?php echo $dashboardStats['total_residents']; ?></p>
            </div>
            <div class="w-10 h-10 rounded-full bg-primary bg-opacity-10 flex items-center justify-center text-primary">
                <i class="ri-user-line ri-lg"></i>
            </div>
        </div>
        <div class="mt-2 flex items-center text-xs">
            <span class="text-green-500 flex items-center">
                <i class="ri-arrow-up-s-line"></i> <?php echo $dashboardStats['resident_growth']; ?>%
            </span>
            <span class="ml-1 text-gray-500">from last month</span>
        </div>
    </div>
    <div class="bg-white rounded shadow p-4 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Cleared Residents</p>
                <p class="text-2xl font-bold text-gray-900"><?php echo $dashboardStats['cleared_residents']; ?></p>
            </div>
            <div class="w-10 h-10 rounded-full bg-green-500 bg-opacity-10 flex items-center justify-center text-green-500">
                <i class="ri-check-double-line ri-lg"></i>
            </div>
        </div>
        <div class="mt-2 flex items-center text-xs">
            <span class="text-green-500 flex items-center">
                <i class="ri-arrow-up-s-line"></i> <?php echo $dashboardStats['clearance_growth']; ?>%
            </span>
            <span class="ml-1 text-gray-500">from last week</span>
        </div>
    </div>
    <div class="bg-white rounded shadow p-4 border-l-4 border-yellow-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Pending Clearance</p>
                <p class="text-2xl font-bold text-gray-900"><?php echo $dashboardStats['pending_clearance']; ?></p>
            </div>
            <div class="w-10 h-10 rounded-full bg-yellow-500 bg-opacity-10 flex items-center justify-center text-yellow-500">
                <i class="ri-time-line ri-lg"></i>
            </div>
        </div>
        <div class="mt-2 flex items-center text-xs">
            <span class="text-red-500 flex items-center">
                <i class="ri-arrow-up-s-line"></i> <?php echo $dashboardStats['pending_growth']; ?>%
            </span>
            <span class="ml-1 text-gray-500">from last week</span>
        </div>
    </div>
    <div class="bg-white rounded shadow p-4 border-l-4 border-red-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Unpaid Fees</p>
                <p class="text-2xl font-bold text-gray-900"><?php echo formatCurrency($dashboardStats['unpaid_fees']); ?></p>
            </div>
            <div class="w-10 h-10 rounded-full bg-red-500 bg-opacity-10 flex items-center justify-center text-red-500">
                <i class="ri-money-dollar-circle-line ri-lg"></i>
            </div>
        </div>
        <div class="mt-2 flex items-center text-xs">
            <span class="text-red-500 flex items-center">
                <i class="ri-arrow-down-s-line"></i> <?php echo $dashboardStats['fees_reduction']; ?>%
            </span>
            <span class="ml-1 text-gray-500">from last month</span>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Clearance Status Chart -->
    <div class="bg-white rounded shadow p-4">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">Clearance Status</h2>
            <div class="flex items-center space-x-2">
                <button class="px-2 py-1 text-xs font-medium text-gray-600 bg-gray-100 rounded hover:bg-gray-200 !rounded-button whitespace-nowrap">
                    Weekly
                </button>
                <button class="px-2 py-1 text-xs font-medium text-white bg-primary rounded hover:bg-primary-dark !rounded-button whitespace-nowrap">
                    Monthly
                </button>
            </div>
        </div>
        <div id="clearanceChart" class="h-64"></div>
    </div>
    <!-- Payment Trends Chart -->
    <div class="bg-white rounded shadow p-4">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">Payment Trends</h2>
            <div class="flex items-center space-x-2">
                <button class="px-2 py-1 text-xs font-medium text-white bg-primary rounded hover:bg-primary-dark !rounded-button whitespace-nowrap">
                    Last 6 Months
                </button>
                <button class="px-2 py-1 text-xs font-medium text-gray-600 bg-gray-100 rounded hover:bg-gray-200 !rounded-button whitespace-nowrap">
                    Last Year
                </button>
            </div>
        </div>
        <div id="paymentChart" class="h-64"></div>
    </div>
</div>

<!-- Recent Activities and Pending Clearance -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- Recent Activities -->
    <div class="bg-white rounded shadow lg:col-span-1">
        <div class="px-4 py-3 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Recent Activities</h2>
        </div>
        <div class="p-4">
            <div class="space-y-4">
                <?php foreach ($recentActivities as $activity): ?>
                <div class="flex items-start space-x-3">
                    <div class="w-8 h-8 rounded-full <?php echo $activity['icon_bg']; ?> flex items-center justify-center <?php echo $activity['icon_text']; ?>">
                        <i class="<?php echo $activity['icon']; ?>"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900"><?php echo $activity['title']; ?></p>
                        <p class="text-xs text-gray-500"><?php echo $activity['description']; ?></p>
                        <p class="text-xs text-gray-400 mt-1"><?php echo $activity['time']; ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <button class="mt-4 text-sm text-primary font-medium hover:text-primary-dark !rounded-button whitespace-nowrap">
                View all activities
            </button>
        </div>
    </div>
    
    <!-- Pending Clearance -->
    <div class="bg-white rounded shadow lg:col-span-2">
        <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-900">Pending Clearance</h2>
            <div class="flex items-center space-x-2">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="ri-search-line text-gray-400"></i>
                    </div>
                    <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary focus:border-primary block w-full pl-10 p-2" placeholder="Search residents...">
                </div>
                <button class="p-2 text-gray-500 rounded-lg hover:bg-gray-100 !rounded-button whitespace-nowrap">
                    <i class="ri-filter-3-line"></i>
                </button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Resident</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Room</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($pendingClearances as $clearance): ?>
                        <?php 
                        $statusClass = '';
                        $statusBg = '';
                        
                        switch ($clearance['status']) {
                            case 'Pending Room Check':
                                $statusClass = 'text-yellow-800';
                                $statusBg = 'bg-yellow-100';
                                break;
                            case 'Unpaid Fees':
                                $statusClass = 'text-red-800';
                                $statusBg = 'bg-red-100';
                                break;
                            case 'Missing Documents':
                                $statusClass = 'text-yellow-800';
                                $statusBg = 'bg-yellow-100';
                                break;
                            case 'Multiple Issues':
                                $statusClass = 'text-red-800';
                                $statusBg = 'bg-red-100';
                                break;
                        }
                        ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-500">
                                        <span class="font-medium"><?php echo getInitials($clearance['resident_name']); ?></span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900"><?php echo $clearance['resident_name']; ?></div>
                                        <div class="text-sm text-gray-500"><?php echo $clearance['resident_email']; ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?php echo $clearance['room']; ?></div>
                                <div class="text-sm text-gray-500"><?php echo $clearance['building']; ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $statusBg; ?> <?php echo $statusClass; ?>">
                                    <?php echo $clearance['status']; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo formatDate($clearance['due_date']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button class="text-primary hover:text-primary-dark !rounded-button whitespace-nowrap">Review</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 bg-gray-50 flex items-center justify-between border-t border-gray-200 sm:px-6">
            <div class="flex-1 flex justify-between sm:hidden">
                <button class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 !rounded-button whitespace-nowrap">
                    Previous
                </button>
                <button class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 !rounded-button whitespace-nowrap">
                    Next
                </button>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700">
                        Showing <span class="font-medium">1</span> to <span class="font-medium">4</span> of <span class="font-medium">32</span> results
                    </p>
                </div>
                <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                        <button class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 !rounded-button whitespace-nowrap">
                            <span class="sr-only">Previous</span>
                            <i class="ri-arrow-left-s-line"></i>
                        </button>
                        <button class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 !rounded-button whitespace-nowrap">
                            1
                        </button>
                        <button class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-primary text-sm font-medium text-white hover:bg-primary-dark !rounded-button whitespace-nowrap">
                            2
                        </button>
                        <button class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 !rounded-button whitespace-nowrap">
                            3
                        </button>
                        <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 !rounded-button whitespace-nowrap">
                            ...
                        </span>
                        <button class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 !rounded-button whitespace-nowrap">
                            8
                        </button>
                        <button class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 !rounded-button whitespace-nowrap">
                            <span class="sr-only">Next</span>
                            <i class="ri-arrow-right-s-line"></i>
                        </button>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="bg-white rounded shadow mb-6">
    <div class="px-4 py-3 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900">Quick Actions</h2>
    </div>
    <div class="p-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <button class="flex flex-col items-center justify-center p-4 border border-gray-200 rounded hover:bg-gray-50 transition-colors !rounded-button whitespace-nowrap">
            <div class="w-12 h-12 rounded-full bg-primary bg-opacity-10 flex items-center justify-center text-primary mb-2">
                <i class="ri-user-add-line ri-lg"></i>
            </div>
            <span class="text-sm font-medium text-gray-900">Add New Resident</span>
        </button>
        <button class="flex flex-col items-center justify-center p-4 border border-gray-200 rounded hover:bg-gray-50 transition-colors !rounded-button whitespace-nowrap">
            <div class="w-12 h-12 rounded-full bg-green-500 bg-opacity-10 flex items-center justify-center text-green-500 mb-2">
                <i class="ri-check-double-line ri-lg"></i>
            </div>
            <span class="text-sm font-medium text-gray-900">Process Clearance</span>
        </button>
        <button class="flex flex-col items-center justify-center p-4 border border-gray-200 rounded hover:bg-gray-50 transition-colors !rounded-button whitespace-nowrap">
            <div class="w-12 h-12 rounded-full bg-blue-500 bg-opacity-10 flex items-center justify-center text-blue-500 mb-2">
                <i class="ri-money-dollar-circle-line ri-lg"></i>
            </div>
            <span class="text-sm font-medium text-gray-900">Record Payment</span>
        </button>
        <button class="flex flex-col items-center justify-center p-4 border border-gray-200 rounded hover:bg-gray-50 transition-colors !rounded-button whitespace-nowrap">
            <div class="w-12 h-12 rounded-full bg-purple-500 bg-opacity-10 flex items-center justify-center text-purple-500 mb-2">
                <i class="ri-file-list-line ri-lg"></i>
            </div>
            <span class="text-sm font-medium text-gray-900">Generate Reports</span>
        </button>
    </div>
</div>

<?php
// Include footer component
include BASE_PATH . '/app/Views/components/footer.php';
?>
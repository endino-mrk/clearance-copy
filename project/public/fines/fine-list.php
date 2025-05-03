<?php
// Include helper functions

require_once __DIR__ . '/../../config/bootstrap.php';

require_once BASE_PATH . '/includes/auth_check.php';

// Set page title
$pageTitle = 'Fines Management';

// Enable charts for this page
$useCharts = true;

// Sample data for fines
$fines = [
    [
        'id' => 'FINE-0001',
        'resident_id' => 'R-0004',
        'resident_name' => 'Kevin Wilson',
        'room' => 'Room 422',
        'building' => 'Building C',
        'fine_amount' => 25,
        'issue_date' => '2025-04-10',
        'due_date' => '2025-04-25',
        'status' => 'Unpaid',
        'reason' => 'Late Payment',
        'description' => 'Late payment of April 2025 rent',
        'payment_date' => null,
        'payment_method' => null,
        'receipt_number' => null
    ],
    [
        'id' => 'FINE-0002',
        'resident_id' => 'R-0002',
        'resident_name' => 'Robert Harris',
        'room' => 'Room 315',
        'building' => 'Building B',
        'fine_amount' => 50,
        'issue_date' => '2025-04-15',
        'due_date' => '2025-04-30',
        'status' => 'Paid',
        'reason' => 'Room Damage',
        'description' => 'Damage to room door',
        'payment_date' => '2025-04-20',
        'payment_method' => 'Credit Card',
        'receipt_number' => 'RCP-82345'
    ],
    [
        'id' => 'FINE-0003',
        'resident_id' => 'R-0001',
        'resident_name' => 'Jessica Davis',
        'room' => 'Room 201',
        'building' => 'Building A',
        'fine_amount' => 15,
        'issue_date' => '2025-04-12',
        'due_date' => '2025-04-27',
        'status' => 'Paid',
        'reason' => 'Lost Key',
        'description' => 'Replacement for lost room key',
        'payment_date' => '2025-04-15',
        'payment_method' => 'Cash',
        'receipt_number' => 'RCP-81256'
    ],
    [
        'id' => 'FINE-0004',
        'resident_id' => 'R-0006',
        'resident_name' => 'Michael Johnson',
        'room' => 'Room 210',
        'building' => 'Building A',
        'fine_amount' => 30,
        'issue_date' => '2025-04-08',
        'due_date' => '2025-04-23',
        'status' => 'Pending Review',
        'reason' => 'Noise Violation',
        'description' => 'Multiple complaints about loud music after quiet hours',
        'payment_date' => null,
        'payment_method' => null,
        'receipt_number' => null
    ],
    [
        'id' => 'FINE-0005',
        'resident_id' => 'R-0003',
        'resident_name' => 'Amanda Miller',
        'room' => 'Room 107',
        'building' => 'Building A',
        'fine_amount' => 40,
        'issue_date' => '2025-04-18',
        'due_date' => '2025-05-03',
        'status' => 'Overdue',
        'reason' => 'Unauthorized Guest',
        'description' => 'Long-term guest without proper registration',
        'payment_date' => null,
        'payment_method' => null,
        'receipt_number' => null
    ],
    [
        'id' => 'FINE-0006',
        'resident_id' => 'R-0005',
        'resident_name' => 'Emma Thompson',
        'room' => 'Room 304',
        'building' => 'Building A',
        'fine_amount' => 20,
        'issue_date' => '2025-04-20',
        'due_date' => '2025-05-05',
        'status' => 'Waived',
        'reason' => 'Late Check-in',
        'description' => 'Returning after curfew',
        'payment_date' => null,
        'payment_method' => null,
        'receipt_number' => null
    ]
];

// Sample fine categories
$fineCategories = [
    [
        'category' => 'Late Payment',
        'amount' => 25,
        'description' => 'Fine for rental payments more than 5 days late'
    ],
    [
        'category' => 'Room Damage',
        'amount' => 50,
        'description' => 'Fine for damage to room or furniture beyond normal wear and tear'
    ],
    [
        'category' => 'Lost Key',
        'amount' => 15,
        'description' => 'Replacement cost for lost room or building keys'
    ],
    [
        'category' => 'Noise Violation',
        'amount' => 30,
        'description' => 'Fine for excessive noise during quiet hours (10 PM - 7 AM)'
    ],
    [
        'category' => 'Unauthorized Guest',
        'amount' => 40,
        'description' => 'Fine for having overnight guests without proper registration'
    ],
    [
        'category' => 'Late Check-in',
        'amount' => 20,
        'description' => 'Fine for returning to dormitory after curfew without prior approval'
    ]
];

// Calculate statistics
$totalFines = array_sum(array_column($fines, 'fine_amount'));
$paidFines = array_sum(array_map(function($fine) {
    return $fine['status'] === 'Paid' ? $fine['fine_amount'] : 0;
}, $fines));
$unpaidFines = array_sum(array_map(function($fine) {
    return ($fine['status'] === 'Unpaid' || $fine['status'] === 'Overdue') ? $fine['fine_amount'] : 0;
}, $fines));
$pendingFines = array_sum(array_map(function($fine) {
    return $fine['status'] === 'Pending Review' ? $fine['fine_amount'] : 0;
}, $fines));
$waivedFines = array_sum(array_map(function($fine) {
    return $fine['status'] === 'Waived' ? $fine['fine_amount'] : 0;
}, $fines));

$paidCount = count(array_filter($fines, function($fine) {
    return $fine['status'] === 'Paid';
}));
$unpaidCount = count(array_filter($fines, function($fine) {
    return $fine['status'] === 'Unpaid';
}));
$overdueCount = count(array_filter($fines, function($fine) {
    return $fine['status'] === 'Overdue';
}));
$pendingCount = count(array_filter($fines, function($fine) {
    return $fine['status'] === 'Pending Review';
}));
$waivedCount = count(array_filter($fines, function($fine) {
    return $fine['status'] === 'Waived';
}));

// Include header
include BASE_PATH . '/includes/header.php';
?>

<!-- Page Header -->
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Fines Management</h1>
        <p class="mt-1 text-sm text-gray-500">Issue, track, and collect fines for dormitory violations</p>
    </div>
    <div class="flex space-x-2">
        <button class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-md flex items-center space-x-2">
            <i class="ri-download-line"></i>
            <span>Export</span>
        </button>
        <button class="bg-primary text-white px-4 py-2 rounded-md flex items-center space-x-2">
            <i class="ri-add-line"></i>
            <span>Issue Fine</span>
        </button>
    </div>
</div>

<!-- Financial Overview -->
<div class="mb-6">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded shadow p-4 border-l-4 border-primary">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Fines</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo formatCurrency($totalFines); ?></p>
                </div>
                <div class="w-10 h-10 rounded-full bg-primary bg-opacity-10 flex items-center justify-center text-primary">
                    <i class="ri-bill-line ri-lg"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded shadow p-4 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Collected Fines</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo formatCurrency($paidFines); ?></p>
                </div>
                <div class="w-10 h-10 rounded-full bg-green-500 bg-opacity-10 flex items-center justify-center text-green-500">
                    <i class="ri-checkbox-circle-line ri-lg"></i>
                </div>
            </div>
            <div class="mt-2 flex items-center text-xs">
                <span class="text-green-500 flex items-center">
                    <i class="ri-arrow-up-s-line"></i> <?php echo round($paidFines / ($totalFines - $waivedFines) * 100); ?>%
                </span>
                <span class="ml-1 text-gray-500">collection rate</span>
            </div>
        </div>
        <div class="bg-white rounded shadow p-4 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Outstanding Fines</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo formatCurrency($unpaidFines); ?></p>
                </div>
                <div class="w-10 h-10 rounded-full bg-red-500 bg-opacity-10 flex items-center justify-center text-red-500">
                    <i class="ri-time-line ri-lg"></i>
                </div>
            </div>
            <div class="mt-2 flex items-center text-xs">
                <span class="text-red-500 flex items-center">
                    <i class="ri-arrow-down-s-line"></i> <?php echo round($unpaidFines / ($totalFines - $waivedFines) * 100); ?>%
                </span>
                <span class="ml-1 text-gray-500">of total fines</span>
            </div>
        </div>
        <div class="bg-white rounded shadow p-4 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Pending Review</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo formatCurrency($pendingFines); ?></p>
                </div>
                <div class="w-10 h-10 rounded-full bg-yellow-500 bg-opacity-10 flex items-center justify-center text-yellow-500">
                    <i class="ri-error-warning-line ri-lg"></i>
                </div>
            </div>
            <div class="mt-2 flex items-center text-xs">
                <span class="text-yellow-500 flex items-center">
                    <i class="ri-arrow-right-s-line"></i> Requires attention
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Fines Status Chart and Categories -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Fines Status Chart -->
    <div class="bg-white rounded shadow p-4">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">Fines Status Distribution</h2>
        </div>
        <div id="paymentStatusChart" data-chart='{"legends":["Paid","Unpaid","Overdue","Pending Review","Waived"],"data":[{"value":<?php echo $paidCount; ?>,"name":"Paid","itemStyle":{"color":"#10B981"}},{"value":<?php echo $unpaidCount; ?>,"name":"Unpaid","itemStyle":{"color":"#FBBF24"}},{"value":<?php echo $overdueCount; ?>,"name":"Overdue","itemStyle":{"color":"#EF4444"}},{"value":<?php echo $pendingCount; ?>,"name":"Pending Review","itemStyle":{"color":"#6366F1"}},{"value":<?php echo $waivedCount; ?>,"name":"Waived","itemStyle":{"color":"#9CA3AF"}}]}' class="h-64"></div>
    </div>
    <!-- Fine Categories -->
    <div class="bg-white rounded shadow p-4">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">Fine Categories</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Violation
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Amount
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Description
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($fineCategories as $category): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <?php echo $category['category']; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">
                                <?php echo formatCurrency($category['amount']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-normal text-sm text-gray-500">
                                <?php echo $category['description']; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Fines Records -->
<div class="bg-white shadow rounded-md overflow-hidden mb-6">
    <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center">
        <h2 class="text-lg font-semibold text-gray-900">Fine Records</h2>
        <div class="flex items-center space-x-2">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="ri-search-line text-gray-400"></i>
                </div>
                <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary focus:border-primary block w-full pl-10 p-2" placeholder="Search fines...">
            </div>
            <button class="p-2 text-gray-500 rounded-lg hover:bg-gray-100">
                <i class="ri-filter-3-line"></i>
            </button>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Fine ID
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Resident
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Room
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Amount
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Reason
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Due Date
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($fines as $fine): ?>
                    <?php 
                    $statusClass = '';
                    $statusBg = '';
                    
                    switch ($fine['status']) {
                        case 'Paid':
                            $statusClass = 'text-green-800';
                            $statusBg = 'bg-green-100';
                            break;
                        case 'Unpaid':
                            $statusClass = 'text-yellow-800';
                            $statusBg = 'bg-yellow-100';
                            break;
                        case 'Overdue':
                            $statusClass = 'text-red-800';
                            $statusBg = 'bg-red-100';
                            break;
                        case 'Pending Review':
                            $statusClass = 'text-indigo-800';
                            $statusBg = 'bg-indigo-100';
                            break;
                        case 'Waived':
                            $statusClass = 'text-gray-800';
                            $statusBg = 'bg-gray-100';
                            break;
                    }
                    ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $fine['id']; ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-primary bg-opacity-10 flex items-center justify-center text-primary">
                                    <span class="font-medium"><?php echo getInitials($fine['resident_name']); ?></span>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900"><?php echo $fine['resident_name']; ?></div>
                                    <div class="text-sm text-gray-500"><?php echo $fine['resident_id']; ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900"><?php echo $fine['room']; ?></div>
                            <div class="text-sm text-gray-500"><?php echo $fine['building']; ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                            <?php echo formatCurrency($fine['fine_amount']); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900"><?php echo $fine['reason']; ?></div>
                            <div class="text-xs text-gray-500 max-w-xs truncate"><?php echo $fine['description']; ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo formatDate($fine['due_date']); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $statusBg; ?> <?php echo $statusClass; ?>">
                                <?php echo $fine['status']; ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex space-x-2 justify-end">
                                <?php if ($fine['status'] !== 'Paid' && $fine['status'] !== 'Waived'): ?>
                                <button class="text-primary hover:text-indigo-900">
                                    <i class="ri-money-dollar-circle-line"></i>
                                </button>
                                <button class="text-yellow-600 hover:text-yellow-900">
                                    <i class="ri-edit-line"></i>
                                </button>
                                <?php endif; ?>
                                <button class="text-blue-600 hover:text-blue-900">
                                    <i class="ri-file-list-line"></i>
                                </button>
                                <button class="text-green-600 hover:text-green-900">
                                    <i class="ri-mail-send-line"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="px-4 py-3 bg-gray-50 flex items-center justify-between border-t border-gray-200 sm:px-6">
        <div class="flex-1 flex justify-between sm:hidden">
            <button class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                Previous
            </button>
            <button class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                Next
            </button>
        </div>
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-700">
                    Showing <span class="font-medium">1</span> to <span class="font-medium">6</span> of <span class="font-medium">6</span> results
                </p>
            </div>
            <div>
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                    <button class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                        <span class="sr-only">Previous</span>
                        <i class="ri-arrow-left-s-line"></i>
                    </button>
                    <button class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-primary text-sm font-medium text-white hover:bg-primary-dark">
                        1
                    </button>
                    <button class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                        <span class="sr-only">Next</span>
                        <i class="ri-arrow-right-s-line"></i>
                    </button>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- Fine Policies -->
<div class="bg-white rounded shadow mb-6">
    <div class="px-4 py-3 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900">Fine Policies</h2>
    </div>
    <div class="p-4">
        <div class="mb-4">
            <h3 class="text-md font-semibold text-gray-900 mb-2">General Fine Policies</h3>
            <ul class="list-disc pl-5 text-sm text-gray-600 space-y-1">
                <li>All fines must be paid within 15 days of issuance</li>
                <li>Unpaid fines will be added to the resident's account and may affect clearance status</li>
                <li>Residents have the right to appeal any fine within 7 days of issuance</li>
                <li>Repeat violations may result in increased fine amounts or disciplinary action</li>
                <li>All fine payments must be made through the approved payment methods</li>
            </ul>
        </div>
        <div>
            <h3 class="text-md font-semibold text-gray-900 mb-2">Fine Appeal Process</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="p-3 bg-gray-50 rounded-md">
                    <div class="flex items-center mb-2">
                        <div class="w-7 h-7 rounded-full bg-primary bg-opacity-10 flex items-center justify-center text-primary mr-2">
                            <span class="font-medium">1</span>
                        </div>
                        <p class="text-sm font-medium text-gray-900">Submit Appeal Form</p>
                    </div>
                    <p class="text-xs text-gray-600">Complete and submit the fine appeal form within 7 days of fine issuance</p>
                </div>
                <div class="p-3 bg-gray-50 rounded-md">
                    <div class="flex items-center mb-2">
                        <div class="w-7 h-7 rounded-full bg-primary bg-opacity-10 flex items-center justify-center text-primary mr-2">
                            <span class="font-medium">2</span>
                        </div>
                        <p class="text-sm font-medium text-gray-900">Appeal Review</p>
                    </div>
                    <p class="text-xs text-gray-600">Dormitory administration will review the appeal and supporting documentation</p>
                </div>
                <div class="p-3 bg-gray-50 rounded-md">
                    <div class="flex items-center mb-2">
                        <div class="w-7 h-7 rounded-full bg-primary bg-opacity-10 flex items-center justify-center text-primary mr-2">
                            <span class="font-medium">3</span>
                        </div>
                        <p class="text-sm font-medium text-gray-900">Decision Notification</p>
                    </div>
                    <p class="text-xs text-gray-600">Resident will be notified of the decision within 10 days of appeal submission</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include BASE_PATH . '/includes/footer.php';
?>
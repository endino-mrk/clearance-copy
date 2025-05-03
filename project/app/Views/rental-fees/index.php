<?php
// Include helper functions
include_once BASE_PATH . '/app/helpers.php';

// Set page title
$pageTitle = 'Rental Fees Management';

// Enable charts for this page
$useCharts = true;

// Sample data for rental fees
$rentalFees = [
    [
        'id' => 'FEE-0001',
        'resident_id' => 'R-0001',
        'resident_name' => 'Jessica Davis',
        'room' => 'Room 201',
        'building' => 'Building A',
        'fee_amount' => 450,
        'due_date' => '2025-05-01',
        'status' => 'Paid',
        'payment_date' => '2025-04-25',
        'payment_method' => 'Credit Card',
        'receipt_number' => 'RCP-83921'
    ],
    [
        'id' => 'FEE-0002',
        'resident_id' => 'R-0002',
        'resident_name' => 'Robert Harris',
        'room' => 'Room 315',
        'building' => 'Building B',
        'fee_amount' => 500,
        'due_date' => '2025-05-01',
        'status' => 'Unpaid',
        'payment_date' => null,
        'payment_method' => null,
        'receipt_number' => null
    ],
    [
        'id' => 'FEE-0003',
        'resident_id' => 'R-0003',
        'resident_name' => 'Amanda Miller',
        'room' => 'Room 107',
        'building' => 'Building A',
        'fee_amount' => 450,
        'due_date' => '2025-05-01',
        'status' => 'Paid',
        'payment_date' => '2025-04-20',
        'payment_method' => 'Bank Transfer',
        'receipt_number' => 'RCP-78542'
    ],
    [
        'id' => 'FEE-0004',
        'resident_id' => 'R-0004',
        'resident_name' => 'Kevin Wilson',
        'room' => 'Room 422',
        'building' => 'Building C',
        'fee_amount' => 550,
        'due_date' => '2025-05-01',
        'status' => 'Overdue',
        'payment_date' => null,
        'payment_method' => null,
        'receipt_number' => null
    ],
    [
        'id' => 'FEE-0005',
        'resident_id' => 'R-0005',
        'resident_name' => 'Emma Thompson',
        'room' => 'Room 304',
        'building' => 'Building A',
        'fee_amount' => 450,
        'due_date' => '2025-05-01',
        'status' => 'Partially Paid',
        'payment_date' => '2025-04-27',
        'payment_method' => 'Cash',
        'receipt_number' => 'RCP-85634'
    ],
    [
        'id' => 'FEE-0006',
        'resident_id' => 'R-0006',
        'resident_name' => 'Michael Johnson',
        'room' => 'Room 210',
        'building' => 'Building A',
        'fee_amount' => 450,
        'due_date' => '2025-05-01',
        'status' => 'Paid',
        'payment_date' => '2025-04-15',
        'payment_method' => 'Bank Transfer',
        'receipt_number' => 'RCP-76921'
    ]
];

// Sample fee types for different room categories
$feeCategories = [
    [
        'category' => 'Standard Single',
        'buildings' => ['A', 'B', 'C'],
        'monthly_fee' => 450,
        'description' => 'Single occupancy with shared bathroom'
    ],
    [
        'category' => 'Deluxe Single',
        'buildings' => ['B', 'C'],
        'monthly_fee' => 500,
        'description' => 'Single occupancy with private bathroom'
    ],
    [
        'category' => 'Premium Single',
        'buildings' => ['C'],
        'monthly_fee' => 550,
        'description' => 'Larger room with private bathroom and mini kitchen'
    ],
    [
        'category' => 'Double Room',
        'buildings' => ['A', 'B'],
        'monthly_fee' => 350,
        'description' => 'Shared room with two beds, shared bathroom'
    ]
];

// Calculate statistics
$totalFees = array_sum(array_column($rentalFees, 'fee_amount'));
$paidFees = array_sum(array_map(function($fee) {
    return $fee['status'] === 'Paid' ? $fee['fee_amount'] : 0;
}, $rentalFees));
$partiallyPaidFees = array_sum(array_map(function($fee) {
    return $fee['status'] === 'Partially Paid' ? $fee['fee_amount'] * 0.5 : 0; // Assuming 50% paid
}, $rentalFees));
$pendingFees = $totalFees - $paidFees - $partiallyPaidFees;

$paidCount = count(array_filter($rentalFees, function($fee) {
    return $fee['status'] === 'Paid';
}));
$unpaidCount = count(array_filter($rentalFees, function($fee) {
    return $fee['status'] === 'Unpaid';
}));
$overdueCount = count(array_filter($rentalFees, function($fee) {
    return $fee['status'] === 'Overdue';
}));
$partialCount = count(array_filter($rentalFees, function($fee) {
    return $fee['status'] === 'Partially Paid';
}));

// Include header
include BASE_PATH . '/app/Views/components/header.php';
?>

<!-- Page Header -->
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Rental Fees Management</h1>
        <p class="mt-1 text-sm text-gray-500">Track, collect, and manage dormitory rental fees</p>
    </div>
    <div class="flex space-x-2">
        <button class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-md flex items-center space-x-2">
            <i class="ri-download-line"></i>
            <span>Export</span>
        </button>
        <button class="bg-primary text-white px-4 py-2 rounded-md flex items-center space-x-2">
            <i class="ri-add-line"></i>
            <span>Record Payment</span>
        </button>
    </div>
</div>

<!-- Financial Overview -->
<div class="mb-6">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded shadow p-4 border-l-4 border-primary">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Fees (May 2025)</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo formatCurrency($totalFees); ?></p>
                </div>
                <div class="w-10 h-10 rounded-full bg-primary bg-opacity-10 flex items-center justify-center text-primary">
                    <i class="ri-money-dollar-circle-line ri-lg"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded shadow p-4 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Collected Fees</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo formatCurrency($paidFees + $partiallyPaidFees); ?></p>
                </div>
                <div class="w-10 h-10 rounded-full bg-green-500 bg-opacity-10 flex items-center justify-center text-green-500">
                    <i class="ri-checkbox-circle-line ri-lg"></i>
                </div>
            </div>
            <div class="mt-2 flex items-center text-xs">
                <span class="text-green-500 flex items-center">
                    <i class="ri-arrow-up-s-line"></i> <?php echo round(($paidFees + $partiallyPaidFees) / $totalFees * 100); ?>%
                </span>
                <span class="ml-1 text-gray-500">collection rate</span>
            </div>
        </div>
        <div class="bg-white rounded shadow p-4 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Outstanding Fees</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo formatCurrency($pendingFees); ?></p>
                </div>
                <div class="w-10 h-10 rounded-full bg-red-500 bg-opacity-10 flex items-center justify-center text-red-500">
                    <i class="ri-time-line ri-lg"></i>
                </div>
            </div>
            <div class="mt-2 flex items-center text-xs">
                <span class="text-red-500 flex items-center">
                    <i class="ri-arrow-down-s-line"></i> <?php echo round($pendingFees / $totalFees * 100); ?>%
                </span>
                <span class="ml-1 text-gray-500">of total fees</span>
            </div>
        </div>
        <div class="bg-white rounded shadow p-4 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Overdue Payments</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo $overdueCount; ?></p>
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

<!-- Payment Status Chart and Room Fee Categories -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Payment Status Chart -->
    <div class="bg-white rounded shadow p-4">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">Payment Status Distribution</h2>
        </div>
        <div id="paymentStatusChart" data-chart='{"legends":["Paid","Partially Paid","Unpaid","Overdue"],"data":[{"value":<?php echo $paidCount; ?>,"name":"Paid","itemStyle":{"color":"#10B981"}},{"value":<?php echo $partialCount; ?>,"name":"Partially Paid","itemStyle":{"color":"#3B82F6"}},{"value":<?php echo $unpaidCount; ?>,"name":"Unpaid","itemStyle":{"color":"#FBBF24"}},{"value":<?php echo $overdueCount; ?>,"name":"Overdue","itemStyle":{"color":"#EF4444"}}]}' class="h-64"></div>
    </div>
    <!-- Room Fee Categories -->
    <div class="bg-white rounded shadow p-4">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">Room Fee Categories</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Room Category
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Buildings
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Monthly Fee
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Description
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($feeCategories as $category): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <?php echo $category['category']; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php 
                                $buildingList = '';
                                foreach ($category['buildings'] as $key => $building) {
                                    $buildingList .= 'Building ' . $building;
                                    if ($key < count($category['buildings']) - 1) {
                                        $buildingList .= ', ';
                                    }
                                }
                                echo $buildingList;
                                ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">
                                <?php echo formatCurrency($category['monthly_fee']); ?>
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

<!-- Rental Fees Records -->
<div class="bg-white shadow rounded-md overflow-hidden mb-6">
    <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center">
        <h2 class="text-lg font-semibold text-gray-900">May 2025 Fees</h2>
        <div class="flex items-center space-x-2">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="ri-search-line text-gray-400"></i>
                </div>
                <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary focus:border-primary block w-full pl-10 p-2" placeholder="Search residents...">
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
                        Fee ID
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
                        Due Date
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Payment Details
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($rentalFees as $fee): ?>
                    <?php 
                    list($statusClass, $statusBg) = getPaymentStatusClass($fee['status']);
                    ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $fee['id']; ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-primary bg-opacity-10 flex items-center justify-center text-primary">
                                    <span class="font-medium"><?php echo getInitials($fee['resident_name']); ?></span>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900"><?php echo $fee['resident_name']; ?></div>
                                    <div class="text-sm text-gray-500"><?php echo $fee['resident_id']; ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900"><?php echo $fee['room']; ?></div>
                            <div class="text-sm text-gray-500"><?php echo $fee['building']; ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                            <?php echo formatCurrency($fee['fee_amount']); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo formatDate($fee['due_date']); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $statusBg; ?> <?php echo $statusClass; ?>">
                                <?php echo $fee['status']; ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php if (!empty($fee['payment_date'])): ?>
                                <div class="text-sm text-gray-900">
                                    <?php echo formatDate($fee['payment_date']); ?>
                                </div>
                                <div class="text-sm text-gray-500">
                                    <?php echo $fee['payment_method']; ?> (<?php echo $fee['receipt_number']; ?>)
                                </div>
                            <?php else: ?>
                                <div class="text-sm text-gray-500">No payment recorded</div>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex space-x-2 justify-end">
                                <?php if ($fee['status'] != 'Paid'): ?>
                                <button class="text-primary hover:text-indigo-900">
                                    <i class="ri-money-dollar-circle-line"></i>
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
                    Showing <span class="font-medium">1</span> to <span class="font-medium">6</span> of <span class="font-medium">124</span> results
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
                    <button class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                        2
                    </button>
                    <button class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                        3
                    </button>
                    <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                        ...
                    </span>
                    <button class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                        21
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

<!-- Payment Instructions -->
<div class="bg-white rounded shadow mb-6">
    <div class="px-4 py-3 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900">Payment Methods</h2>
    </div>
    <div class="p-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="p-4 bg-gray-50 rounded-md">
            <div class="flex items-center mb-2">
                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 mr-2">
                    <i class="ri-bank-card-line"></i>
                </div>
                <h3 class="text-md font-semibold text-gray-900">Credit/Debit Card</h3>
            </div>
            <p class="text-sm text-gray-600 mb-2">Pay online through the resident portal using a credit or debit card. Payment is processed immediately.</p>
            <button class="text-sm text-primary font-medium hover:text-indigo-700">View Instructions</button>
        </div>
        <div class="p-4 bg-gray-50 rounded-md">
            <div class="flex items-center mb-2">
                <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-green-600 mr-2">
                    <i class="ri-bank-line"></i>
                </div>
                <h3 class="text-md font-semibold text-gray-900">Bank Transfer</h3>
            </div>
            <p class="text-sm text-gray-600 mb-2">Transfer payment directly to our bank account. Include resident ID in the transfer details.</p>
            <button class="text-sm text-primary font-medium hover:text-indigo-700">View Account Details</button>
        </div>
        <div class="p-4 bg-gray-50 rounded-md">
            <div class="flex items-center mb-2">
                <div class="w-8 h-8 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-600 mr-2">
                    <i class="ri-money-dollar-box-line"></i>
                </div>
                <h3 class="text-md font-semibold text-gray-900">Cash Payment</h3>
            </div>
            <p class="text-sm text-gray-600 mb-2">Pay in cash at the dormitory office during business hours (8 AM - 5 PM, Monday to Friday).</p>
            <button class="text-sm text-primary font-medium hover:text-indigo-700">View Office Location</button>
        </div>
    </div>
</div>

<?php
// Include footer
include BASE_PATH . '/app/Views/components/footer.php';
?>
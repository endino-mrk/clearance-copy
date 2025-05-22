<?php
// Include helper functions
include_once __DIR__ . '/../../../includes/functions.php';

// Set page title
$pageTitle = 'Payment History';

// Enable charts for this page
$useCharts = true;

// Sample data for payment history
$payments = [
    [
        'id' => 'PMT-0001',
        'resident_id' => 'R-0001',
        'resident_name' => 'Jessica Davis',
        'room' => 'Room 201',
        'building' => 'Building A',
        'amount' => 450,
        'payment_date' => '2025-04-25',
        'due_date' => '2025-05-01',
        'payment_method' => 'Credit Card',
        'receipt_number' => 'RCP-83921',
        'payment_type' => 'Rent',
        'status' => 'Completed',
        'notes' => 'May 2025 rent payment'
    ],
    [
        'id' => 'PMT-0002',
        'resident_id' => 'R-0003',
        'resident_name' => 'Amanda Miller',
        'room' => 'Room 107',
        'building' => 'Building A',
        'amount' => 450,
        'payment_date' => '2025-04-20',
        'due_date' => '2025-05-01',
        'payment_method' => 'Bank Transfer',
        'receipt_number' => 'RCP-78542',
        'payment_type' => 'Rent',
        'status' => 'Completed',
        'notes' => 'May 2025 rent payment'
    ],
    [
        'id' => 'PMT-0003',
        'resident_id' => 'R-0005',
        'resident_name' => 'Emma Thompson',
        'room' => 'Room 304',
        'building' => 'Building A',
        'amount' => 225,
        'payment_date' => '2025-04-27',
        'due_date' => '2025-05-01',
        'payment_method' => 'Cash',
        'receipt_number' => 'RCP-85634',
        'payment_type' => 'Rent',
        'status' => 'Partially Paid',
        'notes' => 'Partial payment for May 2025 rent'
    ],
    [
        'id' => 'PMT-0004',
        'resident_id' => 'R-0006',
        'resident_name' => 'Michael Johnson',
        'room' => 'Room 210',
        'building' => 'Building A',
        'amount' => 450,
        'payment_date' => '2025-04-15',
        'due_date' => '2025-05-01',
        'payment_method' => 'Bank Transfer',
        'receipt_number' => 'RCP-76921',
        'payment_type' => 'Rent',
        'status' => 'Completed',
        'notes' => 'May 2025 rent payment'
    ],
    [
        'id' => 'PMT-0005',
        'resident_id' => 'R-0002',
        'resident_name' => 'Robert Harris',
        'room' => 'Room 315',
        'building' => 'Building B',
        'amount' => 50,
        'payment_date' => '2025-04-20',
        'due_date' => '2025-04-30',
        'payment_method' => 'Credit Card',
        'receipt_number' => 'RCP-82345',
        'payment_type' => 'Fine',
        'status' => 'Completed',
        'notes' => 'Payment for room damage fine'
    ],
    [
        'id' => 'PMT-0006',
        'resident_id' => 'R-0001',
        'resident_name' => 'Jessica Davis',
        'room' => 'Room 201',
        'building' => 'Building A',
        'amount' => 15,
        'payment_date' => '2025-04-15',
        'due_date' => '2025-04-27',
        'payment_method' => 'Cash',
        'receipt_number' => 'RCP-81256',
        'payment_type' => 'Fine',
        'status' => 'Completed',
        'notes' => 'Payment for lost key fine'
    ],
    [
        'id' => 'PMT-0007',
        'resident_id' => 'R-0005',
        'resident_name' => 'Emma Thompson',
        'room' => 'Room 304',
        'building' => 'Building A',
        'amount' => 450,
        'payment_date' => '2025-03-28',
        'due_date' => '2025-04-01',
        'payment_method' => 'Bank Transfer',
        'receipt_number' => 'RCP-74523',
        'payment_type' => 'Rent',
        'status' => 'Completed',
        'notes' => 'April 2025 rent payment'
    ],
    [
        'id' => 'PMT-0008',
        'resident_id' => 'R-0003',
        'resident_name' => 'Amanda Miller',
        'room' => 'Room 107',
        'building' => 'Building A',
        'amount' => 450,
        'payment_date' => '2025-03-25',
        'due_date' => '2025-04-01',
        'payment_method' => 'Credit Card',
        'receipt_number' => 'RCP-73921',
        'payment_type' => 'Rent',
        'status' => 'Completed',
        'notes' => 'April 2025 rent payment'
    ]
];

// Calculate statistics
$totalPayments = count($payments);
$totalAmount = array_sum(array_column($payments, 'amount'));

// Payment types breakdown
$rentPayments = array_filter($payments, function($payment) {
    return $payment['payment_type'] === 'Rent';
});
$finePayments = array_filter($payments, function($payment) {
    return $payment['payment_type'] === 'Fine';
});

$rentAmount = array_sum(array_column($rentPayments, 'amount'));
$fineAmount = array_sum(array_column($finePayments, 'amount'));

// Payment methods breakdown
$creditCardPayments = array_filter($payments, function($payment) {
    return $payment['payment_method'] === 'Credit Card';
});
$bankTransferPayments = array_filter($payments, function($payment) {
    return $payment['payment_method'] === 'Bank Transfer';
});
$cashPayments = array_filter($payments, function($payment) {
    return $payment['payment_method'] === 'Cash';
});

$creditCardAmount = array_sum(array_column($creditCardPayments, 'amount'));
$bankTransferAmount = array_sum(array_column($bankTransferPayments, 'amount'));
$cashAmount = array_sum(array_column($cashPayments, 'amount'));

// Payment status breakdown
$completedPayments = array_filter($payments, function($payment) {
    return $payment['status'] === 'Completed';
});
$partialPayments = array_filter($payments, function($payment) {
    return $payment['status'] === 'Partially Paid';
});

$completedAmount = array_sum(array_column($completedPayments, 'amount'));
$partialAmount = array_sum(array_column($partialPayments, 'amount'));

// Monthly payment data for chart
$monthlyData = [
    'Oct 2024' => 41500,
    'Nov 2024' => 42750,
    'Dec 2024' => 42000,
    'Jan 2025' => 43500,
    'Feb 2025' => 44250,
    'Mar 2025' => 45000,
    'Apr 2025' => 45500
];

// Include header
include '../../../includes/header.php';
?>

<!-- Page Header -->
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Payment History</h1>
        <p class="mt-1 text-sm text-gray-500">View and manage all dormitory payment transactions</p>
    </div>
    <div class="flex space-x-2">
        <div class="relative">
            <input type="month" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary focus:border-primary block w-full p-2.5" value="2025-04">
        </div>
        <button class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-md flex items-center space-x-2">
            <i class="ri-download-line"></i>
            <span>Export</span>
        </button>
    </div>
</div>

<!-- Payment Overview -->
<div class="mb-6">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded shadow p-4 border-l-4 border-primary">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Payments</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo $totalPayments; ?></p>
                </div>
                <div class="w-10 h-10 rounded-full bg-primary bg-opacity-10 flex items-center justify-center text-primary">
                    <i class="ri-bank-card-line ri-lg"></i>
                </div>
            </div>
            <div class="mt-2 flex items-center text-xs">
                <span class="text-primary flex items-center">
                    <i class="ri-arrow-right-s-line"></i> Apr 2025
                </span>
            </div>
        </div>
        <div class="bg-white rounded shadow p-4 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Amount Collected</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo formatCurrency($totalAmount); ?></p>
                </div>
                <div class="w-10 h-10 rounded-full bg-green-500 bg-opacity-10 flex items-center justify-center text-green-500">
                    <i class="ri-money-dollar-circle-line ri-lg"></i>
                </div>
            </div>
            <div class="mt-2 flex items-center text-xs">
                <span class="text-green-500 flex items-center">
                    <i class="ri-arrow-up-s-line"></i> <?php echo round(($totalAmount / 45000) * 100); ?>%
                </span>
                <span class="ml-1 text-gray-500">of projected amount</span>
            </div>
        </div>
        <div class="bg-white rounded shadow p-4 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Rent Payments</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo formatCurrency($rentAmount); ?></p>
                </div>
                <div class="w-10 h-10 rounded-full bg-blue-500 bg-opacity-10 flex items-center justify-center text-blue-500">
                    <i class="ri-home-line ri-lg"></i>
                </div>
            </div>
            <div class="mt-2 flex items-center text-xs">
                <span class="text-blue-500 flex items-center">
                    <i class="ri-arrow-right-s-line"></i> <?php echo count($rentPayments); ?> transactions
                </span>
            </div>
        </div>
        <div class="bg-white rounded shadow p-4 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Fine Payments</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo formatCurrency($fineAmount); ?></p>
                </div>
                <div class="w-10 h-10 rounded-full bg-purple-500 bg-opacity-10 flex items-center justify-center text-purple-500">
                    <i class="ri-bill-line ri-lg"></i>
                </div>
            </div>
            <div class="mt-2 flex items-center text-xs">
                <span class="text-purple-500 flex items-center">
                    <i class="ri-arrow-right-s-line"></i> <?php echo count($finePayments); ?> transactions
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Payment Trends Chart and Payment Method Breakdown -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Payment Trends Chart -->
    <div class="bg-white rounded shadow p-4">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">Monthly Payment Trends</h2>
        </div>
        <div id="paymentTrendsChart" class="h-64"></div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof echarts !== 'undefined') {
                    const paymentTrendsChart = echarts.init(document.getElementById('paymentTrendsChart'));
                    
                    const option = {
                        tooltip: {
                            trigger: 'axis',
                            formatter: '${c}'
                        },
                        grid: {
                            left: '3%',
                            right: '4%',
                            bottom: '3%',
                            containLabel: true
                        },
                        xAxis: {
                            type: 'category',
                            data: <?php echo json_encode(array_keys($monthlyData)); ?>
                        },
                        yAxis: {
                            type: 'value',
                            axisLabel: {
                                formatter: '${value}'
                            }
                        },
                        series: [
                            {
                                name: 'Total Payments',
                                type: 'line',
                                smooth: true,
                                lineStyle: {
                                    width: 3,
                                    color: '#4F46E5'
                                },
                                itemStyle: {
                                    color: '#4F46E5'
                                },
                                areaStyle: {
                                    color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                                        { offset: 0, color: 'rgba(79, 70, 229, 0.3)' },
                                        { offset: 1, color: 'rgba(79, 70, 229, 0.1)' }
                                    ])
                                },
                                data: <?php echo json_encode(array_values($monthlyData)); ?>
                            }
                        ]
                    };
                    
                    paymentTrendsChart.setOption(option);
                    
                    window.addEventListener('resize', function() {
                        paymentTrendsChart.resize();
                    });
                }
            });
        </script>
    </div>
    
    <!-- Payment Method Breakdown -->
    <div class="bg-white rounded shadow p-4">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">Payment Method Breakdown</h2>
        </div>
        <div id="paymentMethodChart" class="h-64"></div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof echarts !== 'undefined') {
                    const paymentMethodChart = echarts.init(document.getElementById('paymentMethodChart'));
                    
                    const option = {
                        tooltip: {
                            trigger: 'item',
                            formatter: '{b}: ${c} ({d}%)'
                        },
                        legend: {
                            orient: 'horizontal',
                            bottom: 0,
                            data: ['Credit Card', 'Bank Transfer', 'Cash']
                        },
                        series: [
                            {
                                name: 'Payment Method',
                                type: 'pie',
                                radius: ['40%', '70%'],
                                avoidLabelOverlap: false,
                                itemStyle: {
                                    borderRadius: 10,
                                    borderColor: '#fff',
                                    borderWidth: 2
                                },
                                label: {
                                    show: false,
                                    position: 'center'
                                },
                                emphasis: {
                                    label: {
                                        show: true,
                                        fontSize: '18',
                                        fontWeight: 'bold'
                                    }
                                },
                                labelLine: {
                                    show: false
                                },
                                data: [
                                    { value: <?php echo $creditCardAmount; ?>, name: 'Credit Card', itemStyle: { color: '#3B82F6' } },
                                    { value: <?php echo $bankTransferAmount; ?>, name: 'Bank Transfer', itemStyle: { color: '#10B981' } },
                                    { value: <?php echo $cashAmount; ?>, name: 'Cash', itemStyle: { color: '#F59E0B' } }
                                ]
                            }
                        ]
                    };
                    
                    paymentMethodChart.setOption(option);
                    
                    window.addEventListener('resize', function() {
                        paymentMethodChart.resize();
                    });
                }
            });
        </script>
    </div>
</div>

<!-- Payment Records -->
<div class="bg-white shadow rounded-md overflow-hidden mb-6">
    <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center">
        <h2 class="text-lg font-semibold text-gray-900">Payment Records</h2>
        <div class="flex items-center space-x-2">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="ri-search-line text-gray-400"></i>
                </div>
                <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary focus:border-primary block w-full pl-10 p-2" placeholder="Search payments...">
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
                        Payment ID
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Resident
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Amount
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Payment Date
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Payment Method
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Type
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
                <?php foreach ($payments as $payment): ?>
                    <?php 
                    $statusClass = '';
                    $statusBg = '';
                    
                    switch ($payment['status']) {
                        case 'Completed':
                            $statusClass = 'text-green-800';
                            $statusBg = 'bg-green-100';
                            break;
                        case 'Partially Paid':
                            $statusClass = 'text-blue-800';
                            $statusBg = 'bg-blue-100';
                            break;
                        case 'Pending':
                            $statusClass = 'text-yellow-800';
                            $statusBg = 'bg-yellow-100';
                            break;
                        case 'Failed':
                            $statusClass = 'text-red-800';
                            $statusBg = 'bg-red-100';
                            break;
                    }
                    
                    $typeClass = '';
                    $typeBg = '';
                    
                    switch ($payment['payment_type']) {
                        case 'Rent':
                            $typeClass = 'text-blue-800';
                            $typeBg = 'bg-blue-100';
                            break;
                        case 'Fine':
                            $typeClass = 'text-purple-800';
                            $typeBg = 'bg-purple-100';
                            break;
                    }
                    ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $payment['id']; ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-primary bg-opacity-10 flex items-center justify-center text-primary">
                                    <span class="font-medium"><?php echo getInitials($payment['resident_name']); ?></span>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900"><?php echo $payment['resident_name']; ?></div>
                                    <div class="text-sm text-gray-500"><?php echo $payment['room']; ?>, <?php echo $payment['building']; ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                            <?php echo formatCurrency($payment['amount']); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo formatDate($payment['payment_date']); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo $payment['payment_method']; ?>
                            <div class="text-xs text-gray-400"><?php echo $payment['receipt_number']; ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $typeBg; ?> <?php echo $typeClass; ?>">
                                <?php echo $payment['payment_type']; ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $statusBg; ?> <?php echo $statusClass; ?>">
                                <?php echo $payment['status']; ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex space-x-2 justify-end">
                                <button class="text-primary hover:text-indigo-900" title="View Receipt">
                                    <i class="ri-file-list-line"></i>
                                </button>
                                <button class="text-blue-600 hover:text-blue-900" title="Print Receipt">
                                    <i class="ri-printer-line"></i>
                                </button>
                                <button class="text-green-600 hover:text-green-900" title="Email Receipt">
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
                    Showing <span class="font-medium">1</span> to <span class="font-medium">8</span> of <span class="font-medium">8</span> results
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

<?php
// Include footer
include '/../../../includes/footer.php';
?>
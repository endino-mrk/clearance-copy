<?php
// Include helper functions
include_once BASE_PATH . '/app/helpers.php';

// Set page title
$pageTitle = 'Room Status';

// Enable charts for this page
$useCharts = true;

// Sample data for rooms
$rooms = [
    [
        'id' => 'RM-0001',
        'room_number' => '201',
        'building' => 'Building A',
        'floor' => '2',
        'type' => 'Standard Single',
        'capacity' => 1,
        'current_occupants' => 1,
        'monthly_fee' => 450,
        'status' => 'Occupied',
        'condition' => 'Good',
        'last_inspection' => '2025-03-15',
        'resident_id' => 'R-0001',
        'resident_name' => 'Jessica Davis'
    ],
    [
        'id' => 'RM-0002',
        'room_number' => '202',
        'building' => 'Building A',
        'floor' => '2',
        'type' => 'Standard Single',
        'capacity' => 1,
        'current_occupants' => 0,
        'monthly_fee' => 450,
        'status' => 'Vacant',
        'condition' => 'Excellent',
        'last_inspection' => '2025-04-10',
        'resident_id' => null,
        'resident_name' => null
    ],
    [
        'id' => 'RM-0003',
        'room_number' => '107',
        'building' => 'Building A',
        'floor' => '1',
        'type' => 'Standard Single',
        'capacity' => 1,
        'current_occupants' => 1,
        'monthly_fee' => 450,
        'status' => 'Occupied',
        'condition' => 'Good',
        'last_inspection' => '2025-03-20',
        'resident_id' => 'R-0003',
        'resident_name' => 'Amanda Miller'
    ],
    [
        'id' => 'RM-0004',
        'room_number' => '304',
        'building' => 'Building A',
        'floor' => '3',
        'type' => 'Standard Single',
        'capacity' => 1,
        'current_occupants' => 1,
        'monthly_fee' => 450,
        'status' => 'Occupied',
        'condition' => 'Good',
        'last_inspection' => '2025-03-18',
        'resident_id' => 'R-0005',
        'resident_name' => 'Emma Thompson'
    ],
    [
        'id' => 'RM-0005',
        'room_number' => '210',
        'building' => 'Building A',
        'floor' => '2',
        'type' => 'Standard Single',
        'capacity' => 1,
        'current_occupants' => 1,
        'monthly_fee' => 450,
        'status' => 'Occupied',
        'condition' => 'Fair',
        'last_inspection' => '2025-03-22',
        'resident_id' => 'R-0006',
        'resident_name' => 'Michael Johnson'
    ],
    [
        'id' => 'RM-0006',
        'room_number' => '315',
        'building' => 'Building B',
        'floor' => '3',
        'type' => 'Deluxe Single',
        'capacity' => 1,
        'current_occupants' => 1,
        'monthly_fee' => 500,
        'status' => 'Occupied',
        'condition' => 'Excellent',
        'last_inspection' => '2025-03-25',
        'resident_id' => 'R-0002',
        'resident_name' => 'Robert Harris'
    ],
    [
        'id' => 'RM-0007',
        'room_number' => '316',
        'building' => 'Building B',
        'floor' => '3',
        'type' => 'Deluxe Single',
        'capacity' => 1,
        'current_occupants' => 0,
        'monthly_fee' => 500,
        'status' => 'Under Maintenance',
        'condition' => 'Poor',
        'last_inspection' => '2025-04-05',
        'resident_id' => null,
        'resident_name' => null
    ],
    [
        'id' => 'RM-0008',
        'room_number' => '422',
        'building' => 'Building C',
        'floor' => '4',
        'type' => 'Premium Single',
        'capacity' => 1,
        'current_occupants' => 1,
        'monthly_fee' => 550,
        'status' => 'Occupied',
        'condition' => 'Good',
        'last_inspection' => '2025-03-12',
        'resident_id' => 'R-0004',
        'resident_name' => 'Kevin Wilson'
    ],
    [
        'id' => 'RM-0009',
        'room_number' => '108',
        'building' => 'Building A',
        'floor' => '1',
        'type' => 'Double Room',
        'capacity' => 2,
        'current_occupants' => 1,
        'monthly_fee' => 350,
        'status' => 'Partially Occupied',
        'condition' => 'Good',
        'last_inspection' => '2025-03-28',
        'resident_id' => 'R-0007',
        'resident_name' => 'Sarah Wilson'
    ],
    [
        'id' => 'RM-0010',
        'room_number' => '109',
        'building' => 'Building A',
        'floor' => '1',
        'type' => 'Double Room',
        'capacity' => 2,
        'current_occupants' => 2,
        'monthly_fee' => 350,
        'status' => 'Fully Occupied',
        'condition' => 'Good',
        'last_inspection' => '2025-03-30',
        'resident_id' => 'R-0008, R-0009',
        'resident_name' => 'David Brown, Lisa Chen'
    ]
];

// Sample maintenance records
$maintenanceRecords = [
    [
        'id' => 'MNT-0001',
        'room_id' => 'RM-0007',
        'room_number' => '316',
        'building' => 'Building B',
        'issue_type' => 'Plumbing',
        'description' => 'Leaking bathroom sink',
        'reported_date' => '2025-04-02',
        'status' => 'In Progress',
        'assigned_to' => 'John Smith',
        'estimated_completion' => '2025-04-15',
        'priority' => 'High'
    ],
    [
        'id' => 'MNT-0002',
        'room_id' => 'RM-0005',
        'room_number' => '210',
        'building' => 'Building A',
        'issue_type' => 'Electrical',
        'description' => 'Faulty light fixture',
        'reported_date' => '2025-04-10',
        'status' => 'Scheduled',
        'assigned_to' => 'Mike Brown',
        'estimated_completion' => '2025-04-18',
        'priority' => 'Medium'
    ],
    [
        'id' => 'MNT-0003',
        'room_id' => 'RM-0009',
        'room_number' => '108',
        'building' => 'Building A',
        'issue_type' => 'Furniture',
        'description' => 'Broken desk chair',
        'reported_date' => '2025-04-08',
        'status' => 'Completed',
        'assigned_to' => 'Sarah Johnson',
        'estimated_completion' => '2025-04-12',
        'actual_completion' => '2025-04-11',
        'priority' => 'Low'
    ]
];

// Calculate statistics
$totalRooms = count($rooms);
$occupiedRooms = count(array_filter($rooms, function($room) {
    return $room['status'] === 'Occupied' || $room['status'] === 'Fully Occupied';
}));
$partiallyOccupiedRooms = count(array_filter($rooms, function($room) {
    return $room['status'] === 'Partially Occupied';
}));
$vacantRooms = count(array_filter($rooms, function($room) {
    return $room['status'] === 'Vacant';
}));
$maintenanceRooms = count(array_filter($rooms, function($room) {
    return $room['status'] === 'Under Maintenance';
}));

$totalCapacity = array_sum(array_column($rooms, 'capacity'));
$currentOccupants = array_sum(array_column($rooms, 'current_occupants'));
$occupancyRate = round(($currentOccupants / $totalCapacity) * 100);

$buildingACount = count(array_filter($rooms, function($room) {
    return $room['building'] === 'Building A';
}));
$buildingBCount = count(array_filter($rooms, function($room) {
    return $room['building'] === 'Building B';
}));
$buildingCCount = count(array_filter($rooms, function($room) {
    return $room['building'] === 'Building C';
}));

// Room conditions
$excellentCondition = count(array_filter($rooms, function($room) {
    return $room['condition'] === 'Excellent';
}));
$goodCondition = count(array_filter($rooms, function($room) {
    return $room['condition'] === 'Good';
}));
$fairCondition = count(array_filter($rooms, function($room) {
    return $room['condition'] === 'Fair';
}));
$poorCondition = count(array_filter($rooms, function($room) {
    return $room['condition'] === 'Poor';
}));

// Include header
include BASE_PATH . '/app/Views/components/header.php';
?>

<!-- Page Header -->
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Room Status</h1>
        <p class="mt-1 text-sm text-gray-500">Monitor dormitory rooms, occupancy, and maintenance status</p>
    </div>
    <div class="flex space-x-2">
        <button class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-md flex items-center space-x-2">
            <i class="ri-building-line"></i>
            <span>Floor Plan</span>
        </button>
        <button class="bg-primary text-white px-4 py-2 rounded-md flex items-center space-x-2">
            <i class="ri-add-line"></i>
            <span>Add Room</span>
        </button>
    </div>
</div>

<!-- Room Status Overview -->
<div class="mb-6">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded shadow p-4 border-l-4 border-primary">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Rooms</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo $totalRooms; ?></p>
                </div>
                <div class="w-10 h-10 rounded-full bg-primary bg-opacity-10 flex items-center justify-center text-primary">
                    <i class="ri-home-line ri-lg"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded shadow p-4 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Occupancy Rate</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo $occupancyRate; ?>%</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-green-500 bg-opacity-10 flex items-center justify-center text-green-500">
                    <i class="ri-user-line ri-lg"></i>
                </div>
            </div>
            <div class="mt-2 flex items-center text-xs">
                <span class="text-green-500 flex items-center">
                    <i class="ri-user-line"></i> <?php echo $currentOccupants; ?>
                </span>
                <span class="ml-1 text-gray-500">of <?php echo $totalCapacity; ?> total capacity</span>
            </div>
        </div>
        <div class="bg-white rounded shadow p-4 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Vacant Rooms</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo $vacantRooms; ?></p>
                </div>
                <div class="w-10 h-10 rounded-full bg-yellow-500 bg-opacity-10 flex items-center justify-center text-yellow-500">
                    <i class="ri-door-open-line ri-lg"></i>
                </div>
            </div>
            <div class="mt-2 flex items-center text-xs">
                <span class="text-yellow-500 flex items-center">
                    <i class="ri-arrow-right-s-line"></i> Available for assignment
                </span>
            </div>
        </div>
        <div class="bg-white rounded shadow p-4 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Under Maintenance</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo $maintenanceRooms; ?></p>
                </div>
                <div class="w-10 h-10 rounded-full bg-red-500 bg-opacity-10 flex items-center justify-center text-red-500">
                    <i class="ri-tools-line ri-lg"></i>
                </div>
            </div>
            <div class="mt-2 flex items-center text-xs">
                <span class="text-red-500 flex items-center">
                    <i class="ri-error-warning-line"></i> Temporarily unavailable
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Room Status Distribution and Building Distribution -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Room Status Distribution -->
    <div class="bg-white rounded shadow p-4">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">Room Status Distribution</h2>
        </div>
        <div id="roomStatusChart" class="h-64"></div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof echarts !== 'undefined') {
                    const roomStatusChart = echarts.init(document.getElementById('roomStatusChart'));
                    
                    const option = {
                        tooltip: {
                            trigger: 'item',
                            formatter: '{b}: {c} ({d}%)'
                        },
                        legend: {
                            orient: 'horizontal',
                            bottom: 0,
                            data: ['Occupied', 'Partially Occupied', 'Vacant', 'Under Maintenance']
                        },
                        series: [
                            {
                                name: 'Room Status',
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
                                    { value: <?php echo $occupiedRooms; ?>, name: 'Occupied', itemStyle: { color: '#10B981' } },
                                    { value: <?php echo $partiallyOccupiedRooms; ?>, name: 'Partially Occupied', itemStyle: { color: '#3B82F6' } },
                                    { value: <?php echo $vacantRooms; ?>, name: 'Vacant', itemStyle: { color: '#F59E0B' } },
                                    { value: <?php echo $maintenanceRooms; ?>, name: 'Under Maintenance', itemStyle: { color: '#EF4444' } }
                                ]
                            }
                        ]
                    };
                    
                    roomStatusChart.setOption(option);
                    
                    window.addEventListener('resize', function() {
                        roomStatusChart.resize();
                    });
                }
            });
        </script>
    </div>
    
    <!-- Room Condition Distribution -->
    <div class="bg-white rounded shadow p-4">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">Room Condition Distribution</h2>
        </div>
        <div id="roomConditionChart" class="h-64"></div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof echarts !== 'undefined') {
                    const roomConditionChart = echarts.init(document.getElementById('roomConditionChart'));
                    
                    const option = {
                        tooltip: {
                            trigger: 'axis',
                            axisPointer: {
                                type: 'shadow'
                            }
                        },
                        grid: {
                            left: '3%',
                            right: '4%',
                            bottom: '10%',
                            containLabel: true
                        },
                        xAxis: [
                            {
                                type: 'category',
                                data: ['Excellent', 'Good', 'Fair', 'Poor'],
                                axisTick: {
                                    alignWithLabel: true
                                }
                            }
                        ],
                        yAxis: [
                            {
                                type: 'value'
                            }
                        ],
                        series: [
                            {
                                name: 'Room Count',
                                type: 'bar',
                                barWidth: '60%',
                                data: [
                                    {value: <?php echo $excellentCondition; ?>, itemStyle: {color: '#10B981'}},
                                    {value: <?php echo $goodCondition; ?>, itemStyle: {color: '#3B82F6'}},
                                    {value: <?php echo $fairCondition; ?>, itemStyle: {color: '#F59E0B'}},
                                    {value: <?php echo $poorCondition; ?>, itemStyle: {color: '#EF4444'}}
                                ]
                            }
                        ]
                    };
                    
                    roomConditionChart.setOption(option);
                    
                    window.addEventListener('resize', function() {
                        roomConditionChart.resize();
                    });
                }
            });
        </script>
    </div>
</div>

<!-- Room Records -->
<div class="bg-white shadow rounded-md overflow-hidden mb-6">
    <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center">
        <h2 class="text-lg font-semibold text-gray-900">Room Records</h2>
        <div class="flex items-center space-x-2">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="ri-search-line text-gray-400"></i>
                </div>
                <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary focus:border-primary block w-full pl-10 p-2" placeholder="Search rooms...">
            </div>
            <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary focus:border-primary block p-2.5">
                <option selected>All Buildings</option>
                <option>Building A</option>
                <option>Building B</option>
                <option>Building C</option>
            </select>
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
                        Room ID
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Location
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Type
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Occupancy
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Condition
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Resident
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($rooms as $room): ?>
                    <?php 
                    $statusClass = '';
                    $statusBg = '';
                    
                    switch ($room['status']) {
                        case 'Occupied':
                        case 'Fully Occupied':
                            $statusClass = 'text-green-800';
                            $statusBg = 'bg-green-100';
                            break;
                        case 'Partially Occupied':
                            $statusClass = 'text-blue-800';
                            $statusBg = 'bg-blue-100';
                            break;
                        case 'Vacant':
                            $statusClass = 'text-yellow-800';
                            $statusBg = 'bg-yellow-100';
                            break;
                        case 'Under Maintenance':
                            $statusClass = 'text-red-800';
                            $statusBg = 'bg-red-100';
                            break;
                    }
                    
                    $conditionClass = '';
                    $conditionBg = '';
                    
                    switch ($room['condition']) {
                        case 'Excellent':
                            $conditionClass = 'text-green-800';
                            $conditionBg = 'bg-green-100';
                            break;
                        case 'Good':
                            $conditionClass = 'text-blue-800';
                            $conditionBg = 'bg-blue-100';
                            break;
                        case 'Fair':
                            $conditionClass = 'text-yellow-800';
                            $conditionBg = 'bg-yellow-100';
                            break;
                        case 'Poor':
                            $conditionClass = 'text-red-800';
                            $conditionBg = 'bg-red-100';
                            break;
                    }
                    ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $room['id']; ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900"><?php echo $room['room_number']; ?></div>
                            <div class="text-sm text-gray-500"><?php echo $room['building']; ?>, Floor <?php echo $room['floor']; ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900"><?php echo $room['type']; ?></div>
                            <div class="text-sm text-gray-500"><?php echo formatCurrency($room['monthly_fee']); ?>/month</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo $room['current_occupants']; ?> / <?php echo $room['capacity']; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $statusBg; ?> <?php echo $statusClass; ?>">
                                <?php echo $room['status']; ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $conditionBg; ?> <?php echo $conditionClass; ?>">
                                <?php echo $room['condition']; ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php if ($room['resident_name']): ?>
                                <?php echo $room['resident_name']; ?>
                            <?php else: ?>
                                <span class="text-gray-400">None</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex space-x-2 justify-end">
                                <button class="text-primary hover:text-indigo-900" title="View Details">
                                    <i class="ri-eye-line"></i>
                                </button>
                                <button class="text-blue-600 hover:text-blue-900" title="Edit Room">
                                    <i class="ri-edit-line"></i>
                                </button>
                                <button class="text-orange-600 hover:text-orange-900" title="Report Issue">
                                    <i class="ri-error-warning-line"></i>
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
                    Showing <span class="font-medium">1</span> to <span class="font-medium">10</span> of <span class="font-medium">10</span> results
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

<!-- Maintenance Records -->
<div class="bg-white shadow rounded-md overflow-hidden mb-6">
    <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center">
        <h2 class="text-lg font-semibold text-gray-900">Maintenance Records</h2>
        <button class="bg-primary text-white px-4 py-2 rounded-md flex items-center space-x-2">
            <i class="ri-add-line"></i>
            <span>Report Issue</span>
        </button>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Ticket ID
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Room
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Issue
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Reported Date
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Assigned To
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Priority
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($maintenanceRecords as $record): ?>
                    <?php 
                    $statusClass = '';
                    $statusBg = '';
                    
                    switch ($record['status']) {
                        case 'Completed':
                            $statusClass = 'text-green-800';
                            $statusBg = 'bg-green-100';
                            break;
                        case 'In Progress':
                            $statusClass = 'text-blue-800';
                            $statusBg = 'bg-blue-100';
                            break;
                        case 'Scheduled':
                            $statusClass = 'text-yellow-800';
                            $statusBg = 'bg-yellow-100';
                            break;
                        case 'Pending':
                            $statusClass = 'text-gray-800';
                            $statusBg = 'bg-gray-100';
                            break;
                    }
                    
                    $priorityClass = '';
                    $priorityBg = '';
                    
                    switch ($record['priority']) {
                        case 'High':
                            $priorityClass = 'text-red-800';
                            $priorityBg = 'bg-red-100';
                            break;
                        case 'Medium':
                            $priorityClass = 'text-yellow-800';
                            $priorityBg = 'bg-yellow-100';
                            break;
                        case 'Low':
                            $priorityClass = 'text-blue-800';
                            $priorityBg = 'bg-blue-100';
                            break;
                    }
                    ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $record['id']; ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900"><?php echo $record['room_number']; ?></div>
                            <div class="text-sm text-gray-500"><?php echo $record['building']; ?></div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900"><?php echo $record['issue_type']; ?></div>
                            <div class="text-sm text-gray-500 max-w-xs truncate"><?php echo $record['description']; ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo formatDate($record['reported_date']); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $statusBg; ?> <?php echo $statusClass; ?>">
                                <?php echo $record['status']; ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo $record['assigned_to']; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $priorityBg; ?> <?php echo $priorityClass; ?>">
                                <?php echo $record['priority']; ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex space-x-2 justify-end">
                                <button class="text-primary hover:text-indigo-900" title="View Details">
                                    <i class="ri-eye-line"></i>
                                </button>
                                <button class="text-blue-600 hover:text-blue-900" title="Update Status">
                                    <i class="ri-edit-line"></i>
                                </button>
                                <?php if ($record['status'] !== 'Completed'): ?>
                                <button class="text-green-600 hover:text-green-900" title="Mark as Completed">
                                    <i class="ri-check-line"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
// Include footer
include BASE_PATH . '/app/Views/components/footer.php';
?>
<?php
// Include helper functions
include_once BASE_PATH . '/app/helpers.php';

// Set page title
$pageTitle = 'Clearance Status';

// Enable charts for this page
$useCharts = true;

// Sample data for clearance records
$clearanceRecords = [
    [
        'id' => 'CL-0001',
        'resident_id' => 'R-0004',
        'resident_name' => 'Kevin Wilson',
        'room' => 'Room 422',
        'building' => 'Building C',
        'due_date' => '2025-05-01',
        'status' => 'Pending',
        'issues' => ['Unpaid Fees', 'Room Damages'],
        'room_check' => 'Incomplete',
        'fees_payment' => 'Incomplete',
        'document_submission' => 'Complete',
        'admin_approval' => 'Pending'
    ],
    [
        'id' => 'CL-0002',
        'resident_id' => 'R-0001',
        'resident_name' => 'Jessica Davis',
        'room' => 'Room 201',
        'building' => 'Building A',
        'due_date' => '2025-05-05',
        'status' => 'Pending',
        'issues' => ['Pending Room Check'],
        'room_check' => 'Incomplete',
        'fees_payment' => 'Complete',
        'document_submission' => 'Complete',
        'admin_approval' => 'Pending'
    ],
    [
        'id' => 'CL-0003',
        'resident_id' => 'R-0003',
        'resident_name' => 'Amanda Miller',
        'room' => 'Room 107',
        'building' => 'Building A',
        'due_date' => '2025-05-10',
        'status' => 'Pending',
        'issues' => ['Missing Documents'],
        'room_check' => 'Complete',
        'fees_payment' => 'Complete',
        'document_submission' => 'Incomplete',
        'admin_approval' => 'Pending'
    ],
    [
        'id' => 'CL-0004',
        'resident_id' => 'R-0006',
        'resident_name' => 'Michael Johnson',
        'room' => 'Room 210',
        'building' => 'Building A',
        'due_date' => '2025-04-28',
        'status' => 'Approved',
        'issues' => [],
        'room_check' => 'Complete',
        'fees_payment' => 'Complete',
        'document_submission' => 'Complete',
        'admin_approval' => 'Approved'
    ],
    [
        'id' => 'CL-0005',
        'resident_id' => 'R-0002',
        'resident_name' => 'Robert Harris',
        'room' => 'Room 315',
        'building' => 'Building B',
        'due_date' => '2025-05-02',
        'status' => 'Pending',
        'issues' => ['Unpaid Fees'],
        'room_check' => 'Complete',
        'fees_payment' => 'Incomplete',
        'document_submission' => 'Complete',
        'admin_approval' => 'Pending'
    ]
];

// Function to get status class
function getStatusClass($status) {
    switch ($status) {
        case 'Approved':
            return ['text-green-800', 'bg-green-100'];
        case 'Rejected':
            return ['text-red-800', 'bg-red-100'];
        case 'Pending':
            return ['text-yellow-800', 'bg-yellow-100'];
        default:
            return ['text-gray-800', 'bg-gray-100'];
    }
}

// Include header
include BASE_PATH . '/app/Views/components/header.php';
?>

<!-- Page Header -->
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Clearance Status</h1>
        <p class="mt-1 text-sm text-gray-500">Monitor and approve resident clearance applications</p>
    </div>
    <button class="bg-primary text-white px-4 py-2 rounded-md flex items-center space-x-2 hover:bg-indigo-700 transition-colors transform hover:scale-105 duration-200">
        <i class="ri-add-line"></i>
        <span>New Clearance</span>
    </button>
</div>

<!-- Status Cards -->
<div class="mb-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded shadow p-4 border-l-4 border-yellow-500 hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Pending Clearances</p>
                    <p class="text-2xl font-bold text-gray-900">32</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-yellow-500 bg-opacity-10 flex items-center justify-center text-yellow-500 pulse-icon">
                    <i class="ri-time-line ri-lg"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded shadow p-4 border-l-4 border-green-500 hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Approved Clearances</p>
                    <p class="text-2xl font-bold text-gray-900">87</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-green-500 bg-opacity-10 flex items-center justify-center text-green-500 pulse-icon">
                    <i class="ri-check-double-line ri-lg"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded shadow p-4 border-l-4 border-red-500 hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Rejected Clearances</p>
                    <p class="text-2xl font-bold text-gray-900">5</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-red-500 bg-opacity-10 flex items-center justify-center text-red-500 pulse-icon">
                    <i class="ri-close-circle-line ri-lg"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters and Tabs -->
<div class="bg-white rounded shadow mb-6 hover:shadow-md transition-all">
    <div class="p-4 border-b border-gray-200">
        <div class="flex flex-col md:flex-row justify-between space-y-3 md:space-y-0">
            <div class="flex space-x-2">
                <button class="tab-button active px-4 py-2 rounded-md text-sm font-medium transform hover:scale-105 transition-transform">
                    All Clearances
                </button>
                <button class="tab-button px-4 py-2 rounded-md text-sm font-medium text-gray-500 bg-gray-100 transform hover:scale-105 transition-transform">
                    Pending
                </button>
                <button class="tab-button px-4 py-2 rounded-md text-sm font-medium text-gray-500 bg-gray-100 transform hover:scale-105 transition-transform">
                    Approved
                </button>
                <button class="tab-button px-4 py-2 rounded-md text-sm font-medium text-gray-500 bg-gray-100 transform hover:scale-105 transition-transform">
                    Rejected
                </button>
            </div>
            <div class="flex space-x-2">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="ri-search-line text-gray-400"></i>
                    </div>
                    <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary focus:border-primary block w-full pl-10 p-2" placeholder="Search clearances...">
                </div>
                <button class="p-2 text-gray-500 bg-gray-50 border border-gray-300 rounded-lg hover:bg-gray-100 transform hover:scale-105 transition-transform">
                    <i class="ri-filter-3-line"></i>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Clearance Records Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        ID
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Resident
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Room
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Due Date
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Issues
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($clearanceRecords as $record): ?>
                    <?php 
                    list($statusClass, $statusBg) = getStatusClass($record['status']);
                    ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $record['id']; ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-primary bg-opacity-10 flex items-center justify-center text-primary">
                                    <span class="font-medium"><?php echo getInitials($record['resident_name']); ?></span>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900"><?php echo $record['resident_name']; ?></div>
                                    <div class="text-sm text-gray-500"><?php echo $record['resident_id']; ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900"><?php echo $record['room']; ?></div>
                            <div class="text-sm text-gray-500"><?php echo $record['building']; ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo formatDate($record['due_date']); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $statusBg; ?> <?php echo $statusClass; ?>">
                                <?php echo $record['status']; ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php if (count($record['issues']) > 0): ?>
                                <div class="text-sm text-red-600">
                                    <?php echo implode(', ', $record['issues']); ?>
                                </div>
                            <?php else: ?>
                                <div class="text-sm text-green-600">No issues</div>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex space-x-2 justify-end">
                                <button class="text-indigo-600 hover:text-indigo-900 transform hover:scale-110 transition-transform">
                                    <i class="ri-eye-line"></i>
                                </button>
                                <button class="text-blue-600 hover:text-blue-900 transform hover:scale-110 transition-transform">
                                    <i class="ri-edit-line"></i>
                                </button>
                                <?php if ($record['status'] === 'Pending'): ?>
                                <button class="text-green-600 hover:text-green-900 transform hover:scale-110 transition-transform">
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
    <div class="px-4 py-3 bg-gray-50 flex items-center justify-between border-t border-gray-200 sm:px-6">
        <div class="flex-1 flex justify-between sm:hidden">
            <button class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transform hover:scale-105 transition-transform">
                Previous
            </button>
            <button class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transform hover:scale-105 transition-transform">
                Next
            </button>
        </div>
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-700">
                    Showing <span class="font-medium">1</span> to <span class="font-medium">5</span> of <span class="font-medium">32</span> results
                </p>
            </div>
            <div>
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                    <button class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 transform hover:scale-105 transition-transform">
                        <span class="sr-only">Previous</span>
                        <i class="ri-arrow-left-s-line"></i>
                    </button>
                    <button class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-primary text-sm font-medium text-white hover:bg-primary-dark transform hover:scale-105 transition-transform">
                        1
                    </button>
                    <button class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transform hover:scale-105 transition-transform">
                        2
                    </button>
                    <button class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transform hover:scale-105 transition-transform">
                        3
                    </button>
                    <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                        ...
                    </span>
                    <button class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transform hover:scale-105 transition-transform">
                        7
                    </button>
                    <button class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 transform hover:scale-105 transition-transform">
                        <span class="sr-only">Next</span>
                        <i class="ri-arrow-right-s-line"></i>
                    </button>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- Clearance Process Information -->
<div class="bg-white rounded shadow mb-6 hover:shadow-md transition-all">
    <div class="px-4 py-3 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900">Clearance Process</h2>
    </div>
    <div class="p-4">
        <div class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[250px] p-4 bg-gray-50 rounded-md hover:bg-gray-100 transition-colors hover-lift">
                <div class="flex items-center mb-2">
                    <div class="w-8 h-8 rounded-full bg-primary bg-opacity-10 flex items-center justify-center text-primary mr-2">
                        <i class="ri-home-line"></i>
                    </div>
                    <h3 class="text-md font-semibold text-gray-900">1. Room Inspection</h3>
                </div>
                <p class="text-sm text-gray-600">Inspection of the room for any damages or issues that need to be addressed before clearance.</p>
            </div>
            <div class="flex-1 min-w-[250px] p-4 bg-gray-50 rounded-md hover:bg-gray-100 transition-colors hover-lift">
                <div class="flex items-center mb-2">
                    <div class="w-8 h-8 rounded-full bg-primary bg-opacity-10 flex items-center justify-center text-primary mr-2">
                        <i class="ri-money-dollar-circle-line"></i>
                    </div>
                    <h3 class="text-md font-semibold text-gray-900">2. Financial Clearance</h3>
                </div>
                <p class="text-sm text-gray-600">Verification that all fees, fines, and other financial obligations have been settled.</p>
            </div>
            <div class="flex-1 min-w-[250px] p-4 bg-gray-50 rounded-md hover:bg-gray-100 transition-colors hover-lift">
                <div class="flex items-center mb-2">
                    <div class="w-8 h-8 rounded-full bg-primary bg-opacity-10 flex items-center justify-center text-primary mr-2">
                        <i class="ri-file-list-line"></i>
                    </div>
                    <h3 class="text-md font-semibold text-gray-900">3. Document Submission</h3>
                </div>
                <p class="text-sm text-gray-600">Confirmation that all required documents have been submitted and verified.</p>
            </div>
            <div class="flex-1 min-w-[250px] p-4 bg-gray-50 rounded-md hover:bg-gray-100 transition-colors hover-lift">
                <div class="flex items-center mb-2">
                    <div class="w-8 h-8 rounded-full bg-primary bg-opacity-10 flex items-center justify-center text-primary mr-2">
                        <i class="ri-user-star-line"></i>
                    </div>
                    <h3 class="text-md font-semibold text-gray-900">4. Admin Approval</h3>
                </div>
                <p class="text-sm text-gray-600">Final review and approval by dormitory administration.</p>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include BASE_PATH . '/app/Views/components/footer.php';
?>
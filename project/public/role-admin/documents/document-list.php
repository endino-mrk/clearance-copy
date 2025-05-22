<?php
// Include helper functions
require_once __DIR__ . '/../../../config/bootstrap.php';

require_once __DIR__ . '/../../../includes/auth_check.php';


// Set page title
$pageTitle = 'Document Tracker';

// Enable charts for this page
$useCharts = true;

// Sample data for document submissions
$documents = []; //Empty list

// Document types
$documentTypes = [
    [
        'id' => 'DOC-TYPE-001',
        'name' => 'Clearance Form',
        'description' => 'Main dormitory clearance application form',
        'required' => true
    ],
    [
        'id' => 'DOC-TYPE-002',
        'name' => 'Room Inspection Form',
        'description' => 'Form documenting the condition of the room upon departure',
        'required' => true
    ],
    [
        'id' => 'DOC-TYPE-003',
        'name' => 'Property Return Form',
        'description' => 'Form listing dormitory properties returned by the resident',
        'required' => true
    ],
    [
        'id' => 'DOC-TYPE-004',
        'name' => 'Financial Clearance Form',
        'description' => 'Form confirming that all financial obligations have been settled',
        'required' => true
    ],
    [
        'id' => 'DOC-TYPE-005',
        'name' => 'Move-out Request Form',
        'description' => 'Form to request permission to move out of the dormitory',
        'required' => false
    ]
];

// Calculate statistics
$totalDocuments = count($documents);
$submittedDocuments = count(array_filter($documents, function($doc) {
    return $doc['status'] === 'Submitted';
}));
$verifiedDocuments = count(array_filter($documents, function($doc) {
    return $doc['verification_status'] === 'Verified';
}));
$pendingVerification = count(array_filter($documents, function($doc) {
    return $doc['verification_status'] === 'Pending Verification';
}));
$rejectedDocuments = count(array_filter($documents, function($doc) {
    return $doc['verification_status'] === 'Rejected';
}));
$notSubmittedDocuments = count(array_filter($documents, function($doc) {
    return $doc['status'] === 'Not Submitted';
}));

$submissionRate = $totalDocuments > 0 ? round(($submittedDocuments / $totalDocuments) * 100) : 0;
$verificationRate = $submittedDocuments > 0 ? round(($verifiedDocuments / $submittedDocuments) * 100) : 0;

// Document type breakdown
$documentTypeCount = [];
foreach ($documents as $doc) {
    if (!isset($documentTypeCount[$doc['document_type']])) {
        $documentTypeCount[$doc['document_type']] = 0;
    }
    $documentTypeCount[$doc['document_type']]++;
}

// Include header
include  '../../../includes/header.php';
?>

<!-- Page Header -->
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Document Tracker</h1>
        <p class="mt-1 text-sm text-gray-500">Manage resident documents and track submission progress</p>
    </div>
    <div class="flex space-x-2">
        <button class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-md flex items-center space-x-2">
            <i class="ri-notification-line"></i>
            <span>Send Reminders</span>
        </button>   
    </div>
</div>

<!-- Statistics Overview -->
<div class="mb-6">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded shadow p-4 border-l-4 border-primary">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Documents</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo $totalDocuments; ?></p>
                </div>
                <div class="w-10 h-10 rounded-full bg-primary bg-opacity-10 flex items-center justify-center text-primary">
                    <i class="ri-file-list-line ri-lg"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded shadow p-4 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Submission Rate</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo $submissionRate; ?>%</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-green-500 bg-opacity-10 flex items-center justify-center text-green-500">
                    <i class="ri-upload-line ri-lg"></i>
                </div>
            </div>
            <div class="mt-2 flex items-center text-xs">
                <span class="text-green-500 flex items-center">
                    <i class="ri-file-line"></i> <?php echo $submittedDocuments; ?>
                </span>
                <span class="ml-1 text-gray-500">of <?php echo $totalDocuments; ?> submitted</span>
            </div>
        </div>
        <div class="bg-white rounded shadow p-4 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Verification Rate</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo $verificationRate; ?>%</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-blue-500 bg-opacity-10 flex items-center justify-center text-blue-500">
                    <i class="ri-check-line ri-lg"></i>
                </div>
            </div>
            <div class="mt-2 flex items-center text-xs">
                <span class="text-blue-500 flex items-center">
                    <i class="ri-check-double-line"></i> <?php echo $verifiedDocuments; ?>
                </span>
                <span class="ml-1 text-gray-500">verified, <?php echo $pendingVerification; ?> pending</span>
            </div>
        </div>
        <div class="bg-white rounded shadow p-4 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Missing Documents</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo $notSubmittedDocuments; ?></p>
                </div>
                <div class="w-10 h-10 rounded-full bg-red-500 bg-opacity-10 flex items-center justify-center text-red-500">
                    <i class="ri-error-warning-line ri-lg"></i>
                </div>
            </div>
            <div class="mt-2 flex items-center text-xs">
                <span class="text-red-500 flex items-center">
                    <i class="ri-close-line"></i> <?php echo $rejectedDocuments; ?>
                </span>
                <span class="ml-1 text-gray-500">rejected documents</span>
            </div>
        </div>
    </div>
</div>

<!-- Document Status and Type Charts -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Document Status Chart -->
    <div class="bg-white rounded shadow p-4">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">Document Status Distribution</h2>
        </div>
        <div id="documentStatusChart" class="h-64"></div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof echarts !== 'undefined') {
                    const documentStatusChart = echarts.init(document.getElementById('documentStatusChart'));
                    
                    const option = {
                        tooltip: {
                            trigger: 'item',
                            formatter: '{b}: {c} ({d}%)'
                        },
                        legend: {
                            orient: 'horizontal',
                            bottom: 0,
                            data: ['Verified', 'Pending Verification', 'Rejected', 'Not Submitted']
                        },
                        series: [
                            {
                                name: 'Document Status',
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
                                    { value: <?php echo $verifiedDocuments; ?>, name: 'Verified', itemStyle: { color: '#10B981' } },
                                    { value: <?php echo $pendingVerification; ?>, name: 'Pending Verification', itemStyle: { color: '#3B82F6' } },
                                    { value: <?php echo $rejectedDocuments; ?>, name: 'Rejected', itemStyle: { color: '#EF4444' } },
                                    { value: <?php echo $notSubmittedDocuments; ?>, name: 'Not Submitted', itemStyle: { color: '#9CA3AF' } }
                                ]
                            }
                        ]
                    };
                    
                    documentStatusChart.setOption(option);
                    
                    window.addEventListener('resize', function() {
                        documentStatusChart.resize();
                    });
                }
            });
        </script>
    </div>
    
    <!-- Document Type Chart -->
    <div class="bg-white rounded shadow p-4">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">Document Type Distribution</h2>
        </div>
        <div id="documentTypeChart" class="h-64"></div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof echarts !== 'undefined') {
                    const documentTypeChart = echarts.init(document.getElementById('documentTypeChart'));
                    
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
                            bottom: '15%',
                            containLabel: true
                        },
                        xAxis: [
                            {
                                type: 'category',
                                data: <?php echo json_encode(array_keys($documentTypeCount)); ?>,
                                axisTick: {
                                    alignWithLabel: true
                                },
                                axisLabel: {
                                    rotate: 30,
                                    interval: 0
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
                                name: 'Count',
                                type: 'bar',
                                barWidth: '60%',
                                data: [
                                    <?php 
                                    $colors = ['#4F46E5', '#10B981', '#F59E0B', '#3B82F6', '#8B5CF6'];
                                    $i = 0;
                                    foreach ($documentTypeCount as $type => $count) {
                                        $color = $colors[$i % count($colors)];
                                        echo "{value: $count, itemStyle: {color: '$color'}}, ";
                                        $i++;
                                    }
                                    ?>
                                ]
                            }
                        ]
                    };
                    
                    documentTypeChart.setOption(option);
                    
                    window.addEventListener('resize', function() {
                        documentTypeChart.resize();
                    });
                }
            });
        </script>
    </div>
</div>

<!-- Document Records -->
<div class="bg-white shadow rounded-md overflow-hidden mb-6">
    <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center">
        <h2 class="text-lg font-semibold text-gray-900">Document Records</h2>
        <div class="flex items-center space-x-2">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="ri-search-line text-gray-400"></i>
                </div>
                <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary focus:border-primary block w-full pl-10 p-2" placeholder="Search documents...">
            </div>
            <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary focus:border-primary block p-2.5">
                <option selected>All Document Types</option>
                <?php foreach ($documentTypes as $type): ?>
                <option><?php echo $type['name']; ?></option>
                <?php endforeach; ?>
            </select>
            <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary focus:border-primary block p-2.5">
                <option selected>All Statuses</option>
                <option>Submitted</option>
                <option>Not Submitted</option>
                <option>Verified</option>
                <option>Pending Verification</option>
                <option>Rejected</option>
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
                        Document ID
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Resident
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Document Type
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Submission Date
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Due Date
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Verification
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($documents as $document): ?>
                    <?php 
                    $statusClass = '';
                    $statusBg = '';
                    
                    switch ($document['status']) {
                        case 'Submitted':
                            $statusClass = 'text-green-800';
                            $statusBg = 'bg-green-100';
                            break;
                        case 'Not Submitted':
                            $statusClass = 'text-gray-800';
                            $statusBg = 'bg-gray-100';
                            break;
                    }
                    
                    $verificationClass = '';
                    $verificationBg = '';
                    
                    switch ($document['verification_status']) {
                        case 'Verified':
                            $verificationClass = 'text-green-800';
                            $verificationBg = 'bg-green-100';
                            break;
                        case 'Pending Verification':
                            $verificationClass = 'text-blue-800';
                            $verificationBg = 'bg-blue-100';
                            break;
                        case 'Rejected':
                            $verificationClass = 'text-red-800';
                            $verificationBg = 'bg-red-100';
                            break;
                        case 'Not Applicable':
                            $verificationClass = 'text-gray-800';
                            $verificationBg = 'bg-gray-100';
                            break;
                    }
                    ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $document['id']; ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-primary bg-opacity-10 flex items-center justify-center text-primary">
                                    <span class="font-medium"><?php echo getInitials($document['resident_name']); ?></span>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900"><?php echo $document['resident_name']; ?></div>
                                    <div class="text-sm text-gray-500"><?php echo $document['room']; ?>, <?php echo $document['building']; ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <?php echo $document['document_type']; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo $document['submission_date'] ? formatDate($document['submission_date']) : 'Not Submitted'; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo formatDate($document['due_date']); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $statusBg; ?> <?php echo $statusClass; ?>">
                                <?php echo $document['status']; ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $verificationBg; ?> <?php echo $verificationClass; ?>">
                                <?php echo $document['verification_status']; ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex space-x-2 justify-end">
                                <?php if ($document['status'] === 'Submitted'): ?>
                                <button class="text-primary hover:text-indigo-900" title="View Document">
                                    <i class="ri-eye-line"></i>
                                </button>
                                <?php endif; ?>
                                
                                <?php if ($document['verification_status'] === 'Pending Verification'): ?>
                                <button class="text-green-600 hover:text-green-900" title="Verify Document">
                                    <i class="ri-check-line"></i>
                                </button>
                                <button class="text-red-600 hover:text-red-900" title="Reject Document">
                                    <i class="ri-close-line"></i>
                                </button>
                                <?php endif; ?>
                                
                                <?php if ($document['status'] === 'Not Submitted'): ?>
                                <button class="text-yellow-600 hover:text-yellow-900" title="Send Reminder">
                                    <i class="ri-notification-line"></i>
                                </button>
                                <?php endif; ?>
                                
                                <button class="text-blue-600 hover:text-blue-900" title="Edit Record">
                                    <i class="ri-edit-line"></i>
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

<!-- Document Types -->
<div class="bg-white shadow rounded-md overflow-hidden mb-6">
    <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center">
        <h2 class="text-lg font-semibold text-gray-900">Document Types</h2>
        <button class="bg-primary text-white px-4 py-2 rounded-md flex items-center space-x-2">
            <i class="ri-add-line"></i>
            <span>Add Document Type</span>
        </button>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Type ID
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Document Name
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Description
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Required
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($documentTypes as $type): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $type['id']; ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            <?php echo $type['name']; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-normal text-sm text-gray-500 max-w-md">
                            <?php echo $type['description']; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php if ($type['required']): ?>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    Required
                                </span>
                            <?php else: ?>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    Optional
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex space-x-2 justify-end">
                                <button class="text-blue-600 hover:text-blue-900" title="Edit Document Type">
                                    <i class="ri-edit-line"></i>
                                </button>
                                <button class="text-red-600 hover:text-red-900" title="Delete Document Type">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

 
<script src="/fines/js/fine-modals.js"></script>

<?php
// Include footer
include  '../../../includes/footer.php';
?>
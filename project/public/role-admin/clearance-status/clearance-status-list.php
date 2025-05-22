<?php
require_once __DIR__ . '/../../../config/bootstrap.php';
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../includes/auth_check.php';
require_once __DIR__ . '/../../../includes/role_auth_check.php';

requireRole(['Manager']);

// Set page title
$pageTitle = 'Clearance Status';

// Enable charts for this page
$useCharts = true;

// Fetch clearance records from database
$clearanceRecords = [];
$stats = [
    'pending' => 0,
    'cleared' => 0
];

try {
    $pdo = connect_db();
    if ($pdo) {
        // Get clearance records with resident details
        $sql = "SELECT 
                cr.clearance_id,
                cr.resident_id,
                cr.occupancy_id,
                cr.status,
                cr.rental_fee_status,
                cr.fine_status,
                cr.room_status,
                cr.document_status,
                cr.date_cleared,
                cr.updated_at,
                CONCAT(u.first_name, ' ', u.last_name) as resident_name,
                r.student_id,
                rm.number as room_number,
                s.end_date as semester_end_date
            FROM clearance_records cr
            JOIN residents r ON cr.resident_id = r.resident_id
            JOIN users u ON r.user_id = u.user_id
            JOIN resident_occupancy ro ON cr.occupancy_id = ro.occupancy_id
            JOIN rooms rm ON ro.room_id = rm.room_id
            JOIN semesters s ON ro.semester_id = s.semester_id
            WHERE cr.status IN ('Pending', 'Cleared')
            ORDER BY cr.updated_at DESC";
        
        $stmt = $pdo->query($sql);
        $clearanceRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Calculate statistics
        foreach ($clearanceRecords as $record) {
            if ($record['status'] === 'Pending') {
                $stats['pending']++;
            } else if ($record['status'] === 'Cleared') {
                $stats['cleared']++;
            }
        }
    }
} catch (PDOException $e) {
    error_log("Error fetching clearance records: " . $e->getMessage());
    $_SESSION['errors']['general'] = "Failed to load clearance records. Please try again.";
}

// Function to get status class
function getStatusClass($status) {
    switch ($status) {
        case 'Cleared':
            return ['text-green-800', 'bg-green-100'];
        case 'Pending':
        default:
            return ['text-yellow-800', 'bg-yellow-100'];
    }
}

// Function to get issues array for a clearance record
function getIssues($record) {
    $issues = [];
    
    if ($record['rental_fee_status'] === 'Pending') {
        $issues[] = 'Unpaid Fees';
    }
    
    if ($record['fine_status'] === 'Pending') {
        $issues[] = 'Outstanding Fines';
    }
    
    if ($record['room_status'] === 'Pending') {
        $issues[] = 'Pending Room Check';
    }
    
    if ($record['document_status'] === 'Pending') {
        $issues[] = 'Missing Documents';
    }
    
    return $issues;
}

// Include header
include '../../../includes/header.php';
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
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white rounded shadow p-4 border-l-4 border-yellow-500 hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Pending Clearances</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo $stats['pending']; ?></p>
                </div>
                <div class="w-10 h-10 rounded-full bg-yellow-500 bg-opacity-10 flex items-center justify-center text-yellow-500 pulse-icon">
                    <i class="ri-time-line ri-lg"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded shadow p-4 border-l-4 border-green-500 hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Cleared Clearances</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo $stats['cleared']; ?></p>
                </div>
                <div class="w-10 h-10 rounded-full bg-green-500 bg-opacity-10 flex items-center justify-center text-green-500 pulse-icon">
                    <i class="ri-check-double-line ri-lg"></i>
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
                <button class="tab-button active px-4 py-2 rounded-md text-sm font-medium transform hover:scale-105 transition-transform" data-filter="all">
                    All Clearances
                </button>
                <button class="tab-button px-4 py-2 rounded-md text-sm font-medium text-gray-500 bg-gray-100 transform hover:scale-105 transition-transform" data-filter="Pending">
                    Pending
                </button>
                <button class="tab-button px-4 py-2 rounded-md text-sm font-medium text-gray-500 bg-gray-100 transform hover:scale-105 transition-transform" data-filter="Cleared">
                    Cleared
                </button>
            </div>
            <div class="flex space-x-2">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="ri-search-line text-gray-400"></i>
                    </div>
                    <input type="text" id="searchClearance" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary focus:border-primary block w-full pl-10 p-2" placeholder="Search clearances...">
                </div>
                <button class="p-2 text-gray-500 bg-gray-50 border border-gray-300 rounded-lg hover:bg-gray-100 transform hover:scale-105 transition-transform">
                    <i class="ri-filter-3-line"></i>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Display error message if any -->
    <?php if (isset($_SESSION['errors']['general'])): ?>
    <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg">
        <i class="ri-error-warning-line mr-2"></i>
        <?php echo $_SESSION['errors']['general']; ?>
    </div>
    <?php unset($_SESSION['errors']['general']); ?>
    <?php endif; ?>

    <?php
    // Debugging section - Remove in production
    echo "<div class='p-4 bg-gray-100 mb-4'>";
    echo "<h3 class='font-bold'>Debug Info - Clearance IDs:</h3>";
    echo "<ul class='list-disc pl-5'>";
    foreach ($clearanceRecords as $index => $record) {
        echo "<li>Record {$index}: clearance_id = " . (isset($record['clearance_id']) ? $record['clearance_id'] : 'NOT SET') . "</li>";
    }
    echo "</ul>";
    echo "</div>";
    ?>
    
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
                <?php if (empty($clearanceRecords)): ?>
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                        <p>No clearance records found</p>
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($clearanceRecords as $record): ?>
                        <?php 
                        list($statusClass, $statusBg) = getStatusClass($record['status']);
                        $issues = getIssues($record);
                        ?>
                        <tr class="hover:bg-gray-50 transition-colors clearance-row" data-status="<?php echo $record['status']; ?>">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">CL-<?php echo str_pad($record['clearance_id'], 4, '0', STR_PAD_LEFT); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-primary bg-opacity-10 flex items-center justify-center text-primary">
                                        <span class="font-medium"><?php echo getInitials($record['resident_name']); ?></span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900"><?php echo $record['resident_name']; ?></div>
                                        <div class="text-sm text-gray-500"><?php echo $record['student_id']; ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo 'Room ' . $record['room_number']; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo formatDate($record['semester_end_date']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $statusBg; ?> <?php echo $statusClass; ?>">
                                    <?php echo $record['status']; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php if (empty($issues)): ?>
                                    <span class="text-green-500">No issues</span>
                                <?php else: ?>
                                    <span class="text-yellow-500"><?php echo implode(', ', $issues); ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="resident-clearance.php?id=<?php echo $record['clearance_id']; ?>" class="text-primary hover:text-indigo-900 transform hover:scale-110 transition-transform">
                                    Review
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <?php if (!empty($clearanceRecords)): ?>
    <div class="px-4 py-3 bg-gray-50 flex items-center justify-between border-t border-gray-200 sm:px-6">
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-700">
                    Showing <span class="font-medium"><?php echo count($clearanceRecords); ?></span> records
                </p>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab filtering
    const tabButtons = document.querySelectorAll('.tab-button');
    const clearanceRows = document.querySelectorAll('.clearance-row');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Toggle active class
            tabButtons.forEach(btn => {
                btn.classList.remove('active');
                btn.classList.add('text-gray-500', 'bg-gray-100');
                btn.classList.remove('bg-primary', 'text-white');
            });
            this.classList.add('active');
            this.classList.remove('text-gray-500', 'bg-gray-100');
            this.classList.add('bg-primary', 'text-white');
            
            // Filter rows
            const filter = this.getAttribute('data-filter');
            clearanceRows.forEach(row => {
                if (filter === 'all' || row.getAttribute('data-status') === filter) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });
    
    // Search functionality
    const searchInput = document.getElementById('searchClearance');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase();
            clearanceRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchValue)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
});
</script>

<?php
// Include footer
include '../../../includes/footer.php';
?>
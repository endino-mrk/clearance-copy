<?php
// Include helper functions - Make sure to include database.php explicitly
require_once __DIR__ . '/../../../config/bootstrap.php';
require_once __DIR__ . '/../../../config/database.php'; // Add this line to ensure connect_db() is available
require_once __DIR__ . '/../../../includes/auth_check.php';

// Define the functions directly in this file if they're not found
if (!function_exists('getFineRecords')) {
    /**
     * Fetch all fine records with related information
     */
    function getFineRecords() {
        $fines = [];
        
        try {
            $pdo = connect_db();
            if (!$pdo) {
                return $fines;
            }
            
            $sql = "SELECT rf.resident_fine_id, rf.occupancy_id, rf.fine_id, f.name as reason, 
                       f.description, f.amount, rf.status, rf.violation_date, 
                       rf.date_issued, rf.amount_paid,  rf.date_paid, rf.updated_at,
                       r.resident_id, CONCAT(u.first_name, ' ', u.last_name) as resident_name,
                       rm.number as room
                FROM resident_fines rf
                JOIN fines f ON rf.fine_id = f.fine_id
                JOIN resident_occupancy ro ON rf.occupancy_id = ro.occupancy_id
                JOIN residents r ON ro.resident_id = r.resident_id
                JOIN users u ON r.user_id = u.user_id
                JOIN rooms rm ON ro.room_id = rm.room_id
                ORDER BY rf.date_issued DESC";
            
            $stmt = $pdo->query($sql);
            $fines = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Format the data for display
            foreach ($fines as &$fine) {
                // Calculate due date (15 days after issue date by default)
                if (!empty($fine['date_issued'])) {
                    $dueDate = date('Y-m-d', strtotime($fine['date_issued'] . ' + 15 days'));
                    $fine['due_date'] = $dueDate;
                }
                
                // Prepare display values
                $fine['formatted_amount'] = formatCurrency($fine['amount']);
                $fine['formatted_amount_paid'] = formatCurrency($fine['amount_paid']);
                $fine['formatted_issue_date'] = formatDate($fine['date_issued']);
                $fine['formatted_due_date'] = formatDate($fine['due_date'] ?? null);
                $fine['formatted_payment_date'] = formatDate($fine['date_paid'] ?? null);
                
                // Check if fine is overdue but not marked as such
                if ($fine['status'] === 'Unpaid' && !empty($fine['due_date'])) {
                    $today = date('Y-m-d');
                    if ($today > $fine['due_date']) {
                        // For display purposes only - doesn't update the database
                        $fine['status'] = 'Overdue';
                    }
                }
                
                // For table display - truncate description
                if (!empty($fine['description'])) {
                    $fine['short_description'] = strlen($fine['description']) > 50 
                        ? substr($fine['description'], 0, 50) . '...' 
                        : $fine['description'];
                } else {
                    $fine['short_description'] = '';
                }
            }
            
            return $fines;
        } catch (PDOException $e) {
            error_log("Error fetching fine records: " . $e->getMessage());
            return [];
        }
    }
}
if (!function_exists('getFineStatistics')) {
    /**
     * Get fine statistics for dashboard
     */
    function getFineStatistics() {
        $stats = [
            'total_count' => 0,
            'total_amount' => 0,
            'paid_count' => 0,
            'paid_amount' => 0,
            'unpaid_count' => 0,
            'unpaid_amount' => 0,
            'overdue_count' => 0,
            'overdue_amount' => 0,
            'waived_count' => 0,
            'waived_amount' => 0,
        ];
        
        try {
            $pdo = connect_db();
            if (!$pdo) {
                return $stats;
            }
            
            // Get all fines
            $sql = "SELECT rf.status, f.amount, rf.date_issued, rf.date_paid 
                    FROM resident_fines rf
                    JOIN fines f ON rf.fine_id = f.fine_id";
            
            $stmt = $pdo->query($sql);
            $fines = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Calculate statistics
            foreach ($fines as $fine) {
                $stats['total_count']++;
                $stats['total_amount'] += $fine['amount'];
                
                $status = $fine['status'];
                
                // Check if fine is actually overdue
                if ($status === 'Unpaid' && !empty($fine['date_issued'])) {
                    $dueDate = date('Y-m-d', strtotime($fine['date_issued'] . ' + 15 days'));
                    $today = date('Y-m-d');
                    
                    if ($today > $dueDate) {
                        $status = 'Overdue';
                    }
                }
                
                switch ($status) {
                    case 'Paid':
                        $stats['paid_count']++;
                        $stats['paid_amount'] += $fine['amount'];
                        break;
                    case 'Unpaid':
                        $stats['unpaid_count']++;
                        $stats['unpaid_amount'] += $fine['amount'];
                        break;
                    case 'Overdue':
                        $stats['overdue_count']++;
                        $stats['overdue_amount'] += $fine['amount'];
                        break;
                    case 'Waived':
                        $stats['waived_count']++;
                        $stats['waived_amount'] += $fine['amount'];
                        break;
                }
            }
            
            // Calculate collection rate
            $stats['collection_rate'] = ($stats['total_amount'] - $stats['waived_amount']) > 0 
                ? round(($stats['paid_amount'] / ($stats['total_amount'] - $stats['waived_amount'])) * 100) 
                : 0;
                
            // Combine unpaid and overdue for "outstanding" calculation
            $stats['outstanding_count'] = $stats['unpaid_count'] + $stats['overdue_count'];
            $stats['outstanding_amount'] = $stats['unpaid_amount'] + $stats['overdue_amount'];
            
            return $stats;
        } catch (PDOException $e) {
            error_log("Error calculating fine statistics: " . $e->getMessage());
            return $stats;
        }
    }
}

// Set page title
$pageTitle = 'Fines Management';

// Get fines data and statistics
$fines = getFineRecords();
$stats = getFineStatistics();

// Get fine types for the modal
$fineTypes = [];
$pdo = connect_db();
if ($pdo) {
    try {
        // Get fine types
        $stmt = $pdo->query("SELECT fine_id, name, description, amount FROM fines ORDER BY name");
        $fineTypes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log("Error fetching data for fine form: " . $e->getMessage());
    }
}
?>

<?php
include '../../../includes/header.php';
?>

<!-- Toast Container for notifications -->
<div id="toast-container" class="fixed top-4 right-4 z-50 flex flex-col items-end"></div>

<!-- Page Header -->
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Fines Management</h1>
        <p class="mt-1 text-sm text-gray-500">Issue, track, and collect fines for dormitory violations</p>
    </div>
    <div class="flex space-x-2">
        <button id="openFineModal" class="bg-primary text-white px-4 py-2 rounded-md flex items-center space-x-2 hover:bg-indigo-700 transition duration-150">
            <i class="ri-add-line"></i>
            <span>Issue Fine</span>
        </button>
    </div>
</div>

<!-- Success Messages -->
<?php if (isset($_SESSION['success'])): ?>
<div class="mb-4 bg-green-50 border-l-4 border-green-400 p-4">
    <div class="flex">
        <div class="flex-shrink-0">
            <i class="ri-checkbox-circle-line text-green-400"></i>
        </div>
        <div class="ml-3">
            <p class="text-sm text-green-700">
                <?php echo $_SESSION['success']; ?>
            </p>
        </div>
    </div>
</div>
<?php unset($_SESSION['success']); ?>
<?php endif; ?>

<!-- Error Messages -->
<?php if (isset($_SESSION['errors']) && isset($_SESSION['errors']['general'])): ?>
<div class="mb-4 bg-red-50 border-l-4 border-red-400 p-4">
    <div class="flex">
        <div class="flex-shrink-0">
            <i class="ri-error-warning-line text-red-400"></i>
        </div>
        <div class="ml-3">
            <p class="text-sm text-red-700">
                <?php echo $_SESSION['errors']['general']; ?>
            </p>
        </div>
    </div>
</div>
<?php unset($_SESSION['errors']['general']); ?>
<?php endif; ?>

<!-- Financial Overview -->
<div class="mb-6">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded shadow p-4 border-l-4 border-primary">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Fines</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo formatCurrency($stats['total_amount']); ?></p>
                </div>
                <div class="w-10 h-10 rounded-full bg-primary bg-opacity-10 flex items-center justify-center text-primary">
                    <i class="ri-bill-line ri-lg"></i>
                </div>
            </div>
            <div class="mt-2 text-xs text-gray-500">
                <?php echo $stats['total_count']; ?> fine<?php echo $stats['total_count'] != 1 ? 's' : ''; ?> issued
            </div>
        </div>
        <div class="bg-white rounded shadow p-4 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Collected Fines</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo formatCurrency($stats['paid_amount']); ?></p>
                </div>
                <div class="w-10 h-10 rounded-full bg-green-500 bg-opacity-10 flex items-center justify-center text-green-500">
                    <i class="ri-checkbox-circle-line ri-lg"></i>
                </div>
            </div>
            <div class="mt-2 flex items-center text-xs">
                <span class="text-green-500 flex items-center">
                    <i class="ri-arrow-up-s-line"></i> <?php echo $stats['collection_rate']; ?>%
                </span>
                <span class="ml-1 text-gray-500">collection rate</span>
            </div>
        </div>
        <div class="bg-white rounded shadow p-4 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Outstanding Fines</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo formatCurrency($stats['outstanding_amount']); ?></p>
                </div>
                <div class="w-10 h-10 rounded-full bg-red-500 bg-opacity-10 flex items-center justify-center text-red-500">
                    <i class="ri-time-line ri-lg"></i>
                </div>
            </div>
            <div class="mt-2 flex items-center text-xs">
                <span class="text-red-500 flex items-center">
                    <i class="ri-arrow-down-s-line"></i> 
                    <?php 
                        $outstandingRate = ($stats['total_amount'] - $stats['waived_amount']) > 0 
                            ? round(($stats['outstanding_amount'] / ($stats['total_amount'] - $stats['waived_amount'])) * 100) 
                            : 0;
                        echo $outstandingRate;
                    ?>%
                </span>
                <span class="ml-1 text-gray-500">of total fines</span>
            </div>
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
                <input type="text" id="searchFines" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary focus:border-primary block w-full pl-10 p-2" placeholder="Search fines...">
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
                        Amount Paid
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
                <?php if (empty($fines)): ?>
                <tr>
                    <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                        <div class="flex flex-col items-center justify-center">
                            <i class="ri-file-list-3-line text-4xl mb-2 text-gray-400"></i>
                            <p class="text-lg font-medium">No fines found</p>
                            <p class="text-sm">No fine records are currently available in the system.</p>
                            <button id="emptyStateIssueBtn" class="mt-4 bg-primary text-white px-4 py-2 rounded-md flex items-center space-x-2 hover:bg-indigo-700 transition duration-150">
                                <i class="ri-add-line"></i>
                                <span>Issue a Fine</span>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php else: ?>
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
                        case 'Waived':
                            $statusClass = 'text-gray-800';
                            $statusBg = 'bg-gray-100';
                            break;
                    }
                    ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $fine['resident_fine_id']; ?></td>
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
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                            <?php echo $fine['formatted_amount']; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900"><?php echo $fine['reason']; ?></div>
                            <div class="text-xs text-gray-500 max-w-xs truncate"><?php echo $fine['short_description'] ?? ''; ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $fine['formatted_due_date']; ?></td>
                        
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                            <?php echo formatCurrency($fine['amount_paid'] ?? 0); ?>
                            <?php if ($fine['status'] === 'Partially Paid'): ?>
                                <span class="text-xs text-yellow-600">(partial)</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $statusBg; ?> <?php echo $statusClass; ?>">
                                <?php echo $fine['status']; ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex space-x-2 justify-end">
                                <?php if ($fine['status'] !== 'Paid' && $fine['status'] !== 'Waived'): ?>
                                    <button type="button" 
                                            onclick="openPaymentModal(<?php echo $fine['resident_fine_id']; ?>, <?php echo $fine['amount']; ?>, <?php echo isset($fine['amount_paid']) ? $fine['amount_paid'] : 0; ?>)" 
                                            class="text-green-600 hover:text-green-900" 
                                            title="Record Payment">
                                        <i class="ri-money-dollar-circle-line"></i>
                                    </button>
                                    <a href="edit-fine.php?id=<?php echo $fine['resident_fine_id']; ?>" class="text-primary hover:text-indigo-900">
                                        <i class="ri-edit-line"></i>
                                    </a>
                                    <form action="functions/delete-functions.php" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this fine?');">
                                        <input type="hidden" name="fine_id" value="<?php echo $fine['resident_fine_id']; ?>">
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
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
                    <?php if (empty($fines)): ?>
                    Showing <span class="font-medium">0</span> results
                    <?php else: ?>
                    Showing <span class="font-medium">1</span> to <span class="font-medium"><?php echo count($fines); ?></span> of <span class="font-medium"><?php echo count($fines); ?></span> results
                    <?php endif; ?>
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

<!-- Modal for adding new fine with correct input naming -->
<div id="addFineModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <div class="flex justify-between items-center pb-4 mb-4 border-b border-gray-200">
                            <h3 class="text-xl font-semibold text-gray-900">Issue New Fine</h3>
                            <button type="button" id="closeFineModal" class="text-gray-400 hover:text-gray-500">
                                <span class="sr-only">Close</span>
                                <i class="ri-close-line text-2xl"></i>
                            </button>
                        </div>
                        
                        <form id="addFineForm" action="functions/create-functions.php" method="POST">
                            <!-- Resident Selection with Student ID support -->
                            <div class="mb-4">
                                <label for="resident" class="block text-sm font-medium text-gray-700 mb-1">Resident (Student ID)</label>
                                <div class="relative">
                                    <!-- IMPORTANT: Changed the name from 'residentSearch' to 'resident' -->
                                    <input type="text" id="resident" name="resident"
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                        placeholder="Enter student ID or name..."
                                        autocomplete="off">
                                    
                                    <!-- Include hidden input for resident_id but make it optional -->
                                    <input type="hidden" name="resident_id" id="resident_id">
                                </div>
                                
                                <!-- Selected resident info display (keep for AJAX version) -->
                                <div id="selectedResidentInfo" class="mt-2 hidden">
                                    <div class="flex items-center p-2 bg-gray-50 rounded">
                                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-primary bg-opacity-10 flex items-center justify-center text-primary">
                                            <span id="residentInitials" class="font-medium"></span>
                                        </div>
                                        <div class="ml-3">
                                            <p id="residentName" class="text-sm font-medium text-gray-900"></p>
                                            <p id="residentRoom" class="text-xs text-gray-500"></p>
                                        </div>
                                    </div>
                                </div>
                                
                                <?php if (isset($_SESSION['errors']['resident_id'])): ?>
                                    <p class="text-red-500 text-xs mt-1"><?php echo $_SESSION['errors']['resident_id']; ?></p>
                                <?php endif; ?>
                            </div>

                            <!-- Fine Type -->
                            <div class="mb-4">
                                <label for="fine_id" class="block text-sm font-medium text-gray-700 mb-1">Fine Type</label>
                                <div class="relative">
                                    <select name="fine_id" id="fine_id" 
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                        required>
                                        <option value="">Select Fine Type</option>
                                        <?php foreach ($fineTypes as $fineType): ?>
                                            <?php
                                            $selected = '';
                                            if (isset($_SESSION['old']['fine_id']) && $_SESSION['old']['fine_id'] == $fineType['fine_id']) {
                                                $selected = 'selected';
                                            }
                                            ?>
                                            <option value="<?php echo $fineType['fine_id']; ?>" 
                                                    data-amount="<?php echo $fineType['amount']; ?>" 
                                                    data-description="<?php echo htmlspecialchars($fineType['description']); ?>" 
                                                    <?php echo $selected; ?>>
                                                <?php echo $fineType['name'] . ' - ₱' . number_format($fineType['amount'], 2); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div id="fineDetails" class="mt-2 text-sm text-gray-500 hidden p-2 bg-gray-50 rounded">
                                    <p><span class="font-medium">Amount:</span> ₱<span id="fineAmount"></span></p>
                                    <p><span class="font-medium">Description:</span> <span id="fineDescription"></span></p>
                                </div>
                                <?php if (isset($_SESSION['errors']['fine_id'])): ?>
                                    <p class="text-red-500 text-xs mt-1"><?php echo $_SESSION['errors']['fine_id']; ?></p>
                                <?php endif; ?>
                            </div>

                            <!-- Violation Date -->
                            <div class="mb-4">
                                <label for="violation_date" class="block text-sm font-medium text-gray-700 mb-1">Violation Date</label>
                                <input type="date" name="violation_date" id="violation_date" 
                                    value="<?php echo isset($_SESSION['old']['violation_date']) ? $_SESSION['old']['violation_date'] : date('Y-m-d'); ?>"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                    required>
                                <?php if (isset($_SESSION['errors']['violation_date'])): ?>
                                    <p class="text-red-500 text-xs mt-1"><?php echo $_SESSION['errors']['violation_date']; ?></p>
                                <?php endif; ?>
                            </div>

                            <!-- Additional Description (Optional) -->
                            <div class="mb-6">
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Additional Notes (Optional)</label>
                                <textarea name="description" id="description" rows="3"
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"><?php echo isset($_SESSION['old']['description']) ? $_SESSION['old']['description'] : ''; ?></textarea>
                                <?php if (isset($_SESSION['errors']['description'])): ?>
                                    <p class="text-red-500 text-xs mt-1"><?php echo $_SESSION['errors']['description']; ?></p>
                                <?php endif; ?>
                            </div>
                            <!-- Button group -->
                            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                                    Issue Fine
                                </button>
                                <button type="button" id="cancelAddFine" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pass data to JavaScript -->
<script>
<?php if (isset($_SESSION['old']['resident_id'])): ?>
window.savedResidentId = <?php echo json_encode($_SESSION['old']['resident_id']); ?>;
<?php endif; ?>

<?php if (isset($_SESSION['success'])): ?>
window.successMessage = <?php echo json_encode($_SESSION['success']); ?>;
<?php endif; ?>

<?php if (isset($_SESSION['errors']['general'])): ?>
window.errorMessage = <?php echo json_encode($_SESSION['errors']['general']); ?>;
<?php endif; ?>
</script>

<!-- Include the JavaScript for functionality -->
<script src="./js/fine-list.js"></script>
<script src="./js/payment.js"></script>
<script src="/js/edit-fine.js"></script>
<?php 
include 'payment.php'; 
include 'edit-fine.php';
?>

<?php
// Clear any remaining session data once it's been displayed
if (isset($_SESSION['errors'])) unset($_SESSION['errors']);
if (isset($_SESSION['old'])) unset($_SESSION['old']);

// Include footer
include '../../../includes/footer.php';
?>
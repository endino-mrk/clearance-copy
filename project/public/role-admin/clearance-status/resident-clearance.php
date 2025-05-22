<?php
// project/public/role-admin/clearance-status/resident-clearance.php
require_once __DIR__ . '/../../../config/bootstrap.php';
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../includes/role_auth_check.php';

// Require login and allow Resident, Treasurer, or Manager
requireRole(['Resident', 'Treasurer', 'Manager']);

$pdo = connect_db();
if (!$pdo) {
    die('Database connection failed');
}

// Get resident ID - if Manager, they might view any resident (implement parameter later)
$residentId = getResidentId();
if (!$residentId && !isManager()) {
    $_SESSION['error'] = 'Resident information not found.';
    header('Location: ../../login.php');
    exit;
}

// For managers, allow viewing specific resident via GET parameter
if (isManager() && isset($_GET['resident_id'])) {
    $residentId = (int)$_GET['resident_id'];
}

// Set page title
$pageTitle = 'My Clearance Status';
if (isManager()) {
    $pageTitle = 'Clearance Status';
}

try {
    // Get resident information with current semester data
    $residentSql = "
        SELECT 
            u.first_name, u.last_name, u.email, r.student_id,
            ro.room_id, rm.number as room_number, rm.monthly_rental,
            ro.rental_balance, ro.room_status as occupancy_room_status,
            s.academic_year, s.term, s.start_date, s.end_date,
            cr.clearance_id, cr.status as overall_status,
            cr.rental_fee_status, cr.fine_status, cr.room_status as clearance_room_status,
            cr.document_status, cr.due_date, cr.date_cleared
        FROM residents r
        JOIN users u ON r.user_id = u.user_id
        LEFT JOIN resident_occupancy ro ON r.resident_id = ro.resident_id AND ro.active = 1
        LEFT JOIN rooms rm ON ro.room_id = rm.room_id
        LEFT JOIN semesters s ON ro.semester_id = s.semester_id
        LEFT JOIN clearance_records cr ON ro.occupancy_id = cr.occupancy_id
        WHERE r.resident_id = :resident_id AND r.active = 1
        ORDER BY s.start_date DESC
        LIMIT 1
    ";
    
    $stmt = $pdo->prepare($residentSql);
    $stmt->bindParam(':resident_id', $residentId);
    $stmt->execute();
    $resident = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$resident) {
        $_SESSION['error'] = 'Resident not found or no active enrollment.';
        redirectToUserDashboard();
    }
    
    // Get unpaid fines
    $finesSql = "
        SELECT 
            f.name as fine_name, f.description, f.amount as fine_amount,
            rf.status, rf.violation_date, rf.date_issued,
            (f.amount - COALESCE(rf.amount_paid, 0)) as amount_due
        FROM resident_fines rf
        JOIN fines f ON rf.fine_id = f.fine_id
        JOIN resident_occupancy ro ON rf.occupancy_id = ro.occupancy_id
        WHERE ro.resident_id = :resident_id AND ro.active = 1 AND rf.status != 'Paid'
        ORDER BY rf.date_issued DESC
    ";
    
    $finesStmt = $pdo->prepare($finesSql);
    $finesStmt->bindParam(':resident_id', $residentId);
    $finesStmt->execute();
    $unpaidFines = $finesStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get document submission status
    $docsSql = "
        SELECT 
            d.name as document_name, d.description,
            ds.submitted, ds.submission_date
        FROM document_submissions ds
        JOIN documents d ON ds.document_id = d.document_id
        JOIN resident_occupancy ro ON ds.occupancy_id = ro.occupancy_id
        WHERE ro.resident_id = :resident_id AND ro.active = 1
        ORDER BY d.document_id
    ";
    
    $docsStmt = $pdo->prepare($docsSql);
    $docsStmt->bindParam(':resident_id', $residentId);
    $docsStmt->execute();
    $documents = $docsStmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    error_log("Clearance Status Error: " . $e->getMessage());
    $_SESSION['error'] = 'An error occurred while fetching clearance data.';
    redirectToUserDashboard();
}

// Calculate totals
$totalFines = array_sum(array_column($unpaidFines, 'amount_due'));
$totalOwed = $resident['rental_balance'] + $totalFines;

// Include header
include '../../../includes/header.php';
?>

<!-- Resident-specific Sidebar -->
<div class="hidden md:flex md:flex-shrink-0">
    <div class="flex flex-col w-64 border-r border-gray-200 bg-white">
        <div class="flex items-center justify-center h-16 px-4 border-b border-gray-200">
            <h1 class="text-xl font-['Pacifico'] text-primary">DormClear</h1>
        </div>
        <div class="flex flex-col flex-grow px-4 py-4 overflow-y-auto">
            <div class="mb-4">
                <p class="px-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Dashboard</p>
                <a href="/clearance/project/public/role-admin/clearance-status/resident-clearance.php" class="sidebar-link active flex items-center px-2 py-2 mt-1 text-sm font-medium text-gray-600 rounded-md hover:bg-gray-50 hover:text-gray-900">
                    <div class="w-5 h-5 mr-3 flex items-center justify-center">
                        <i class="ri-dashboard-line"></i>
                    </div>
                    My Clearance
                </a>
            </div>

            <?php if (isTreasurer()): ?>
            <div class="mb-4">
                <p class="px-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Finance</p>
                <a href="/clearance/project/public/role-admin/fines/fine-list.php" class="sidebar-link flex items-center px-2 py-2 mt-1 text-sm font-medium text-gray-600 rounded-md hover:bg-gray-50 hover:text-gray-900">
                    <div class="w-5 h-5 mr-3 flex items-center justify-center">
                        <i class="ri-bill-line"></i>
                    </div>
                    Manage Fines
                </a>
                <a href="/clearance/project/public/role-admin/payment-history/payment-history-list.php" class="sidebar-link flex items-center px-2 py-2 mt-1 text-sm font-medium text-gray-600 rounded-md hover:bg-gray-50 hover:text-gray-900">
                    <div class="w-5 h-5 mr-3 flex items-center justify-center">
                        <i class="ri-history-line"></i>
                    </div>
                    Payment History
                </a>
            </div>
            <?php endif; ?>

            <div class="mt-auto">
                <form action="../../logout.php" method="POST" class="m-0">
                    <button type="submit" class="w-full sidebar-link flex items-center px-2 py-2 mt-1 text-sm font-medium text-gray-600 rounded-md hover:bg-gray-50 hover:text-gray-900">
                        <div class="w-5 h-5 mr-3 flex items-center justify-center">
                            <i class="ri-logout-box-line"></i>
                        </div>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Page Header -->
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900"><?php echo $pageTitle; ?></h1>
    <p class="mt-1 text-sm text-gray-500">
        <?php echo $resident['academic_year'] . ' - ' . $resident['term'] . ' Semester'; ?>
    </p>
</div>

<!-- Overall Status Card -->
<div class="bg-white rounded-lg shadow mb-6">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900">Overall Clearance Status</h2>
    </div>
    <div class="p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-xl font-bold text-gray-900"><?php echo $resident['first_name'] . ' ' . $resident['last_name']; ?></h3>
                <p class="text-gray-500">Student ID: <?php echo $resident['student_id']; ?></p>
                <p class="text-gray-500">Room: <?php echo $resident['room_number']; ?></p>
            </div>
            <div class="text-right">
                <?php 
                $statusClass = $resident['overall_status'] === 'Cleared' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800';
                ?>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?php echo $statusClass; ?>">
                    <?php echo $resident['overall_status']; ?>
                </span>
                <?php if ($resident['due_date']): ?>
                <p class="text-sm text-gray-500 mt-1">Due: <?php echo formatDate($resident['due_date']); ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if ($resident['overall_status'] === 'Cleared'): ?>
        <p class="text-green-600">✓ Your clearance has been approved! Cleared on <?php echo formatDate($resident['date_cleared']); ?></p>
        <?php else: ?>
        <p class="text-amber-600">⚠ Please complete all requirements below to get your clearance approved.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Requirements Status Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <!-- Rental Fee Status -->
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-medium text-gray-900">Rental Fees</h3>
            <?php 
            $rentalStatusClass = $resident['rental_fee_status'] === 'Cleared' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
            ?>
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium <?php echo $rentalStatusClass; ?>">
                <?php echo $resident['rental_fee_status']; ?>
            </span>
        </div>
        <p class="text-2xl font-bold text-gray-900"><?php echo formatCurrency($resident['rental_balance']); ?></p>
        <p class="text-sm text-gray-500">Outstanding Balance</p>
    </div>
    
    <!-- Fines Status -->
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-medium text-gray-900">Fines</h3>
            <?php 
            $fineStatusClass = $resident['fine_status'] === 'Cleared' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
            ?>
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium <?php echo $fineStatusClass; ?>">
                <?php echo $resident['fine_status']; ?>
            </span>
        </div>
        <p class="text-2xl font-bold text-gray-900"><?php echo formatCurrency($totalFines); ?></p>
        <p class="text-sm text-gray-500"><?php echo count($unpaidFines); ?> unpaid fine(s)</p>
    </div>
    
    <!-- Room Status -->
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-medium text-gray-900">Room Status</h3>
            <?php 
            $roomStatusClass = $resident['clearance_room_status'] === 'Cleared' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800';
            ?>
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium <?php echo $roomStatusClass; ?>">
                <?php echo $resident['clearance_room_status']; ?>
            </span>
        </div>
        <p class="text-lg font-semibold text-gray-900"><?php echo $resident['occupancy_room_status']; ?></p>
        <p class="text-sm text-gray-500">Current Status</p>
    </div>
    
    <!-- Documents Status -->
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-medium text-gray-900">Documents</h3>
            <?php 
            $docStatusClass = $resident['document_status'] === 'Cleared' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800';
            ?>
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium <?php echo $docStatusClass; ?>">
                <?php echo $resident['document_status']; ?>
            </span>
        </div>
        <?php 
        $submittedDocs = array_filter($documents, function($doc) { return $doc['submitted'] === 'True'; });
        ?>
        <p class="text-lg font-semibold text-gray-900"><?php echo count($submittedDocs) . '/' . count($documents); ?></p>
        <p class="text-sm text-gray-500">Submitted</p>
    </div>
</div>

<!-- Detailed Information -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Unpaid Fines -->
    <?php if (!empty($unpaidFines)): ?>
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Outstanding Fines</h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <?php foreach ($unpaidFines as $fine): ?>
                <div class="border-l-4 border-red-400 pl-4">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="font-medium text-gray-900"><?php echo $fine['fine_name']; ?></h4>
                            <p class="text-sm text-gray-600"><?php echo $fine['description']; ?></p>
                            <p class="text-xs text-gray-500">Violation Date: <?php echo formatDate($fine['violation_date']); ?></p>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-bold text-red-600"><?php echo formatCurrency($fine['amount_due']); ?></p>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <?php echo $fine['status']; ?>
                            </span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Document Status -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Required Documents</h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <?php foreach ($documents as $doc): ?>
                <div class="flex items-center justify-between p-3 border rounded-lg">
                    <div>
                        <h4 class="font-medium text-gray-900"><?php echo $doc['document_name']; ?></h4>
                        <p class="text-sm text-gray-600"><?php echo $doc['description']; ?></p>
                        <?php if ($doc['submitted'] === 'True' && $doc['submission_date']): ?>
                        <p class="text-xs text-gray-500">Submitted: <?php echo formatDate($doc['submission_date']); ?></p>
                        <?php endif; ?>
                    </div>
                    <div>
                        <?php if ($doc['submitted'] === 'True'): ?>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <i class="ri-check-line mr-1"></i>
                            Submitted
                        </span>
                        <?php else: ?>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            <i class="ri-time-line mr-1"></i>
                            Pending
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Summary Card -->
<?php if ($totalOwed > 0): ?>
<div class="bg-red-50 border border-red-200 rounded-lg p-6 mt-6">
    <div class="flex items-center">
        <div class="flex-shrink-0">
            <i class="ri-error-warning-line text-red-400 text-2xl"></i>
        </div>
        <div class="ml-3">
            <h3 class="text-lg font-medium text-red-800">Action Required</h3>
            <div class="mt-2 text-sm text-red-700">
                <p>You have outstanding obligations totaling <strong><?php echo formatCurrency($totalOwed); ?></strong></p>
                <ul class="list-disc list-inside mt-2 space-y-1">
                    <?php if ($resident['rental_balance'] > 0): ?>
                    <li>Rental balance: <?php echo formatCurrency($resident['rental_balance']); ?></li>
                    <?php endif; ?>
                    <?php if ($totalFines > 0): ?>
                    <li>Unpaid fines: <?php echo formatCurrency($totalFines); ?></li>
                    <?php endif; ?>
                </ul>
                <p class="mt-2">Please settle these amounts and complete all requirements to receive your clearance.</p>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php
// Include footer
include '../../../includes/footer.php';
?>
<?php
// File: /public/semesters/index.php
require_once __DIR__ . '/../../../config/bootstrap.php';
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../includes/role_auth_check.php';

requireRole(['Manager']);

/**
 * Function to check if an active semester currently exists.
 *
 * @param mixed $pdo
 * @return bool 
 */
function checkActiveSemester($pdo) {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM semesters WHERE active = 1");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['count'] > 0;
}

$pdo = connect_db(); 
$semesters = []; // Initialize empty array for semesters

// Fetch semesters if database connection is successful
if ($pdo) {
    $stmt = $pdo->query("SELECT * FROM semesters ORDER BY start_date DESC");
    $semesters = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Process actions
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_POST['action'])) {
        switch($_POST['action']){
            case 'activate':
                // Activate semester logic here
                if(isset($_POST['semester_id'])) {
                    // Deactivate current active semester
                    $pdo->exec("UPDATE semesters SET active = 0 WHERE active = 1");

                    // Activate selected semester
                    $stmt = $pdo->prepare("UPDATE semesters SET active = 1 WHERE semester_id = :semester_id");
                    $stmt->bindParam(':semester_id', $_POST['semester_id'], PDO::PARAM_INT);
                    $stmt->execute();

                    $_SESSION['success'] = 'Semester activated successfully!';
                } 
                break;

            case 'create':
                // Create new semester logic here

                // TO DO: 
                $academic_year = $_POST['academic_year'] ?? '';
                $term = $_POST['term'] ?? '';
                $start_date = $_POST['start_date'] ?? '';
                $end_date = $_POST['end_date'] ?? '';

                // If all fields are not empty
                if($academic_year && $term && $start_date && $end_date) {
                    $stmt = $pdo->prepare("INSERT INTO semesters (academic_year, term, active, start_date, end_date) VALUES (:academic_year, :term, :active, :start_date, :end_date)");

                    // If there is no currently active semester, make this active
                    $active = !checkActiveSemester($pdo) ? 1 : 0;

                    $stmt->bindParam(':academic_year', $academic_year);
                    $stmt->bindParam(':term', $term);
                    $stmt->bindParam(':active', $active, PDO::PARAM_INT);
                    $stmt->bindParam(':start_date', $start_date);
                    $stmt->bindParam(':end_date', $end_date);

                    if($stmt->execute()) {
                        $_SESSION['success'] = 'New semester created successfully!';
                    } else {
                        $_SESSION['error'] = 'Failed to create semester.';
                    }
                }
                else {
                    $_SESSION['error'] = 'All fields are required.';
                }
                break;
            }
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}


$pageTitle = 'Semester Management';
//Include header
include __DIR__ . '/../../../includes/header.php';
?>

    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Semester Management</h1>
                <p class="mt-1 text-sm text-gray-500">Create and manage academic semesters</p>
            </div>
            <button id="openCreateModal" class="bg-primary text-white px-4 py-2 rounded-md flex items-center space-x-2 hover:bg-indigo-700 transition-colors">
                <i class="ri-add-line"></i>
                <span>New Semester</span>
            </button>
        </div>

        <!-- Success Message -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <!-- Error Message -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <ul class="list-disc list-inside">
                    <li><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></li>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Create Semester Form (hidden by default) -->
        <div id="createForm" class="mb-6 bg-white rounded-lg shadow p-6 hidden">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Create New Semester</h2>
            <form id="semesterForm" method="POST">
                <!-- $_POST['action'] = 'create' -->
                <input type="hidden" name="action" value="create">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="academic_year" class="block text-sm font-medium text-gray-700 mb-1">Academic Year</label>
                        <input type="text" id="academic_year" name="academic_year" placeholder="e.g., 2024-2025" required
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary">
                        <p class="text-xs text-gray-500 mt-1">Format: YYYY-YYYY (e.g., 2024-2025)</p>
                    </div>
                    
                    <div>
                        <label for="term" class="block text-sm font-medium text-gray-700 mb-1">Term</label>
                        <select id="term" name="term" required 
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary">
                            <option value="">Select Term</option>
                            <option value="First">First Semester</option>
                            <option value="Second">Second Semester</option>
                            <option value="Summer">Summer</option>
                        </select>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                        <input type="date" id="start_date" name="start_date" required
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary">
                    </div>
                    
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                        <input type="date" id="end_date" name="end_date" required
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary">
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" id="cancelBtn" class="px-4 py-2 bg-white border border-gray-300 rounded-md font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-primary text-white rounded-md font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        Create Semester
                    </button>
                </div>
            </form>
        </div>

        <!-- Semesters List -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Academic Semesters</h2>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Academic Year</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Term</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>

                    <!-- If no semester records -->
                    <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($semesters)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">No semesters found. Create your first semester.</td>
                        </tr>

                    <!-- If there are existing records -->
                    <?php else: ?>
                        <!-- Display each record -->
                        <?php foreach ($semesters as $semester): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($semester['academic_year']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($semester['term']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?php echo date('M d, Y', strtotime($semester['start_date'])); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?php echo date('M d, Y', strtotime($semester['end_date'])); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <!-- If semester active -->
                                    <?php if ($semester['active']): ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    <!-- If semester inactive -->
                                    <?php else: ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            Inactive
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <!-- If semester is inactive, display active button -->
                                    <?php if (!$semester['active']): ?>
                                        <form method="POST" class="inline-block">
                                            <input type="hidden" name="action" value="activate">
                                            <input type="hidden" name="semester_id" value="<?php echo $semester['semester_id']; ?>">
                                            <button type="submit" class="text-blue-600 hover:text-blue-900">Activate</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Simple JavaScript for toggling the form -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Elements
            const openCreateBtn = document.getElementById('openCreateModal');
            const cancelBtn = document.getElementById('cancelBtn');
            const createForm = document.getElementById('createForm');
            const semesterForm = document.getElementById('semesterForm');
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');
            const successMessage = document.getElementById('successMessage');
            const errorMessage = document.getElementById('errorMessage');
            
            // Toggle create form
            openCreateBtn.addEventListener('click', function() {
                createForm.classList.remove('hidden');
            });
            
            cancelBtn.addEventListener('click', function() {
                createForm.classList.add('hidden');
            });
            
            // Date validation
            endDateInput.addEventListener('change', function() {
                if (startDateInput.value && this.value) {
                    const startDate = new Date(startDateInput.value);
                    const endDate = new Date(this.value);
                    
                    if (endDate <= startDate) {
                        alert('End date must be after start date');
                        this.value = '';
                    }
                }
            });
            
            // // Form submission (just for demonstration - would be handled by PHP)
            // semesterForm.addEventListener('submit', function(e) {
            //     e.preventDefault();
                
            //     // Hide any existing messages
            //     successMessage.classList.add('hidden');
            //     errorMessage.classList.add('hidden');
                
            //     // Simple client-side validation
            //     if (!this.checkValidity()) {
            //         errorMessage.textContent = 'Please fill out all required fields.';
            //         errorMessage.classList.remove('hidden');
            //         return;
            //     }
                
            //     // In a real app, form would be submitted to the server
            //     // For this demo, just show success message
            //     successMessage.classList.remove('hidden');
            //     createForm.classList.add('hidden');
                
            //     // In a real app, page would refresh with new data
            //     // For demo, clear the form
            //     this.reset();
            // });
            
            // Toggle example rows vs empty state
            // This would be handled by PHP in the real application
            // const toggleEmptyState = function() {
            //     const semesterRows = document.querySelectorAll('.semester-row');
            //     const emptyState = document.querySelector('.empty-state');
                
            //     if (semesterRows.length > 0) {
            //         emptyState.classList.add('hidden');
            //         semesterRows.forEach(row => row.classList.remove('hidden'));
            //     } else {
            //         emptyState.classList.remove('hidden');
            //     }
            // };
            
            // // Initialize display
            // toggleEmptyState();
        });
    </script>

<?php include '../../../includes/footer.php';?>
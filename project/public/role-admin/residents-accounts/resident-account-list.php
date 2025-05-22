<?php
require_once __DIR__ . '/../../../config/bootstrap.php';

require_once __DIR__ . '/../../../includes/auth_check.php';

require_once  'functions/index-functions.php';

$pageTitle = 'Resident Accounts';

include __DIR__ . '/../../../includes/header.php'; 

?>

<!-- Page Header -->
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Resident Accounts</h1>
        <p class="mt-1 text-sm text-gray-500">Manage resident accounts in the dormitory system</p>
    </div>
    <button id="openCreateModal" class="bg-primary text-white px-4 py-2 rounded-md flex items-center space-x-2 hover:bg-indigo-700 transition-colors transform hover:scale-105 duration-200">
        <i class="ri-user-add-line"></i>
        <span>New Resident</span>
    </button>
</div>

<!-- Toast Notification Container -->
<div id="toast-container" class="fixed top-4 right-4 z-50 flex flex-col gap-2"></div>

<!-- Modal Backdrop - Shared by all modals -->
<div id="modal-backdrop" class="fixed inset-0 bg-black bg-opacity-50 z-40 transition-opacity duration-300 ease-in-out opacity-0 pointer-events-none"></div>

<!-- Residents Listing -->
<div class="bg-white shadow rounded-lg overflow-hidden">
    <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center">
        <h2 class="text-lg font-semibold text-gray-900">All Residents</h2>
        <div class="flex items-center space-x-2">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="ri-search-line text-gray-400"></i>
                </div>
                <input type="text" id="searchResidents" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary focus:border-primary block w-full pl-10 p-2" placeholder="Search residents...">
            </div>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($resident_accounts)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No residents found</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($resident_accounts as $resident_account): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $resident_account->user_id; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-primary bg-opacity-10 flex items-center justify-center text-primary">
                                        <span class="font-medium"><?php echo getInitials($resident_account->first_name . ' ' . $resident_account->last_name); ?></span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900"><?php echo $resident_account->first_name . ' ' . $resident_account->last_name; ?></div>
                                        <?php if (!empty($resident_account->middle_name)): ?>
                                            <div class="text-sm text-gray-500">Middle name: <?php echo $resident_account->middle_name; ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $resident_account->email; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $resident_account->phone_number ?? 'N/A'; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex space-x-2 justify-end">
                                    <button 
                                        class="view-resident text-indigo-600 hover:text-indigo-900 transform hover:scale-110 transition-transform" 
                                        data-id="<?php echo $resident_account->user_id; ?>"
                                        title="View Details">
                                        <i class="ri-eye-line"></i>
                                    </button>
                                    <button 
                                        class="edit-resident text-blue-600 hover:text-blue-900 transform hover:scale-110 transition-transform" 
                                        data-id="<?php echo $resident_account->user_id; ?>"
                                        title="Edit Resident">
                                        <i class="ri-edit-line"></i>
                                    </button>
                                    <button 
                                        class="delete-resident text-red-600 hover:text-red-900 transform hover:scale-110 transition-transform" 
                                        data-id="<?php echo $resident_account->user_id; ?>"
                                        title="Delete Resident">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination (if needed) -->
    <?php if (!empty($resident_accounts) && count($resident_accounts) > 10): ?>
    <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 sm:px-6">
        <div class="flex-1 flex justify-between sm:hidden">
            <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Previous</a>
            <a href="#" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Next</a>
        </div>
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-700">
                    Showing <span class="font-medium">1</span> to <span class="font-medium">10</span> of <span class="font-medium"><?php echo count($resident_accounts); ?></span> results
                </p>
            </div>
            <div>
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                    <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                        <span class="sr-only">Previous</span>
                        <i class="ri-arrow-left-s-line"></i>
                    </a>
                    <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-primary text-sm font-medium text-white hover:bg-indigo-700">1</a>
                    <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">2</a>
                    <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                        <span class="sr-only">Next</span>
                        <i class="ri-arrow-right-s-line"></i>
                    </a>
                </nav>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Include Modal Components -->
<?php include __DIR__ . '/create-resident-account.php'; ?>
<?php include __DIR__ . '/edit-resident-account.php'; ?>
<?php include __DIR__ . '/show-resident-account-details.php'; ?>
<?php include __DIR__ . '/delete-resident-account.php'; ?>

<!-- JavaScript for modals and actions -->
 <script>
// JavaScript without AJAX for modals and actions
document.addEventListener('DOMContentLoaded', function() {
    // Toast notification system - keep this functionality
    function showToast(message, type = 'success', duration = 5000) {
        const toast = document.createElement('div'); 
        toast.className = `transform transition-all duration-300 ease-out scale-95 opacity-0 flex items-center p-4 mb-3 text-sm rounded-lg ${
            type === 'success' ? 'bg-green-100 text-green-700' : 
            type === 'error' ? 'bg-red-100 text-red-700' : 
            type === 'warning' ? 'bg-yellow-100 text-yellow-700' : 
            'bg-blue-100 text-blue-700'
        }`;
        const iconClass = type === 'success' ? 'ri-check-line' : 
                        type === 'error' ? 'ri-error-warning-line' : 
                        type === 'warning' ? 'ri-alert-line' : 
                        'ri-information-line';
        
        toast.innerHTML = `
            <i class="${iconClass} mr-2 text-lg"></i>
            <span>${message}</span>
            <button type="button" class="ml-auto text-gray-500 hover:text-gray-900">
                <i class="ri-close-line"></i>
            </button>
        `;
        
        const toastContainer = document.getElementById('toast-container');
        toastContainer.appendChild(toast);
        setTimeout(() => {
            toast.classList.remove('scale-95', 'opacity-0');
            toast.classList.add('scale-100', 'opacity-100');
        }, 10);
        const closeButton = toast.querySelector('button');
        closeButton.addEventListener('click', () => {
            closeToast(toast);
        });
        setTimeout(() => {
            closeToast(toast);
        }, duration);
    }
    
    function closeToast(toast) {
        toast.classList.remove('scale-100', 'opacity-100');
        toast.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            toast.remove();
        }, 300);
    }
    
    <?php if (isset($_SESSION['success'])): ?>
        showToast('<?php echo $_SESSION['success']; ?>', 'success');
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['errors']) && isset($_SESSION['errors']['general'])): ?>
        showToast('<?php echo $_SESSION['errors']['general']; ?>', 'error');
        <?php unset($_SESSION['errors']); ?>
    <?php endif; ?>
    
    // Modal functions 
    const modalBackdrop = document.getElementById('modal-backdrop');
    
    // Function to display a modal
    function showModal(modal) {
        modalBackdrop.classList.add('opacity-100');
        modalBackdrop.classList.remove('pointer-events-none');
        modal.classList.remove('hidden');
        setTimeout(() => {
            const modalContent = modal.querySelector('.modal-content');
            if (modalContent) {
                modalContent.classList.remove('opacity-0', 'translate-y-4', 'scale-95');
                modalContent.classList.add('opacity-100', 'translate-y-0', 'scale-100');
            }
        }, 10);
    }
    
    function hideModal(modal) {
        const modalContent = modal.querySelector('.modal-content');
        if (modalContent) {
            modalContent.classList.remove('opacity-100', 'translate-y-0', 'scale-100');
            modalContent.classList.add('opacity-0', 'translate-y-4', 'scale-95');
        }
        
        modalBackdrop.classList.remove('opacity-100');
        modalBackdrop.classList.add('pointer-events-none');
        
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    // Create modal functionality 
    const createModal = document.getElementById('createResidentModal');
    const openCreateBtn = document.getElementById('openCreateModal');
    const closeCreateBtn = document.getElementById('closeCreateModal');
    const cancelCreateBtn = document.getElementById('cancelCreateBtn');
    
    // If create resident button is clicked, shows the create resident modal
    if (openCreateBtn) {
        openCreateBtn.addEventListener('click', function() {
            showModal(createModal);
        });
    }
    
    if (closeCreateBtn && cancelCreateBtn) {
        [closeCreateBtn, cancelCreateBtn].forEach(btn => {
            btn.addEventListener('click', function() {
                hideModal(createModal);
                setTimeout(() => {
                    if (document.getElementById('createResidentForm')) {
                        document.getElementById('createResidentForm').reset();
                    }
                }, 300);
            });
        });
    }
    
    // Edit modal functionality - for opening/closing only
    const editModal = document.getElementById('editResidentModal');
    const closeEditBtn = document.getElementById('closeEditModal');
    const cancelEditBtn = document.getElementById('cancelEditBtn');
    const editButtons = document.querySelectorAll('.edit-resident');
    
    editButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const residentId = this.getAttribute('data-id');
            // Redirect to the edit page instead of using a modal with AJAX
            window.location.href = `/residents-accounts/edit.php?id=${residentId}`;
        });
    });
    
    if (closeEditBtn && cancelEditBtn) {
        [closeEditBtn, cancelEditBtn].forEach(btn => {
            btn.addEventListener('click', function() {
                hideModal(editModal);
            });
        });
    }
    
    // View modal functionality
    const viewModal = document.getElementById('viewResidentModal');
    const closeViewBtn = document.getElementById('closeViewModal');
    const closeViewDetailBtn = document.getElementById('closeViewBtn');
    const viewButtons = document.querySelectorAll('.view-resident');
    
    if (viewModal && viewButtons.length > 0) {
        viewButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const residentId = this.getAttribute('data-id');
                // Redirect to the view page instead of using a modal with AJAX
                window.location.href = `/residents-accounts/view.php?id=${residentId}`;
            });
        });
        
        if (closeViewBtn) {
            closeViewBtn.addEventListener('click', function() {
                hideModal(viewModal);
            });
        }
        if (closeViewDetailBtn) {
            closeViewDetailBtn.addEventListener('click', function() {
                hideModal(viewModal);
            });
        }
    }
    
    // Delete functionality - modified to use standard form submission
    const deleteModal = document.getElementById('deleteResidentModal');
    const closeDeleteBtn = document.getElementById('closeDeleteModal');
    const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
    const deleteButtons = document.querySelectorAll('.delete-resident');
    
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const residentId = this.getAttribute('data-id');
            const row = this.closest('tr');
            const name = row.querySelector('td:nth-child(2) .text-sm.font-medium.text-gray-900')?.textContent.trim() || 'this resident';
            
            // Set the form action with the ID
            const deleteForm = document.getElementById('deleteResidentForm');
            if (deleteForm) {
                deleteForm.action = `/residents-accounts/functions/delete-functions.php`;
                
                const deleteIdInput = document.getElementById('delete_resident_id');
                const deleteNameSpan = document.getElementById('delete_resident_name');
                
                if (deleteIdInput && deleteNameSpan) {
                    deleteIdInput.value = residentId;
                    deleteNameSpan.textContent = name;
                    showModal(deleteModal);
                }
            }
        });
    });
    
    if (closeDeleteBtn && cancelDeleteBtn) {
        [closeDeleteBtn, cancelDeleteBtn].forEach(btn => {
            btn.addEventListener('click', function() {
                hideModal(deleteModal);
            });
        });
    }
    
    // Search functionality - keep this as it works without AJAX
    const searchInput = document.getElementById('searchResidents');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchValue)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
    
    // Close modals when clicking outside
    if (modalBackdrop) {
        modalBackdrop.addEventListener('click', function() {
            if (createModal) hideModal(createModal);
            if (editModal) hideModal(editModal);
            if (viewModal) hideModal(viewModal);
            if (deleteModal) hideModal(deleteModal);
        });
    }
    
    // Close modals with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (createModal) hideModal(createModal);
            if (editModal) hideModal(editModal);
            if (viewModal) hideModal(viewModal);
            if (deleteModal) hideModal(deleteModal);
        }
    });
});
</script>

<?php
if (isset($_SESSION['errors'])) { unset($_SESSION['errors']); }
if (isset($_SESSION['old'])) { unset($_SESSION['old']); }

include __DIR__ . '/../../includes/footer.php';
?>
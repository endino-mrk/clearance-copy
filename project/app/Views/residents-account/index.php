<?php
// Include helper functions
include_once BASE_PATH . '/app/helpers.php';

// Set page title
$pageTitle = 'Resident Accounts';

// Include header
include BASE_PATH . '/app/Views/components/header.php';
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
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $resident_account->id; ?></td>
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
                                        data-id="<?php echo $resident_account->id; ?>"
                                        title="View Details">
                                        <i class="ri-eye-line"></i>
                                    </button>
                                    <button 
                                        class="edit-resident text-blue-600 hover:text-blue-900 transform hover:scale-110 transition-transform" 
                                        data-id="<?php echo $resident_account->id; ?>"
                                        title="Edit Resident">
                                        <i class="ri-edit-line"></i>
                                    </button>
                                    <button 
                                        class="delete-resident text-red-600 hover:text-red-900 transform hover:scale-110 transition-transform" 
                                        data-id="<?php echo $resident_account->id; ?>"
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
<?php include BASE_PATH . '/app/Views/residents-account/create.php'; ?>
<?php include BASE_PATH . '/app/Views/residents-account/edit.php'; ?>
<?php include BASE_PATH . '/app/Views/residents-account/show.php'; ?>
<?php include BASE_PATH . '/app/Views/residents-account/delete.php'; ?>

<!-- JavaScript for modals and actions -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
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
        function setLoading(button, isLoading) {
            if (isLoading) {
                button.dataset.originalText = button.innerHTML;
                button.disabled = true;
                button.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Processing...
                `;
                button.classList.add('opacity-75', 'cursor-not-allowed');
            } else {
                button.innerHTML = button.dataset.originalText;
                button.disabled = false;
                button.classList.remove('opacity-75', 'cursor-not-allowed');
            }
        }
        
        const modalBackdrop = document.getElementById('modal-backdrop');
        
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
        
        const createModal = document.getElementById('createResidentModal');
        const openCreateBtn = document.getElementById('openCreateModal');
        const closeCreateBtn = document.getElementById('closeCreateModal');
        const cancelCreateBtn = document.getElementById('cancelCreateBtn');
        const createSubmitBtn = document.getElementById('createSubmitBtn');
        
        openCreateBtn.addEventListener('click', function() {
            showModal(createModal);
        });
        
        [closeCreateBtn, cancelCreateBtn].forEach(btn => {
            btn.addEventListener('click', function() {
                hideModal(createModal);
                setTimeout(() => {
                    document.getElementById('createResidentForm').reset();
                    document.querySelectorAll('#createResidentForm .error-message').forEach(el => {
                        el.classList.add('hidden');
                        el.textContent = '';
                    });
                }, 300);
            });
        });
        
        // Handle create form submission with loading state
        document.getElementById('createResidentForm').addEventListener('submit', function(e) {
            setLoading(createSubmitBtn, true);
        });
        
        const editModal = document.getElementById('editResidentModal');
        const closeEditBtn = document.getElementById('closeEditModal');
        const cancelEditBtn = document.getElementById('cancelEditBtn');
        const editButtons = document.querySelectorAll('.edit-resident');
        const editSubmitBtn = document.getElementById('editSubmitBtn');
        
        editButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const residentId = this.getAttribute('data-id');
                fetchResidentData(residentId, 'edit');
            });
        });
        
        [closeEditBtn, cancelEditBtn].forEach(btn => {
            btn.addEventListener('click', function() {
                hideModal(editModal);
                setTimeout(() => {
                    document.getElementById('editResidentForm').reset();
                    document.querySelectorAll('#editResidentForm .error-message').forEach(el => {
                        el.classList.add('hidden');
                        el.textContent = '';
                    });
                }, 300);
            });
        });
        const viewModal = document.getElementById('viewResidentModal');
        const closeViewBtn = document.getElementById('closeViewModal');
        const closeViewDetailBtn = document.getElementById('closeViewBtn');
        const viewButtons = document.querySelectorAll('.view-resident');
        
        viewButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const residentId = this.getAttribute('data-id');
                fetchResidentData(residentId, 'view');
            });
        });
        
        [closeViewBtn, closeViewDetailBtn].forEach(btn => {
            btn.addEventListener('click', function() {
                hideModal(viewModal);
            });
        });
        
        // Delete modal functionality
        const deleteModal = document.getElementById('deleteResidentModal');
        const closeDeleteBtn = document.getElementById('closeDeleteModal');
        const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
        const deleteButtons = document.querySelectorAll('.delete-resident');
        const deleteSubmitBtn = document.getElementById('deleteSubmitBtn');
        
        deleteButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const residentId = this.getAttribute('data-id');
                const row = this.closest('tr');
                const name = row.querySelector('.text-gray-900').textContent.trim();
                document.getElementById('delete_resident_id').value = residentId;
                document.getElementById('delete_resident_name').textContent = name;
                const deleteForm = document.getElementById('deleteResidentForm');
                deleteForm.action = `/residents-account/${residentId}`;
                deleteForm.method = 'DELETE';
                
                showModal(deleteModal);
            });
        });
        
        [closeDeleteBtn, cancelDeleteBtn].forEach(btn => {
            btn.addEventListener('click', function() {
                hideModal(deleteModal);
            });
        });
        document.getElementById('editResidentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            setLoading(editSubmitBtn, true);
            const residentId = document.getElementById('edit_resident_id').value;
            const formData = new FormData(this);
            const data = new URLSearchParams();
            for (const [key, value] of formData.entries()) {
                data.append(key, value);
            }
            
            fetch(`/residents-account/${residentId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: data.toString()
            })
            .then(response => {
                return response.json();
            })
            .then(data => {
                setLoading(editSubmitBtn, false);
                if (data.errors) {
                    Object.keys(data.errors).forEach(key => {
                        const errorElement = document.getElementById(`edit_${key}_error`);
                        if (errorElement) {
                            errorElement.textContent = data.errors[key];
                            errorElement.classList.remove('hidden');
                        }
                    });
                } else if (data.success) {
                    hideModal(editModal);
                    showToast(data.message || 'Resident updated successfully', 'success');
                    updateResidentRow(residentId, formData);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Reset loading state
                setLoading(editSubmitBtn, false);
                showToast('An unexpected error occurred', 'error');
            });
        });
        
        // Function to update the resident row in the table
        function updateResidentRow(residentId, formData) {
            const row = document.querySelector(`tr [data-id="${residentId}"]`).closest('tr');
            if (!row) return;
            
            // Get values from the form
            const firstName = formData.get('first_name');
            const lastName = formData.get('last_name');
            const middleName = formData.get('middle_name') || '';
            const email = formData.get('email');
            const phoneNumber = formData.get('phone_number') || 'N/A';
            
            // Update name cell
            const nameCell = row.querySelector('td:nth-child(2)');
            if (nameCell) {
                const initials = getInitials(firstName + ' ' + lastName);
                nameCell.querySelector('.font-medium').textContent = initials;
                nameCell.querySelector('.text-gray-900').textContent = firstName + ' ' + lastName;
                
                // Update middle name if it exists
                const middleNameEl = nameCell.querySelector('.text-gray-500');
                if (middleNameEl && middleName) {
                    middleNameEl.textContent = 'Middle name: ' + middleName;
                } else if (middleNameEl) {
                    middleNameEl.style.display = 'none';
                }
            }
            
            // Update email cell
            const emailCell = row.querySelector('td:nth-child(3)');
            if (emailCell) {
                emailCell.textContent = email;
            }
            
            // Update phone cell
            const phoneCell = row.querySelector('td:nth-child(4)');
            if (phoneCell) {
                phoneCell.textContent = phoneNumber;
            }
        }
        
        // Helper function to get initials from a name
        function getInitials(name) {
            const words = name.split(' ');
            let initials = '';
            words.forEach(word => {
                if (word.length > 0) {
                    initials += word.charAt(0).toUpperCase();
                }
            });
            return initials.length > 2 ? initials.substring(0, 2) : initials;
        }
        
        document.getElementById('deleteResidentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Set loading state
            setLoading(deleteSubmitBtn, true);
            
            const residentId = document.getElementById('delete_resident_id').value;
            
            // Send DELETE request
            fetch(`/residents-account/${residentId}`, {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                return response.json();
            })
            .then(data => {
                // Reset loading state
                setLoading(deleteSubmitBtn, false);
                
                if (data.success) {
                    // Success! Close the modal
                    hideModal(deleteModal);
                    
                    // Show success toast
                    showToast(data.message || 'Resident deleted successfully', 'success');
                    
                    // Remove the row from the table
                    removeResidentRow(residentId);
                } else {
                    // Show error message
                    showToast(data.message || 'Failed to delete resident', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Reset loading state
                setLoading(deleteSubmitBtn, false);
                showToast('An unexpected error occurred', 'error');
            });
        });
        
        // Function to remove the resident row from the table
        function removeResidentRow(residentId) {
            // Find the row for this resident
            const row = document.querySelector(`tr [data-id="${residentId}"]`).closest('tr');
            if (row) {
                // Add fade out animation
                row.style.transition = 'opacity 0.5s, transform 0.5s';
                row.style.opacity = '0';
                row.style.transform = 'translateX(20px)';
                
                // Remove after animation completes
                setTimeout(() => {
                    row.remove();
                    
                    // If no rows left, show "no residents" message
                    const tbody = document.querySelector('tbody');
                    if (tbody && tbody.querySelectorAll('tr').length === 0) {
                        const noDataRow = document.createElement('tr');
                        noDataRow.innerHTML = `<td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No residents found</td>`;
                        tbody.appendChild(noDataRow);
                    }
                }, 500);
            }
        }
        
        // Function to fetch resident data
        function fetchResidentData(id, mode) {
            fetch(`/residents-account/${id}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (mode === 'edit') {
                    // Populate edit form
                    document.getElementById('edit_resident_id').value = data.id;
                    document.getElementById('edit_first_name').value = data.first_name;
                    document.getElementById('edit_last_name').value = data.last_name;
                    document.getElementById('edit_middle_name').value = data.middle_name || '';
                    document.getElementById('edit_email').value = data.email;
                    document.getElementById('edit_phone_number').value = data.phone_number || '';
                    
                    // Update form action
                    document.getElementById('editResidentForm').action = `/residents-account/${data.id}`;
                    
                    // Show edit modal
                    showModal(editModal);
                } else if (mode === 'view') {
                    // Populate view details
                    document.getElementById('view_first_name').textContent = data.first_name;
                    document.getElementById('view_last_name').textContent = data.last_name;
                    document.getElementById('view_middle_name').textContent = data.middle_name || '-';
                    document.getElementById('view_email').textContent = data.email;
                    document.getElementById('view_phone_number').textContent = data.phone_number || '-';
                    document.getElementById('view_created_at').textContent = new Date(data.created_at).toLocaleDateString();
                    
                    // Set initials
                    const initials = (data.first_name.charAt(0) + data.last_name.charAt(0)).toUpperCase();
                    document.getElementById('view_initials').textContent = initials;
                    
                    // Show view modal
                    showModal(viewModal);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Failed to load resident data', 'error');
            });
        }
        
        // Search functionality
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
        modalBackdrop.addEventListener('click', function() {
            hideModal(createModal);
            hideModal(editModal);
            hideModal(viewModal);
            hideModal(deleteModal);
        });
        
        // Close modals with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                hideModal(createModal);
                hideModal(editModal);
                hideModal(viewModal);
                hideModal(deleteModal);
            }
        });
    });
</script>

<?php
// Include footer
include BASE_PATH . '/app/Views/components/footer.php';
?>
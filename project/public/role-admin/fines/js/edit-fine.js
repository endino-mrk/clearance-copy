// edit-fine.js - Handles the edit fine modal functionality

document.addEventListener('DOMContentLoaded', function() {
    // ----- DOM Elements -----
    const editModal = document.getElementById('editFineModal');
    const closeEditModalBtn = document.getElementById('closeEditModal');
    const cancelEditBtn = document.getElementById('cancelEditFine');
    const editForm = document.getElementById('editFineForm');
    const editStudentId = document.getElementById('edit_student_id');
    const editFineTypeSelect = document.getElementById('edit_fine_type_id');
    const editViolationDate = document.getElementById('edit_violation_date');
    const editDescription = document.getElementById('edit_description');
    const editDescriptionContainer = document.getElementById('editDescriptionContainer');
    const editFineDetails = document.getElementById('editFineDetails');
    const editFineAmount = document.getElementById('editFineAmount');
    const editFineDescription = document.getElementById('editFineDescription');
    const editResidentInfo = document.getElementById('editResidentInfo');
    
    // ----- Event Listeners -----
    
    // Add click handlers to all edit buttons
    document.querySelectorAll('.edit-fine-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const fineId = this.getAttribute('data-id');
            openEditModal(fineId);
        });
    });
    
    // Close modal buttons
    if (closeEditModalBtn) {
        closeEditModalBtn.addEventListener('click', closeEditModal);
    }
    
    if (cancelEditBtn) {
        cancelEditBtn.addEventListener('click', closeEditModal);
    }
    
    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        if (event.target === editModal) {
            closeEditModal();
        }
    });
    
    // Fine type selection change
    if (editFineTypeSelect) {
        editFineTypeSelect.addEventListener('change', handleFineTypeChange);
    }
    
    // Form validation
    if (editForm) {
        editForm.addEventListener('submit', validateEditForm);
    }
    
    // ----- Functions -----
    
    /**
     * Opens the edit modal and loads fine data
     */
    function openEditModal(fineId) {
        if (!editModal) return;
        
        // Show modal
        editModal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        
        if (editForm) {
            // Reset form
            editForm.reset();
            
            // Set the fine ID in the hidden input
            document.getElementById('edit_fine_id').value = fineId;
            
            // Show loading indicator
            if (editResidentInfo) {
                editResidentInfo.innerHTML = `
                    <div class="flex items-center justify-center w-full py-2">
                        <i class="ri-loader-4-line animate-spin text-indigo-600 mr-2"></i>
                        <span class="text-gray-600 text-sm">Loading fine details...</span>
                    </div>
                `;
            }
            
            // Fetch fine details
            fetch(`functions/fetch-function.php?id=${fineId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        showToast(data.error, 'error');
                        closeEditModal();
                        return;
                    }
                    
                    populateEditForm(data);
                })
                .catch(error => {
                    console.error('Error fetching fine details:', error);
                    showToast('Error loading fine details. Please try again.', 'error');
                    closeEditModal();
                });
        }
    }
    
    /**
     * Populates the edit form with fine data
     */
    function populateEditForm(fine) {
        // Hide resident info div (to match the Issue Fine modal style)
        if (editResidentInfo) {
            editResidentInfo.innerHTML = '';
        }
        
        // Set student ID
        if (editStudentId && fine.student_id) {
            editStudentId.value = fine.student_id;
        }
        
        // Set fine type
        if (editFineTypeSelect) {
            editFineTypeSelect.value = fine.fine_id;
            updateFineDetails();
        }
        
        // Set violation date
        if (editViolationDate && fine.violation_date) {
            editViolationDate.value = fine.violation_date;
        }
        
        // Set description if it exists
        if (editDescriptionContainer && editDescription) {
            if (typeof fine.description !== 'undefined') {
                editDescriptionContainer.style.display = 'block';
                editDescription.value = fine.description || '';
            } else {
                editDescriptionContainer.style.display = 'none';
            }
        }
    }
    
    /**
     * Updates fine details when fine type changes
     */
    function handleFineTypeChange() {
        updateFineDetails();
    }
    
    /**
     * Updates the fine details display based on selected fine type
     */
    function updateFineDetails() {
        if (!editFineDetails || !editFineAmount || !editFineDescription || !editFineTypeSelect) return;
        
        const selectedOption = editFineTypeSelect.options[editFineTypeSelect.selectedIndex];
        
        if (selectedOption && selectedOption.value) {
            // Get data attributes
            const amount = selectedOption.getAttribute('data-amount');
            const description = selectedOption.getAttribute('data-description');
            
            // Update fine details display
            if (amount) {
                try {
                    editFineAmount.textContent = parseFloat(amount).toLocaleString('en-PH', { 
                        minimumFractionDigits: 2, 
                        maximumFractionDigits: 2 
                    });
                } catch (e) {
                    editFineAmount.textContent = amount;
                }
            } else {
                editFineAmount.textContent = '0.00';
            }
            
            editFineDescription.textContent = description || 'No description available';
            editFineDetails.classList.remove('hidden');
        } else {
            editFineDetails.classList.add('hidden');
        }
    }
    
    /**
     * Validates the edit form before submission
     */
    function validateEditForm(event) {
        let isValid = true;
        
        // Validate fine type
        if (editFineTypeSelect && !editFineTypeSelect.value) {
            isValid = false;
            showFieldError(editFineTypeSelect, 'Please select a fine type');
        }
        
        // Validate violation date
        if (editViolationDate && !editViolationDate.value) {
            isValid = false;
            showFieldError(editViolationDate, 'Please select a violation date');
        }
        
        if (!isValid) {
            event.preventDefault();
            return false;
        }
        
        // Add loading state to the submit button
        const submitButton = editForm.querySelector('button[type="submit"]');
        if (submitButton) {
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="ri-loader-4-line animate-spin inline-block mr-2"></i> Updating...';
        }
        
        return true;
    }
    
    /**
     * Closes the edit modal
     */
    function closeEditModal() {
        if (!editModal) return;
        
        editModal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
        
        // Reset form
        if (editForm) editForm.reset();
        
        // Hide fine details
        if (editFineDetails) editFineDetails.classList.add('hidden');
    }
    
    /**
     * Shows an error message for a form field
     */
    function showFieldError(inputElement, message) {
        // Get the parent div
        const parentDiv = inputElement.closest('div');
        
        // Remove any existing error
        const existingError = parentDiv.querySelector('.text-red-500');
        if (existingError) {
            existingError.remove();
        }
        
        // Add new error message
        const errorMsg = document.createElement('p');
        errorMsg.className = 'text-red-500 text-xs mt-1';
        errorMsg.textContent = message;
        parentDiv.appendChild(errorMsg);
        
        // Highlight the input
        inputElement.classList.add('border-red-500');
    }
    
    /**
     * Shows a toast message
     */
    function showToast(message, type = 'success', duration = 5000) {
        if (!message) return;
        
        const toast = document.createElement('div');
        toast.className = `transform transition-all duration-300 ease-out scale-95 opacity-0 fixed top-4 right-4 z-50 flex items-center p-4 mb-3 text-sm rounded-lg shadow-lg ${
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
        
        document.body.appendChild(toast);
        
        // Animate in
        setTimeout(() => {
            toast.classList.remove('scale-95', 'opacity-0');
            toast.classList.add('scale-100', 'opacity-100');
        }, 10);
        
        // Setup close button
        const closeButton = toast.querySelector('button');
        if (closeButton) {
            closeButton.addEventListener('click', () => {
                closeToast(toast);
            });
        }
        
        // Auto dismiss
        setTimeout(() => {
            closeToast(toast);
        }, duration);
    }
    
    /**
     * Closes a toast message with animation
     */
    function closeToast(toast) {
        toast.classList.remove('scale-100', 'opacity-100');
        toast.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            toast.remove();
        }, 300);
    }
});
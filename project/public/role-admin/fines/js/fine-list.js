// Updated fine-list.js with fixed resident search

document.addEventListener('DOMContentLoaded', function() {
    // ===== MODAL CONTROLS =====
    const modal = document.getElementById('addFineModal');
    const openModalBtn = document.getElementById('openFineModal');
    const emptyStateBtn = document.getElementById('emptyStateIssueBtn');
    const closeModalBtn = document.getElementById('closeFineModal');
    const cancelBtn = document.getElementById('cancelAddFine');
    
    function openModal() {
        if (!modal) return;
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden'); // Prevent background scrolling
    }
    
    function closeModal() {
        if (!modal) return;
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
        
        // Reset form
        const form = document.getElementById('addFineForm');
        if (form) form.reset();
        
        // Hide resident info and fine details
        const selectedResidentInfo = document.getElementById('selectedResidentInfo');
        if (selectedResidentInfo) selectedResidentInfo.classList.add('hidden');
        
        const fineDetails = document.getElementById('fineDetails');
        if (fineDetails) fineDetails.classList.add('hidden');
        
        const residentDropdown = document.getElementById('residentDropdown');
        if (residentDropdown) residentDropdown.classList.add('hidden');
        
        // Reset hidden resident_id
        const residentIdInput = document.getElementById('resident_id');
        if (residentIdInput) residentIdInput.value = '';
    }
    
    if (openModalBtn) {
        openModalBtn.addEventListener('click', openModal);
    }
    
    if (emptyStateBtn) {
        emptyStateBtn.addEventListener('click', openModal);
    }
    
    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', closeModal);
    }
    
    if (cancelBtn) {
        cancelBtn.addEventListener('click', closeModal);
    }
    
    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            closeModal();
        }
    });
    
    // Function to select a resident
    function selectResident(resident) {
        console.log("Selecting resident:", resident);
        if (!resident) return;
        
        const residentIdInput = document.getElementById('resident_id');
        const residentSearch = document.getElementById('resident'); // UPDATED: Changed from 'residentSearch' to 'resident'
        const selectedResidentInfo = document.getElementById('selectedResidentInfo');
        const residentNameElement = document.getElementById('residentName');
        const residentRoomElement = document.getElementById('residentRoom');
        const residentInitialsElement = document.getElementById('residentInitials');
        const residentDropdown = document.getElementById('residentDropdown');
        
        // Set hidden input value
        if (residentIdInput) residentIdInput.value = resident.id;
        
        // Update search input to show selected resident
        if (residentSearch) {
            // Update to show student ID in the search field
            if (resident.studentId) {
                residentSearch.value = `${resident.fullName} (${resident.studentId})`;
            } else {
                residentSearch.value = resident.fullName;
            }
        }
        
        // Update resident info display
        if (residentNameElement) {
            let nameText = resident.fullName;
            if (resident.studentId) {
                nameText += ` (${resident.studentId})`;
            }
            residentNameElement.textContent = nameText;
        }
        
        let roomText = 'No room assigned';
        if (resident.room) {
            roomText = `Room ${resident.room}`;
        }
        if (residentRoomElement) residentRoomElement.textContent = roomText;
        
        if (residentInitialsElement) {
            residentInitialsElement.textContent = resident.initials || '';
        }
        
        if (selectedResidentInfo) selectedResidentInfo.classList.remove('hidden');
        
        // Hide dropdown
        if (residentDropdown) residentDropdown.classList.add('hidden');
    }
    
    // Setup resident search functionality with AJAX
    const residentSearch = document.getElementById('resident'); // UPDATED: Changed from 'residentSearch' to 'resident' 
    const residentDropdown = document.getElementById('residentDropdown');
    
    if (residentSearch && residentDropdown) {
        // Add loading indicator if it doesn't exist
        let loadingIndicator = document.getElementById('residentSearchLoading');
        if (!loadingIndicator) {
            loadingIndicator = document.createElement('div');
            loadingIndicator.id = 'residentSearchLoading';
            loadingIndicator.className = 'hidden px-4 py-3 text-sm text-gray-500 text-center';
            loadingIndicator.innerHTML = '<i class="ri-loader-4-line animate-spin inline-block mr-2"></i> Searching...';
            residentDropdown.parentNode.insertBefore(loadingIndicator, residentDropdown.nextSibling);
        }
        
        let searchTimeout = null;
        
        residentSearch.addEventListener('focus', function() {
            if (this.value.length < 2) {
                residentDropdown.innerHTML = `
                    <div class="px-4 py-3 text-sm text-gray-500">
                        Type at least 2 characters to search
                    </div>
                `;
                residentDropdown.classList.remove('hidden');
            } else {
                // Trigger search if already has 2+ characters
                this.dispatchEvent(new Event('input'));
            }
        });
        
        residentSearch.addEventListener('input', function() {
            const query = this.value.trim();
            
            // Clear previous timeout
            if (searchTimeout) {
                clearTimeout(searchTimeout);
            }
            
            // Hide dropdown and show loading if query is long enough
            if (query.length < 2) {
                residentDropdown.innerHTML = `
                    <div class="px-4 py-3 text-sm text-gray-500">
                        Type at least 2 characters to search
                    </div>
                `;
                residentDropdown.classList.remove('hidden');
                loadingIndicator.classList.add('hidden');
                return;
            }
            
            // Clear selection if search field is changed
            const residentIdInput = document.getElementById('resident_id');
            const selectedResidentInfo = document.getElementById('selectedResidentInfo');
            if (residentIdInput) residentIdInput.value = '';
            if (selectedResidentInfo) selectedResidentInfo.classList.add('hidden');
            
            // Show loading indicator
            loadingIndicator.classList.remove('hidden');
            
            // Use a timeout to prevent too many requests
            searchTimeout = setTimeout(function() {
                // Perform AJAX request
                fetch(`functions/search-residents.php?query=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        // Hide loading indicator
                        loadingIndicator.classList.add('hidden');
                        
                        // Clear the dropdown
                        residentDropdown.innerHTML = '';
                        
                        if (!data.success) {
                            const errorDiv = document.createElement('div');
                            errorDiv.className = 'px-4 py-3 text-sm text-red-500';
                            errorDiv.textContent = data.message || 'An error occurred';
                            residentDropdown.appendChild(errorDiv);
                        } else if (data.data.length === 0) {
                            const noResults = document.createElement('div');
                            noResults.className = 'px-4 py-3 text-sm text-gray-500';
                            noResults.textContent = 'No residents found';
                            residentDropdown.appendChild(noResults);
                        } else {
                            // Populate dropdown with results
                            data.data.forEach(resident => {
                                const option = document.createElement('div');
                                option.className = 'cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-gray-100';
                                option.setAttribute('data-id', resident.id);
                                
                                // Create content with name, student ID, and room info
                                let studentIdInfo = resident.studentId ? ` (${resident.studentId})` : '';
                                let roomInfo = resident.room ? ` - Room ${resident.room}` : '';
                                
                                option.innerHTML = `
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-6 w-6 rounded-full bg-primary bg-opacity-10 flex items-center justify-center text-primary text-xs">
                                            <span>${resident.initials}</span>
                                        </div>
                                        <span class="ml-3 block font-medium text-gray-900 truncate">
                                            ${resident.fullName}
                                        </span>
                                        <span class="ml-2 truncate text-gray-500">
                                            ${studentIdInfo}${roomInfo}
                                        </span>
                                    </div>
                                `;
                                
                                option.addEventListener('click', () => {
                                    selectResident(resident);
                                });
                                
                                residentDropdown.appendChild(option);
                            });
                        }
                        
                        // Show dropdown
                        residentDropdown.classList.remove('hidden');
                    })
                    .catch(error => {
                        console.error('Error fetching residents:', error);
                        
                        // Hide loading indicator
                        loadingIndicator.classList.add('hidden');
                        
                        // Show error message
                        residentDropdown.innerHTML = `
                            <div class="px-4 py-3 text-sm text-red-500">
                                Error searching residents. Please try again.
                            </div>
                        `;
                        residentDropdown.classList.remove('hidden');
                    });
            }, 300); // 300ms delay
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (residentDropdown &&
                e.target !== residentSearch && 
                !residentSearch.contains(e.target) && 
                !residentDropdown.contains(e.target)) {
                residentDropdown.classList.add('hidden');
                if (loadingIndicator) loadingIndicator.classList.add('hidden');
            }
        });
    } else {
        console.error("Resident search or dropdown elements not found!");
    }
    
    // Check if there's a savedResidentId in the window object and load that resident
    if (window.savedResidentId) {
        console.log("Attempting to load saved resident ID:", window.savedResidentId);
        
        // Show loading in selected resident info
        const selectedResidentInfo = document.getElementById('selectedResidentInfo');
        const residentNameElement = document.getElementById('residentName');
        const residentRoomElement = document.getElementById('residentRoom');
        
        if (selectedResidentInfo && residentNameElement && residentRoomElement) {
            selectedResidentInfo.classList.remove('hidden');
            residentNameElement.innerHTML = '<i class="ri-loader-4-line animate-spin inline-block mr-2"></i> Loading resident...';
            residentRoomElement.textContent = '';
        }
        
        // Fetch resident details
        fetch(`functions/get-resident.php?id=${window.savedResidentId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data) {
                    // Set the resident using the selectResident function
                    selectResident(data.data);
                    
                    // Update search field value
                    const residentSearch = document.getElementById('resident'); // UPDATED
                    if (residentSearch) {
                        if (data.data.studentId) {
                            residentSearch.value = `${data.data.fullName} (${data.data.studentId})`;
                        } else {
                            residentSearch.value = data.data.fullName;
                        }
                    }
                } else {
                    console.warn("Failed to load resident:", data.message);
                    if (selectedResidentInfo) {
                        selectedResidentInfo.classList.add('hidden');
                    }
                }
            })
            .catch(error => {
                console.error("Error loading saved resident:", error);
                if (selectedResidentInfo) {
                    selectedResidentInfo.classList.add('hidden');
                }
            });
    }
    
    // Set up fine type selection
    const fineTypeSelect = document.getElementById('fine_id');
    const fineDetails = document.getElementById('fineDetails');
    const fineAmountElement = document.getElementById('fineAmount');
    const fineDescriptionElement = document.getElementById('fineDescription');
    
    if (fineTypeSelect) {
        fineTypeSelect.addEventListener('change', function() {
            if (!fineDetails || !fineAmountElement || !fineDescriptionElement) return;
            
            const selectedOption = this.options[this.selectedIndex];
            
            if (selectedOption && selectedOption.value) {
                // Get data attributes
                const amount = selectedOption.getAttribute('data-amount');
                const description = selectedOption.getAttribute('data-description');
                
                // Update fine details display
                if (amount) {
                    try {
                        fineAmountElement.textContent = parseFloat(amount).toLocaleString('en-PH', { 
                            minimumFractionDigits: 2, 
                            maximumFractionDigits: 2 
                        });
                    } catch (e) {
                        fineAmountElement.textContent = amount;
                    }
                } else {
                    fineAmountElement.textContent = '0.00';
                }
                
                fineDescriptionElement.textContent = description || 'No description available';
                fineDetails.classList.remove('hidden');
            } else {
                fineDetails.classList.add('hidden');
            }
        });
    }
    
    // Simple search functionality for the fines table
    const searchInput = document.getElementById('searchFines');
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
    
    // Display toast notifications for success/error messages
    function showToast(message, type = 'success', duration = 5000) {
        if (!message) return;
        
        const toast = document.createElement('div'); 
        toast.className = `transform transition-all duration-300 ease-out scale-95 opacity-0 flex items-center p-4 mb-3 text-sm rounded-lg shadow-lg ${
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
        
        let toastContainer = document.getElementById('toast-container');
        if (!toastContainer) {
            // Create toast container if it doesn't exist
            toastContainer = document.createElement('div');
            toastContainer.id = 'toast-container';
            toastContainer.className = 'fixed top-4 right-4 z-50 flex flex-col items-end';
            document.body.appendChild(toastContainer);
        }
        
        toastContainer.appendChild(toast);
        
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
    
    function closeToast(toast) {
        if (!toast) return;
        toast.classList.remove('scale-100', 'opacity-100');
        toast.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            toast.remove();
        }, 300);
    }
    
    // Check for success or error messages
    if (window.successMessage) {
        showToast(window.successMessage, 'success');
    }
    
    if (window.errorMessage) {
        showToast(window.errorMessage, 'error');
    }
    
    // Show modal if there were errors
    if (window.location.hash === '#addFine') {
        openModal();
    }
    
    // Form validation
    const fineForm = document.getElementById('addFineForm');
    if (fineForm) {
        fineForm.addEventListener('submit', function(event) {
            let isValid = true;
            
            // Check if resident is selected or entered
            const resident = document.getElementById('resident');
            const residentId = document.getElementById('resident_id');
            
            if ((!resident || !resident.value) && (!residentId || !residentId.value)) {
                isValid = false;
                const residentError = document.createElement('p');
                residentError.className = 'text-red-500 text-xs mt-1';
                residentError.textContent = 'Please select or enter a resident';
                
                // Remove existing error message if any
                const existingError = resident.parentElement.parentElement.querySelector('.text-red-500');
                if (existingError) {
                    existingError.remove();
                }
                
                resident.parentElement.parentElement.appendChild(residentError);
            }
            
            // Check if fine type is selected
            const fineId = document.getElementById('fine_id');
            if (!fineId || !fineId.value) {
                isValid = false;
                const fineError = document.createElement('p');
                fineError.className = 'text-red-500 text-xs mt-1';
                fineError.textContent = 'Please select a fine type';
                
                // Remove existing error message if any
                const existingError = fineId.parentElement.parentElement.querySelector('.text-red-500');
                if (existingError) {
                    existingError.remove();
                }
                
                fineId.parentElement.parentElement.appendChild(fineError);
            }
            
            // Check if violation date is provided
            const violationDate = document.getElementById('violation_date');
            if (!violationDate || !violationDate.value) {
                isValid = false;
                const dateError = document.createElement('p');
                dateError.className = 'text-red-500 text-xs mt-1';
                dateError.textContent = 'Please select a violation date';
                
                // Remove existing error message if any
                const existingError = violationDate.parentElement.querySelector('.text-red-500');
                if (existingError) {
                    existingError.remove();
                }
                
                violationDate.parentElement.appendChild(dateError);
            }
            
            if (!isValid) {
                event.preventDefault();
                return false;
            }
            
            // Add loading state to the submit button
            const submitButton = fineForm.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="ri-loader-4-line animate-spin inline-block mr-2"></i> Processing...';
            }
            
            return true;
        });
    }
});
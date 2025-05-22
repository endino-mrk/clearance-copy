// payment.js - Handles fine payment functionality

document.addEventListener('DOMContentLoaded', function() {
    // ===== PAYMENT MODAL CONTROLS =====
    const paymentModal = document.getElementById('paymentModal');
    const closePaymentModalBtn = document.getElementById('closePaymentModal');
    const cancelPaymentBtn = document.getElementById('cancelPayment');
    
    function closePaymentModal() {
        if (!paymentModal) return;
        paymentModal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
        
        // Reset form
        const form = document.getElementById('paymentForm');
        if (form) form.reset();
    }
    
    if (closePaymentModalBtn) {
        closePaymentModalBtn.addEventListener('click', closePaymentModal);
    }
    
    if (cancelPaymentBtn) {
        cancelPaymentBtn.addEventListener('click', closePaymentModal);
    }
    
    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        if (event.target === paymentModal) {
            closePaymentModal();
        }
    });
    
    // Payment form validation
    const paymentForm = document.getElementById('paymentForm');
    const paymentAmountInput = document.getElementById('payment_amount');
    
    if (paymentForm && paymentAmountInput) {
        paymentForm.addEventListener('submit', function(event) {
            let isValid = true;
            
            // Get values
            const amount = parseFloat(paymentAmountInput.value) || 0;
            const balanceText = document.getElementById('payment_balance').textContent;
            const balance = parseFloat(balanceText.replace(/[^0-9.-]+/g, '')) || 0;
            
            // Validate amount is positive
            if (amount <= 0) {
                isValid = false;
                showFieldError(paymentAmountInput, 'Amount must be greater than zero');
            }
            
            // Validate amount doesn't exceed balance
            if (amount > balance) {
                isValid = false;
                showFieldError(paymentAmountInput, 'Amount cannot exceed the remaining balance');
            }
            
            // Validate receipt number
            const receiptInput = document.getElementById('receipt_no');
            if (!receiptInput.value.trim()) {
                isValid = false;
                showFieldError(receiptInput, 'Receipt number is required');
            }
            
            // Validate payment date
            const dateInput = document.getElementById('date_paid');
            if (!dateInput.value) {
                isValid = false;
                showFieldError(dateInput, 'Payment date is required');
            }
            
            if (!isValid) {
                event.preventDefault();
                return false;
            }
            
            // Add loading state to submit button
            const submitButton = paymentForm.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="ri-loader-4-line animate-spin inline-block mr-2"></i> Processing...';
            }
            
            return true;
        });
        
        // Helper function to show field errors
        function showFieldError(inputElement, message) {
            // Remove any existing error
            const parentEl = inputElement.parentElement.parentElement;
            const existingError = parentEl.querySelector('.text-red-500');
            if (existingError) {
                existingError.remove();
            }
            
            // Add new error message
            const errorMsg = document.createElement('p');
            errorMsg.className = 'text-red-500 text-xs mt-1';
            errorMsg.textContent = message;
            parentEl.appendChild(errorMsg);
            
            // Highlight the input
            inputElement.classList.add('border-red-500');
        }
        
        // Clear error when input changes
        paymentAmountInput.addEventListener('input', function() {
            clearFieldError(this);
        });
        
        document.getElementById('receipt_no').addEventListener('input', function() {
            clearFieldError(this);
        });
        
        document.getElementById('date_paid').addEventListener('input', function() {
            clearFieldError(this);
        });
        
        function clearFieldError(inputElement) {
            // Remove error message
            const parentEl = inputElement.parentElement.parentElement;
            const existingError = parentEl.querySelector('.text-red-500');
            if (existingError) {
                existingError.remove();
            }
            
            // Remove highlighting
            inputElement.classList.remove('border-red-500');
        }
    }
    
    // Toast notifications for success/error messages
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
});

// Function to open payment modal
window.openPaymentModal = function(fineId, totalAmount, amountPaid) {
    const modal = document.getElementById('paymentModal');
    if (!modal) return;
    
    // Set form values
    document.getElementById('payment_fine_id').value = fineId;
    
    // Format currency for display
    const formatter = new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP'
    });
    
    // Set formatted amounts
    document.getElementById('payment_fine_amount').textContent = formatter.format(totalAmount);
    document.getElementById('payment_amount_paid').textContent = formatter.format(amountPaid || 0);
    
    // Calculate and display balance
    const balance = totalAmount - (amountPaid || 0);
    document.getElementById('payment_balance').textContent = formatter.format(balance);
    
    // Set maximum payment amount
    document.getElementById('payment_amount').max = balance;
    
    // Default to full payment
    document.getElementById('payment_amount').value = balance.toFixed(2);
    
    // Show modal
    modal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
};